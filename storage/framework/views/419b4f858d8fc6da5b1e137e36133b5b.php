
<?php $__env->startSection('title', 'Backup & Restore'); ?>

<?php $__env->startSection('content'); ?>


<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 style="font-weight:800;color:#111;margin:0;">Backup & Restore</h5>
       
    </div>
</div>


<?php if(session('success')): ?>
<div class="alert d-flex align-items-center gap-2 mb-4" style="border-radius:12px;border:none;background:#dcfce7;color:#15803d;font-weight:600;padding:14px 18px;">
    <span style="font-size:18px;">✅</span> <?php echo e(session('success')); ?>

</div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="alert d-flex align-items-center gap-2 mb-4" style="border-radius:12px;border:none;background:#fee2e2;color:#dc2626;font-weight:600;padding:14px 18px;">
    <span style="font-size:18px;">❌</span> <?php echo e(session('error')); ?>

</div>
<?php endif; ?>

<div class="row g-4">

    
    <div class="col-lg-7">

        <div class="panel mb-4">
            <div class="panel-head">
                <h6> Create New Backup</h6>
            </div>
            <div style="padding:22px;">
                <form method="POST" action="<?php echo e(route('admin.backup.create')); ?>"
                      id="createBackupForm" onsubmit="startBackup(event)">
                    <?php echo csrf_field(); ?>

                    
                    <div class="mb-4">
                        <label style="font-size:13px;font-weight:700;color:#111;display:block;margin-bottom:8px;">
                             Backup Name <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="text" name="backup_name" id="backup_name"
                               class="form-ctrl <?php $__errorArgs = ['backup_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="e.g...."
                               value="<?php echo e(old('backup_name')); ?>"
                               maxlength="60"
                               required
                               oninput="updatePreview()"
                               style="font-size:13px;letter-spacing:.01em;">
                        <?php $__errorArgs = ['backup_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div style="font-size:12px;color:#dc2626;margin-top:5px;"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <div style="font-size:11px;color:#9ca3af;margin-top:6px;">
                           
                        </div>
                    </div>

                    
                    <div class="mb-4">
                        <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">
                            Database Tables
                        </div>

                        <div style="border:1.5px solid #bfdbfe;border-radius:12px;overflow:hidden;background:#f8faff;">
                            
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:#eff6ff;border-bottom:1px solid #dbeafe;">
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin:0;font-weight:700;font-size:13px;color:#1e40af;">
                                    <input type="checkbox" id="cb-all-tables" checked onchange="toggleAllTables(this.checked)"
                                           style="width:16px;height:16px;accent-color:#3b82f6;">
                                    Select All Tables
                                </label>
                                <div class="d-flex gap-2">
                                    <button type="button" onclick="toggleAllTables(true)"
                                            style="font-size:11px;color:#3b82f6;background:none;border:none;cursor:pointer;font-weight:700;">✅ All</button>
                                    <button type="button" onclick="toggleAllTables(false)"
                                            style="font-size:11px;color:#9ca3af;background:none;border:none;cursor:pointer;font-weight:700;">☐ None</button>
                                </div>
                            </div>

                            
                            <div style="padding:10px 14px;max-height:220px;overflow-y:auto;">
                                <div class="row g-1">
                                    <?php $__currentLoopData = $tables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-6">
                                        <label class="table-cb-label">
                                            <input type="checkbox" name="tables[]" value="<?php echo e($table); ?>"
                                                   class="table-cb" checked onchange="updateSummary()">
                                            <span class="table-cb-text"><?php echo e($table); ?></span>
                                        </label>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>

                            
                            <div style="padding:8px 14px;border-top:1px solid #dbeafe;background:#eff6ff;font-size:12px;color:#3b82f6;font-weight:600;">
                                <span id="table-count-label"><?php echo e(count($tables)); ?> / <?php echo e(count($tables)); ?> tables selected</span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="mb-4">
                        <label style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:8px;">
                            Save To
                        </label>
                        <div style="position:relative;">
                            <select name="save_location" id="save_location" class="form-ctrl" onchange="updateSummary()"
                                    style="font-size:13px;font-weight:600;padding:11px 40px 11px 14px;appearance:none;-webkit-appearance:none;cursor:pointer;background:#fff;">
                                <option value="server">Save on Server </option>
                                <option value="telegram">Send to Telegram</option>
                                <option value="email">Send to Email</option>
                                <option value="drive">Google Drive </option>
                            </select>
                            <div style="position:absolute;right:12px;top:50%;transform:translateY(-50%);pointer-events:none;color:#6b7280;font-size:13px;">▾</div>
                        </div>
                    </div>

                    
                    <input type="hidden" name="save_server"   id="h_server"   value="0">
                    <input type="hidden" name="save_download" id="h_download" value="1">
                    <input type="hidden" name="save_drive"    id="h_drive"    value="0">
                    <input type="hidden" name="save_telegram" id="h_telegram" value="0">
                    <input type="hidden" name="save_email"    id="h_email"    value="0">


                    <button type="submit" class="qa-btn qa-primary w-100" id="createBtn"
                            style="justify-content:center;padding:13px;font-size:14px;">
                        <span id="createBtnText">Create Backup</span>
                        <span id="createBtnLoading" style="display:none;">
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span> Creating…
                        </span>
                    </button>
                </form>
            </div>
        </div>

        
        <div class="panel">
            <div class="panel-head d-flex align-items-center justify-content-between">
                <h6>Saved Backups (<?php echo e(count($files)); ?>)</h6>
             
            </div>

            <?php if(count($files) === 0): ?>
            <div style="text-align:center;padding:50px 20px;color:#9ca3af;">
                
                <div style="font-weight:600;font-size:14px;color:#6b7280;">No backups yet</div>
                <div style="font-size:12px;margin-top:4px;">Create your first backup above.</div>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="ra-table">
                    <thead>
                        <tr>
                            <th style="padding-left:18px;">Name</th>
                            <th>Contents</th>
                            <th>Size</th>
                            <th>Date</th>
                            <th style="padding-right:18px;text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td style="padding-left:18px;">
                                <div style="font-weight:700;font-size:12px;color:#1d4ed8;">
                                    <?php echo e($file['label'] ?: '—'); ?>

                                </div>
                                <div style="font-size:10px;color:#9ca3af;margin-top:1px;"><?php echo e($file['name']); ?></div>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <?php if($file['has_db']): ?>     <span class="pill pill-processing" style="font-size:10px;"> DB</span> <?php endif; ?>
                                    <?php if($file['has_images']): ?> <span class="pill pill-delivered"  style="font-size:10px;">🖼 IMG</span> <?php endif; ?>
                                    <?php if($file['has_storage']): ?><span class="pill" style="font-size:10px;background:#faf5ff;color:#7c3aed;padding:2px 7px;border-radius:99px;font-weight:600;">📁 Files</span> <?php endif; ?>
                                </div>
                            </td>
                            <td style="font-size:12px;font-weight:700;color:#374151;"><?php echo e($file['size']); ?></td>
                            <td style="font-size:11px;color:#9ca3af;white-space:nowrap;">
                                <?php echo e(\Carbon\Carbon::parse($file['created_at'])->format('d M Y H:i')); ?>

                            </td>
                            <td style="padding-right:18px;text-align:right;">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="<?php echo e(route('admin.backup.download', $file['name'])); ?>"
                                       class="view-btn" title="Download"
                                       style="text-decoration:none;border-color:#bfdbfe;color:#1d4ed8;background:#eff6ff;">⬇</a>
                                    <form action="<?php echo e(route('admin.backup.delete', $file['name'])); ?>" method="POST"
                                          onsubmit="return confirm('Delete backup?\n<?php echo e($file['name']); ?>')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="view-btn" title="Delete"
                                                style="border-color:#fecaca;color:#dc2626;background:#fff;">🗑</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </div>

    
    <div class="col-lg-5">

        
        <div class="panel mb-4">
            <div class="panel-head">
                <h6>🔄 Restore Database</h6>
            </div>
            <div style="padding:20px;">
               
                <form method="POST" action="<?php echo e(route('admin.backup.restore')); ?>"
                      enctype="multipart/form-data" onsubmit="return confirmRestore()">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">
                            Upload Backup File <span style="font-size:11px;color:#9ca3af;"></span>
                        </label>
                        <input type="file" name="backup_file" accept=".tar.gz,.gz" required
                               class="form-ctrl" style="padding:8px;font-size:12px;"
                               onchange="showFileName(this)"/>
                        <div id="fileNameDisplay" style="font-size:12px;color:#6b7280;margin-top:6px;display:none;"></div>
                    </div>

                    <button type="submit" class="qa-btn w-100" id="restoreBtn"
                            style="justify-content:center;padding:12px;background:#dc2626;color:#fff;border:none;">
                        <span id="restoreBtnText">🔄 Restore Now</span>
                        <span id="restoreBtnLoading" style="display:none;">
                            <span class="spinner-border spinner-border-sm me-1"></span> Restoring…
                        </span>
                    </button>
                </form>
            </div>
        </div>

        

    </div>
</div>


<div id="backupOverlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:20px;padding:40px;text-align:center;min-width:300px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="font-size:48px;margin-bottom:14px;" id="overlayIcon">💾</div>
        <div style="font-weight:800;font-size:17px;color:#111;margin-bottom:6px;" id="overlayTitle">Creating Backup…</div>
        <div style="font-size:13px;color:#6b7280;margin-bottom:20px;" id="overlayMsg">Please wait.</div>
        <div class="progress" style="height:7px;border-radius:99px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width:100%;"></div>
        </div>
    </div>
</div>

<style>
.content-option-label {
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 14px;
    cursor: pointer;
    transition: border-color .15s, background .15s;
    user-select: none;
}
.content-option-label:has(input:checked) {
    border-color: #3b82f6;
    background: #f8faff;
}
.content-option-label input[type=checkbox] {
    width: 17px; height: 17px;
    accent-color: #3b82f6;
    flex-shrink: 0;
    cursor: pointer;
}
.content-option-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 19px; flex-shrink: 0;
}
</style>

<script>
// ── Live filename preview ─────────────────────────────────
function updatePreview() {
    const raw   = document.getElementById('backup_name').value;
    const clean = raw.replace(/[^a-zA-Z0-9_\-]/g, '') || '<em>your-name</em>';
    const today = '<?php echo e(now()->format("Y-m-d")); ?>';
    document.getElementById('namePreview').innerHTML =
        'backup_' + clean + '_' + today + '_HH-MM-SS.tar.gz';
    updateSummary();
}

// ── Toggle all tables ─────────────────────────────────────
function toggleAllTables(checked) {
    document.querySelectorAll('.table-cb').forEach(cb => cb.checked = checked);
    document.getElementById('cb-all-tables').checked = checked;
    updateSummary();
}

// ── Sync dropdown → hidden fields ────────────────────────
function syncSaveFields() {
    const val = document.getElementById('save_location').value;
    document.getElementById('h_server').value   = (val === 'server' || val === 'both' || val === 'server_telegram' || val === 'server_email' || val === 'server_drive' || val === 'all') ? '1' : '0';
    document.getElementById('h_download').value = (val === 'download' || val === 'both' || val === 'all') ? '1' : '0';
    document.getElementById('h_drive').value    = (val === 'drive') ? '1' : '0';
    document.getElementById('h_telegram').value = (val === 'telegram' || val === 'server_telegram' || val === 'all') ? '1' : '0';
    document.getElementById('h_email').value    = (val === 'email' || val === 'server_email' || val === 'all' || val === 'all_cloud') ? '1' : '0';
    document.getElementById('h_drive').value    = (val === 'drive' || val === 'server_drive' || val === 'all_cloud') ? '1' : '0';
}

// ── Summary bar ──────────────────────────────────────────
function updateSummary() {
    const total    = document.querySelectorAll('.table-cb').length;
    const selected = document.querySelectorAll('.table-cb:checked').length;
    const saveVal  = document.getElementById('save_location').value;

    // Update "X / Y tables selected" counter
    const countLabel = document.getElementById('table-count-label');
    if (countLabel) countLabel.textContent = selected + ' / ' + total + ' tables selected';

    // Sync "select all" header checkbox state
    const allCb = document.getElementById('cb-all-tables');
    if (allCb) allCb.checked = selected === total;

    const saveLabels = {
        'server':          '💾 Server',
        'download':        '⬇️ Download',
        'both':            '💾 Server + ⬇️ Download',
        'telegram':        '📨 Telegram',
        'server_telegram': '💾 Server + 📨 Telegram',
        'email':           '📧 Email',
        'server_email':    '💾 Server + 📧 Email',
        'all':             '💾 Server + ⬇️ Download + 📨 Telegram + 📧 Email',
        'drive':           '☁️ Google Drive',
        'server_drive':    '💾 Server + ☁️ Google Drive',
        'all_cloud':       '📨 Telegram + 📧 Email + ☁️ Drive',
    };

    const el = document.getElementById('summary-text');
    if (selected === 0) {
        el.innerHTML = '<span style="color:#dc2626;">⚠️ Select at least one table</span>';
    } else {
        el.textContent = ' ' + selected + ' tables → ' + (saveLabels[saveVal] || '');
        el.style.color = '#374151';
    }

    syncSaveFields();
}

// ── Start backup ─────────────────────────────────────────
function startBackup(e) {
    const name     = document.getElementById('backup_name').value.trim();
    const selected = document.querySelectorAll('.table-cb:checked').length;

    if (!name) {
        e.preventDefault();
        document.getElementById('backup_name').focus();
        return;
    }
    if (selected === 0) {
        e.preventDefault();
        alert('Please select at least one table to backup!');
        return;
    }

    syncSaveFields();
    document.getElementById('createBtnText').style.display    = 'none';
    document.getElementById('createBtnLoading').style.display = 'inline';
    document.getElementById('createBtn').disabled = true;
    document.getElementById('backupOverlay').style.display    = 'flex';
}

// ── Restore ──────────────────────────────────────────────
function showFileName(input) {
    const d = document.getElementById('fileNameDisplay');
    if (input.files && input.files[0]) {
        d.textContent = '📎 ' + input.files[0].name + ' (' + (input.files[0].size / 1048576).toFixed(2) + ' MB)';
        d.style.display = 'block';
    }
}

function confirmRestore() {
    const file = document.getElementById ? document.querySelector('[name=backup_file]') : null;
    const fname = file && file.files[0] ? file.files[0].name : '';
    const ok = confirm('⚠️ RESTORE CONFIRMATION\n\nThis will OVERWRITE your entire database!\n\nFile: ' + fname + '\n\nAre you sure?');
    if (ok) {
        document.getElementById('restoreBtnText').style.display    = 'none';
        document.getElementById('restoreBtnLoading').style.display = 'inline';
        document.getElementById('restoreBtn').disabled = true;
        document.getElementById('overlayIcon').textContent  = '🔄';
        document.getElementById('overlayTitle').textContent = 'Restoring…';
        document.getElementById('overlayMsg').textContent   = 'Importing database. Please wait.';
        document.getElementById('backupOverlay').style.display = 'flex';
    }
    return ok;
}

document.addEventListener('DOMContentLoaded', function() {
    updateSummary();
});

window.addEventListener('pageshow', function() {
    document.getElementById('backupOverlay').style.display = 'none';
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/admin/backup.blade.php ENDPATH**/ ?>