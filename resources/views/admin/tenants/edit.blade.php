@extends('admin.layouts.app')
@section('panel')
@php
    // Pre-load all tenant data to avoid multiple calls
    $tenantName = $tenant->getSetting('name', '');
    $tenantStatus = $tenant->getSetting('status', 'active');
    $dbConfig = $tenant->getDatabaseConfig() ?? [];
    $dbType = $tenant->getSetting('db_type', 'auto');
    $statusClass = $tenantStatus == 'active' ? 'success' : ($tenantStatus == 'suspended' ? 'warning' : 'danger');
@endphp
<div class="row">
    <div class="col-lg-12">
        <form action="{{ route('admin.tenants.update', $tenant->id) }}" method="post">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Edit Tenant: {{ $tenantName }}</h4>
                    <div>
                        <span class="badge badge-{{ $statusClass }}">{{ ucfirst($tenantStatus) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">Tenant Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $tenantName) }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="active" {{ old('status', $tenantStatus) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="suspended" {{ old('status', $tenantStatus) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="inactive" {{ old('status', $tenantStatus) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Current Database Info -->
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="las la-database"></i> Current Database Configuration</h6>
                                <p><strong>Database Type:</strong> {{ ucfirst($dbType) }}</p>
                                @if(!empty($dbConfig))
                                    <p><strong>Database:</strong> {{ $dbConfig['database'] ?? 'Not configured' }}</p>
                                    <p><strong>Host:</strong> {{ $dbConfig['host'] ?? 'Not configured' }}</p>
                                    @if($dbType === 'remote')
                                        <p><strong>Port:</strong> {{ $dbConfig['port'] ?? 'Not configured' }}</p>
                                        <p><strong>Username:</strong> {{ $dbConfig['username'] ?? 'Not configured' }}</p>
                                    @endif
                                @else
                                    <p class="text-muted">Database configuration not found</p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Primary Domain Info (Read Only) -->
                        <div class="col-md-12">
                            <div class="alert alert-secondary">
                                <h6><i class="las la-globe"></i> Primary Domain</h6>
                                @if($tenant->domains->count() > 0)
                                    @php $primaryDomain = $tenant->domains->where('is_primary', true)->first(); @endphp
                                    @if($primaryDomain)
                                        <p><strong>Domain:</strong> {{ $primaryDomain->domain }}</p>
                                        <p><strong>Type:</strong> {{ ucfirst($primaryDomain->type) }}</p>
                                        <small class="text-muted">Domain management is handled separately</small>
                                    @endif
                                @else
                                    <p class="text-muted">No domain configured for this tenant.</p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Database Configuration Update -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Update Database Name</label>
                                <input type="text" name="database_name" class="form-control" id="database_name" 
                                       value="{{ old('database_name', $dbConfig['database'] ?? '') }}" 
                                       placeholder="tenant_database_name">
                                <small class="text-muted">Update database name for this tenant (database must exist on server)</small>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" name="use_external_db" class="form-check-input" id="use_external_db" 
                                           value="1" {{ old('use_external_db') !== null ? (old('use_external_db') ? 'checked' : '') : ($dbType === 'remote' ? 'checked' : '') }}>
                                    <label class="form-check-label" for="use_external_db">
                                        Use External Database
                                    </label>
                                </div>
                                <small class="text-muted">Check this if you want to use a remote/external database server</small>
                            </div>
                        </div>
                        
                        <!-- Remote Database Configuration -->
                        <div class="col-md-12 db-config" id="remote_db_config" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">External Database Configuration</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Database Host</label>
                                                <input type="text" name="remote_db_host" class="form-control" 
                                                       value="{{ old('remote_db_host', $dbType === 'remote' ? ($dbConfig['host'] ?? '') : '') }}" placeholder="localhost">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Database Port</label>
                                                <input type="number" name="remote_db_port" class="form-control" 
                                                       value="{{ old('remote_db_port', $dbType === 'remote' ? ($dbConfig['port'] ?? 3306) : 3306) }}" placeholder="3306">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Database Name</label>
                                                <input type="text" name="remote_db_name" class="form-control" 
                                                       value="{{ old('remote_db_name', $dbType === 'remote' ? ($dbConfig['database'] ?? '') : '') }}" placeholder="tenant_database">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Database Username</label>
                                                <input type="text" name="remote_db_username" class="form-control" 
                                                       value="{{ old('remote_db_username', $dbType === 'remote' ? ($dbConfig['username'] ?? '') : '') }}" placeholder="username">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">Database Password</label>
                                                <input type="password" name="remote_db_password" class="form-control" 
                                                       value="{{ old('remote_db_password') }}" placeholder="Leave blank to keep current password">
                                                <small class="text-muted">Leave blank to keep current password</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="las la-save"></i> Update Tenant
                    </button>
                    <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                        <i class="las la-times"></i> Cancel
                    </a>
                    
                    @if($tenantStatus == 'active')
                        <form action="{{ route('admin.tenants.status', $tenant->id) }}" method="POST" style="display: inline;" class="ms-2">
                            @csrf
                            <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to suspend this tenant?')">
                                <i class="las la-pause"></i> Suspend
                            </button>
                        </form>
                    @elseif($tenantStatus == 'suspended')
                        <form action="{{ route('admin.tenants.status', $tenant->id) }}" method="POST" style="display: inline;" class="ms-2">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to activate this tenant?')">
                                <i class="las la-play"></i> Activate
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.tenants.status', $tenant->id) }}" method="POST" style="display: inline;" class="ms-2">
                            @csrf
                            <button type="submit" class="btn btn-info" onclick="return confirm('Are you sure you want to activate this tenant?')">
                                <i class="las la-power-off"></i> Power On
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        $('#use_external_db').on('change', function() {
            if ($(this).is(':checked')) {
                // Show external database config
                $('#remote_db_config').show();
                // Hide default database name field
                $('#database_name').closest('.form-group').hide();
            } else {
                // Hide external database config
                $('#remote_db_config').hide();
                // Show default database name field
                $('#database_name').closest('.form-group').show();
            }
        });
        
        // Trigger change on page load
        $('#use_external_db').trigger('change');
    });
</script>
@endpush