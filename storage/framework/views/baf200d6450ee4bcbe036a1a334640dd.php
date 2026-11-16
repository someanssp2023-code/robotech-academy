
<?php $__env->startSection('title', __('app.products')); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* ── Layout ── */
.shop-wrap { display:flex; gap:24px; align-items:flex-start; }

/* ── Sidebar ── */
.filter-sidebar {
    width:240px; flex-shrink:0;
    position:sticky; top:80px;
}
.filter-card {
    background:#fff; border-radius:12px;
    border:1px solid #e5e7eb; padding:20px;
    margin-bottom:16px;
}
.filter-title {
    font-size:11px; font-weight:700;
    text-transform:uppercase; letter-spacing:.08em;
    color:#9ca3af; margin-bottom:12px;
}
.filter-input {
    width:100%; border:1.5px solid #e5e7eb;
    border-radius:8px; padding:8px 12px;
    font-size:13px; outline:none; font-family:inherit;
    transition:border-color .15s;
}
.filter-input:focus { border-color:#f59e0b; }
.filter-select {
    width:100%; border:1.5px solid #e5e7eb;
    border-radius:8px; padding:8px 12px;
    font-size:13px; outline:none; font-family:inherit;
    background:#fff; cursor:pointer;
    transition:border-color .15s;
}
.filter-select:focus { border-color:#f59e0b; }
.price-row { display:flex; gap:8px; }
.price-row .filter-input { width:50%; }
.sort-btns { display:flex; flex-direction:column; gap:6px; }
.sort-btn {
    display:block; padding:7px 10px;
    border-radius:7px; font-size:12px; font-weight:600;
    color:#6b7280; text-decoration:none;
    transition:all .15s; border:1px solid transparent;
}
.sort-btn:hover { background:#f9fafb; color:#111; }
.sort-btn.active { background:#fff8e1; color:#f59e0b; border-color:#f59e0b; }
.btn-apply {
    width:100%; background:#111; color:#fff;
    border:none; border-radius:8px; padding:10px;
    font-size:13px; font-weight:700; cursor:pointer;
    font-family:inherit; transition:background .15s; margin-top:4px;
}
.btn-apply:hover { background:#f59e0b; color:#000; }
.btn-clear {
    display:block; width:100%; text-align:center;
    font-size:12px; color:#9ca3af; text-decoration:none;
    margin-top:10px; transition:color .15s;
}
.btn-clear:hover { color:#ef4444; }

/* ── Products Grid ── */
.products-main { flex:1; min-width:0; }
.products-header {
    display:flex; align-items:center;
    justify-content:space-between;
    margin-bottom:16px; flex-wrap:wrap; gap:8px;
}
.products-count { font-size:13px; color:#6b7280; }
.products-count strong { color:#111; font-weight:700; }
.product-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(200px, 1fr));
    gap:16px;
}
.empty-state {
    text-align:center; padding:60px 20px;
    background:#fff; border-radius:12px;
    border:1px solid #e5e7eb;
}
.empty-state p { color:#9ca3af; font-size:14px; margin-top:12px; }

@media (max-width:767px) {
    .shop-wrap { flex-direction:column; }
    .filter-sidebar { width:100%; position:static; }
    .product-grid { grid-template-columns:repeat(2,1fr); gap:12px; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">

<form method="GET" action="<?php echo e(route('products')); ?>">
<div class="shop-wrap">

    
    <aside class="filter-sidebar">

        <div class="filter-card">
            <div class="filter-title"><?php echo e(__('app.search')); ?></div>
            <input type="text" name="search" class="filter-input"
                   placeholder="<?php echo e(__('app.search')); ?>..."
                   value="<?php echo e(request('search')); ?>"/>
        </div>

        <div class="filter-card">
            <div class="filter-title"><?php echo e(__('app.categories')); ?></div>
            <select name="category_id" class="filter-select" onchange="this.form.submit()">
                <option value=""><?php echo e(app()->getLocale()=='km' ? 'ប្រភេទទាំងអស់' : 'All Categories'); ?></option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id')==$cat->id ? 'selected' : ''); ?>>
                        <?php echo e($cat->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="filter-card">
            <div class="filter-title"><?php echo e(app()->getLocale()=='km' ? 'ម៉ាក' : 'Brand'); ?></div>
            <select name="brand_id" class="filter-select" onchange="this.form.submit()">
                <option value=""><?php echo e(app()->getLocale()=='km' ? 'ម៉ាកទាំងអស់' : 'All Brands'); ?></option>
                <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($brand->id); ?>" <?php echo e(request('brand_id')==$brand->id ? 'selected' : ''); ?>>
                        <?php echo e($brand->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="filter-card">
            <div class="filter-title"><?php echo e(app()->getLocale()=='km' ? 'ជួរតម្លៃ' : 'Price Range'); ?></div>
            <div class="price-row">
                <input type="number" name="min_price" class="filter-input"
                       placeholder="<?php echo e(app()->getLocale()=='km' ? 'តូចបំផុត' : 'Min'); ?>"
                       value="<?php echo e(request('min_price')); ?>"/>
                <input type="number" name="max_price" class="filter-input"
                       placeholder="<?php echo e(app()->getLocale()=='km' ? 'ធំបំផុត' : 'Max'); ?>"
                       value="<?php echo e(request('max_price')); ?>"/>
            </div>
        </div>

        <div class="filter-card">
            <div class="filter-title"><?php echo e(app()->getLocale()=='km' ? 'តម្រៀបតាម' : 'Sort By'); ?></div>
            <div class="sort-btns">
                <?php $__currentLoopData = [
                    'newest'     => app()->getLocale()=='km' ? 'ថ្មីបំផុត'        : 'Newest First',
                    'price_asc'  => app()->getLocale()=='km' ? 'តម្លៃ: ទាប → ខ្ពស់' : 'Price: Low → High',
                    'price_desc' => app()->getLocale()=='km' ? 'តម្លៃ: ខ្ពស់ → ទាប' : 'Price: High → Low',
                    'popular'    => app()->getLocale()=='km' ? 'ពេញនិយម'          : 'Most Popular',
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(request()->fullUrlWithQuery(['sort' => $val])); ?>"
                       class="sort-btn <?php echo e(request('sort','newest')==$val ? 'active' : ''); ?>">
                        <?php echo e($lbl); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <button type="submit" class="btn-apply">
            <?php echo e(app()->getLocale()=='km' ? 'អនុវត្តតម្រង' : 'Apply Filters'); ?>

        </button>

        <?php if(request()->hasAny(['search','category_id','brand_id','min_price','max_price','sort'])): ?>
            <a href="<?php echo e(route('products')); ?>" class="btn-clear">
                <?php echo e(app()->getLocale()=='km' ? 'លប់ចោលតម្រង' : 'Clear Filters'); ?>

            </a>
        <?php endif; ?>

    </aside>

    
    <div class="products-main">

        <div class="products-header">
            <div class="products-count">
                <?php if($products->total() > 0): ?>
                    <?php echo e(app()->getLocale()=='km' ? 'បង្ហាញ' : 'Showing'); ?>

                    <strong><?php echo e($products->firstItem()); ?>–<?php echo e($products->lastItem()); ?></strong>
                    <?php echo e(app()->getLocale()=='km' ? 'នៃ' : 'of'); ?>

                    <strong><?php echo e($products->total()); ?></strong>
                    <?php echo e(__('app.products')); ?>

                <?php else: ?>
                    <strong>0</strong> <?php echo e(__('app.products')); ?>

                <?php endif; ?>
            </div>
        </div>

        <?php if($products->isEmpty()): ?>
            <div class="empty-state">
                <div style="font-size:40px;">📦</div>
                <p><?php echo e(app()->getLocale()=='km' ? 'រកមិនឃើញផលិតផល' : 'No products found.'); ?></p>
                <a href="<?php echo e(route('products')); ?>"
                   style="display:inline-block;margin-top:12px;background:#111;color:#fff;font-weight:700;padding:9px 20px;border-radius:8px;text-decoration:none;font-size:13px;">
                    <?php echo e(app()->getLocale()=='km' ? 'លប់ចោលតម្រង' : 'Clear Filters'); ?>

                </a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('shop._product-card', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <?php if($products->hasPages()): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($products->withQueryString()->links('pagination::bootstrap-5')); ?>

                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</div>
</form>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/shop/products.blade.php ENDPATH**/ ?>