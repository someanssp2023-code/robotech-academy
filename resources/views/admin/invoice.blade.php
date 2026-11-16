<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice #RA-{{ $order->id }} — RoboTech Academy</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    background: #f5f5f5;
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 14px;
    line-height: 1.5;
    color: #222;
    padding: 32px 16px 48px;
  }

  /* ── Toolbar (hidden on print) ── */
  .toolbar {
    max-width: 700px;
    margin: 0 auto 16px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .btn-back {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 14px; border-radius: 7px; font-size: 13px; font-weight: 600;
    background: #fff; color: #222; text-decoration: none;
    border: 1px solid #ccc;
  }
  .btn-back:hover { background: #f5f5f5; }
  .btn-print {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 7px; font-size: 13px; font-weight: 600;
    background: #222; color: #fff; border: none; cursor: pointer;
  }
  .btn-print:hover { background: #000; }

  /* ── Invoice card ── */
  .invoice {
    background: #fff;
    max-width: 700px;
    margin: 0 auto;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    overflow: hidden;
    box-shadow: 0 1px 6px rgba(0,0,0,.07);
  }

  /* ── HEADER — white bg, logo top-right ── */
  .inv-header {
    padding: 28px 32px 20px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    border-bottom: 1px solid #eee;
  }
  .inv-company h1 {
    font-size: 18px;
    font-weight: 900;
    color: #555;
    letter-spacing: .5px;
    text-transform: uppercase;
    margin-bottom: 10px;
  }
  .inv-company p {
    color: #777;
    font-size: 12.5px;
    line-height: 1.9;
  }
  .inv-logo-box {
    flex-shrink: 0;
    width: 70px;
    height: 70px;
    border-radius: 10px;
    overflow: hidden;
    background: #f3f3f3;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .inv-logo-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  /* ── BILL TO / INVOICE DETAILS ── */
  .inv-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    padding: 22px 32px;
    border-bottom: 1px solid #eee;
    gap: 0;
  }
  .inv-bill {
    padding-right: 24px;
    border-right: 1px solid #e0e0e0;
  }
  .inv-deets {
    padding-left: 24px;
  }
  .sec-label {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #999;
    margin-bottom: 10px;
  }
  .inv-bill h2 {
    font-size: 15px;
    font-weight: 800;
    color: #111;
    margin-bottom: 5px;
  }
  .inv-bill address {
    font-size: 13px;
    color: #555;
    line-height: 1.85;
    font-style: normal;
  }
  .deet-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    gap: 12px;
    padding: 4px 0;
    border-bottom: 1px solid #f3f3f3;
    font-size: 13px;
  }
  .deet-row:last-child { border-bottom: none; }
  .deet-k { color: #777; font-weight: 500; }
  .deet-v { color: #111; font-weight: 700; }
  .pill {
    display: inline-block;
    padding: 2px 9px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    background: #f0f0f0;
    color: #333;
    border: 1px solid #ddd;
  }

  /* ── ITEMS TABLE ── */
  .inv-table-wrap {
    padding: 0 32px 20px;
  }
  table { width: 100%; border-collapse: collapse; }
  thead tr { border-bottom: 1px solid #eee; }
  thead th {
    padding: 10px 10px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: #aaa;
    text-align: left;
  }
  thead th.r { text-align: right; }
  tbody tr { border-bottom: 1px solid #f3f3f3; }
  tbody tr:last-child { border-bottom: none; }
  tbody td { padding: 12px 10px; font-size: 13px; color: #222; vertical-align: middle; }
  td.r { text-align: right; font-variant-numeric: tabular-nums; }
  td.item-name { font-weight: 500; color: #222; }
  td.item-sku  { font-size: 11px; color: #aaa; }
  td.item-qty  { color: #555; }
  td.item-price{ color: #555; }
  td.item-total{ font-weight: 800; color: #111; }
  .item-img {
    width: 38px; height: 38px; border-radius: 6px; object-fit: contain;
    background: #f5f5f5; border: 1px solid #eee; padding: 3px;
  }

  /* ── TOTALS (right-aligned, no background) ── */
  .inv-bottom {
    padding: 16px 32px 24px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: flex-end;
  }
  .inv-totals { width: 260px; }
  .tot-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 0;
    font-size: 13px;
    color: #555;
    border-bottom: 1px solid #f3f3f3;
    font-variant-numeric: tabular-nums;
  }
  .tot-row:last-child {
    border-bottom: none;
    font-weight: 800;
    font-size: 14px;
    color: #111;
    padding-top: 10px;
    margin-top: 2px;
    border-top: 1px solid #ddd;
  }
  .tot-row .tot-label { color: #555; font-weight: 500; }
  .tot-row:last-child .tot-label { color: #111; font-weight: 800; }
  .tot-free { font-weight: 700; color: #222; }

  /* ── SIGNATURE ── */
  .inv-signature {
    padding: 20px 32px 28px;
    border-top: 1px solid #eee;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    align-items: end;
  }
  .sig-block { display: flex; flex-direction: column; gap: 5px; }
  .sig-space { height: 48px; }
  .sig-line { border-bottom: 1.5px solid #222; width: 100%; }
  .sig-lbl {
    font-size: 9.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #666;
    margin-top: 5px;
  }
  .sig-sub { font-size: 11px; color: #999; }

  /* ── FOOTER ── */
  .inv-footer {
    border-top: 1px solid #eee;
    padding: 12px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }
  .footer-logo { display: flex; align-items: center; gap: 10px; }
  .footer-logo img {
    height: 28px; width: auto; object-fit: contain;
    filter: grayscale(1); opacity: .6;
  }
  .footer-name  { font-size: 12px; font-weight: 700; color: #444; }
  .footer-tagline { font-size: 10px; color: #aaa; margin-top: 1px; }
  .footer-note  { font-size: 11px; color: #aaa; text-align: right; line-height: 1.7; }

  /* ── PRINT ── */
  @media print {
    .no-print { display: none !important; }
    body { background: #fff; padding: 0; }
    .invoice { box-shadow: none; border-radius: 0; border: none; }
    @page { margin: 10mm; size: A4; }
  }

  /* ── RESPONSIVE ── */
  @media (max-width: 560px) {
    body { padding: 12px 6px 32px; }
    .inv-header { padding: 18px; }
    .inv-meta { grid-template-columns: 1fr; padding: 16px 18px; gap: 16px; }
    .inv-bill { padding-right: 0; border-right: none; border-bottom: 1px solid #eee; padding-bottom: 14px; }
    .inv-deets { padding-left: 0; }
    .inv-table-wrap { padding: 0 12px 16px; overflow-x: auto; }
    .inv-bottom { padding: 14px 16px 20px; }
    .inv-totals { width: 100%; }
    .inv-signature { grid-template-columns: 1fr; padding: 14px 16px 22px; gap: 20px; }
    .inv-footer { padding: 10px 16px; }
  }
</style>
</head>
<body>

{{-- ── TOOLBAR (no-print) ── --}}
<div class="toolbar no-print">
  <a href="{{ route('admin.order.detail', $order->id) }}" class="btn-back">
    ← {{ app()->getLocale()=='km' ? 'ត្រឡប់' : 'Back to Order' }}
  </a>
  <button class="btn-print" onclick="window.print()">
    🖨 {{ app()->getLocale()=='km' ? 'បោះពុម្ព' : 'Print Invoice' }}
  </button>
</div>

<div class="invoice">

  {{-- ══ HEADER ══ --}}
  <div class="inv-header">
    <div class="inv-company">
      <h1>RoboTech </h1>
      <p>
        Tel: +855 0974906226<br>
        robotech.gmail.com
        
        
      </p>
    </div>
    <div class="inv-logo-box">
      <img src="{{ asset('images/logo.png') }}"
           alt="RoboTech Academy Logo"
           onerror="this.style.display='none'">
    </div>
  </div>

  {{-- ══ BILL TO / INVOICE DETAILS ══ --}}
  <div class="inv-meta">
    <div class="inv-bill">
      <div class="sec-label">{{ app()->getLocale()=='km' ? 'ព័ត៌មានអតិថិជន' : 'Bill To' }}</div>
      <h2>{{ $order->user->name ?? '—' }}</h2>
      <address>
        {{ $order->user->email ?? '' }}<br>
        @if($order->user->phone ?? $order->phone ?? null)
          {{ $order->user->phone ?? $order->phone }}<br>
        @endif
        @if($order->shipping_address ?? null)
          {{ $order->shipping_address }}<br>
        @endif
        @if($order->notes ?? null)
          <em style="color:#aaa;font-size:12px;">{{ $order->notes }}</em>
        @endif
      </address>
    </div>
    <div class="inv-deets">
      <div class="deet-row">
        <span class="deet-k">{{ app()->getLocale()=='km' ? 'លេខវិក្កយបត្រ' : 'Invoice No' }}</span>
        <span class="deet-v">#RA-{{ $order->id }}</span>
      </div>
      <div class="deet-row">
        <span class="deet-k">{{ app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date' }}</span>
        <span class="deet-v">{{ $order->created_at->format('d M Y') }}</span>
      </div>
      <div class="deet-row">
        <span class="deet-k">{{ app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payment' }}</span>
        <span class="deet-v">{{ $order->payment_method }}</span>
      </div>
      <div class="deet-row">
        <span class="deet-k">{{ app()->getLocale()=='km' ? 'ស្ថានភាពទូទាត់' : ' Status:' }}</span>
        <span class="deet-v">
          @php $pc = $order->payment_status === 'Paid' ? 'pill-paid' : 'pill-unpaid'; @endphp
          <span class="pill {{ $pc }}">{{ $order->payment_status }}</span>
        </span>
      </div>
      
    </div>
  </div>

  {{-- ══ ITEMS TABLE ══ --}}
  <div class="inv-table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:50px;"></th>
          <th>{{ app()->getLocale()=='km' ? 'ផលិតផល' : 'Description' }}</th>
          <th class="r">{{ app()->getLocale()=='km' ? 'ចំនួន' : 'Qty' }}</th>
          <th class="r">{{ app()->getLocale()=='km' ? 'តម្លៃឯកតា' : 'Unit Price' }}</th>
          <th class="r">{{ app()->getLocale()=='km' ? 'សរុប' : 'Amount' }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->orderItems as $item)
        <tr>
          <td>
            <img class="item-img"
                 src="{{ $item->product->image_url ?? '' }}"
                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2238%22 height=%2238%22%3E%3Crect width=%2238%22 height=%2238%22 fill=%22%23f0f0f0%22/%3E%3C/svg%3E'"
                 alt="{{ $item->product_name }}">
          </td>
          <td>
            <div class="item-name">{{ $item->product_name }}</div>
            @if($item->product->sku ?? null)
              <div class="item-sku">SKU: {{ $item->product->sku }}</div>
            @endif
          </td>
          <td class="r item-qty">{{ $item->quantity }}</td>
          <td class="r item-price">${{ number_format($item->unit_price, 2) }}</td>
          <td class="r item-total">${{ number_format($item->subtotal, 2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- ══ TOTALS (right-aligned) ══ --}}
  <div class="inv-bottom">
    <div class="inv-totals">
      @php
        $subtotal  = $order->subtotal ?? $order->total_amount;
        $discount  = $order->discount ?? 0;
        $taxAmount = $order->tax_amount ?? 0;
        $taxRate   = $order->tax_rate ?? 0;
        $total     = $order->total_amount;
      @endphp
      <div class="tot-row">
        <span class="tot-label">{{ app()->getLocale()=='km' ? 'តម្លៃរង' : 'Sub Total' }}</span>
        <span>${{ number_format($subtotal, 2) }}</span>
      </div>
      @if($discount > 0)
      <div class="tot-row">
        <span class="tot-label">{{ app()->getLocale()=='km' ? 'បញ្ចុះតម្លៃ' : 'Discount' }}</span>
        <span>-${{ number_format($discount, 2) }}</span>
      </div>
      @endif
      @if($taxAmount > 0)
      <div class="tot-row">
        <span class="tot-label">{{ app()->getLocale()=='km' ? 'ពន្ធ' : 'Tax' }} ({{ number_format($taxRate, 0) }}%)</span>
        <span>${{ number_format($taxAmount, 2) }}</span>
      </div>
      @endif
      <div class="tot-row">
        <span class="tot-label">{{ app()->getLocale()=='km' ? 'ដឹកជញ្ជូន' : 'Shipping' }}</span>
        <span class="tot-free">{{ app()->getLocale()=='km' ? 'ឥតគិតថ្លៃ' : 'Free' }}</span>
      </div>
      <div class="tot-row">
        <span class="tot-label">{{ app()->getLocale()=='km' ? 'សរុបទូទាត់' : 'Total Due' }}</span>
        <span>${{ number_format($total, 2) }}</span>
      </div>
    </div>
  </div>

  {{-- ══ SIGNATURE ══ --}}
  <div class="inv-signature">
    <div class="sig-block">
      <div class="sig-space"></div>
      <div class="sig-line"></div>
      <div class="sig-lbl">{{ app()->getLocale()=='km' ? 'ហត្ថលេខាអ្នកចេញ' : 'Owner' }}</div>
      <div class="sig-sub">RoboTech Academy</div>
    </div>
    <div class="sig-block">
      <div class="sig-space"></div>
      <div class="sig-line"></div>
      <div class="sig-lbl">{{ app()->getLocale()=='km' ? 'ហត្ថលេខាអតិថិជន' : 'Customer' }}</div>
      <div class="sig-sub">{{ $order->user->name ?? '' }}</div>
    </div>
  </div>

  {{-- ══ FOOTER ══ --}}
  <div class="inv-footer">
    <div class="footer-logo">
      <img src="{{ asset('images/logo.png') }}"
           alt="RoboTech Academy"
           onerror="this.style.display='none'">
      <div>
        <div class="footer-name">RoboTech Academy</div>
        <div class="footer-tagline">Siemreap, Cambodia</div>
      </div>
    </div>
    <div class="footer-note">
      {{ now()->format('d M Y, h:i A') }}<br>
      Invoice #RA-{{ $order->id }}
    </div>
  </div>

</div><!-- /.invoice -->

</body>
</html>