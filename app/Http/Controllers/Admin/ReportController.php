<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    // ── Report Page ───────────────────────────────────────
    public function index(Request $request)
    {
        // Load category/brand lists for filter dropdowns
        $categories = Category::orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();

        if (!$request->has('type')) {
            return view('admin.report', compact('categories', 'brands'));
        }

        $reportType = $request->get('report_type', 'sales');
        $reportData = null;

        [$periodLabel] = $this->buildPeriodLabel($request);

        if      ($reportType === 'sales')      $reportData = $this->buildSalesReport($request, $periodLabel);
        elseif  ($reportType === 'products')   $reportData = $this->buildProductsReport($request, $periodLabel);
        elseif  ($reportType === 'categories') $reportData = $this->buildCategoriesReport($request, $periodLabel);
        elseif  ($reportType === 'brands')     $reportData = $this->buildBrandsReport($request, $periodLabel);
        elseif  ($reportType === 'users')      $reportData = $this->buildUsersReport($request, $periodLabel);
        elseif  ($reportType === 'stock')      $reportData = $this->buildStockReport($request, $periodLabel);

        return view('admin.report', compact('reportData', 'reportType', 'categories', 'brands'));
    }

    // ══════════════════════════════════════════════════════
    //  EXPORT PDF
    // ══════════════════════════════════════════════════════
    public function exportPdf(Request $request)
    {
        $reportType  = $request->get('report_type', 'sales');
        [$periodLabel] = $this->buildPeriodLabel($request);
        $dateFrom    = $request->get('date_from', date('Y-m-01'));
        $dateTo      = $request->get('date_to', date('Y-m-d'));

        // ── STOCK PDF ─────────────────────────────────────
        if ($reportType === 'stock') {
            $stockSub    = $request->input('stock_type', '');
            $categoryId  = $request->input('category_id');
            $brandId     = $request->input('brand_id');

            $allInRange  = StockHistory::whereDate('created_at', '>=', $dateFrom)->whereDate('created_at', '<=', $dateTo);
            // NULL reason = old addStock() records (no reason saved) → treat as 'restock'
            $countAdd    = (clone $allInRange)->where(fn($q)=>$q->where('reason','restock')->orWhereNull('reason'))->count();
            $countDamage = (clone $allInRange)->where('reason', 'damaged')->count();
            $countReturn = (clone $allInRange)->where('reason', 'returned')->count();
            $totalChanges= $countAdd + $countDamage + $countReturn;

            $typeNames     = ['restock'=>'Add Stock','damaged'=>'Damage','returned'=>'Return'];
            $activeFilters = ['📅 ' . $periodLabel];
            if ($stockSub !== '') $activeFilters[] = '🏷 ' . ($typeNames[$stockSub] ?? $stockSub);

            $stats = [
                
            ];

            // '' = Total (All) — no reason filter; specific sub-type filters by reason
            if ($categoryId) { $activeFilters[] = '📁 ' . (Category::find($categoryId)->name ?? ''); }
            if ($brandId)    { $activeFilters[] = '🏷 ' . (Brand::find($brandId)->name ?? ''); }

            $hQuery = StockHistory::with(['product','addedBy'])
                ->whereDate('created_at','>=',$dateFrom)->whereDate('created_at','<=',$dateTo);
            // restock filter also picks up old records with NULL reason
            if ($stockSub === 'restock') $hQuery->where(fn($q)=>$q->where('reason','restock')->orWhereNull('reason'));
            elseif ($stockSub !== '') $hQuery->where('reason',$stockSub);
            if ($categoryId) $hQuery->whereHas('product', fn($q)=>$q->where('category_id',$categoryId));
            if ($brandId)    $hQuery->whereHas('product', fn($q)=>$q->where('brand_id',$brandId));
            $histories = $hQuery->latest()->get();

            $columns = [
                ['label'=>'Date',    'render'=>fn($h)=>'<span style="font-size:12px;color:#6b7280;">'.$h->created_at->format('d M Y').'<br>'.$h->created_at->format('h:i A').'</span>'],
                ['label'=>'Product', 'render'=>fn($h)=>'<span style="font-weight:600;font-size:13px;">'.e(Str::limit($h->product->name??'—',28)).'</span>'],
                ['label'=>'Type',    'render'=>function($h){ $isAdd=($h->stock_after>=$h->stock_before); $reason=$h->reason??($h->adj_type==='add'?'restock':($h->adj_type==='subtract'?'damaged':($isAdd?'restock':'damaged'))); return match($reason){'damaged'=>'<span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:#fee2e2;color:#dc2626;">🔴 Damage</span>','returned'=>'<span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:#dbeafe;color:#2563eb;">↩ Return</span>',default=>'<span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:#dcfce7;color:#16a34a;">＋ Add Stock</span>'}; }],
                ['label'=>'Before',  'render'=>fn($h)=>'<span style="display:block;text-align:center;color:#6b7280;font-weight:600;">'.$h->stock_before.'</span>'],
                ['label'=>'Change',  'render'=>function($h){ $isAdd=($h->stock_after>=$h->stock_before); $diff=abs($h->stock_after-$h->stock_before); $color=$isAdd?'#10b981':'#ef4444'; return '<span style="font-weight:900;color:'.$color.';font-size:15px;display:block;text-align:center;">'.($isAdd?'+':'−').$diff.'</span>'; }],
                ['label'=>'After',   'render'=>fn($h)=>'<span style="display:block;text-align:center;font-weight:800;font-size:14px;">'.$h->stock_after.'</span>'],
                ['label'=>'By',      'render'=>fn($h)=>'<span style="font-size:13px;color:#374151;">'.e($h->addedBy->name??'—').'</span>'],
                ['label'=>'Note',    'render'=>fn($h)=>'<span style="font-size:12px;color:#9ca3af;">'.e($h->note??'—').'</span>'],
            ];
            $subTitle = $stockSub === '' ? 'Total (All)' : ($typeNames[$stockSub] ?? $stockSub);
            return view('admin.export-pdf',['title'=>'Stock Report ('.$subTitle.') — '.$periodLabel,'icon'=>'📦','rowLabel'=>'records','rows'=>$histories,'stats'=>$stats,'columns'=>$columns,'activeFilters'=>$activeFilters]);
        }

        // ── PRODUCTS PDF ──────────────────────────────────
        if ($reportType === 'products') {
            $categoryId  = $request->input('category_id');
            $brandId     = $request->input('brand_id');

            $pQuery = Product::with(['category','brand']);
            if ($request->filled('date_from')) $pQuery->whereDate('created_at','>=',$dateFrom);
            if ($request->filled('date_to'))   $pQuery->whereDate('created_at','<=',$dateTo);
            if ($categoryId) $pQuery->where('category_id',$categoryId);
            if ($brandId)    $pQuery->where('brand_id',$brandId);
            $products   = $pQuery->latest()->get();
            $outOfStock = $products->where('stock',0)->count();
            $lowStock   = $products->where('stock','>',0)->where('stock','<',10)->count();
            $totalValue = $products->sum(fn($p)=>$p->price*$p->stock);

            $activeFilters = ['📅 '.$periodLabel];
            if ($categoryId) $activeFilters[] = '📁 '.(Category::find($categoryId)->name??'');
            if ($brandId)    $activeFilters[] = '🏷 '.(Brand::find($brandId)->name??'');

            $stats = [
                ['label'=>'Total Products','value'=>number_format($products->count()),'color'=>'indigo'],
                ['label'=>'Active',        'value'=>number_format($products->where('is_active',true)->count()),'color'=>'green'],
                ['label'=>'Out of Stock',  'value'=>number_format($outOfStock),'color'=>'red'],
                ['label'=>'Low Stock',     'value'=>number_format($lowStock),'color'=>'amber'],
            ];
            $columns = [
                ['label'=>'Product',  'render'=>fn($p)=>'<span style="font-weight:600;">'.e(Str::limit($p->name,30)).'</span><br><span style="font-size:11px;color:#9ca3af;">'.e($p->sku??'').'</span>'],
                ['label'=>'Category', 'render'=>fn($p)=>e($p->category->name??'—')],
                ['label'=>'Brand',    'render'=>fn($p)=>e($p->brand->name??'—')],
                ['label'=>'Price',    'render'=>fn($p)=>'<span style="font-weight:700;color:#f59e0b;">$'.number_format($p->price,2).'</span>'],
                ['label'=>'Stock',    'render'=>function($p){ $c=$p->stock==0?'#ef4444':($p->stock<10?'#f59e0b':'#10b981'); return '<span style="font-weight:800;color:'.$c.';">'.$p->stock.'</span>'; }],
                ['label'=>'Status',   'render'=>fn($p)=>$p->is_active?'<span style="background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Active</span>':'<span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Inactive</span>'],
                ['label'=>'Created',  'render'=>fn($p)=>'<span style="font-size:12px;color:#9ca3af;">'.$p->created_at->format('d M Y').'</span>'],
            ];
            return view('admin.export-pdf',['title'=>'Products Report — '.$periodLabel,'icon'=>'📦','rowLabel'=>'products','rows'=>$products,'stats'=>$stats,'columns'=>$columns,'activeFilters'=>$activeFilters]);
        }

        // ── CATEGORIES PDF ────────────────────────────────
        if ($reportType === 'categories') {
            $categories = Category::withCount(['products'])->orderBy('sort_order')->get();
            $stats = [
                ['label'=>'Total Categories','value'=>number_format($categories->count()),'color'=>'indigo'],
                ['label'=>'Active',           'value'=>number_format($categories->where('is_active',true)->count()),'color'=>'green'],
                ['label'=>'Has Products',     'value'=>number_format($categories->filter(fn($c)=>$c->products_count>0)->count()),'color'=>'amber'],
            ];
            $columns = [
                ['label'=>'Name',     'render'=>fn($c)=>'<span style="font-weight:600;">'.e($c->name).'</span>'],
                ['label'=>'Products', 'render'=>fn($c)=>'<span style="font-weight:700;color:#6366f1;">'.$c->products_count.'</span>'],
                ['label'=>'Sort',     'render'=>fn($c)=>$c->sort_order??0],
                ['label'=>'Status',   'render'=>fn($c)=>($c->is_active??true)?'<span style="background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Active</span>':'<span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Inactive</span>'],
            ];
            return view('admin.export-pdf',['title'=>'Categories Report — '.$periodLabel,'icon'=>'📁','rowLabel'=>'categories','rows'=>$categories,'stats'=>$stats,'columns'=>$columns,'activeFilters'=>['📅 '.$periodLabel]]);
        }

        // ── BRANDS PDF ────────────────────────────────────
        if ($reportType === 'brands') {
            $brands = Brand::withCount(['products'])->latest()->get();
            $stats = [
                ['label'=>'Total Brands','value'=>number_format($brands->count()),'color'=>'indigo'],
                ['label'=>'Active',      'value'=>number_format($brands->where('is_active',true)->count()),'color'=>'green'],
                ['label'=>'Inactive',    'value'=>number_format($brands->where('is_active',false)->count()),'color'=>'red'],
            ];
            $columns = [
                ['label'=>'Brand',    'render'=>fn($b)=>'<span style="font-weight:600;">'.e($b->name).'</span>'],
                ['label'=>'Products', 'render'=>fn($b)=>'<span style="font-weight:700;color:#6366f1;">'.$b->products_count.'</span>'],
                ['label'=>'Description','render'=>fn($b)=>'<span style="font-size:12px;color:#6b7280;">'.e(Str::limit($b->description??'—',40)).'</span>'],
                ['label'=>'Status',   'render'=>fn($b)=>$b->is_active?'<span style="background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Active</span>':'<span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Inactive</span>'],
            ];
            return view('admin.export-pdf',['title'=>'Brands Report — '.$periodLabel,'icon'=>'🏷','rowLabel'=>'brands','rows'=>$brands,'stats'=>$stats,'columns'=>$columns,'activeFilters'=>['📅 '.$periodLabel]]);
        }

        // ── USERS PDF ─────────────────────────────────────
        if ($reportType === 'users') {
            $uQuery = User::query();
            if ($request->filled('date_from')) $uQuery->whereDate('created_at','>=',$dateFrom);
            if ($request->filled('date_to'))   $uQuery->whereDate('created_at','<=',$dateTo);
            $users = $uQuery->latest()->get();
            $stats = [
                ['label'=>'Total Users','value'=>number_format($users->count()),'color'=>'indigo'],
                ['label'=>'Customers',  'value'=>number_format($users->where('role','Customer')->count()),'color'=>'green'],
                ['label'=>'Admins',     'value'=>number_format($users->where('role','Admin')->count()),'color'=>'amber'],
                ['label'=>'Inactive',   'value'=>number_format($users->where('is_active',false)->count()),'color'=>'red'],
            ];
            $columns = [
                ['label'=>'Name',   'render'=>fn($u)=>'<span style="font-weight:600;">'.e($u->name).'</span>'],
                ['label'=>'Email',  'render'=>fn($u)=>'<span style="font-size:12px;color:#6b7280;">'.e($u->email).'</span>'],
                ['label'=>'Role',   'render'=>function($u){ $c=match($u->role??'Customer'){'Admin'=>'#dc2626','Staff'=>'#7c3aed',default=>'#2563eb'}; return '<span style="font-size:11px;font-weight:700;color:'.$c.';background:'.$c.'18;padding:3px 8px;border-radius:20px;">'.e($u->role??'Customer').'</span>'; }],
                ['label'=>'Status', 'render'=>fn($u)=>$u->is_active?'<span style="background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Active</span>':'<span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Banned</span>'],
                ['label'=>'Joined', 'render'=>fn($u)=>'<span style="font-size:12px;color:#9ca3af;">'.$u->created_at->format('d M Y').'</span>'],
            ];
            return view('admin.export-pdf',['title'=>'Users Report — '.$periodLabel,'icon'=>'👥','rowLabel'=>'users','rows'=>$users,'stats'=>$stats,'columns'=>$columns,'activeFilters'=>['📅 '.$periodLabel]]);
        }

        // ── SALES PDF (default) ───────────────────────────
        [$query, $periodLabel] = $this->buildReportQuery($request);
        $query->with(['user','orderItems']);
        $orders   = $query->latest()->get();
        $orderIds = $orders->pluck('id');
        $products = OrderItem::whereIn('order_id',$orderIds)
            ->select('product_id','product_name',DB::raw('SUM(quantity) as total_sold'),DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id','product_name')->orderByDesc('total_sold')->with('product.category')->get();

        $totalRevenue = $orders->sum('total_amount');
        $totalOrders  = $orders->count();
        $totalItems   = $products->sum('total_sold');
        $avgOrder     = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $stats = [
            ['label'=>'Total Revenue','value'=>'$'.number_format($totalRevenue,2),'color'=>'amber'],
            ['label'=>'Total Orders', 'value'=>number_format($totalOrders),       'color'=>'indigo'],
            ['label'=>'Products Sold','value'=>number_format($totalItems),         'color'=>'green'],
            ['label'=>'Avg Order',    'value'=>'$'.number_format($avgOrder,2),     'color'=>'blue'],
        ];
        $columns = [
            ['label'=>'Order',    'render'=>fn($o)=>'<span style="font-weight:700;">RA-'.$o->id.'</span>'],
            ['label'=>'Customer', 'render'=>fn($o)=>'<span style="font-weight:600;">'.e(Str::limit($o->user->name??'—',20)).'</span><br><span style="font-size:11px;color:#94a3b8;">'.e($o->user->email??'').'</span>'],
            ['label'=>'Date',     'render'=>fn($o)=>'<span style="font-size:12px;color:#6b7280;">'.$o->created_at->format('d M Y').'</span>'],
            ['label'=>'Items',    'render'=>fn($o)=>'<span style="font-size:13px;">'.$o->orderItems->count().'</span>'],
            ['label'=>'Total',    'render'=>fn($o)=>'<span style="font-weight:700;color:#f59e0b;">$'.number_format($o->total_amount,2).'</span>'],
            ['label'=>'Status',   'render'=>fn($o)=>match($o->status){'Delivered'=>'<span class="pill pill-delivered">Delivered</span>','Cancelled'=>'<span class="pill pill-cancelled">Cancelled</span>','Shipped'=>'<span class="pill pill-shipped">Shipped</span>','Processing'=>'<span class="pill pill-processing">Processing</span>',default=>'<span class="pill pill-pending">Pending</span>'}],
        ];
        return view('admin.export-pdf',['title'=>'Sales Report — '.$periodLabel,'icon'=>'📊','rowLabel'=>'orders','rows'=>$orders,'stats'=>$stats,'columns'=>$columns,'activeFilters'=>['📅 '.$periodLabel]]);
    }

    // ══════════════════════════════════════════════════════
    //  EXPORT EXCEL
    // ══════════════════════════════════════════════════════
    public function exportExcel(Request $request)
    {
        $isKm       = $request->get('locale', app()->getLocale()) == 'km';
        $reportType = $request->get('report_type', 'sales');
        [$periodLabel] = $this->buildPeriodLabel($request);
        $dateFrom   = $request->get('date_from', date('Y-m-01'));
        $dateTo     = $request->get('date_to',   date('Y-m-d'));
        $categoryId = $request->input('category_id');
        $brandId    = $request->input('brand_id');

        $baseStyle = '
        body    { font-family: Arial, sans-serif; font-size: 11pt; }
        h1      { font-size: 16pt; font-weight: bold; color: #111; margin: 0 0 4px; }
        h2      { font-size: 12pt; font-weight: bold; color: #1d6f42; margin: 20px 0 6px; background: #f0fdf4; padding: 6px 10px; }
        p       { font-size: 10pt; color: #6b7280; margin: 2px 0; }
        table   { border-collapse: collapse; width: 100%; margin-bottom: 10px; }
        th      { background: #111; color: #fff; font-size: 10pt; font-weight: bold; padding: 7px 12px; border: 1px solid #374151; }
        td      { padding: 7px 12px; border: 1px solid #e5e7eb; font-size: 10pt; vertical-align: middle; }
        tr:nth-child(even) td { background: #f9fafb; }
        .sum-th { background: #1d6f42; color: #fff; font-size: 11pt; font-weight: bold; padding: 8px 14px; border: 1px solid #166534; text-align: center; }
        .sum-td { font-size: 13pt; font-weight: bold; padding: 10px 14px; border: 1px solid #d1fae5; text-align: center; background: #f0fdf4; }
        .gold   { color: #b45309; font-weight: bold; }
        .green  { color: #065f46; font-weight: bold; }
        .center { text-align: center; }
        .right  { text-align: right; }
        .badge-add    { background: #dcfce7; color: #16a34a; padding: 2px 8px; border-radius: 4px; font-weight: bold; }
        .badge-damage { background: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 4px; font-weight: bold; }
        .badge-return { background: #dbeafe; color: #2563eb; padding: 2px 8px; border-radius: 4px; font-weight: bold; }
        .positive { color: #16a34a; font-weight: bold; }
        .negative { color: #dc2626; font-weight: bold; }';

        $footer = '<p style="margin-top:20px;font-size:9pt;color:#9ca3af;border-top:1px solid #e5e7eb;padding-top:8px;">© '.date('Y').' ROBOTECH Academy — '.($isKm?'របាយការណ៍ត្រូវបានបង្កើតដោយប្រព័ន្ធ':'Report generated automatically by system').'</p>';

        // ── STOCK EXCEL ───────────────────────────────────
        if ($reportType === 'stock') {
            $stockSub = $request->input('stock_type','');
            $typeNames = ['restock'=>($isKm?'បន្ថែមស្តុក':'Add Stock'),'damaged'=>($isKm?'ខូចខាត':'Damage'),'returned'=>($isKm?'ត្រឡប់មក':'Return')];

            $allInRange  = StockHistory::whereDate('created_at','>=',$dateFrom)->whereDate('created_at','<=',$dateTo);
            // NULL reason = old addStock() records → treat as 'restock'
            $countAdd    = (clone $allInRange)->where(fn($q)=>$q->where('reason','restock')->orWhereNull('reason'))->count();
            $countDamage = (clone $allInRange)->where('reason','damaged')->count();
            $countReturn = (clone $allInRange)->where('reason','returned')->count();
            $totalChanges= $countAdd + $countDamage + $countReturn;

            $summaryRows = '
    <table><thead><tr>
        <th class="sum-th">📦 '.($isKm?'ស្តុកសរុប':'All Stock Changes').'</th>
        <th class="sum-th">＋ '.($isKm?'បន្ថែម':'Add Stock').'</th>
        <th class="sum-th">🔴 '.($isKm?'ខូចខាត':'Damage').'</th>
        <th class="sum-th">↩ '.($isKm?'ត្រឡប់':'Return').'</th>
    </tr></thead><tbody><tr>
        <td class="sum-td" style="color:#6366f1;">'.number_format($totalChanges).'</td>
        <td class="sum-td" style="color:#16a34a;">'.number_format($countAdd).'</td>
        <td class="sum-td" style="color:#dc2626;">'.number_format($countDamage).'</td>
        <td class="sum-td" style="color:#2563eb;">'.number_format($countReturn).'</td>
    </tr></tbody></table>';

            // '' = Total (All) — no reason filter; specific sub-type filters by reason
            // restock filter also picks up old records with NULL reason
            $hQuery = StockHistory::with(['product','addedBy'])
                ->whereDate('created_at','>=',$dateFrom)->whereDate('created_at','<=',$dateTo);
            if ($stockSub === 'restock') $hQuery->where(fn($q)=>$q->where('reason','restock')->orWhereNull('reason'));
            elseif ($stockSub !== '') $hQuery->where('reason',$stockSub);
            if ($categoryId) $hQuery->whereHas('product',fn($q)=>$q->where('category_id',$categoryId));
            if ($brandId)    $hQuery->whereHas('product',fn($q)=>$q->where('brand_id',$brandId));
            $histories = $hQuery->latest()->get();
            $subLabel  = $stockSub === '' ? ($isKm?'សរុបទាំងអស់':'Total (All)') : ($typeNames[$stockSub]??$stockSub);
            $filename  = 'StockReport-'.($stockSub===''?'Total':ucfirst($typeNames[$stockSub]??$stockSub)).'-'.$periodLabel.'.xls';

            $rows = '';
            foreach ($histories as $i => $h) {
                $isAdd  = ($h->stock_after >= $h->stock_before);
                $diff   = abs($h->stock_after - $h->stock_before);
                $reason = $h->reason??($h->adj_type==='add'?'restock':($h->adj_type==='subtract'?'damaged':($isAdd?'restock':'damaged')));
                [$badgeClass,$typeText] = match($reason){'damaged'=>['badge-damage',$isKm?'ខូចខាត':'🔴 Damage'],'returned'=>['badge-return',$isKm?'ត្រឡប់មក':'↩ Return'],default=>['badge-add',$isKm?'បន្ថែមស្តុក':'＋ Add Stock']};
                $changeClass = $isAdd ? 'positive' : 'negative';
                $rows .= '<tr>
                    <td class="center" style="color:#9ca3af;">'.($i+1).'</td>
                    <td>'.$h->created_at->format('d M Y').'<br><span style="color:#9ca3af;font-size:9pt;">'.$h->created_at->format('h:i A').'</span></td>
                    <td><strong>'.htmlspecialchars($h->product->name??'—').'</strong></td>
                    <td class="center"><span class="'.$badgeClass.'">'.$typeText.'</span></td>
                    <td class="center" style="color:#6b7280;font-weight:600;">'.$h->stock_before.'</td>
                    <td class="center '.$changeClass.'" style="font-size:13pt;">'.($isAdd?'+':'−').$diff.'</td>
                    <td class="center" style="font-weight:800;">'.$h->stock_after.'</td>
                    <td>'.htmlspecialchars($h->addedBy->name??'—').'</td>
                    <td style="color:#9ca3af;">'.htmlspecialchars($h->note??'').'</td>
                </tr>';
            }

            $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head><meta charset="UTF-8"><style>'.$baseStyle.'</style></head>
<body>
<h1>🤖 ROBOTECH Academy</h1>
<p>'.($isKm?'របាយការណ៍ស្តុក':'Stock Report').' — '.$subLabel.': <strong>'.$periodLabel.'</strong></p>
<p>'.($isKm?'បង្កើតនៅ':'Generated').': <strong>'.now()->format('M d, Y — h:i A').'</strong></p>
<h2>📊 '.($isKm?'សង្ខេបស្តុក':'Stock Summary').'</h2>'.$summaryRows.'
<h2>📋 '.$subLabel.' — '.($isKm?'ប្រវត្តិ':'History').' ('.$histories->count().')</h2>
<table><thead><tr>
    <th>#</th><th>'.($isKm?'កាលបរិច្ឆេទ':'Date').'</th><th>'.($isKm?'ផលិតផល':'Product').'</th>
    <th class="center">'.($isKm?'ប្រភេទ':'Type').'</th><th class="center">'.($isKm?'មុន':'Before').'</th>
    <th class="center">'.($isKm?'ការផ្លាស់ប្តូរ':'Change').'</th><th class="center">'.($isKm?'ក្រោយ':'After').'</th>
    <th>'.($isKm?'ដោយ':'By').'</th><th>'.($isKm?'កំណត់ចំណាំ':'Note').'</th>
</tr></thead><tbody>'.($rows?:('<tr><td colspan="9" class="center" style="color:#9ca3af;padding:16px;">'.($isKm?'មិនមានប្រវត្តិ':'No history').'</td></tr>')).'</tbody></table>'.$footer.'</body></html>';

            return response($html)
                ->header('Content-Type','application/vnd.ms-excel; charset=UTF-8')
                ->header('Content-Disposition','attachment; filename="'.$filename.'"')
                ->header('Pragma','no-cache')->header('Expires','0');
        }

        // ── PRODUCTS EXCEL ────────────────────────────────
        if ($reportType === 'products') {
            $pQuery = Product::with(['category','brand']);
            if ($request->filled('date_from')) $pQuery->whereDate('created_at','>=',$dateFrom);
            if ($request->filled('date_to'))   $pQuery->whereDate('created_at','<=',$dateTo);
            if ($categoryId) $pQuery->where('category_id',$categoryId);
            if ($brandId)    $pQuery->where('brand_id',$brandId);
            $products   = $pQuery->latest()->get();
            $outOfStock = $products->where('stock',0)->count();
            $lowStock   = $products->where('stock','>',0)->where('stock','<',10)->count();
            $totalValue = $products->sum(fn($p)=>$p->price*$p->stock);

            $rows = '';
            foreach ($products as $i => $p) {
                $sColor = $p->stock==0?'#dc2626':($p->stock<10?'#d97706':'#059669');
                $rows .= '<tr>
                    <td class="center" style="color:#9ca3af;">'.($i+1).'</td>
                    <td><strong>'.htmlspecialchars($p->name).'</strong><br><span style="color:#9ca3af;font-size:9pt;">'.htmlspecialchars($p->sku??'').'</span></td>
                    <td>'.htmlspecialchars($p->category->name??'—').'</td>
                    <td>'.htmlspecialchars($p->brand->name??'—').'</td>
                    <td class="right gold">$'.number_format($p->price,2).'</td>
                    <td class="center" style="font-weight:800;color:'.$sColor.';">'.$p->stock.'</td>
                    <td class="center">'.($p->is_active?($isKm?'សកម្ម':'Active'):($isKm?'បិទ':'Inactive')).'</td>
                    <td style="color:#9ca3af;font-size:9pt;">'.$p->created_at->format('d M Y').'</td>
                </tr>';
            }

            $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head><meta charset="UTF-8"><style>'.$baseStyle.'</style></head>
<body>
<h1>🤖 ROBOTECH Academy</h1>
<p>'.($isKm?'របាយការណ៍ផលិតផល':'Products Report').': <strong>'.$periodLabel.'</strong></p>
<p>'.($isKm?'បង្កើតនៅ':'Generated').': <strong>'.now()->format('M d, Y — h:i A').'</strong></p>
<table><thead><tr class="sum-th">
    <th class="sum-th">'.($isKm?'ផលិតផលសរុប':'Total').'</th><th class="sum-th">'.($isKm?'អស់ស្ទុក':'Out of Stock').'</th>
    <th class="sum-th">'.($isKm?'ស្ទុកតិច':'Low Stock').'</th><th class="sum-th">'.($isKm?'តម្លៃស្ទុក':'Stock Value').'</th>
</tr></thead><tbody><tr>
    <td class="sum-td" style="color:#6366f1;">'.$products->count().'</td>
    <td class="sum-td" style="color:#dc2626;">'.$outOfStock.'</td>
    <td class="sum-td" style="color:#d97706;">'.$lowStock.'</td>
    <td class="sum-td gold">$'.number_format($totalValue,2).'</td>
</tr></tbody></table>
<h2>📦 '.($isKm?'ផលិតផលទាំងអស់':'All Products').' ('.$products->count().')</h2>
<table><thead><tr>
    <th>#</th><th>'.($isKm?'ផលិតផល':'Product').'</th>
    <th>'.($isKm?'ប្រភេទ':'Category').'</th><th>'.($isKm?'ម៉ាក':'Brand').'</th>
    <th class="right">'.($isKm?'តម្លៃ':'Price').'</th><th class="center">'.($isKm?'ស្ទុក':'Stock').'</th>
    <th class="center">'.($isKm?'ស្ថានភាព':'Status').'</th><th>'.($isKm?'កាលបរិច្ឆេទ':'Created').'</th>
</tr></thead><tbody>'.($rows?:('<tr><td colspan="8" class="center" style="color:#9ca3af;padding:16px;">'.($isKm?'មិនមានទិន្នន័យ':'No products').'</td></tr>')).'</tbody></table>'.$footer.'</body></html>';

            return response($html)
                ->header('Content-Type','application/vnd.ms-excel; charset=UTF-8')
                ->header('Content-Disposition','attachment; filename="ProductsReport-'.$periodLabel.'.xls"')
                ->header('Pragma','no-cache')->header('Expires','0');
        }

        // ── CATEGORIES EXCEL ──────────────────────────────
        if ($reportType === 'categories') {
            $categories = Category::withCount(['products'])->orderBy('sort_order')->get();
            $rows = '';
            foreach ($categories as $i => $c) {
                $rows .= '<tr>
                    <td class="center" style="color:#9ca3af;">'.($i+1).'</td>
                    <td><strong>'.htmlspecialchars($c->name).'</strong></td>
                    <td class="center" style="font-weight:700;color:#6366f1;">'.$c->products_count.'</td>
                    <td class="center">'.($c->sort_order??0).'</td>
                    <td class="center">'.($c->is_active??true ? ($isKm?'សកម្ម':'Active') : ($isKm?'បិទ':'Inactive')).'</td>
                </tr>';
            }
            $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head><meta charset="UTF-8"><style>'.$baseStyle.'</style></head>
<body>
<h1>🤖 ROBOTECH Academy</h1>
<p>'.($isKm?'របាយការណ៍ប្រភេទ':'Categories Report').': <strong>'.$periodLabel.'</strong></p>
<p>'.($isKm?'បង្កើតនៅ':'Generated').': <strong>'.now()->format('M d, Y — h:i A').'</strong></p>
<h2>📁 '.($isKm?'ប្រភេទទាំងអស់':'All Categories').' ('.$categories->count().')</h2>
<table><thead><tr>
    <th>#</th><th>'.($isKm?'ឈ្មោះ':'Name').'</th>
    <th class="center">'.($isKm?'ផលិតផល':'Products').'</th>
    <th class="center">'.($isKm?'លំដាប់':'Sort').'</th>
    <th class="center">'.($isKm?'ស្ថានភាព':'Status').'</th>
</tr></thead><tbody>'.($rows?:('<tr><td colspan="5" class="center" style="color:#9ca3af;padding:16px;">'.($isKm?'មិនមានប្រភេទ':'No categories').'</td></tr>')).'</tbody></table>'.$footer.'</body></html>';
            return response($html)->header('Content-Type','application/vnd.ms-excel; charset=UTF-8')->header('Content-Disposition','attachment; filename="CategoriesReport-'.$periodLabel.'.xls"')->header('Pragma','no-cache')->header('Expires','0');
        }

        // ── BRANDS EXCEL ──────────────────────────────────
        if ($reportType === 'brands') {
            $brands = Brand::withCount(['products'])->latest()->get();
            $rows = '';
            foreach ($brands as $i => $b) {
                $rows .= '<tr>
                    <td class="center" style="color:#9ca3af;">'.($i+1).'</td>
                    <td><strong>'.htmlspecialchars($b->name).'</strong></td>
                    <td class="center" style="font-weight:700;color:#6366f1;">'.$b->products_count.'</td>
                    <td style="color:#6b7280;font-size:9pt;">'.htmlspecialchars(Str::limit($b->description??'',60)).'</td>
                    <td class="center">'.($b->is_active?($isKm?'សកម្ម':'Active'):($isKm?'បិទ':'Inactive')).'</td>
                </tr>';
            }
            $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head><meta charset="UTF-8"><style>'.$baseStyle.'</style></head>
<body>
<h1>🤖 ROBOTECH Academy</h1>
<p>'.($isKm?'របាយការណ៍ម៉ាក':'Brands Report').': <strong>'.$periodLabel.'</strong></p>
<p>'.($isKm?'បង្កើតនៅ':'Generated').': <strong>'.now()->format('M d, Y — h:i A').'</strong></p>
<h2>🏷 '.($isKm?'ម៉ាកទាំងអស់':'All Brands').' ('.$brands->count().')</h2>
<table><thead><tr>
    <th>#</th><th>'.($isKm?'ម៉ាក':'Brand').'</th>
    <th class="center">'.($isKm?'ផលិតផល':'Products').'</th>
    <th>'.($isKm?'ការពិពណ៌នា':'Description').'</th>
    <th class="center">'.($isKm?'ស្ថានភាព':'Status').'</th>
</tr></thead><tbody>'.($rows?:('<tr><td colspan="5" class="center" style="color:#9ca3af;padding:16px;">'.($isKm?'មិនមានម៉ាក':'No brands').'</td></tr>')).'</tbody></table>'.$footer.'</body></html>';
            return response($html)->header('Content-Type','application/vnd.ms-excel; charset=UTF-8')->header('Content-Disposition','attachment; filename="BrandsReport-'.$periodLabel.'.xls"')->header('Pragma','no-cache')->header('Expires','0');
        }

        // ── USERS EXCEL ───────────────────────────────────
        if ($reportType === 'users') {
            $uQuery = User::query();
            if ($request->filled('date_from')) $uQuery->whereDate('created_at','>=',$dateFrom);
            if ($request->filled('date_to'))   $uQuery->whereDate('created_at','<=',$dateTo);
            $users = $uQuery->latest()->get();
            $rows  = '';
            foreach ($users as $i => $u) {
                $rows .= '<tr>
                    <td class="center" style="color:#9ca3af;">'.($i+1).'</td>
                    <td><strong>'.htmlspecialchars($u->name).'</strong></td>
                    <td>'.htmlspecialchars($u->email).'</td>
                    <td class="center">'.($u->role??'Customer').'</td>
                    <td class="center">'.($u->is_active?($isKm?'សកម្ម':'Active'):($isKm?'ហាមឃាត់':'Banned')).'</td>
                    <td style="color:#9ca3af;font-size:9pt;">'.$u->created_at->format('d M Y').'</td>
                </tr>';
            }
            $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head><meta charset="UTF-8"><style>'.$baseStyle.'</style></head>
<body>
<h1>🤖 ROBOTECH Academy</h1>
<p>'.($isKm?'របាយការណ៍អ្នកប្រើ':'Users Report').': <strong>'.$periodLabel.'</strong></p>
<p>'.($isKm?'បង្កើតនៅ':'Generated').': <strong>'.now()->format('M d, Y — h:i A').'</strong></p>
<table><thead><tr>
    <th class="sum-th">'.($isKm?'សរុប':'Total').'</th>
    <th class="sum-th">'.($isKm?'អតិថិជន':'Customers').'</th>
    <th class="sum-th">'.($isKm?'Admin':'Admin').'</th>
    <th class="sum-th">'.($isKm?'អសកម្ម':'Inactive').'</th>
</tr><tr>
    <td class="sum-td" style="color:#6366f1;">'.$users->count().'</td>
    <td class="sum-td" style="color:#16a34a;">'.$users->where('role','Customer')->count().'</td>
    <td class="sum-td" style="color:#f59e0b;">'.$users->where('role','Admin')->count().'</td>
    <td class="sum-td" style="color:#dc2626;">'.$users->where('is_active',false)->count().'</td>
</tr></thead></table>
<h2>👥 '.($isKm?'អ្នកប្រើទាំងអស់':'All Users').' ('.$users->count().')</h2>
<table><thead><tr>
    <th>#</th><th>'.($isKm?'ឈ្មោះ':'Name').'</th><th>'.($isKm?'អ៊ីមែល':'Email').'</th>
    <th class="center">'.($isKm?'តួនាទី':'Role').'</th>
    <th class="center">'.($isKm?'ស្ថានភាព':'Status').'</th>
    <th>'.($isKm?'ចូលរួម':'Joined').'</th>
</tr></thead><tbody>'.($rows?:('<tr><td colspan="6" class="center" style="color:#9ca3af;padding:16px;">'.($isKm?'មិនមានអ្នកប្រើ':'No users').'</td></tr>')).'</tbody></table>'.$footer.'</body></html>';
            return response($html)->header('Content-Type','application/vnd.ms-excel; charset=UTF-8')->header('Content-Disposition','attachment; filename="UsersReport-'.$periodLabel.'.xls"')->header('Pragma','no-cache')->header('Expires','0');
        }

        // ── SALES EXCEL (default) ─────────────────────────
        [$query, $periodLabel] = $this->buildReportQuery($request);
        $query->with(['user','orderItems']);
        $orders   = $query->latest()->get();
        $orderIds = $orders->pluck('id');
        $products = OrderItem::whereIn('order_id',$orderIds)
            ->select('product_id','product_name',DB::raw('SUM(quantity) as total_sold'),DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id','product_name')->orderByDesc('total_sold')->get();
        $totalRevenue = $orders->sum('total_amount');
        $totalOrders  = $orders->count();
        $totalItems   = $products->sum('total_sold');
        $avgOrder     = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $prodRows = '';
        foreach ($products as $i => $p) {
            $prodRows .= '<tr>
                <td class="center">'.($i+1).'</td>
                <td>'.htmlspecialchars($p->product_name).'</td>
                <td class="center"><strong>'.number_format($p->total_sold).'</strong></td>
                <td class="right gold">$'.number_format($p->total_revenue,2).'</td>
                <td class="right">$'.number_format($p->total_revenue/max($p->total_sold,1),2).'</td>
            </tr>';
        }
        $statusBadge=['Pending'=>'badge-pending','Processing'=>'badge-processing','Shipped'=>'badge-shipped','Delivered'=>'badge-delivered','Cancelled'=>'badge-cancelled'];
        $orderRows = '';
        foreach ($orders as $o) {
            $badge = $statusBadge[$o->status]??'';
            $orderRows .= '<tr>
                <td><strong>RA-'.$o->id.'</strong></td>
                <td>'.htmlspecialchars($o->user->name??'—').'</td>
                <td>'.htmlspecialchars($o->user->email??'—').'</td>
                <td>'.$o->created_at->format('M d, Y').'</td>
                <td class="right gold">$'.number_format($o->total_amount,2).'</td>
                <td class="center">'.$o->payment_method.'</td>
                <td class="center"><span class="'.$badge.'">'.$o->status.'</span></td>
            </tr>';
        }

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head><meta charset="UTF-8"><style>'.$baseStyle.'
.badge-pending{background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:4px;}
.badge-delivered{background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:4px;}
.badge-cancelled{background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:4px;}
.badge-shipped{background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:4px;}
.badge-processing{background:#e0e7ff;color:#3730a3;padding:2px 8px;border-radius:4px;}
</style></head>
<body>
<h1>🤖 ROBOTECH Academy</h1>
<p>'.($isKm?'របាយការណ៍លក់':'Sales Report').': <strong>'.$periodLabel.'</strong></p>
<p>'.($isKm?'បង្កើតនៅ':'Generated').': <strong>'.now()->format('M d, Y — h:i A').'</strong></p>
<table><thead><tr>
    <th class="sum-th">'.($isKm?'ចំណូលសរុប':'Total Revenue').'</th>
    <th class="sum-th">'.($isKm?'ការបញ្ជាទិញ':'Total Orders').'</th>
    <th class="sum-th">'.($isKm?'ផលិតផលបានលក់':'Products Sold').'</th>
    <th class="sum-th">'.($isKm?'មធ្យម':'Avg Order').'</th>
</tr></thead><tbody><tr>
    <td class="sum-td gold">$'.number_format($totalRevenue,2).'</td>
    <td class="sum-td">'.$totalOrders.'</td>
    <td class="sum-td">'.$totalItems.'</td>
    <td class="sum-td gold">$'.number_format($avgOrder,2).'</td>
</tr></tbody></table>
<h2>📊 '.($isKm?'ផលិតផលបានលក់':'Products Sold').'</h2>
<table><thead><tr>
    <th>#</th><th>'.($isKm?'ផលិតផល':'Product').'</th>
    <th class="center">'.($isKm?'បរិមាណ':'Units Sold').'</th>
    <th class="right">'.($isKm?'ចំណូល':'Revenue').'</th>
    <th class="right">'.($isKm?'មធ្យម':'Avg Price').'</th>
</tr></thead><tbody>'.($prodRows?:('<tr><td colspan="5" class="center" style="color:#9ca3af;padding:16px;">'.($isKm?'មិនមានទិន្នន័យ':'No products sold').'</td></tr>')).'</tbody></table>
<h2>🛒 '.($isKm?'ការបញ្ជាទិញ':'Orders').' ('.$totalOrders.')</h2>
<table><thead><tr>
    <th>'.($isKm?'លេខ':'Order').'</th><th>'.($isKm?'អតិថិជន':'Customer').'</th>
    <th>'.($isKm?'អ៊ីមែល':'Email').'</th><th>'.($isKm?'កាលបរិច្ឆេទ':'Date').'</th>
    <th class="right">'.($isKm?'សរុប':'Total').'</th>
    <th class="center">'.($isKm?'ការទូទាត់':'Payment').'</th>
    <th class="center">'.($isKm?'ស្ថានភាព':'Status').'</th>
</tr></thead><tbody>'.($orderRows?:('<tr><td colspan="7" class="center" style="color:#9ca3af;padding:16px;">'.($isKm?'មិនមានការបញ្ជាទិញ':'No orders').'</td></tr>')).'</tbody></table>'.$footer.'</body></html>';

        return response($html)
            ->header('Content-Type','application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition','attachment; filename="SalesReport-'.$periodLabel.'.xls"')
            ->header('Pragma','no-cache')->header('Expires','0');
    }

    // ══════════════════════════════════════════════════════
    //  PRIVATE REPORT BUILDERS
    // ══════════════════════════════════════════════════════

    private function buildSalesReport(Request $request, string $periodLabel): array
    {
        $type  = $request->type;
        $query = Order::with(['user','orderItems'])->whereNotIn('status',['Cancelled']);

        if ($type === 'range') {
            $dateFrom = $request->get('date_from', date('Y-m-01'));
            $dateTo   = $request->get('date_to',   date('Y-m-d'));
            $query->whereDate('created_at','>=',$dateFrom)->whereDate('created_at','<=',$dateTo);
            $from = new \DateTime($dateFrom); $to = new \DateTime($dateTo);
            $chartLabels = $chartRevenue = $chartOrders = [];
            $cursor = clone $from;
            while ($cursor <= $to) {
                $ds = $cursor->format('Y-m-d');
                $chartLabels[] = $cursor->format('d M');
                $dq = Order::whereDate('created_at',$ds)->whereNotIn('status',['Cancelled']);
                $chartRevenue[] = (float)$dq->sum('total_amount');
                $chartOrders[]  = $dq->count();
                $cursor->modify('+1 day');
            }
        } elseif ($type === 'month') {
            $monthFrom=(int)$request->get('month_from',1);  $yearFrom=(int)$request->get('year_from',date('Y'));
            $monthTo=(int)$request->get('month_to',date('n')); $yearTo=(int)$request->get('year_to',date('Y'));
            $fromDate=\Carbon\Carbon::create($yearFrom,$monthFrom,1)->startOfMonth();
            $toDate=\Carbon\Carbon::create($yearTo,$monthTo,1)->endOfMonth();
            $query->whereBetween('created_at',[$fromDate,$toDate]);
            $chartLabels=$chartRevenue=$chartOrders=[];
            $cursor=clone $fromDate;
            while ($cursor<=$toDate) {
                $chartLabels[]=$cursor->format('M Y');
                $dq=Order::whereYear('created_at',$cursor->year)->whereMonth('created_at',$cursor->month)->whereNotIn('status',['Cancelled']);
                $chartRevenue[]=(float)$dq->sum('total_amount'); $chartOrders[]=$dq->count();
                $cursor->addMonth();
            }
        } else {
            $yearFrom=(int)$request->get('year_from',date('Y')); $yearTo=(int)$request->get('year_to',date('Y'));
            $query->whereYear('created_at','>=',$yearFrom)->whereYear('created_at','<=',$yearTo);
            $chartLabels=$chartRevenue=$chartOrders=[];
            for ($y=$yearFrom;$y<=$yearTo;$y++) {
                $chartLabels[]=(string)$y;
                $yq=Order::whereYear('created_at',$y)->whereNotIn('status',['Cancelled']);
                $chartRevenue[]=(float)$yq->sum('total_amount'); $chartOrders[]=$yq->count();
            }
        }

        $orders   = $query->latest()->get();
        $orderIds = $orders->pluck('id');
        $products = OrderItem::whereIn('order_id',$orderIds)
            ->select('product_id','product_name',DB::raw('SUM(quantity) as total_sold'),DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id','product_name')->orderByDesc('total_sold')->with('product.category')->get();

        return [
            'report_type'   => 'sales',
            'period_label'  => $periodLabel,
            'total_revenue' => $orders->sum('total_amount'),
            'total_orders'  => $orders->count(),
            'total_items'   => $products->sum('total_sold'),
            'avg_order'     => $orders->count()>0 ? $orders->sum('total_amount')/$orders->count() : 0,
            'orders'        => $orders,
            'products'      => $products,
            'chart_labels'  => $chartLabels,
            'chart_revenue' => $chartRevenue,
            'chart_orders'  => $chartOrders,
        ];
    }

    private function buildProductsReport(Request $request, string $periodLabel): array
    {
        $query = Product::with(['category','brand']);
        if ($request->filled('date_from'))   $query->whereDate('created_at','>=',$request->date_from);
        if ($request->filled('date_to'))     $query->whereDate('created_at','<=',$request->date_to);
        if ($request->filled('category_id')) $query->where('category_id',$request->category_id);
        if ($request->filled('brand_id'))    $query->where('brand_id',$request->brand_id);

        $products   = $query->latest()->get();
        $active     = $products->where('is_active',true)->count();
        $outOfStock = $products->where('stock',0)->count();
        $lowStock   = $products->where('stock','>',0)->where('stock','<',10)->count();
        $totalValue = $products->sum(fn($p)=>$p->price*$p->stock);

        return [
            'report_type'  => 'products',
            'period_label' => $periodLabel,
            'total'        => $products->count(),
            'active'       => $active,
            'out_of_stock' => $outOfStock,
            'low_stock'    => $lowStock,
            'total_value'  => $totalValue,
            'items'        => $products,
        ];
    }

    private function buildCategoriesReport(Request $request, string $periodLabel): array
    {
        $categories = Category::withCount(['products'])->orderBy('sort_order')->get();
        return [
            'report_type'   => 'categories',
            'period_label'  => $periodLabel,
            'total'         => $categories->count(),
            'active'        => $categories->where('is_active',true)->count(),
            'with_products' => $categories->filter(fn($c)=>$c->products_count>0)->count(),
            'items'         => $categories,
        ];
    }

    private function buildBrandsReport(Request $request, string $periodLabel): array
    {
        $brands = Brand::withCount(['products'])->latest()->get();
        return [
            'report_type'  => 'brands',
            'period_label' => $periodLabel,
            'total'        => $brands->count(),
            'active'       => $brands->where('is_active',true)->count(),
            'items'        => $brands,
        ];
    }

    private function buildUsersReport(Request $request, string $periodLabel): array
    {
        $query = User::query();
        if ($request->filled('date_from')) $query->whereDate('created_at','>=',$request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at','<=',$request->date_to);
        $users = $query->latest()->get();
        return [
            'report_type'  => 'users',
            'period_label' => $periodLabel,
            'total'        => $users->count(),
            'customers'    => $users->where('role','Customer')->count(),
            'admins'       => $users->where('role','Admin')->count(),
            'active'       => $users->where('is_active',true)->count(),
            'inactive'     => $users->where('is_active',false)->count(),
            'items'        => $users,
        ];
    }

    private function buildStockReport(Request $request, string $periodLabel): array
    {
        $stockSub   = $request->input('stock_type','');
        $dateFrom   = $request->get('date_from', date('Y-m-01'));
        $dateTo     = $request->get('date_to',   date('Y-m-d'));
        $categoryId = $request->input('category_id');
        $brandId    = $request->input('brand_id');

        $allInRange   = StockHistory::whereDate('created_at','>=',$dateFrom)->whereDate('created_at','<=',$dateTo);
        // NULL reason = old addStock() records (reason not saved) → treat as 'restock'
        $countAdd     = (clone $allInRange)->where(fn($q)=>$q->where('reason','restock')->orWhereNull('reason'))->count();
        $countDamage  = (clone $allInRange)->where('reason','damaged')->count();
        $countReturn  = (clone $allInRange)->where('reason','returned')->count();
        $totalChanges = $countAdd + $countDamage + $countReturn;

        // '' = Total (All) — show all history regardless of reason
        // restock filter also picks up old records with NULL reason
        $hQuery = StockHistory::with(['product','addedBy'])
            ->whereDate('created_at','>=',$dateFrom)->whereDate('created_at','<=',$dateTo);
        if ($stockSub === 'restock') $hQuery->where(fn($q)=>$q->where('reason','restock')->orWhereNull('reason'));
        elseif ($stockSub !== '') $hQuery->where('reason',$stockSub);
        if ($categoryId) $hQuery->whereHas('product',fn($q)=>$q->where('category_id',$categoryId));
        if ($brandId)    $hQuery->whereHas('product',fn($q)=>$q->where('brand_id',$brandId));

        return [
            'report_type'    => 'stock',
            'stock_sub'      => $stockSub,
            'period_label'   => $periodLabel,
            'total_changes'  => $totalChanges,
            'count_add'      => $countAdd,
            'count_damage'   => $countDamage,
            'count_return'   => $countReturn,
            'total_products' => 0,
            'out_of_stock'   => 0,
            'low_stock'      => 0,
            'total_value'    => 0,
            'products'       => collect(),
            'items'          => $hQuery->latest()->get(),
        ];
    }

    private function buildReportQuery(Request $request): array
    {
        $type  = $request->get('type','range');
        $query = Order::whereNotIn('status',['Cancelled']);

        if ($type === 'range') {
            $dateFrom = $request->get('date_from', date('Y-m-01'));
            $dateTo   = $request->get('date_to',   date('Y-m-d'));
            $query->whereDate('created_at','>=',$dateFrom)->whereDate('created_at','<=',$dateTo);
            $periodLabel = date('d M Y',strtotime($dateFrom)).' → '.date('d M Y',strtotime($dateTo));
        } elseif ($type === 'month') {
            $monthFrom=(int)$request->get('month_from',1);  $yearFrom=(int)$request->get('year_from',date('Y'));
            $monthTo=(int)$request->get('month_to',date('n')); $yearTo=(int)$request->get('year_to',date('Y'));
            $fromDate=\Carbon\Carbon::create($yearFrom,$monthFrom,1)->startOfMonth();
            $toDate=\Carbon\Carbon::create($yearTo,$monthTo,1)->endOfMonth();
            $query->whereBetween('created_at',[$fromDate,$toDate]);
            $periodLabel=date('M Y',mktime(0,0,0,$monthFrom,1,$yearFrom)).' → '.date('M Y',mktime(0,0,0,$monthTo,1,$yearTo));
        } else {
            $yearFrom=(int)$request->get('year_from',date('Y')); $yearTo=(int)$request->get('year_to',date('Y'));
            $query->whereYear('created_at','>=',$yearFrom)->whereYear('created_at','<=',$yearTo);
            $periodLabel=$yearFrom===$yearTo?(string)$yearFrom:$yearFrom.' → '.$yearTo;
        }
        return [$query,$periodLabel];
    }

    private function buildPeriodLabel(Request $request): array
    {
        $type = $request->get('type','range');
        if ($type === 'range') {
            $dateFrom=$request->get('date_from',date('Y-m-01'));
            $dateTo=$request->get('date_to',date('Y-m-d'));
            $periodLabel=date('d M Y',strtotime($dateFrom)).' → '.date('d M Y',strtotime($dateTo));
        } elseif ($type === 'month') {
            $monthFrom=(int)$request->get('month_from',1); $yearFrom=(int)$request->get('year_from',date('Y'));
            $monthTo=(int)$request->get('month_to',date('n')); $yearTo=(int)$request->get('year_to',date('Y'));
            $periodLabel=date('M Y',mktime(0,0,0,$monthFrom,1,$yearFrom)).' → '.date('M Y',mktime(0,0,0,$monthTo,1,$yearTo));
        } else {
            $yearFrom=(int)$request->get('year_from',date('Y')); $yearTo=(int)$request->get('year_to',date('Y'));
            $periodLabel=$yearFrom===$yearTo?(string)$yearFrom:$yearFrom.' → '.$yearTo;
        }
        return [$periodLabel];
    }
}