
<?php $__env->startSection('title', app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payments'); ?>

<?php $__env->startSection('content'); ?>

<form method="GET" action="<?php echo e(route('admin.payments')); ?>" class="panel p-3 mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <select name="status" class="form-ctrl" style="width:180px;" onchange="this.form.submit()">
            <option value=""><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាពទាំងអស់' : 'All Statuses'); ?></option>
            <?php $__currentLoopData = ['Pending'=>(app()->getLocale()=='km'?'រង់ចាំ':'Pending'),'Completed'=>(app()->getLocale()=='km'?'បានបញ្ចប់':'Completed'),'Failed'=>(app()->getLocale()=='km'?'បរាជ័យ':'Failed'),'Refunded'=>(app()->getLocale()=='km'?'បានសង':'Refunded')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($val); ?>" <?php echo e(request('status')==$val ? 'selected' : ''); ?>><?php echo e($lbl); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        
        <input type="date" name="date_from" class="form-ctrl" style="width:160px;"
               value="<?php echo e(request('date_from')); ?>"/>
        <span style="color:#6b7280;font-weight:600;font-size:13px;"><?php echo e(app()->getLocale()=='km' ? 'ដល់' : 'To'); ?></span>
        <input type="date" name="date_to" class="form-ctrl" style="width:160px;"
               value="<?php echo e(request('date_to')); ?>"/>
        <button type="submit" class="qa-btn qa-primary"><?php echo e(app()->getLocale()=='km' ? 'ស្វែងរក' : 'Search'); ?></button>
        <?php if(request()->hasAny(['status','date_from','date_to'])): ?>
            <a href="<?php echo e(route('admin.payments')); ?>" class="qa-btn"><?php echo e(app()->getLocale()=='km' ? 'លប់ចោល' : 'Clear'); ?></a>
        <?php endif; ?>
    </div>
</form>

<div class="panel">
    <div class="panel-head">
        <h6><?php echo e(app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payments'); ?> (<?php echo e($payments->total()); ?>)</h6>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('admin.payments.export.excel', request()->all())); ?>"
               class="qa-btn" style="border-color:#16a34a;color:#16a34a;">⬇ Excel</a>
            <a href="<?php echo e(route('admin.payments.export.pdf', request()->all())); ?>"
               target="_blank"
               class="qa-btn" style="border-color:#dc2626;color:#dc2626;">⬇ PDF</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;">No</th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ការបញ្ជាទិញ' : 'Order'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ចំនួន' : 'Amount'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'វិធី' : 'Method'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $sc = match($payment->status){'Completed'=>'pill-delivered','Failed'=>'pill-cancelled','Refunded'=>'pill-shipped',default=>'pill-pending'};
                    $statusLabels = ['Pending'=>app()->getLocale()=='km'?'រង់ចាំ':'Pending','Completed'=>app()->getLocale()=='km'?'បានបញ្ចប់':'Completed','Failed'=>app()->getLocale()=='km'?'បរាជ័យ':'Failed','Refunded'=>app()->getLocale()=='km'?'បានសង':'Refunded'];
                ?>
                <tr>
                    <td style="padding-left:20px;font-family:monospace;font-size:12px;color:#9ca3af;">
                        <?php echo e(($payments->currentPage()-1)*$payments->perPage()+$loop->index+1); ?>

                    </td>
                    <td style="font-weight:700;font-size:13px;color:#111;">RA-<?php echo e($payment->order->id ?? '—'); ?></td>
                    <td>
                        <div style="font-weight:600;font-size:13px;color:#111;"><?php echo e($payment->user->name); ?></div>
                        <div style="color:#9ca3af;font-size:11px;"><?php echo e($payment->user->email); ?></div>
                    </td>
                    <td style="font-weight:600;color:#374151;">$<?php echo e(number_format($payment->amount, 2)); ?></td>
                    <td style="font-size:12px;color:#6b7280;"><?php echo e($payment->method); ?></td>
                    <td><span class="pill <?php echo e($sc); ?>"><?php echo e($statusLabels[$payment->status] ?? $payment->status); ?></span></td>
                    <td style="font-size:12px;color:#9ca3af;"><?php echo e($payment->created_at->format('M d, Y')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនទាន់មានការទូទាត់' : 'No payments found.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($payments->hasPages()): ?>
    <div class="p-3 border-top" style="border-color:#f3f4f6!important;">
        <?php echo e($payments->withQueryString()->links('pagination::bootstrap-5')); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/admin/payments.blade.php ENDPATH**/ ?>