@extends('layouts.app')
@section('title', app()->getLocale()=='km' ? 'ការបញ្ជាទិញបានជោគជ័យ' : 'Order Placed Successfully')

@section('content')
<style>
/* ── Layout ── */
.sw { max-width: 660px; margin: 0 auto; padding: 48px 16px 100px; }

/* ── Hero ── */
.sw-hero { text-align: center; margin-bottom: 36px; }
.sw-anim { position: relative; width: 100px; height: 100px; margin: 0 auto 24px; }
.sw-ring { position: absolute; inset: 0; border-radius: 50%;
           border: 3px solid #22c55e; opacity: 0;
           animation: ringOut .6s .1s ease-out forwards; }
@keyframes ringOut { to { transform: scale(1.45); opacity: 0; } }
.sw-circle { width: 100px; height: 100px; border-radius: 50%;
             background: linear-gradient(135deg,#bbf7d0,#dcfce7);
             display: flex; align-items: center; justify-content: center;
             font-size: 48px; animation: popIn .4s cubic-bezier(.17,.67,.34,1.3); }
@keyframes popIn { from { transform: scale(0); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.sw-title { font-size: 26px; font-weight: 900; color: #0f172a; letter-spacing: -.3px; margin-bottom: 8px; }
.sw-sub   { font-size: 14px; color: #64748b; line-height: 1.65; }

/* ── Email notice ── */
.sw-email { display: flex; align-items: center; gap: 10px;
            background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px;
            padding: 12px 16px; font-size: 13px; color: #166534;
            margin-bottom: 20px; }
.sw-email-icon { font-size: 18px; flex-shrink: 0; }

/* ── Cards ── */
.sw-card { background: #fff; border-radius: 18px; border: 1px solid #e2e8f0;
           box-shadow: 0 2px 10px rgba(0,0,0,.05); margin-bottom: 16px; overflow: hidden; }
.sw-card-h { padding: 15px 22px; border-bottom: 1px solid #f1f5f9;
             display: flex; align-items: center; justify-content: space-between; }
.sw-card-title { font-size: 13px; font-weight: 800; color: #0f172a;
                 display: flex; align-items: center; gap: 8px; }
.sw-card-body { padding: 6px 22px 18px; }

/* ── Info rows ── */
.i-row { display: flex; justify-content: space-between; align-items: center;
         font-size: 13px; padding: 10px 0; border-bottom: 1px solid #f8fafc; }
.i-row:last-child { border-bottom: none; }
.i-key { color: #64748b; }
.i-val { font-weight: 700; color: #0f172a; text-align: right; max-width: 260px; }

/* ── Status pill ── */
.spill { display: inline-block; padding: 3px 12px; border-radius: 20px;
         font-size: 11px; font-weight: 800; background: #fef3c7; color: #92400e; }

/* ── Order number badge ── */
.order-badge { font-size: 16px; font-weight: 900; color: #f59e0b;
               background: #fffbeb; border: 1.5px solid #fde68a;
               border-radius: 8px; padding: 2px 10px; letter-spacing: .3px; }

/* ── Item rows ── */
.item-row { display: flex; align-items: center; gap: 12px; padding: 10px 0;
            border-bottom: 1px solid #f8fafc; }
.item-row:last-child { border-bottom: none; }
.item-img { width: 50px; height: 50px; border-radius: 10px; object-fit: contain;
            background: #f8fafc; border: 1px solid #e2e8f0; padding: 5px; flex-shrink: 0; }
.item-name  { font-size: 13px; font-weight: 700; color: #0f172a; flex: 1; min-width: 0; line-height: 1.4; }
.item-qty   { font-size: 11px; color: #94a3b8; margin-top: 3px; }
.item-price { font-size: 13px; font-weight: 800; color: #f59e0b; white-space: nowrap; }

/* ── Price breakdown ── */
.sum-row { display: flex; justify-content: space-between; font-size: 13px;
           padding: 6px 0; color: #64748b; }

/* ── Total box ── */
.total-box { margin: 14px 0 4px; background: #0f172a; border-radius: 14px;
             padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; }
.total-label  { color: rgba(255,255,255,.65); font-size: 14px; font-weight: 600; }
.total-amount { color: #fbbf24; font-size: 22px; font-weight: 900; letter-spacing: -.5px; }

/* ── Action buttons ── */
.sw-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-top: 24px; }
.btn-pry { display: inline-flex; align-items: center; gap: 8px;
           background: #0f172a; color: #fff; text-decoration: none;
           padding: 12px 28px; border-radius: 12px; font-size: 14px; font-weight: 700;
           transition: all .2s; }
.btn-pry:hover { background: #f59e0b; color: #000; transform: translateY(-1px); }
.btn-out { display: inline-flex; align-items: center; gap: 8px;
           background: transparent; color: #374151; text-decoration: none;
           padding: 12px 24px; border-radius: 12px; font-size: 14px; font-weight: 600;
           border: 1.5px solid #e2e8f0; transition: all .2s; }
.btn-out:hover { border-color: #0f172a; color: #0f172a; transform: translateY(-1px); }
</style>

<div class="sw">

    {{-- Hero --}}
    <div class="sw-hero">
        <div class="sw-anim">
            <div class="sw-ring"></div>
            <div class="sw-circle">✅</div>
        </div>
        <h1 class="sw-title">
            {{ app()->getLocale()=='km' ? 'ការបញ្ជាទិញបានជោគជ័យ!' : 'Order Placed Successfully!' }}
        </h1>
        <p class="sw-sub">
            {!! app()->getLocale()=='km'
                ? 'សូមអរគុណ '.$order->user->name.'! យើងបានទទួលការបញ្ជាទិញរបស់អ្នក។'
                : 'Thank you, <strong>'.e($order->user->name).'</strong>! We\'ve received your order and will process it shortly.' !!}
        </p>
    </div>

    {{-- Email notice --}}
    <div class="sw-email">
        <span class="sw-email-icon">📧</span>
        <span>
    {!! app()->getLocale()=='km'
        ? 'Email បញ្ជាក់ត្រូវបានផ្ញើទៅ '.e($order->user->email)
        : 'A confirmation email has been sent to <strong>'.e($order->user->email).'</strong>' !!}
        </span>
    </div>

    {{-- Order Info card --}}
    <div class="sw-card">
        <div class="sw-card-h">
            <div class="sw-card-title">📋 {{ app()->getLocale()=='km' ? 'ព័ត៌មានការបញ្ជាទិញ' : 'Order Details' }}</div>
            <span class="order-badge">#RA-{{ $order->id }}</span>
        </div>
        <div class="sw-card-body">
            <div class="i-row">
                <span class="i-key">{{ app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date' }}</span>
                <span class="i-val">{{ $order->created_at->format('M d, Y  h:i A') }}</span>
            </div>
            <div class="i-row">
                <span class="i-key">{{ app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status' }}</span>
                <span class="spill">{{ $order->status }}</span>
            </div>
            <div class="i-row">
                <span class="i-key">{{ app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payment' }}</span>
                <span class="i-val">{{ $order->payment_method }}</span>
            </div>
            <div class="i-row">
                <span class="i-key">{{ app()->getLocale()=='km' ? 'អាសយដ្ឋាន' : 'Ship To' }}</span>
                <span class="i-val" style="font-weight:600;color:#374151;">{{ $order->shipping_address }}</span>
            </div>
        </div>
    </div>

    {{-- Items card --}}
    <div class="sw-card">
        <div class="sw-card-h">
            <div class="sw-card-title">
                {{ app()->getLocale()=='km' ? 'ទំនិញ' : 'Items Ordered' }}
            </div>
            <span style="font-size:12px;color:#94a3b8;font-weight:600;">
                {{ $order->orderItems->count() }} {{ app()->getLocale()=='km' ? 'មុខ' : 'items' }}
            </span>
        </div>
        <div class="sw-card-body">
            @foreach($order->orderItems as $item)
            <div class="item-row">
                <img class="item-img"
                     src="{{ $item->product->image_url ?? '' }}"
                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2248%22 height=%2248%22%3E%3Crect width=%2248%22 height=%2248%22 fill=%22%23f3f4f6%22/%3E%3C/svg%3E'"
                     alt="{{ $item->product_name }}"/>
                <div style="flex:1;min-width:0;">
                    <div class="item-name">{{ Str::limit($item->product_name, 34) }}</div>
                    <div class="item-qty">x{{ $item->quantity }} × ${{ number_format($item->unit_price,2) }}</div>
                </div>
                <div class="item-price">${{ number_format($item->subtotal,2) }}</div>
            </div>
            @endforeach

            {{-- Price breakdown --}}
            <div style="margin-top:14px;padding-top:14px;border-top:1.5px dashed #e2e8f0;">
                <div class="sum-row">
                    <span>{{ app()->getLocale()=='km' ? 'តម្លៃរង' : 'Subtotal' }}</span>
                    <span style="font-weight:600;color:#0f172a;">${{ number_format($order->subtotal ?? $order->total_amount,2) }}</span>
                </div>
                @if(($order->discount ?? 0) > 0)
                <div class="sum-row" style="color:#10b981;">
                    <span>🏷 {{ app()->getLocale()=='km' ? 'បញ្ចុះតម្លៃ' : 'Discount' }}</span>
                    <span style="font-weight:700;">-${{ number_format($order->discount,2) }}</span>
                </div>
                @endif
                @if(($order->tax_amount ?? 0) > 0)
                <div class="sum-row" style="color:#6366f1;">
                    <span>🧾 {{ app()->getLocale()=='km' ? 'ពន្ធ' : 'Tax' }} ({{ number_format($order->tax_rate,0) }}%)</span>
                    <span style="font-weight:700;">+${{ number_format($order->tax_amount,2) }}</span>
                </div>
                @endif
                <div class="sum-row">
                    <span>{{ app()->getLocale()=='km' ? 'ដឹកជញ្ជូន' : 'Shipping' }}</span>
                    <span style="font-weight:700;color:#10b981;">{{ app()->getLocale()=='km' ? 'ឥតគិតថ្លៃ' : 'Free' }}</span>
                </div>
            </div>

            {{-- Total --}}
            <div class="total-box">
                <span class="total-label">{{ app()->getLocale()=='km' ? 'សរុបទាំងអស់' : 'Order Total' }}</span>
                <span class="total-amount">${{ number_format($order->total_amount,2) }}</span>
            </div>
        </div>
    </div>

    {{-- Action buttons --}}
    <div class="sw-actions">
        <a href="{{ route('my.orders') }}" class="btn-pry">
            📦 {{ app()->getLocale()=='km' ? 'ការបញ្ជាទិញរបស់ខ្ញុំ' : 'My Orders' }}
        </a>
        <a href="{{ route('home') }}" class="btn-out">
            🛒 {{ app()->getLocale()=='km' ? 'ទិញបន្ថែម' : 'Continue Shopping' }}
        </a>
    </div>

</div>
@endsection