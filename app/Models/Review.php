<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'rating', 'comment', 'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ── Helpers ────────────────────────────────────────────
    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
}