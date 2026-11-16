
<?php $__env->startSection('title', app()->getLocale()=='km' ? 'ម៉ាក' : 'Brands'); ?>

<?php $__env->startSection('content'); ?>


<div class="panel mb-4">
    <form method="GET" action="<?php echo e(route('admin.brands')); ?>">
        <div class="p-3 d-flex flex-wrap gap-2 align-items-center">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                   class="form-ctrl" style="width:220px;"
                   placeholder="<?php echo e(app()->getLocale()=='km' ? 'ស្វែងរកម៉ាក...' : 'Search brands...'); ?>">
            <select name="status" class="form-ctrl" style="width:160px;" onchange="this.form.submit()">
                <option value=""><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាពទាំងអស់' : 'All statuses'); ?></option>
                <option value="active" <?php echo e(request('status')=='active' ? 'selected' : ''); ?>><?php echo e(app()->getLocale()=='km' ? 'សកម្ម' : 'Active'); ?></option>
                <option value="hidden" <?php echo e(request('status')=='hidden' ? 'selected' : ''); ?>><?php echo e(app()->getLocale()=='km' ? 'លាក់'  : 'Hidden'); ?></option>
            </select>
            <button type="submit" class="qa-btn qa-primary"><?php echo e(app()->getLocale()=='km' ? 'ស្វែងរក' : 'Search'); ?></button>
            <?php if(request('search') || request('status')): ?>
                <a href="<?php echo e(route('admin.brands')); ?>" class="qa-btn"><?php echo e(app()->getLocale()=='km' ? 'លប់ចោល' : 'Clear'); ?></a>
            <?php endif; ?>

            
            <a href="<?php echo e(route('admin.brands.export.excel', request()->query())); ?>"
               class="qa-btn" style="border-color:#16a34a;color:#16a34a;">
                ⬇ Excel
            </a>
            <a href="<?php echo e(route('admin.brands.export.pdf', request()->query())); ?>"
               target="_blank"
               class="qa-btn" style="border-color:#dc2626;color:#dc2626;">
                ⬇ PDF
            </a>

            <a href="<?php echo e(route('admin.brand.create')); ?>" class="qa-btn qa-primary ms-auto">
                + <?php echo e(app()->getLocale()=='km' ? 'បន្ថែមម៉ាក' : 'Add Brand'); ?>

            </a>
        </div>
    </form>
</div>


<div id="bulk-bar" style="display:none;" class="mb-3">
    <div class="d-flex align-items-center gap-3 px-3 py-2 rounded"
         style="background:#fef9c3;border:1px solid #fde68a;">
        <span style="font-size:13px;font-weight:700;color:#92400e;" id="bulk-count">0 selected</span>
        <form id="bulk-delete-form" method="POST" action="<?php echo e(route('admin.brands.bulk-delete')); ?>"
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
        <h6><?php echo e(app()->getLocale()=='km' ? 'ម៉ាក' : 'Brands'); ?> (<?php echo e($brands->count()); ?>)</h6>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="width:44px;padding-left:20px;">
                        <input type="checkbox" id="select-all" style="width:15px;height:15px;cursor:pointer;accent-color:#111;">
                    </th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ម៉ាក' : 'Brand'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ផលិតផល' : 'Products'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                    <th style="padding-right:20px;text-align:right;"><?php echo e(app()->getLocale()=='km' ? 'សកម្មភាព' : 'Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="padding-left:20px;">
                        <input type="checkbox" class="row-check" value="<?php echo e($brand->id); ?>"
                               style="width:15px;height:15px;cursor:pointer;accent-color:#111;">
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <?php if($brand->logo_url): ?>
                                <img src="<?php echo e($brand->logo_url); ?>"
                                     onerror="this.style.display='none'"
                                     style="width:36px;height:36px;object-fit:contain;border-radius:8px;background:#f9fafb;padding:4px;border:1px solid #e5e7eb;"/>
                            <?php else: ?>
                                <div style="width:36px;height:36px;background:#f3f4f6;border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:12px;color:#6b7280;">
                                    <?php echo e(strtoupper(substr($brand->name, 0, 2))); ?>

                                </div>
                            <?php endif; ?>
                            <span style="font-weight:600;color:#111;font-size:13px;"><?php echo e($brand->name); ?></span>
                        </div>
                    </td>
                    <td style="font-size:13px;color:#374151;">
                        <?php echo e($brand->products_count); ?>

                        <span style="color:#9ca3af;font-size:12px;"> <?php echo e(app()->getLocale()=='km' ? 'មុខ' : 'products'); ?></span>
                    </td>
                    <td>
                        <span class="pill <?php echo e($brand->is_active ? 'pill-delivered' : 'pill-cancelled'); ?>">
                            <?php echo e($brand->is_active ? (app()->getLocale()=='km' ? 'សកម្ម' : 'Active') : (app()->getLocale()=='km' ? 'លាក់' : 'Hidden')); ?>

                        </span>
                    </td>
                    <td style="padding-right:20px;text-align:right;">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="<?php echo e(route('admin.brand.edit', $brand->id)); ?>" class="view-btn"><?php echo e(app()->getLocale()=='km' ? 'កែ' : 'Edit'); ?></a>
                            <form action="<?php echo e(route('admin.brand.delete', $brand->id)); ?>" method="POST"
                                  onsubmit="return confirm('<?php echo e(app()->getLocale()=='km' ? 'លុបចោលម៉ាក?' : 'Delete this brand?'); ?>')">
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
                    <td colspan="5" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនទាន់មានម៉ាក' : 'No brands yet.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const selectAll  = document.getElementById('select-all');
const bulkBar    = document.getElementById('bulk-bar');
const bulkCount  = document.getElementById('bulk-count');

function getChecked() {
    return [...document.querySelectorAll('.row-check:checked')];
}

function updateBulkBar() {
    const checked = getChecked();
    const total   = document.querySelectorAll('.row-check').length;
    if (checked.length > 0) {
        bulkBar.style.display = 'flex';
        bulkCount.textContent = checked.length + ' <?php echo e(app()->getLocale()=='km' ? 'ម៉ាកបានជ្រើស' : 'brand(s) selected'); ?>';
    } else {
        bulkBar.style.display = 'none';
    }
    selectAll.checked       = checked.length === total && total > 0;
    selectAll.indeterminate = checked.length > 0 && checked.length < total;
}

function clearSelection() {
    document.querySelectorAll('.row-check').forEach(c => c.checked = false);
    selectAll.checked = false;
    updateBulkBar();
}

function confirmBulkDelete() {
    const checked = getChecked();
    if (!checked.length) return false;
    if (!confirm('<?php echo e(app()->getLocale()=='km' ? 'លុបម៉ាក ' : 'Delete '); ?>' + checked.length + '<?php echo e(app()->getLocale()=='km' ? ' ដែលបានជ្រើស?' : ' selected brand(s)?'); ?>')) return false;
    const container = document.getElementById('bulk-ids');
    container.innerHTML = '';
    checked.forEach(c => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'ids[]';
        inp.value = c.value;
        container.appendChild(inp);
    });
    return true;
}

selectAll.addEventListener('change', () => {
    document.querySelectorAll('.row-check').forEach(c => c.checked = selectAll.checked);
    updateBulkBar();
});

document.querySelectorAll('.row-check').forEach(c => c.addEventListener('change', updateBulkBar));
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/admin/brands.blade.php ENDPATH**/ ?>