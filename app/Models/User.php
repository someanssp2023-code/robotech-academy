<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'phone', 'address', 'avatar', 'is_active', 'is_main_admin',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
        'is_main_admin'     => 'boolean',
    ];

    // ── Roles ──────────────────────────────────────────────
    // Roles that MainAdmin can assign
    public static array $roles = [
        'Customer'  => 'Customer',
        'Staff'     => 'Staff',
        'Admin'     => 'Admin',
    ];

    // All granular roles
    public static array $allRoles = [
        'Customer', 'Staff', 'Admin',
    ];

    public static function roleLabel(string $role): string
    {
        $labels = [
            'Customer' => app()->getLocale()=='km' ? 'អតិថិជន'       : 'Customer',
            'Staff'    => app()->getLocale()=='km' ? 'បុគ្គលិក'       : 'Staff',
            'Admin'    => app()->getLocale()=='km' ? 'អ្នកគ្រប់គ្រង' : 'Admin',
        ];
        return $labels[$role] ?? $role;
    }

    public static function roleColor(string $role): string
    {
        return match($role) {
            'Admin'    => '#f59e0b',
            'Staff'    => '#8b5cf6',
            'Customer' => '#6366f1',
            default    => '#6366f1',
        };
    }

    // ── Relationships ──────────────────────────────────────
    public function orders()         { return $this->hasMany(Order::class); }
    public function cartItems()      { return $this->hasMany(CartItem::class); }
    public function wishlists()      { return $this->hasMany(Wishlist::class); }
    public function reviews()        { return $this->hasMany(Review::class); }
    public function payments()       { return $this->hasMany(Payment::class); }
    public function stockHistories() { return $this->hasMany(StockHistory::class, 'added_by_user_id'); }

    // ── Role Helpers ───────────────────────────────────────
    public function isMainAdmin(): bool { return (bool) $this->is_main_admin; }
    public function isAdmin(): bool     { return $this->role === 'Admin' || $this->isMainAdmin(); }
    public function isStaff(): bool     { return $this->role === 'Staff'; }
    public function isCustomer(): bool  { return $this->role === 'Customer'; }

    // Can access admin panel
    public function canAccessAdmin(): bool
    {
        return in_array($this->role, ['Admin', 'Staff']) || $this->isMainAdmin();
    }

    // Staff can access specific sections
    public function canManageProducts(): bool   { return $this->isAdmin() || $this->isStaff(); }
    public function canManageStock(): bool      { return $this->isAdmin() || $this->isStaff(); }
    public function canManagePromotions(): bool { return $this->isAdmin() || $this->isStaff(); }
    public function canManageOrders(): bool     { return $this->isAdmin(); }
    public function canManageUsers(): bool      { return $this->isAdmin(); }
    public function canManageReports(): bool    { return $this->isAdmin(); }
    public function canManageSettings(): bool   { return $this->isAdmin(); }
    // Only MainAdmin can promote
    public function canPromoteUsers(): bool     { return $this->isMainAdmin(); }

    public function getCartCountAttribute(): int
    {
        return $this->cartItems()->sum('quantity');
    }

    public function getWishlistCountAttribute(): int
    {
        return $this->wishlists()->count();
    }
}