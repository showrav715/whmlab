@extends('admin.layouts.app')
@section('panel')
@php
    $tenantName = $tenant->getSetting('name', 'Unnamed Tenant');
@endphp

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    <i class="las la-list"></i> Subscriptions - {{ $tenantName }}
                </h4>
                <a href="{{ route('admin.tenants.index') }}" class="btn btn-sm btn-secondary">
                    <i class="las la-arrow-left"></i> Back to Tenants
                </a>
            </div>
            <div class="card-body">
                <!-- Current Subscription -->
                @php $currentSubscription = $tenant->currentSubscription; @endphp
                @if($currentSubscription)
                    <div class="card border-left-primary mb-4">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="las la-crown text-primary"></i> Current Active Subscription
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-primary text-white me-3">
                                            <i class="las la-box"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Subscription Plan</h6>
                                            <small class="text-muted">Current active plan</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold">{{ $currentSubscription->subscriptionPlan->name }}</span>
                                        <br>
                                        <small class="text-muted">{{ $currentSubscription->subscriptionPlan->billing_cycle_text }}</small>
                                    </div>
                                </div>
                                
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-success text-white me-3">
                                            <i class="las la-dollar-sign"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Amount Paid</h6>
                                            <small class="text-muted">Last payment</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold text-success">${{ $currentSubscription->price_paid }}</span>
                                    </div>
                                </div>
                                
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-info text-white me-3">
                                            <i class="las la-calendar"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Subscription Period</h6>
                                            <small class="text-muted">Start and end dates</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold">{{ $currentSubscription->started_at->format('M d, Y') }}</span>
                                        <br>
                                        <small class="text-muted">to {{ $currentSubscription->expires_at->format('M d, Y') }}</small>
                                    </div>
                                </div>
                                
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-{{ $currentSubscription->status_badge }} text-white me-3">
                                            <i class="las la-{{ $currentSubscription->isActive() ? 'check-circle' : 'times-circle' }}"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Status & Duration</h6>
                                            <small class="text-muted">Current status</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        @if($currentSubscription->isActive())
                                            <span class="badge badge--success">{{ ucfirst($currentSubscription->status) }}</span>
                                        @elseif($currentSubscription->status === 'expired')
                                            <span class="badge badge--danger">{{ ucfirst($currentSubscription->status) }}</span>
                                        @else
                                            <span class="badge badge--warning">{{ ucfirst($currentSubscription->status) }}</span>
                                        @endif
                                        <br>
                                        @if($currentSubscription->isActive())
                                            <small class="text-{{ $currentSubscription->daysRemaining() > 7 ? 'success' : 'warning' }}">
                                                {{ round($currentSubscription->daysRemaining()) }} days remaining
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($currentSubscription->subscriptionPlan->features && count($currentSubscription->subscriptionPlan->features) > 0)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-secondary text-white me-3">
                                                <i class="las la-list-ul"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">Plan Features</h6>
                                                <small class="text-muted">Included in this subscription</small>
                                            </div>
                                        </div>
                                        <button class="btn btn-link btn-sm p-0" type="button" data-bs-toggle="collapse" data-bs-target="#featuresCollapse" aria-expanded="false">
                                            <i class="las la-chevron-down"></i>
                                        </button>
                                    </div>
                                    <div class="collapse mt-3" id="featuresCollapse">
                                        <div class="row">
                                            @foreach($currentSubscription->subscriptionPlan->features as $feature)
                                                <div class="col-md-6 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="las la-check-circle text-success me-2"></i>
                                                        <span class="small">{{ $feature }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <div class="mt-3 pt-3 border-top">
                                            <h6 class="mb-2 text-muted">Billing Information</h6>
                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <small class="text-muted">Billing Cycle:</small>
                                                    <strong class="d-block">{{ $currentSubscription->subscriptionPlan->billing_cycle_text }}</strong>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <small class="text-muted">Next Due:</small>
                                                    <strong class="d-block">{{ $currentSubscription->expires_at->format('M d, Y') }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="las la-clock"></i> 
                                    Last updated: {{ $currentSubscription->updated_at->diffForHumans() }}
                                </small>
                                <div class="button--group">
                                    @if($currentSubscription->isActive())
                                        <button type="button" class="btn btn-sm btn-outline--warning confirmationBtn" 
                                                data-action="{{ route('admin.tenants.subscription.remove', [$tenant->id, $currentSubscription->id]) }}"
                                                data-question="Are you sure you want to suspend this subscription?">
                                            <i class="las la-pause"></i> Suspend
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline--primary" data-bs-toggle="collapse" data-bs-target="#featuresCollapse">
                                        <i class="las la-eye"></i> Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card border-left-warning mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-warning text-white me-3">
                                    <i class="las la-exclamation-triangle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 text-warning">No Active Subscription</h6>
                                    <p class="mb-0 text-muted">This tenant does not have an active subscription. Add one using the form below.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Add New Subscription -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="las la-plus"></i> Add New Subscription</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.tenants.subscriptions.add', $tenant->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="form-label required">Subscription Plan</label>
                                        <select name="subscription_plan_id" class="form-control" required id="subscription_plan_select">
                                            <option value="">Choose Plan</option>
                                            @foreach($subscriptionPlans as $plan)
                                                <option value="{{ $plan->id }}" data-price="{{ $plan->price }}" data-cycle="{{ $plan->billing_cycle }}">
                                                    {{ $plan->name }} - ${{ $plan->formatted_price }}/{{ $plan->billing_cycle }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-6">
                                    <div class="form-group">
                                        <label class="form-label required">Start Date</label>
                                        <input type="date" name="started_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-6">
                                    <div class="form-group">
                                        <label class="form-label required">End Date</label>
                                        <input type="date" name="expires_at" class="form-control" required id="expires_at">
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6 col-8">
                                    <div class="form-group">
                                        <label class="form-label">Price Paid</label>
                                        <input type="number" name="price_paid" class="form-control" step="0.01" placeholder="Auto" id="price_paid">
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6 col-4">
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn--primary d-block w-100">
                                            <i class="las la-plus"></i> <span class="d-none d-sm-inline">Add</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- All Subscriptions List -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="las la-history"></i> All Subscriptions</h6>
                    </div>
                    <div class="card-body">
                        @if($tenant->tenantSubscriptions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Plan</th>
                                            <th class="d-none d-md-table-cell">Price</th>
                                            <th class="d-none d-lg-table-cell">Period</th>
                                            <th>Status</th>
                                            <th class="d-none d-sm-table-cell">Days Left</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tenant->tenantSubscriptions->sortByDesc('created_at') as $subscription)
                                            <tr>
                                                <td>
                                                    <strong>{{ $subscription->subscriptionPlan->name }}</strong>
                                                    <br>
                                                    <small class="text-muted d-block d-md-none">
                                                        ${{ $subscription->price_paid }} / {{ $subscription->subscriptionPlan->billing_cycle_text }}
                                                    </small>
                                                    <small class="text-muted d-none d-md-block">{{ $subscription->subscriptionPlan->description }}</small>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    ${{ $subscription->price_paid }}
                                                    <br>
                                                    <small class="text-muted">{{ $subscription->subscriptionPlan->billing_cycle_text }}</small>
                                                </td>
                                                <td class="d-none d-lg-table-cell">
                                                    <strong>Start:</strong> {{ $subscription->started_at->format('M d, Y') }}<br>
                                                    <strong>End:</strong> {{ $subscription->expires_at->format('M d, Y') }}
                                                </td>
                                                <td>
                                                    @if($subscription->isActive())
                                                        <span class="badge badge--success">{{ ucfirst($subscription->status) }}</span>
                                                    @elseif($subscription->status === 'expired')
                                                        <span class="badge badge--danger">{{ ucfirst($subscription->status) }}</span>
                                                    @else
                                                        <span class="badge badge--warning">{{ ucfirst($subscription->status) }}</span>
                                                    @endif
                                                    <br class="d-sm-none">
                                                    <small class="d-sm-none text-muted">
                                                        @if($subscription->isActive())
                                                            {{ round($subscription->daysRemaining()) }} days left
                                                        @endif
                                                    </small>
                                                </td>
                                                <td class="d-none d-sm-table-cell">
                                                    @if($subscription->isActive())
                                                        <span class="badge badge--success">{{ round($subscription->daysRemaining()) }} days</span>
                                                    @elseif($subscription->status === 'expired')
                                                        <span class="badge badge--danger">Expired</span>
                                                    @else
                                                        <span class="badge badge--warning">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($subscription->status === 'active')
                                                        <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn"
                                                                data-action="{{ route('admin.tenants.subscription.remove', [$tenant->id, $subscription->id]) }}"
                                                                data-question="Are you sure you want to cancel this subscription?">
                                                            <i class="las la-ban"></i> Cancel
                                                        </button>
                                                    @else
                                                        <span class="text-muted">No actions</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 px-3">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="las la-inbox la-3x text-muted mb-3"></i>
                                    <h6 class="mt-2 mb-2">No Subscriptions Found</h6>
                                    <p class="text-muted mb-3">This tenant doesn't have any subscriptions yet.</p>
                                    <small class="text-muted">Use the form above to add a subscription</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Features collapse toggle
        $('[data-bs-toggle="collapse"]').on('click', function() {
            const target = $($(this).data('bs-target'));
            const icon = $(this).find('i');
            
            if (target.hasClass('show')) {
                icon.removeClass('la-chevron-up').addClass('la-chevron-down');
            } else {
                icon.removeClass('la-chevron-down').addClass('la-chevron-up');
            }
        });
        
        // Bootstrap collapse events
        $('#featuresCollapse').on('shown.bs.collapse', function() {
            $('[data-bs-target="#featuresCollapse"] i').removeClass('la-chevron-down').addClass('la-chevron-up');
        });
        
        $('#featuresCollapse').on('hidden.bs.collapse', function() {
            $('[data-bs-target="#featuresCollapse"] i').removeClass('la-chevron-up').addClass('la-chevron-down');
        });
        
        // Subscription plan change handler
        $('#subscription_plan_select').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const price = selectedOption.data('price');
            const cycle = selectedOption.data('cycle');
            
            // Auto fill price
            if (price) {
                $('#price_paid').val(price);
            }
            
            // Auto calculate end date based on billing cycle
            const startDate = new Date($('input[name="started_at"]').val());
            if (startDate && cycle) {
                let endDate = new Date(startDate);
                if (cycle === 'monthly') {
                    endDate.setMonth(endDate.getMonth() + 1);
                } else if (cycle === 'yearly') {
                    endDate.setFullYear(endDate.getFullYear() + 1);
                }
                
                $('#expires_at').val(endDate.toISOString().split('T')[0]);
            }
        });
        
        $('input[name="started_at"]').on('change', function() {
            // Trigger plan change to recalculate end date
            $('#subscription_plan_select').trigger('change');
        });
    });
</script>
@endpush

@push('style')
<style>
    /* Icon Circle Styles */
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    
    /* Card Border Left Styles */
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }
    
    .border-left-warning {
        border-left: 4px solid #ffc107 !important;
    }
    
    /* List Group Enhancements */
    .list-group-item {
        border-left: none;
        border-right: none;
        padding: 1rem 1.25rem;
    }
    
    .list-group-item:first-child {
        border-top: none;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
    
    /* Badge Large */
    .badge-lg {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
    
    /* Admin Style Badges */
    .badge--success {
        background-color: #28a745;
        color: white;
    }
    
    .badge--danger {
        background-color: #dc3545;
        color: white;
    }
    
    .badge--warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .badge--primary {
        background-color: #007bff;
        color: white;
    }
    
    /* Responsive margins */
    .me-3 {
        margin-right: 1rem !important;
    }
    
    /* Responsive Design */
    @media (max-width: 767px) {
        .icon-circle {
            width: 35px;
            height: 35px;
            font-size: 14px;
        }
        
        .me-3 {
            margin-right: 0.75rem !important;
        }
        
        .list-group-item {
            padding: 0.75rem 1rem;
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .list-group-item .text-end {
            text-align: left !important;
            margin-top: 0.5rem;
            width: 100%;
        }
        
        .d-flex.justify-content-between {
            width: 100%;
            flex-direction: column;
        }
        
        .d-flex.justify-content-between .text-end {
            margin-top: 0.5rem;
        }
        
        .card-footer .d-flex {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .card-footer .btn-group {
            width: 100%;
        }
        
        .card-footer .btn-group .btn {
            flex: 1;
        }
        
        .table {
            font-size: 14px;
        }
        
        .table th,
        .table td {
            padding: 0.5rem 0.25rem;
            border-top: 1px solid #dee2e6;
        }
        
        .badge {
            font-size: 11px;
            padding: 0.25em 0.5em;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .form-group label {
            font-size: 14px;
            margin-bottom: 0.25rem;
        }
        
        .form-control {
            font-size: 14px;
            padding: 0.5rem 0.75rem;
        }
    }
    
    .table-responsive {
        border: none;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .subscription-plan-card {
        transition: transform 0.2s;
    }
    
    .subscription-plan-card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush