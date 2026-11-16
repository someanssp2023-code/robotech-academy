<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ABA PayWay — RoboTech</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700;800&display=swap" rel="stylesheet"/>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Space Grotesk', sans-serif; background: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #fff; border-radius: 20px; box-shadow: 0 8px 40px rgba(0,0,0,.1); width: 100%; max-width: 420px; overflow: hidden; }
        .header { background: #003478; padding: 24px; text-align: center; }
        .header img { height: 36px; }
        .header h2 { color: #fff; font-size: 16px; font-weight: 700; margin-top: 12px; }
        .body { padding: 28px; }
        .order-info { background: #f8fafc; border-radius: 12px; padding: 16px; margin-bottom: 24px; }
        .info-row { display: flex; justify-content: space-between; font-size: 13px; padding: 5px 0; color: #64748b; }
        .info-val { font-weight: 700; color: #0f172a; }
        .total-row { display: flex; justify-content: space-between; font-size: 18px; font-weight: 800; margin-top: 10px; padding-top: 10px; border-top: 2px solid #e2e8f0; }
        .total-amt { color: #003478; }

        .btn-pay { width: 100%; background: #003478; color: #fff; border: none; border-radius: 12px; padding: 14px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all .2s; }
        .btn-pay:hover { background: #002060; }
        .btn-cancel { display: block; text-align: center; margin-top: 12px; font-size: 13px; color: #94a3b8; text-decoration: none; }
        .btn-cancel:hover { color: #ef4444; }
        .sandbox-badge { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; border-radius: 8px; padding: 8px 12px; font-size: 12px; font-weight: 600; text-align: center; margin-bottom: 16px; }



        /* Loading overlay */
        .loading { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.6); z-index: 999; align-items: center; justify-content: center; flex-direction: column; gap: 16px; }
        .loading.show { display: flex; }
        .spinner { width: 48px; height: 48px; border: 4px solid rgba(255,255,255,.3); border-top-color: #fff; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading p { color: #fff; font-size: 15px; font-weight: 600; }
    </style>
</head>
<body>

{{-- Loading overlay --}}
<div class="loading" id="loading">
    <div class="spinner"></div>
    <p>{{ app()->getLocale()=='km' ? 'កំពុងដំណើរការ...' : 'Processing payment...' }}</p>
</div>

<div class="card">
    {{-- Header --}}
    <div class="header">
        <div style="font-size:28px;font-weight:900;color:#f59e0b;letter-spacing:-.02em;">ABA</div>
        <h2>{{ app()->getLocale()=='km' ? 'ការទូទាត់ ABA PayWay' : 'ABA PayWay Payment' }}</h2>
    </div>

    <div class="body">
        {{-- Sandbox notice --}}
        <div class="sandbox-badge">
            {{ app()->getLocale()=='km' ? 'Sandbox Mode — Testing Only' : ' Testing Only' }}
        </div>

        {{-- Order info --}}
        <div class="order-info">
            <div class="info-row">
                <span>{{ app()->getLocale()=='km' ? 'លេខការបញ្ជាទិញ' : 'Order ID' }}</span>
                <span class="info-val">#RA-{{ $order->id }}</span>
            </div>
            <div class="info-row">
                <span>{{ app()->getLocale()=='km' ? 'ឈ្មោះ' : 'Customer' }}</span>
                <span class="info-val">{{ $order->user->name }}</span>
            </div>
            <div class="info-row">
                <span>{{ app()->getLocale()=='km' ? 'ចំនួនទំនិញ' : 'Items' }}</span>
                <span class="info-val">{{ $order->orderItems->count() }}</span>
            </div>
            <div class="total-row">
                <span>{{ app()->getLocale()=='km' ? 'សរុប' : 'Total' }}</span>
                <span class="total-amt">${{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        {{-- Sandbox notice --}}
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:14px 16px;margin-bottom:20px;text-align:center;">
            <div style="font-size:13px;color:#166534;font-weight:600;">
                {{ app()->getLocale()=='km' ? 'ចុច Pay ខាងក្រោម' : ' Click Pay below to simulate' }}
            </div>
        </div>

        {{-- Pay button --}}
        <form action="{{ route('payment.aba.success', $order->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn-pay" onclick="showLoading()">
                {{ app()->getLocale()=='km' ? 'ទូទាត់ $' : 'Pay $' }}{{ number_format($order->total_amount, 2) }}
            </button>
        </form>

        <a href="{{ route('cart') }}" class="btn-cancel">
            ← {{ app()->getLocale()=='km' ? 'បោះបង់' : 'Cancel' }}
        </a>
    </div>
</div>

<script>
function showLoading() {
    document.getElementById('loading').classList.add('show');
}
</script>
</body>
</html>