<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    protected $fillable = [
    'product_id',
    'quantity_added',
    'stock_before',
    'stock_after',
    'adj_type',      // ← must be here
    'reason',        // ← must be here
    'note',
    'added_by_user_id',
];

    // ── Relationships ──────────────────────────────────────
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }
}