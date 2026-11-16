
<?php $__env->startSection('title', $brand ? 'Edit Brand' : 'Add Brand'); ?>

<?php $__env->startSection('content'); ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?php echo e(route('admin.brands')); ?>" class="qa-btn">← <?php echo e(app()->getLocale()=='km' ? 'ត្រឡប់' : 'Back'); ?></a>
    <h5 class="mb-0 fw-bold" style="font-size:16px;">
        <?php echo e($brand ? (app()->getLocale()=='km' ? 'កែម៉ាក' : 'Edit Brand') : (app()->getLocale()=='km' ? 'បន្ថែមម៉ាក' : 'Add New Brand')); ?>

    </h5>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="panel p-4">
            <h6 style="font-size:13px;font-weight:700;color:#111;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #f3f4f6;">
                <?php echo e(app()->getLocale()=='km' ? 'ព័ត៌មានម៉ាក' : 'Brand Details'); ?>

            </h6>

            <form action="<?php echo e($brand ? route('admin.brand.update', $brand->id) : route('admin.brand.store')); ?>"
                  method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php if($brand): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold small"><?php echo e(app()->getLocale()=='km' ? 'ឈ្មោះម៉ាក *' : 'Brand Name *'); ?></label>
                    <input type="text" name="name" class="form-ctrl" required
                           value="<?php echo e(old('name', $brand?->name)); ?>"
                           placeholder="<?php echo e(app()->getLocale()=='km' ? 'ឈ្មោះម៉ាក' : 'e.g. Apple'); ?>"/>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">
                        <?php echo e(app()->getLocale()=='km' ? 'ការពិពណ៌នា' : 'Description'); ?>

                        <span style="color:#9ca3af;font-weight:400;"><?php echo e(app()->getLocale()=='km' ? '(ស្រេចចិត្ត)' : '(optional)'); ?></span>
                    </label>
                    <textarea name="description" class="form-ctrl" rows="3"
                              placeholder="<?php echo e(app()->getLocale()=='km' ? 'ពណ៌នាអំពីម៉ាក...' : 'Brief brand description...'); ?>"><?php echo e(old('description', $brand?->description)); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">
                        <?php echo e(app()->getLocale()=='km' ? 'រូបភាព Logo' : 'Logo Image'); ?>

                        <span style="color:#9ca3af;font-weight:400;"><?php echo e(app()->getLocale()=='km' ? '(ស្រេចចិត្ត)' : '(optional)'); ?></span>
                    </label>

                    <?php if($brand?->logo_url): ?>
                        <img src="<?php echo e($brand->logo_url); ?>"
                             onerror="this.style.display='none'"
                             style="width:80px;height:80px;object-fit:contain;border-radius:10px;background:#f9fafb;padding:8px;display:block;margin-bottom:10px;border:1px solid #e5e7eb;"/>
                    <?php endif; ?>

                    <div id="logo-upload-area"
                         style="border:2px dashed #e5e7eb;border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:border-color .15s;background:#fafafa;"
                         onclick="document.getElementById('logoInput').click()"
                         ondragover="event.preventDefault();this.style.borderColor='#f59e0b';this.style.background='#fffbeb';"
                         ondragleave="this.style.borderColor='#e5e7eb';this.style.background='#fafafa';"
                         ondrop="handleLogoDrop(event)">
                        <div id="logo-placeholder">
                            <div style="font-size:28px;color:#d1d5db;margin-bottom:6px;">↑</div>
                            <div style="font-size:13px;font-weight:600;color:#374151;">
                                <?php echo e(app()->getLocale()=='km' ? 'ចុចឬអូសដើម្បីបញ្ចូល Logo' : 'Click or drag logo here'); ?>

                            </div>
                            
                        </div>
                        <img id="logoPreview" style="display:none;max-width:120px;max-height:80px;object-fit:contain;border-radius:8px;"/>
                    </div>
                    <input type="file" name="logo" id="logoInput" accept="image/*"
                           style="display:none;" onchange="previewLogo(this)"/>
                    <button type="button" id="removeLogo"
                            style="display:none;margin-top:8px;width:100%;background:#fee2e2;color:#dc2626;border:none;border-radius:8px;padding:7px;font-size:12px;font-weight:600;cursor:pointer;"
                            onclick="removeLogo()">
                        <?php echo e(app()->getLocale()=='km' ? 'លប់រូបភាព' : 'Remove Logo'); ?>

                    </button>
                </div>

                <?php if($brand): ?>
                <div class="mb-4">
                    <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                        <input type="checkbox" name="is_active" value="1"
                               style="width:16px;height:16px;accent-color:#f59e0b;"
                               <?php echo e(old('is_active', $brand->is_active) ? 'checked' : ''); ?>/>
                        <span class="fw-semibold small"><?php echo e(app()->getLocale()=='km' ? 'សកម្ម (បង្ហាញក្នុងហាង)' : 'Active (visible in store)'); ?></span>
                    </label>
                </div>
                <?php endif; ?>

                <div class="d-flex gap-2">
                    <button type="submit" class="qa-btn qa-primary" style="padding:10px 24px;">
                        <?php echo e($brand ? (app()->getLocale()=='km' ? 'រក្សាទុក' : 'Update Brand') : (app()->getLocale()=='km' ? 'បន្ថែមម៉ាក' : 'Create Brand')); ?>

                    </button>
                    <a href="<?php echo e(route('admin.brands')); ?>" class="qa-btn" style="padding:10px 16px;">
                        <?php echo e(app()->getLocale()=='km' ? 'បោះបង់' : 'Cancel'); ?>

                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('logo-placeholder').style.display = 'none';
            const prev = document.getElementById('logoPreview');
            prev.src = e.target.result;
            prev.style.display = 'block';
            document.getElementById('removeLogo').style.display = 'block';
            document.getElementById('logo-upload-area').style.borderColor = '#f59e0b';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeLogo() {
    document.getElementById('logoInput').value = '';
    document.getElementById('logoPreview').style.display = 'none';
    document.getElementById('logoPreview').src = '';
    document.getElementById('logo-placeholder').style.display = 'block';
    document.getElementById('removeLogo').style.display = 'none';
    document.getElementById('logo-upload-area').style.borderColor = '#e5e7eb';
}

function handleLogoDrop(event) {
    event.preventDefault();
    event.currentTarget.style.borderColor = '#e5e7eb';
    event.currentTarget.style.background  = '#fafafa';
    const file = event.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const input = document.getElementById('logoInput');
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        previewLogo(input);
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/admin/brand-form.blade.php ENDPATH**/ ?>