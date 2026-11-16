<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>KHQR Payment — RoboTech</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700;800&display=swap" rel="stylesheet"/>
    <style>body { font-family: 'Kantumruy Pro', sans-serif !important; }</style>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Space Grotesk', sans-serif; background: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #fff; border-radius: 20px; box-shadow: 0 8px 40px rgba(0,0,0,.1); width: 100%; max-width: 400px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%); padding: 24px; text-align: center; }
        .header h2 { color: #fff; font-size: 18px; font-weight: 800; margin-top: 8px; }
        .header p { color: rgba(255,255,255,.6); font-size: 12px; margin-top: 4px; }
        .body { padding: 24px; }
        .sandbox-badge { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; border-radius: 8px; padding: 8px 12px; font-size: 12px; font-weight: 600; text-align: center; margin-bottom: 20px; }
        .order-row { display: flex; justify-content: space-between; font-size: 13px; padding: 5px 0; color: #64748b; }
        .order-val { font-weight: 700; color: #0f172a; }
        .qr-wrap { background: #f8fafc; border-radius: 16px; padding: 20px; text-align: center; margin: 20px 0; border: 2px dashed #e2e8f0; }
        .qr-img { width: 200px; height: 200px; border-radius: 8px; }
        .qr-label { font-size: 12px; color: #94a3b8; margin-top: 10px; }
        .amount-box { background: #ffffff; border-radius: 12px; padding: 13px 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .amount-label { color: rgba(1, 1, 1, 0.6); font-size: 13px; }
        .amount-val { color: #f59e0b; font-size: 24px; font-weight: 900; }
        .status-box { border-radius: 10px; padding: 12px 16px; font-size: 13px; font-weight: 600; text-align: center; margin-bottom: 16px; }
        .status-waiting { background: #fef3c7; color: #92400e; }
        .status-success { background: #dcfce7; color: #166534; }
        .timer { text-align: center; font-size: 12px; color: #94a3b8; margin-bottom: 16px; }
        .timer span { font-weight: 700; color: #ef4444; }
        .banks { display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
        .bank { background: #f1f5f9; border-radius: 8px; padding: 6px 12px; font-size: 11px; font-weight: 600; color: #64748b; }
        .btn-test { width: 100%; background: #10b981; color: #fff; border: none; border-radius: 12px; padding: 13px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all .2s; margin-bottom: 8px; }
        .btn-test:hover { background: #059669; }
        .btn-cancel { display: block; text-align: center; font-size: 13px; color: #94a3b8; text-decoration: none; padding: 8px; }
        .btn-cancel:hover { color: #ef4444; }
    </style>
</head>
<body>
<div class="card">
    {{-- Header --}}
    <div class="header">
        
        <h2>KHQR Payment</h2>
        <p>{{ app()->getLocale()=='km' ? 'ស្កែន QR ដើម្បីទូទាត់' : 'Scan QR code to pay' }}</p>
    </div>

    <div class="body">
        {{-- Sandbox --}}
        

        {{-- Order info --}}
        <div class="order-row">
            <span>{{ app()->getLocale()=='km' ? 'លេខការបញ្ជាទិញ' : 'Order ID' }}</span>
            <span class="order-val">#RA-{{ $order->id }}</span>
        </div>
        <div class="order-row">
            <span>{{ app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer' }}</span>
            <span class="order-val">{{ $order->user->name }}</span>
        </div>

        {{-- Amount --}}
        <div class="amount-box" style="margin-top:16px;">
            <span class="amount-label">{{ app()->getLocale()=='km' ? 'ចំនួនទូទាត់' : 'Amount Due' }}</span>
            <span class="amount-val">${{ number_format($order->total_amount, 2) }}</span>
        </div>

        {{-- QR Code --}}
        <div class="qr-wrap">
            @if(isset($qrData['data']['qrString']))
                <img class="qr-img"
                     src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrData['data']['qrString']) }}"
                     alt="KHQR"/>
            @else
                {{-- Mock QR for testing --}}
                <img class="qr-img"
                     src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=MOCK-KHQR-RA{{ $order->id }}-{{ number_format($order->total_amount,2) }}"
                     alt="Mock KHQR"/>
            @endif
            <div class="qr-label">
                {{ app()->getLocale()=='km' ? 'ស្កែនដោយ' : 'Scan with' }}
            </div>
        </div>

        {{-- Supported banks --}}
        <div class="banks">
            <span class="bank">ABA</span>
            <span class="bank">ACLEDA</span>
            <span class="bank">Wing</span>
            <span class="bank">Bakong</span>
            <span class="bank">True Money</span>
        </div>

        {{-- Status --}}
        <div class="status-box status-waiting" id="payStatus">
            ⏳ {{ app()->getLocale()=='km' ? 'រង់ចាំការទូទាត់...' : 'Waiting for payment...' }}
        </div>

        {{-- Timer --}}
        <div class="timer">
            {{ app()->getLocale()=='km' ? 'QR ផុតកំណត់ក្នុង' : 'Expires in' }}:
            <span id="countdown">15:00</span>
        </div>

        {{-- TEST BUTTON (for local testing) --}}
        <form action="{{ route('payment.aba.success', $order->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn-test">
                {{ app()->getLocale()=='km' ? 'ក្លែងបង្ហាញការទូទាត់ (Test)' : ' Payment Success (Test)' }}
            </button>
        </form>

        <a href="{{ route('cart') }}" class="btn-cancel">
            ← {{ app()->getLocale()=='km' ? 'បោះបង់' : 'Cancel' }}
        </a>
    </div>
</div>

<script>
// Countdown
let secs = 900;
const el = document.getElementById('countdown');
const t  = setInterval(() => {
    secs--;
    if (secs <= 0) { clearInterval(t); el.textContent = 'Expired'; return; }
    el.textContent = Math.floor(secs/60).toString().padStart(2,'0') + ':' + (secs%60).toString().padStart(2,'0');
}, 1000);

// Poll payment (only if real MD5 available)
@if(isset($qrData['data']['md5']))
const md5 = "{{ $qrData['data']['md5'] }}";
const poll = setInterval(async () => {
    try {
        const r    = await fetch('{{ route("payment.khqr.check", $order->id) }}', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ md5 })
        });
        const data = await r.json();
        if (data.paid) {
            clearInterval(poll);
            document.getElementById('payStatus').className = 'status-box status-success';
            document.getElementById('payStatus').textContent = '✅ {{ app()->getLocale()=="km" ? "ការទូទាត់បានជោគជ័យ!" : "Payment Successful!" }}';
            setTimeout(() => window.location.href = '{{ route("order.success", $order->id) }}', 2000);
        }
    } catch(e) {}
}, 5000);
@endif
</script>
</body>
</html>