<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\DatabaseConfig;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'data',
    ];

    protected $casts = [
        'id' => 'string',
        'data' => 'array',
    ];
    
    // Custom method to save tenant data properly
    public static function createWithData($id, $data)
    {
        $tenant = new static();
        $tenant->id = $id;
        $tenant->save();
        
        // Update data using raw query to bypass model issues
        \Illuminate\Support\Facades\DB::table('tenants')
            ->where('id', $id)
            ->update(['data' => json_encode($data)]);
            
        return $tenant->fresh();
    }

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'database',
            'plan',
            'status',
            'settings',
        ];
    }

    /**
     * Get the database name for this tenant
     */
    public function getDatabaseName(): string
    {
        $config = $this->getSetting('database_config', []);
        return $config['database'] ?? 'tenant_' . $this->id;
    }

    /**
     * Check if tenant is active
     */
    public function isActive(): bool
    {
        return $this->status === true;
    }

    /**
     * Get tenant setting using raw database query
     */
    public function getSetting(string $key, $default = null)
    {
        // Get data directly from database to bypass model issues
        $rawData = \Illuminate\Support\Facades\DB::table('tenants')
            ->where('id', $this->id)
            ->first();
            
        if (!$rawData || !$rawData->data) {
            return $default;
        }
        
        $data = json_decode($rawData->data, true) ?? [];
        return data_get($data, $key, $default);
    }

    /**
     * Set tenant setting using raw database query
     */
    public function setSetting(string $key, $value): void
    {
        try {
            // Get current data from database
            $rawData = \Illuminate\Support\Facades\DB::table('tenants')
                ->where('id', $this->id)
                ->first();
                
            $data = [];
            if ($rawData && $rawData->data) {
                $data = json_decode($rawData->data, true) ?? [];
            }
            
            // Set the new value
            data_set($data, $key, $value);
            
            // Update in database
            \Illuminate\Support\Facades\DB::table('tenants')
                ->where('id', $this->id)
                ->update(['data' => json_encode($data)]);
                
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error setting tenant data', [
                'tenant_id' => $this->id,
                'key' => $key,
                'value' => $value,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get primary domain
     */
    public function getPrimaryDomain()
    {
        return $this->domains()->where('is_primary', true)->first();
    }

    /**
     * Get tenant subscriptions
     */
    public function tenantSubscriptions()
    {
        return $this->hasMany(TenantSubscription::class, 'tenant_id', 'id');
    }

    /**
     * Get current active subscription
     */
    public function currentSubscription()
    {
        return $this->hasOne(TenantSubscription::class, 'tenant_id', 'id')
            ->where('status', 'active')
            ->where('expires_at', '>=', now())
            ->latest();
    }

    /**
     * Get all custom domains
     */
    public function getCustomDomains()
    {
        return $this->domains()->where('is_primary', false)->get();
    }

    /**
     * Get database configuration for HasDatabase trait
     */
    public function database(): DatabaseConfig
    {
        $config = $this->getSetting('database_config', []);
        
        if (empty($config)) {
            // Default configuration if not set
            $config = [
                'database' => 'tenant_' . str_replace('-', '_', $this->id),
                'host' => env('DB_HOST'),
                'port' => env('DB_PORT'),
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
            ];
        }
        
        return new DatabaseConfig($this);
    }
    
    /**
     * Configure tenant database connection manually
     */
    public function configureTenantDatabase(): bool
    {
        $config = $this->getSetting('database_config', []);
        
        if (empty($config)) {
            \Illuminate\Support\Facades\Log::error('No database config found for tenant: ' . $this->id);
            return false;
        }
        
        try {
            // Force disconnect from current connection
            \Illuminate\Support\Facades\DB::disconnect();
            
            // Clear all database connections
            \Illuminate\Support\Facades\DB::purge('mysql');
            \Illuminate\Support\Facades\DB::purge('tenant');
            
            // Set up fresh tenant database connection
            config([
                'database.connections.tenant' => [
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
            
            // Switch default connection FIRST, then test
            config(['database.default' => 'tenant']);
            
            // Force reconnection with new default
            \Illuminate\Support\Facades\DB::reconnect();
            
            // Test the connection to ensure it's working
            $testDb = \Illuminate\Support\Facades\DB::select('SELECT DATABASE() as db')[0]->db;
            
            if ($testDb !== $config['database']) {
                throw new \Exception("Database switch failed. Expected: {$config['database']}, Got: {$testDb}");
            }
            
            \Illuminate\Support\Facades\Log::info('Successfully switched to tenant database', [
                'tenant_id' => $this->id,
                'database' => $config['database'],
                'verified_db' => $testDb
            ]);
            return true;
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to configure tenant database', [
                'tenant_id' => $this->id,
                'database' => $config['database'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get tenant database configuration
     */
    public function getDatabaseConfig(): array
    {
        return $this->getSetting('database_config', []);
    }

    /**
     * Get internal setting (required by TenantWithDatabase interface)
     */
    public function getInternal(string $key)
    {
        return $this->getSetting('internal.' . $key);
    }

    /**
     * Set internal setting
     */
    public function setInternal(string $key, $value): void
    {
        $this->setSetting('internal.' . $key, $value);
    }

}