
<?php $__env->startSection('title', app()->getLocale()=='km' ? 'ការបញ្ជាទិញរបស់ខ្ញុំ' : 'My Orders'); ?>

<?php $__env->startSection('content'); ?>
<style>
/* ── Layout ── */
.mo-wrap  { max-width: 860px; margin: 0 auto; padding: 40px 16px 100px; }
.mo-header { display: flex; align-items: center; justify-content: space-between;
             margin-bottom: 28px; flex-wrap: wrap; gap: 12px; }
.mo-title  { font-size: 22px; font-weight: 900; color: #0f172a; letter-spacing: -.3px; }
.mo-count  { font-size: 13px; color: #94a3b8; font-weight: 600; }

/* ── Order card ── */
.oc { background: #fff; border-radius: 18px; border: 1px solid #e2e8f0;
      margin-bottom: 16px; overflow: hidden;
      box-shadow: 0 1px 4px rgba(0,0,0,.04);
      transition: box-shadow .2s, transform .2s; }
.oc:hover { box-shadow: 0 6px 24px rgba(0,0,0,.09); transform: translateY(-1px); }

/* ── Header ── */
.oc-head { padding: 14px 20px; display: flex; align-items: center;
           justify-content: space-between; flex-wrap: wrap; gap: 10px;
           background: #fafafa; border-bottom: 1px solid #f1f5f9; }
.oc-id   { font-size: 15px; font-weight: 900; color: #f59e0b;
           background: #fffbeb; border: 1.5px solid #fde68a;
           border-radius: 8px; padding: 2px 10px; letter-spacing: .2px; }
.oc-date { font-size: 11px; color: #94a3b8; margin-top: 4px; }
.oc-pills { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }

/* ── Items body ── */
.oc-body { padding: 14px 20px; }
.oi { display: flex; align-items: center; gap: 12px; padding: 8px 0; border-bottom: 1px solid #f8fafc; }
.oi:last-child { border-bottom: none; }
.oi-img  { width: 50px; height: 50px; border-radius: 10px; object-fit: contain;
           background: #f8fafc; border: 1px solid #e2e8f0; padding: 5px; flex-shrink: 0; }
.oi-name { font-size: 13px; font-weight: 700; color: #0f172a; flex: 1; min-width: 0; }
.oi-qty  { font-size: 11px; color: #94a3b8; margin-top: 3px; }
.oi-price{ font-size: 13px; font-weight: 800; color: #f59e0b; white-space: nowrap; }
.oi-more { font-size: 12px; color: #94a3b8; text-align: center; padding: 6px 0; }

/* ── Footer ── */
.oc-foot { padding: 14px 20px; background: #f8fafc; border-top: 1px solid #f1f5f9;
           display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
.oc-pay  { font-size: 11px; color: #64748b; margin-bottom: 3px; }
.oc-total{ font-size: 16px; font-weight: 900; color: #0f172a; }
.oc-total span { color: #f59e0b; }
.btn-det { display: inline-flex; align-items: center; gap: 6px;
           background: #0f172a; color: #fff; text-decoration: none;
           padding: 9px 20px; border-radius: 10px; font-size: 12px; font-weight: 700;
           transition: all .2s; }
.btn-det:hover { background: #f59e0b; color: #000; transform: translateY(-1px); }

/* ── Pills ── */
.pill { display: inline-block; padding: 3px 11px; border-radius: 20px; font-size: 11px; font-weight: 800; }
.pill-pending    { background: #fef3c7; color: #92400e; }
.pill-processing { background: #dbeafe; color: #1e40af; }
.pill-shipped    { background: #e0f2fe; color: #0369a1; }
.pill-delivered  { background: #dcfce7; color: #166534; }
.pill-cancelled  { background: #fee2e2; color: #991b1b; }

/* ── Empty state ── */
.empty { text-align: center; padding: 80px 20px; }
.empty-icon  { font-size: 72px; margin-bottom: 16px; opacity: .7; }
.empty-title { font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
.empty-sub   { font-size: 14px; color: #94a3b8; margin-bottom: 28px; }
.btn-shop { display: inline-flex; align-items: center; gap: 8px;
            background: #0f172a; color: #fff; text-decoration: none;
            padding: 13px 30px; border-radius: 12px; font-size: 14px; font-weight: 700;
            transition: all .2s; }
.btn-shop:hover { background: #f59e0b; color: #000; transform: translateY(-2px); }
</style>

<div class="mo-wrap">

    <div class="mo-header">
        <div>
            <div class="mo-title">
                <?php echo e(app()->getLocale()=='km' ? 'ការបញ្ជាទិញរបស់ខ្ញុំ' : 'My Orders'); ?>

            </div>
            <?php if(!$orders->isEmpty()): ?>
            <div class="mo-count">
                <?php echo e($orders->total()); ?> <?php echo e(app()->getLocale()=='km' ? 'ការបញ្ជាទិញ' : 'orders total'); ?>

            </div>
            <?php endif; ?>
        </div>
        <a href="<?php echo e(route('products')); ?>" class="btn-shop" style="padding:9px 20px;font-size:13px;">
            🛒 <?php echo e(app()->getLocale()=='km' ? 'ទិញបន្ថែម' : 'Shop More'); ?>

        </a>
    </div>

    <?php if($orders->isEmpty()): ?>
    <div class="empty">
        <div class="empty-icon">🛍️</div>
        <div class="empty-title"><?php echo e(app()->getLocale()=='km' ? 'មិនទាន់មានការបញ្ជាទិញ' : 'No orders yet'); ?></div>
        <div class="empty-sub"><?php echo e(app()->getLocale()=='km' ? 'ចាប់ផ្តើមទិញផលិតផលដំបូងរបស់អ្នក!' : 'Start shopping and your orders will appear here.'); ?></div>
        <a href="<?php echo e(route('products')); ?>" class="btn-shop">
            🛒 <?php echo e(app()->getLocale()=='km' ? 'ទៅទិញទំនិញ' : 'Shop Now'); ?>

        </a>
    </div>
    <?php else: ?>

    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $pillClass = match($order->status) {
            'Processing' => 'pill-processing',
            'Shipped'    => 'pill-shipped',
            'Delivered'  => 'pill-delivered',
            'Cancelled'  => 'pill-cancelled',
            default      => 'pill-pending',
        };
    ?>
    <div class="oc">

        
        <div class="oc-head">
            <div>
                <div class="oc-id">#RA-<?php echo e($order->id); ?></div>
                <div class="oc-date"><?php echo e($order->created_at->format('M d, Y  h:i A')); ?></div>
            </div>
            <div class="oc-pills">
                <span class="pill <?php echo e($pillClass); ?>"><?php echo e($order->status); ?></span>
                <?php if($order->payment_status === 'Paid'): ?>
                    <span class="pill pill-delivered"><?php echo e(app()->getLocale()=='km' ? 'បានបង់' : 'Paid'); ?></span>
                <?php else: ?>
                    <span class="pill pill-pending"><?php echo e(app()->getLocale()=='km' ? 'មិនទាន់បង់' : 'Unpaid'); ?></span>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="oc-body">
            <?php $__currentLoopData = $order->orderItems->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="oi">
                <img class="oi-img"
                     src="<?php echo e($item->product->image_url ?? ''); ?>"
                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2248%22 height=%2248%22%3E%3Crect width=%2248%22 height=%2248%22 fill=%22%23f3f4f6%22/%3E%3C/svg%3E'"
                     alt="<?php echo e($item->product_name); ?>"/>
                <div style="flex:1;min-width:0;">
                    <div class="oi-name"><?php echo e(Str::limit($item->product_name, 38)); ?></div>
                    <div class="oi-qty">x<?php echo e($item->quantity); ?> × $<?php echo e(number_format($item->unit_price,2)); ?></div>
                </div>
                <div class="oi-price">$<?php echo e(number_format($item->subtotal,2)); ?></div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($order->orderItems->count() > 3): ?>
            <div class="oi-more">
                +<?php echo e($order->orderItems->count() - 3); ?> <?php echo e(app()->getLocale()=='km' ? 'មុខទៀត' : 'more items'); ?>

            </div>
            <?php endif; ?>
        </div>

        
        <div class="oc-foot">
            <div>
                <div class="oc-pay"><?php echo e(app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payment'); ?>: <?php echo e($order->payment_method); ?></div>
                <div class="oc-total">
                    <?php echo e(app()->getLocale()=='km' ? 'សរុប' : 'Total'); ?>:
                    <span>$<?php echo e(number_format($order->total_amount,2)); ?></span>
                </div>
            </div>
            <a href="<?php echo e(route('order.detail', $order->id)); ?>" class="btn-det">
                <?php echo e(app()->getLocale()=='km' ? 'មើលលម្អិត' : 'View Details'); ?>

                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <?php if($orders->hasPages()): ?>
    <div class="mt-4"><?php echo e($orders->links('pagination::bootstrap-5')); ?></div>
    <?php endif; ?>

    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/orders/my-orders.blade.php ENDPATH**/ ?>