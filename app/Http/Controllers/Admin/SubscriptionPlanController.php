<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $pageTitle = 'Subscription Plans';
        $plans = SubscriptionPlan::orderBy('sort_order')->orderBy('price')->get();
        
        return view('admin.subscription_plans.index', compact('pageTitle', 'plans'));
    }

    public function create()
    {
        $pageTitle = 'Create Subscription Plan';
        return view('admin.subscription_plans.create', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'description', 'price', 'billing_cycle', 'is_active', 'sort_order']);
        
        // Handle features
        if ($request->has('features') && is_array($request->features)) {
            $data['features'] = array_filter($request->features, function($feature) {
                return !empty(trim($feature));
            });
        }

        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['sort_order'] = $request->sort_order ?? 0;

        SubscriptionPlan::create($data);

        $notify[] = ['success', 'Subscription plan created successfully'];
        return redirect()->route('admin.subscription.plans.index')->withNotify($notify);
    }

    public function show(SubscriptionPlan $plan)
    {
        $pageTitle = 'Subscription Plan Details';
        return view('admin.subscription_plans.show', compact('pageTitle', 'plan'));
    }

    public function edit(SubscriptionPlan $plan)
    {
        $pageTitle = 'Edit Subscription Plan';
        return view('admin.subscription_plans.edit', compact('pageTitle', 'plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'description', 'price', 'billing_cycle', 'is_active', 'sort_order']);
        
        // Handle features
        if ($request->has('features') && is_array($request->features)) {
            $data['features'] = array_filter($request->features, function($feature) {
                return !empty(trim($feature));
            });
        }

        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['sort_order'] = $request->sort_order ?? 0;

        $plan->update($data);

        $notify[] = ['success', 'Subscription plan updated successfully'];
        return redirect()->route('admin.subscription.plans.index')->withNotify($notify);
    }

    public function destroy(SubscriptionPlan $plan)
    {
        // Check if plan has active subscriptions
        if ($plan->activeTenantSubscriptions()->count() > 0) {
            $notify[] = ['error', 'Cannot delete plan with active subscriptions'];
            return back()->withNotify($notify);
        }

        $plan->delete();

        $notify[] = ['success', 'Subscription plan deleted successfully'];
        return back()->withNotify($notify);
    }

    public function status(SubscriptionPlan $plan)
    {
        $plan->is_active = !$plan->is_active;
        $plan->save();

        $status = $plan->is_active ? 'enabled' : 'disabled';
        $notify[] = ['success', "Subscription plan $status successfully"];
        
        return back()->withNotify($notify);
    }
}
