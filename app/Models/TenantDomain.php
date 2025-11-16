<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Domain as BaseDomain;

class TenantDomain extends BaseDomain
{
    protected $table = 'tenant_domains';
    
    protected $fillable = [
        'domain',
        'tenant_id',
        'type',
        'is_primary',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the tenant that owns this domain
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class, 'tenant_id', 'id');
    }

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'domain',
            'tenant_id',
            'type',
            'is_primary',
        ];
    }

    /**
     * Check if this is a subdomain
     */
    public function isSubdomain(): bool
    {
        return $this->type === 'subdomain';
    }

    /**
     * Check if this is a custom domain
     */
    public function isCustomDomain(): bool
    {
        return $this->type === 'custom';
    }

    /**
     * Get the subdomain part
     */
    public function getSubdomain(): ?string
    {
        if ($this->isSubdomain()) {
            return explode('.', $this->domain)[0];
        }
        return null;
    }

    /**
     * Get the full URL for this domain
     */
    public function getUrl(bool $https = true): string
    {
        $protocol = $https ? 'https://' : 'http://';
        return $protocol . $this->domain;
    }

    /**
     * Get primary domain for a tenant
     */
    public static function getPrimaryDomain(string $tenantId): ?self
    {
        return static::where('tenant_id', $tenantId)
                    ->where('is_primary', true)
                    ->first();
    }

    /**
     * Set as primary domain (only one primary per tenant)
     */
    public function setAsPrimary(): void
    {
        // Remove primary status from other domains of this tenant
        static::where('tenant_id', $this->tenant_id)
              ->where('id', '!=', $this->id)
              ->update(['is_primary' => false]);
        
        // Set this as primary
        $this->update(['is_primary' => true]);
    }
}