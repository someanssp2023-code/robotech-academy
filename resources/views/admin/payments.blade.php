@extends('layouts.admin')
@section('title', app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payments')

@section('content')

<form method="GET" action="{{ route('admin.payments') }}" class="panel p-3 mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <select name="status" class="form-ctrl" style="width:180px;" onchange="this.form.submit()">
            <option value="">{{ app()->getLocale()=='km' ? 'ស្ថានភាពទាំងអស់' : 'All Statuses' }}</option>
            @foreach(['Pending'=>(app()->getLocale()=='km'?'រង់ចាំ':'Pending'),'Completed'=>(app()->getLocale()=='km'?'បានបញ្ចប់':'Completed'),'Failed'=>(app()->getLocale()=='km'?'បរាជ័យ':'Failed'),'Refunded'=>(app()->getLocale()=='km'?'បានសង':'Refunded')] as $val=>$lbl)
                <option value="{{ $val }}" {{ request('status')==$val ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
        </select>
        {{-- Date range --}}
        <input type="date" name="date_from" class="form-ctrl" style="width:160px;"
               value="{{ request('date_from') }}"/>
        <span style="color:#6b7280;font-weight:600;font-size:13px;">{{ app()->getLocale()=='km' ? 'ដល់' : 'To' }}</span>
        <input type="date" name="date_to" class="form-ctrl" style="width:160px;"
               value="{{ request('date_to') }}"/>
        <button type="submit" class="qa-btn qa-primary">{{ app()->getLocale()=='km' ? 'ស្វែងរក' : 'Search' }}</button>
        @if(request()->hasAny(['status','date_from','date_to']))
            <a href="{{ route('admin.payments') }}" class="qa-btn">{{ app()->getLocale()=='km' ? 'លប់ចោល' : 'Clear' }}</a>
        @endif
    </div>
</form>

<div class="panel">
    <div class="panel-head">
        <h6>{{ app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payments' }} ({{ $payments->total() }})</h6>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.payments.export.excel', request()->all()) }}"
               class="qa-btn" style="border-color:#16a34a;color:#16a34a;">⬇ Excel</a>
            <a href="{{ route('admin.payments.export.pdf', request()->all()) }}"
               target="_blank"
               class="qa-btn" style="border-color:#dc2626;color:#dc2626;">⬇ PDF</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">No</th>
                    <th>{{ app()->getLocale()=='km' ? 'ការបញ្ជាទិញ' : 'Order' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ចំនួន' : 'Amount' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'វិធី' : 'Method' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                @php
                    $sc = match($payment->status){'Completed'=>'pill-delivered','Failed'=>'pill-cancelled','Refunded'=>'pill-shipped',default=>'pill-pending'};
                    $statusLabels = ['Pending'=>app()->getLocale()=='km'?'រង់ចាំ':'Pending','Completed'=>app()->getLocale()=='km'?'បានបញ្ចប់':'Completed','Failed'=>app()->getLocale()=='km'?'បរាជ័យ':'Failed','Refunded'=>app()->getLocale()=='km'?'បានសង':'Refunded'];
                @endphp
                <tr>
                    <td style="padding-left:20px;font-family:monospace;font-size:12px;color:#9ca3af;">
                        {{ ($payments->currentPage()-1)*$payments->perPage()+$loop->index+1 }}
                    </td>
                    <td style="font-weight:700;font-size:13px;color:#111;">RA-{{ $payment->order->id ?? '—' }}</td>
                    <td>
                        <div style="font-weight:600;font-size:13px;color:#111;">{{ $payment->user->name }}</div>
                        <div style="color:#9ca3af;font-size:11px;">{{ $payment->user->email }}</div>
                    </td>
                    <td style="font-weight:600;color:#374151;">${{ number_format($payment->amount, 2) }}</td>
                    <td style="font-size:12px;color:#6b7280;">{{ $payment->method }}</td>
                    <td><span class="pill {{ $sc }}">{{ $statusLabels[$payment->status] ?? $payment->status }}</span></td>
                    <td style="font-size:12px;color:#9ca3af;">{{ $payment->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                        {{ app()->getLocale()=='km' ? 'មិនទាន់មានការទូទាត់' : 'No payments found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="p-3 border-top" style="border-color:#f3f4f6!important;">
        {{ $payments->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection