<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class InitializeTenantAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:initialize-assets {--tenant=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize assets folder for all tenants or a specific tenant';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tenantId = $this->option('tenant');

        if ($tenantId) {
            // Initialize for specific tenant
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                $this->error("Tenant not found: {$tenantId}");
                return 1;
            }
            
            $this->initializeTenant($tenant);
            $this->info("Assets initialized for tenant: {$tenantId}");
        } else {
            // Initialize for all tenants
            $tenants = Tenant::all();
            $count = 0;
            
            foreach ($tenants as $tenant) {
                $this->initializeTenant($tenant);
                $count++;
            }
            
            $this->info("Assets initialized for {$count} tenant(s)");
        }

        return 0;
    }

    /**
     * Initialize assets for a single tenant
     *
     * @param Tenant $tenant
     * @return void
     */
    protected function initializeTenant(Tenant $tenant)
    {
        $basePath = base_path('../assets/images/logo_icon/tenant_' . $tenant->id);
        
        try {
            if (!file_exists($basePath)) {
                mkdir($basePath, 0755, true);
                $this->line("Created folder: {$basePath}");
            }
            
            // Copy default logos if they don't exist
            $defaultLogo = base_path('../assets/images/logo_icon/logo.png');
            $defaultLogoDark = base_path('../assets/images/logo_icon/logo_dark.png');
            $defaultFavicon = base_path('../assets/images/logo_icon/favicon.png');
            
            if (file_exists($defaultLogo) && !file_exists($basePath . '/logo.png')) {
                copy($defaultLogo, $basePath . '/logo.png');
                $this->line("Copied default logo");
            }
            
            if (file_exists($defaultLogoDark) && !file_exists($basePath . '/logo_dark.png')) {
                copy($defaultLogoDark, $basePath . '/logo_dark.png');
                $this->line("Copied default dark logo");
            }
            
            if (file_exists($defaultFavicon) && !file_exists($basePath . '/favicon.png')) {
                copy($defaultFavicon, $basePath . '/favicon.png');
                $this->line("Copied default favicon");
            }
        } catch (\Exception $e) {
            $this->error("Failed to initialize tenant {$tenant->id}: " . $e->getMessage());
        }
    }
}
