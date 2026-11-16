@extends('layouts.admin')
@section('title', app()->getLocale()=='km' ? 'លម្អិតការបញ្ជាទិញ' : 'Order Detail')

@section('content')

@php
    $sc = match($order->status) {
        'Delivered'=>'pill-delivered','Cancelled'=>'pill-cancelled',
        'Shipped'=>'pill-shipped','Processing'=>'pill-processing',default=>'pill-pending'
    };
    $statusLabels = [
        'Pending'    => app()->getLocale()=='km' ? 'រង់ចាំ'              : 'Pending',
        'Processing' => app()->getLocale()=='km' ? 'កំពុងដំណើរការ'       : 'Processing',
        'Shipped'    => app()->getLocale()=='km' ? 'កំពុងដឹក'            : 'Shipped',
        'Delivered'  => app()->getLocale()=='km' ? 'បានដឹកជញ្ជូន'        : 'Delivered',
        'Cancelled'  => app()->getLocale()=='km' ? 'បានបោះបង់'           : 'Cancelled',
    ];
@endphp

@if(session('success'))
<div class="alert alert-success mb-3">{{ session('success') }}</div>
@endif

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.orders') }}" class="qa-btn">← {{ app()->getLocale()=='km' ? 'ត្រឡប់' : 'Back' }}</a>
    <h5 class="mb-0 fw-bold">
        {{ app()->getLocale()=='km' ? 'ការបញ្ជាទិញ' : 'Order' }} #RA-{{ $order->id }}
    </h5>
    <span class="pill {{ $sc }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
</div>

<div class="row g-4">

    {{-- LEFT: Items + Price --}}
    <div class="col-lg-8">

        {{-- Items --}}
        <div class="panel mb-4">
            <div class="panel-head">
                <h6> {{ app()->getLocale()=='km' ? 'ទំនិញ' : 'Items' }}</h6>
                <span style="font-size:12px;color:#9ca3af;">{{ $order->orderItems->count() }} {{ app()->getLocale()=='km' ? 'មុខ' : 'items' }}</span>
            </div>
            <div style="padding:0 20px;">
                @foreach($order->orderItems as $item)
                <div style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid #f3f4f6;">
                    <img src="{{ $item->product->image_url ?? '' }}"
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2256%22 height=%2256%22%3E%3Crect width=%2256%22 height=%2256%22 fill=%22%23f3f4f6%22/%3E%3C/svg%3E'"
                         style="width:56px;height:56px;border-radius:10px;object-fit:contain;background:#f8fafc;border:1px solid #e5e7eb;padding:5px;flex-shrink:0;"
                         alt="{{ $item->product_name }}"/>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:700;font-size:13px;color:#111;">{{ $item->product_name }}</div>
                        <div style="font-size:12px;color:#9ca3af;margin-top:2px;">
                            x{{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}
                        </div>
                    </div>
                    <div style="font-weight:800;font-size:14px;color:#f59e0b;">${{ number_format($item->subtotal, 2) }}</div>
                </div>
                @endforeach
            </div>

            {{-- Price breakdown --}}
            <div style="padding:16px 20px;border-top:1px solid #f3f4f6;background:#fafafa;">
                <div style="display:flex;justify-content:space-between;font-size:13px;color:#6b7280;padding:5px 0;">
                    <span>{{ app()->getLocale()=='km' ? 'តម្លៃរង' : 'Subtotal' }}</span>
                    <span>${{ number_format($order->subtotal ?? $order->total_amount, 2) }}</span>
                </div>
                @if(($order->discount ?? 0) > 0)
                <div style="display:flex;justify-content:space-between;font-size:13px;color:#10b981;padding:5px 0;">
                    <span>{{ app()->getLocale()=='km' ? 'បញ្ចុះតម្លៃ' : 'Discount' }}</span>
                    <span>-${{ number_format($order->discount, 2) }}</span>
                </div>
                @endif
                @if(($order->tax_amount ?? 0) > 0)
                <div style="display:flex;justify-content:space-between;font-size:13px;color:#6366f1;padding:5px 0;">
                    <span> {{ app()->getLocale()=='km' ? 'ពន្ធ' : 'Tax' }} ({{ number_format($order->tax_rate, 0) }}%)</span>
                    <span>+${{ number_format($order->tax_amount, 2) }}</span>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;font-size:13px;color:#6b7280;padding:5px 0;">
                    <span>{{ app()->getLocale()=='km' ? 'ដឹកជញ្ជូន' : 'Shipping' }}</span>
                    <span style="color:#10b981;font-weight:600;">{{ app()->getLocale()=='km' ? 'ឥតគិតថ្លៃ' : 'Free' }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:16px;font-weight:800;color:#111;padding:12px 0 5px;border-top:2px solid #e5e7eb;margin-top:6px;">
                    <span>{{ app()->getLocale()=='km' ? 'សរុប' : 'Total' }}</span>
                    <span style="color:#f59e0b;">${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Update Status --}}
        <div class="panel">
            <div class="panel-head">
                <h6>🔄 {{ app()->getLocale()=='km' ? 'ធ្វើបច្ចុប្បន្នភាពស្ថានភាព' : 'Update Status' }}</h6>
            </div>
            <form method="POST" action="{{ route('admin.order.status', $order->id) }}" class="p-4">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size:13px;">
                            {{ app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Order Status' }}
                        </label>
                        <select name="status" class="form-ctrl">
                            @foreach(['Pending','Processing','Shipped','Delivered','Cancelled'] as $s)
                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                {{ $statusLabels[$s] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size:13px;">
                            {{ app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payment Status' }}
                        </label>
                        <select name="payment_status" class="form-ctrl">
                            <option value="Unpaid" {{ $order->payment_status==='Unpaid' ? 'selected' : '' }}>
                                {{ app()->getLocale()=='km' ? 'មិនទាន់បង់' : 'Unpaid' }}
                            </option>
                            <option value="Paid" {{ $order->payment_status==='Paid' ? 'selected' : '' }}>
                                {{ app()->getLocale()=='km' ? 'បានបង់' : 'Paid' }}
                            </option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="qa-btn qa-primary">
                            {{ app()->getLocale()=='km' ? 'រក្សាទុក' : 'Save Changes' }}
                        </button>
                        <a href="{{ route('admin.order.invoice', $order->id) }}"
                        target="_blank"
                        style="display:inline-flex;align-items:center;gap:6px;
                                padding:9px 16px;border-radius:9px;font-size:13px;font-weight:700;
                                background:#f59e0b;color:#000;text-decoration:none;border:none;">
                            🖨 {{ app()->getLocale()=='km' ? 'បោះពុម្ពវិក្កយបត្រ' : 'Print Invoice' }}
                        </a>
                    </div>
                </div>
            </form>
        </div>

    </div>

    {{-- RIGHT: Customer + Order info --}}
    <div class="col-lg-4">

        {{-- Customer --}}
        <div class="panel mb-4">
            <div class="panel-head">
                <h6>👤 {{ app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer' }}</h6>
            </div>
            <div class="p-4">
                <div style="font-weight:700;font-size:14px;color:#111;margin-bottom:4px;">{{ $order->user->name }}</div>
                <div style="font-size:12px;color:#9ca3af;margin-bottom:12px;">{{ $order->user->email }}</div>
                <div style="font-size:13px;color:#374151;">
                    <div style="margin-bottom:6px;">Tell: {{ $order->phone ?? '—' }}</div>
                    <div>Address {{ $order->shipping_address }}</div>
                </div>
                @if($order->notes)
                <div style="margin-top:12px;padding:10px;background:#fef9c3;border-radius:8px;font-size:12px;color:#713f12;">
                    📝 {{ $order->notes }}
                </div>
                @endif
            </div>
        </div>

        {{-- Order meta --}}
        <div class="panel">
            <div class="panel-head">
                <h6> {{ app()->getLocale()=='km' ? 'ព័ត៌មាន' : 'Order Info' }}</h6>
            </div>
            <div class="p-4">
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0;border-bottom:1px solid #f3f4f6;">
                    <span style="color:#6b7280;">{{ app()->getLocale()=='km' ? 'លេខ' : 'Order #' }}</span>
                    <span style="font-weight:700;color:#f59e0b;">{{ $order->order_number ?? 'RA-'.$order->id }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0;border-bottom:1px solid #f3f4f6;">
                    <span style="color:#6b7280;">{{ app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date' }}</span>
                    <span style="font-weight:600;color:#111;">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0;border-bottom:1px solid #f3f4f6;">
                    <span style="color:#6b7280;">{{ app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payment' }}</span>
                    <span style="font-weight:600;color:#111;">{{ $order->payment_method }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0;">
                    <span style="color:#6b7280;">{{ app()->getLocale()=='km' ? 'ស្ថានភាពទូទាត់' : 'Paid?' }}</span>
                    <span class="pill {{ $order->payment_status==='Paid' ? 'pill-delivered' : 'pill-pending' }}">
                        {{ $order->payment_status==='Paid' ? (app()->getLocale()=='km'?'បានបង់':'Paid') : (app()->getLocale()=='km'?'មិនទាន់បង់':'Unpaid') }}
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection