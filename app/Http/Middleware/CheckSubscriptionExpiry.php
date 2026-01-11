<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use Illuminate\Support\Facades\Log;

class CheckSubscriptionExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for tenant domains (not admin panel)
        if ($request->is('admin/*') || $request->is('admin')) {
            return $next($request);
        }

        // Get current tenant from domain
        $currentDomain = $request->getHost();
        $tenant = $this->getTenantByDomain($currentDomain);

        if ($tenant) {
            $this->checkAndUpdateExpiredSubscriptions($tenant);
            
            // Check if tenant has active subscription
            $activeSubscription = $tenant->currentSubscription;
            
            if (!$activeSubscription || $activeSubscription->isExpired()) {
                // Redirect to subscription expired page or suspend tenant
                return $this->handleExpiredSubscription($tenant);
            }
        }

        return $next($request);
    }

    private function getTenantByDomain($domain)
    {
        return Tenant::whereHas('domains', function($query) use ($domain) {
            $query->where('domain', $domain);
        })->first();
    }

    private function checkAndUpdateExpiredSubscriptions(Tenant $tenant)
    {
        // Get all active subscriptions that are actually expired
        $expiredSubscriptions = TenantSubscription::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);
            
            Log::info('Subscription expired and updated', [
                'tenant_id' => $tenant->id,
                'subscription_id' => $subscription->id,
                'expired_at' => $subscription->expires_at
            ]);
        }
    }

    private function handleExpiredSubscription(Tenant $tenant)
    {
        // Suspend tenant if no active subscription
        $tenant->setSetting('status', 'suspended');
        $tenant->setSetting('suspension_reason', 'Subscription expired');
        
        Log::warning('Tenant suspended due to expired subscription', [
            'tenant_id' => $tenant->id,
            'domain' => request()->getHost()
        ]);

        // Return subscription expired view
        return response()->view('tenant.subscription_expired', compact('tenant'), 503);
    }
}
