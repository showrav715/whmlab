@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-building me-2"></i>
                        @lang('Select Tenant for Subscription Management')
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">@lang('Please select a tenant to manage their subscription plans.')</p>
                    
                    <div class="row">
                        @forelse($tenants as $tenant)
                        <div class="col-md-6 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $tenant->getSetting('name', 'Tenant #' . $tenant->id) }}</h6>
                                            <small class="text-muted">
                                                <i class="las la-globe me-1"></i>
                                                {{ $tenant->domains->first()?->domain ?? 'No domain' }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="las la-calendar me-1"></i>
                                                @lang('Created'): {{ $tenant->created_at->format('M j, Y') }}
                                            </small>
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.subscription.index', ['tenant_id' => $tenant->id]) }}" 
                                               class="btn btn--primary btn-sm">
                                                <i class="las la-arrow-right me-1"></i>
                                                @lang('Select')
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="las la-building" style="font-size: 4rem; color: #ccc;"></i>
                                <h5 class="mt-3">@lang('No Tenants Found')</h5>
                                <p class="text-muted">@lang('There are no tenants in the system yet.')</p>
                                <a href="{{ route('admin.tenants.create') }}" class="btn btn--primary">
                                    <i class="las la-plus me-1"></i>
                                    @lang('Create First Tenant')
                                </a>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection