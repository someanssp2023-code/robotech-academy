<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f1f5f9; }
        .wrap { max-width: 580px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: #0f172a; padding: 24px 32px; display: flex; align-items: center; gap: 12px; }
        .logo { width: 36px; height: 36px; background: #f59e0b; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 900; color: #000; }
        .brand { font-size: 18px; font-weight: 800; color: #fff; }
        .body { padding: 32px; }
        h1 { font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
        .sub { font-size: 14px; color: #64748b; margin-bottom: 24px; line-height: 1.6; }
        .status-box { border-radius: 12px; padding: 20px; margin-bottom: 24px; text-align: center; }
        .status-label { font-size: 20px; font-weight: 800; margin-top: 8px; }
        .order-info { background: #f8fafc; border-radius: 10px; padding: 16px; margin-bottom: 24px; border: 1px solid #e2e8f0; }
        .info-row { display: flex; justify-content: space-between; font-size: 13px; padding: 6px 0; border-bottom: 1px solid #f1f5f9; }
        .info-row:last-child { border-bottom: none; }
        .info-key { color: #64748b; }
        .info-val { font-weight: 700; color: #0f172a; }
        .btn { display: block; width: fit-content; margin: 0 auto 8px; background: #0f172a; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 700; }
        .footer { background: #f8fafc; padding: 16px 32px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <div class="logo">R</div>
        <div class="brand">RoboTech Academy</div>
    </div>
    <div class="body">
        <h1>{{ app()->getLocale()=='km' ? 'ស្ថានភាពការបញ្ជាទិញបានផ្លាស់ប្តូរ' : 'Order Status Updated' }}</h1>
        <p class="sub">
            {{ app()->getLocale()=='km'
                ? 'សួស្តី '.$order->user->name.'! ស្ថានភាពការបញ្ជាទិញ RA-'.$order->id.' របស់អ្នកបានផ្លាស់ប្តូរ។'
                : 'Hello '.$order->user->name.'! Your order RA-'.$order->id.' has been updated.' }}
        </p>

        {{-- Status Badge --}}
        @php
            $statusConfig = [
                'Processing' => ['bg'=>'#dbeafe','color'=>'#1e40af','icon'=>'⚙️'],
                'Shipped'    => ['bg'=>'#e0f2fe','color'=>'#0369a1','icon'=>'🚚'],
                'Delivered'  => ['bg'=>'#dcfce7','color'=>'#166534','icon'=>'✅'],
                'Cancelled'  => ['bg'=>'#fee2e2','color'=>'#991b1b','icon'=>'❌'],
                'Pending'    => ['bg'=>'#fef3c7','color'=>'#92400e','icon'=>'⏳'],
            ];
            $config = $statusConfig[$order->status] ?? ['bg'=>'#f1f5f9','color'=>'#475569','icon'=>'📦'];
        @endphp
        <div class="status-box" style="background:{{ $config['bg'] }};">
            <div style="font-size:36px;">{{ $config['icon'] }}</div>
            <div class="status-label" style="color:{{ $config['color'] }};">{{ $order->status }}</div>
        </div>

        {{-- Order Info --}}
        <div class="order-info">
            <div class="info-row">
                <span class="info-key">{{ app()->getLocale()=='km' ? 'លេខការបញ្ជាទិញ' : 'Order ID' }}</span>
                <span class="info-val">RA-{{ $order->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-key">{{ app()->getLocale()=='km' ? 'ចំនួនទំនិញ' : 'Items' }}</span>
                <span class="info-val">{{ $order->orderItems->count() }} {{ app()->getLocale()=='km' ? 'មុខ' : 'items' }}</span>
            </div>
            <div class="info-row">
                <span class="info-key">{{ app()->getLocale()=='km' ? 'សរុប' : 'Total' }}</span>
                <span class="info-val" style="color:#f59e0b;">${{ number_format($order->total_amount, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="info-key">{{ app()->getLocale()=='km' ? 'អាសយដ្ឋាន' : 'Shipping To' }}</span>
                <span class="info-val">{{ Str::limit($order->shipping_address, 40) }}</span>
            </div>
        </div>

        <a href="{{ url('/my-orders/'.$order->id) }}" class="btn">
            {{ app()->getLocale()=='km' ? 'មើលការបញ្ជាទិញ' : 'View Order Details' }}
        </a>
    </div>
    <div class="footer">
        © {{ date('Y') }} RoboTech Academy. Siem Reap, Cambodia.
    </div>
</div>
</body>
</html>