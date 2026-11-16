<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?php echo e(app()->getLocale()=='km' ? 'ផ្ទេរប្រាក់ធនាគារ' : 'Bank Transfer'); ?> — RoboTech</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700;800&display=swap" rel="stylesheet"/>
    <style>body { font-family: 'Kantumruy Pro', sans-serif !important; }</style>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Space Grotesk', sans-serif; background: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #fff; border-radius: 20px; box-shadow: 0 8px 40px rgba(0,0,0,.1); width: 100%; max-width: 440px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%); padding: 24px; text-align: center; }
        .header h2 { color: #fff; font-size: 18px; font-weight: 800; margin-top: 8px; }
        .header p { color: rgba(255,255,255,.6); font-size: 12px; margin-top: 4px; }
        .body { padding: 24px; }

        /* Order summary */
        .order-box { background: #f8fafc; border-radius: 12px; padding: 16px; margin-bottom: 20px; }
        .order-row { display: flex; justify-content: space-between; font-size: 13px; padding: 4px 0; color: #64748b; }
        .order-val { font-weight: 700; color: #0f172a; }
        .total-row { display: flex; justify-content: space-between; font-size: 18px; font-weight: 800; margin-top: 10px; padding-top: 10px; border-top: 2px solid #e2e8f0; }
        .total-amt { color: #f59e0b; }

        /* Bank account info */
        .bank-info { background: #f0f7ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px; margin-bottom: 20px; }
        .bank-title { font-size: 12px; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 12px; }
        .bank-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #dbeafe; }
        .bank-row:last-child { border-bottom: none; }
        .bank-key { font-size: 12px; color: #64748b; font-weight: 600; }
        .bank-val { font-size: 13px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 6px; }
        .copy-btn { background: none; border: none; cursor: pointer; color: #94a3b8; font-size: 14px; transition: color .15s; padding: 0; }
        .copy-btn:hover { color: #2563eb; }
        .copied { color: #10b981 !important; }

        /* QR Code */
        .qr-section { text-align: center; margin-bottom: 20px; }
        .qr-title { font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 12px; }
        .qr-box { background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 16px; padding: 16px; display: block; width: 100%; }
        .qr-note { font-size: 11px; color: #94a3b8; margin-top: 8px; }

        /* Steps */
        .steps { margin-bottom: 20px; }
        .steps-title { font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 10px; }
        .step-item { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 8px; }
        .step-num { width: 22px; height: 22px; background: #0f172a; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; flex-shrink: 0; margin-top: 1px; }
        .step-text { font-size: 13px; color: #374151; line-height: 1.5; }

        /* Upload slip form */
        .upload-section { margin-bottom: 20px; }
        .upload-title { font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 10px; }
        .upload-area { border: 2px dashed #e2e8f0; border-radius: 12px; padding: 20px; text-align: center; cursor: pointer; transition: all .15s; background: #f8fafc; }
        .upload-area:hover { border-color: #f59e0b; background: #fffbeb; }
        .upload-area.has-file { border-color: #10b981; background: #f0fdf4; }
        .upload-icon { font-size: 32px; margin-bottom: 6px; }
        .upload-text { font-size: 13px; font-weight: 600; color: #374151; }
        .upload-sub { font-size: 11px; color: #94a3b8; margin-top: 3px; }
        .upload-preview { max-width: 100%; max-height: 150px; border-radius: 8px; margin-top: 10px; display: none; }
        .note-input { width: 100%; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; font-size: 13px; font-family: inherit; outline: none; transition: border-color .15s; margin-top: 10px; resize: none; }
        .note-input:focus { border-color: #f59e0b; }

        /* Submit button */
        .btn-submit { width: 100%; background: #0f172a; color: #fff; border: none; border-radius: 12px; padding: 14px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all .2s; margin-bottom: 8px; }
       
        .btn-submit:disabled { background: #e2e8f0; color: #94a3b8; cursor: not-allowed; }
        .btn-cancel { display: block; text-align: center; font-size: 13px; color: #94a3b8; text-decoration: none; padding: 8px; }
        .btn-cancel:hover { color: #ef4444; }

        /* Alert */
        .alert-s { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 14px; font-size: 13px; margin-bottom: 16px; text-align: center; }

        /* Loading */
        .loading { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.6); z-index: 999; align-items: center; justify-content: center; flex-direction: column; gap: 16px; }
        .loading.show { display: flex; }
        .spinner { width: 44px; height: 44px; border: 4px solid rgba(255,255,255,.3); border-top-color: #fff; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading p { color: #fff; font-size: 14px; font-weight: 600; }

        /* Telegram badge */
        .tg-badge { display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 12px; color: #0088cc; font-weight: 600; margin-top: 8px; }
    </style>
</head>
<body>

<div class="loading" id="loading">
    <div class="spinner"></div>
    <p><?php echo e(app()->getLocale()=='km' ? 'កំពុងផ្ញើ...' : 'Sending...'); ?></p>
</div>

<div class="card">
    
    <div class="header">
        
        <h2><?php echo e(app()->getLocale()=='km' ? 'ផ្ទេរប្រាក់ធនាគារ' : 'Bank Transfer'); ?></h2>
        <p><?php echo e(app()->getLocale()=='km' ? 'ផ្ទេរប្រាក់ ហើយ upload slip' : 'Transfer money then upload your slip'); ?></p>
    </div>

    <div class="body">

        <?php if(session('success')): ?>
        <div class="alert-s">✅ <?php echo e(session('success')); ?></div>
        <?php endif; ?>

        
        <div class="order-box">
            <div class="order-row">
                <span><?php echo e(app()->getLocale()=='km' ? 'លេខការបញ្ជាទិញ' : 'Order ID'); ?></span>
                <span class="order-val">#RA-<?php echo e($order->id); ?></span>
            </div>
            <div class="order-row">
                <span><?php echo e(app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer'); ?></span>
                <span class="order-val"><?php echo e($order->user->name); ?></span>
            </div>
            <div class="total-row">
                <span><?php echo e(app()->getLocale()=='km' ? 'សរុប' : 'Total'); ?></span>
                <span class="total-amt">$<?php echo e(number_format($order->total_amount, 2)); ?></span>
            </div>
        </div>

        
        <div class="qr-section">
            <div class="qr-title"><?php echo e(app()->getLocale()=='km' ? 'ស្កែន QR ដើម្បីផ្ទេរ' : 'Scan QR to Transfer'); ?></div>
            <div class="qr-box">
                
                <?php if(file_exists(public_path('images/bank-qr.png'))): ?>
                    <img src="<?php echo e(asset('images/bank-qr.png')); ?>" alt="Bank QR" style="width:100%;max-width:260px;height:auto;display:block;margin:0 auto;"/>
                <?php elseif(file_exists(public_path('images/bank-qr.jpg'))): ?>
                    <img src="<?php echo e(asset('images/bank-qr.jpg')); ?>" alt="Bank QR" style="width:100%;max-width:260px;height:auto;display:block;margin:0 auto;"/>
                <?php else: ?>
                    <div style="width:260px;height:260px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;border-radius:8px;color:#94a3b8;font-size:12px;text-align:center;padding:10px;">
                        📁 <?php echo e(app()->getLocale()=='km' ? 'ដាក់ QR image នៅ public/images/bank-qr.png' : 'Place QR image at public/images/bank-qr.png'); ?>

                    </div>
                <?php endif; ?>
                <div class="qr-note"><?php echo e(app()->getLocale()=='km' ? 'ស្កែនដោយ ABA, ACLEDA, Wing, Bakong' : 'Scan with ABA, ACLEDA, Wing, Bakong'); ?></div>
            </div>
        </div>

      

      

        
        <form action="<?php echo e(route('payment.bank.slip', $order->id)); ?>" method="POST" enctype="multipart/form-data" id="slipForm">
            <?php echo csrf_field(); ?>
            <div class="upload-section">
                <div class="upload-title"><?php echo e(app()->getLocale()=='km' ? 'Upload Slip ការផ្ទេរ' : 'Upload Transfer Slip'); ?></div>
                <div class="upload-area" id="uploadArea" onclick="document.getElementById('slipFile').click()">
    
                    <div class="upload-text"><?php echo e(app()->getLocale()=='km' ? 'ចុចដើម្បីជ្រើស រូបភាព' : 'Click to select image'); ?></div>
                    <div class="upload-sub"></div>
                    <img id="preview" class="upload-preview" alt="Preview"/>
                </div>
                <input type="file" name="slip" id="slipFile" accept="image/*" style="display:none;" required onchange="previewSlip(this)"/>
                <textarea name="note" class="note-input" rows="2"
                          placeholder="<?php echo e(app()->getLocale()=='km' ? 'កំណត់ចំណាំ (ស្រេចចិត្ត)...' : 'Note (optional)...'); ?>"></textarea>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn" onclick="showLoading()">
                 <?php echo e(app()->getLocale()=='km' ? 'ផ្ញើ Slip ហើយ ជូនដំណឹង' : 'Send'); ?>

            </button>
        </form>

       

        <a href="<?php echo e(route('cart')); ?>" class="btn-cancel">
            ← <?php echo e(app()->getLocale()=='km' ? 'បោះបង់' : 'Cancel'); ?>

        </a>
    </div>
</div>

<script>
function previewSlip(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('preview');
            const area    = document.getElementById('uploadArea');
            preview.src   = e.target.result;
            preview.style.display = 'block';
            area.classList.add('has-file');
            area.querySelector('.upload-text').textContent = input.files[0].name;
            area.querySelector('.upload-sub').textContent  = '<?php echo e(app()->getLocale()=="km" ? "ចុចដើម្បីផ្លាស់ប្តូរ" : "Click to change"); ?>';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function copyText(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        btn.textContent = '✅';
        btn.classList.add('copied');
        setTimeout(() => { btn.textContent = '📋'; btn.classList.remove('copied'); }, 2000);
    });
}

function showLoading() {
    const file = document.getElementById('slipFile');
    if (!file.files.length) { event.preventDefault(); return; }
    document.getElementById('loading').classList.add('show');
}
</script>
</body>
</html><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/payment/bank.blade.php ENDPATH**/ ?>