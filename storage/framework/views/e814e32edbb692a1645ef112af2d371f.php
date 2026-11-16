
<?php $__env->startSection('title', app()->getLocale()=='km' ? 'ការបញ្ជាទិញ' : 'Orders'); ?>

<?php $__env->startSection('content'); ?>

<form method="GET" action="<?php echo e(route('admin.orders')); ?>" class="panel p-3 mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <input type="text" name="search" class="form-ctrl" style="width:200px;"
               placeholder="<?php echo e(app()->getLocale()=='km' ? 'លេខការបញ្ជាទិញ...' : 'Order number...'); ?>"
               value="<?php echo e(request('search')); ?>"/>
        <select name="status" class="form-ctrl" style="width:160px;" onchange="this.form.submit()">
            <option value=""><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាពទាំងអស់' : 'All Statuses'); ?></option>
            <?php $__currentLoopData = ['Pending'=>(app()->getLocale()=='km'?'រង់ចាំ':'Pending'),'Processing'=>(app()->getLocale()=='km'?'កំពុងដំណើរការ':'Processing'),'Shipped'=>(app()->getLocale()=='km'?'កំពុងដឹក':'Shipped'),'Delivered'=>(app()->getLocale()=='km'?'បានដឹកជញ្ជូន':'Delivered'),'Cancelled'=>(app()->getLocale()=='km'?'បានបោះបង់':'Cancelled')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($val); ?>" <?php echo e(request('status') == $val ? 'selected' : ''); ?>><?php echo e($lbl); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="payment_status" class="form-ctrl" style="width:140px;" onchange="this.form.submit()">
            <option value=""><?php echo e(app()->getLocale()=='km' ? 'ការទូទាត់ទាំងអស់' : 'All Payments'); ?></option>
            <option value="Unpaid" <?php echo e(request('payment_status')=='Unpaid' ? 'selected' : ''); ?>><?php echo e(app()->getLocale()=='km' ? 'មិនទាន់បង់' : 'Unpaid'); ?></option>
            <option value="Paid"   <?php echo e(request('payment_status')=='Paid'   ? 'selected' : ''); ?>><?php echo e(app()->getLocale()=='km' ? 'បានបង់' : 'Paid'); ?></option>
        </select>
        
        <input type="date" name="date_from" class="form-ctrl" style="width:160px;"
               value="<?php echo e(request('date_from')); ?>"/>
        <span style="color:#6b7280;font-weight:600;font-size:13px;"><?php echo e(app()->getLocale()=='km' ? 'ដល់' : 'To'); ?></span>
        <input type="date" name="date_to" class="form-ctrl" style="width:160px;"
               value="<?php echo e(request('date_to')); ?>"/>
        <button type="submit" class="qa-btn qa-primary"><?php echo e(app()->getLocale()=='km' ? 'ស្វែងរក' : 'Search'); ?></button>
        <?php if(request()->hasAny(['search','status','payment_status','date_from','date_to'])): ?>
            <a href="<?php echo e(route('admin.orders')); ?>" class="qa-btn"><?php echo e(app()->getLocale()=='km' ? 'លប់ចោល' : 'Clear'); ?></a>
        <?php endif; ?>
    </div>
</form>

<div class="panel">
    <div class="panel-head">
        <h6><?php echo e(app()->getLocale()=='km' ? 'ការបញ្ជាទិញ' : 'Orders'); ?> (<?php echo e($orders->total()); ?>)</h6>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('admin.orders.export.excel', request()->all())); ?>"
               class="qa-btn" style="border-color:#16a34a;color:#16a34a;">⬇ Excel</a>
            <a href="<?php echo e(route('admin.orders.export.pdf', request()->all())); ?>"
               target="_blank"
               class="qa-btn" style="border-color:#dc2626;color:#dc2626;">⬇ PDF</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;"><?php echo e(app()->getLocale()=='km' ? 'លេខ' : 'No'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'មុខ' : 'Items'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'សរុប' : 'Total'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payment'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                    <th style="padding-right:20px;text-align:right;"><?php echo e(app()->getLocale()=='km' ? 'សកម្មភាព' : 'Action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $sc = match($order->status) {
                        'Delivered'=>'pill-delivered','Cancelled'=>'pill-cancelled',
                        'Shipped'=>'pill-shipped','Processing'=>'pill-processing',default=>'pill-pending'
                    };
                    $statusLabels = ['Pending'=>app()->getLocale()=='km'?'រង់ចាំ':'Pending','Processing'=>app()->getLocale()=='km'?'កំពុងដំណើរការ':'Processing','Shipped'=>app()->getLocale()=='km'?'កំពុងដឹក':'Shipped','Delivered'=>app()->getLocale()=='km'?'បានដឹកជញ្ជូន':'Delivered','Cancelled'=>app()->getLocale()=='km'?'បានបោះបង់':'Cancelled'];
                ?>
                <tr>
                    <td style="padding-left:20px;font-weight:700;color:#111;font-size:13px;">RA-<?php echo e($order->id); ?></td>
                    <td>
                        <div style="font-weight:600;font-size:13px;color:#111;"><?php echo e($order->user->name); ?></div>
                        <div style="color:#9ca3af;font-size:11px;"><?php echo e($order->user->email); ?></div>
                    </td>
                    <td style="color:#6b7280;font-size:12px;"><?php echo e($order->created_at->format('M d, Y')); ?></td>
                    <td style="font-size:13px;color:#374151;"><?php echo e($order->orderItems->count()); ?></td>
                    <td style="font-weight:600;color:#374151;">$<?php echo e(number_format($order->total_amount, 2)); ?></td>
                    <td>
                        <div style="font-size:12px;color:#6b7280;margin-bottom:3px;"><?php echo e($order->payment_method); ?></div>
                        <span class="pill <?php echo e($order->payment_status==='Paid' ? 'pill-delivered' : 'pill-pending'); ?>">
                            <?php echo e($order->payment_status==='Paid' ? (app()->getLocale()=='km'?'បានបង់':'Paid') : (app()->getLocale()=='km'?'មិនទាន់បង់':'Unpaid')); ?>

                        </span>
                    </td>
                    <td><span class="pill <?php echo e($sc); ?>"><?php echo e($statusLabels[$order->status] ?? $order->status); ?></span></td>
                    <td style="padding-right:20px;text-align:right;">
                        <a href="<?php echo e(route('admin.order.detail', $order->id)); ?>" class="view-btn"><?php echo e(app()->getLocale()=='km' ? 'មើល' : 'View'); ?></a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនទាន់មានការបញ្ជាទិញ' : 'No orders found.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($orders->hasPages()): ?>
    <div class="p-3 border-top" style="border-color:#f3f4f6!important;">
        <?php echo e($orders->withQueryString()->links('pagination::bootstrap-5')); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/admin/orders.blade.php ENDPATH**/ ?>