@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">
    <!-- System Health Overview -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="las la-tachometer-alt"></i> 
                    @lang('External Database Performance Monitor')
                    <small class="text-muted">(cPanel Optimized)</small>
                </h5>
                <div class="card-header-actions">
                    <button class="btn btn-outline-primary btn-sm" onclick="refreshHealthCheck()">
                        <i class="las la-sync-alt"></i> @lang('Refresh')
                    </button>
                    <button class="btn btn-outline-warning btn-sm" onclick="testExternalDB()">
                        <i class="las la-database"></i> @lang('Test External DB')
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="clearAllCaches()">
                        <i class="las la-trash"></i> @lang('Clear Caches')
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Real-time Status Cards -->
                <div class="row" id="status-cards">
                    <div class="col-xl-3 col-sm-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-uppercase">Database Health</h6>
                                        <h3 class="mb-0" id="db-status">Loading...</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="las la-database font-size-40"></i>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small>Response: <span id="db-response-time">--</span>ms</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-sm-6">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-uppercase">Memory Usage</h6>
                                        <h3 class="mb-0" id="memory-usage">Loading...</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="las la-memory font-size-40"></i>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small>Limit: <span id="memory-limit">--</span></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-sm-6">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-uppercase">Execution Limit</h6>
                                        <h3 class="mb-0" id="exec-limit">Loading...</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="las la-clock font-size-40"></i>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small>PHP Version: <span id="php-version">--</span></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-sm-6">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-uppercase">Performance</h6>
                                        <h3 class="mb-0" id="performance-status">Loading...</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="las la-chart-line font-size-40"></i>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small>Last check: <span id="last-check">--</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Test Results -->
                <div class="row mt-4" id="performance-tests" style="display: none;">
                    <div class="col-12">
                        <h6><i class="las la-flask"></i> Performance Test Results</h6>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Test Type</th>
                                        <th>Status</th>
                                        <th>Response Time</th>
                                        <th>Performance Rating</th>
                                    </tr>
                                </thead>
                                <tbody id="test-results">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h6><i class="las la-lightbulb"></i> Performance Recommendations</h6>
                        <div id="recommendations" class="alert alert-info">
                            <ul class="mb-0" id="recommendations-list">
                                <li>Loading recommendations...</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">System Configuration</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <td>PHP Version:</td>
                                        <td>{{ $systemInfo['php_version'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Laravel Version:</td>
                                        <td>{{ $systemInfo['laravel_version'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Memory Limit:</td>
                                        <td>{{ $systemInfo['memory_limit'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Execution Time:</td>
                                        <td>{{ $systemInfo['execution_time_limit'] ?? 'N/A' }}s</td>
                                    </tr>
                                    <tr>
                                        <td>Cache Driver:</td>
                                        <td>{{ $systemInfo['cache_driver'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Session Driver:</td>
                                        <td>{{ $systemInfo['session_driver'] ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Database Statistics</h6>
                            </div>
                            <div class="card-body" id="database-stats">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- cPanel Specific Tips -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <h6><i class="las la-server"></i> cPanel Optimization Tips:</h6>
                            <ul class="mb-0">
                                <li>Use <code>optimizeForExternalDB()</code> function before heavy database operations</li>
                                <li>Enable file-based caching: <code>CACHE_STORE=file</code> in .env</li>
                                <li>Add performance settings to .htaccess file</li>
                                <li>Monitor resource usage regularly through this dashboard</li>
                                <li>Use batch processing for large datasets with <code>batchProcess()</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Auto-refresh modal -->
<div class="modal fade" id="autoRefreshModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Auto Refresh</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="autoRefreshEnabled">
                    <label class="form-check-label" for="autoRefreshEnabled">
                        Enable auto-refresh every 30 seconds
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    let autoRefreshInterval;
    
    $(document).ready(function() {
        // Initial load
        refreshHealthCheck();
        
        // Auto-refresh every 30 seconds if enabled
        $('#autoRefreshEnabled').change(function() {
            if (this.checked) {
                autoRefreshInterval = setInterval(refreshHealthCheck, 30000);
                toastr.success('Auto-refresh enabled');
            } else {
                clearInterval(autoRefreshInterval);
                toastr.info('Auto-refresh disabled');
            }
        });

        // Auto-refresh modal trigger
        $(document).keydown(function(e) {
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                $('#autoRefreshModal').modal('show');
            }
        });
    });

    function refreshHealthCheck() {
        // Show loading state
        updateLoadingState(true);
        
        $.get('{{ route("admin.performance.health.check") }}')
            .done(function(response) {
                if (response.success) {
                    updateStatusCards(response);
                    updateDatabaseStats(response.database_stats);
                    updateRecommendations(response.recommendations);
                } else {
                    toastr.error('Health check failed');
                }
            })
            .fail(function(xhr) {
                toastr.error('Failed to fetch health data');
                console.error(xhr.responseText);
            })
            .always(function() {
                updateLoadingState(false);
            });
    }

    function testExternalDB() {
        $('#performance-tests').hide();
        toastr.info('Running external database performance tests...');
        
        $.get('{{ route("admin.performance.test.external.db") }}')
            .done(function(response) {
                if (response.success) {
                    updatePerformanceTests(response.tests);
                    $('#performance-tests').show();
                    toastr.success('Performance tests completed');
                } else {
                    toastr.error('Performance tests failed');
                }
            })
            .fail(function(xhr) {
                toastr.error('Failed to run performance tests');
                console.error(xhr.responseText);
            });
    }

    function clearAllCaches() {
        toastr.info('Clearing all caches...');
        
        $.post('{{ route("admin.performance.clear.caches") }}', {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            if (response.success) {
                toastr.success('All caches cleared successfully');
                refreshHealthCheck(); // Refresh after clearing
            } else {
                toastr.error('Failed to clear caches');
            }
        })
        .fail(function(xhr) {
            toastr.error('Cache clear operation failed');
            console.error(xhr.responseText);
        });
    }

    function updateStatusCards(data) {
        // Database Health
        const dbHealth = data.database_health;
        $('#db-status').text(dbHealth.healthy ? 'Healthy' : 'Error');
        $('#db-response-time').text(dbHealth.response_time_ms || '--');
        
        // Memory Usage
        const memoryUsage = formatBytes(data.system_info.memory_usage);
        $('#memory-usage').text(memoryUsage);
        $('#memory-limit').text(data.system_info.memory_limit);
        
        // Execution Limit
        $('#exec-limit').text(data.system_info.execution_time_limit + 's');
        $('#php-version').text(data.system_info.php_version);
        
        // Performance
        $('#performance-status').text(dbHealth.status || 'Unknown');
        $('#last-check').text(new Date(data.timestamp).toLocaleTimeString());
    }

    function updateDatabaseStats(stats) {
        const html = `
            <table class="table table-sm">
                <tr><td>Total Users:</td><td>${stats.users_count || 0}</td></tr>
                <tr><td>Total Admins:</td><td>${stats.admins_count || 0}</td></tr>
                <tr><td>Database Tables:</td><td>${stats.total_tables || 0}</td></tr>
            </table>
        `;
        $('#database-stats').html(html);
    }

    function updateRecommendations(recommendations) {
        const listItems = recommendations.map(rec => `<li>${rec}</li>`).join('');
        $('#recommendations-list').html(listItems);
    }

    function updatePerformanceTests(tests) {
        let html = '';
        
        Object.keys(tests).forEach(testName => {
            const test = tests[testName];
            const statusBadge = test.success ? 
                `<span class="badge bg-success">Success</span>` : 
                `<span class="badge bg-danger">Failed</span>`;
            
            const performanceBadge = test.status ? 
                `<span class="badge bg-${getPerformanceBadgeColor(test.status)}">${test.status}</span>` : 
                '<span class="badge bg-secondary">N/A</span>';
            
            html += `
                <tr>
                    <td>${testName.replace(/_/g, ' ').toUpperCase()}</td>
                    <td>${statusBadge}</td>
                    <td>${test.time_ms || '--'}ms</td>
                    <td>${performanceBadge}</td>
                </tr>
            `;
        });
        
        $('#test-results').html(html);
    }

    function getPerformanceBadgeColor(status) {
        switch(status) {
            case 'excellent': return 'success';
            case 'good': return 'info';
            case 'slow': return 'warning';
            default: return 'secondary';
        }
    }

    function updateLoadingState(loading) {
        if (loading) {
            $('#db-status').text('Loading...');
            $('#memory-usage').text('Loading...');
            $('#exec-limit').text('Loading...');
            $('#performance-status').text('Loading...');
        }
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Keyboard shortcuts
    $(document).keydown(function(e) {
        if (e.ctrlKey) {
            switch(e.key) {
                case 'r':
                    e.preventDefault();
                    refreshHealthCheck();
                    break;
                case 't':
                    e.preventDefault();
                    testExternalDB();
                    break;
                case 'c':
                    e.preventDefault();
                    clearAllCaches();
                    break;
            }
        }
    });
</script>
@endpush