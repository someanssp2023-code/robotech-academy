
<?php $__env->startSection('title', app()->getLocale()=='km' ? 'ប្រវត្តិស្តុក' : 'Stock History'); ?>

<?php $__env->startSection('content'); ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?php echo e(route('admin.stock')); ?>" class="qa-btn">← <?php echo e(app()->getLocale()=='km' ? 'ត្រឡប់ទៅស្តុក' : 'Back to Stock'); ?></a>
    <h5 class="mb-0 fw-bold" style="font-size:16px;"><?php echo e(app()->getLocale()=='km' ? 'ប្រវត្តិស្តុក' : 'Stock History'); ?></h5>
</div>


<form method="GET" action="<?php echo e(route('admin.stock.history')); ?>" class="panel p-3 mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center">

        
        <select name="product_id" class="form-ctrl" style="width:220px;" onchange="this.form.submit()">
            <option value=""><?php echo e(app()->getLocale()=='km' ? 'ផលិតផលទាំងអស់' : 'All Products'); ?></option>
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($product->id); ?>" <?php echo e(request('product_id')==$product->id ? 'selected' : ''); ?>><?php echo e($product->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        
        <select name="type_filter" class="form-ctrl" style="width:160px;" onchange="this.form.submit()">
            <option value=""><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទទាំងអស់' : 'All Types'); ?></option>
            <option value="restock"  <?php echo e(request('type_filter')=='restock'  ? 'selected' : ''); ?>>＋ <?php echo e(app()->getLocale()=='km' ? 'បន្ថែម' : 'Add'); ?></option>
            <option value="damaged"  <?php echo e(request('type_filter')=='damaged'  ? 'selected' : ''); ?>> <?php echo e(app()->getLocale()=='km' ? 'ខូចខាត' : 'Damage'); ?></option>
            <option value="returned" <?php echo e(request('type_filter')=='returned' ? 'selected' : ''); ?>>↩ <?php echo e(app()->getLocale()=='km' ? 'ត្រឡប់មក' : 'Return'); ?></option>
        </select>

        
        <input type="date" name="date_from" class="form-ctrl" style="width:150px;" value="<?php echo e(request('date_from')); ?>" onchange="this.form.submit()"/>
        <input type="date" name="date_to"   class="form-ctrl" style="width:150px;" value="<?php echo e(request('date_to')); ?>"   onchange="this.form.submit()"/>

        <?php if(request('product_id') || request('type_filter') || request('date_from') || request('date_to')): ?>
            <a href="<?php echo e(route('admin.stock.history')); ?>" class="qa-btn">✕ <?php echo e(app()->getLocale()=='km' ? 'លប់ចោល' : 'Clear'); ?></a>
        <?php endif; ?>

        
        <a href="<?php echo e(route('admin.stock.history.export.excel', request()->query())); ?>"
           class="qa-btn" style="border-color:#16a34a;color:#16a34a;">
            ⬇ Excel
        </a>
        <a href="<?php echo e(route('admin.stock.history.export.pdf', request()->query())); ?>"
           target="_blank"
           class="qa-btn" style="border-color:#dc2626;color:#dc2626;">
            ⬇ PDF
        </a>
    </div>
</form>

<div class="panel">
    <div class="panel-head">
        <h6><?php echo e(app()->getLocale()=='km' ? 'ការផ្លាស់ប្តូរស្តុក' : 'Stock Changes'); ?> (<?php echo e($histories->total()); ?>)</h6>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="padding-left:20px;"><?php echo e(app()->getLocale()=='km' ? 'កាលបរិច្ឆេទ' : 'Date'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ផលិតផល' : 'Product'); ?></th>
                    <th style="text-align:center;"><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទ' : 'Type'); ?></th>
                    <th style="text-align:center;"><?php echo e(app()->getLocale()=='km' ? 'មុន' : 'Before'); ?></th>
                    <th style="text-align:center;"><?php echo e(app()->getLocale()=='km' ? 'ការផ្លាស់ប្តូរ' : 'Change'); ?></th>
                    <th style="text-align:center;"><?php echo e(app()->getLocale()=='km' ? 'ក្រោយ' : 'After'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'ដោយ' : 'By'); ?></th>
                    <th><?php echo e(app()->getLocale()=='km' ? 'កំណត់ចំណាំ' : 'Note'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                    <td style="padding-left:20px;font-size:12px;color:#6b7280;">
                        <?php echo e($h->created_at->format('d M Y')); ?><br>
                        <span style="font-size:11px;color:#9ca3af;"><?php echo e($h->created_at->format('h:i A')); ?></span>
                    </td>
                    <td style="font-weight:600;font-size:13px;color:#111;"><?php echo e(Str::limit($h->product->name ?? '—', 28)); ?></td>
                    <td style="text-align:center;">
                        <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;background:<?php echo e($typeLabel['bg']); ?>;color:<?php echo e($typeLabel['color']); ?>;">
                            <?php echo e($typeLabel['icon']); ?> <?php echo e($typeLabel['label']); ?>

                        </span>
                    </td>
                    <td style="text-align:center;font-size:13px;color:#6b7280;"><?php echo e($h->stock_before); ?></td>
                    <td style="text-align:center;">
                        <span style="font-weight:700;color:#374151;font-size:14px;"><?php echo e($change); ?></span>
                    </td>
                    <td style="text-align:center;font-weight:600;font-size:13px;color:#111;"><?php echo e($h->stock_after); ?></td>
                    <td style="font-size:13px;color:#374151;"><?php echo e($h->addedBy->name ?? '—'); ?></td>
                    <td style="font-size:12px;color:#9ca3af;"><?php echo e($h->note ?? '—'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:#9ca3af;">
                        <?php echo e(app()->getLocale()=='km' ? 'មិនទាន់មានប្រវត្តិ' : 'No stock history.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($histories->hasPages()): ?>
    <div class="p-3 border-top" style="border-color:#f3f4f6!important;">
        <?php echo e($histories->withQueryString()->links('pagination::bootstrap-5')); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/admin/stock-history-translated.blade.php ENDPATH**/ ?>