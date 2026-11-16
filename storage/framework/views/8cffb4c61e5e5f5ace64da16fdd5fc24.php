
<?php $__env->startSection('title', app()->getLocale()=='km' ? 'លម្អិតការបញ្ជាទិញ' : 'Order Details'); ?>

<?php $__env->startSection('content'); ?>
<style>
.detail-wrap { max-width: 860px; margin: 0 auto; padding: 36px 16px 80px; }
.back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #64748b; text-decoration: none; margin-bottom: 20px; transition: color .15s; }
.back-link:hover { color: #0f172a; }
.page-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
.page-sub { font-size: 13px; color: #94a3b8; margin-bottom: 24px; }
.co-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; margin-bottom: 16px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
.co-card-head { padding: 14px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
.co-card-title { font-size: 13px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px; }
.co-card-body { padding: 20px; }
.info-row { display: flex; justify-content: space-between; align-items: center; font-size: 13px; padding: 8px 0; border-bottom: 1px solid #f9fafb; }
.info-row:last-child { border-bottom: none; }
.info-key { color: #64748b; }
.info-val { font-weight: 600; color: #0f172a; text-align: right; max-width: 60%; }
.pill { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.pill-pending    { background: #fef3c7; color: #92400e; }
.pill-processing { background: #dbeafe; color: #1e40af; }
.pill-shipped    { background: #e0f2fe; color: #0369a1; }
.pill-delivered  { background: #dcfce7; color: #166534; }
.pill-cancelled  { background: #fee2e2; color: #991b1b; }
.item-row { display: flex; align-items: center; gap: 14px; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
.item-row:last-child { border-bottom: none; }
.item-img { width: 60px; height: 60px; border-radius: 10px; object-fit: contain; background: #f8fafc; border: 1px solid #e2e8f0; padding: 6px; flex-shrink: 0; }
.item-name { font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 3px; }
.item-meta { font-size: 12px; color: #64748b; }
.item-total { font-size: 14px; font-weight: 800; color: #f59e0b; margin-left: auto; flex-shrink: 0; }
.sum-row { display: flex; justify-content: space-between; font-size: 13px; padding: 6px 0; color: #64748b; }
.sum-total { display: flex; justify-content: space-between; font-size: 16px; font-weight: 800; padding-top: 12px; margin-top: 8px; border-top: 2px solid #e2e8f0; }
.sum-total span:last-child { color: #f59e0b; }

/* Timeline */
.timeline { display: flex; flex-direction: column; gap: 0; }
.tl-item { display: flex; gap: 14px; }
.tl-left { display: flex; flex-direction: column; align-items: center; }
.tl-dot { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; flex-shrink: 0; }
.tl-dot.done { background: #dcfce7; color: #166534; }
.tl-dot.active { background: #f59e0b; color: #000; }
.tl-dot.pending { background: #f1f5f9; color: #94a3b8; }
.tl-line { width: 2px; flex: 1; background: #e2e8f0; margin: 4px 0; min-height: 24px; }
.tl-line.done { background: #10b981; }
.tl-content { padding-bottom: 24px; flex: 1; }
.tl-title { font-size: 13px; font-weight: 700; color: #0f172a; }
.tl-sub { font-size: 11px; color: #94a3b8; margin-top: 2px; }
.tl-item:last-child .tl-content { padding-bottom: 0; }
</style>

<div class="detail-wrap">

    <a href="<?php echo e(route('my.orders')); ?>" class="back-link">
        ← <?php echo e(app()->getLocale()=='km' ? 'ត្រឡប់ទៅការបញ្ជាទិញ' : 'Back to My Orders'); ?>

    </a>

    <div class="page-title">#RA-<?php echo e($order->id); ?></div>
    <div class="page-sub"><?php echo e($order->created_at->format('l, M d, Y h:i A')); ?></div>

    <div class="row g-4">

        
        <div class="col-lg-7">

            
            <div class="co-card">
                <div class="co-card-head">
                    <div class="co-card-title">

                        <?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Order Status'); ?>

                    </div>
                    <?php
                        $pillClass = match($order->status) {
                            'Processing' => 'pill-processing',
                            'Shipped'    => 'pill-shipped',
                            'Delivered'  => 'pill-delivered',
                            'Cancelled'  => 'pill-cancelled',
                            default      => 'pill-pending',
                        };
                        $steps = ['Pending','Processing','Shipped','Delivered'];
                        $currentStep = array_search($order->status, $steps);
                        if($currentStep === false) $currentStep = -1;
                    ?>
                    <span class="pill <?php echo e($pillClass); ?>"><?php echo e($order->status); ?></span>
                </div>
                <div class="co-card-body">
                    <div class="timeline">
                        <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isDone   = $i < $currentStep;
                            $isActive = $i === $currentStep;
                            $dotClass = $isDone ? 'done' : ($isActive ? 'active' : 'pending');
                            $lineClass = $isDone ? 'done' : '';
                        ?>
                        <div class="tl-item">
                            <div class="tl-left">
                                <div class="tl-dot <?php echo e($dotClass); ?>">
                                    <?php if($isDone): ?> ✓
                                    <?php elseif($isActive): ?> <?php echo e($i+1); ?>

                                    <?php else: ?> <?php echo e($i+1); ?>

                                    <?php endif; ?>
                                </div>
                                <?php if(!$loop->last): ?>
                                <div class="tl-line <?php echo e($lineClass); ?>"></div>
                                <?php endif; ?>
                            </div>
                            <div class="tl-content">
                                <div class="tl-title" style="<?php echo e($isActive ? 'color:#f59e0b;' : ''); ?>">
                                    <?php if($step === 'Pending'): ?> <?php echo e(app()->getLocale()=='km' ? 'ការបញ្ជាទិញ' : 'Order Placed'); ?>

                                    <?php elseif($step === 'Processing'): ?> <?php echo e(app()->getLocale()=='km' ? 'កំពុងដំណើរការ' : 'Processing'); ?>

                                    <?php elseif($step === 'Shipped'): ?> <?php echo e(app()->getLocale()=='km' ? 'កំពុងដឹក' : 'Shipped'); ?>

                                    <?php else: ?> <?php echo e(app()->getLocale()=='km' ? 'បានទទួល' : 'Delivered'); ?>

                                    <?php endif; ?>
                                </div>
                                <div class="tl-sub">
                                    <?php if($isDone): ?> ✅ <?php echo e(app()->getLocale()=='km' ? 'បានបញ្ចប់' : 'Completed'); ?>

                                    <?php elseif($isActive): ?> ⏳ <?php echo e(app()->getLocale()=='km' ? 'ដំណើរការ' : 'In progress'); ?>

                                    <?php else: ?> <?php echo e(app()->getLocale()=='km' ? 'រង់ចាំ' : 'Pending'); ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            
            <div class="co-card">
                <div class="co-card-head">
                    <div class="co-card-title">

                        <?php echo e(app()->getLocale()=='km' ? 'ទំនិញ' : 'Items'); ?>

                    </div>
                    <span style="font-size:12px;color:#94a3b8;"><?php echo e($order->orderItems->count()); ?> <?php echo e(app()->getLocale()=='km' ? 'មុខ' : 'items'); ?></span>
                </div>
                <div class="co-card-body">
                    <?php $__currentLoopData = $order->orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="item-row">
                        <img class="item-img"
                             src="<?php echo e($item->product->image_url ?? ''); ?>"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2256%22 height=%2256%22%3E%3Crect width=%2256%22 height=%2256%22 fill=%22%23f3f4f6%22/%3E%3C/svg%3E'"
                             alt="<?php echo e($item->product_name); ?>"/>
                        <div style="flex:1;min-width:0;">
                            <div class="item-name"><?php echo e($item->product_name); ?></div>
                            <div class="item-meta">
                                x<?php echo e($item->quantity); ?> × $<?php echo e(number_format($item->unit_price, 2)); ?>

                            </div>
                        </div>
                        <div class="item-total">$<?php echo e(number_format($item->subtotal, 2)); ?></div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

        </div>

        
        <div class="col-lg-5">

            
            <div class="co-card">
                <div class="co-card-head">
                    <div class="co-card-title">

                        <?php echo e(app()->getLocale()=='km' ? 'ព័ត៌មានការបញ្ជាទិញ' : 'Order Info'); ?>

                    </div>
                </div>
                <div class="co-card-body">
                    <div class="info-row">
                        <span class="info-key"><?php echo e(app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payment'); ?></span>
                        <span class="info-val"><?php echo e($order->payment_method); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-key"><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាពទូទាត់' : 'Payment Status'); ?></span>
                        <span class="pill <?php echo e($order->payment_status==='Paid' ? 'pill-delivered' : 'pill-pending'); ?>">
                            <?php echo e($order->payment_status); ?>

                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-key"><?php echo e(app()->getLocale()=='km' ? 'ទូរស័ព្ទ' : 'Phone'); ?></span>
                        <span class="info-val"><?php echo e($order->phone ?? '—'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-key"><?php echo e(app()->getLocale()=='km' ? 'អាសយដ្ឋាន' : 'Address'); ?></span>
                        <span class="info-val"><?php echo e($order->shipping_address); ?></span>
                    </div>
                    <?php if($order->notes): ?>
                    <div class="info-row">
                        <span class="info-key"><?php echo e(app()->getLocale()=='km' ? 'កំណត់ចំណាំ' : 'Notes'); ?></span>
                        <span class="info-val"><?php echo e($order->notes); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="co-card">
                <div class="co-card-head">
                    <div class="co-card-title">

                        <?php echo e(app()->getLocale()=='km' ? 'សង្ខេបតម្លៃ' : 'Price Summary'); ?>

                    </div>
                </div>
                <div class="co-card-body">
                    <div class="sum-row">
                        <span><?php echo e(app()->getLocale()=='km' ? 'តម្លៃរង' : 'Subtotal'); ?></span>
                        <span>$<?php echo e(number_format($order->subtotal ?? $order->total_amount, 2)); ?></span>
                    </div>
                    <?php if(($order->discount ?? 0) > 0): ?>
                    <div class="sum-row" style="color:#10b981;">
                        <span><?php echo e(app()->getLocale()=='km' ? 'បញ្ចុះតម្លៃ' : 'Discount'); ?></span>
                        <span>-$<?php echo e(number_format($order->discount, 2)); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if(($order->tax_amount ?? 0) > 0): ?>
                    <div class="sum-row" style="color:#6366f1;">
                        <span>🧾 <?php echo e(app()->getLocale()=='km' ? 'ពន្ធ' : 'Tax'); ?> (<?php echo e(number_format($order->tax_rate, 0)); ?>%)</span>
                        <span>+$<?php echo e(number_format($order->tax_amount, 2)); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="sum-row">
                        <span><?php echo e(app()->getLocale()=='km' ? 'ដឹកជញ្ជូន' : 'Shipping'); ?></span>
                        <span style="color:#10b981;font-weight:600;"><?php echo e(app()->getLocale()=='km' ? 'ឥតគិតថ្លៃ' : 'Free'); ?></span>
                    </div>
                    <div class="sum-total">
                        <span><?php echo e(app()->getLocale()=='km' ? 'សរុប' : 'Total'); ?></span>
                        <span>$<?php echo e(number_format($order->total_amount, 2)); ?></span>
                    </div>
                </div>
            </div>

            
            <div style="display:flex;flex-direction:column;gap:8px;">
                <a href="<?php echo e(route('products')); ?>" style="display:flex;align-items:center;justify-content:center;gap:8px;background:#0f172a;color:#fff;text-decoration:none;padding:13px;border-radius:10px;font-size:14px;font-weight:700;transition:all .2s;" onmouseover="this.style.background='#f59e0b';this.style.color='#000'" onmouseout="this.style.background='#0f172a';this.style.color='#fff'">
                    <?php echo e(app()->getLocale()=='km' ? 'ទិញបន្ថែម' : 'Continue Shopping'); ?>

                </a>
                <a href="<?php echo e(route('my.orders')); ?>" style="display:flex;align-items:center;justify-content:center;gap:8px;background:transparent;color:#374151;text-decoration:none;padding:12px;border-radius:10px;font-size:13px;font-weight:600;border:1.5px solid #e2e8f0;transition:all .2s;">
                    ← <?php echo e(app()->getLocale()=='km' ? 'ការបញ្ជាទិញទាំងអស់' : 'back'); ?>

                </a>
            </div>
            

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/orders/detail.blade.php ENDPATH**/ ?>