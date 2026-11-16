
<?php $__env->startSection('title', $category ? 'Edit Category' : 'Add Category'); ?>

<?php $__env->startSection('content'); ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?php echo e(route('admin.categories')); ?>" class="qa-btn">← Back</a>
    <h5 class="mb-0 fw-bold" style="font-size:16px;"><?php echo e($category ? 'Edit Category' : 'Add New Category'); ?></h5>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="panel p-4">
           

            <form action="<?php echo e($category ? route('admin.category.update', $category->id) : route('admin.category.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php if($category): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Category Name *</label>
                    <input type="text" name="name" class="form-ctrl" required
                           value="<?php echo e(old('name', $category?->name)); ?>"
                           placeholder="e.g. Smartphones"/>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Description <span style="color:#9ca3af;font-weight:400;">optional</span></label>
                    <textarea name="description" class="form-ctrl" rows="3"
                              placeholder=""><?php echo e(old('description', $category?->description)); ?></textarea>
                </div>


                <?php if($category): ?>
                <div class="mb-4">
                    <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                        <input type="checkbox" name="is_active" value="1"
                               style="width:16px;height:16px;accent-color:#f59e0b;"
                               <?php echo e(old('is_active', $category->is_active) ? 'checked' : ''); ?>/>
                        <span class="fw-semibold small">Active (visible in store)</span>
                    </label>
                </div>
                <?php endif; ?>

                <div class="d-flex gap-2">
                    <button type="submit" class="qa-btn qa-primary" style="padding:10px 24px;">
                        <?php echo e($category ? 'Update Category' : 'Create Category'); ?>

                    </button>
                    <a href="<?php echo e(route('admin.categories')); ?>" class="qa-btn" style="padding:10px 16px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/admin/category-form.blade.php ENDPATH**/ ?>