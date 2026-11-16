@extends('layouts.admin')
@section('title', app()->getLocale()=='km' ? 'ថែទាំស្តុក' : 'Stock Maintenance')

@section('content')
<style>
.maint-card { background:#fff; border-radius:12px; border:1px solid #e5e7eb; overflow:hidden; margin-bottom:24px; }
.maint-head { padding:16px 20px; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; justify-content:space-between; }
.maint-head h6 { margin:0; font-size:13px; font-weight:700; color:#111; }

.type-select {
    appearance: none;
    -webkit-appearance: none;
    width: 140px;
    padding: 7px 32px 7px 12px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 8px;
    border: 1.5px solid #e5e7eb;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 10px center;
    cursor: pointer;
    transition: .15s;
    color: #374151;
}
.type-select:focus { outline: none; border-color: #6366f1; }
.type-select.opt-add    { border-color: #16a34a; color: #16a34a; background-color: #f0fdf4; }
.type-select.opt-damage { border-color: #dc2626; color: #dc2626; background-color: #fef2f2; }
.type-select.opt-return { border-color: #2563eb; color: #2563eb; background-color: #eff6ff; }
</style>

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.stock') }}" class="qa-btn">
            ← {{ app()->getLocale()=='km' ? 'ត្រឡប់' : 'Back' }}
        </a>
        <h5 class="mb-0 fw-bold" style="font-size:16px;">
            🔧 {{ app()->getLocale()=='km' ? 'ថែទាំស្តុក' : 'Stock Maintenance' }}
        </h5>
    </div>
    <a href="{{ route('admin.stock.history') }}" class="qa-btn">
        {{ app()->getLocale()=='km' ? 'មើលប្រវត្តិ' : 'View History' }}
    </a>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;margin-bottom:6px;">{{ app()->getLocale()=='km' ? 'ផលិតផលសរុប' : 'Total Products' }}</div>
            <div style="font-size:28px;font-weight:700;color:#111;">{{ $products->count() }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;margin-bottom:6px;">{{ app()->getLocale()=='km' ? 'ស្តុកសរុប' : 'Total Units' }}</div>
            <div style="font-size:28px;font-weight:700;color:#111;">{{ number_format($products->sum('stock')) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;margin-bottom:6px;">{{ app()->getLocale()=='km' ? 'ស្តុកទាប' : 'Low Stock' }}</div>
            <div style="font-size:28px;font-weight:700;color:#111;">{{ $products->whereBetween('stock',[1,9])->count() }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;margin-bottom:6px;">{{ app()->getLocale()=='km' ? 'អស់ស្តុក' : 'Out of Stock' }}</div>
            <div style="font-size:28px;font-weight:700;color:#111;">{{ $products->where('stock',0)->count() }}</div>
        </div>
    </div>
</div>

{{-- Search / Filter --}}
<form method="GET" action="{{ route('admin.stock.maintenance') }}" class="panel p-3 mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <input type="text" name="search" class="form-ctrl" style="width:260px;"
               placeholder="{{ app()->getLocale()=='km' ? 'ស្វែងរក...' : 'Search products...' }}"
               value="{{ request('search') }}"/>
        @foreach(['all'=>(app()->getLocale()=='km'?'ទាំងអស់':'All'),'out'=>(app()->getLocale()=='km'?'អស់ស្តុក':'Out of Stock'),'low'=>(app()->getLocale()=='km'?'ស្តុកទាប':'Low Stock'),'ok'=>(app()->getLocale()=='km'?'មានស្តុក':'In Stock')] as $val=>$lbl)
            <button type="submit" name="filter" value="{{ $val }}"
                    class="qa-btn {{ ($filter??'all')==$val ? 'qa-primary' : '' }}"
                    style="font-size:12px;padding:7px 14px;">{{ $lbl }}</button>
        @endforeach
    </div>
</form>

{{-- Products Table --}}
<div class="maint-card">
    <div class="maint-head">
        <h6>{{ app()->getLocale()=='km' ? 'ផលិតផល' : 'Products' }} ({{ $products->count() }})</h6>
        <span style="font-size:12px;color:#9ca3af;">
            {{ app()->getLocale()=='km' ? 'ជ្រើសប្រភេទ បន្ទាប់មកបញ្ចូលបរិមាណ រួចចុចរក្សាទុក' : 'Select type → enter qty → save' }}
        </span>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">{{ app()->getLocale()=='km' ? 'ផលិតផល' : 'Product' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ប្រភេទ' : 'Category' }}</th>
                    <th style="text-align:center;">{{ app()->getLocale()=='km' ? 'ស្តុក' : 'Stock' }}</th>
                    <th style="text-align:center;">{{ app()->getLocale()=='km' ? 'ប្រភេទសកម្មភាព' : 'Action Type' }}</th>
                    <th style="text-align:center;min-width:100px;">{{ app()->getLocale()=='km' ? 'បរិមាណ' : 'Qty' }}</th>
                    <th style="min-width:160px;">{{ app()->getLocale()=='km' ? 'កំណត់ចំណាំ' : 'Note' }}</th>
                    <th style="padding-right:20px;text-align:center;">{{ app()->getLocale()=='km' ? 'រក្សាទុក' : 'Save' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr id="row-{{ $product->id }}">

                    {{-- Product --}}
                    <td style="padding-left:20px;">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $product->image_url ?? '' }}"
                                 onerror="this.style.background='#f3f4f6'"
                                 style="width:40px;height:40px;object-fit:contain;border-radius:8px;background:#f9fafb;padding:3px;flex-shrink:0;border:1px solid #e5e7eb;"/>
                            <div>
                                <div style="font-weight:600;font-size:13px;color:#111;">{{ Str::limit($product->name,28) }}</div>
                                <div style="font-size:11px;color:#9ca3af;">{{ $product->sku ?? '' }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Category --}}
                    <td style="font-size:12px;color:#6b7280;">{{ $product->category->name ?? '—' }}</td>

                    {{-- Stock --}}
                    <td style="text-align:center;">
                        <div style="font-size:18px;font-weight:700;color:#374151;" id="stock-{{ $product->id }}">
                            {{ $product->stock }}
                        </div>
                        @if($product->stock==0)
                            <span class="pill pill-cancelled" style="font-size:10px;">{{ app()->getLocale()=='km'?'អស់':'Out' }}</span>
                        @elseif($product->stock<10)
                            <span class="pill pill-pending" style="font-size:10px;">{{ app()->getLocale()=='km'?'ទាប':'Low' }}</span>
                        @endif
                    </td>

                    {{-- Action Type Dropdown + hidden inputs --}}
                    <td style="text-align:center;">
                        <form action="{{ route('admin.stock.adjust', $product->id) }}" method="POST" id="form-{{ $product->id }}">
                        @csrf
                        <input type="hidden" name="adj_type" id="adjtype-{{ $product->id }}" value="add"/>
                        <input type="hidden" name="reason"   id="reason-{{ $product->id }}"  value="restock"/>
                        <select class="type-select opt-add" id="typesel-{{ $product->id }}"
                                onchange="onTypeChange({{ $product->id }}, this)">
                            <option value="add|restock">＋ {{ app()->getLocale()=='km' ? 'បន្ថែម (Add)' : 'Add Stock' }}</option>
                            <option value="subtract|damaged"> {{ app()->getLocale()=='km' ? 'ខូច (Damage)' : 'Damage' }}</option>
                            <option value="subtract|returned">↩ {{ app()->getLocale()=='km' ? 'ត្រឡប់ (Return)' : 'Return' }}</option>
                        </select>
                    </td>

                    {{-- Quantity --}}
                    <td style="text-align:center;">
                        <input type="number" name="quantity" id="qty-{{ $product->id }}"
                               class="form-ctrl" style="width:80px;text-align:center;"
                               min="1" max="9999" placeholder="0" required
                               oninput="updatePreview({{ $product->id }},{{ $product->stock }},this.value)"/>
                        <div style="font-size:11px;margin-top:4px;font-weight:600;" id="preview-{{ $product->id }}"></div>
                    </td>

                    {{-- Note --}}
                    <td>
                        <input type="text" name="note" class="form-ctrl"
                               style="font-size:12px;"
                               placeholder="{{ app()->getLocale()=='km' ? 'កំណត់ចំណាំ...' : 'Note (optional)' }}"
                               maxlength="200"/>
                    </td>

                    {{-- Save --}}
                    <td style="padding-right:20px;text-align:center;">
                        <button type="submit" class="qa-btn qa-primary" style="font-size:12px;padding:7px 18px;">
                            {{ app()->getLocale()=='km' ? 'រក្សាទុក' : 'Save' }}
                        </button>
                        </form>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                        {{ app()->getLocale()=='km' ? 'រកមិនឃើញផលិតផល' : 'No products found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Recent Log --}}
<div class="maint-card">
    <div class="maint-head">
        <h6> {{ app()->getLocale()=='km' ? 'ប្រវត្តិថែទាំ' : 'Maintenance Log' }}</h6>
        <a href="{{ route('admin.stock.history') }}" style="font-size:12px;color:#6366f1;">
            {{ app()->getLocale()=='km' ? 'មើលទាំងអស់' : 'View All' }} →
        </a>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">{{ app()->getLocale()=='km' ? 'ផលិតផល' : 'Product' }}</th>
                    <th style="text-align:center;">{{ app()->getLocale()=='km' ? 'ប្រភេទ' : 'Type' }}</th>
                    <th style="text-align:center;">{{ app()->getLocale()=='km' ? 'មុន' : 'Before' }}</th>
                    <th style="text-align:center;">{{ app()->getLocale()=='km' ? 'ការផ្លាស់ប្តូរ' : 'Change' }}</th>
                    <th style="text-align:center;">{{ app()->getLocale()=='km' ? 'ក្រោយ' : 'After' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ដោយ' : 'By' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'កំណត់ចំណាំ' : 'Note' }}</th>
                    <th style="padding-right:20px;">{{ app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLog as $h)
                @php
                    $isAdd       = ($h->stock_after >= $h->stock_before);
                    $diff        = abs($h->stock_after - $h->stock_before);
                    $change      = $isAdd ? '+' . $diff : '−' . $diff;

                    $reason = $h->reason
                        ?? ($h->adj_type === 'add'      ? 'restock'
                          : ($h->adj_type === 'subtract' ? 'damaged'
                          : ($isAdd                      ? 'restock' : 'damaged')));

                    $typeLabel = match($reason) {
                        'damaged'  => ['icon'=>'', 'label'=> app()->getLocale()=='km' ? 'ខូចខាត'   : 'Damage', 'color'=>'#dc2626', 'bg'=>'#fee2e2'],
                        'returned' => ['icon'=>'↩',  'label'=> app()->getLocale()=='km' ? 'ត្រឡប់មក' : 'Return', 'color'=>'#2563eb', 'bg'=>'#dbeafe'],
                        default    => ['icon'=>'＋',  'label'=> app()->getLocale()=='km' ? 'បន្ថែម'   : 'Add',    'color'=>'#16a34a', 'bg'=>'#dcfce7'],
                    };
                @endphp
                <tr>
                    <td style="padding-left:20px;font-weight:600;font-size:13px;color:#111;">
                        {{ Str::limit($h->product->name ?? '—', 24) }}
                    </td>
                    <td style="text-align:center;">
                        <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;background:{{ $typeLabel['bg'] }};color:{{ $typeLabel['color'] }};">
                            {{ $typeLabel['icon'] }} {{ $typeLabel['label'] }}
                        </span>
                    </td>
                    <td style="text-align:center;font-size:13px;color:#6b7280;">{{ $h->stock_before }}</td>
                    <td style="text-align:center;font-size:14px;font-weight:700;color:#374151;">{{ $change }}</td>
                    <td style="text-align:center;font-size:13px;font-weight:600;color:#111;">{{ $h->stock_after }}</td>
                    <td style="font-size:12px;color:#374151;">{{ $h->addedBy->name ?? '—' }}</td>
                    <td style="font-size:12px;color:#9ca3af;">{{ $h->note ?? '—' }}</td>
                    <td style="padding-right:20px;font-size:12px;color:#9ca3af;">
                        {{ $h->created_at->format('d M Y') }}<br>
                        <span style="font-size:11px;">{{ $h->created_at->format('h:i A') }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:32px;color:#9ca3af;">
                        {{ app()->getLocale()=='km' ? 'មិនទាន់មានប្រវត្តិ' : 'No log yet.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
function onTypeChange(id, sel) {
    var parts   = sel.value.split('|');
    var adjType = parts[0];
    var reason  = parts[1];

    document.getElementById('adjtype-' + id).value = adjType;
    document.getElementById('reason-'  + id).value = reason;

    sel.className = 'type-select';
    if (reason === 'restock')  sel.classList.add('opt-add');
    if (reason === 'damaged')  sel.classList.add('opt-damage');
    if (reason === 'returned') sel.classList.add('opt-return');

    var qtyEl   = document.getElementById('qty-' + id);
    var stockEl = document.getElementById('stock-' + id);
    var stock   = parseInt(stockEl ? stockEl.textContent : 0) || 0;
    if (qtyEl && qtyEl.value) updatePreview(id, stock, qtyEl.value);
}

function updatePreview(id, current, val) {
    var qty     = parseInt(val) || 0;
    var adjType = document.getElementById('adjtype-' + id).value;
    var result  = adjType === 'add' ? current + qty : current - qty;
    var el      = document.getElementById('preview-' + id);
    if (!el) return;
    el.textContent = '→ ' + result;
    el.style.color = result <= 0 ? '#ef4444' : (result < 10 ? '#f59e0b' : '#10b981');
}
</script>
@endpush
@endsection