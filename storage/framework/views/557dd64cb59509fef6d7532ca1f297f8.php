<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f1f5f9; color: #1e293b; }
        .wrap { max-width: 580px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: #0f172a; padding: 28px 32px; display: flex; align-items: center; gap: 12px; }
        .logo { width: 40px; height: 40px; background: #f59e0b; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 900; color: #000; }
        .brand { font-size: 20px; font-weight: 800; color: #fff; }
        .body { padding: 32px; }
        .success-icon { width: 64px; height: 64px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 32px; }
        h1 { font-size: 22px; font-weight: 800; color: #0f172a; text-align: center; margin-bottom: 8px; }
        .sub { font-size: 14px; color: #64748b; text-align: center; margin-bottom: 28px; }
        .order-box { background: #f8fafc; border-radius: 12px; padding: 20px; margin-bottom: 24px; border: 1px solid #e2e8f0; }
        .order-id { font-size: 18px; font-weight: 800; color: #0f172a; }
        .order-meta { display: flex; justify-content: space-between; margin-top: 12px; flex-wrap: wrap; gap: 8px; }
        .meta-item { font-size: 12px; color: #64748b; }
        .meta-val { font-weight: 700; color: #0f172a; }
        .section-title { font-size: 13px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 12px; }
        .item-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
        .item-row:last-child { border-bottom: none; }
        .item-name { font-size: 13px; font-weight: 600; color: #0f172a; }
        .item-qty { font-size: 12px; color: #64748b; }
        .item-price { font-size: 13px; font-weight: 700; color: #0f172a; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 13px; color: #64748b; }
        .total-final { display: flex; justify-content: space-between; padding-top: 12px; margin-top: 4px; border-top: 2px solid #e2e8f0; font-size: 16px; font-weight: 800; }
        .total-amount { color: #f59e0b; }
        .btn { display: block; width: fit-content; margin: 24px auto 0; background: #0f172a; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 700; text-align: center; }
        .footer { background: #f8fafc; padding: 20px 32px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
        .address-box { background: #f8fafc; border-radius: 10px; padding: 14px 16px; margin-bottom: 20px; border: 1px solid #e2e8f0; font-size: 13px; color: #374151; line-height: 1.6; }
    </style>
</head>
<body>
<div class="wrap">
    
    <div class="header">
        <div class="logo">R</div>
        <div class="brand">RoboTech Academy</div>
    </div>

    <div class="body">
        
        <div class="success-icon">✅</div>
        <h1><?php echo e(app()->getLocale()=='km' ? 'ការបញ្ជាទិញបានជោគជ័យ!' : 'Order Confirmed!'); ?></h1>
        <p class="sub">
            <?php echo e(app()->getLocale()=='km'
                ? 'សូមអរគុណ '.$order->user->name.'! យើងបានទទួលការបញ្ជាទិញរបស់អ្នក។'
                : 'Thank you, '.$order->user->name.'! We have received your order.'); ?>

        </p>

        
        <div class="order-box">
            <div class="order-id">RA-<?php echo e($order->id); ?></div>
            <div class="order-meta">
                <div class="meta-item">
                    <?php echo e(app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date'); ?><br>
                    <span class="meta-val"><?php echo e($order->created_at->format('M d, Y h:i A')); ?></span>
                </div>
                <div class="meta-item">
                    <?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?><br>
                    <span class="meta-val" style="color:#f59e0b;"><?php echo e($order->status); ?></span>
                </div>
                <div class="meta-item">
                    <?php echo e(app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payment'); ?><br>
                    <span class="meta-val"><?php echo e($order->payment_method); ?></span>
                </div>
            </div>
        </div>

        
        <div class="section-title"><?php echo e(app()->getLocale()=='km' ? 'អាសយដ្ឋានដឹកជញ្ជូន' : 'Shipping Address'); ?></div>
        <div class="address-box"><?php echo e($order->shipping_address); ?></div>

        
        <div class="section-title"><?php echo e(app()->getLocale()=='km' ? 'ទំនិញដែលបញ្ជា' : 'Order Items'); ?></div>
        <div style="margin-bottom:20px;">
            <?php $__currentLoopData = $order->orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="item-row">
                <div>
                    <div class="item-name"><?php echo e($item->product_name); ?></div>
                    <div class="item-qty">x<?php echo e($item->quantity); ?> × $<?php echo e(number_format($item->unit_price, 2)); ?></div>
                </div>
                <div class="item-price">$<?php echo e(number_format($item->subtotal, 2)); ?></div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="total-row">
            <span><?php echo e(app()->getLocale()=='km' ? 'តម្លៃរង' : 'Subtotal'); ?></span>
            <span>$<?php echo e(number_format($order->subtotal ?? $order->total_amount, 2)); ?></span>
        </div>
        <?php if(($order->discount ?? 0) > 0): ?>
        <div class="total-row">
            <span style="color:#10b981;"><?php echo e(app()->getLocale()=='km' ? 'បញ្ចុះតម្លៃ' : 'Discount'); ?></span>
            <span style="color:#10b981;">-$<?php echo e(number_format($order->discount, 2)); ?></span>
        </div>
        <?php endif; ?>
        <div class="total-row">
            <span><?php echo e(app()->getLocale()=='km' ? 'ការដឹក' : 'Shipping'); ?></span>
            <span style="color:#10b981;"><?php echo e(app()->getLocale()=='km' ? 'ឥតគិតថ្លៃ' : 'Free'); ?></span>
        </div>
        <div class="total-final">
            <span><?php echo e(app()->getLocale()=='km' ? 'សរុប' : 'Total'); ?></span>
            <span class="total-amount">$<?php echo e(number_format($order->total_amount, 2)); ?></span>
        </div>

        <a href="<?php echo e(url('/my-orders/'.$order->id)); ?>" class="btn">
            <?php echo e(app()->getLocale()=='km' ? 'មើលការបញ្ជាទិញ' : 'View Order'); ?>

        </a>
    </div>

    <div class="footer">
        © <?php echo e(date('Y')); ?> RoboTech Academy. Siem Reap, Cambodia.<br>
        <?php echo e(app()->getLocale()=='km' ? 'សូមកុំឆ្លើយតបទៅ email នេះ' : 'Please do not reply to this email.'); ?>

    </div>
</div>
</body>
</html><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/emails/order-confirmation.blade.php ENDPATH**/ ?>