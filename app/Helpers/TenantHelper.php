<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantHelper
{
    /**
     * Get current tenant info from request (no database access to tenant tables)
     */
    public static function getCurrentTenantInfo(Request $request = null): ?array
    {
        if (!$request) {
            $request = request();
        }

        $tenantId = $request->attributes->get('tenant_id');
        $tenantDomain = $request->attributes->get('tenant_domain');

        if (!$tenantId || !$tenantDomain) {
            return null;
        }

        return [
            'id' => $tenantId,
            'domain' => $tenantDomain,
            'database' => DB::select('SELECT DATABASE() as db')[0]->db ?? null
        ];
    }

    /**
     * Check if current request is for a tenant (not main site)
     */
    public static function isTenantRequest(Request $request = null): bool
    {
        if (!$request) {
            $request = request();
        }

        return $request->attributes->has('tenant_id');
    }

    /**
     * Get tenant info from MAIN database (use carefully, switches connection)
     */
    public static function getTenantInfoFromMain(string $tenantId): ?array
    {
        try {
            // Force main database connection
            $originalConnection = DB::getDefaultConnection();
            DB::setDefaultConnection('mysql');
            
            // Get raw tenant data from main database
            $rawTenantData = DB::table('tenants')
                ->where('id', $tenantId)
                ->first();
            
            // Restore connection
            DB::setDefaultConnection($originalConnection);
            
            if (!$rawTenantData) {
                return null;
            }
            
            $tenantData = json_decode($rawTenantData->data, true);
            
            return [
                'id' => $tenantId,
                'name' => $tenantData['name'] ?? null,
                'status' => $tenantData['status'] ?? null,
                'db_type' => $tenantData['db_type'] ?? null,
                'database_config' => $tenantData['database_config'] ?? null,
                'created_at' => $rawTenantData->created_at ?? null,
                'updated_at' => $rawTenantData->updated_at ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting tenant info from main DB', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}