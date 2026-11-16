
<?php $__env->startSection('title', app()->getLocale()=='km' ? 'គ្រប់គ្រងស្តុក' : 'Manage Stock'); ?>

<?php $__env->startSection('content'); ?>


<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="stat-lbl"><?php echo e(app()->getLocale()=='km' ? 'ស្តុកសរុប' : 'Total Units in Stock'); ?></div>
            <div class="stat-val"><?php echo e(number_format($totalStock)); ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="stat-lbl"><?php echo e(app()->getLocale()=='km' ? 'ស្តុកទាប (< 10)' : 'Low Stock (< 10)'); ?></div>
            <div class="stat-val"><?php echo e($lowStock); ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="stat-lbl"><?php echo e(app()->getLocale()=='km' ? 'អស់ស្តុក' : 'Out of Stock'); ?></div>
            <div class="stat-val"><?php echo e($outOfStock); ?></div>
        </div>
    </div>
</div>


<form method="GET" action="<?php echo e(route('admin.stock')); ?>" class="panel p-3 mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <input type="text" name="search" class="form-ctrl" style="width:220px;"
               placeholder="<?php echo e(app()->getLocale()=='km' ? 'ស្វែងរកផលិតផល...' : 'Search products...'); ?>"
               value="<?php echo e(request('search')); ?>"/>

        
        <div class="d-flex align-items-center gap-1">
            <span style="font-size:12px;color:#6b7280;white-space:nowrap;"><?php echo e(app()->getLocale()=='km' ? 'ពីថ្ងៃ' : 'From'); ?></span>
            <input type="date" name="date_from" class="form-ctrl" style="width:150px;" value="<?php echo e(request('date_from')); ?>">
        </div>
        <div class="d-flex align-items-center gap-1">
            <span style="font-size:12px;color:#6b7280;white-space:nowrap;"><?php echo e(app()->getLocale()=='km' ? 'ដល់ថ្ងៃ' : 'To'); ?></span>
            <input type="date" name="date_to" class="form-ctrl" style="width:150px;" value="<?php echo e(request('date_to')); ?>">
        </div>

        
        <a href="<?php echo e(route('admin.stock.export.excel', request()->query())); ?>"
           class="qa-btn" style="border-color:#16a34a;color:#16a34a;">
             Excel
        </a>
        <a href="<?php echo e(route('admin.stock.export.pdf', request()->query())); ?>"
           target="_blank"
           class="qa-btn" style="border-color:#dc2626;color:#dc2626;">
             PDF
        </a>

        <a href="<?php echo e(route('admin.stock.maintenance')); ?>" class="qa-btn ms-auto" style="border-color:#6366f1;color:#6366f1;">
            <?php echo e(app()->getLocale()=='km' ? 'ថែទាំស្តុក' : 'Maintenance'); ?>

        </a>
        <a href="<?php echo e(route('admin.stock.history')); ?>" class="qa-btn">
            <?php echo e(app()->getLocale()=='km' ? 'មើលប្រវត្តិ' : 'View History'); ?>

        </a>
    </div>
</form>

<div class="panel">
    <div class="panel-head">
        <h6><?php echo e(app()->getLocale()=='km' ? 'ស្តុកផលិតផល' : 'Products Stock'); ?> (<?php echo e(count($products)); ?>)</h6>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;"><?php echo e(app()->getLocale()=='km' ? 'ផលិតផល' : 'Product'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទ' : 'Category'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ម៉ាក' : 'Brand'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ស្តុក' : 'Stock'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                    <th style="padding-right:20px;text-align:right;"><?php echo e(app()->getLocale()=='km' ? 'សកម្មភាព' : 'Action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="padding-left:20px;">
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?php echo e($product->image_url ?? ''); ?>"
                                 onerror="this.style.background='#f3f4f6'"
                                 style="width:40px;height:40px;object-fit:contain;border-radius:8px;background:#f9fafb;padding:4px;"/>
                            <div style="font-weight:600;font-size:13px;color:#111;"><?php echo e(Str::limit($product->name, 30)); ?></div>
                        </div>
                    </td>
                    <td style="font-size:12px;color:#6b7280;"><?php echo e($product->category->name); ?></td>
                    <td style="font-size:12px;color:#6b7280;"><?php echo e($product->brand->name ?? '—'); ?></td>
                    <td>
                        <span style="font-size:16px;font-weight:700;color:#374151;">
                            <?php echo e($product->stock); ?>

                        </span>
                    </td>
                    <td>
                        <?php if($product->stock == 0): ?>
                            <span class="pill pill-cancelled"><?php echo e(app()->getLocale()=='km' ? 'អស់ស្តុក' : 'Out of Stock'); ?></span>
                        <?php elseif($product->stock < 10): ?>
                            <span class="pill pill-pending"><?php echo e(app()->getLocale()=='km' ? 'ស្តុកទាប' : 'Low Stock'); ?></span>
                        <?php else: ?>
                            <span class="pill pill-delivered"><?php echo e(app()->getLocale()=='km' ? 'មានស្តុក' : 'In Stock'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="padding-right:20px;text-align:right;">
                        <a href="<?php echo e(route('admin.stock.add', $product->id)); ?>" class="qa-btn qa-primary" style="font-size:12px;padding:6px 14px;">
                            + <?php echo e(app()->getLocale()=='km' ? 'បន្ថែមស្តុក' : 'Add Stock'); ?>

                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'រកមិនឃើញផលិតផល' : 'No products found.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/admin/stock.blade.php ENDPATH**/ ?>