
<?php $__env->startSection('title', app()->getLocale()=='km' ? 'គូប៉ុង' : 'Coupons'); ?>

<?php $__env->startSection('content'); ?>

<div class="d-flex justify-content-end mb-4">
    <a href="<?php echo e(route('admin.coupon.create')); ?>" class="qa-btn qa-primary">+ <?php echo e(app()->getLocale()=='km' ? 'បន្ថែមគូប៉ុង' : 'Add Coupon'); ?></a>
</div>

<div class="panel">
    <div class="panel-head">
        <h6><?php echo e(app()->getLocale()=='km' ? 'គូប៉ុង' : 'Coupons'); ?> (<?php echo e($coupons->count()); ?>)</h6>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;"><?php echo e(app()->getLocale()=='km' ? 'លេខកូដ' : 'Code'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទ' : 'Type'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'តម្លៃ' : 'Value'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'អប្បបរមា' : 'Min Order'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ការប្រើ' : 'Uses'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ផុតកំណត់' : 'Expires'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                    <th style="padding-right:20px;text-align:right;"><?php echo e(app()->getLocale()=='km' ? 'សកម្មភាព' : 'Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="padding-left:20px;">
                        <span style="font-family:monospace;font-weight:700;font-size:13px;background:#f3f4f6;padding:4px 10px;border-radius:6px;letter-spacing:1px;">
                            <?php echo e($coupon->code); ?>

                        </span>
                    </td>
                    <td>
                        <span class="pill <?php echo e($coupon->type==='percent' ? 'pill-processing' : 'pill-shipped'); ?>">
                            <?php echo e($coupon->type==='percent' ? (app()->getLocale()=='km' ? 'ភាគរយ' : 'Percent') : (app()->getLocale()=='km' ? 'ថេរ' : 'Fixed')); ?>

                        </span>
                    </td>
                    <td style="font-weight:600;color:#374151;">
                        <?php echo e($coupon->type==='percent' ? $coupon->value.'%' : '$'.number_format($coupon->value, 2)); ?>

                    </td>
                    <td style="font-size:13px;color:#374151;">$<?php echo e(number_format($coupon->min_order, 2)); ?></td>
                    <td style="font-size:13px;color:#374151;">
                        <?php echo e($coupon->used_count); ?>

                        <?php if($coupon->max_uses): ?> / <?php echo e($coupon->max_uses); ?> <?php else: ?> / ∞ <?php endif; ?>
                    </td>
                    <td style="font-size:12px;color:#6b7280;">
                        <?php echo e($coupon->expires_at ? $coupon->expires_at->format('M d, Y') : '—'); ?>

                        <?php if($coupon->expires_at && $coupon->expires_at->isPast()): ?>
                            <span class="pill pill-cancelled" style="font-size:10px;margin-left:4px;">
                                <?php echo e(app()->getLocale()=='km' ? 'ផុតកំណត់' : 'Expired'); ?>

                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="pill <?php echo e($coupon->is_active ? 'pill-delivered' : 'pill-cancelled'); ?>">
                            <?php echo e($coupon->is_active ? (app()->getLocale()=='km' ? 'សកម្ម' : 'Active') : (app()->getLocale()=='km' ? 'អសកម្ម' : 'Inactive')); ?>

                        </span>
                    </td>
                    <td style="padding-right:20px;text-align:right;">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="<?php echo e(route('admin.coupon.edit', $coupon->id)); ?>" class="view-btn"><?php echo e(app()->getLocale()=='km' ? 'កែ' : 'Edit'); ?></a>
                            <form action="<?php echo e(route('admin.coupon.delete', $coupon->id)); ?>" method="POST" onsubmit="return confirm('<?php echo e(app()->getLocale()=='km' ? 'លប់ចោលគូប៉ុង?' : 'Delete this coupon?'); ?>')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="view-btn" style="border-color:#fecaca;color:#dc2626;background:#fff;"><?php echo e(app()->getLocale()=='km' ? 'លប់' : 'Delete'); ?></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនទាន់មានគូប៉ុង' : 'No coupons yet.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/admin/coupons.blade.php ENDPATH**/ ?>