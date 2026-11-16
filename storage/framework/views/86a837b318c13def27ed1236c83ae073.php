<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f1f5f9; }
        .wrap { max-width: 580px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: #0f172a; padding: 24px 32px; border-bottom: 3px solid #f59e0b; }
        .brand { font-size: 18px; font-weight: 800; color: #fff; }
        .alert { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 14px 20px; margin: 24px 32px; border-radius: 0 8px 8px 0; }
        .alert-title { font-size: 15px; font-weight: 700; color: #92400e; }
        .body { padding: 0 32px 32px; }
        .customer-box { background: #f8fafc; border-radius: 10px; padding: 16px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
        .section-title { font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 10px; }
        .info-row { display: flex; justify-content: space-between; font-size: 13px; padding: 5px 0; }
        .info-key { color: #64748b; }
        .info-val { font-weight: 700; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; padding: 8px; background: #f8fafc; text-align: left; }
        td { font-size: 13px; padding: 10px 8px; border-bottom: 1px solid #f1f5f9; color: #374151; }
        .total-box { background: #0f172a; border-radius: 10px; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; }
        .total-label { color: #94a3b8; font-size: 13px; font-weight: 600; }
        .total-amount { color: #f59e0b; font-size: 22px; font-weight: 900; }
        .btn { display: block; width: fit-content; margin: 20px auto 0; background: #f59e0b; color: #000; text-decoration: none; padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 800; }
        .footer { background: #f8fafc; padding: 16px 32px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <div class="brand">🤖 RoboTech — Admin Alert</div>
    </div>

    <div class="alert">
        <div class="alert-title">🛒 ការបញ្ជាទិញថ្មី #RA-<?php echo e($order->id); ?></div>
        <div style="font-size:12px;color:#92400e;margin-top:4px;"><?php echo e($order->created_at->format('M d, Y h:i A')); ?></div>
    </div>

    <div class="body">
        
        <div class="section-title">ព័ត៌មានអតិថិជន</div>
        <div class="customer-box" style="margin-bottom:20px;">
            <div class="info-row">
                <span class="info-key">ឈ្មោះ</span>
                <span class="info-val"><?php echo e($order->user->name); ?></span>
            </div>
            <div class="info-row">
                <span class="info-key">Email</span>
                <span class="info-val"><?php echo e($order->user->email); ?></span>
            </div>
            <div class="info-row">
                <span class="info-key">ទូរស័ព្ទ</span>
                <span class="info-val"><?php echo e($order->user->phone ?? '—'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-key">អាសយដ្ឋាន</span>
                <span class="info-val"><?php echo e($order->shipping_address); ?></span>
            </div>
            <div class="info-row">
                <span class="info-key">ការទូទាត់</span>
                <span class="info-val"><?php echo e($order->payment_method); ?></span>
            </div>
        </div>

        
        <div class="section-title">ទំនិញ</div>
        <table>
            <thead>
                <tr>
                    <th>ផលិតផល</th>
                    <th style="text-align:center;">ចំនួន</th>
                    <th style="text-align:right;">តម្លៃ</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $order->orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($item->product_name); ?></td>
                    <td style="text-align:center;"><?php echo e($item->quantity); ?></td>
                    <td style="text-align:right;font-weight:700;">$<?php echo e(number_format($item->subtotal, 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        
        <div class="total-box">
            <span class="total-label">សរុប / Total</span>
            <span class="total-amount">$<?php echo e(number_format($order->total_amount, 2)); ?></span>
        </div>

        <a href="<?php echo e(url('/admin/orders/'.$order->id)); ?>" class="btn">
            គ្រប់គ្រងការបញ្ជាទិញ →
        </a>
    </div>

    <div class="footer">
        RoboTech Academy Admin System — <?php echo e(now()->format('Y')); ?>

    </div>
</div>
</body>
</html><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/emails/admin-new-order.blade.php ENDPATH**/ ?>