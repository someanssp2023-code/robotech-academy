
<?php $__env->startSection('title', app()->getLocale()=='km' ? 'មតិ' : 'Reviews'); ?>

<?php $__env->startSection('content'); ?>

<form method="GET" action="<?php echo e(route('admin.reviews')); ?>" class="panel p-3 mb-4">
    <div class="d-flex gap-2 align-items-center">
        <select name="status" class="form-ctrl" style="width:180px;" onchange="this.form.submit()">
            <option value=""><?php echo e(app()->getLocale()=='km' ? 'មតិទាំងអស់' : 'All Reviews'); ?></option>
            <option value="pending"  <?php echo e(request('status')=='pending'  ? 'selected' : ''); ?>><?php echo e(app()->getLocale()=='km' ? 'រង់ចាំការអនុម័ត' : 'Pending Approval'); ?></option>
            <option value="approved" <?php echo e(request('status')=='approved' ? 'selected' : ''); ?>><?php echo e(app()->getLocale()=='km' ? 'បានអនុម័ត' : 'Approved'); ?></option>
        </select>
        <?php if(request('status')): ?>
            <a href="<?php echo e(route('admin.reviews')); ?>" class="qa-btn"><?php echo e(app()->getLocale()=='km' ? 'លប់ចោល' : 'Clear'); ?></a>
        <?php endif; ?>
    </div>
</form>

<div class="panel">
    <div class="panel-head">
        <h6><?php echo e(app()->getLocale()=='km' ? 'មតិ' : 'Reviews'); ?> (<?php echo e($reviews->total()); ?>)</h6>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;"><?php echo e(app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ផលិតផល' : 'Product'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ការវាយតម្លៃ' : 'Rating'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'មតិ' : 'Comment'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                    <th style="padding-right:20px;text-align:right;"><?php echo e(app()->getLocale()=='km' ? 'សកម្មភាព' : 'Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="padding-left:20px;font-weight:600;font-size:13px;color:#111;"><?php echo e($review->user->name ?? '—'); ?></td>
                    <td style="font-size:12px;color:#374151;"><?php echo e(Str::limit($review->product->name ?? '—', 25)); ?></td>
                    <td><span style="font-size:13px;font-weight:600;color:#374151;"><?php echo e($review->rating); ?>/5</span></td>
                    <td style="font-size:12px;color:#6b7280;max-width:200px;"><?php echo e(Str::limit($review->comment ?? '—', 50)); ?></td>
                    <td style="font-size:12px;color:#9ca3af;"><?php echo e($review->created_at->format('M d, Y')); ?></td>
                    <td>
                        <span class="pill <?php echo e($review->is_approved ? 'pill-delivered' : 'pill-pending'); ?>">
                            <?php echo e($review->is_approved ? (app()->getLocale()=='km' ? 'បានអនុម័ត' : 'Approved') : (app()->getLocale()=='km' ? 'រង់ចាំ' : 'Pending')); ?>

                        </span>
                    </td>
                    <td style="padding-right:20px;text-align:right;">
                        <div class="d-flex gap-1 justify-content-end">
                            <?php if(!$review->is_approved): ?>
                            <form action="<?php echo e(route('admin.review.approve', $review->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="view-btn" style="border-color:#bbf7d0;color:#16a34a;background:#fff;">
                                    <?php echo e(app()->getLocale()=='km' ? 'អនុម័ត' : 'Approve'); ?>

                                </button>
                            </form>
                            <?php endif; ?>
                            <form action="<?php echo e(route('admin.review.delete', $review->id)); ?>" method="POST" onsubmit="return confirm('<?php echo e(app()->getLocale()=='km' ? 'លប់ចោលមតិ?' : 'Delete review?'); ?>')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="view-btn" style="border-color:#fecaca;color:#dc2626;background:#fff;">
                                    <?php echo e(app()->getLocale()=='km' ? 'លប់' : 'Delete'); ?>

                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនទាន់មានមតិ' : 'No reviews found.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($reviews->hasPages()): ?>
    <div class="p-3 border-top" style="border-color:#f3f4f6!important;">
        <?php echo e($reviews->withQueryString()->links('pagination::bootstrap-5')); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/admin/reviews.blade.php ENDPATH**/ ?>