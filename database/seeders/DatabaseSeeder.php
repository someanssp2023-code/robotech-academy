<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ────────────────────────────────────────────
        User::create([
            'name'    => 'Admin Robotech',
            'email'   => 'admin@robotech.com',
            'password'=> Hash::make('admin123'),
            'role'    => 'Admin',
            'phone'   => '012-345-678',
            'address' => 'Robotech Academy, Siem Reap, Cambodia',
        ]);

        User::create([
            'name'    => 'Sok Dara',
            'email'   => 'dara@gmail.com',
            'password'=> Hash::make('password'),
            'role'    => 'Customer',
            'phone'   => '011-222-333',
        ]);

        User::create([
            'name'    => 'Chan Thy',
            'email'   => 'thy@gmail.com',
            'password'=> Hash::make('password'),
            'role'    => 'Customer',
            'phone'   => '011-444-555',
        ]);

        User::create([
            'name'    => 'Pich Sovan',
            'email'   => 'sovan@gmail.com',
            'password'=> Hash::make('password'),
            'role'    => 'Customer',
            'phone'   => '011-666-777',
        ]);

        User::create([
            'name'    => 'Lim Piseth',
            'email'   => 'piseth@gmail.com',
            'password'=> Hash::make('password'),
            'role'    => 'Customer',
            'phone'   => '011-888-999',
        ]);

        // ── Categories ───────────────────────────────────────
        $categoryData = [
            ['name' => 'Smartphones',  'slug' => 'smartphones',  'sort_order' => 1],
            ['name' => 'Laptops',      'slug' => 'laptops',      'sort_order' => 2],
            ['name' => 'Tablets',      'slug' => 'tablets',      'sort_order' => 3],
            ['name' => 'Headphones',   'slug' => 'headphones',   'sort_order' => 4],
            ['name' => 'Cameras',      'slug' => 'cameras',      'sort_order' => 5],
            ['name' => 'Gaming',       'slug' => 'gaming',       'sort_order' => 6],
            ['name' => 'Smart Watch',  'slug' => 'smart-watch',  'sort_order' => 7],
            ['name' => 'Accessories',  'slug' => 'accessories',  'sort_order' => 8],
        ];

        foreach ($categoryData as $cat) {
            Category::create(array_merge($cat, ['is_active' => true]));
        }

        // ── Brands ───────────────────────────────────────────
        $brandNames = ['Apple', 'Samsung', 'Sony', 'Dell', 'Lenovo', 'Canon', 'Microsoft', 'Xiaomi', 'Bose', 'LG'];

        foreach ($brandNames as $name) {
            Brand::create([
                'name'      => $name,
                'slug'      => Str::slug($name),
                'is_active' => true,
            ]);
        }

        // ── Products ─────────────────────────────────────────
        $products = [
            // Smartphones (cat 1)
            ['name' => 'iPhone 15 Pro',           'brand' => 'Apple',     'cat' => 1, 'price' => 999,  'sale' => 949,  'stock' => 50, 'featured' => true],
            ['name' => 'iPhone 14',               'brand' => 'Apple',     'cat' => 1, 'price' => 799,  'sale' => null, 'stock' => 35, 'featured' => false],
            ['name' => 'Samsung Galaxy S24 Ultra','brand' => 'Samsung',   'cat' => 1, 'price' => 1199, 'sale' => 1099, 'stock' => 28, 'featured' => true],
            ['name' => 'Samsung Galaxy A54',      'brand' => 'Samsung',   'cat' => 1, 'price' => 449,  'sale' => null, 'stock' => 45, 'featured' => false],
            ['name' => 'Xiaomi 14 Pro',           'brand' => 'Xiaomi',    'cat' => 1, 'price' => 699,  'sale' => 649,  'stock' => 25, 'featured' => false],
            // Laptops (cat 2)
            ['name' => 'MacBook Pro M3 14"',      'brand' => 'Apple',     'cat' => 2, 'price' => 1999, 'sale' => null, 'stock' => 20, 'featured' => true],
            ['name' => 'MacBook Air M2',          'brand' => 'Apple',     'cat' => 2, 'price' => 1299, 'sale' => 1199, 'stock' => 18, 'featured' => true],
            ['name' => 'Dell XPS 15',             'brand' => 'Dell',      'cat' => 2, 'price' => 1499, 'sale' => null, 'stock' => 15, 'featured' => false],
            ['name' => 'Lenovo ThinkPad X1 Carbon','brand' => 'Lenovo',   'cat' => 2, 'price' => 1399, 'sale' => null, 'stock' => 10, 'featured' => false],
            ['name' => 'Lenovo IdeaPad 5',        'brand' => 'Lenovo',    'cat' => 2, 'price' => 799,  'sale' => 749,  'stock' => 22, 'featured' => false],
            // Tablets (cat 3)
            ['name' => 'iPad Air 5th Gen',        'brand' => 'Apple',     'cat' => 3, 'price' => 599,  'sale' => 549,  'stock' => 40, 'featured' => true],
            ['name' => 'iPad Pro 12.9"',          'brand' => 'Apple',     'cat' => 3, 'price' => 1099, 'sale' => null, 'stock' => 18, 'featured' => false],
            ['name' => 'Samsung Tab S9 Ultra',    'brand' => 'Samsung',   'cat' => 3, 'price' => 1199, 'sale' => 999,  'stock' => 15, 'featured' => true],
            // Headphones (cat 4)
            ['name' => 'Sony WH-1000XM5',         'brand' => 'Sony',      'cat' => 4, 'price' => 349,  'sale' => 299,  'stock' => 60, 'featured' => true],
            ['name' => 'AirPods Pro 2nd Gen',     'brand' => 'Apple',     'cat' => 4, 'price' => 249,  'sale' => null, 'stock' => 55, 'featured' => true],
            ['name' => 'Bose QuietComfort 45',    'brand' => 'Bose',      'cat' => 4, 'price' => 329,  'sale' => 279,  'stock' => 30, 'featured' => false],
            ['name' => 'Samsung Galaxy Buds2 Pro','brand' => 'Samsung',   'cat' => 4, 'price' => 229,  'sale' => null, 'stock' => 40, 'featured' => false],
            // Cameras (cat 5)
            ['name' => 'Canon EOS R50',           'brand' => 'Canon',     'cat' => 5, 'price' => 879,  'sale' => null, 'stock' => 8,  'featured' => false],
            ['name' => 'Sony A7C II',             'brand' => 'Sony',      'cat' => 5, 'price' => 2299, 'sale' => null, 'stock' => 5,  'featured' => false],
            // Gaming (cat 6)
            ['name' => 'Xbox Series X',           'brand' => 'Microsoft', 'cat' => 6, 'price' => 499,  'sale' => null, 'stock' => 7,  'featured' => true],
            ['name' => 'Sony DualSense Controller','brand' => 'Sony',     'cat' => 6, 'price' => 79,   'sale' => 69,   'stock' => 35, 'featured' => false],
            ['name' => 'Samsung Odyssey G7',      'brand' => 'Samsung',   'cat' => 6, 'price' => 599,  'sale' => 499,  'stock' => 12, 'featured' => false],
            // Smart Watch (cat 7)
            ['name' => 'Apple Watch Series 9',    'brand' => 'Apple',     'cat' => 7, 'price' => 399,  'sale' => 369,  'stock' => 35, 'featured' => true],
            ['name' => 'Apple Watch Ultra 2',     'brand' => 'Apple',     'cat' => 7, 'price' => 799,  'sale' => null, 'stock' => 15, 'featured' => false],
            ['name' => 'Samsung Galaxy Watch 6',  'brand' => 'Samsung',   'cat' => 7, 'price' => 299,  'sale' => null, 'stock' => 28, 'featured' => false],
            // Accessories (cat 8)
            ['name' => 'iPhone 15 Silicone Case', 'brand' => 'Apple',     'cat' => 8, 'price' => 49,   'sale' => null, 'stock' => 100,'featured' => false],
            ['name' => 'USB-C Cable 2m',          'brand' => 'Samsung',   'cat' => 8, 'price' => 19,   'sale' => null, 'stock' => 200,'featured' => false],
            ['name' => 'MagSafe Charger',         'brand' => 'Apple',     'cat' => 8, 'price' => 39,   'sale' => 29,   'stock' => 80, 'featured' => false],
        ];

        foreach ($products as $idx => $p) {
            $brand = Brand::where('name', $p['brand'])->first();

            Product::create([
                'category_id'       => $p['cat'],
                'brand_id'          => $brand?->id,
                'name'              => $p['name'],
                'slug'              => Str::slug($p['name']) . '-' . ($idx + 1),
                'sku'               => 'RA-' . strtoupper(Str::random(6)),
                'description'       => "The {$p['name']} by {$p['brand']} is a premium quality device designed for performance and durability. Available exclusively at Robotech Academy Siem Reap.",
                'short_description' => "Premium {$p['name']} from {$p['brand']}.",
                'price'             => $p['price'],
                'sale_price'        => $p['sale'],
                'stock'             => $p['stock'],
                'image_url'         => '/images/placeholder.jpg',
                'is_active'         => true,
                'is_featured'       => $p['featured'],
            ]);
        }

        // ── Coupons ──────────────────────────────────────────
        Coupon::create([
            'code'       => 'ROBOTECH10',
            'type'       => 'percent',
            'value'      => 10,
            'min_order'  => 50,
            'is_active'  => true,
            'expires_at' => '2026-12-31',
        ]);

        Coupon::create([
            'code'       => 'SAVE20',
            'type'       => 'fixed',
            'value'      => 20,
            'min_order'  => 100,
            'is_active'  => true,
            'expires_at' => '2026-12-31',
        ]);

        Coupon::create([
            'code'       => 'SUMMER50',
            'type'       => 'percent',
            'value'      => 50,
            'min_order'  => 200,
            'max_uses'   => 100,
            'is_active'  => true,
            'expires_at' => '2026-06-30',
        ]);

        Coupon::create([
            'code'       => 'NEWUSER',
            'type'       => 'fixed',
            'value'      => 15,
            'min_order'  => 0,
            'max_uses'   => 500,
            'is_active'  => true,
            'expires_at' => '2026-12-31',
        ]);

        // ── Banners ──────────────────────────────────────────
        Banner::create([
            'title'       => 'New iPhone 15 Pro',
            'subtitle'    => 'The most advanced iPhone ever — now available',
            'image_url'   => '/images/banner1.jpg',
            'link_url'    => '/products?category_id=1',
            'button_text' => 'Shop Now',
            'is_active'   => true,
            'sort_order'  => 1,
        ]);

        Banner::create([
            'title'       => 'MacBook Pro M3',
            'subtitle'    => 'Outrageous performance. Battery that lasts all day.',
            'image_url'   => '/images/banner2.jpg',
            'link_url'    => '/products?category_id=2',
            'button_text' => 'Explore Laptops',
            'is_active'   => true,
            'sort_order'  => 2,
        ]);

        Banner::create([
            'title'       => 'Save Up to 50%',
            'subtitle'    => 'Use code ROBOTECH10 for instant discount',
            'image_url'   => '/images/banner3.jpg',
            'link_url'    => '/products?on_sale=1',
            'button_text' => 'Get the Deal',
            'is_active'   => true,
            'sort_order'  => 3,
        ]);
    }
}