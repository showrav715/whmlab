<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Middleware\IdentificationMiddleware;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use App\Models\Tenant;
use App\Models\TenantDomain;

class InitializeTenancyByDomain extends IdentificationMiddleware
{
    /** @var callable|null */
    public static $onFail;

    /** @var callable|null */
    public static $onSuccess;

    public function handle(Request $request, Closure $next)
    {
 
        $host = $request->getHost();
        
        // Check if this is central domain
        if ($this->isCentralDomain($host)) {
            return $next($request);
        }

        try {
            // Manual tenant resolution from MAIN database
            $tenant = $this->resolveTenantManually($host);

            if ($tenant) {
                // Get database config from MAIN database before switching
                $dbConfig = $this->getTenantDatabaseConfigFromMain($tenant->id);
                
                if (!$dbConfig) {
                    Log::error('Tenant database configuration not found', [
                        'tenant_id' => $tenant->id,
                        'domain' => $host
                    ]);
                    abort(503, 'Service temporarily unavailable - Database not configured');
                }

                // Switch to tenant database
                $this->switchToTenantDatabase($dbConfig);

                // Verify database switch actually happened
                $currentDb = \Illuminate\Support\Facades\DB::select('SELECT DATABASE() as db')[0]->db;
                $expectedDb = $dbConfig['database'];
                
                if ($currentDb !== $expectedDb) {
                    Log::error('Database switch verification failed', [
                        'tenant_id' => $tenant->id,
                        'domain' => $host,
                        'current_db' => $currentDb,
                        'expected_db' => $expectedDb
                    ]);
                    abort(503, 'Service temporarily unavailable - Database switch failed');
                }

                // Store tenant info in request for later use (no database access)
                $request->attributes->set('tenant_id', $tenant->id);
                $request->attributes->set('tenant_domain', $host);

                Log::info('Successfully switched to tenant database', [
                    'tenant_id' => $tenant->id,
                    'domain' => $host,
                    'database' => $currentDb,
                    'verified_db' => $expectedDb
                ]);

                if (static::$onSuccess) {
                    call_user_func(static::$onSuccess, $tenant, $request);
                }

                return $next($request);
            } else {
                // Domain not found or suspended, show 404
                abort(404, 'Tenant not found or suspended');
            }
        } catch (\Exception $e) {
            // Log error
            Log::error('Tenancy initialization failed: ' . $e->getMessage());
            abort(503, 'Service temporarily unavailable');
        }

        // Tenant not found or inactive
        if (static::$onFail) {
            return call_user_func(static::$onFail, $request);
        }

        // Default: redirect to main site
        return redirect()->to(config('app.url'));
    }

    protected function isCentralDomain(string $domain): bool
    {
        $centralDomains = config('tenancy.central_domains', []);
        
        // Log domain check for debugging
        Log::info('Checking if central domain', [
            'domain' => $domain,
            'central_domains' => $centralDomains
        ]);
        
        foreach ($centralDomains as $centralDomain) {
            if ($domain === $centralDomain) {
                Log::info('Domain is central: ' . $domain);
                return true;
            }
        }

        Log::info('Domain is NOT central: ' . $domain);
        return false;
    }

    /**
     * Manually resolve tenant by domain without Stancl package
     * IMPORTANT: This method uses MAIN database for tenant verification
     */
    protected function resolveTenantManually(string $domain): ?Tenant
    {
        try {
            // FORCE main database connection for tenant lookup
            $originalConnection = \Illuminate\Support\Facades\DB::getDefaultConnection();
            \Illuminate\Support\Facades\DB::setDefaultConnection('mysql');
            
            // Find tenant domain from MAIN database
            $tenantDomain = \App\Models\TenantDomain::where('domain', $domain)->first();
            
            if (!$tenantDomain) {
                Log::info('Domain not found: ' . $domain);
                \Illuminate\Support\Facades\DB::setDefaultConnection($originalConnection);
                return null;
            }
            
            // Get tenant from MAIN database
            $tenant = $tenantDomain->tenant;
            
            if (!$tenant) {
                Log::error('Tenant not found for domain: ' . $domain);
                \Illuminate\Support\Facades\DB::setDefaultConnection($originalConnection);
                return null;
            }
            
            // Check if tenant is active using RAW data from MAIN database
            $rawTenantData = \Illuminate\Support\Facades\DB::table('tenants')
                ->where('id', $tenant->id)
                ->first();
                
            if (!$rawTenantData) {
                Log::error('Raw tenant data not found for: ' . $tenant->id);
                \Illuminate\Support\Facades\DB::setDefaultConnection($originalConnection);
                return null;
            }
            
            $tenantData = json_decode($rawTenantData->data, true);
            $status = $tenantData['status'] ?? 'inactive';
            
            if ($status !== 'active') {
                Log::info('Tenant inactive for domain: ' . $domain);
                \Illuminate\Support\Facades\DB::setDefaultConnection($originalConnection);
                return null;
            }
            
            // Restore original connection
            \Illuminate\Support\Facades\DB::setDefaultConnection($originalConnection);
            
            Log::info('Tenant resolved successfully from MAIN database', [
                'tenant_id' => $tenant->id,
                'domain' => $domain,
                'status' => $status
            ]);
            
            return $tenant;
            
        } catch (\Exception $e) {
            Log::error('Error resolving tenant for domain: ' . $domain, [
                'error' => $e->getMessage()
            ]);
            // Restore connection on error
            if (isset($originalConnection)) {
                \Illuminate\Support\Facades\DB::setDefaultConnection($originalConnection);
            }
            return null;
        }
    }

    /**
     * Get tenant database config from MAIN database (no tenant DB access)
     */
    protected function getTenantDatabaseConfigFromMain(string $tenantId): ?array
    {
        try {
            // Force main database connection
            $originalConnection = \Illuminate\Support\Facades\DB::getDefaultConnection();
            \Illuminate\Support\Facades\DB::setDefaultConnection('mysql');
            
            // Get raw tenant data from main database
            $rawTenantData = \Illuminate\Support\Facades\DB::table('tenants')
                ->where('id', $tenantId)
                ->first();
            
            // Restore connection
            \Illuminate\Support\Facades\DB::setDefaultConnection($originalConnection);
            
            if (!$rawTenantData) {
                return null;
            }
            
            $tenantData = json_decode($rawTenantData->data, true);
            
            // Return database config
            return $tenantData['database_config'] ?? null;
            
        } catch (\Exception $e) {
            Log::error('Error getting tenant database config from main DB', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Switch to tenant database using config
     */
    protected function switchToTenantDatabase(array $dbConfig): void
    {
        // Configure tenant database connection
        config([
            'database.connections.tenant' => [
                'driver' => 'mysql',
                'host' => $dbConfig['host'],
                'port' => $dbConfig['port'],
                'database' => $dbConfig['database'],
                'username' => $dbConfig['username'],
                'password' => $dbConfig['password'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]
        ]);

        // Set tenant as default connection
        \Illuminate\Support\Facades\DB::setDefaultConnection('tenant');
        
        // Clear any existing connection cache
        \Illuminate\Support\Facades\DB::purge('tenant');
    }

    protected function validateTenantDatabase($tenant): bool
    {
        try {
            $config = $tenant->getDatabaseConfig();
            
            if (empty($config)) {
                return false;
            }

            // Test database connection
            $testConfig = [
                'driver' => 'mysql',
                'host' => $config['host'],
                'port' => $config['port'],
                'database' => $config['database'],
                'username' => $config['username'],
                'password' => $config['password'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ];

            config(['database.connections.tenant_validation' => $testConfig]);
            
            // Try to connect
            \Illuminate\Support\Facades\DB::connection('tenant_validation')->getPdo();
            
            // Clean up
            \Illuminate\Support\Facades\DB::purge('tenant_validation');
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Tenant database validation failed', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}