@extends('layouts.admin')
@section('title', app()->getLocale()=='km' ? 'ប្រវត្តិស្តុក' : 'Stock History')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.stock') }}" class="qa-btn">← {{ app()->getLocale()=='km' ? 'ត្រឡប់ទៅស្តុក' : 'Back to Stock' }}</a>
    <h5 class="mb-0 fw-bold" style="font-size:16px;">{{ app()->getLocale()=='km' ? 'ប្រវត្តិស្តុក' : 'Stock History' }}</h5>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.stock.history') }}" class="panel p-3 mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center">

        {{-- Product filter --}}
        <select name="product_id" class="form-ctrl" style="width:220px;" onchange="this.form.submit()">
            <option value="">{{ app()->getLocale()=='km' ? 'ផលិតផលទាំងអស់' : 'All Products' }}</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}" {{ request('product_id')==$product->id ? 'selected' : '' }}>{{ $product->name }}</option>
            @endforeach
        </select>

        {{-- Type filter --}}
        <select name="type_filter" class="form-ctrl" style="width:160px;" onchange="this.form.submit()">
            <option value="">{{ app()->getLocale()=='km' ? 'ប្រភេទទាំងអស់' : 'All Types' }}</option>
            <option value="restock"  {{ request('type_filter')=='restock'  ? 'selected' : '' }}>＋ {{ app()->getLocale()=='km' ? 'បន្ថែម' : 'Add' }}</option>
            <option value="damaged"  {{ request('type_filter')=='damaged'  ? 'selected' : '' }}> {{ app()->getLocale()=='km' ? 'ខូចខាត' : 'Damage' }}</option>
            <option value="returned" {{ request('type_filter')=='returned' ? 'selected' : '' }}>↩ {{ app()->getLocale()=='km' ? 'ត្រឡប់មក' : 'Return' }}</option>
        </select>

        {{-- Date range --}}
        <input type="date" name="date_from" class="form-ctrl" style="width:150px;" value="{{ request('date_from') }}" onchange="this.form.submit()"/>
        <input type="date" name="date_to"   class="form-ctrl" style="width:150px;" value="{{ request('date_to') }}"   onchange="this.form.submit()"/>

        @if(request('product_id') || request('type_filter') || request('date_from') || request('date_to'))
            <a href="{{ route('admin.stock.history') }}" class="qa-btn">✕ {{ app()->getLocale()=='km' ? 'លប់ចោល' : 'Clear' }}</a>
        @endif

        {{-- Export buttons --}}
        <a href="{{ route('admin.stock.history.export.excel', request()->query()) }}"
           class="qa-btn" style="border-color:#16a34a;color:#16a34a;">
            ⬇ Excel
        </a>
        <a href="{{ route('admin.stock.history.export.pdf', request()->query()) }}"
           target="_blank"
           class="qa-btn" style="border-color:#dc2626;color:#dc2626;">
            ⬇ PDF
        </a>
    </div>
</form>

<div class="panel">
    <div class="panel-head">
        <h6>{{ app()->getLocale()=='km' ? 'ការផ្លាស់ប្តូរស្តុក' : 'Stock Changes' }} ({{ $histories->total() }})</h6>
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
                    <th>{{ app()->getLocale()=='km' ? 'កំណត់ចំណាំ' : 'Note' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($histories as $h)
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
                    <td style="padding-left:20px;font-size:12px;color:#6b7280;">
                        {{ $h->created_at->format('d M Y') }}<br>
                        <span style="font-size:11px;color:#9ca3af;">{{ $h->created_at->format('h:i A') }}</span>
                    </td>
                    <td style="font-weight:600;font-size:13px;color:#111;">{{ Str::limit($h->product->name ?? '—', 28) }}</td>
                    <td style="text-align:center;">
                        <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;background:{{ $typeLabel['bg'] }};color:{{ $typeLabel['color'] }};">
                            {{ $typeLabel['icon'] }} {{ $typeLabel['label'] }}
                        </span>
                    </td>
                    <td style="text-align:center;font-size:13px;color:#6b7280;">{{ $h->stock_before }}</td>
                    <td style="text-align:center;">
                        <span style="font-weight:700;color:#374151;font-size:14px;">{{ $change }}</span>
                    </td>
                    <td style="text-align:center;font-weight:600;font-size:13px;color:#111;">{{ $h->stock_after }}</td>
                    <td style="font-size:13px;color:#374151;">{{ $h->addedBy->name ?? '—' }}</td>
                    <td style="font-size:12px;color:#9ca3af;">{{ $h->note ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#9ca3af;">
                        {{ app()->getLocale()=='km' ? 'មិនទាន់មានប្រវត្តិ' : 'No stock history.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($histories->hasPages())
    <div class="p-3 border-top" style="border-color:#f3f4f6!important;">
        {{ $histories->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection