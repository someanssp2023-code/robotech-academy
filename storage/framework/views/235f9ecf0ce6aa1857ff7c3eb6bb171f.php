
<?php $__env->startSection('title', app()->getLocale()=='km' ? 'ផលិតផល' : 'Products'); ?>

<?php $__env->startSection('content'); ?>


<div class="panel mb-4">
    <form method="GET" action="<?php echo e(route('admin.products')); ?>">
        <div class="p-3 d-flex flex-wrap gap-2 align-items-center">
            <input type="text" name="search" class="form-ctrl" style="width:220px;"
                   placeholder="<?php echo e(app()->getLocale()=='km' ? 'ស្វែងរកផលិតផល...' : 'Search products...'); ?>"
                   value="<?php echo e(request('search')); ?>"/>
            <select name="category_id" class="form-ctrl" style="width:180px;" onchange="this.form.submit()">
                <option value=""><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទទាំងអស់' : 'All Categories'); ?></option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id')==$cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="brand_id" class="form-ctrl" style="width:160px;" onchange="this.form.submit()">
                <option value=""><?php echo e(app()->getLocale()=='km' ? 'ម៉ាកទាំងអស់' : 'All Brands'); ?></option>
                <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($brand->id); ?>" <?php echo e(request('brand_id')==$brand->id ? 'selected' : ''); ?>><?php echo e($brand->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            
            <div class="d-flex align-items-center gap-1">
                <input type="date" name="date_from" class="form-ctrl" style="width:150px;"
                       value="<?php echo e(request('date_from')); ?>">
            </div>
            <div class="d-flex align-items-center gap-1">
                <span style="font-size:12px;color:#6b7280;white-space:nowrap;"><?php echo e(app()->getLocale()=='km' ? 'ដល់ថ្ងៃ' : 'To'); ?></span>
                <input type="date" name="date_to" class="form-ctrl" style="width:150px;"
                       value="<?php echo e(request('date_to')); ?>">
            </div>

            <button type="submit" class="qa-btn qa-primary"><?php echo e(app()->getLocale()=='km' ? 'ស្វែងរក' : 'Search'); ?></button>
            <?php if(request()->hasAny(['search','category_id','brand_id','date_from','date_to'])): ?>
                <a href="<?php echo e(route('admin.products')); ?>" class="qa-btn"><?php echo e(app()->getLocale()=='km' ? 'លប់ចោល' : 'Clear'); ?></a>
            <?php endif; ?>

            
            <a href="<?php echo e(route('admin.products.export.excel', request()->query())); ?>"
               class="qa-btn" style="border-color:#16a34a;color:#16a34a;">
                ⬇ Excel
            </a>
            <a href="<?php echo e(route('admin.products.export.pdf', request()->query())); ?>"
               target="_blank"
               class="qa-btn" style="border-color:#dc2626;color:#dc2626;">
                ⬇ PDF
            </a>
        </div>
    </form>
</div>

<div class="d-flex justify-content-end mb-3">
    <a href="<?php echo e(route('admin.product.create')); ?>" class="qa-btn qa-primary">
        + <?php echo e(app()->getLocale()=='km' ? 'បន្ថែមផលិតផល' : 'Add Product'); ?>

    </a>
</div>


<div id="bulk-bar" style="display:none;" class="mb-3">
    <div class="d-flex align-items-center gap-3 px-3 py-2 rounded"
         style="background:#fef9c3;border:1px solid #fde68a;">
        <span style="font-size:13px;font-weight:700;color:#92400e;" id="bulk-count">0 selected</span>
        <form id="bulk-delete-form" method="POST" action="<?php echo e(route('admin.products.bulk-delete')); ?>"
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
        <h6><?php echo e(app()->getLocale()=='km' ? 'ផលិតផល' : 'Products'); ?> (<?php echo e($products->total()); ?>)</h6>
        <span style="font-size:12px;color:#9ca3af;"><?php echo e($products->total()); ?> <?php echo e(app()->getLocale()=='km' ? 'មុខសរុប' : 'total items'); ?></span>
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
                    <th style="padding-left:8px;width:320px;"><?php echo e(app()->getLocale()=='km' ? 'ផលិតផល' : 'Product'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទ' : 'Category'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ម៉ាក' : 'Brand'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'តម្លៃ' : 'Price'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ស្តុក' : 'Stock'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ថ្ងៃបង្កើត' : 'Created'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?></th>
                    <th style="padding-right:20px;text-align:right;"><?php echo e(app()->getLocale()=='km' ? 'សកម្មភាព' : 'Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    
                    <td style="padding-left:16px;">
                        <input type="checkbox" class="row-check" value="<?php echo e($product->id); ?>"
                               style="width:16px;height:16px;cursor:pointer;accent-color:#111;">
                    </td>

                    
                    <td style="padding-left:8px;">
                        <div class="d-flex align-items-center gap-3">
                            <img src="<?php echo e($product->image_url ?? ''); ?>"
                                 onerror="this.style.background='#f3f4f6';this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22%3E%3Crect width=%2240%22 height=%2240%22 fill=%22%23f3f4f6%22/%3E%3C/svg%3E'"
                                 style="width:48px;height:48px;object-fit:contain;border-radius:10px;background:#f9fafb;padding:4px;border:1px solid #e5e7eb;flex-shrink:0;cursor:zoom-in;transition:opacity .15s;"
                                 onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'"
                                 onclick="viewImg('<?php echo e($product->image_url); ?>', '<?php echo e($product->name); ?>')"
                                 title="<?php echo e(app()->getLocale()=='km' ? 'ចុចដើម្បីមើល' : 'Click to view'); ?>"/>
                            <div>
                                <div style="font-weight:600;font-size:13px;color:#111;line-height:1.3;">
                                    <?php echo e(Str::limit($product->name, 32)); ?>

                                </div>
                            </div>
                        </div>
                    </td>

                    
                    <td>
                        <span style="font-size:12px;color:#374151;">
                            <?php echo e($product->category->name ?? '—'); ?>

                        </span>
                    </td>

                    
                    <td style="font-size:12px;color:#374151;">
                        <?php echo e($product->brand->name ?? '—'); ?>

                    </td>

                    
                    <td>
                        <div style="font-weight:600;color:#111;font-size:13px;">
                            $<?php echo e(number_format($product->effective_price, 2)); ?>

                        </div>
                    </td>

                    
                    <td>
                        <?php if($product->stock == 0): ?>
                            <span style="font-size:13px;font-weight:600;color:#374151;">0</span>
                            <span class="pill pill-cancelled" style="font-size:10px;margin-left:4px;"><?php echo e(app()->getLocale()=='km' ? 'អស់' : 'Out'); ?></span>
                        <?php elseif($product->stock < 10): ?>
                            <span style="font-size:13px;font-weight:600;color:#374151;"><?php echo e($product->stock); ?></span>
                            <span class="pill pill-pending" style="font-size:10px;margin-left:4px;"><?php echo e(app()->getLocale()=='km' ? 'ទាប' : 'Low'); ?></span>
                        <?php else: ?>
                            <span style="font-size:13px;font-weight:600;color:#374151;"><?php echo e($product->stock); ?></span>
                        <?php endif; ?>
                    </td>

                    
                    <td style="font-size:12px;color:#6b7280;white-space:nowrap;">
                        <?php echo e($product->created_at->format('d M Y')); ?>

                    </td>

                    
                    <td>
                        <span class="pill <?php echo e($product->is_active ? 'pill-delivered' : 'pill-cancelled'); ?>">
                            <?php echo e($product->is_active ? (app()->getLocale()=='km' ? 'សកម្ម' : 'Active') : (app()->getLocale()=='km' ? 'លាក់' : 'Hidden')); ?>

                        </span>
                    </td>

                    
                    <td style="padding-right:20px;text-align:right;">
                        <div class="d-flex gap-1 justify-content-end align-items-center">
                            <a href="<?php echo e(route('admin.product.edit', $product->id)); ?>" class="view-btn">
                                <?php echo e(app()->getLocale()=='km' ? 'កែ' : 'Edit'); ?>

                            </a>
                            <a href="<?php echo e(route('admin.stock.add', $product->id)); ?>" class="view-btn" style="border-color:#dbeafe;color:#1e40af;">
                                + <?php echo e(app()->getLocale()=='km' ? 'ស្តុក' : 'Stock'); ?>

                            </a>
                            <form action="<?php echo e(route('admin.product.delete', $product->id)); ?>" method="POST"
                                  onsubmit="return confirm('<?php echo e(app()->getLocale()=='km' ? 'លប់ចោលផលិតផល?' : 'Delete this product?'); ?>')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="view-btn" style="border-color:#fecaca;color:#dc2626;background:#fff;">
                                    <?php echo e(app()->getLocale()=='km' ? 'លប់' : 'Delete'); ?>

                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" style="text-align:center;padding:48px;color:#9ca3af;">
                        <div style="font-size:32px;margin-bottom:8px;">📦</div>
                        <div><?php echo e(app()->getLocale()=='km' ? 'រកមិនឃើញផលិតផល' : 'No products found.'); ?></div>
                        <a href="<?php echo e(route('admin.product.create')); ?>"
                           style="display:inline-block;margin-top:12px;background:#111;color:#fff;padding:8px 20px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;">
                            + <?php echo e(app()->getLocale()=='km' ? 'បន្ថែមផលិតផលដំបូង' : 'Add First Product'); ?>

                        </a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <?php if($products->hasPages()): ?>
    <div class="p-3 border-top d-flex align-items-center justify-content-between" style="border-color:#f3f4f6!important;">
        <div style="font-size:12px;color:#9ca3af;">
            <?php echo e(app()->getLocale()=='km' ? 'បង្ហាញ' : 'Showing'); ?>

            <strong><?php echo e($products->firstItem()); ?>–<?php echo e($products->lastItem()); ?></strong>
            <?php echo e(app()->getLocale()=='km' ? 'នៃ' : 'of'); ?>

            <strong><?php echo e($products->total()); ?></strong>
        </div>
        <?php echo e($products->withQueryString()->links('pagination::bootstrap-5')); ?>

    </div>
    <?php endif; ?>
</div>


<div id="imgModal" onclick="closeModal()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.88);z-index:9999;align-items:center;justify-content:center;flex-direction:column;gap:12px;cursor:zoom-out;">
    <img id="imgModalSrc" src="" style="max-width:88vw;max-height:80vh;border-radius:14px;box-shadow:0 24px 64px rgba(0,0,0,.6);object-fit:contain;"/>
    <div id="imgModalName" style="color:rgba(255,255,255,.8);font-size:14px;font-weight:600;margin-top:4px;"></div>
    <button onclick="closeModal()" style="position:absolute;top:20px;right:24px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);color:#fff;font-size:20px;width:40px;height:40px;border-radius:50%;cursor:pointer;backdrop-filter:blur(8px);">✕</button>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Image modal
function viewImg(src, name) {
    if (!src) return;
    document.getElementById('imgModalSrc').src = src;
    document.getElementById('imgModalName').textContent = name || '';
    document.getElementById('imgModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    document.getElementById('imgModal').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if(e.key==='Escape') closeModal(); });

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
    const bar = document.getElementById('bulk-bar');
    document.getElementById('bulk-count').textContent = checked.length + ' selected';
    bar.style.display = checked.length > 0 ? 'block' : 'none';
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
    if (!confirm('Delete ' + checked.length + ' selected product(s)? This cannot be undone.')) return false;
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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/admin/products.blade.php ENDPATH**/ ?>