<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; // Always use central database

    protected $fillable = [
        'name',
        'description',
        'price',
        'billing_cycle',
        'features',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function tenantSubscriptions()
    {
        return $this->hasMany(TenantSubscription::class);
    }

    public function activeTenantSubscriptions()
    {
        return $this->hasMany(TenantSubscription::class)->where('status', 'active');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2);
    }

    public function getBillingCycleTextAttribute()
    {
        return ucfirst($this->billing_cycle);
    }

    public function getPriceWithCycleAttribute()
    {
        return '$' . $this->formatted_price . '/' . $this->billing_cycle;
    }
}