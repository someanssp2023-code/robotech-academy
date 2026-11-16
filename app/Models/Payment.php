<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'user_id',
        'transaction_id',
        'amount', 'method', 'status', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ────────────────────────────────────────────
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Completed' => 'success',
            'Failed'    => 'danger',
            'Refunded'  => 'warning',
            default     => 'secondary',
        };
    }
}