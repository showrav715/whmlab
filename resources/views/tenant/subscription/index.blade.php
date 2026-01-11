@extends('admin.layouts.app')
@section('panel')
    <!-- Tenant Information Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">@lang('Subscription Management')</h4>
                    <p class="text-muted mb-0">
                        <i class="las la-building me-1"></i>
                        @lang('Managing subscriptions for'): <strong>{{ $currentTenant->getSetting('name', 'Tenant #' . $currentTenant->id) }}</strong>
                        @if($currentTenant->domains->first())
                            <small class="ms-2 text-muted">({{ $currentTenant->domains->first()->domain }})</small>
                        @endif
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.subscription.index') }}" class="btn btn--secondary btn-sm">
                        <i class="las la-exchange-alt me-1"></i>
                        @lang('Switch Tenant')
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Current Subscription Status -->
            @if($currentSubscription)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="las la-crown me-2"></i>
                                @lang('Your Current Subscription')
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>@lang('Plan'): {{ $currentSubscription->subscriptionPlan->name }}</h6>
                                    <p class="text-muted">{{ $currentSubscription->subscriptionPlan->description }}</p>
                                </div>
                                <div class="col-md-3">
                                    <h6>@lang('Status'): 
                                        @if($currentSubscription->status === 'active')
                                            <span class="badge badge--success">@lang('Active')</span>
                                        @else
                                            <span class="badge badge--warning">{{ __(ucfirst($currentSubscription->status)) }}</span>
                                        @endif
                                    </h6>
                                    <small class="text-muted">@lang('Expires'): {{ $currentSubscription->expires_at->format('M j, Y') }}</small>
                                </div>
                                <div class="col-md-3 text-end">
                                    @php
                                        $daysLeft = now()->diffInDays($currentSubscription->expires_at, false);
                                    @endphp
                                    @if($daysLeft > 0)
                                        <h6 class="text-success">{{ $daysLeft }} @lang('days left')</h6>
                                    @else
                                        <h6 class="text-danger">@lang('Expired')</h6>
                                    @endif
                                    
                                    @if($daysLeft < 30)
                                        <div class="alert alert-warning mt-2">
                                            <small>
                                                <i class="las la-exclamation-triangle"></i>
                                                @if($daysLeft > 0)
                                                    @lang('Subscription expires soon! Renew now to avoid service interruption.')
                                                @else
                                                    @lang('Subscription has expired! Renew immediately to restore services.')
                                                @endif
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Available Subscription Plans -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="las la-layer-group me-2"></i>
                                @lang('Available Subscription Plans')
                            </h5>
                            <p class="card-subtitle text-muted">@lang('Choose a plan that fits your business needs')</p>
                        </div>
                        <div class="card-body">
                            <div class="row gy-4">
                                @foreach($subscriptionPlans as $plan)
                                <div class="col-xl-4 col-lg-6">
                                    <div class="pricing-card {{ $currentSubscription && $currentSubscription->subscription_plan_id === $plan->id ? 'pricing-card--active' : '' }}">
                                        <div class="pricing-card__header">
                                            <h4 class="pricing-card__title">{{ $plan->name }}</h4>
                                            <div class="pricing-card__price">
                                                <span class="pricing-card__currency">{{ gs()->cur_sym }}</span>
                                                <span class="pricing-card__amount">{{ showAmount($plan->price) }}</span>
                                                <span class="pricing-card__period">/ {{ $plan->billing_cycle }}</span>
                                            </div>
                                            <p class="pricing-card__description">{{ $plan->description }}</p>
                                        </div>
                                        
                                        <div class="pricing-card__body">
                                            <ul class="pricing-card__features">
                                                @if($plan->features && is_array($plan->features))
                                                    @foreach($plan->features as $feature)
                                                        <li>
                                                            <i class="las la-check text-success"></i>
                                                            {{ $feature }}
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </div>

                                        <div class="pricing-card__footer">
                                            @if($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id)
                                                <button class="btn btn--success btn--block" disabled>
                                                    <i class="las la-check"></i>
                                                    @lang('Current Plan')
                                                </button>
                                            @else
                                                <a href="{{ route('admin.subscription.renew', $plan->id) }}" class="btn btn--primary btn--block">
                                                    <i class="las la-credit-card"></i>
                                                    @if($currentSubscription)
                                                        @lang('Upgrade/Renew')
                                                    @else
                                                        @lang('Subscribe Now')
                                                    @endif
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription History -->
            @if($currentSubscription)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="las la-history me-2"></i>
                                @lang('Subscription History')
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>@lang('Plan')</th>
                                            <th>@lang('Duration')</th>
                                            <th>@lang('Amount')</th>
                                            <th>@lang('Status')</th>
                                            <th>@lang('Started')</th>
                                            <th>@lang('Expires')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $allSubscriptions = App\Models\TenantSubscription::where('tenant_id', $currentTenant->id)
                                                ->with('subscriptionPlan')
                                                ->orderBy('created_at', 'desc')
                                                ->take(5)
                                                ->get();
                                        @endphp
                                        @forelse($allSubscriptions as $subscription)
                                            <tr>
                                                <td>{{ $subscription->subscriptionPlan->name }}</td>
                                                <td>{{ ucfirst($subscription->subscriptionPlan->billing_cycle) }}</td>
                                                <td>{{ gs()->cur_sym }}{{ showAmount($subscription->price_paid) }}</td>
                                                <td>
                                                    @if($subscription->status === 'active')
                                                        <span class="badge badge--success">@lang('Active')</span>
                                                    @elseif($subscription->status === 'expired')
                                                        <span class="badge badge--warning">@lang('Expired')</span>
                                                    @else
                                                        <span class="badge badge--info">{{ __(ucfirst($subscription->status)) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $subscription->started_at->format('M j, Y') }}</td>
                                                <td>{{ $subscription->expires_at->format('M j, Y') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">@lang('No subscription history found')</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <style>
        .pricing-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 30px 20px;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }

        .pricing-card:hover {
            border-color: #007bff;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 123, 255, 0.1);
        }

        .pricing-card--active {
            border-color: #28a745;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .pricing-card__title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .pricing-card__price {
            margin-bottom: 1rem;
        }

        .pricing-card__currency {
            font-size: 1.2rem;
            vertical-align: top;
        }

        .pricing-card__amount {
            font-size: 3rem;
            font-weight: 700;
        }

        .pricing-card__period {
            color: #6c757d;
            font-size: 1rem;
        }

        .pricing-card__features {
            list-style: none;
            padding: 0;
            margin: 2rem 0;
        }

        .pricing-card__features li {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .pricing-card--active .pricing-card__features li {
            border-bottom-color: rgba(255,255,255,0.2);
        }
    </style>
@endsection