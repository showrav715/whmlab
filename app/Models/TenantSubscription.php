<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantSubscription extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; // Always use central database

    protected $fillable = [
        'tenant_id',
        'subscription_plan_id',
        'status',
        'started_at',
        'expires_at',
        'price_paid',
        'plan_features',
        'payment_id',
        'receipt_id'
    ];

    protected $casts = [
        'started_at' => 'date',
        'expires_at' => 'date',
        'plan_features' => 'array',
        'price_paid' => 'decimal:2'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->expires_at >= now();
    }

    public function daysRemaining()
    {
        if ($this->expires_at < now()) {
            return 0;
        }
        return now()->diffInDays($this->expires_at, false);
    }

    public function isExpired()
    {
        return $this->expires_at < now();
    }

    public function isExpiringSoon($days = 7)
    {
        return $this->isActive() && $this->daysRemaining() <= $days;
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->isExpired()) {
            return 'danger';
        }
        
        $classes = [
            'active' => $this->isExpiringSoon() ? 'warning' : 'success',
            'expired' => 'danger',
            'cancelled' => 'secondary',
            'suspended' => 'warning'
        ];

        return $classes[$this->status] ?? 'secondary';
    }

    public function getStatusTextAttribute()
    {
        if ($this->isExpired()) {
            return 'Expired';
        }
        
        if ($this->isExpiringSoon()) {
            return 'Expiring Soon';
        }
        
        return ucfirst($this->status);
    }
}
