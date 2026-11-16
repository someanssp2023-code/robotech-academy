
<?php $__env->startSection('title', app()->getLocale()=='km' ? 'គ្រប់គ្រងអ្នកប្រើប្រាស់' : 'Manage Users'); ?>

<?php $__env->startSection('content'); ?>


<?php if(session('success')): ?>
<div style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;border-radius:8px;padding:10px 16px;font-size:13px;margin-bottom:16px;">
    ✅ <?php echo e(session('success')); ?>

</div>
<?php endif; ?>
<?php if(session('error')): ?>
<div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:8px;padding:10px 16px;font-size:13px;margin-bottom:16px;">
    ❌ <?php echo e(session('error')); ?>

</div>
<?php endif; ?>


<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;text-align:center;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;"><?php echo e(app()->getLocale()=='km' ? 'សរុប' : 'Total'); ?></div>
            <div style="font-size:28px;font-weight:900;color:#0f172a;"><?php echo e($totalUsers); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;text-align:center;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;">Admin</div>
            <div style="font-size:28px;font-weight:900;color:#111;"><?php echo e($totalAdmins); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;text-align:center;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;"><?php echo e(app()->getLocale()=='km' ? 'បុគ្គលិក' : 'Staff'); ?></div>
            <div style="font-size:28px;font-weight:900;color:#111;"><?php echo e($totalStaff); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;text-align:center;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;"><?php echo e(app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer'); ?></div>
            <div style="font-size:28px;font-weight:900;color:#111;"><?php echo e($totalCustomers); ?></div>
        </div>
    </div>
</div>


<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px;">
    <?php $__currentLoopData = [
        [null,       app()->getLocale()=='km' ? 'ទាំងអស់' : 'All',      $totalUsers,   '#0f172a', '#fff'],
        ['admin',    'Admin',                                              $totalAdmins,  '#f59e0b', '#000'],
        ['staff',    app()->getLocale()=='km' ? 'បុគ្គលិក' : 'Staff',   $totalStaff,   '#8b5cf6', '#fff'],
        ['customer', app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer', $totalCustomers,'#6366f1','#fff'],
        ['inactive', app()->getLocale()=='km' ? 'អសកម្ម' : 'Inactive',  $totalInactive, '#ef4444', '#fff'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$tab, $label, $count, $bg, $color]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $active = request('tab') === $tab; ?>
    <a href="<?php echo e(route('admin.users', $tab ? ['tab'=>$tab] : [])); ?>"
       style="padding:7px 16px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;transition:all .15s;
       <?php echo e($active ? "background:{$bg};color:{$color};" : 'background:#fff;color:#64748b;border:1px solid #e2e8f0;'); ?>">
        <?php echo e($label); ?> (<?php echo e($count); ?>)
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<form method="GET" action="<?php echo e(route('admin.users')); ?>" class="panel p-3 mb-4">
    <input type="hidden" name="tab" value="<?php echo e(request('tab')); ?>"/>
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <input type="text" name="search" class="form-ctrl" style="width:220px;font-size:13px;"
               placeholder="<?php echo e(app()->getLocale()=='km' ? 'ស្វែងរក...' : 'Search name or email...'); ?>"
               value="<?php echo e(request('search')); ?>"/>

        <select name="status" class="form-ctrl" style="width:140px;font-size:13px;" onchange="this.form.submit()">
            <option value=""><?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាពទាំងអស់' : 'All Status'); ?></option>
            <option value="active"   <?php echo e(request('status')=='active'   ? 'selected':''); ?>><?php echo e(app()->getLocale()=='km' ? 'សកម្ម' : 'Active'); ?></option>
            <option value="inactive" <?php echo e(request('status')=='inactive' ? 'selected':''); ?>><?php echo e(app()->getLocale()=='km' ? 'អសកម្ម' : 'Inactive'); ?></option>
        </select>



        <button type="submit" class="qa-btn qa-primary" style="font-size:13px;">
            <?php echo e(app()->getLocale()=='km' ? 'ស្វែងរក' : 'Search'); ?>

        </button>

        <?php if(request()->hasAny(['search','status','date_from','date_to'])): ?>
        <a href="<?php echo e(route('admin.users', request('tab') ? ['tab'=>request('tab')] : [])); ?>" class="qa-btn" style="font-size:13px;">
            <?php echo e(app()->getLocale()=='km' ? 'លប់ចោល' : 'Clear'); ?>

        </a>
        <?php endif; ?>

        
        <a href="<?php echo e(route('admin.users.export.excel', request()->query())); ?>"
           class="qa-btn" style="border-color:#16a34a;color:#16a34a;">
            ⬇ Excel
        </a>
        <a href="<?php echo e(route('admin.users.export.pdf', request()->query())); ?>"
           target="_blank"
           class="qa-btn" style="border-color:#dc2626;color:#dc2626;">
            ⬇ PDF
        </a>
    </div>
</form>


<div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:13px;font-weight:700;color:#0f172a;">
            <?php echo e(app()->getLocale()=='km' ? 'អ្នកប្រើប្រាស់' : 'Users'); ?> (<?php echo e($users->total()); ?>)
        </span>
    </div>

    <div class="table-responsive">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:10px 18px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;border-bottom:1px solid #e2e8f0;">
                        <?php echo e(app()->getLocale()=='km' ? 'អ្នកប្រើប្រាស់' : 'User'); ?>

                    </th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;border-bottom:1px solid #e2e8f0;">
                        <?php echo e(app()->getLocale()=='km' ? 'ប្រភេទ' : 'Role'); ?>

                    </th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;border-bottom:1px solid #e2e8f0;">
                        <?php echo e(app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status'); ?>

                    </th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;border-bottom:1px solid #e2e8f0;">
                        <?php echo e(app()->getLocale()=='km' ? 'ចូលរួម' : 'Joined'); ?>

                    </th>
                    <th style="padding:10px 18px;text-align:right;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;border-bottom:1px solid #e2e8f0;">
                        <?php echo e(app()->getLocale()=='km' ? 'សកម្មភាព' : 'Actions'); ?>

                    </th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr style="border-bottom:1px solid #f8fafc;transition:background .1s;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">

                    
                    <td style="padding:12px 18px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff;background:
                                <?php echo e($user->isMainAdmin() ? '#dc2626' : ($user->role=='Admin' ? '#f59e0b' : ($user->role=='Staff' ? '#8b5cf6' : '#6366f1'))); ?>;">
                                <?php echo e(strtoupper(substr($user->name,0,1))); ?>

                            </div>
                            <div>
                                <div style="font-weight:700;color:#0f172a;display:flex;align-items:center;gap:6px;">
                                    <?php echo e($user->name); ?>

                                    <?php if($user->isMainAdmin()): ?>
                                    <span style="font-size:10px;background:#fef3c7;color:#92400e;padding:1px 6px;border-radius:4px;"></span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size:11px;color:#94a3b8;"><?php echo e($user->email); ?></div>
                            </div>
                        </div>
                    </td>

                    
                    <td style="padding:12px 14px;">
                        <?php if($user->isMainAdmin()): ?>
                            <span style="background:#fee2e2;color:#dc2626;border:1px solid #fecaca;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">Main Admin</span>
                        <?php elseif($user->role=='Admin'): ?>
                            <span style="background:#fef3c7;color:#d97706;border:1px solid #fde68a;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">Admin</span>
                        <?php elseif($user->role=='Staff'): ?>
                            <span style="background:#ede9fe;color:#7c3aed;border:1px solid #ddd6fe;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;"><?php echo e(app()->getLocale()=='km' ? 'បុគ្គលិក' : 'Staff'); ?></span>
                        <?php else: ?>
                            <span style="background:#e0e7ff;color:#4338ca;border:1px solid #c7d2fe;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;"><?php echo e(app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer'); ?></span>
                        <?php endif; ?>
                    </td>

                    
                    <td style="padding:12px 14px;">
                        <?php if($user->is_active): ?>
                            <span style="background:#dcfce7;color:#166534;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;"><?php echo e(app()->getLocale()=='km' ? 'សកម្ម' : 'Active'); ?></span>
                        <?php else: ?>
                            <span style="background:#fee2e2;color:#991b1b;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;"><?php echo e(app()->getLocale()=='km' ? 'អសកម្ម' : 'Inactive'); ?></span>
                        <?php endif; ?>
                    </td>

                    
                    <td style="padding:12px 14px;font-size:12px;color:#94a3b8;">
                        <?php echo e($user->created_at->format('M d, Y')); ?>

                    </td>

                    
                    <td style="padding:12px 18px;text-align:right;">
                        <?php if($user->isMainAdmin()): ?>
                            <span style="font-size:11px;color:#cbd5e1;"><?php echo e(app()->getLocale()=='km' ? 'ការពារ' : 'Protected'); ?></span>
                        <?php else: ?>
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;">

                            
                            <form action="<?php echo e(route('admin.user.toggle', $user->id)); ?>" method="POST" style="margin:0;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" style="font-size:12px;font-weight:600;padding:5px 12px;border-radius:7px;border:1.5px solid <?php echo e($user->is_active ? '#fecaca' : '#bbf7d0'); ?>;color:<?php echo e($user->is_active ? '#dc2626' : '#166534'); ?>;background:#fff;cursor:pointer;font-family:inherit;transition:all .15s;">
                                    <?php echo e($user->is_active ? (app()->getLocale()=='km' ? 'បិទ' : 'Deactivate') : (app()->getLocale()=='km' ? 'បើក' : 'Activate')); ?>

                                </button>
                            </form>

                            
                            <form action="<?php echo e(route('admin.user.promote', $user->id)); ?>" method="POST" style="margin:0;">
                                <?php echo csrf_field(); ?>
                                <select name="role"
                                        style="font-size:12px;font-weight:600;padding:5px 10px;border-radius:7px;border:1.5px solid #e2e8f0;background:#fff;cursor:pointer;outline:none;font-family:inherit;color:#374151;"
                                        onchange="this.form.submit()">
                                    <option value="Customer" <?php echo e($user->role=='Customer' ? 'selected':''); ?>><?php echo e(app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer'); ?></option>
                                    <option value="Staff"    <?php echo e($user->role=='Staff'    ? 'selected':''); ?>><?php echo e(app()->getLocale()=='km' ? 'បុគ្គលិក' : 'Staff'); ?></option>
                                    <option value="Admin"    <?php echo e($user->role=='Admin'    ? 'selected':''); ?>>Admin</option>
                                </select>
                            </form>

                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" style="text-align:center;padding:48px;color:#94a3b8;">
                        <?php echo e(app()->getLocale()=='km' ? 'រកមិនឃើញអ្នកប្រើប្រាស់' : 'No users found.'); ?>

                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <?php if($users->hasPages()): ?>
    <div style="padding:12px 18px;border-top:1px solid #f1f5f9;">
        <?php echo e($users->withQueryString()->links('pagination::bootstrap-5')); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/admin/users.blade.php ENDPATH**/ ?>