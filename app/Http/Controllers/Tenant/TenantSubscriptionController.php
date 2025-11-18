<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Gateway;
use App\Models\SubscriptionPlan;
use App\Models\TenantSubscription;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Stancl\Tenancy\Facades\Tenancy;

class TenantSubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show subscription plans for renewal
     */
    public function index(Request $request)
    {
        $pageTitle = 'Subscription Plans';
        
        // If tenant_id is passed in request, set it in session
        if ($request->has('tenant_id')) {
            session(['current_tenant_id' => $request->tenant_id]);
        }

        // Get current tenant from admin context or session
        $currentTenant = $this->getCurrentTenant();
        
        if (!$currentTenant) {
            // If no tenant selected, show tenant selection
            $tenants = Tenant::on('mysql')->get(); // Force central database
            return view('tenant.subscription.select_tenant', compact('pageTitle', 'tenants'));
        }

        // Get current subscription from central database
        $currentSubscription = TenantSubscription::on('mysql')
            ->where('tenant_id', $currentTenant->id)
            ->where('status', 'active')
            ->with('subscriptionPlan')
            ->first();

        // Get all available subscription plans from central database
        $subscriptionPlans = SubscriptionPlan::on('mysql')
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        return view('tenant.subscription.index', compact(
            'pageTitle', 
            'subscriptionPlans', 
            'currentSubscription', 
            'currentTenant'
        ));
    }

    /**
     * Show subscription renewal confirmation
     */
    public function renew($planId)
    {
        $pageTitle = 'Renew Subscription';
        
        $currentTenant = $this->getCurrentTenant();
        if (!$currentTenant) {
            return redirect()->route('admin.dashboard')->with('error', 'Tenant information not found.');
        }

        $subscriptionPlan = SubscriptionPlan::on('mysql')->findOrFail($planId);
        
        // Get current subscription
        $currentSubscription = TenantSubscription::on('mysql')
            ->where('tenant_id', $currentTenant->id)
            ->where('status', 'active')
            ->with('subscriptionPlan')
            ->first();

        return view('tenant.subscription.renew', compact(
            'pageTitle', 
            'subscriptionPlan', 
            'currentSubscription', 
            'currentTenant'
        ));
    }

    /**
     * Process payment with Razorpay
     */
    public function processPayment(Request $request)
    {
     
        $request->validate([
            'plan_id' => 'required|integer',
            'payment_method' => 'required|in:razorpay'
        ]);

        // Custom validation for subscription plan existence in central database
        $planExists = SubscriptionPlan::on('mysql')->where('id', $request->plan_id)->exists();
        if (!$planExists) {
            return response()->json(['error' => 'Invalid subscription plan.'], 400);
        }

        $currentTenant = $this->getCurrentTenant();
        if (!$currentTenant) {
            return response()->json(['error' => 'Tenant information not found.'], 400);
        }

        $subscriptionPlan = SubscriptionPlan::on('mysql')->findOrFail($request->plan_id);

        try {
            // Initialize Razorpay
            $razorpay = $this->initializeRazorpay();
            
            // Create Razorpay order
            $orderData = [
                'receipt' => 'subscription_' .  '_' . time(),
                'amount' => (int) $subscriptionPlan->price * 100, // Amount in paisa
                'currency' => 'INR',
                'payment_capture' => '0'
            ];

            $razorpayOrder = $razorpay->order->create($orderData);

   
            // Store payment intent in session
            session([
                'subscription_payment' => [
                    'tenant_id' => $currentTenant->id,
                    'plan_id' => $subscriptionPlan->id,
                    'order_id' => $razorpayOrder->id,
                    'amount' => $subscriptionPlan->price,
                    'receipt' => $orderData['receipt']
                ]
            ]);

         
             $gateway = Gateway::where('alias','Razorpay')->first();
        $keys = json_decode($gateway->gateway_parameters);
        $apiKey = $keys->key_id->value;

            return response()->json([
                'success' => true,
                'order_id' => $razorpayOrder->id,
                'amount' => $subscriptionPlan->price * 100,
                'currency' => 'INR',
                'key' => $apiKey,
                'name' => 'Subscription Renewal',
                'description' => $subscriptionPlan->name . ' Plan Renewal',
                'prefill' => [
                    'name' => Auth::guard('admin')->user()->username,
                    'email' => Auth::guard('admin')->user()->email,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle payment success callback
     */
    public function paymentSuccess(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_signature' => 'required'
        ]);

        try {
            $razorpay = $this->initializeRazorpay();
            
            // Verify payment signature
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $razorpay->utility->verifyPaymentSignature($attributes);

            // Get payment details from session
            $paymentData = session('subscription_payment');
            
            if (!$paymentData || $paymentData['order_id'] !== $request->razorpay_order_id) {
                throw new \Exception('Invalid payment session.');
            }

            // Process subscription renewal
            $this->renewSubscription($paymentData, $request->razorpay_payment_id);

            // Clear session
            session()->forget('subscription_payment');

            return redirect()->route('admin.subscription.success')
                ->with('success', 'Subscription renewed successfully!');

        } catch (\Exception $e) {
            return redirect()->route('admin.subscription.index')
                ->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Show payment success page
     */
    public function success()
    {
        $pageTitle = 'Payment Successful';
        return view('tenant.subscription.success', compact('pageTitle'));
    }

    /**
     * Get current tenant information
     */
    private function getCurrentTenant()
    {
        // Get admin user
        $admin = Auth::guard('admin')->user();
        
        // Check if tenant ID is in session (selected by admin)
        if (session('current_tenant_id')) {
            return Tenant::on('mysql')->find(session('current_tenant_id'));
        }

        // For tenant-specific admins, get tenant from request domain
        $currentDomain = request()->getHost();
        
        // Skip if accessing from main domain
        $centralDomains = config('tenancy.central_domains', []);
        if (in_array($currentDomain, $centralDomains)) {
            return null; // Admin needs to select a tenant
        }

        // Try to find tenant by domain
        $tenant = Tenant::on('mysql')
            ->whereHas('domains', function($query) use ($currentDomain) {
                $query->where('domain', $currentDomain);
            })
            ->first();

        if ($tenant) {
            // Store in session for future requests
            session(['current_tenant_id' => $tenant->id]);
            return $tenant;
        }

        return null;
    }

    /**
     * Initialize Razorpay API
     */
    private function initializeRazorpay()
    {
        $gateway = Gateway::where('alias','Razorpay')->first();
        $keys = json_decode($gateway->gateway_parameters);
       // dd($keys);
// gateway_parameter

        //  API request and response for creating an order
        $apiKey = $keys->key_id->value;
        $apiSecret = $keys->key_secret->value;
        return new Api(
            $apiKey,
            $apiSecret
        );
    }

    /**
     * Process subscription renewal
     */
    private function renewSubscription($paymentData, $paymentId)
    {
        $tenant = Tenant::on('mysql')->findOrFail($paymentData['tenant_id']);
        $plan = SubscriptionPlan::on('mysql')->findOrFail($paymentData['plan_id']);

        // Deactivate current subscription
        TenantSubscription::on('mysql')
            ->where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        // Create new subscription
        $newSubscription = TenantSubscription::on('mysql')->create([
            'tenant_id' => $tenant->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addMonths($plan->billing_cycle === 'monthly' ? 1 : 12),
            'price_paid' => $paymentData['amount'],
            'plan_features' => $plan->features,
            'payment_id' => $paymentId,
            'receipt_id' => $paymentData['receipt']
        ]);

        return $newSubscription;
    }

    /**
     * Temporarily switch to central database for subscription operations
     */
    private function useCentralDatabase($callback)
    {
        $originalConnection = config('database.default');
        $tenancyWasInitialized = false;
        
        try {
            // Check if tenancy is initialized
            if (app()->bound('tenancy') && tenancy()->initialized) {
                $tenancyWasInitialized = true;
                tenancy()->end();
            }
            
            // Switch to central database
            config(['database.default' => 'mysql']);
            
            // Execute callback
            return $callback();
            
        } finally {
            // Restore original state
            config(['database.default' => $originalConnection]);
            
            // Re-initialize tenancy if it was active
            if ($tenancyWasInitialized) {
                // Note: tenancy will be re-initialized on next request
            }
        }
    }
}