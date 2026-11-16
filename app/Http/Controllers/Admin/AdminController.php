<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\StockHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // ════════════════════════════════════════════════════════
    //  DASHBOARD
    // ════════════════════════════════════════════════════════

    public function dashboard()
    {
        $totalCategories = Category::count();
        $totalBrands     = Brand::count();
        $totalProducts   = Product::where('is_active', true)->count();
        $totalRevenue    = Order::whereNotIn('status', ['Cancelled'])->sum('total_amount');
        $totalOrders     = Order::count();
        $totalUsers      = User::where('role', 'Customer')->count();
        $lowStock        = Product::where('stock', '<', 10)->where('is_active', true)->count();
        $pendingOrders   = Order::where('status', 'Pending')->count();
        $pendingReviews  = Review::where('is_approved', false)->count();

        $recentOrders = Order::with('user')->latest()->take(10)->get();

        // Revenue cards
        $revenueToday = Order::whereDate('created_at', today())->whereNotIn('status', ['Cancelled'])->sum('total_amount');
        $revenueWeek  = Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->whereNotIn('status', ['Cancelled'])->sum('total_amount');
        $revenueMonth = Order::whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->whereNotIn('status', ['Cancelled'])->sum('total_amount');
        $ordersWeek   = Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        // Revenue last month for comparison
        $revenueLastMonth = Order::whereYear('created_at', now()->subMonth()->year)->whereMonth('created_at', now()->subMonth()->month)->whereNotIn('status', ['Cancelled'])->sum('total_amount');
        $revenueGrowth    = $revenueLastMonth > 0 ? round((($revenueMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1) : 0;

        // Chart data
        $chartLabels  = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $chartRevenue = [];
        $chartOrders  = [];
        for ($m = 1; $m <= 12; $m++) {
            $mq = Order::whereYear('created_at', now()->year)->whereMonth('created_at', $m)->whereNotIn('status', ['Cancelled']);
            $chartRevenue[] = (float) $mq->sum('total_amount');
            $chartOrders[]  = $mq->count();
        }

        // Status counts for donut chart
        $statusCounts = [
            'Pending'    => Order::where('status', 'Pending')->count(),
            'Processing' => Order::where('status', 'Processing')->count(),
            'Shipped'    => Order::where('status', 'Shipped')->count(),
            'Delivered'  => Order::where('status', 'Delivered')->count(),
            'Cancelled'  => Order::where('status', 'Cancelled')->count(),
        ];

        // Top 5 best selling products
        $topProducts = OrderItem::select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $maxSold = $topProducts->max('total_sold') ?: 1;

        return view('admin.dashboard', compact(
            'totalCategories','totalBrands','totalProducts','totalRevenue',
            'totalOrders','totalUsers','lowStock','pendingOrders','pendingReviews',
            'recentOrders','revenueToday','revenueWeek','revenueMonth',
            'ordersWeek','revenueLastMonth','revenueGrowth',
            'chartLabels','chartRevenue','chartOrders',
            'statusCounts','topProducts','maxSold'
        ));
    }

    // ════════════════════════════════════════════════════════
    //  PRODUCTS
    // ════════════════════════════════════════════════════════

    public function products(Request $request)
    {
        $query = Product::with(['category', 'brand']);

        // Search by name or SKU
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->filled('brand_id'))    $query->where('brand_id', $request->brand_id);

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Stock filter
        if ($request->filled('stock')) {
            match ($request->stock) {
                'out'  => $query->where('stock', 0),
                'low'  => $query->where('stock', '>', 0)->where('stock', '<', 10),
                'ok'   => $query->where('stock', '>=', 10),
                default => null,
            };
        }

        // Price range
        if ($request->filled('min_price')) $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price')) $query->where('price', '<=', $request->max_price);

        // Date range (created_at)
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);

        // Sort
        match ($request->sort) {
            'name_asc'   => $query->orderBy('name', 'asc'),
            'name_desc'  => $query->orderBy('name', 'desc'),
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'stock_asc'  => $query->orderBy('stock', 'asc'),
            'stock_desc' => $query->orderBy('stock', 'desc'),
            default      => $query->latest(),
        };

        return view('admin.products', [
            'products'   => $query->paginate(15)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
            'brands'     => Brand::orderBy('name')->get(),
        ]);
    }

    public function createProduct()
    {
        return view('admin.product-form', [
            'product'    => null,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'brands'     => Brand::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:200',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $imageUrl = '/images/placeholder.jpg';
        if ($request->hasFile('image')) {
            $path     = $request->file('image')->store('products', 'public');
            $imageUrl = '/storage/'.$path;
        }

        Product::create([
            'category_id'       => $request->category_id,
            'brand_id'          => $request->brand_id,
            'name'              => $request->name,
            'slug'              => Str::slug($request->name).'-'.uniqid(),
            'sku'               => 'RA-'.strtoupper(Str::random(6)),
            'description'       => $request->description,
            'short_description' => $request->short_description,
            'price'             => $request->price,
            'sale_price'        => $request->sale_price ?: null,
            'stock'             => $request->stock,
            'image_url'         => $imageUrl,
            'is_active'         => true,
            'is_featured'       => $request->boolean('is_featured'),
        ]);

        return redirect()->route('admin.products')
            ->with('success', app()->getLocale()=='km' ? 'បានបន្ថែមផលិតផល!' : 'Product created!');
    }

    public function editProduct(int $id)
    {
        return view('admin.product-form', [
            'product'    => Product::findOrFail($id),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'brands'     => Brand::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function updateProduct(Request $request, int $id)
    {
        $request->validate([
            'name'        => 'required|string|max:200',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product  = Product::findOrFail($id);
        $imageUrl = $product->image_url;
        if ($request->hasFile('image')) {
            $path     = $request->file('image')->store('products', 'public');
            $imageUrl = '/storage/'.$path;
        }

        $product->update([
            'category_id'       => $request->category_id,
            'brand_id'          => $request->brand_id,
            'name'              => $request->name,
            'description'       => $request->description,
            'short_description' => $request->short_description,
            'price'             => $request->price,
            'sale_price'        => $request->sale_price ?: null,
            'stock'             => $request->stock,
            'image_url'         => $imageUrl,
            'is_featured'       => $request->boolean('is_featured'),
            'is_active'         => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.products')
            ->with('success', app()->getLocale()=='km' ? 'បានធ្វើបច្ចុប្បន្នភាព!' : 'Product updated!');
    }

    public function deleteProduct(int $id)
    {
        $product = Product::findOrFail($id);
        $product->cartItems()->delete();
        $product->wishlists()->delete();
        $product->allReviews()->delete();
        $product->images()->delete();
        $product->delete();

        return redirect()->route('admin.products')
            ->with('success', app()->getLocale()=='km' ? 'បានលប់ចោល!' : 'Product deleted!');
    }

    public function bulkDeleteProducts(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return redirect()->route('admin.products')
                ->with('error', app()->getLocale()=='km' ? 'មិនបានជ្រើសផលិតផល!' : 'No products selected!');
        }

        $deleted = 0;
        foreach (Product::whereIn('id', $ids)->get() as $product) {
            $product->cartItems()->delete();
            $product->wishlists()->delete();
            $product->allReviews()->delete();
            $product->images()->delete();
            $product->delete();
            $deleted++;
        }

        return redirect()->route('admin.products')
            ->with('success', app()->getLocale()=='km'
                ? "បានលប់ {$deleted} ផលិតផល!"
                : "{$deleted} product(s) deleted!");
    }

    // ── Export Excel (XML SpreadsheetML — no package needed) ─────────────────
    public function exportProductsExcel(Request $request)
    {
        $products = $this->getFilteredProducts($request);
        $filename = 'products_' . now()->format('Y-m-d_H-i-s') . '.xls';

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                   xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
                 <Worksheet ss:Name="Products"><Table>';

        $xml .= '<Row>
            <Cell><Data ss:Type="String">No.</Data></Cell>
            <Cell><Data ss:Type="String">Name</Data></Cell>
            <Cell><Data ss:Type="String">SKU</Data></Cell>
            <Cell><Data ss:Type="String">Category</Data></Cell>
            <Cell><Data ss:Type="String">Brand</Data></Cell>
            <Cell><Data ss:Type="String">Price ($)</Data></Cell>
            <Cell><Data ss:Type="String">Sale Price ($)</Data></Cell>
            <Cell><Data ss:Type="String">Stock</Data></Cell>
            <Cell><Data ss:Type="String">Stock Status</Data></Cell>
            <Cell><Data ss:Type="String">Status</Data></Cell>
        </Row>';

        foreach ($products as $i => $p) {
            $stockLabel = $p->stock == 0 ? 'Out of Stock' : ($p->stock < 10 ? 'Low Stock' : 'In Stock');
            $xml .= '<Row>
                <Cell><Data ss:Type="Number">' . ($i + 1) . '</Data></Cell>
                <Cell><Data ss:Type="String">' . htmlspecialchars($p->name, ENT_XML1) . '</Data></Cell>
                <Cell><Data ss:Type="String">' . htmlspecialchars($p->sku ?? '', ENT_XML1) . '</Data></Cell>
                <Cell><Data ss:Type="String">' . htmlspecialchars($p->category->name ?? '', ENT_XML1) . '</Data></Cell>
                <Cell><Data ss:Type="String">' . htmlspecialchars($p->brand->name ?? '', ENT_XML1) . '</Data></Cell>
                <Cell><Data ss:Type="Number">' . $p->price . '</Data></Cell>
                <Cell><Data ss:Type="String">' . ($p->sale_price ? number_format($p->sale_price, 2) : '') . '</Data></Cell>
                <Cell><Data ss:Type="Number">' . $p->stock . '</Data></Cell>
                <Cell><Data ss:Type="String">' . $stockLabel . '</Data></Cell>
                <Cell><Data ss:Type="String">' . ($p->is_active ? 'Active' : 'Inactive') . '</Data></Cell>
            </Row>';
        }

        $xml .= '</Table></Worksheet></Workbook>';

        return response($xml, 200, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    // ── Export PDF (HTML print page — opens in new tab) ───────────────────────
    public function exportProductsPdf(Request $request)
    {
        $products = $this->getFilteredProducts($request);

        // Build filter chips
        $activeFilters = [];
        if ($request->filled('search'))      $activeFilters[] = '🔍 "' . $request->search . '"';
        if ($request->filled('category_id')) {
            $cat = Category::find($request->category_id);
            if ($cat) $activeFilters[] = '📂 ' . $cat->name;
        }
        if ($request->filled('brand_id')) {
            $br = Brand::find($request->brand_id);
            if ($br) $activeFilters[] = '🏷 ' . $br->name;
        }
        if ($request->filled('status'))    $activeFilters[] = '● ' . ucfirst($request->status);
        if ($request->filled('stock'))     $activeFilters[] = '📦 Stock: ' . $request->stock;
        if ($request->filled('min_price') || $request->filled('max_price'))
            $activeFilters[] = '💵 $' . ($request->min_price ?? '0') . ' – $' . ($request->max_price ?? '∞');
        if ($request->filled('date_from') || $request->filled('date_to'))
            $activeFilters[] = '📅 ' . ($request->date_from ?? '…') . ' → ' . ($request->date_to ?? '…');

        $stats = [
            ['label' => 'Total',        'value' => $products->count(),                                     'color' => 'blue'],
            ['label' => 'Active',       'value' => $products->where('is_active', true)->count(),           'color' => 'green'],
            ['label' => 'Avg Price',    'value' => '$' . number_format($products->avg('price'), 2),        'color' => 'amber'],
            ['label' => 'Total Stock',  'value' => number_format($products->sum('stock')),                 'color' => 'indigo'],
            ['label' => 'Out of Stock', 'value' => $products->where('stock', 0)->count(),                  'color' => 'red'],
            ['label' => 'On Sale',      'value' => $products->filter(fn($p) => $p->sale_price)->count(),   'color' => 'orange'],
        ];

        $columns = [
            ['label' => 'Product',      'render' => fn($p) => '<div class="cell-bold">' . e(Str::limit($p->name, 40)) . ($p->sale_price ? ' <span class="pill pill-sale">SALE</span>' : '') . '</div><div class="cell-sub">' . e($p->sku ?? '') . '</div>'],
            ['label' => 'Category',     'render' => fn($p) => '<span class="tag">' . e($p->category->name ?? '—') . '</span>'],
            ['label' => 'Brand',        'render' => fn($p) => '<span style="font-size:11px;color:#64748b;">' . e($p->brand->name ?? '—') . '</span>'],
            ['label' => 'Price',        'render' => fn($p) => $p->sale_price
                ? '<div class="price-orig">$' . number_format($p->price, 2) . '</div><div class="price-sale">$' . number_format($p->sale_price, 2) . '</div>'
                : '<div class="price-main">$' . number_format($p->price, 2) . '</div>'],
            ['label' => 'Stock',        'render' => fn($p) => '<span style="font-weight:800;font-size:13px;">' . $p->stock . '</span>'],
            ['label' => 'Stock Status', 'render' => fn($p) => $p->stock == 0
                ? '<span class="pill pill-out">Out</span>'
                : ($p->stock < 10 ? '<span class="pill pill-low">Low</span>' : '<span class="pill pill-ok">OK</span>')],
            ['label' => 'Status',       'render' => fn($p) => $p->is_active
                ? '<span class="pill pill-active">Active</span>'
                : '<span class="pill pill-inactive">Inactive</span>'],
        ];

        return view('admin.export-pdf', [
            'title'         => 'Products Report',
            'icon'          => '📦',
            'rowLabel'      => 'products',
            'rows'          => $products,
            'stats'         => $stats,
            'columns'       => $columns,
            'activeFilters' => $activeFilters,
        ]);
    }

    // ── Shared filter helper (no pagination) ─────────────────────────────────
    private function getFilteredProducts(Request $request)
    {
        $query = Product::with(['category', 'brand']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%");
            });
        }
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->filled('brand_id'))    $query->where('brand_id', $request->brand_id);
        if ($request->filled('status'))      $query->where('is_active', $request->status === 'active');
        if ($request->filled('stock')) {
            match ($request->stock) {
                'out'  => $query->where('stock', 0),
                'low'  => $query->where('stock', '>', 0)->where('stock', '<', 10),
                'ok'   => $query->where('stock', '>=', 10),
                default => null,
            };
        }
        if ($request->filled('min_price')) $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price')) $query->where('price', '<=', $request->max_price);

        // Date range (created_at)
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);

        return $query->orderBy('name')->get();
    }

    // ════════════════════════════════════════════════════════
    //  CATEGORIES
    // ════════════════════════════════════════════════════════

    public function categories()
    {
        return view('admin.categories', [
            'categories' => Category::withCount('products')->orderBy('sort_order')->get(),
        ]);
    }

    public function createCategory()
    {
        return view('admin.category-form', ['category' => null]);
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        Category::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'is_active'   => true,
            'sort_order'  => $request->sort_order ?? 0,
        ]);
        return redirect()->route('admin.categories')
            ->with('success', app()->getLocale()=='km' ? 'បានបន្ថែមប្រភេទ!' : 'Category created!');
    }

    public function editCategory(int $id)
    {
        return view('admin.category-form', ['category' => Category::findOrFail($id)]);
    }

    public function updateCategory(Request $request, int $id)
    {
        $request->validate(['name' => 'required|string|max:100']);
        Category::findOrFail($id)->update([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true),
            'sort_order'  => $request->sort_order ?? 0,
        ]);
        return redirect()->route('admin.categories')
            ->with('success', app()->getLocale()=='km' ? 'បានធ្វើបច្ចុប្បន្នភាព!' : 'Category updated!');
    }

    public function deleteCategory(int $id)
    {
        $category = Category::findOrFail($id);
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories')
                ->with('error', app()->getLocale()=='km' ? 'មិនអាចលប់ — មានផលិតផលនៅក្នុងប្រភេទនេះ!' : 'Cannot delete — category has products!');
        }
        $category->delete();
        return redirect()->route('admin.categories')
            ->with('success', app()->getLocale()=='km' ? 'បានលប់ចោល!' : 'Category deleted!');
    }

    public function bulkDeleteCategories(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->route('admin.categories')
                ->with('error', app()->getLocale()=='km' ? 'មិនបានជ្រើសប្រភេទ!' : 'No categories selected!');
        }

        $skipped = 0;
        foreach ($ids as $id) {
            $category = Category::find($id);
            if (!$category) continue;
            if ($category->products()->count() > 0) { $skipped++; continue; }
            $category->delete();
        }

        $deleted = count($ids) - $skipped;
        $msg = $deleted . ' ' . (app()->getLocale()=='km' ? 'ប្រភេទបានលប់ចោល' : 'categor' . ($deleted === 1 ? 'y' : 'ies') . ' deleted');
        if ($skipped > 0) $msg .= ' (' . $skipped . ' skipped — has products)';

        return redirect()->route('admin.categories')->with('success', $msg);
    }

    // ── Export Excel (XML SpreadsheetML) ─────────────────────────────────────
    public function exportCategoriesExcel(Request $request)
    {
        $categories = $this->getFilteredCategories($request);
        $filename   = 'categories_' . now()->format('Y-m-d_H-i-s') . '.xls';

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                   xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
                 <Worksheet ss:Name="Categories"><Table>';

        $xml .= '<Row>
            <Cell><Data ss:Type="String">No.</Data></Cell>
            <Cell><Data ss:Type="String">ID</Data></Cell>
            <Cell><Data ss:Type="String">Name</Data></Cell>
            <Cell><Data ss:Type="String">Products</Data></Cell>
            <Cell><Data ss:Type="String">Status</Data></Cell>
        </Row>';

        foreach ($categories as $i => $cat) {
            $xml .= '<Row>
                <Cell><Data ss:Type="Number">' . ($i + 1) . '</Data></Cell>
                <Cell><Data ss:Type="Number">' . $cat->id . '</Data></Cell>
                <Cell><Data ss:Type="String">' . htmlspecialchars($cat->name, ENT_XML1) . '</Data></Cell>
                <Cell><Data ss:Type="Number">' . ($cat->products_count ?? 0) . '</Data></Cell>
                <Cell><Data ss:Type="String">' . ($cat->is_active ? 'Active' : 'Hidden') . '</Data></Cell>
            </Row>';
        }

        $xml .= '</Table></Worksheet></Workbook>';

        return response($xml, 200, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    // ── Export PDF (HTML print page) ─────────────────────────────────────────
    public function exportCategoriesPdf(Request $request)
    {
        $categories    = $this->getFilteredCategories($request);
        $activeFilters = [];
        if ($request->filled('search')) $activeFilters[] = '🔍 "' . $request->search . '"';

        $stats = [
            ['label' => 'Total',          'value' => $categories->count(),                               'color' => 'blue'],
            ['label' => 'Active',         'value' => $categories->where('is_active', true)->count(),     'color' => 'green'],
            ['label' => 'Hidden',         'value' => $categories->where('is_active', false)->count(),    'color' => 'red'],
            ['label' => 'Total Products', 'value' => number_format($categories->sum('products_count')),  'color' => 'amber'],
        ];

        $columns = [
            ['label' => 'Name',     'render' => fn($c) => '<span class="cell-bold">' . e($c->name) . '</span>'],
            ['label' => 'Products', 'render' => fn($c) => '<span style="font-weight:800;font-size:13px;color:#3b82f6;">' . ($c->products_count ?? 0) . '</span> <span style="font-size:10px;color:#94a3b8;">products</span>'],
            ['label' => 'Status',   'render' => fn($c) => $c->is_active
                ? '<span class="pill pill-active">Active</span>'
                : '<span class="pill pill-inactive">Hidden</span>'],
        ];

        return view('admin.export-pdf', [
            'title'         => 'Categories Report',
            'icon'          => '📂',
            'rowLabel'      => 'categories',
            'rows'          => $categories,
            'stats'         => $stats,
            'columns'       => $columns,
            'activeFilters' => $activeFilters,
        ]);
    }

    // ── Shared filter helper ──────────────────────────────────────────────────
    private function getFilteredCategories(Request $request)
    {
        $query = Category::withCount('products');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return $query->orderBy('sort_order')->orderBy('name')->get();
    }

    // ════════════════════════════════════════════════════════
    //  BRANDS
    // ════════════════════════════════════════════════════════

    public function brands(Request $request)
    {
        return view('admin.brands', [
            'brands' => $this->getFilteredBrands($request),
        ]);
    }

    public function createBrand()
    {
        return view('admin.brand-form', ['brand' => null]);
    }

    public function storeBrand(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $logoUrl = null;
        if ($request->hasFile('logo')) {
            $path    = $request->file('logo')->store('brands', 'public');
            $logoUrl = '/storage/'.$path;
        }
        Brand::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'logo_url'    => $logoUrl,
            'is_active'   => true,
        ]);
        return redirect()->route('admin.brands')
            ->with('success', app()->getLocale()=='km' ? 'បានបន្ថែមម៉ាក!' : 'Brand created!');
    }

    public function editBrand(int $id)
    {
        return view('admin.brand-form', ['brand' => Brand::findOrFail($id)]);
    }

    public function updateBrand(Request $request, int $id)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $brand   = Brand::findOrFail($id);
        $logoUrl = $brand->logo_url;
        if ($request->hasFile('logo')) {
            $path    = $request->file('logo')->store('brands', 'public');
            $logoUrl = '/storage/'.$path;
        }
        $brand->update([
            'name'        => $request->name,
            'description' => $request->description,
            'logo_url'    => $logoUrl,
            'is_active'   => $request->boolean('is_active', true),
        ]);
        return redirect()->route('admin.brands')
            ->with('success', app()->getLocale()=='km' ? 'បានធ្វើបច្ចុប្បន្នភាព!' : 'Brand updated!');
    }

    public function deleteBrand(int $id)
    {
        Brand::findOrFail($id)->delete();
        return redirect()->route('admin.brands')
            ->with('success', app()->getLocale()=='km' ? 'បានលប់ចោល!' : 'Brand deleted!');
    }

    public function bulkDeleteBrands(Request $request)
    {
        $ids     = array_filter((array) $request->input('ids', []));
        $deleted = 0;
        $skipped = 0;
        foreach ($ids as $id) {
            $brand = Brand::find($id);
            if (!$brand) continue;
            if ($brand->products()->count() > 0) { $skipped++; continue; }
            $brand->delete();
            $deleted++;
        }
        $msg = app()->getLocale()=='km'
            ? "បានលុប {$deleted} ម៉ាក" . ($skipped ? " (រំលង {$skipped} ដែលមានផលិតផល)" : '')
            : "Deleted {$deleted} brand(s)" . ($skipped ? " ({$skipped} skipped — has products)" : '');
        return redirect()->route('admin.brands')->with('success', $msg);
    }

    public function exportBrandsExcel(Request $request)
    {
        $brands = $this->getFilteredBrands($request);

        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" ";
        $xml .= "xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\">\n";
        $xml .= "<Worksheet ss:Name=\"Brands\"><Table>\n";

        // Header row
        $xml .= "<Row>";
        foreach (['No.', 'ID', 'Name', 'Products', 'Status'] as $h) {
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($h) . "</Data></Cell>";
        }
        $xml .= "</Row>\n";

        foreach ($brands as $i => $brand) {
            $xml .= "<Row>";
            $xml .= "<Cell><Data ss:Type=\"Number\">" . ($i + 1) . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"Number\">{$brand->id}</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($brand->name) . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"Number\">{$brand->products_count}</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . ($brand->is_active ? 'Active' : 'Hidden') . "</Data></Cell>";
            $xml .= "</Row>\n";
        }

        $xml .= "</Table></Worksheet></Workbook>";

        $filename = 'brands_' . now()->format('Ymd_His') . '.xls';
        return response($xml, 200, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function exportBrandsPdf(Request $request)
    {
        $brands = $this->getFilteredBrands($request);

        $stats = [
            ['label' => 'Total',    'value' => $brands->count(),                              'color' => 'blue'],
            ['label' => 'Active',   'value' => $brands->where('is_active', true)->count(),    'color' => 'green'],
            ['label' => 'Hidden',   'value' => $brands->where('is_active', false)->count(),   'color' => 'red'],
            ['label' => 'Products', 'value' => number_format($brands->sum('products_count')), 'color' => 'amber'],
        ];

        $activeFilters = [];
        if ($request->filled('search')) $activeFilters[] = '🔍 "' . $request->search . '"';
        if ($request->filled('status')) $activeFilters[] = 'Status: ' . ucfirst($request->status);

        $columns = [
            ['label' => 'Name',     'render' => fn($b) => '<span class="cell-bold">' . e($b->name) . '</span>'],
            ['label' => 'Products', 'render' => fn($b) => '<span class="cell-bold" style="color:#3b82f6;">' . $b->products_count . '</span> <span style="font-size:10px;color:#94a3b8;">products</span>'],
            ['label' => 'Status',   'render' => fn($b) => $b->is_active
                ? '<span class="pill pill-active">Active</span>'
                : '<span class="pill pill-inactive">Hidden</span>'],
        ];

        return view('admin.export-pdf', [
            'title'         => 'Brands Report',
            'icon'          => '🏷',
            'rowLabel'      => 'brands',
            'rows'          => $brands,
            'stats'         => $stats,
            'columns'       => $columns,
            'activeFilters' => $activeFilters,
        ]);
    }

    private function getFilteredBrands(Request $request)
    {
        $query = Brand::withCount('products')->orderBy('name');
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        return $query->get();
    }

    // ════════════════════════════════════════════════════════
    //  ORDERS
    // ════════════════════════════════════════════════════════

    public function orders(Request $request)
    {
        $orders = $this->getFilteredOrders($request)->paginate(20)->withQueryString();
        return view('admin.orders', compact('orders'));
    }

    public function exportOrdersExcel(Request $request)
    {
        $orders   = $this->getFilteredOrders($request)->with('orderItems')->get();
        $fromDate = $request->filled('date_from') ? $request->date_from : null;
        $toDate   = $request->filled('date_to')   ? $request->date_to   : null;
        $period   = ($fromDate || $toDate)
            ? (($fromDate ? date('d M Y', strtotime($fromDate)) : '…') . ' → ' . ($toDate ? date('d M Y', strtotime($toDate)) : '…'))
            : date('d M Y');

        $filename = 'Orders-' . $period . '.xls';

        $html = '
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head><meta charset="UTF-8">
<style>
body { font-family:Arial,sans-serif;font-size:11pt; }
h1   { font-size:15pt;font-weight:bold;color:#111;margin:0 0 4px; }
p    { font-size:10pt;color:#6b7280;margin:2px 0; }
table { border-collapse:collapse;width:100%; }
th { background:#111;color:#fff;font-size:10pt;font-weight:bold;padding:7px 12px;border:1px solid #374151; }
td { padding:7px 12px;border:1px solid #e5e7eb;font-size:10pt;vertical-align:middle; }
tr:nth-child(even) td { background:#f9fafb; }
.gold  { color:#b45309;font-weight:bold; }
.center{ text-align:center; }
.right { text-align:right; }
.badge-pending    { background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:4px; }
.badge-delivered  { background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:4px; }
.badge-cancelled  { background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:4px; }
.badge-shipped    { background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:4px; }
.badge-processing { background:#e0e7ff;color:#3730a3;padding:2px 8px;border-radius:4px; }
.badge-paid       { background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:4px; }
.badge-unpaid     { background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:4px; }
</style></head>
<body>
<h1>🤖 ROBOTECH Academy</h1>
<p>Orders: <strong>' . $period . '</strong></p>
<p>Generated: <strong>' . now()->format('M d, Y — h:i A') . '</strong> &nbsp;|&nbsp; Total: <strong>' . $orders->count() . '</strong> orders &nbsp;|&nbsp; Revenue: <strong>$' . number_format($orders->sum('total_amount'), 2) . '</strong></p>
<br>
<table>
<thead><tr>
<th class="center">#</th>
<th>Order No.</th>
<th>Customer</th>
<th>Email</th>
<th>Date</th>
<th class="center">Items</th>
<th class="right">Total</th>
<th class="center">Payment Method</th>
<th class="center">Payment Status</th>
<th class="center">Status</th>
</tr></thead>
<tbody>';

        $statusBadge = [
            'Pending'=>'badge-pending','Processing'=>'badge-processing',
            'Shipped'=>'badge-shipped','Delivered'=>'badge-delivered','Cancelled'=>'badge-cancelled',
        ];
        foreach ($orders as $i => $order) {
            $sb    = $statusBadge[$order->status] ?? 'badge-pending';
            $pb    = $order->payment_status === 'Paid' ? 'badge-paid' : 'badge-unpaid';
            $html .= '<tr>
                <td class="center">' . ($i + 1) . '</td>
                <td><strong>RA-' . $order->id . '</strong></td>
                <td>' . htmlspecialchars($order->user->name ?? '—') . '</td>
                <td>' . htmlspecialchars($order->user->email ?? '—') . '</td>
                <td>' . $order->created_at->format('M d, Y') . '</td>
                <td class="center">' . $order->orderItems->count() . '</td>
                <td class="right gold">$' . number_format($order->total_amount, 2) . '</td>
                <td class="center">' . $order->payment_method . '</td>
                <td class="center"><span class="' . $pb . '">' . $order->payment_status . '</span></td>
                <td class="center"><span class="' . $sb . '">' . $order->status . '</span></td>
            </tr>';
        }

        $html .= '</tbody></table>
<p style="margin-top:20px;font-size:9pt;color:#9ca3af;border-top:1px solid #e5e7eb;padding-top:8px;">
    © ' . date('Y') . ' ROBOTECH Academy — Report generated automatically by system
</p></body></html>';

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function exportOrdersPdf(Request $request)
    {
        $orders   = $this->getFilteredOrders($request)->with('orderItems')->get();
        $fromDate = $request->filled('date_from') ? $request->date_from : null;
        $toDate   = $request->filled('date_to')   ? $request->date_to   : null;
        $period   = ($fromDate || $toDate)
            ? (($fromDate ? date('d M Y', strtotime($fromDate)) : '…') . ' → ' . ($toDate ? date('d M Y', strtotime($toDate)) : '…'))
            : date('d M Y');

        $totalRevenue   = $orders->sum('total_amount');
        $totalOrders    = $orders->count();
        $totalDelivered = $orders->where('status', 'Delivered')->count();
        $totalPaid      = $orders->where('payment_status', 'Paid')->count();

        $stats = [
            ['label' => 'Total Orders',  'value' => number_format($totalOrders),           'color' => 'indigo'],
            ['label' => 'Total Revenue', 'value' => '$' . number_format($totalRevenue, 2), 'color' => 'amber'],
            ['label' => 'Delivered',     'value' => number_format($totalDelivered),          'color' => 'green'],
            ['label' => 'Paid',          'value' => number_format($totalPaid),               'color' => 'blue'],
        ];

        $columns = [
            ['label' => 'Order',    'render' => fn($o) => '<span class="cell-bold">RA-' . $o->id . '</span>'],
            ['label' => 'Customer', 'render' => fn($o) => '<span class="cell-bold">' . e(\Illuminate\Support\Str::limit($o->user->name ?? '—', 18)) . '</span><br><span style="font-size:11px;color:#94a3b8;">' . e($o->user->email ?? '') . '</span>'],
            ['label' => 'Date',     'render' => fn($o) => '<span style="font-size:12px;color:#6b7280;">' . $o->created_at->format('d M Y') . '</span>'],
            ['label' => 'Items',    'render' => fn($o) => $o->orderItems->count()],
            ['label' => 'Total',    'render' => fn($o) => '<span style="font-weight:700;color:#f59e0b;">$' . number_format($o->total_amount, 2) . '</span>'],
            ['label' => 'Payment',  'render' => fn($o) => $o->payment_method . '<br><span style="font-size:11px;color:' . ($o->payment_status === 'Paid' ? '#16a34a' : '#d97706') . ';">' . $o->payment_status . '</span>'],
            ['label' => 'Status',   'render' => fn($o) => match($o->status) {
                'Delivered'  => '<span class="pill pill-delivered">Delivered</span>',
                'Cancelled'  => '<span class="pill pill-cancelled">Cancelled</span>',
                'Shipped'    => '<span class="pill pill-shipped">Shipped</span>',
                'Processing' => '<span class="pill pill-processing">Processing</span>',
                default      => '<span class="pill pill-pending">Pending</span>',
            }],
        ];

        return view('admin.export-pdf', [
            'title'         => 'Orders — ' . $period,
            'icon'          => '🛒',
            'rowLabel'      => 'orders',
            'rows'          => $orders,
            'stats'         => $stats,
            'columns'       => $columns,
            'activeFilters' => ['📅 ' . $period],
        ]);
    }

    private function getFilteredOrders(Request $request)
    {
        $query = Order::with(['user', 'orderItems']);
        if ($request->filled('status'))         $query->where('status', $request->status);
        if ($request->filled('payment_status')) $query->where('payment_status', $request->payment_status);
        if ($request->filled('search'))         $query->where('id', 'like', '%' . $request->search . '%');
        if ($request->filled('date_from'))      $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))        $query->whereDate('created_at', '<=', $request->date_to);
        return $query->latest();
    }

    public function orderDetail(int $id)
    {
        $order = Order::with(['user', 'orderItems.product', 'coupon', 'payment'])->findOrFail($id);
        return view('admin.order-detail', compact('order'));
    }

    // ── Invoice (printable) ───────────────────────────────────────────────────
    public function invoice(int $id)
    {
        $order = Order::with(['user', 'orderItems.product'])->findOrFail($id);
        return view('admin.invoice', compact('order'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status'         => 'required|in:Pending,Processing,Shipped,Delivered,Cancelled',
            'payment_status' => 'nullable|in:Unpaid,Paid',
        ]);

        $order      = Order::with('user')->findOrFail($id);
        $oldStatus  = $order->status;
        $newStatus  = $request->status;

        $order->update(['status' => $newStatus]);

        if ($request->filled('payment_status')) {
            $order->update(['payment_status' => $request->payment_status]);
            if ($order->payment) {
                $order->payment->update([
                    'status' => $request->payment_status === 'Paid' ? 'Completed' : 'Pending',
                ]);
            }
        }

        // Notify user when order status changes
        if ($oldStatus !== $newStatus && $order->user_id) {
            try {
                $statusEmoji = match($newStatus) {
                    'Processing' => '⚙️',
                    'Shipped'    => '🚚',
                    'Delivered'  => '✅',
                    'Cancelled'  => '❌',
                    default      => '🔔',
                };
                Notification::notifyUser(
                    userId: $order->user_id,
                    type: 'order',
                    title: 'Order #' . $order->id . ' ' . $statusEmoji,
                    message: 'Your order status has been updated to: ' . $newStatus,
                    url: route('order.detail', $order->id)
                );
            } catch (\Exception $e) {
                Log::error('Order status notification failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.order.detail', $id)
            ->with('success', app()->getLocale()=='km' ? 'បានធ្វើបច្ចុប្បន្នភាព!' : 'Order status updated!');
    }

    // ════════════════════════════════════════════════════════
    //  USERS MANAGEMENT
    // ════════════════════════════════════════════════════════

    public function users(Request $request)
    {
        $users = $this->getFilteredUsers($request);

        return view('admin.users', [
            'users'          => $users->paginate(20)->withQueryString(),
            'totalUsers'     => User::count(),
            'totalCustomers' => User::where('role', 'Customer')->count(),
            'totalAdmins'    => User::where('role', 'Admin')->count(),
            'totalStaff'     => User::where('role', 'Staff')->count(),
            'totalInactive'  => User::where('is_active', false)->count(),
        ]);
    }

    public function exportUsersExcel(Request $request)
    {
        $users    = $this->getFilteredUsers($request)->latest()->get();
        $filename = 'users_' . now()->format('Ymd_His') . '.xls';

        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\">\n";
        $xml .= "<Worksheet ss:Name=\"Users\"><Table>\n";
        $xml .= "<Row>";
        foreach (['No.', 'Name', 'Email', 'Role', 'Status', 'Joined'] as $h)
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($h) . "</Data></Cell>";
        $xml .= "</Row>\n";

        foreach ($users as $i => $u) {
            $xml .= "<Row>";
            $xml .= "<Cell><Data ss:Type=\"Number\">" . ($i + 1) . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($u->name) . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($u->email) . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($u->role) . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . ($u->is_active ? 'Active' : 'Inactive') . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($u->created_at->format('d M Y')) . "</Data></Cell>";
            $xml .= "</Row>\n";
        }
        $xml .= "</Table></Worksheet></Workbook>";

        return response($xml, 200, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function exportUsersPdf(Request $request)
    {
        $users = $this->getFilteredUsers($request)->latest()->get();

        $stats = [
            ['label' => 'Total Users',  'value' => $users->count(),                              'color' => 'blue'],
            ['label' => 'Admins',       'value' => $users->where('role', 'Admin')->count(),       'color' => 'amber'],
            ['label' => 'Staff',        'value' => $users->where('role', 'Staff')->count(),       'color' => 'purple'],
            ['label' => 'Customers',    'value' => $users->where('role', 'Customer')->count(),    'color' => 'indigo'],
            ['label' => 'Active',       'value' => $users->where('is_active', true)->count(),     'color' => 'green'],
        ];

        $activeFilters = [];
        if ($request->filled('search'))  $activeFilters[] = '🔍 "' . $request->search . '"';
        if ($request->filled('tab'))     $activeFilters[] = '👤 ' . ucfirst($request->tab);
        if ($request->filled('status'))  $activeFilters[] = '🔘 ' . ucfirst($request->status);
        if ($request->filled('date_from') || $request->filled('date_to'))
            $activeFilters[] = '📅 ' . ($request->date_from ?? '…') . ' → ' . ($request->date_to ?? '…');

        $columns = [
            ['label' => 'User',   'render' => fn($u) => '<span class="cell-bold">' . e($u->name) . '</span><br><span style="font-size:11px;color:#94a3b8;">' . e($u->email) . '</span>'],
            ['label' => 'Role',   'render' => fn($u) => match($u->role) {
                'Admin'    => '<span style="background:#fef3c7;color:#d97706;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Admin</span>',
                'Staff'    => '<span style="background:#ede9fe;color:#7c3aed;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Staff</span>',
                default    => '<span style="background:#e0e7ff;color:#4338ca;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Customer</span>',
            }],
            ['label' => 'Status', 'render' => fn($u) => $u->is_active
                ? '<span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Active</span>'
                : '<span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Inactive</span>'],
            ['label' => 'Joined', 'render' => fn($u) => '<span style="font-size:12px;color:#6b7280;">' . e($u->created_at->format('d M Y')) . '</span>'],
        ];

        return view('admin.export-pdf', [
            'title'         => 'Users Report',
            'icon'          => '👥',
            'rowLabel'      => 'users',
            'rows'          => $users,
            'stats'         => $stats,
            'columns'       => $columns,
            'activeFilters' => $activeFilters,
        ]);
    }

    private function getFilteredUsers(Request $request)
    {
        $query = User::withCount('orders');

        if ($request->filled('search')) {
            $query->where(fn($q) => $q
                ->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
            );
        }

        if ($request->filled('status'))   $query->where('is_active', $request->status === 'active');
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);

        if ($request->filled('tab')) {
            if ($request->tab === 'admin')    $query->where('role', 'Admin');
            if ($request->tab === 'staff')    $query->where('role', 'Staff');
            if ($request->tab === 'customer') $query->where('role', 'Customer');
            if ($request->tab === 'inactive') $query->where('is_active', false);
        }

        return $query->latest();
    }

    public function toggleUser(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->isMainAdmin()) {
            return back()->with('error',
                app()->getLocale()=='km' ? 'មិនអាចបិទ Admin ចម្បងបានទេ!' : 'Cannot deactivate the main admin!'
            );
        }

        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success',
            $user->fresh()->is_active
                ? (app()->getLocale()=='km' ? 'បានបើក User!' : 'User activated!')
                : (app()->getLocale()=='km' ? 'បានបិទ User!' : 'User deactivated!')
        );
    }

    public function promoteUser(Request $request, int $id)
    {
        $user    = User::findOrFail($id);
        $newRole = $request->input('role', 'Customer');

        if ($user->isMainAdmin()) {
            return back()->with('error',
                app()->getLocale()=='km'
                    ? 'មិនអាចផ្លាស់ប្តូរ Main Admin!'
                    : 'Cannot change Main Admin role!'
            );
        }

        $validRoles = ['Customer', 'Staff', 'Admin'];
        if (!in_array($newRole, $validRoles)) {
            return back()->with('error',
                app()->getLocale()=='km' ? 'ប្រភេទមិនត្រឹមត្រូវ!' : 'Invalid role!'
            );
        }

        $user->update(['role' => $newRole]);

        $label = match($newRole) {
            'Admin'    => 'Admin',
            'Staff'    => app()->getLocale()=='km' ? 'បុគ្គលិក' : 'Staff',
            'Customer' => app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer',
            default    => $newRole,
        };

        return back()->with('success',
            app()->getLocale()=='km'
                ? "បានផ្លាស់ប្តូរ {$user->name} ជា {$label}!"
                : "Changed {$user->name} role to {$label}!"
        );
    }

    public function resetUserPassword(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->isMainAdmin()) {
            return back()->with('error',
                app()->getLocale()=='km' ? 'មិនអាច Reset password Admin ចម្បងបានទេ!' : 'Cannot reset main admin password here!'
            );
        }

        $user->update(['password' => Hash::make('password123')]);

        return back()->with('success',
            app()->getLocale()=='km' ? 'បាន Reset password ជា "password123"!' : 'Password reset to "password123"!'
        );
    }

    public function deleteUser(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->isMainAdmin()) {
            return back()->with('error',
                app()->getLocale()=='km' ? 'មិនអាចលប់ Admin ចម្បងបានទេ!' : 'Cannot delete the main admin!'
            );
        }

        if ($user->orders()->count() > 0) {
            return back()->with('error',
                app()->getLocale()=='km' ? 'មិនអាចលប់ — User នេះមានការបញ្ជាទិញ!' : 'Cannot delete — user has orders!'
            );
        }

        $user->cartItems()->delete();
        $user->wishlists()->delete();
        $user->reviews()->delete();
        $user->delete();

        return back()->with('success',
            app()->getLocale()=='km' ? 'បានលប់ User!' : 'User deleted!'
        );
    }

    // ════════════════════════════════════════════════════════
    //  COUPONS
    // ════════════════════════════════════════════════════════

    public function coupons()
    {
        return view('admin.coupons', ['coupons' => Coupon::latest()->get()]);
    }

    public function createCoupon()
    {
        return view('admin.coupon-form', ['coupon' => null]);
    }

    public function storeCoupon(Request $request)
    {
        $request->validate([
            'code'  => 'required|string|max:50|unique:coupons,code',
            'type'  => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
        ]);
        Coupon::create([
            'code'       => strtoupper($request->code),
            'type'       => $request->type,
            'value'      => $request->value,
            'min_order'  => $request->min_order ?? 0,
            'max_uses'   => $request->max_uses ?: null,
            'expires_at' => $request->expires_at ?: null,
            'is_active'  => true,
            'used_count' => 0,
        ]);
        return redirect()->route('admin.coupons')
            ->with('success', app()->getLocale()=='km' ? 'បានបន្ថែមគូប៉ុង!' : 'Coupon created!');
    }

    public function editCoupon(int $id)
    {
        return view('admin.coupon-form', ['coupon' => Coupon::findOrFail($id)]);
    }

    public function updateCoupon(Request $request, int $id)
    {
        $request->validate(['type' => 'required|in:percent,fixed', 'value' => 'required|numeric|min:0']);
        Coupon::findOrFail($id)->update([
            'type'       => $request->type,
            'value'      => $request->value,
            'min_order'  => $request->min_order ?? 0,
            'max_uses'   => $request->max_uses ?: null,
            'expires_at' => $request->expires_at ?: null,
            'is_active'  => $request->boolean('is_active', true),
        ]);
        return redirect()->route('admin.coupons')
            ->with('success', app()->getLocale()=='km' ? 'បានធ្វើបច្ចុប្បន្នភាព!' : 'Coupon updated!');
    }

    public function deleteCoupon(int $id)
    {
        Coupon::findOrFail($id)->delete();
        return redirect()->route('admin.coupons')
            ->with('success', app()->getLocale()=='km' ? 'បានលប់ចោល!' : 'Coupon deleted!');
    }

    // ════════════════════════════════════════════════════════
    //  REVIEWS
    // ════════════════════════════════════════════════════════

    public function reviews(Request $request)
    {
        $query = Review::with(['user', 'product']);
        if ($request->filled('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }
        return view('admin.reviews', ['reviews' => $query->latest()->paginate(20)]);
    }

    public function approveReview(int $id)
    {
        Review::findOrFail($id)->update(['is_approved' => true]);
        return back()->with('success', app()->getLocale()=='km' ? 'បានអនុម័ត!' : 'Review approved!');
    }

    public function deleteReview(int $id)
    {
        Review::findOrFail($id)->delete();
        return back()->with('success', app()->getLocale()=='km' ? 'បានលប់ចោល!' : 'Review deleted!');
    }

    // ════════════════════════════════════════════════════════
    //  BANNERS
    // ════════════════════════════════════════════════════════

    public function banners()
    {
        return view('admin.banners', [
            'banners' => Banner::orderBy('sort_order')->get(),
        ]);
    }

    public function createBanner()
    {
        return view('admin.banner-form', ['banner' => null]);
    }

    public function storeBanner(Request $request)
    {
        $request->validate(['title' => 'required|string|max:200']);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path     = $request->file('image')->store('banners', 'public');
            $imageUrl = '/storage/'.$path;
        }

        Banner::create([
            'title'       => $request->title,
            'subtitle'    => $request->subtitle,
            'image_url'   => $imageUrl,
            'link_url'    => $request->link_url,
            'button_text' => $request->button_text,
            'sort_order'  => $request->sort_order ?? 0,
            'is_active'   => true,
        ]);

        return redirect()->route('admin.banners')
            ->with('success', app()->getLocale()=='km' ? 'បានបន្ថែមបដា!' : 'Banner created!');
    }

    public function editBanner(int $id)
    {
        return view('admin.banner-form', ['banner' => Banner::findOrFail($id)]);
    }

    public function updateBanner(Request $request, int $id)
    {
        $banner   = Banner::findOrFail($id);
        $imageUrl = $banner->image_url;

        if ($request->hasFile('image')) {
            $path     = $request->file('image')->store('banners', 'public');
            $imageUrl = '/storage/'.$path;
        }

        $banner->update([
            'title'       => $request->title,
            'subtitle'    => $request->subtitle,
            'image_url'   => $imageUrl,
            'link_url'    => $request->link_url,
            'button_text' => $request->button_text,
            'sort_order'  => $request->sort_order ?? 0,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.banners')
            ->with('success', app()->getLocale()=='km' ? 'បានធ្វើបច្ចុប្បន្នភាព!' : 'Banner updated!');
    }

    public function toggleBanner(int $id)
    {
        $banner = Banner::findOrFail($id);
        $banner->update(['is_active' => !$banner->is_active]);
        return back()->with('success',
            $banner->fresh()->is_active
                ? (app()->getLocale()=='km' ? 'បានបើក!' : 'Banner activated!')
                : (app()->getLocale()=='km' ? 'បានបិទ!' : 'Banner deactivated!')
        );
    }

    public function deleteBanner(int $id)
    {
        Banner::findOrFail($id)->delete();
        return redirect()->route('admin.banners')
            ->with('success', app()->getLocale()=='km' ? 'បានលប់ចោល!' : 'Banner deleted!');
    }

    // ════════════════════════════════════════════════════════
    //  PAYMENTS
    // ════════════════════════════════════════════════════════

    public function payments(Request $request)
    {
        $payments = $this->getFilteredPayments($request)->paginate(20)->withQueryString();
        return view('admin.payments', compact('payments'));
    }

    public function exportPaymentsExcel(Request $request)
    {
        $payments = $this->getFilteredPayments($request)->get();

        $headers = ['No','Order','Customer','Email','Amount','Method','Status','Date'];

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"';
        $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        $xml .= '<Worksheet ss:Name="Payments"><Table>' . "\n";

        // Header row
        $xml .= '<Row>';
        foreach ($headers as $h) {
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($h) . '</Data></Cell>';
        }
        $xml .= '</Row>' . "\n";

        // Data rows
        foreach ($payments as $i => $p) {
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="Number">' . ($i + 1) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">RA-' . ($p->order->id ?? '—') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($p->user->name ?? '') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($p->user->email ?? '') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="Number">' . number_format($p->amount, 2, '.', '') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($p->method ?? '') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($p->status ?? '') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . $p->created_at->format('M d, Y') . '</Data></Cell>';
            $xml .= '</Row>' . "\n";
        }

        $xml .= '</Table></Worksheet></Workbook>';

        $filename = 'payments_' . date('Ymd_His') . '.xls';
        return response($xml, 200, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPaymentsPdf(Request $request)
    {
        $payments = $this->getFilteredPayments($request)->get();

        $locale = app()->getLocale();

        $total     = $payments->count();
        $revenue   = $payments->sum('amount');
        $completed = $payments->where('status', 'Completed')->count();
        $failed    = $payments->where('status', 'Failed')->count();

        $stats = [
            ['label' => $locale === 'km' ? 'ការទូទាត់សរុប'  : 'Total Payments', 'value' => number_format($total),         'color' => '#6366f1'],
            ['label' => $locale === 'km' ? 'ចំណូលសរុប'      : 'Total Revenue',  'value' => '$' . number_format($revenue, 2), 'color' => '#f59e0b'],
            ['label' => $locale === 'km' ? 'បានបញ្ចប់'      : 'Completed',       'value' => number_format($completed),       'color' => '#10b981'],
            ['label' => $locale === 'km' ? 'បរាជ័យ'         : 'Failed',          'value' => number_format($failed),          'color' => '#ef4444'],
        ];

        $activeFilters = [];
        if ($request->filled('status'))    $activeFilters[] = 'Status: ' . $request->status;
        if ($request->filled('date_from')) $activeFilters[] = 'From: ' . $request->date_from;
        if ($request->filled('date_to'))   $activeFilters[] = 'To: '   . $request->date_to;

        $columns = [
            ['label' => 'Order',    'render' => fn($p) => '<span class="cell-bold">RA-' . ($p->order->id ?? '—') . '</span>'],
            ['label' => 'Customer', 'render' => fn($p) => '<span class="cell-bold">' . e(\Illuminate\Support\Str::limit($p->user->name ?? '—', 18)) . '</span><br><span style="font-size:11px;color:#94a3b8;">' . e($p->user->email ?? '') . '</span>'],
            ['label' => 'Amount',   'render' => fn($p) => '<span style="font-weight:700;color:#f59e0b;">$' . number_format($p->amount, 2) . '</span>'],
            ['label' => 'Method',   'render' => fn($p) => $p->method ?? '—'],
            ['label' => 'Status',   'render' => fn($p) => match($p->status) {
                'Completed' => '<span class="pill pill-delivered">Completed</span>',
                'Failed'    => '<span class="pill pill-cancelled">Failed</span>',
                'Refunded'  => '<span class="pill pill-shipped">Refunded</span>',
                default     => '<span class="pill pill-pending">Pending</span>',
            }],
            ['label' => 'Date',     'render' => fn($p) => '<span style="font-size:12px;color:#6b7280;">' . $p->created_at->format('d M Y') . '</span>'],
        ];

        return view('admin.export-pdf', [
            'title'         => $locale === 'km' ? 'ការទូទាត់' : 'Payments',
            'icon'          => '💳',
            'rows'          => $payments,
            'columns'       => $columns,
            'stats'         => $stats,
            'activeFilters' => $activeFilters,
            'rowLabel'      => $locale === 'km' ? 'ការទូទាត់' : 'payments',
        ]);
    }

    private function getFilteredPayments(Request $request)
    {
        $query = Payment::with(['order', 'user']);
        if ($request->filled('status'))    $query->where('status', $request->status);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);
        return $query->latest();
    }

    // ════════════════════════════════════════════════════════
    //  STOCK
    // ════════════════════════════════════════════════════════

    public function stock(Request $request)
    {
        $products = $this->getFilteredStock($request);
        return view('admin.stock', [
            'products'   => $products,
            'totalStock' => Product::where('is_active', true)->sum('stock'),
            'outOfStock' => Product::where('is_active', true)->where('stock', 0)->count(),
            'lowStock'   => Product::where('is_active', true)->whereBetween('stock', [1, 9])->count(),
            'filter'     => $request->filter ?? 'all',
            'categories' => Category::orderBy('name')->get(),
            'brands'     => Brand::orderBy('name')->get(),
        ]);
    }

    public function exportStockExcel(Request $request)
    {
        $products = $this->getFilteredStock($request);
        $filename = 'stock_' . now()->format('Ymd_His') . '.xls';

        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\">\n";
        $xml .= "<Worksheet ss:Name=\"Stock\"><Table>\n";
        $xml .= "<Row>";
        foreach (['No.', 'Product', 'Category', 'Brand', 'Stock', 'Status'] as $h)
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($h) . "</Data></Cell>";
        $xml .= "</Row>\n";

        foreach ($products as $i => $p) {
            $status = $p->stock == 0 ? 'Out of Stock' : ($p->stock < 10 ? 'Low Stock' : 'In Stock');
            $xml .= "<Row>";
            $xml .= "<Cell><Data ss:Type=\"Number\">" . ($i + 1) . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($p->name) . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($p->category->name ?? '') . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($p->brand->name ?? '') . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"Number\">" . $p->stock . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . $status . "</Data></Cell>";
            $xml .= "</Row>\n";
        }
        $xml .= "</Table></Worksheet></Workbook>";

        return response($xml, 200, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function exportStockPdf(Request $request)
    {
        $products = $this->getFilteredStock($request);
        $filter   = $request->filter ?? 'all';

        $stats = [
            ['label' => 'Total Products', 'value' => $products->count(),                           'color' => 'blue'],
            ['label' => 'In Stock',       'value' => $products->where('stock', '>=', 10)->count(),  'color' => 'green'],
            ['label' => 'Low Stock',      'value' => $products->whereBetween('stock', [1,9])->count(), 'color' => 'amber'],
            ['label' => 'Out of Stock',   'value' => $products->where('stock', 0)->count(),         'color' => 'red'],
            ['label' => 'Total Units',    'value' => number_format($products->sum('stock')),        'color' => 'indigo'],
        ];

        $activeFilters = [];
        if ($request->filled('search'))    $activeFilters[] = '🔍 "' . $request->search . '"';
        if ($filter !== 'all')             $activeFilters[] = '📦 ' . ucfirst($filter);
        if ($request->filled('category_id')) {
            $cat = Category::find($request->category_id);
            if ($cat) $activeFilters[] = '📂 ' . $cat->name;
        }
        if ($request->filled('brand_id')) {
            $br = Brand::find($request->brand_id);
            if ($br) $activeFilters[] = '🏷 ' . $br->name;
        }
        if ($request->filled('date_from') || $request->filled('date_to'))
            $activeFilters[] = '📅 ' . ($request->date_from ?? '…') . ' → ' . ($request->date_to ?? '…');

        $columns = [
            ['label' => 'Product',  'render' => fn($p) => '<span class="cell-bold">' . e(Str::limit($p->name, 35)) . '</span>'],
            ['label' => 'Category', 'render' => fn($p) => '<span class="tag">' . e($p->category->name ?? '—') . '</span>'],
            ['label' => 'Brand',    'render' => fn($p) => '<span style="font-size:11px;color:#64748b;">' . e($p->brand->name ?? '—') . '</span>'],
            ['label' => 'Stock',    'render' => fn($p) => '<span style="font-size:18px;font-weight:900;color:' . ($p->stock==0 ? '#ef4444' : ($p->stock<10 ? '#f59e0b' : '#111')) . ';">' . $p->stock . '</span>'],
            ['label' => 'Status',   'render' => fn($p) => $p->stock == 0
                ? '<span class="pill pill-out">Out of Stock</span>'
                : ($p->stock < 10 ? '<span class="pill pill-low">Low Stock</span>' : '<span class="pill pill-ok">In Stock</span>')],
        ];

        return view('admin.export-pdf', [
            'title'         => 'Stock Report',
            'icon'          => '📦',
            'rowLabel'      => 'products',
            'rows'          => $products,
            'stats'         => $stats,
            'columns'       => $columns,
            'activeFilters' => $activeFilters,
        ]);
    }

    private function getFilteredStock(Request $request)
    {
        $query  = Product::with(['category', 'brand'])->where('is_active', true);
        $filter = $request->filter ?? 'all';
        if ($request->filled('search'))      $query->where('name', 'like', '%' . $request->search . '%');
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->filled('brand_id'))    $query->where('brand_id', $request->brand_id);
        if ($request->filled('date_from'))   $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))     $query->whereDate('created_at', '<=', $request->date_to);
        match ($filter) {
            'out'   => $query->where('stock', 0),
            'low'   => $query->whereBetween('stock', [1, 9]),
            'ok'    => $query->where('stock', '>=', 10),
            default => null,
        };
        return $query->orderBy('stock', 'asc')->get();
    }

    public function addStockForm(int $id)
    {
        return view('admin.add-stock', ['product' => Product::findOrFail($id)]);
    }

    public function addStock(Request $request, int $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:9999',
            'note'     => 'nullable|string|max:200',
        ]);

        $product = Product::findOrFail($id);
        $before  = $product->stock;
        $product->increment('stock', $request->quantity);
        $after = $product->fresh()->stock;

        StockHistory::create([
            'product_id'       => $product->id,
            'quantity_added'   => $request->quantity,
            'stock_before'     => $before,
            'stock_after'      => $after,
            'adj_type'         => 'add',
            'reason'           => 'restock',
            'note'             => $request->note,
            'added_by_user_id' => Auth::id(),
        ]);

        // Notify admin if stock was critically low and is now restocked
        try {
            if ($before < 10) {
                Notification::notifyAdmin(
                    type: 'stock',
                    title: 'Stock Restocked 📦',
                    message: "'{$product->name}' restocked: {$before} → {$after} units (+{$request->quantity})",
                    url: route('admin.stock')
                );
            }
        } catch (\Exception $e) {
            Log::error('Stock restock notification failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.stock')
            ->with('success', app()->getLocale()=='km'
                ? "បានបន្ថែម {$request->quantity} ទៅ '{$product->name}'!"
                : "Added {$request->quantity} units to '{$product->name}'!");
    }

    // ── Stock Maintenance ────────────────────────────────
    public function stockMaintenance(Request $request)
    {
        $query = Product::with(['category', 'brand'])->where('is_active', true);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        match ($request->get('filter', 'all')) {
            'out'   => $query->where('stock', 0),
            'low'   => $query->whereBetween('stock', [1, 9]),
            'ok'    => $query->where('stock', '>=', 10),
            default => null,
        };

        $products  = $query->orderBy('stock', 'asc')->get();
        $recentLog = StockHistory::with(['product', 'addedBy'])->latest()->take(20)->get();

        return view('admin.stock-maintenance', [
            'products'  => $products,
            'recentLog' => $recentLog,
            'filter'    => $request->get('filter', 'all'),
        ]);
    }

    public function stockAdjust(Request $request, int $id)
    {
        $request->validate([
            'adj_type' => 'required|in:add,subtract',
            'quantity' => 'required|integer|min:1|max:9999',
            'reason'   => 'required|string|max:50',
            'note'     => 'nullable|string|max:200',
        ]);

        $product = Product::findOrFail($id);
        $before  = $product->stock;
        $qty     = (int) $request->quantity;

        if ($request->adj_type === 'subtract') {
            if ($qty > $before) {
                return back()->with('error',
                    app()->getLocale() == 'km'
                        ? "មិនអាចកាត់ {$qty} ពីស្តុក {$before}!"
                        : "Cannot remove {$qty} from stock of {$before}!");
            }
            $product->decrement('stock', $qty);
        } else {
            $product->increment('stock', $qty);
        }

        $after = $product->fresh()->stock;

        StockHistory::create([
            'product_id'       => $product->id,
            'quantity_added'   => $qty,
            'stock_before'     => $before,
            'stock_after'      => $after,
            'adj_type'         => $request->adj_type,
            'reason'           => $request->reason,
            'note'             => $request->note,
            'added_by_user_id' => Auth::id(),
        ]);

        // Notify admin if stock drops to low/out after subtraction
        try {
            if ($request->adj_type === 'subtract') {
                if ($after == 0) {
                    Notification::notifyAdmin(
                        type: 'stock',
                        title: 'Out of Stock! 🚨',
                        message: "'{$product->name}' is now OUT OF STOCK (was {$before}).",
                        url: route('admin.stock')
                    );
                } elseif ($after < 10 && $before >= 10) {
                    Notification::notifyAdmin(
                        type: 'stock',
                        title: 'Low Stock Warning ⚠️',
                        message: "'{$product->name}' stock is low: only {$after} units remaining.",
                        url: route('admin.stock')
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Stock adjust notification failed: ' . $e->getMessage());
        }

        $action   = $request->adj_type === 'add' ? 'Added' : 'Removed';
        $actionKm = $request->adj_type === 'add' ? 'បានបន្ថែម' : 'បានកាត់';

        return back()->with('success',
            app()->getLocale() == 'km'
                ? "{$actionKm} {$qty} ចំពោះ '{$product->name}'!"
                : "{$action} {$qty} units for '{$product->name}'!");
    }

    public function stockHistory(Request $request)
    {
        $query = StockHistory::with(['product.category', 'addedBy']);

        if ($request->filled('product_id'))  $query->where('product_id', $request->product_id);
        if ($request->filled('date_from'))   $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))     $query->whereDate('created_at', '<=', $request->date_to);

        // Filter by type (reason column)
        if ($request->filled('type_filter')) {
            $query->where('reason', $request->type_filter);
        }

        $histories         = $query->latest()->paginate(50)->withQueryString();
        $totalAdded        = StockHistory::sum('quantity_added');
        $productsRestocked = StockHistory::distinct('product_id')->count('product_id');
        $lastRestock       = StockHistory::latest()->value('created_at');

        return view('admin.stock-history-translated', [
            'histories'         => $histories,
            'products'          => Product::where('is_active', true)->orderBy('name')->get(),
            'totalAdded'        => $totalAdded,
            'productsRestocked' => $productsRestocked,
            'lastRestock'       => $lastRestock ? \Carbon\Carbon::parse($lastRestock) : null,
        ]);
    }

    public function exportStockHistoryExcel(Request $request)
    {
        $query = StockHistory::with(['product.category', 'addedBy']);
        if ($request->filled('product_id'))  $query->where('product_id', $request->product_id);
        if ($request->filled('date_from'))   $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))     $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->filled('type_filter')) $query->where('reason', $request->type_filter);
        $histories = $query->latest()->get();

        $filename = 'stock_history_' . now()->format('Ymd_His') . '.xls';

        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\">\n";
        $xml .= "<Worksheet ss:Name=\"Stock History\"><Table>\n";
        $xml .= "<Row>";
        foreach (['No.', 'Date', 'Product', 'Category', 'Type', 'Before', 'Change', 'After', 'By', 'Note'] as $h)
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($h) . "</Data></Cell>";
        $xml .= "</Row>\n";

        foreach ($histories as $i => $h) {
            $isAdd  = ($h->stock_after >= $h->stock_before);
            $diff   = abs($h->stock_after - $h->stock_before);
            $change = $isAdd ? '+' . $diff : '-' . $diff;
            $reason = $h->reason ?? ($isAdd ? 'restock' : 'damaged');
            $type   = match($reason) {
                'damaged'  => 'Damage',
                'returned' => 'Return',
                default    => 'Add',
            };
            $xml .= "<Row>";
            $xml .= "<Cell><Data ss:Type=\"Number\">" . ($i + 1) . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($h->created_at->format('d M Y H:i')) . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($h->product->name ?? '') . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($h->product->category->name ?? '') . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . $type . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"Number\">" . $h->stock_before . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . $change . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"Number\">" . $h->stock_after . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($h->addedBy->name ?? '') . "</Data></Cell>";
            $xml .= "<Cell><Data ss:Type=\"String\">" . htmlspecialchars($h->note ?? '') . "</Data></Cell>";
            $xml .= "</Row>\n";
        }
        $xml .= "</Table></Worksheet></Workbook>";

        return response($xml, 200, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function exportStockHistoryPdf(Request $request)
    {
        $query = StockHistory::with(['product.category', 'addedBy']);
        if ($request->filled('product_id'))  $query->where('product_id', $request->product_id);
        if ($request->filled('date_from'))   $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))     $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->filled('type_filter')) $query->where('reason', $request->type_filter);
        $histories = $query->latest()->get();

        $stats = [
            ['label' => 'Total Changes',     'value' => number_format($histories->count()),                                      'color' => 'blue'],
            ['label' => 'Total Units Added',  'value' => '+' . number_format($histories->where('adj_type','add')->sum('quantity_added')), 'color' => 'green'],
            ['label' => 'Products Affected',  'value' => $histories->pluck('product_id')->unique()->count(),                      'color' => 'indigo'],
        ];

        $activeFilters = [];
        if ($request->filled('product_id')) {
            $pName = Product::find($request->product_id)?->name ?? $request->product_id;
            $activeFilters[] = '📦 ' . $pName;
        }
        if ($request->filled('type_filter')) {
            $typeNames = ['restock'=>'Add','damaged'=>'Damage','returned'=>'Return'];
            $activeFilters[] = '🏷 ' . ($typeNames[$request->type_filter] ?? $request->type_filter);
        }
        if ($request->filled('date_from') || $request->filled('date_to'))
            $activeFilters[] = '📅 ' . ($request->date_from ?? '…') . ' → ' . ($request->date_to ?? '…');

        $columns = [
            ['label' => 'Date',     'render' => fn($h) => '<span style="font-size:11px;">' . e($h->created_at->format('d M Y')) . '<br><span style="color:#9ca3af;">' . e($h->created_at->format('h:i A')) . '</span></span>'],
            ['label' => 'Product',  'render' => fn($h) => '<span class="cell-bold">' . e(Str::limit($h->product->name ?? '—', 28)) . '</span>'],
            ['label' => 'Category', 'render' => fn($h) => '<span class="tag">' . e($h->product->category->name ?? '—') . '</span>'],
            ['label' => 'Type',     'render' => function($h) {
                $isAdd  = ($h->stock_after >= $h->stock_before);
                $reason = $h->reason ?? ($isAdd ? 'restock' : 'damaged');
                [$label, $color, $bg] = match($reason) {
                    'damaged'  => ['Damage', '#dc2626', '#fee2e2'],
                    'returned' => ['Return', '#2563eb', '#dbeafe'],
                    default    => ['Add',    '#16a34a', '#dcfce7'],
                };
                return '<span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:' . $bg . ';color:' . $color . ';">' . $label . '</span>';
            }],
            ['label' => 'Before',   'render' => fn($h) => '<span style="color:#6b7280;font-weight:600;">' . $h->stock_before . '</span>'],
            ['label' => 'Change',   'render' => function($h) {
                $isAdd  = ($h->stock_after >= $h->stock_before);
                $diff   = abs($h->stock_after - $h->stock_before);
                $change = $isAdd ? '+' . $diff : '−' . $diff;
                $color  = $isAdd ? '#10b981' : '#ef4444';
                return '<span style="font-weight:900;color:' . $color . ';">' . $change . '</span>';
            }],
            ['label' => 'After',    'render' => fn($h) => '<span style="font-weight:800;font-size:14px;">' . $h->stock_after . '</span>'],
            ['label' => 'By',       'render' => fn($h) => '<span style="font-size:11px;color:#374151;">' . e($h->addedBy->name ?? '—') . '</span>'],
            ['label' => 'Note',     'render' => fn($h) => '<span style="font-size:11px;color:#9ca3af;">' . e($h->note ?? '—') . '</span>'],
        ];

        return view('admin.export-pdf', [
            'title'         => 'Stock History Report',
            'icon'          => '🕒',
            'rowLabel'      => 'changes',
            'rows'          => $histories,
            'stats'         => $stats,
            'columns'       => $columns,
            'activeFilters' => $activeFilters,
        ]);
    }

    // ════════════════════════════════════════════════════════
    //  SETTINGS
    // ════════════════════════════════════════════════════════

    public function settings()
    {
        return view('admin.settings');
    }

    // ════════════════════════════════════════════════════════
    //  PROFILE & PASSWORD
    // ════════════════════════════════════════════════════════

    public function profileUpdate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'phone'  => 'nullable|string|max:20',
            'title'  => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'title']);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if it exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return back()->with('profile_success', 'Profile updated successfully!');
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('password_error', 'Current password is incorrect.');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('password_success', 'Password changed successfully!');
    }
}