@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-header">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="mb-0">@lang('All Tenants')</h6>
                            <div class="icon-list">
                                <span class="icon-list-item">{{ $tenants->total() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <form action="">
                            <div class="input-group w-auto flex-fill">
                                <input type="search" name="search" class="form-control" placeholder="@lang('Search by name, domain')" value="{{ request()->search }}">
                                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                        <a href="{{ route('admin.tenants.create') }}" class="btn btn-outline--primary h-45">
                            <i class="fas fa-plus"></i>@lang('Add New Tenant')
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Name')</th>
                                <th>@lang('Primary Domain')</th>
                                <th>@lang('Database')</th>
                                <th>@lang('Subscription')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Created')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tenants as $tenant)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $tenant->getSetting('name', 'Unnamed Tenant') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $tenant->id }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @php $primaryDomain = $tenant->domains->where('is_primary', true)->first(); @endphp
                                        @if($primaryDomain)
                                            <a href="{{ $primaryDomain->getUrl() }}" target="_blank" class="text--primary">
                                                {{ $primaryDomain->domain }}
                                                <i class="fas fa-external-link-alt ms-1"></i>
                                            </a>
                                            <br>
                                            <small class="badge badge--{{ $primaryDomain->type == 'subdomain' ? 'primary' : 'success' }}">
                                                {{ ucfirst($primaryDomain->type) }}
                                            </small>
                                        @else
                                            <span class="text-muted">@lang('No domain')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <small class="text-muted">@lang('Database'):</small><br>
                                            <span class="font-weight-bold">{{ $tenant->getDatabaseName() }}</span>
                                            <br>
                                            <small class="badge badge--{{ $tenant->getSetting('db_type', 'auto') == 'auto' ? 'primary' : ($tenant->getSetting('db_type') == 'custom' ? 'warning' : 'info') }}">
                                                {{ ucfirst($tenant->getSetting('db_type', 'auto')) }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        @php 
                                            $currentSubscription = $tenant->tenantSubscriptions()->active()->with('subscriptionPlan')->first();
                                        @endphp
                                        @if($currentSubscription)
                                            <div>
                                                <strong>{{ $currentSubscription->subscriptionPlan->name }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    ${{ $currentSubscription->subscriptionPlan->formatted_price }}/{{ $currentSubscription->subscriptionPlan->billing_cycle }}
                                                </small>
                                                <br>
                                                <small class="badge badge--{{ $currentSubscription->status_badge }}">
                                                    {{ ucfirst($currentSubscription->status) }}
                                                    @if($currentSubscription->isActive())
                                                        ({{ round($currentSubscription->daysRemaining()) }} days left)
                                                    @endif
                                                </small>
                                            </div>
                                        @else
                                            <span class="text-muted">@lang('No subscription')</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php $status = $tenant->getSetting('status', 'active'); @endphp
                                        <span class="badge badge--{{ $status == 'active' ? 'success' : ($status == 'suspended' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            {{ showDateTime($tenant->created_at, 'd M Y') }}<br>
                                            <small class="text-muted">{{ $tenant->created_at->diffForHumans() }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="button-group">
                                            <a href="{{ route('admin.tenants.edit', $tenant->id) }}" class="btn btn-sm btn-outline--primary" title="@lang('Edit Tenant')">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                            
                                            <a href="{{ route('admin.tenants.subscriptions', $tenant->id) }}" class="btn btn-sm btn-outline--info" title="@lang('Manage Subscriptions')">
                                                <i class="fa fa-list"></i>
                                            </a>
                                            
                                            <form action="{{ route('admin.tenants.destroy', $tenant->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('@lang('Are you sure you want to delete this tenant permanently? This action cannot be undone!')')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline--danger" title="@lang('Delete Tenant')">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </form>
                                            
                                            @if($tenant->getSetting('status', 'active') == 'active')
                                                <form action="{{ route('admin.tenants.status', $tenant->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('@lang('Are you sure to suspend this tenant?')')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline--warning" title="@lang('Suspend Tenant')">
                                                        <i class="fa fa-pause"></i>
                                                    </button>
                                                </form>
                                            @elseif($tenant->getSetting('status', 'active') == 'suspended')
                                                <form action="{{ route('admin.tenants.status', $tenant->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('@lang('Are you sure to activate this tenant?')')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline--success" title="@lang('Activate Tenant')">
                                                        <i class="fa fa-play"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.tenants.status', $tenant->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('@lang('Are you sure to activate this tenant?')')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline--info" title="@lang('Activate Tenant')">
                                                        <i class="fa fa-power-off"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($tenants->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($tenants) }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Add/Edit Modal --}}
<div id="cuModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.tenants.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Tenant Name') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Primary Domain') <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="domain" placeholder="@lang('Enter subdomain or full domain')" required>
                            <div class="input-group-text">
                                <small>.{{ config('app.domain', 'example.com') }}</small>
                            </div>
                        </div>
                        <small class="text-muted">@lang('Enter subdomain (e.g., tenant1) or full custom domain (e.g., tenant.com)')</small>
                    </div>
                    <div class="form-group">
                        <label>@lang('Domain Type')</label>
                        <select class="form-control" name="domain_type">
                            <option value="subdomain">@lang('Subdomain')</option>
                            <option value="custom">@lang('Custom Domain')</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>@lang('Database Type')</label>
                        <select class="form-control" name="db_type" id="dbType">
                            <option value="auto">@lang('Auto Generate')</option>
                            <option value="custom">@lang('Custom Database')</option>
                            <option value="remote">@lang('Remote Database')</option>
                        </select>
                    </div>
                    
                    <div id="customDbFields" style="display: none;">
                        <div class="form-group">
                            <label>@lang('Database Name')</label>
                            <input type="text" class="form-control" name="custom_db_name">
                        </div>
                    </div>
                    
                    <div id="remoteDbFields" style="display: none;">
                        <div class="form-group">
                            <label>@lang('Database Host')</label>
                            <input type="text" class="form-control" name="remote_db_host">
                        </div>
                        <div class="form-group">
                            <label>@lang('Database Name')</label>
                            <input type="text" class="form-control" name="remote_db_name">
                        </div>
                        <div class="form-group">
                            <label>@lang('Database Username')</label>
                            <input type="text" class="form-control" name="remote_db_username">
                        </div>
                        <div class="form-group">
                            <label>@lang('Database Password')</label>
                            <input type="password" class="form-control" name="remote_db_password">
                        </div>
                        <div class="form-group">
                            <label>@lang('Database Port')</label>
                            <input type="number" class="form-control" name="remote_db_port" value="3306">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Domain Management Modal --}}
<div id="domainModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Manage Domains')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="domainsList">
                    <!-- Domains will be loaded here -->
                </div>
                <hr>
                <form id="addDomainForm">
                    <h6>@lang('Add New Domain')</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Domain')</label>
                                <input type="text" class="form-control" name="domain" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>@lang('Type')</label>
                                <select class="form-control" name="type">
                                    <option value="subdomain">@lang('Subdomain')</option>
                                    <option value="custom">@lang('Custom')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn--primary form-control">@lang('Add')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<x-confirmation-modal />
@endsection

@push('script')
<script>
    (function ($) {
        "use strict";
        
        // Database type change handler
        $('#dbType').on('change', function() {
            const type = $(this).val();
            $('#customDbFields, #remoteDbFields').hide();
            if (type === 'custom') {
                $('#customDbFields').show();
            } else if (type === 'remote') {
                $('#remoteDbFields').show();
            }
        });
        
        
    })(jQuery);
</script>
@endpush