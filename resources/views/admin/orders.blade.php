@extends('layouts.admin')
@section('title', app()->getLocale()=='km' ? 'ការបញ្ជាទិញ' : 'Orders')

@section('content')

<form method="GET" action="{{ route('admin.orders') }}" class="panel p-3 mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <input type="text" name="search" class="form-ctrl" style="width:200px;"
               placeholder="{{ app()->getLocale()=='km' ? 'លេខការបញ្ជាទិញ...' : 'Order number...' }}"
               value="{{ request('search') }}"/>
        <select name="status" class="form-ctrl" style="width:160px;" onchange="this.form.submit()">
            <option value="">{{ app()->getLocale()=='km' ? 'ស្ថានភាពទាំងអស់' : 'All Statuses' }}</option>
            @foreach(['Pending'=>(app()->getLocale()=='km'?'រង់ចាំ':'Pending'),'Processing'=>(app()->getLocale()=='km'?'កំពុងដំណើរការ':'Processing'),'Shipped'=>(app()->getLocale()=='km'?'កំពុងដឹក':'Shipped'),'Delivered'=>(app()->getLocale()=='km'?'បានដឹកជញ្ជូន':'Delivered'),'Cancelled'=>(app()->getLocale()=='km'?'បានបោះបង់':'Cancelled')] as $val => $lbl)
                <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
        </select>
        <select name="payment_status" class="form-ctrl" style="width:140px;" onchange="this.form.submit()">
            <option value="">{{ app()->getLocale()=='km' ? 'ការទូទាត់ទាំងអស់' : 'All Payments' }}</option>
            <option value="Unpaid" {{ request('payment_status')=='Unpaid' ? 'selected' : '' }}>{{ app()->getLocale()=='km' ? 'មិនទាន់បង់' : 'Unpaid' }}</option>
            <option value="Paid"   {{ request('payment_status')=='Paid'   ? 'selected' : '' }}>{{ app()->getLocale()=='km' ? 'បានបង់' : 'Paid' }}</option>
        </select>
        {{-- Date range --}}
        <input type="date" name="date_from" class="form-ctrl" style="width:160px;"
               value="{{ request('date_from') }}"/>
        <span style="color:#6b7280;font-weight:600;font-size:13px;">{{ app()->getLocale()=='km' ? 'ដល់' : 'To' }}</span>
        <input type="date" name="date_to" class="form-ctrl" style="width:160px;"
               value="{{ request('date_to') }}"/>
        <button type="submit" class="qa-btn qa-primary">{{ app()->getLocale()=='km' ? 'ស្វែងរក' : 'Search' }}</button>
        @if(request()->hasAny(['search','status','payment_status','date_from','date_to']))
            <a href="{{ route('admin.orders') }}" class="qa-btn">{{ app()->getLocale()=='km' ? 'លប់ចោល' : 'Clear' }}</a>
        @endif
    </div>
</form>

<div class="panel">
    <div class="panel-head">
        <h6>{{ app()->getLocale()=='km' ? 'ការបញ្ជាទិញ' : 'Orders' }} ({{ $orders->total() }})</h6>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders.export.excel', request()->all()) }}"
               class="qa-btn" style="border-color:#16a34a;color:#16a34a;">⬇ Excel</a>
            <a href="{{ route('admin.orders.export.pdf', request()->all()) }}"
               target="_blank"
               class="qa-btn" style="border-color:#dc2626;color:#dc2626;">⬇ PDF</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">{{ app()->getLocale()=='km' ? 'លេខ' : 'No' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'មុខ' : 'Items' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'សរុប' : 'Total' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payment' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status' }}</th>
                    <th style="padding-right:20px;text-align:right;">{{ app()->getLocale()=='km' ? 'សកម្មភាព' : 'Action' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                @php
                    $sc = match($order->status) {
                        'Delivered'=>'pill-delivered','Cancelled'=>'pill-cancelled',
                        'Shipped'=>'pill-shipped','Processing'=>'pill-processing',default=>'pill-pending'
                    };
                    $statusLabels = ['Pending'=>app()->getLocale()=='km'?'រង់ចាំ':'Pending','Processing'=>app()->getLocale()=='km'?'កំពុងដំណើរការ':'Processing','Shipped'=>app()->getLocale()=='km'?'កំពុងដឹក':'Shipped','Delivered'=>app()->getLocale()=='km'?'បានដឹកជញ្ជូន':'Delivered','Cancelled'=>app()->getLocale()=='km'?'បានបោះបង់':'Cancelled'];
                @endphp
                <tr>
                    <td style="padding-left:20px;font-weight:700;color:#111;font-size:13px;">RA-{{ $order->id }}</td>
                    <td>
                        <div style="font-weight:600;font-size:13px;color:#111;">{{ $order->user->name }}</div>
                        <div style="color:#9ca3af;font-size:11px;">{{ $order->user->email }}</div>
                    </td>
                    <td style="color:#6b7280;font-size:12px;">{{ $order->created_at->format('M d, Y') }}</td>
                    <td style="font-size:13px;color:#374151;">{{ $order->orderItems->count() }}</td>
                    <td style="font-weight:600;color:#374151;">${{ number_format($order->total_amount, 2) }}</td>
                    <td>
                        <div style="font-size:12px;color:#6b7280;margin-bottom:3px;">{{ $order->payment_method }}</div>
                        <span class="pill {{ $order->payment_status==='Paid' ? 'pill-delivered' : 'pill-pending' }}">
                            {{ $order->payment_status==='Paid' ? (app()->getLocale()=='km'?'បានបង់':'Paid') : (app()->getLocale()=='km'?'មិនទាន់បង់':'Unpaid') }}
                        </span>
                    </td>
                    <td><span class="pill {{ $sc }}">{{ $statusLabels[$order->status] ?? $order->status }}</span></td>
                    <td style="padding-right:20px;text-align:right;">
                        <a href="{{ route('admin.order.detail', $order->id) }}" class="view-btn">{{ app()->getLocale()=='km' ? 'មើល' : 'View' }}</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#9ca3af;">
                        {{ app()->getLocale()=='km' ? 'មិនទាន់មានការបញ្ជាទិញ' : 'No orders found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="p-3 border-top" style="border-color:#f3f4f6!important;">
        {{ $orders->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection