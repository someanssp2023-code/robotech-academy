@extends('layouts.admin')
@section('title', app()->getLocale()=='km' ? 'របាយការណ៍' : 'Reports')

@section('content')
<style>
.report-card { background:#fff; border-radius:12px; border:1px solid #e5e7eb; overflow:hidden; margin-bottom:24px; }
.report-head { padding:16px 20px; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; justify-content:space-between; }
.report-head h6 { margin:0; font-size:13px; font-weight:700; color:#111; }
.summary-card { background:#fff; border-radius:12px; border:1px solid #e5e7eb; padding:20px; }
.summary-lbl { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.7px; color:#9ca3af; margin-bottom:6px; }
.summary-val { font-size:28px; font-weight:800; line-height:1; }
</style>

{{-- ════════════════════ FILTER FORM ════════════════════ --}}
<div class="panel mb-4" style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;">
    <div class="report-head">
        <h6>{{ app()->getLocale()=='km' ? 'តម្រងរបាយការណ៍' : 'Report Filter' }}</h6>
    </div>
    <form method="GET" action="{{ route('admin.reports') }}" class="p-4" id="reportForm">

        {{-- Report Type Dropdown --}}
        @php $currentType = request('report_type', 'sales'); @endphp
        <input type="hidden" name="type" value="range"/>

        <div class="d-flex align-items-end gap-3 flex-wrap">

            {{-- Dropdown --}}
            <div>
                <label class="form-label fw-semibold small">
                    {{ app()->getLocale()=='km' ? 'ប្រភេទរបាយការណ៍' : 'Report Type' }}
                </label>
                <select name="report_type" id="reportTypeSelect" class="form-ctrl" onchange="toggleStockSub(this.value)" style="min-width:220px;">
                    <option value="sales"      {{ $currentType==='sales'      ? 'selected' : '' }}> {{ app()->getLocale()=='km' ? 'របាយការណ៍លក់'       : 'Sales Report' }}</option>
                    <option value="products"   {{ $currentType==='products'   ? 'selected' : '' }}> {{ app()->getLocale()=='km' ? 'របាយការណ៍ផលិតផល'   : 'Stock Report' }}</option>
                    <option value="categories" {{ $currentType==='categories' ? 'selected' : '' }}> {{ app()->getLocale()=='km' ? 'របាយការណ៍ប្រភេទ'   : 'Categories Report' }}</option>
                    <option value="brands"     {{ $currentType==='brands'     ? 'selected' : '' }}> {{ app()->getLocale()=='km' ? 'របាយការណ៍ម៉ាក'     : 'Brands Report' }}</option>
                    <option value="users"      {{ $currentType==='users'      ? 'selected' : '' }}> {{ app()->getLocale()=='km' ? 'របាយការណ៍អ្នកប្រើ' : 'Users Report' }}</option>
                    <option value="stock"      {{ $currentType==='stock'      ? 'selected' : '' }}> {{ app()->getLocale()=='km' ? 'របាយការណ៍ស្តុក'    : 'History Report' }}</option>
                </select>
            </div>

            {{-- Stock Sub-type dropdown — only when Stock Report is selected --}}
            <div id="stockSubRow" style="{{ $currentType !== 'stock' ? 'display:none;' : '' }}">
                <label class="form-label fw-semibold small">
                    {{ app()->getLocale()=='km' ? 'ប្រភេទស្តុក' : 'Stock Type' }}
                </label>
                <select name="stock_type" class="form-ctrl" style="min-width:200px;">

                    <option value="restock" {{ request('stock_type')=='restock'    ? 'selected' : '' }}> {{ app()->getLocale()=='km' ? 'បន្ថែមស្តុក'     : 'Add Stock' }}</option>
                    <option value="damaged" {{ request('stock_type')=='damaged'    ? 'selected' : '' }}> {{ app()->getLocale()=='km' ? 'ខូចខាត'          : 'Damage' }}</option>
                    <option value="returned"{{ request('stock_type')=='returned'   ? 'selected' : '' }}> {{ app()->getLocale()=='km' ? 'ត្រឡប់មក'        : 'Return' }}</option>
                </select>
            </div>

            {{-- Category filter — Products & Stock --}}
            @php $showCatBrand = in_array($currentType, ['products','stock']); @endphp
            <div id="categoryRow" style="{{ !$showCatBrand ? 'display:none;' : '' }}">
                <label class="form-label fw-semibold small">
                    {{ app()->getLocale()=='km' ? 'ប្រភេទ' : 'Category' }}
                </label>
                <select name="category_id" class="form-ctrl" style="min-width:180px;">
                    <option value="">{{ app()->getLocale()=='km' ? 'ប្រភេទទាំងអស់' : 'All Categories' }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Brand filter — Products & Stock --}}
            <div id="brandRow" style="{{ !$showCatBrand ? 'display:none;' : '' }}">
                <label class="form-label fw-semibold small">
                    {{ app()->getLocale()=='km' ? 'ម៉ាក' : 'Brand' }}
                </label>
                <select name="brand_id" class="form-ctrl" style="min-width:180px;">
                    <option value="">{{ app()->getLocale()=='km' ? 'ម៉ាកទាំងអស់' : 'All Brands' }}</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand_id')==$brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date Range — ALL reports --}}
            <div id="dateRangeRow">
                <div class="d-flex align-items-end gap-3 flex-wrap">
                    <div>
                        <label class="form-label fw-semibold small">{{ app()->getLocale()=='km' ? 'ពីថ្ងៃ' : 'Date From' }}</label>
                        <input type="date" name="date_from" class="form-ctrl"
                               value="{{ request('date_from', date('Y-m-01')) }}" style="min-width:160px;"/>
                    </div>
                    <div style="padding-bottom:9px;color:#6b7280;font-weight:600;font-size:13px;">—</div>
                    <div>
                        <label class="form-label fw-semibold small">{{ app()->getLocale()=='km' ? 'ដល់ថ្ងៃ' : 'Date To' }}</label>
                        <input type="date" name="date_to" class="form-ctrl"
                               value="{{ request('date_to', date('Y-m-d')) }}" style="min-width:160px;"/>
                    </div>
                </div>
            </div>

            {{-- Time Range — Sales only --}}
            <div id="timeRangeRow" style="{{ $currentType !== 'sales' ? 'display:none;' : '' }}">
                <div class="d-flex align-items-end gap-3 flex-wrap" style="margin-top:12px;">
                    <div>
                        <label class="form-label fw-semibold small">{{ app()->getLocale()=='km' ? 'ពីម៉ោង' : 'Time From' }}</label>
                        <input type="time" name="time_from" class="form-ctrl"
                               value="{{ request('time_from', '00:00') }}" style="min-width:140px;"/>
                    </div>
                    <div style="padding-bottom:9px;color:#6b7280;font-weight:600;font-size:13px;">—</div>
                    <div>
                        <label class="form-label fw-semibold small">{{ app()->getLocale()=='km' ? 'ដល់ម៉ោង' : 'Time To' }}</label>
                        <input type="time" name="time_to" class="form-ctrl"
                               value="{{ request('time_to', '23:59') }}" style="min-width:140px;"/>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div>
                <label class="form-label fw-semibold small d-block" style="visibility:hidden;">.</label>
                <button type="submit" class="qa-btn qa-primary" style="padding:9px 22px;white-space:nowrap;">
                    {{ app()->getLocale()=='km' ? 'បង្កើតរបាយការណ៍' : 'Generate Report' }}
                </button>
            </div>

        </div>

        <script>
        function toggleStockSub(val) {
            document.getElementById('timeRangeRow').style.display  = (val === 'sales') ? 'block' : 'none';
            document.getElementById('stockSubRow').style.display   = (val === 'stock') ? 'block' : 'none';
            // Category & Brand filters visible for Products and Stock
            var showCatBrand = (val === 'products' || val === 'stock');
            document.getElementById('categoryRow').style.display   = showCatBrand ? 'block' : 'none';
            document.getElementById('brandRow').style.display      = showCatBrand ? 'block' : 'none';
            // Clear stock_type when switching away from stock report
            if (val !== 'stock') {
                var st = document.querySelector('select[name="stock_type"]');
                if (st) st.value = '';
            }
            // Clear category/brand when switching to a type that doesn't use them
            if (!showCatBrand) {
                var cat = document.querySelector('select[name="category_id"]');
                var brd = document.querySelector('select[name="brand_id"]');
                if (cat) cat.value = '';
                if (brd) brd.value = '';
            }
        }
        </script>

    </form>
</div>

{{-- Export Buttons — always visible --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div style="font-size:13px;color:#6b7280;">
        @if(isset($reportData))
            {{ app()->getLocale()=='km' ? 'របាយការណ៍' : 'Report' }}:
            <strong style="color:#111;">{{ $reportData['period_label'] }}</strong>
        @endif
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reports.excel', array_merge(request()->all(), ['locale' => app()->getLocale()])) }}"
           class="qa-btn" style="border-color:#16a34a;color:#16a34a;">⬇ Excel</a>
        <a href="{{ route('admin.reports.pdf', request()->all()) }}"
           target="_blank"
           class="qa-btn" style="border-color:#dc2626;color:#dc2626;">⬇ PDF</a>
    </div>
</div>

@if(isset($reportData))

{{-- ════════════════════════════════════════════════════════ --}}
{{--                    SALES REPORT                         --}}
{{-- ════════════════════════════════════════════════════════ --}}
@if($reportType === 'sales')

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'ចំណូលសរុប' : 'Total Revenue' }}</div>
            <div class="summary-val" style="color:#111;">${{ number_format($reportData['total_revenue'], 2) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'ការបញ្ជាទិញសរុប' : 'Total Orders' }}</div>
            <div class="summary-val" style="color:#111;">{{ number_format($reportData['total_orders']) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'ផលិតផលបានលក់' : 'Products Sold' }}</div>
            <div class="summary-val" style="color:#111;">{{ number_format($reportData['total_items']) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'មធ្យមការបញ្ជាទិញ' : 'Avg Order Value' }}</div>
            <div class="summary-val" style="color:#111;">${{ number_format($reportData['avg_order'], 2) }}</div>
        </div>
    </div>
</div>



{{-- Products sold table --}}
<div class="report-card">
    <div class="report-head">
        <h6>{{ app()->getLocale()=='km' ? 'ផលិតផលបានលក់' : 'Products Sold' }} — {{ $reportData['period_label'] }}</h6>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">#</th>
                    <th>{{ app()->getLocale()=='km' ? 'ផលិតផល' : 'Product' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ប្រភេទ' : 'Category' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'បានលក់' : 'Units Sold' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ចំណូល' : 'Revenue' }}</th>
                    <th style="padding-right:20px;text-align:right;">{{ app()->getLocale()=='km' ? 'តម្លៃមធ្យម' : 'Avg Price' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData['products'] as $i => $p)
                <tr>
                    <td style="padding-left:20px;color:#9ca3af;font-size:12px;">{{ $i+1 }}</td>
                    <td><div style="font-weight:600;font-size:13px;color:#111;">{{ $p->product_name }}</div></td>
                    <td style="font-size:12px;color:#6b7280;">{{ $p->product->category->name ?? '—' }}</td>
                    <td>
                        <span style="font-weight:600;color:#374151;">{{ number_format($p->total_sold) }}</span>
                        <span style="color:#9ca3af;font-size:11px;">{{ app()->getLocale()=='km' ? 'មុខ' : 'units' }}</span>
                    </td>
                    <td style="font-weight:600;color:#374151;">${{ number_format($p->total_revenue, 2) }}</td>
                    <td style="padding-right:20px;text-align:right;font-size:13px;color:#374151;">
                        ${{ number_format($p->total_revenue / max($p->total_sold, 1), 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                        {{ app()->getLocale()=='km' ? 'មិនមានទិន្នន័យ' : 'No data for this period.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Orders list --}}
<div class="report-card">
    <div class="report-head">
        <h6>{{ app()->getLocale()=='km' ? 'ការបញ្ជាទិញ' : 'Orders' }} — {{ $reportData['period_label'] }}</h6>
        <span style="font-size:12px;color:#9ca3af;">{{ $reportData['total_orders'] }} {{ app()->getLocale()=='km' ? 'ការបញ្ជាទិញ' : 'orders' }}</span>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">{{ app()->getLocale()=='km' ? 'លេខ' : 'Order' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'មុខ' : 'Items' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'សរុប' : 'Total' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payment' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData['orders'] as $order)
                @php
                    $sc = match($order->status){
                        'Delivered'  => 'pill-delivered',
                        'Cancelled'  => 'pill-cancelled',
                        'Shipped'    => 'pill-shipped',
                        'Processing' => 'pill-processing',
                        default      => 'pill-pending'
                    };
                    $statusLabels = [
                        'Pending'    => app()->getLocale()=='km' ? 'រង់ចាំ'         : 'Pending',
                        'Processing' => app()->getLocale()=='km' ? 'កំពុងដំណើរការ' : 'Processing',
                        'Shipped'    => app()->getLocale()=='km' ? 'កំពុងដឹក'      : 'Shipped',
                        'Delivered'  => app()->getLocale()=='km' ? 'បានដឹកជញ្ជូន' : 'Delivered',
                        'Cancelled'  => app()->getLocale()=='km' ? 'បានបោះបង់'     : 'Cancelled',
                    ];
                @endphp
                <tr>
                    <td style="padding-left:20px;font-weight:700;color:#111;">RA-{{ $order->id }}</td>
                    <td>
                        <div style="font-weight:600;font-size:13px;color:#111;">{{ $order->user->name }}</div>
                        <div style="color:#9ca3af;font-size:11px;">{{ $order->user->email }}</div>
                    </td>
                    <td style="color:#9ca3af;font-size:12px;">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                    <td style="font-size:13px;color:#374151;">{{ $order->orderItems->count() }}</td>
                    <td style="font-weight:600;color:#374151;">${{ number_format($order->total_amount, 2) }}</td>
                    <td style="font-size:12px;color:#6b7280;">{{ $order->payment_method }}</td>
                    <td><span class="pill {{ $sc }}">{{ $statusLabels[$order->status] ?? $order->status }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                        {{ app()->getLocale()=='km' ? 'មិនមានការបញ្ជាទិញ' : 'No orders for this period.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
new Chart(document.getElementById('revenueChart'), {
    type:'bar',
    data:{
        labels: @json($reportData['chart_labels']),
        datasets:[
            {
                type:'bar',
                label:'{{ app()->getLocale()=="km" ? "ការបញ្ជាទិញ" : "Orders" }}',
                data:@json($reportData['chart_orders']),
                backgroundColor:'rgba(42,120,214,0.25)',
                borderRadius:4, barThickness:20, yAxisID:'y1'
            },
            {
                type:'line',
                label:'{{ app()->getLocale()=="km" ? "ចំណូល" : "Revenue" }}',
                data:@json($reportData['chart_revenue']),
                borderColor:'#eb6834', borderWidth:2,
                pointRadius:3, pointBackgroundColor:'#eb6834',
                tension:0.4, fill:false, yAxisID:'y2'
            }
        ]
    },
    options:{
        responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false } },
        scales:{
            x:{ ticks:{font:{size:11}}, grid:{display:false} },
            y1:{ position:'left',  ticks:{font:{size:11},stepSize:1}, grid:{color:'#f3f4f6'} },
            y2:{ position:'right', ticks:{font:{size:11},callback:v=>'$'+v.toLocaleString()}, grid:{display:false} }
        }
    }
});
</script>
@endpush


{{-- ════════════════════════════════════════════════════════ --}}
{{--                  PRODUCTS REPORT                        --}}
{{-- ════════════════════════════════════════════════════════ --}}
@elseif($reportType === 'products')

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'ផលិតផលសរុប' : 'Total Products' }}</div>
            <div class="summary-val" style="color:#111;">{{ number_format($reportData['total']) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'សកម្ម' : 'Active' }}</div>
            <div class="summary-val" style="color:#111;">{{ number_format($reportData['active']) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'អស់ស្ទុក' : 'Out of Stock' }}</div>
            <div class="summary-val" style="color:#111;">{{ number_format($reportData['out_of_stock']) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'ស្ទុកតិច' : 'Low Stock' }}</div>
            <div class="summary-val" style="color:#111;">{{ number_format($reportData['low_stock']) }}</div>
        </div>
    </div>
</div>

<div class="report-card">
    <div class="report-head">
        <h6> {{ app()->getLocale()=='km' ? 'ផលិតផលទាំងអស់' : 'All Products' }} ({{ $reportData['total'] }})</h6>
        <span style="font-size:12px;color:#6b7280;">
            {{ app()->getLocale()=='km' ? 'តម្លៃស្ទុកសរុប' : 'Total Stock Value' }}:
            <strong style="color:#374151;">${{ number_format($reportData['total_value'], 2) }}</strong>
        </span>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">#</th>
                    <th>{{ app()->getLocale()=='km' ? 'ផលិតផល' : 'Product' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ប្រភេទ' : 'Category' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ម៉ាក' : 'Brand' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'តម្លៃ' : 'Price' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ស្ទុក' : 'Stock' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status' }}</th>
                    <th style="padding-right:20px;text-align:right;">{{ app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Created' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData['items'] as $i => $p)
                <tr>
                    <td style="padding-left:20px;color:#9ca3af;font-size:12px;">{{ $i+1 }}</td>
                    <td>
                        <div style="font-weight:600;font-size:13px;color:#111;">{{ $p->name }}</div>
                        <div style="color:#9ca3af;font-size:11px;">{{ $p->sku ?? '' }}</div>
                    </td>
                    <td style="font-size:12px;color:#6b7280;">{{ $p->category->name ?? '—' }}</td>
                    <td style="font-size:12px;color:#6b7280;">{{ $p->brand->name ?? '—' }}</td>
                    <td style="font-weight:600;color:#374151;">${{ number_format($p->price, 2) }}</td>
                    <td><span style="font-weight:700;font-size:13px;color:#374151;">{{ $p->stock }}</span></td>
                    <td>
                        @if($p->is_active)
                            <span class="pill pill-delivered">{{ app()->getLocale()=='km' ? 'សកម្ម' : 'Active' }}</span>
                        @else
                            <span class="pill pill-cancelled">{{ app()->getLocale()=='km' ? 'បិទ' : 'Inactive' }}</span>
                        @endif
                    </td>
                    <td style="padding-right:20px;text-align:right;font-size:12px;color:#9ca3af;">
                        {{ $p->created_at->format('M d, Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#9ca3af;">
                        {{ app()->getLocale()=='km' ? 'មិនមានផលិតផល' : 'No products found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


{{-- ════════════════════════════════════════════════════════ --}}
{{--                 CATEGORIES REPORT                       --}}
{{-- ════════════════════════════════════════════════════════ --}}
@elseif($reportType === 'categories')

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'ប្រភេទសរុប' : 'Total Categories' }}</div>
            <div class="summary-val" style="color:#111;">{{ number_format($reportData['total']) }}</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'សកម្ម' : 'Active' }}</div>
            <div class="summary-val" style="color:#111;">{{ number_format($reportData['active']) }}</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'មានផលិតផល' : 'Has Products' }}</div>
            <div class="summary-val" style="color:#111;">{{ number_format($reportData['with_products']) }}</div>
        </div>
    </div>
</div>

<div class="report-card">
    <div class="report-head">
        <h6> {{ app()->getLocale()=='km' ? 'ប្រភេទទាំងអស់' : 'All Categories' }} ({{ $reportData['total'] }})</h6>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">#</th>
                    <th>{{ app()->getLocale()=='km' ? 'ឈ្មោះ' : 'Name' }}</th>

                    <th>{{ app()->getLocale()=='km' ? 'ផលិតផល' : 'Products' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'លំដាប់' : 'Sort Order' }}</th>
                    <th style="padding-right:20px;text-align:right;">{{ app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData['items'] as $i => $c)
                <tr>
                    <td style="padding-left:20px;color:#9ca3af;font-size:12px;">{{ $i+1 }}</td>
                    <td style="font-weight:600;font-size:13px;color:#111;">{{ $c->name }}</td>

                    <td>
                        <span style="font-weight:600;color:#374151;">{{ $c->products_count }}</span>
                        <span style="color:#9ca3af;font-size:11px;">{{ app()->getLocale()=='km' ? 'មុខ' : 'items' }}</span>
                    </td>
                    <td style="font-size:13px;color:#374151;">{{ $c->sort_order ?? 0 }}</td>
                    <td style="padding-right:20px;text-align:right;">
                        @if($c->is_active ?? true)
                            <span class="pill pill-delivered">{{ app()->getLocale()=='km' ? 'សកម្ម' : 'Active' }}</span>
                        @else
                            <span class="pill pill-cancelled">{{ app()->getLocale()=='km' ? 'បិទ' : 'Inactive' }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                        {{ app()->getLocale()=='km' ? 'មិនមានប្រភេទ' : 'No categories found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


{{-- ════════════════════════════════════════════════════════ --}}
{{--                    BRANDS REPORT                        --}}
{{-- ════════════════════════════════════════════════════════ --}}
@elseif($reportType === 'brands')

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'ម៉ាកសរុប' : 'Total Brands' }}</div>
            <div class="summary-val" style="color:#111;">{{ number_format($reportData['total']) }}</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'សកម្ម' : 'Active' }}</div>
            <div class="summary-val" style="color:#111;">{{ number_format($reportData['active']) }}</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'អសកម្ម' : 'Inactive' }}</div>
            <div class="summary-val" style="color:#111;">{{ $reportData['total'] - $reportData['active'] }}</div>
        </div>
    </div>
</div>

<div class="report-card">
    <div class="report-head">
        <h6> {{ app()->getLocale()=='km' ? 'ម៉ាកទាំងអស់' : 'All Brands' }} ({{ $reportData['total'] }})</h6>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">#</th>
                    <th>{{ app()->getLocale()=='km' ? 'ម៉ាក' : 'Brand' }}</th>

                    <th>{{ app()->getLocale()=='km' ? 'ផលិតផល' : 'Products' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ការពិពណ៌នា' : 'Description' }}</th>
                    <th style="padding-right:20px;text-align:right;">{{ app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData['items'] as $i => $b)
                <tr>
                    <td style="padding-left:20px;color:#9ca3af;font-size:12px;">{{ $i+1 }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            @if($b->logo_url ?? null)
                                <img src="{{ $b->logo_url }}" style="width:32px;height:32px;object-fit:contain;border-radius:6px;border:1px solid #e5e7eb;" alt="">
                            @else
                                <div style="width:32px;height:32px;background:#f3f4f6;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:14px;"></div>
                            @endif
                            <span style="font-weight:600;font-size:13px;color:#111;">{{ $b->name }}</span>
                        </div>
                    </td>

                    <td>
                        <span style="font-weight:600;color:#374151;">{{ $b->products_count }}</span>
                        <span style="color:#9ca3af;font-size:11px;">{{ app()->getLocale()=='km' ? 'មុខ' : 'items' }}</span>
                    </td>
                    <td style="font-size:12px;color:#6b7280;">{{ \Illuminate\Support\Str::limit($b->description ?? '—', 40) }}</td>
                    <td style="padding-right:20px;text-align:right;">
                        @if($b->is_active)
                            <span class="pill pill-delivered">{{ app()->getLocale()=='km' ? 'សកម្ម' : 'Active' }}</span>
                        @else
                            <span class="pill pill-cancelled">{{ app()->getLocale()=='km' ? 'បិទ' : 'Inactive' }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                        {{ app()->getLocale()=='km' ? 'មិនមានម៉ាក' : 'No brands found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


{{-- ════════════════════════════════════════════════════════ --}}
{{--                    USERS REPORT                         --}}
{{-- ════════════════════════════════════════════════════════ --}}
@elseif($reportType === 'users')

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'អ្នកប្រើសរុប' : 'Total Users' }}</div>
            <div class="summary-val" style="color:#111;">{{ number_format($reportData['total']) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'អតិថិជន' : 'Customers' }}</div>
            <div class="summary-val" style="color:#111;">{{ number_format($reportData['customers']) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'អ្នកគ្រប់គ្រង' : 'Admins' }}</div>
            <div class="summary-val" style="color:#111;">{{ number_format($reportData['admins']) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="summary-card">
            <div class="summary-lbl">{{ app()->getLocale()=='km' ? 'អសកម្ម' : 'Inactive' }}</div>
            <div class="summary-val" style="color:#111;">{{ number_format($reportData['inactive']) }}</div>
        </div>
    </div>
</div>

<div class="report-card">
    <div class="report-head">
        <h6> {{ app()->getLocale()=='km' ? 'អ្នកប្រើទាំងអស់' : 'All Users' }} ({{ $reportData['total'] }})</h6>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">#</th>
                    <th>{{ app()->getLocale()=='km' ? 'អ្នកប្រើ' : 'User' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'អ៊ីមែល' : 'Email' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'តួនាទី' : 'Role' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status' }}</th>
                    <th style="padding-right:20px;text-align:right;">{{ app()->getLocale()=='km' ? 'ចូលរួម' : 'Joined' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData['items'] as $i => $u)
                @php
                    $roleColor = match($u->role ?? 'Customer'){
                        'Admin'  => '#dc2626',
                        'Staff'  => '#7c3aed',
                        default  => '#2563eb',
                    };
                @endphp
                <tr>
                    <td style="padding-left:20px;color:#9ca3af;font-size:12px;">{{ $i+1 }}</td>
                    <td>
                        <div style="font-weight:600;font-size:13px;color:#111;">{{ $u->name }}</div>
                        @if($u->phone ?? null)
                        <div style="color:#9ca3af;font-size:11px;">{{ $u->phone }}</div>
                        @endif
                    </td>
                    <td style="font-size:12px;color:#6b7280;">{{ $u->email }}</td>
                    <td>
                        <span style="font-size:11px;font-weight:700;color:{{ $roleColor }};background:{{ $roleColor }}18;padding:3px 8px;border-radius:20px;">
                            {{ $u->role ?? 'Customer' }}
                        </span>
                    </td>
                    <td>
                        @if($u->is_active)
                            <span class="pill pill-delivered">{{ app()->getLocale()=='km' ? 'សកម្ម' : 'Active' }}</span>
                        @else
                            <span class="pill pill-cancelled">{{ app()->getLocale()=='km' ? 'ហាមឃាត់' : 'Banned' }}</span>
                        @endif
                    </td>
                    <td style="padding-right:20px;text-align:right;font-size:12px;color:#9ca3af;">
                        {{ $u->created_at->format('M d, Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                        {{ app()->getLocale()=='km' ? 'មិនមានអ្នកប្រើ' : 'No users found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


{{-- ════════════════════════════════════════════════════════ --}}
{{--                    STOCK REPORT                          --}}
{{-- ════════════════════════════════════════════════════════ --}}
@elseif($reportType === 'stock')

@php
    $stockSub = $reportData['stock_sub'] ?? request('stock_type', '');
    $stockSubLabel = match($stockSub) {
        'restock'  => (app()->getLocale()=='km' ? 'បន្ថែមស្តុក' : 'Add Stock'),
        'damaged'  => (app()->getLocale()=='km' ? 'ខូចខាត'       : 'Damage'),
        'returned' => (app()->getLocale()=='km' ? 'ត្រឡប់មក'     : 'Return'),

    };
@endphp



{{-- ════════════════════════════════════════════════════════ --}}
{{-- Product inventory table (never reached via dropdown     --}}
{{-- since '' now means Total-All; kept for direct URL use)  --}}
{{-- ════════════════════════════════════════════════════════ --}}
@if($stockSub === '__inventory__')

<div class="report-card">
    <div class="report-head">
        <h6>📦 {{ app()->getLocale()=='km' ? 'ផលិតផលទាំងអស់' : 'All Products' }} ({{ number_format($reportData['total_products'] ?? 0) }})</h6>
        <span style="font-size:12px;color:#6b7280;">
            {{ app()->getLocale()=='km' ? 'តម្លៃស្ទុករួម' : 'Total Stock Value' }}:
            <strong style="color:#374151;">${{ number_format($reportData['total_value'] ?? 0, 2) }}</strong>
            &nbsp;|&nbsp;
            <span style="color:#ef4444;">{{ app()->getLocale()=='km' ? 'អស់ស្ទុក' : 'Out of stock' }}: <strong>{{ $reportData['out_of_stock'] ?? 0 }}</strong></span>
            &nbsp;|&nbsp;
            <span style="color:#f59e0b;">{{ app()->getLocale()=='km' ? 'ស្ទុកតិច' : 'Low stock' }}: <strong>{{ $reportData['low_stock'] ?? 0 }}</strong></span>
        </span>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">#</th>
                    <th>{{ app()->getLocale()=='km' ? 'ផលិតផល' : 'Product' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ប្រភេទ' : 'Category' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ម៉ាក' : 'Brand' }}</th>
                    <th style="text-align:right;">{{ app()->getLocale()=='km' ? 'តម្លៃ' : 'Price' }}</th>
                    <th style="text-align:center;">{{ app()->getLocale()=='km' ? 'ស្ទុក' : 'Stock' }}</th>
                    <th style="padding-right:20px;text-align:right;">{{ app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData['products'] ?? [] as $i => $p)
                <tr>
                    <td style="padding-left:20px;color:#9ca3af;font-size:12px;">{{ $i + 1 }}</td>
                    <td>
                        <div style="font-weight:600;font-size:13px;color:#111;">{{ $p->name }}</div>
                        @if($p->sku)
                        <div style="color:#9ca3af;font-size:11px;">{{ $p->sku }}</div>
                        @endif
                    </td>
                    <td style="font-size:12px;color:#6b7280;">{{ $p->category->name ?? '—' }}</td>
                    <td style="font-size:12px;color:#6b7280;">{{ $p->brand->name ?? '—' }}</td>
                    <td style="text-align:right;font-weight:600;color:#374151;">${{ number_format($p->price, 2) }}</td>
                    <td style="text-align:center;">
                        <span style="font-weight:700;font-size:14px;color:#374151;">{{ $p->stock }}</span>
                    </td>
                    <td style="padding-right:20px;text-align:right;">
                        @if($p->is_active)
                            <span class="pill pill-delivered">{{ app()->getLocale()=='km' ? 'សកម្ម' : 'Active' }}</span>
                        @else
                            <span class="pill pill-cancelled">{{ app()->getLocale()=='km' ? 'បិទ' : 'Inactive' }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                        {{ app()->getLocale()=='km' ? 'មិនមានផលិតផល' : 'No products found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════ --}}
{{-- Add Stock / Damage / Return → History table             --}}
{{-- ════════════════════════════════════════════════════════ --}}
@else

<div class="report-card">
    <div class="report-head">
        <h6>
            @if($stockSub === '' || $stockSub === null)
                <span style="color:#6366f1;">📋 {{ app()->getLocale()=='km' ? 'ប្រវត្តិស្ទុកទាំងអស់' : 'Total Stock History (All)' }}</span>
            @elseif($stockSub === 'restock')
                <span style="color:#16a34a;">＋ {{ app()->getLocale()=='km' ? 'ប្រវត្តិបន្ថែមស្តុក' : 'Add Stock History' }}</span>
            @elseif($stockSub === 'damaged')
                <span style="color:#dc2626;">🔴 {{ app()->getLocale()=='km' ? 'ប្រវត្តិខូចខាត' : 'Damage History' }}</span>
            @elseif($stockSub === 'returned')
                <span style="color:#2563eb;">↩ {{ app()->getLocale()=='km' ? 'ប្រវត្តិត្រឡប់មក' : 'Return History' }}</span>
            @endif
        </h6>
        <span style="font-size:12px;color:#9ca3af;">{{ count($reportData['items'] ?? []) }} {{ app()->getLocale()=='km' ? 'កំណត់ត្រា' : 'records' }}</span>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">{{ app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ផលិតផល' : 'Product' }}</th>
                    <th style="text-align:center;">{{ app()->getLocale()=='km' ? 'ប្រភេទ' : 'Type' }}</th>
                    <th style="text-align:center;">{{ app()->getLocale()=='km' ? 'មុន' : 'Before' }}</th>
                    <th style="text-align:center;">{{ app()->getLocale()=='km' ? 'ការផ្លាស់ប្តូរ' : 'Change' }}</th>
                    <th style="text-align:center;">{{ app()->getLocale()=='km' ? 'ក្រោយ' : 'After' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ដោយ' : 'By' }}</th>
                    <th style="padding-right:20px;">{{ app()->getLocale()=='km' ? 'កំណត់ចំណាំ' : 'Note' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData['items'] ?? [] as $h)
                @php
                    $isAdd       = ($h->stock_after >= $h->stock_before);
                    $diff        = abs($h->stock_after - $h->stock_before);
                    $change      = $isAdd ? '+' . $diff : '−' . $diff;
                    $reason = $h->reason
                        ?? ($h->adj_type === 'add'      ? 'restock'
                          : ($h->adj_type === 'subtract' ? 'damaged'
                          : ($isAdd                      ? 'restock' : 'damaged')));
                    $typeLabel = match($reason) {
                        'damaged'  => ['icon'=>'🔴', 'label'=> app()->getLocale()=='km' ? 'ខូចខាត'       : 'Damage',    'color'=>'#dc2626', 'bg'=>'#fee2e2'],
                        'returned' => ['icon'=>'↩',  'label'=> app()->getLocale()=='km' ? 'ត្រឡប់មក'     : 'Return',    'color'=>'#2563eb', 'bg'=>'#dbeafe'],
                        default    => ['icon'=>'＋',  'label'=> app()->getLocale()=='km' ? 'បន្ថែមស្តុក' : 'Add Stock', 'color'=>'#16a34a', 'bg'=>'#dcfce7'],
                    };
                @endphp
                <tr>
                    <td style="padding-left:20px;font-size:12px;color:#6b7280;">
                        {{ $h->created_at->format('d M Y') }}<br>
                        <span style="font-size:11px;color:#9ca3af;">{{ $h->created_at->format('h:i A') }}</span>
                    </td>
                    <td style="font-weight:600;font-size:13px;color:#111;">{{ \Illuminate\Support\Str::limit($h->product->name ?? '—', 28) }}</td>
                    <td style="text-align:center;">
                        <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:{{ $typeLabel['bg'] }};color:{{ $typeLabel['color'] }};">
                            {{ $typeLabel['icon'] }} {{ $typeLabel['label'] }}
                        </span>
                    </td>
                    <td style="text-align:center;font-size:13px;color:#6b7280;font-weight:600;">{{ $h->stock_before }}</td>
                    <td style="text-align:center;">
                        <span style="font-weight:700;color:#374151;font-size:14px;">{{ $change }}</span>
                    </td>
                    <td style="text-align:center;font-weight:700;font-size:14px;color:#111;">{{ $h->stock_after }}</td>
                    <td style="font-size:13px;color:#374151;">{{ $h->addedBy->name ?? '—' }}</td>
                    <td style="padding-right:20px;font-size:12px;color:#9ca3af;">{{ $h->note ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#9ca3af;">
                        {{ app()->getLocale()=='km' ? 'មិនទាន់មានប្រវត្តិ' : 'No history for this period.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endif
{{-- end stock sub-type --}}

@endif
{{-- end @if reportType --}}

@endif
{{-- end @if isset(reportData) --}}


@endsection