@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <div class="row gy-4">

                <!-- Tenant Management Statistics -->
                <div class="col-xxl-3 col-sm-6">
                    <div class="card bg-primary">
                        <div class="card-body">
                            <div class="currency-widget">
                                <div class="d-flex justify-content-between">
                                    <div class="currency-widget__content">
                                        <h6 class="currency-widget__title text-white">@lang('Total Tenants')</h6>
                                        <h3 class="currency-widget__amount text-white">{{ @$tenantData['total_tenants'] ?? 0 }}</h3>
                                    </div>
                                    <div class="currency-widget__icon">
                                        <i class="las la-globe"></i>
                                    </div>
                                </div>
                                <div class="currency-widget__footer">
                                    <a href="{{ route('admin.tenants.index') }}" class="text-white text--small">
                                        @lang('View All') <i class="las la-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <div class="card bg-success">
                        <div class="card-body">
                            <div class="currency-widget">
                                <div class="d-flex justify-content-between">
                                    <div class="currency-widget__content">
                                        <h6 class="currency-widget__title text-white">@lang('Active Tenants')</h6>
                                        <h3 class="currency-widget__amount text-white">{{ @$tenantData['active_tenants'] ?? 0 }}</h3>
                                    </div>
                                    <div class="currency-widget__icon">
                                        <i class="las la-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <div class="card bg-warning">
                        <div class="card-body">
                            <div class="currency-widget">
                                <div class="d-flex justify-content-between">
                                    <div class="currency-widget__content">
                                        <h6 class="currency-widget__title text-white">@lang('Suspended Tenants')</h6>
                                        <h3 class="currency-widget__amount text-white">{{ @$tenantData['suspended_tenants'] ?? 0 }}</h3>
                                    </div>
                                    <div class="currency-widget__icon">
                                        <i class="las la-ban"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <div class="card bg-info">
                        <div class="card-body">
                            <div class="currency-widget">
                                <div class="d-flex justify-content-between">
                                    <div class="currency-widget__content">
                                        <h6 class="currency-widget__title text-white">@lang('Subscription Plans')</h6>
                                        <h3 class="currency-widget__amount text-white">{{ @$tenantData['subscription_plans'] ?? 0 }}</h3>
                                    </div>
                                    <div class="currency-widget__icon">
                                        <i class="las la-credit-card"></i>
                                    </div>
                                </div>
                                <div class="currency-widget__footer">
                                    <a href="{{ route('admin.subscription.plans.index') }}" class="text-white text--small">
                                        @lang('Manage Plans') <i class="las la-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Metrics Row -->
                <div class="col-xxl-3 col-sm-6">
                    <div class="card bg-info">
                        <div class="card-body">
                            <div class="currency-widget">
                                <div class="d-flex justify-content-between">
                                    <div class="currency-widget__content">
                                        <h6 class="currency-widget__title text-white">@lang('Monthly Growth')</h6>
                                        <h3 class="currency-widget__amount text-white">{{ @$tenantData['monthly_growth'] ?? 0 }}</h3>
                                    </div>
                                    <div class="currency-widget__icon">
                                        <i class="las la-chart-line"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <div class="card bg-success">
                        <div class="card-body">
                            <div class="currency-widget">
                                <div class="d-flex justify-content-between">
                                    <div class="currency-widget__content">
                                        <h6 class="currency-widget__title text-white">@lang('Subscription Revenue')</h6>
                                        <h3 class="currency-widget__amount text-white">{{ showAmount(@$tenantData['subscription_revenue']) }}</h3>
                                    </div>
                                    <div class="currency-widget__icon">
                                        <i class="las la-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <div class="card bg-secondary">
                        <div class="card-body">
                            <div class="currency-widget">
                                <div class="d-flex justify-content-between">
                                    <div class="currency-widget__content">
                                        <h6 class="currency-widget__title text-white">@lang('Trial Users')</h6>
                                        <h3 class="currency-widget__amount text-white">{{ @$tenantData['trial_subscriptions'] ?? 0 }}</h3>
                                    </div>
                                    <div class="currency-widget__icon">
                                        <i class="las la-clock"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($tenantData['popular_plan']) && $tenantData['popular_plan'])
                <div class="col-xxl-3 col-sm-6">
                    <div class="card bg-primary">
                        <div class="card-body">
                            <div class="currency-widget">
                                <div class="d-flex justify-content-between">
                                    <div class="currency-widget__content">
                                        <h6 class="currency-widget__title text-white">@lang('Popular Plan')</h6>
                                        <h3 class="currency-widget__amount text-white" style="font-size: 1rem;">{{ $tenantData['popular_plan']->name }}</h3>
                                        <small class="text-white">{{ $tenantData['popular_plan_count'] }} @lang('subscribers')</small>
                                    </div>
                                    <div class="currency-widget__icon">
                                        <i class="las la-crown"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Tenant Management Charts & Recent Activity -->
            <div class="row gy-4 mt-4">
                <!-- Enhanced Subscription Overview -->
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">@lang('Subscription Overview')</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3 col-sm-6">
                                    <div class="dashboard-report-card">
                                        <div class="dashboard-report-card__content">
                                            <h4 class="dashboard-report-card__amount text--success">{{ @$tenantData['active_subscriptions'] ?? 0 }}</h4>
                                            <span class="dashboard-report-card__subtitle">@lang('Active Subscriptions')</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="dashboard-report-card">
                                        <div class="dashboard-report-card__content">
                                            <h4 class="dashboard-report-card__amount text--warning">{{ @$tenantData['trial_subscriptions'] ?? 0 }}</h4>
                                            <span class="dashboard-report-card__subtitle">@lang('Trial Subscriptions')</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="dashboard-report-card">
                                        <div class="dashboard-report-card__content">
                                            <h4 class="dashboard-report-card__amount text--danger">{{ @$tenantData['expired_subscriptions'] ?? 0 }}</h4>
                                            <span class="dashboard-report-card__subtitle">@lang('Expired Subscriptions')</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="dashboard-report-card">
                                        <div class="dashboard-report-card__content">
                                            <h4 class="dashboard-report-card__amount text--info">{{ showAmount(@$tenantData['subscription_revenue']) }}</h4>
                                            <span class="dashboard-report-card__subtitle">@lang('Monthly Revenue')</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tenant Status Overview -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">@lang('Tenant Status')</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>@lang('Active Tenants')</span>
                                <span class="badge badge--success">{{ @$tenantData['active_tenants'] ?? 0 }}</span>
                            </div>
                            <div class="progress mb-3">
                                @php
                                    $totalTenants = $tenantData['total_tenants'] ?? 1;
                                    $activePer = $totalTenants > 0 ? ($tenantData['active_tenants'] / $totalTenants) * 100 : 0;
                                @endphp
                                <div class="progress-bar bg-success" style="width: {{ $activePer }}%"></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>@lang('Suspended Tenants')</span>
                                <span class="badge badge--warning">{{ @$tenantData['suspended_tenants'] ?? 0 }}</span>
                            </div>
                            <div class="progress mb-3">
                                @php
                                    $suspendedPer = $totalTenants > 0 ? ($tenantData['suspended_tenants'] / $totalTenants) * 100 : 0;
                                @endphp
                                <div class="progress-bar bg-warning" style="width: {{ $suspendedPer }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div class="row gy-4 mt-4">
                <!-- Recent Tenants -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">@lang('Recent Tenants')</h5>
                            <a href="{{ route('admin.tenants.index') }}" class="btn btn--primary btn--sm">@lang('View All')</a>
                        </div>
                        <div class="card-body">
                            @if(isset($tenantData['recent_tenants']) && count($tenantData['recent_tenants']) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>@lang('Tenant ID')</th>
                                                <th>@lang('Name')</th>
                                                <th>@lang('Status')</th>
                                                <th>@lang('Created')</th>
                                                <th>@lang('Action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tenantData['recent_tenants'] as $tenant)
                                                @php
                                                    $tenantDataJson = json_decode($tenant->data, true) ?? [];
                                                @endphp
                                                <tr>
                                                    <td><code>{{ $tenant->id }}</code></td>
                                                    <td>{{ $tenantDataJson['name'] ?? 'N/A' }}</td>
                                                    <td>
                                                        @if(isset($tenantDataJson['status']) && $tenantDataJson['status'] === 'active')
                                                            <span class="badge badge--success">@lang('Active')</span>
                                                        @else
                                                            <span class="badge badge--warning">@lang('Suspended')</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($tenant->created_at)->format('M j, Y') }} 
                                                        <small class="text-muted d-block">{{ \Carbon\Carbon::parse($tenant->created_at)->diffForHumans() }}</small>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.tenants.show', $tenant->id) }}" class="btn btn--primary btn--sm">
                                                            <i class="las la-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="las la-exclamation-circle text-muted" style="font-size: 48px;"></i>
                                    <p class="text-muted mt-2">@lang('No recent tenants found')</p>
                                    <a href="{{ route('admin.tenants.create') }}" class="btn btn--primary">@lang('Create First Tenant')</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection