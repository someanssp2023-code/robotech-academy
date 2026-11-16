@extends('layouts.admin')
@section('title', app()->getLocale()=='km' ? 'គ្រប់គ្រងស្តុក' : 'Manage Stock')

@section('content')

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="stat-lbl">{{ app()->getLocale()=='km' ? 'ស្តុកសរុប' : 'Total Units in Stock' }}</div>
            <div class="stat-val">{{ number_format($totalStock) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="stat-lbl">{{ app()->getLocale()=='km' ? 'ស្តុកទាប (< 10)' : 'Low Stock (< 10)' }}</div>
            <div class="stat-val">{{ $lowStock }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="stat-lbl">{{ app()->getLocale()=='km' ? 'អស់ស្តុក' : 'Out of Stock' }}</div>
            <div class="stat-val">{{ $outOfStock }}</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.stock') }}" class="panel p-3 mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <input type="text" name="search" class="form-ctrl" style="width:220px;"
               placeholder="{{ app()->getLocale()=='km' ? 'ស្វែងរកផលិតផល...' : 'Search products...' }}"
               value="{{ request('search') }}"/>

        {{-- Date range --}}
        <div class="d-flex align-items-center gap-1">
            <span style="font-size:12px;color:#6b7280;white-space:nowrap;">{{ app()->getLocale()=='km' ? 'ពីថ្ងៃ' : 'From' }}</span>
            <input type="date" name="date_from" class="form-ctrl" style="width:150px;" value="{{ request('date_from') }}">
        </div>
        <div class="d-flex align-items-center gap-1">
            <span style="font-size:12px;color:#6b7280;white-space:nowrap;">{{ app()->getLocale()=='km' ? 'ដល់ថ្ងៃ' : 'To' }}</span>
            <input type="date" name="date_to" class="form-ctrl" style="width:150px;" value="{{ request('date_to') }}">
        </div>

        {{-- Export buttons --}}
        <a href="{{ route('admin.stock.export.excel', request()->query()) }}"
           class="qa-btn" style="border-color:#16a34a;color:#16a34a;">
             Excel
        </a>
        <a href="{{ route('admin.stock.export.pdf', request()->query()) }}"
           target="_blank"
           class="qa-btn" style="border-color:#dc2626;color:#dc2626;">
             PDF
        </a>

        <a href="{{ route('admin.stock.maintenance') }}" class="qa-btn ms-auto" style="border-color:#6366f1;color:#6366f1;">
            {{ app()->getLocale()=='km' ? 'ថែទាំស្តុក' : 'Maintenance' }}
        </a>
        <a href="{{ route('admin.stock.history') }}" class="qa-btn">
            {{ app()->getLocale()=='km' ? 'មើលប្រវត្តិ' : 'View History' }}
        </a>
    </div>
</form>

<div class="panel">
    <div class="panel-head">
        <h6>{{ app()->getLocale()=='km' ? 'ស្តុកផលិតផល' : 'Products Stock' }} ({{ count($products) }})</h6>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">{{ app()->getLocale()=='km' ? 'ផលិតផល' : 'Product' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ប្រភេទ' : 'Category' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ម៉ាក' : 'Brand' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ស្តុក' : 'Stock' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status' }}</th>
                    <th style="padding-right:20px;text-align:right;">{{ app()->getLocale()=='km' ? 'សកម្មភាព' : 'Action' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td style="padding-left:20px;">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $product->image_url ?? '' }}"
                                 onerror="this.style.background='#f3f4f6'"
                                 style="width:40px;height:40px;object-fit:contain;border-radius:8px;background:#f9fafb;padding:4px;"/>
                            <div style="font-weight:600;font-size:13px;color:#111;">{{ Str::limit($product->name, 30) }}</div>
                        </div>
                    </td>
                    <td style="font-size:12px;color:#6b7280;">{{ $product->category->name }}</td>
                    <td style="font-size:12px;color:#6b7280;">{{ $product->brand->name ?? '—' }}</td>
                    <td>
                        <span style="font-size:16px;font-weight:700;color:#374151;">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td>
                        @if($product->stock == 0)
                            <span class="pill pill-cancelled">{{ app()->getLocale()=='km' ? 'អស់ស្តុក' : 'Out of Stock' }}</span>
                        @elseif($product->stock < 10)
                            <span class="pill pill-pending">{{ app()->getLocale()=='km' ? 'ស្តុកទាប' : 'Low Stock' }}</span>
                        @else
                            <span class="pill pill-delivered">{{ app()->getLocale()=='km' ? 'មានស្តុក' : 'In Stock' }}</span>
                        @endif
                    </td>
                    <td style="padding-right:20px;text-align:right;">
                        <a href="{{ route('admin.stock.add', $product->id) }}" class="qa-btn qa-primary" style="font-size:12px;padding:6px 14px;">
                            + {{ app()->getLocale()=='km' ? 'បន្ថែមស្តុក' : 'Add Stock' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                        {{ app()->getLocale()=='km' ? 'រកមិនឃើញផលិតផល' : 'No products found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection