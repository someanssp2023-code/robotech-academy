
<?php $__env->startSection('title', app()->getLocale()=='km' ? 'ប្រភេទ' : 'Categories'); ?>

<?php $__env->startSection('content'); ?>


<div class="panel mb-4">
    <form method="GET" action="<?php echo e(route('admin.categories')); ?>">
        <div class="p-3 d-flex flex-wrap gap-2 align-items-center">
            <input type="text" name="search" class="form-ctrl" style="width:220px;"
                   placeholder="<?php echo e(app()->getLocale()=='km' ? 'ស្វែងរកប្រភេទ...' : 'Search categories...'); ?>"
                   value="<?php echo e(request('search')); ?>"/>
            <button type="submit" class="qa-btn qa-primary"><?php echo e(app()->getLocale()=='km' ? 'ស្វែងរក' : 'Search'); ?></button>
            <?php if(request('search')): ?>
                <a href="<?php echo e(route('admin.categories')); ?>" class="qa-btn"><?php echo e(app()->getLocale()=='km' ? 'លប់ចោល' : 'Clear'); ?></a>
            <?php endif; ?>

            
            <a href="<?php echo e(route('admin.categories.export.excel', request()->query())); ?>"
               class="qa-btn" style="border-color:#16a34a;color:#16a34a;">
                ⬇ Excel
            </a>
            <a href="<?php echo e(route('admin.categories.export.pdf', request()->query())); ?>"
               target="_blank"
               class="qa-btn" style="border-color:#dc2626;color:#dc2626;">
                ⬇ PDF
            </a>

            <a href="<?php echo e(route('admin.category.create')); ?>" class="qa-btn qa-primary ms-auto">
                + <?php echo e(app()->getLocale()=='km' ? 'បន្ថែមប្រភេទ' : 'Add Category'); ?>

            </a>
        </div>
    </form>
</div>


<div id="bulk-bar" style="display:none;" class="mb-3">
    <div class="d-flex align-items-center gap-3 px-3 py-2 rounded"
         style="background:#fef9c3;border:1px solid #fde68a;">
        <span style="font-size:13px;font-weight:700;color:#92400e;" id="bulk-count">0 selected</span>
        <form id="bulk-delete-form" method="POST" action="<?php echo e(route('admin.categories.bulk-delete')); ?>"
              onsubmit="return confirmBulkDelete()">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <div id="bulk-ids"></div>
            <button type="submit" class="qa-btn" style="border-color:#fecaca;color:#dc2626;background:#fff;">
                <?php echo e(app()->getLocale()=='km' ? 'លប់ដែលបានជ្រើស' : 'Delete Selected'); ?>

            </button>
        </form>
        <button type="button" class="qa-btn" onclick="clearSelection()">
            <?php echo e(app()->getLocale()=='km' ? 'លប់ចោល' : 'Clear'); ?>

        </button>
    </div>
</div>


<div class="panel">
    <div class="panel-head">
        <h6><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទ' : 'Categories'); ?> (<?php echo e($categories->count()); ?>)</h6>
        <span style="font-size:12px;color:#9ca3af;"><?php echo e($categories->count()); ?> <?php echo e(app()->getLocale()=='km' ? 'ប្រភេទសរុប' : 'total'); ?></span>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="width:40px;padding-left:16px;">
                        <input type="checkbox" id="select-all"
                               style="width:16px;height:16px;cursor:pointer;accent-color:#111;"
                               title="<?php echo e(app()->getLocale()=='km' ? 'ជ្រើសទាំងអស់' : 'Select all'); ?>">
                    </th>
                    <th style="padding-left:8px;">No</th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ឈ្មោះ' : 'Name'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ផលិតផល' : 'Products'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                    <th style="padding-right:20px;text-align:right;"><?php echo e(app()->getLocale()=='km' ? 'សកម្មភាព' : 'Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    
                    <td style="padding-left:16px;">
                        <input type="checkbox" class="row-check" value="<?php echo e($cat->id); ?>"
                               style="width:16px;height:16px;cursor:pointer;accent-color:#111;">
                    </td>

                    <td style="padding-left:8px;font-size:12px;color:#9ca3af;"><?php echo e($cat->id); ?></td>
                    <td style="font-weight:600;color:#111;font-size:13px;"><?php echo e($cat->name); ?></td>
                    <td style="font-size:13px;color:#374151;">
                        <?php echo e($cat->products_count); ?>

                        <span style="color:#9ca3af;font-size:12px;"> <?php echo e(app()->getLocale()=='km' ? 'មុខ' : 'products'); ?></span>
                    </td>
                    <td>
                        <span class="pill <?php echo e($cat->is_active ? 'pill-delivered' : 'pill-cancelled'); ?>">
                            <?php echo e($cat->is_active ? (app()->getLocale()=='km' ? 'សកម្ម' : 'Active') : (app()->getLocale()=='km' ? 'លាក់' : 'Hidden')); ?>

                        </span>
                    </td>
                    <td style="padding-right:20px;text-align:right;">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="<?php echo e(route('admin.category.edit', $cat->id)); ?>" class="view-btn">
                                <?php echo e(app()->getLocale()=='km' ? 'កែ' : 'Edit'); ?>

                            </a>
                            <form action="<?php echo e(route('admin.category.delete', $cat->id)); ?>" method="POST"
                                  onsubmit="return confirm('<?php echo e(app()->getLocale()=='km' ? 'លុបចោលប្រភេទ?' : 'Delete this category?'); ?>')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="view-btn" style="border-color:#fecaca;color:#dc2626;background:#fff;">
                                    <?php echo e(app()->getLocale()=='km' ? 'លុប' : 'Delete'); ?>

                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនទាន់មានប្រភេទ' : 'No categories yet.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Select all
document.getElementById('select-all').addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
    updateBulkBar();
});

// Individual row checkbox
document.querySelectorAll('.row-check').forEach(cb => {
    cb.addEventListener('change', function () {
        const total   = document.querySelectorAll('.row-check').length;
        const checked = document.querySelectorAll('.row-check:checked').length;
        const selectAll = document.getElementById('select-all');
        selectAll.indeterminate = checked > 0 && checked < total;
        selectAll.checked = checked === total;
        updateBulkBar();
    });
});

function updateBulkBar() {
    const checked = document.querySelectorAll('.row-check:checked');
    document.getElementById('bulk-count').textContent = checked.length + ' selected';
    document.getElementById('bulk-bar').style.display = checked.length > 0 ? 'block' : 'none';
}

function clearSelection() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
    const selectAll = document.getElementById('select-all');
    selectAll.checked = false;
    selectAll.indeterminate = false;
    document.getElementById('bulk-bar').style.display = 'none';
}

function confirmBulkDelete() {
    const checked = document.querySelectorAll('.row-check:checked');
    if (checked.length === 0) return false;
    if (!confirm('Delete ' + checked.length + ' selected categor' + (checked.length === 1 ? 'y' : 'ies') + '? This cannot be undone.')) return false;
    const container = document.getElementById('bulk-ids');
    container.innerHTML = '';
    checked.forEach(cb => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'ids[]';
        inp.value = cb.value;
        container.appendChild(inp);
    });
    return true;
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/admin/categories.blade.php ENDPATH**/ ?>