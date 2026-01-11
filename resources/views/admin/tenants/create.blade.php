@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <form action="{{ route('admin.tenants.store') }}" method="post">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Create New Tenant</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">Tenant Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        
                        <!-- Domain Configuration -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">Domain</label>
                                <input type="text" name="domain" class="form-control" value="{{ old('domain') }}" placeholder="example or example.com" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">Domain Type</label>
                                <select name="domain_type" class="form-control" required>
                                    <option value="">Choose Domain Type</option>
                                    <option value="subdomain" {{ old('domain_type') == 'subdomain' ? 'selected' : '' }}>Subdomain (subdomain.whmlab.test)</option>
                                    <option value="custom" {{ old('domain_type') == 'custom' ? 'selected' : '' }}>Custom Domain (example.com)</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Database Configuration -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Database Name</label>
                                <input type="text" name="database_name" class="form-control" id="database_name" value="{{ old('database_name') }}" placeholder="tenant_database_name">
                                <small class="text-muted">Default database name for this tenant (must exist on server)</small>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" name="use_external_db" class="form-check-input" id="use_external_db" value="1" {{ old('use_external_db') ? 'checked' : '' }}>
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
                                                <input type="text" name="remote_db_host" class="form-control" value="{{ old('remote_db_host') }}" placeholder="localhost">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Database Port</label>
                                                <input type="number" name="remote_db_port" class="form-control" value="{{ old('remote_db_port', 3306) }}" placeholder="3306">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Database Name</label>
                                                <input type="text" name="remote_db_name" class="form-control" value="{{ old('remote_db_name') }}" placeholder="tenant_database">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Database Username</label>
                                                <input type="text" name="remote_db_username" class="form-control" value="{{ old('remote_db_username') }}" placeholder="username">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">Database Password</label>
                                                <input type="password" name="remote_db_password" class="form-control" value="{{ old('remote_db_password') }}" placeholder="password">
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
                        <i class="las la-plus"></i> Create Tenant
                    </button>
                    <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                        <i class="las la-times"></i> Cancel
                    </a>
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
        
        // Auto-generate database name based on domain
        $('input[name="domain"]').on('input', function() {
            if (!$('#use_external_db').is(':checked')) {
                var domain = $(this).val().toLowerCase();
                // Remove special characters and replace with underscore
                var dbName = 'tenant_' + domain.replace(/[^a-z0-9]/g, '_').replace(/_+/g, '_').replace(/^_|_$/g, '');
                $('#database_name').val(dbName);
            }
        });
    });
</script>
@endpush