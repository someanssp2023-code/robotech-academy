<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'brand_id', 'name', 'slug', 'sku',
        'description', 'short_description',
        'price', 'sale_price', 'stock',
        'image_url', 'is_active', 'is_featured', 'views',
    ];

    protected $casts = [
        'price'       => 'decimal:2',
        'sale_price'  => 'decimal:2',
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────
    public function category()       { return $this->belongsTo(Category::class); }
    public function brand()          { return $this->belongsTo(Brand::class); }
    public function images()         { return $this->hasMany(ProductImage::class)->orderBy('sort_order'); }
    public function cartItems()      { return $this->hasMany(CartItem::class); }
    public function orderItems()     { return $this->hasMany(OrderItem::class); }
    public function wishlists()      { return $this->hasMany(Wishlist::class); }
    public function stockHistories() { return $this->hasMany(StockHistory::class); }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function allReviews()
    {
        return $this->hasMany(Review::class);
    }

    // ── Accessors ──────────────────────────────────────────
    public function getEffectivePriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return !is_null($this->sale_price) && $this->sale_price < $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if (!$this->is_on_sale) return 0;
        return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock == 0)  return 'Out of Stock';
        if ($this->stock < 10)  return 'Low Stock';
        return 'In Stock';
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->count();
    }
}