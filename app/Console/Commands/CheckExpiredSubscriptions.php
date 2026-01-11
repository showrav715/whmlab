<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TenantSubscription;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expired {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and handle expired subscriptions, suspend tenants and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('Starting subscription expiry check...');
        
        // Check expired active subscriptions
        $expiredSubscriptions = TenantSubscription::with(['tenant', 'subscriptionPlan'])
            ->where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        if ($expiredSubscriptions->isEmpty()) {
            $this->info('No expired subscriptions found.');
            return;
        }

        $this->info("Found {$expiredSubscriptions->count()} expired subscriptions.");

        foreach ($expiredSubscriptions as $subscription) {
            $this->processExpiredSubscription($subscription, $isDryRun);
        }

        // Check subscriptions expiring soon (within 7 days)
        $expiringSoon = TenantSubscription::with(['tenant', 'subscriptionPlan'])
            ->where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addDays(7)])
            ->get();

        if ($expiringSoon->isNotEmpty()) {
            $this->info("Found {$expiringSoon->count()} subscriptions expiring soon.");
            foreach ($expiringSoon as $subscription) {
                $this->processExpiringSoonSubscription($subscription, $isDryRun);
            }
        }

        $this->info('Subscription check completed.');
    }

    private function processExpiredSubscription(TenantSubscription $subscription, bool $isDryRun)
    {
        $tenant = $subscription->tenant;
        $tenantName = $tenant->getSetting('name', 'Unnamed Tenant');

        $this->warn("Processing expired subscription: {$tenantName} (ID: {$tenant->id})");

        if (!$isDryRun) {
            // Update subscription status
            $subscription->update(['status' => 'expired']);

            // Suspend tenant
            $tenant->setSetting('status', 'suspended');
            $tenant->setSetting('suspension_reason', 'Subscription expired on ' . $subscription->expires_at->format('Y-m-d'));
            $tenant->setSetting('suspended_at', now()->toDateTimeString());

            Log::warning('Tenant suspended due to expired subscription', [
                'tenant_id' => $tenant->id,
                'subscription_id' => $subscription->id,
                'expired_at' => $subscription->expires_at,
                'plan' => $subscription->subscriptionPlan->name
            ]);

            // Send notification email (if configured)
            $this->sendExpiryNotification($subscription);
        }

        $this->line("  - Subscription expired on: {$subscription->expires_at->format('Y-m-d H:i:s')}");
        $this->line("  - Plan: {$subscription->subscriptionPlan->name}");
        $this->line("  - Status: " . ($isDryRun ? 'Would be marked as expired' : 'Marked as expired'));
        $this->line("  - Tenant: " . ($isDryRun ? 'Would be suspended' : 'Suspended'));
    }

    private function processExpiringSoonSubscription(TenantSubscription $subscription, bool $isDryRun)
    {
        $tenant = $subscription->tenant;
        $tenantName = $tenant->getSetting('name', 'Unnamed Tenant');
        $daysRemaining = $subscription->daysRemaining();

        $this->info("Subscription expiring soon: {$tenantName} ({$daysRemaining} days remaining)");

        if (!$isDryRun) {
            // Send reminder notification
            $this->sendExpiryReminderNotification($subscription);
            
            Log::info('Expiry reminder sent', [
                'tenant_id' => $tenant->id,
                'subscription_id' => $subscription->id,
                'expires_at' => $subscription->expires_at,
                'days_remaining' => $daysRemaining
            ]);
        }
    }

    private function sendExpiryNotification(TenantSubscription $subscription)
    {
        // Implementation for sending expiry notification email
        // You can customize this based on your email system
        try {
            $tenant = $subscription->tenant;
            $adminEmail = $tenant->getSetting('admin_email') ?? config('mail.from.address');
            
            if ($adminEmail) {
                // Send email notification
                Log::info('Sending expiry notification', [
                    'tenant_id' => $tenant->id,
                    'email' => $adminEmail
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send expiry notification', [
                'error' => $e->getMessage(),
                'tenant_id' => $subscription->tenant_id
            ]);
        }
    }

    private function sendExpiryReminderNotification(TenantSubscription $subscription)
    {
        // Implementation for sending reminder notification email
        try {
            $tenant = $subscription->tenant;
            $adminEmail = $tenant->getSetting('admin_email') ?? config('mail.from.address');
            
            if ($adminEmail) {
                Log::info('Sending expiry reminder', [
                    'tenant_id' => $tenant->id,
                    'email' => $adminEmail,
                    'days_remaining' => $subscription->daysRemaining()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send expiry reminder', [
                'error' => $e->getMessage(),
                'tenant_id' => $subscription->tenant_id
            ]);
        }
    }
}
