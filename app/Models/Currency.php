<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'rate',
        'is_default',
        'status',
        'sort_order'
    ];

    protected $casts = [
        'rate' => 'decimal:8',
        'is_default' => 'boolean',
        'status' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get active currencies
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get default currency
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get currencies ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Set currency as default (ensures only one default exists)
     */
    public function setAsDefault()
    {
        // Remove default from all other currencies
        static::where('is_default', true)->update(['is_default' => false]);
        
        // Set this currency as default
        $this->update(['is_default' => true]);
        
        return $this;
    }

    /**
     * Convert amount from base currency to this currency
     */
    public function convertFromBase($amount)
    {
        return $amount * $this->rate;
    }

    /**
     * Convert amount from this currency to base currency
     */
    public function convertToBase($amount)
    {
        return $amount / $this->rate;
    }

    /**
     * Format amount in this currency
     */
    public function formatAmount($amount, $showCode = true)
    {
        $formatted = number_format($amount, 2, '.', ',');
        
        if ($showCode) {
            return $this->symbol . $formatted . ' ' . $this->code;
        }
        
        return $this->symbol . $formatted;
    }

    /**
     * Get the default currency
     */
    public static function getDefault()
    {
        return static::default()->first() ?? static::first();
    }

    /**
     * Get all active currencies for dropdown
     */
    public static function getForDropdown()
    {
        return static::active()->ordered()->pluck('name', 'code');
    }
}