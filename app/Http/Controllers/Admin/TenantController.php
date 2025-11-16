<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\TenantSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index()
    {
        $pageTitle = 'Tenant Management';
        $tenants = Tenant::with(['domains', 'tenantSubscriptions.subscriptionPlan'])->latest()->paginate(getPaginate());
        $emptyMessage = 'No tenants found';
        return view('admin.tenants.index', compact('pageTitle', 'tenants', 'emptyMessage'));
    }

    public function create()
    {
        $pageTitle = 'Create New Tenant';
        return view('admin.tenants.create', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenant_domains,domain',
            'domain_type' => 'required|in:subdomain,custom',
            'database_name' => 'nullable|required_unless:use_external_db,1|string|max:255',
            'use_external_db' => 'nullable|boolean',
            'remote_db_host' => 'nullable|required_if:use_external_db,1',
            'remote_db_port' => 'nullable|required_if:use_external_db,1|integer',
            'remote_db_name' => 'nullable|required_if:use_external_db,1',
            'remote_db_username' => 'nullable|required_if:use_external_db,1',
            'remote_db_password' => 'nullable|required_if:use_external_db,1',
        ]);

        try {
            DB::beginTransaction();

            // Generate tenant ID
            $tenantId = Str::uuid();

            // Determine database configuration
            $databaseConfig = $this->getDatabaseConfig($request, $tenantId);

            // Create tenant using createWithData method to bypass data saving issues
            $tenant = Tenant::createWithData($tenantId, [
                'name' => $request->name,
                'status' => 'active',
                'db_type' => $request->use_external_db ? 'remote' : 'local',
                'use_external_db' => $request->use_external_db ?? false,
                'database_config' => $databaseConfig,
            ]);

            // Set internal database name for tenancy package
            $tenant->setInternal('db_name', $databaseConfig['database']);

            // Create primary domain
            $domain = $request->domain;
            if ($request->domain_type == 'subdomain' && !str_contains($domain, '.')) {
                $domain = $domain . '.'.env('APP_URL'); // Your main domain
            }
            
            TenantDomain::create([
                'domain' => $domain,
                'tenant_id' => $tenant->id,
                'type' => $request->domain_type,
                'is_primary' => true,
            ]);

            // Setup and verify tenant database
            $this->configureTenantDatabase($tenant, $databaseConfig);

            DB::commit();

            $notify[] = ['success', 'Tenant created successfully'];
            return redirect()->route('admin.tenant.index')->withNotify($notify);

        } catch (\Exception $e) {
            DB::rollback();
            $notify[] = ['error', 'Error creating tenant: ' . $e->getMessage()];
            return back()->withNotify($notify)->withInput();
        }
    }

    public function show(Tenant $tenant)
    {
        // Redirect to edit page since we don't have a separate show view
        return redirect()->route('admin.tenants.edit', $tenant->id);
    }

    public function edit(Tenant $tenant)
    {
        $pageTitle = 'Edit Tenant';
        $tenant->load('domains');
        
        // Debug: Let's make sure data is accessible
        try {
            $tenantName = $tenant->getSetting('name', 'Unnamed Tenant');
            $tenantStatus = $tenant->getSetting('status', 'active');
            $dbConfig = $tenant->getDatabaseConfig();
        } catch (\Exception $e) {
            Log::error('Error loading tenant data for edit', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
        }
        
        return view('admin.tenants.edit', compact('pageTitle', 'tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,suspended,inactive',
            'database_name' => 'nullable|string|max:64',
            'use_external_db' => 'nullable|boolean',
            'remote_db_host' => 'required_if:use_external_db,1|nullable|string',
            'remote_db_port' => 'required_if:use_external_db,1|nullable|integer|min:1|max:65535',
            'remote_db_name' => 'required_if:use_external_db,1|nullable|string|max:64',
            'remote_db_username' => 'required_if:use_external_db,1|nullable|string',
            'remote_db_password' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Get current tenant data
            $currentData = json_decode(DB::table('tenants')->where('id', $tenant->id)->value('data'), true);
            
            // Update basic info
            $updatedData = array_merge($currentData, [
                'name' => $request->name,
                'status' => $request->status,
            ]);

            // Handle database configuration
            if ($request->use_external_db) {
                // External database configuration
                $updatedData['db_type'] = 'remote';
                $updatedData['database_config'] = [
                    'host' => $request->remote_db_host,
                    'port' => $request->remote_db_port,
                    'database' => $request->remote_db_name,
                    'username' => $request->remote_db_username,
                    'password' => $request->remote_db_password ?: ($currentData['database_config']['password'] ?? ''),
                ];
            } else {
                // Auto database configuration
                $updatedData['db_type'] = 'auto';
                $databaseName = env('DATABASE_PREFIX', '') . $request->database_name;
                $updatedData['database_config'] = [
                    'host' => env('DB_HOST'),
                    'port' => env('DB_PORT'),
                    'database' => $databaseName,
                    'username' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                ];
            }

            // Update tenant data using raw query
            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update(['data' => json_encode($updatedData)]);

            DB::commit();

            $notify[] = ['success', 'Tenant updated successfully'];
            return redirect()->route('admin.tenants.index')->withNotify($notify);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tenant update failed', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
            
            $notify[] = ['error', 'Failed to update tenant: ' . $e->getMessage()];
            return back()->withNotify($notify)->withInput();
        }
    }

    public function destroy(Tenant $tenant)
    {
        try {
            DB::beginTransaction();

            // Delete tenant database
            $this->deleteTenantDatabase($tenant);

            // Delete tenant and domains
            $tenant->domains()->delete();
            $tenant->delete();

            DB::commit();

            $notify[] = ['success', 'Tenant deleted successfully'];
            return redirect()->route('admin.tenants.index')->withNotify($notify);

        } catch (\Exception $e) {
            DB::rollback();
            $notify[] = ['error', 'Error deleting tenant: ' . $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    public function addDomain(Request $request, Tenant $tenant)
    {
        $request->validate([
            'domain' => 'required|string|unique:domains,domain',
            'is_custom' => 'required|boolean',
        ]);

        TenantDomain::create([
            'domain' => $request->domain,
            'tenant_id' => $tenant->id,
            'is_primary' => false,
            'is_custom' => $request->is_custom,
        ]);

        $notify[] = ['success', 'Domain added successfully'];
        return back()->withNotify($notify);
    }

    public function removeDomain(TenantDomain $domain)
    {
        if ($domain->is_primary) {
            $notify[] = ['error', 'Cannot delete primary domain'];
            return back()->withNotify($notify);
        }

        $domain->delete();

        $notify[] = ['success', 'Domain removed successfully'];
        return back()->withNotify($notify);
    }

    private function getDatabaseConfig(Request $request, string $tenantId): array
    {
        if ($request->use_external_db) {
            // External database configuration
            return [
                'database' => $request->remote_db_name,
                'host' => $request->remote_db_host,
                'port' => $request->remote_db_port,
                'username' => $request->remote_db_username,
                'password' => $request->remote_db_password,
            ];
        } else {
            // Local database configuration
            return [
                'database' => env('DATABASE_PREFIX', '') . $request->database_name,
                    'host' => env('DB_HOST'),
                    'port' => env('DB_PORT'),
                    'username' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                ];
        }
    }

    private function configureTenantDatabase(Tenant $tenant, array $config)
    {
        $databaseName = $config['database'];
        $dbType = $tenant->getSetting('db_type');

        // Check if database exists first
        $this->validateDatabaseExists($config);

        Log::info("Configuring tenant database", [
            'tenant_id' => $tenant->id,
            'database' => $databaseName,
            'db_type' => $dbType
        ]);

        try {
            // Test database connection
            $this->testDatabaseConnection($config);
            
            Log::info("Database connection successful", ['database' => $databaseName]);

            // Run migrations on tenant database
            tenancy()->initialize($tenant);
            Artisan::call('migrate', ['--force' => true]);
            tenancy()->end();

            Log::info("Tenant migrations completed", ['tenant_id' => $tenant->id]);

        } catch (\Exception $e) {
            Log::error("Tenant database configuration failed", [
                'tenant_id' => $tenant->id,
                'database' => $databaseName,
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception("Database '{$databaseName}' configuration failed: " . $e->getMessage());
        }
    }

    private function validateDatabaseExists(array $config)
    {
        $databaseName = $config['database'];
        
        try {
            // For local databases, check if database exists
            if ($config['host'] === '127.0.0.1' || $config['host'] === 'localhost') {
                $databases = DB::select('SHOW DATABASES');
                $exists = false;
                foreach ($databases as $db) {
                    if ($db->Database === $databaseName) {
                        $exists = true;
                        break;
                    }
                }
                
                if (!$exists) {
                    throw new \Exception("Database '{$databaseName}' does not exist. Please create it manually first.");
                }
            }
        } catch (\Exception $e) {
            throw new \Exception("Failed to validate database existence: " . $e->getMessage());
        }
    }

    private function testDatabaseConnection(array $config)
    {
        // Configure temporary connection to test
        config([
            'database.connections.tenant_test' => [
                'driver' => 'mysql',
                'host' => $config['host'],
                'port' => $config['port'],
                'database' => $config['database'],
                'username' => $config['username'],
                'password' => $config['password'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]
        ]);

        // Test connection
        DB::connection('tenant_test')->getPdo();
        
        // Clean up test connection
        DB::purge('tenant_test');
    }

    private function verifyDatabaseExists(array $config, string $dbType)
    {
        $databaseName = $config['database'];
        
        try {
            if ($dbType === 'remote') {
                // For remote databases, test the connection
                DB::connection('tenant')->select('SELECT 1');
            } else {
                // For local databases, check if database exists
                $databases = DB::select('SHOW DATABASES');
                $exists = false;
                foreach ($databases as $db) {
                    if ($db->Database === $databaseName) {
                        $exists = true;
                        break;
                    }
                }
                
                if (!$exists) {
                    throw new \Exception("Database '{$databaseName}' does not exist. Please create it manually first.");
                }
            }
        } catch (\Exception $e) {
            throw new \Exception("Database verification failed: " . $e->getMessage());
        }
    }

    private function deleteTenantDatabase(Tenant $tenant)
    {
        $config = $tenant->getSetting('database_config');
        $databaseName = $config['database'];
        $dbType = $tenant->getSetting('db_type');

        // We don't delete any databases since they are all manually created
        // Log the action instead
        Log::info("Tenant {$tenant->id} deleted. Database '{$databaseName}' (type: {$dbType}) was not dropped - manual cleanup required if needed.");
        
        // Note: All databases (auto, custom, remote) are manually managed
        // so we don't automatically delete them when tenant is removed
    }

    public function status(Tenant $tenant)
    {
        try {
            $currentStatus = $tenant->getSetting('status', 'active');
            $newStatus = $currentStatus == 'active' ? 'suspended' : 'active';
            
            Log::info('Changing tenant status', [
                'tenant_id' => $tenant->id,
                'current_status' => $currentStatus,
                'new_status' => $newStatus
            ]);
            
            $tenant->setSetting('status', $newStatus);
            
            // Verify the change
            $verifyStatus = $tenant->getSetting('status');
            Log::info('Status change verification', [
                'tenant_id' => $tenant->id,
                'expected' => $newStatus,
                'actual' => $verifyStatus
            ]);
            
            $notify[] = ['success', "Tenant status updated to {$newStatus} successfully"];
            return back()->withNotify($notify);
            
        } catch (\Exception $e) {
            Log::error('Failed to update tenant status', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
            
            $notify[] = ['error', 'Failed to update tenant status: ' . $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    public function migrate(Request $request) 
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id'
        ]);

        try {
            $tenant = Tenant::findOrFail($request->tenant_id);
            
            tenancy()->initialize($tenant);
            Artisan::call('migrate', ['--force' => true]);
            tenancy()->end();
            
            return response()->json([
                'success' => true,
                'message' => 'Database migrated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Migration failed: ' . $e->getMessage()
            ]);
        }
    }

    public function domainsIndex(Request $request)
    {
        $tenantId = $request->tenant_id;
        $domains = TenantDomain::where('tenant_id', $tenantId)->get();
        
        $html = view('admin.tenants.domains-list', compact('domains', 'tenantId'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function domainStore(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'domain' => 'required|string|unique:tenant_domains,domain',
            'type' => 'required|in:subdomain,custom'
        ]);

        try {
            $domain = $request->domain;
            if ($request->type == 'subdomain' && !str_contains($domain, '.')) {
                $domain = $domain . '.' . config('app.domain', 'localhost');
            }

            TenantDomain::create([
                'domain' => $domain,
                'tenant_id' => $request->tenant_id,
                'type' => $request->type,
                'is_primary' => false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Domain added successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding domain: ' . $e->getMessage()
            ]);
        }
    }

    public function domainDestroy(Request $request)
    {
        $request->validate([
            'domain_id' => 'required|exists:tenant_domains,id'
        ]);

        try {
            $domain = TenantDomain::findOrFail($request->domain_id);
            
            if ($domain->is_primary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete primary domain'
                ]);
            }

            $domain->delete();

            return response()->json([
                'success' => true,
                'message' => 'Domain deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting domain: ' . $e->getMessage()
            ]);
        }
    }

    public function domainSetPrimary(Request $request)
    {
        $request->validate([
            'domain_id' => 'required|exists:tenant_domains,id'
        ]);

        try {
            $domain = TenantDomain::findOrFail($request->domain_id);
            $domain->setAsPrimary();

            return response()->json([
                'success' => true,
                'message' => 'Primary domain updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating primary domain: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show tenant subscriptions page
     */
    public function subscriptions(Tenant $tenant)
    {
        $pageTitle = 'Tenant Subscriptions - ' . $tenant->getSetting('name', 'Unnamed');
        $tenant->load(['tenantSubscriptions.subscriptionPlan']);
        $subscriptionPlans = \App\Models\SubscriptionPlan::active()->ordered()->get();
        
        return view('admin.tenants.subscriptions', compact('pageTitle', 'tenant', 'subscriptionPlans'));
    }

    /**
     * Add subscription to tenant
     */
    public function addSubscription(Request $request, Tenant $tenant)
    {
        $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'started_at' => 'required|date',
            'expires_at' => 'required|date|after:started_at',
            'price_paid' => 'nullable|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $plan = \App\Models\SubscriptionPlan::findOrFail($request->subscription_plan_id);
            
            // Create new subscription
            \App\Models\TenantSubscription::create([
                'tenant_id' => $tenant->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'started_at' => $request->started_at,
                'expires_at' => $request->expires_at,
                'price_paid' => $request->price_paid ?? $plan->price,
                'plan_features' => $plan->features
            ]);

            DB::commit();

            $notify[] = ['success', 'Subscription added successfully'];
            return back()->withNotify($notify);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding subscription', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);

            $notify[] = ['error', 'Failed to add subscription: ' . $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    /**
     * Remove subscription from tenant
     */
    public function removeSubscription(Tenant $tenant, TenantSubscription $subscription)
    {
        try {
            if ($subscription->tenant_id !== $tenant->id) {
                $notify[] = ['error', 'Subscription does not belong to this tenant'];
                return back()->withNotify($notify);
            }

            $subscription->update(['status' => 'cancelled']);

            $notify[] = ['success', 'Subscription cancelled successfully'];
            return back()->withNotify($notify);

        } catch (\Exception $e) {
            Log::error('Error removing subscription', [
                'tenant_id' => $tenant->id,
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);

            $notify[] = ['error', 'Failed to cancel subscription: ' . $e->getMessage()];
            return back()->withNotify($notify);
        }
    }
}