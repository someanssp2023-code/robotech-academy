
<?php $__env->startSection('title', app()->getLocale()=='km' ? 'ថែទាំស្តុក' : 'Stock Maintenance'); ?>

<?php $__env->startSection('content'); ?>
<style>
.maint-card { background:#fff; border-radius:12px; border:1px solid #e5e7eb; overflow:hidden; margin-bottom:24px; }
.maint-head { padding:16px 20px; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; justify-content:space-between; }
.maint-head h6 { margin:0; font-size:13px; font-weight:700; color:#111; }

.type-select {
    appearance: none;
    -webkit-appearance: none;
    width: 140px;
    padding: 7px 32px 7px 12px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 8px;
    border: 1.5px solid #e5e7eb;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 10px center;
    cursor: pointer;
    transition: .15s;
    color: #374151;
}
.type-select:focus { outline: none; border-color: #6366f1; }
.type-select.opt-add    { border-color: #16a34a; color: #16a34a; background-color: #f0fdf4; }
.type-select.opt-damage { border-color: #dc2626; color: #dc2626; background-color: #fef2f2; }
.type-select.opt-return { border-color: #2563eb; color: #2563eb; background-color: #eff6ff; }
</style>


<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="<?php echo e(route('admin.stock')); ?>" class="qa-btn">
            ← <?php echo e(app()->getLocale()=='km' ? 'ត្រឡប់' : 'Back'); ?>

        </a>
        <h5 class="mb-0 fw-bold" style="font-size:16px;">
            🔧 <?php echo e(app()->getLocale()=='km' ? 'ថែទាំស្តុក' : 'Stock Maintenance'); ?>

        </h5>
    </div>
    <a href="<?php echo e(route('admin.stock.history')); ?>" class="qa-btn">
        <?php echo e(app()->getLocale()=='km' ? 'មើលប្រវត្តិ' : 'View History'); ?>

    </a>
</div>


<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;margin-bottom:6px;"><?php echo e(app()->getLocale()=='km' ? 'ផលិតផលសរុប' : 'Total Products'); ?></div>
            <div style="font-size:28px;font-weight:700;color:#111;"><?php echo e($products->count()); ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;margin-bottom:6px;"><?php echo e(app()->getLocale()=='km' ? 'ស្តុកសរុប' : 'Total Units'); ?></div>
            <div style="font-size:28px;font-weight:700;color:#111;"><?php echo e(number_format($products->sum('stock'))); ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;margin-bottom:6px;"><?php echo e(app()->getLocale()=='km' ? 'ស្តុកទាប' : 'Low Stock'); ?></div>
            <div style="font-size:28px;font-weight:700;color:#111;"><?php echo e($products->whereBetween('stock',[1,9])->count()); ?></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;margin-bottom:6px;"><?php echo e(app()->getLocale()=='km' ? 'អស់ស្តុក' : 'Out of Stock'); ?></div>
            <div style="font-size:28px;font-weight:700;color:#111;"><?php echo e($products->where('stock',0)->count()); ?></div>
        </div>
    </div>
</div>


<form method="GET" action="<?php echo e(route('admin.stock.maintenance')); ?>" class="panel p-3 mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <input type="text" name="search" class="form-ctrl" style="width:260px;"
               placeholder="<?php echo e(app()->getLocale()=='km' ? 'ស្វែងរក...' : 'Search products...'); ?>"
               value="<?php echo e(request('search')); ?>"/>
        <?php $__currentLoopData = ['all'=>(app()->getLocale()=='km'?'ទាំងអស់':'All'),'out'=>(app()->getLocale()=='km'?'អស់ស្តុក':'Out of Stock'),'low'=>(app()->getLocale()=='km'?'ស្តុកទាប':'Low Stock'),'ok'=>(app()->getLocale()=='km'?'មានស្តុក':'In Stock')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button type="submit" name="filter" value="<?php echo e($val); ?>"
                    class="qa-btn <?php echo e(($filter??'all')==$val ? 'qa-primary' : ''); ?>"
                    style="font-size:12px;padding:7px 14px;"><?php echo e($lbl); ?></button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</form>


<div class="maint-card">
    <div class="maint-head">
        <h6><?php echo e(app()->getLocale()=='km' ? 'ផលិតផល' : 'Products'); ?> (<?php echo e($products->count()); ?>)</h6>
        <span style="font-size:12px;color:#9ca3af;">
            <?php echo e(app()->getLocale()=='km' ? 'ជ្រើសប្រភេទ បន្ទាប់មកបញ្ចូលបរិមាណ រួចចុចរក្សាទុក' : 'Select type → enter qty → save'); ?>

        </span>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;"><?php echo e(app()->getLocale()=='km' ? 'ផលិតផល' : 'Product'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទ' : 'Category'); ?></th>
                    <th style="text-align:center;"><?php echo e(app()->getLocale()=='km' ? 'ស្តុក' : 'Stock'); ?></th>
                    <th style="text-align:center;"><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទសកម្មភាព' : 'Action Type'); ?></th>
                    <th style="text-align:center;min-width:100px;"><?php echo e(app()->getLocale()=='km' ? 'បរិមាណ' : 'Qty'); ?></th>
                    <th style="min-width:160px;"><?php echo e(app()->getLocale()=='km' ? 'កំណត់ចំណាំ' : 'Note'); ?></th>
                    <th style="padding-right:20px;text-align:center;"><?php echo e(app()->getLocale()=='km' ? 'រក្សាទុក' : 'Save'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr id="row-<?php echo e($product->id); ?>">

                    
                    <td style="padding-left:20px;">
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?php echo e($product->image_url ?? ''); ?>"
                                 onerror="this.style.background='#f3f4f6'"
                                 style="width:40px;height:40px;object-fit:contain;border-radius:8px;background:#f9fafb;padding:3px;flex-shrink:0;border:1px solid #e5e7eb;"/>
                            <div>
                                <div style="font-weight:600;font-size:13px;color:#111;"><?php echo e(Str::limit($product->name,28)); ?></div>
                                <div style="font-size:11px;color:#9ca3af;"><?php echo e($product->sku ?? ''); ?></div>
                            </div>
                        </div>
                    </td>

                    
                    <td style="font-size:12px;color:#6b7280;"><?php echo e($product->category->name ?? '—'); ?></td>

                    
                    <td style="text-align:center;">
                        <div style="font-size:18px;font-weight:700;color:#374151;" id="stock-<?php echo e($product->id); ?>">
                            <?php echo e($product->stock); ?>

                        </div>
                        <?php if($product->stock==0): ?>
                            <span class="pill pill-cancelled" style="font-size:10px;"><?php echo e(app()->getLocale()=='km'?'អស់':'Out'); ?></span>
                        <?php elseif($product->stock<10): ?>
                            <span class="pill pill-pending" style="font-size:10px;"><?php echo e(app()->getLocale()=='km'?'ទាប':'Low'); ?></span>
                        <?php endif; ?>
                    </td>

                    
                    <td style="text-align:center;">
                        <form action="<?php echo e(route('admin.stock.adjust', $product->id)); ?>" method="POST" id="form-<?php echo e($product->id); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="adj_type" id="adjtype-<?php echo e($product->id); ?>" value="add"/>
                        <input type="hidden" name="reason"   id="reason-<?php echo e($product->id); ?>"  value="restock"/>
                        <select class="type-select opt-add" id="typesel-<?php echo e($product->id); ?>"
                                onchange="onTypeChange(<?php echo e($product->id); ?>, this)">
                            <option value="add|restock">＋ <?php echo e(app()->getLocale()=='km' ? 'បន្ថែម (Add)' : 'Add Stock'); ?></option>
                            <option value="subtract|damaged"> <?php echo e(app()->getLocale()=='km' ? 'ខូច (Damage)' : 'Damage'); ?></option>
                            <option value="subtract|returned">↩ <?php echo e(app()->getLocale()=='km' ? 'ត្រឡប់ (Return)' : 'Return'); ?></option>
                        </select>
                    </td>

                    
                    <td style="text-align:center;">
                        <input type="number" name="quantity" id="qty-<?php echo e($product->id); ?>"
                               class="form-ctrl" style="width:80px;text-align:center;"
                               min="1" max="9999" placeholder="0" required
                               oninput="updatePreview(<?php echo e($product->id); ?>,<?php echo e($product->stock); ?>,this.value)"/>
                        <div style="font-size:11px;margin-top:4px;font-weight:600;" id="preview-<?php echo e($product->id); ?>"></div>
                    </td>

                    
                    <td>
                        <input type="text" name="note" class="form-ctrl"
                               style="font-size:12px;"
                               placeholder="<?php echo e(app()->getLocale()=='km' ? 'កំណត់ចំណាំ...' : 'Note (optional)'); ?>"
                               maxlength="200"/>
                    </td>

                    
                    <td style="padding-right:20px;text-align:center;">
                        <button type="submit" class="qa-btn qa-primary" style="font-size:12px;padding:7px 18px;">
                            <?php echo e(app()->getLocale()=='km' ? 'រក្សាទុក' : 'Save'); ?>

                        </button>
                        </form>
                    </td>

                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'រកមិនឃើញផលិតផល' : 'No products found.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<div class="maint-card">
    <div class="maint-head">
        <h6> <?php echo e(app()->getLocale()=='km' ? 'ប្រវត្តិថែទាំ' : 'Maintenance Log'); ?></h6>
        <a href="<?php echo e(route('admin.stock.history')); ?>" style="font-size:12px;color:#6366f1;">
            <?php echo e(app()->getLocale()=='km' ? 'មើលទាំងអស់' : 'View All'); ?> →
        </a>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;"><?php echo e(app()->getLocale()=='km' ? 'ផលិតផល' : 'Product'); ?></th>
                    <th style="text-align:center;"><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទ' : 'Type'); ?></th>
                    <th style="text-align:center;"><?php echo e(app()->getLocale()=='km' ? 'មុន' : 'Before'); ?></th>
                    <th style="text-align:center;"><?php echo e(app()->getLocale()=='km' ? 'ការផ្លាស់ប្តូរ' : 'Change'); ?></th>
                    <th style="text-align:center;"><?php echo e(app()->getLocale()=='km' ? 'ក្រោយ' : 'After'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ដោយ' : 'By'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'កំណត់ចំណាំ' : 'Note'); ?></th>
                    <th style="padding-right:20px;"><?php echo e(app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $recentLog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $isAdd       = ($h->stock_after >= $h->stock_before);
                    $diff        = abs($h->stock_after - $h->stock_before);
                    $change      = $isAdd ? '+' . $diff : '−' . $diff;

                    $reason = $h->reason
                        ?? ($h->adj_type === 'add'      ? 'restock'
                          : ($h->adj_type === 'subtract' ? 'damaged'
                          : ($isAdd                      ? 'restock' : 'damaged')));

                    $typeLabel = match($reason) {
                        'damaged'  => ['icon'=>'', 'label'=> app()->getLocale()=='km' ? 'ខូចខាត'   : 'Damage', 'color'=>'#dc2626', 'bg'=>'#fee2e2'],
                        'returned' => ['icon'=>'↩',  'label'=> app()->getLocale()=='km' ? 'ត្រឡប់មក' : 'Return', 'color'=>'#2563eb', 'bg'=>'#dbeafe'],
                        default    => ['icon'=>'＋',  'label'=> app()->getLocale()=='km' ? 'បន្ថែម'   : 'Add',    'color'=>'#16a34a', 'bg'=>'#dcfce7'],
                    };
                ?>
                <tr>
                    <td style="padding-left:20px;font-weight:600;font-size:13px;color:#111;">
                        <?php echo e(Str::limit($h->product->name ?? '—', 24)); ?>

                    </td>
                    <td style="text-align:center;">
                        <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;background:<?php echo e($typeLabel['bg']); ?>;color:<?php echo e($typeLabel['color']); ?>;">
                            <?php echo e($typeLabel['icon']); ?> <?php echo e($typeLabel['label']); ?>

                        </span>
                    </td>
                    <td style="text-align:center;font-size:13px;color:#6b7280;"><?php echo e($h->stock_before); ?></td>
                    <td style="text-align:center;font-size:14px;font-weight:700;color:#374151;"><?php echo e($change); ?></td>
                    <td style="text-align:center;font-size:13px;font-weight:600;color:#111;"><?php echo e($h->stock_after); ?></td>
                    <td style="font-size:12px;color:#374151;"><?php echo e($h->addedBy->name ?? '—'); ?></td>
                    <td style="font-size:12px;color:#9ca3af;"><?php echo e($h->note ?? '—'); ?></td>
                    <td style="padding-right:20px;font-size:12px;color:#9ca3af;">
                        <?php echo e($h->created_at->format('d M Y')); ?><br>
                        <span style="font-size:11px;"><?php echo e($h->created_at->format('h:i A')); ?></span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:32px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនទាន់មានប្រវត្តិ' : 'No log yet.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function onTypeChange(id, sel) {
    var parts   = sel.value.split('|');
    var adjType = parts[0];
    var reason  = parts[1];

    document.getElementById('adjtype-' + id).value = adjType;
    document.getElementById('reason-'  + id).value = reason;

    sel.className = 'type-select';
    if (reason === 'restock')  sel.classList.add('opt-add');
    if (reason === 'damaged')  sel.classList.add('opt-damage');
    if (reason === 'returned') sel.classList.add('opt-return');

    var qtyEl   = document.getElementById('qty-' + id);
    var stockEl = document.getElementById('stock-' + id);
    var stock   = parseInt(stockEl ? stockEl.textContent : 0) || 0;
    if (qtyEl && qtyEl.value) updatePreview(id, stock, qtyEl.value);
}

function updatePreview(id, current, val) {
    var qty     = parseInt(val) || 0;
    var adjType = document.getElementById('adjtype-' + id).value;
    var result  = adjType === 'add' ? current + qty : current - qty;
    var el      = document.getElementById('preview-' + id);
    if (!el) return;
    el.textContent = '→ ' + result;
    el.style.color = result <= 0 ? '#ef4444' : (result < 10 ? '#f59e0b' : '#10b981');
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/admin/stock-maintenance.blade.php ENDPATH**/ ?>