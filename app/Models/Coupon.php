<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_order',
        'max_uses', 'used_count', 'is_active', 'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'date',
        'is_active'  => 'boolean',
        'value'      => 'decimal:2',
        'min_order'  => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // ── Helpers ────────────────────────────────────────────
    public function isValid(float $orderTotal = 0): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        if ($orderTotal < $this->min_order) return false;
        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percent') {
            return round($subtotal * ($this->value / 100), 2);
        }
        return min((float)$this->value, $subtotal);
    }
}