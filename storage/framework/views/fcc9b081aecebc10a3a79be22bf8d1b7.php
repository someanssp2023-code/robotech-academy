
<?php $__env->startSection('title', app()->getLocale()=='km' ? 'បញ្ចប់ការបញ្ជាទិញ' : 'Checkout'); ?>

<?php $__env->startSection('content'); ?>
<style>
*, *::before, *::after { box-sizing: border-box; }
.co-wrap { max-width: 1080px; margin: 0 auto; padding: 36px 16px 80px; }
.co-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }

/* Steps */
.steps { display: flex; align-items: center; gap: 0; margin-bottom: 32px; }
.step { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700; color: #94a3b8; }
.step.active { color: #0f172a; }
.step.done { color: #10b981; }
.step-num { width: 26px; height: 26px; border-radius: 50%; background: #e2e8f0; color: #94a3b8; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; flex-shrink: 0; }
.step.active .step-num { background: #0f172a; color: #fff; }
.step.done .step-num { background: #10b981; color: #fff; }
.step-line { flex: 1; height: 1px; background: #e2e8f0; margin: 0 8px; }

/* Cards */
.co-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; margin-bottom: 16px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
.co-card-head { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
.co-card-title { font-size: 13px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px; }
.co-card-body { padding: 20px; }

/* Form */
.fl { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px; display: block; }
.fi { width: 100%; border: 1.5px solid #e2e8f0; border-radius: 9px; padding: 9px 12px; font-size: 13px; font-family: inherit; outline: none; transition: border-color .15s; color: #0f172a; background: #fff; }
.fi:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.1); }
.fi:read-only { background: #f8fafc; color: #64748b; cursor: not-allowed; }

/* Payment options */
.pm-option { display: flex; align-items: center; gap: 12px; border: 2px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; cursor: pointer; transition: all .15s; }
.pm-option:hover { border-color: #fbbf24; background: #fffbeb; }
.pm-option.active { border-color: #f59e0b; background: #fffbeb; }
.pm-option input { display: none; }
.pm-radio { width: 18px; height: 18px; border-radius: 50%; border: 2px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all .15s; }
.pm-option.active .pm-radio { border-color: #f59e0b; background: #f59e0b; }
.pm-option.active .pm-radio::after { content: ''; width: 7px; height: 7px; border-radius: 50%; background: #fff; display: block; }
.pm-name { font-size: 13px; font-weight: 700; color: #0f172a; }
.pm-desc { font-size: 11px; color: #6b7280; margin-top: 2px; }

/* Summary */
.sum-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
.sum-item:last-child { border-bottom: none; }
.sum-img { width: 50px; height: 50px; border-radius: 8px; object-fit: contain; background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px; flex-shrink: 0; }
.sum-name { font-size: 12px; font-weight: 600; color: #0f172a; line-height: 1.4; }
.sum-qty { font-size: 11px; color: #6b7280; margin-top: 2px; }
.sum-price { font-size: 13px; font-weight: 700; color: #f59e0b; margin-left: auto; flex-shrink: 0; }
.sum-row { display: flex; justify-content: space-between; font-size: 13px; padding: 5px 0; color: #6b7280; }
.sum-total { display: flex; justify-content: space-between; font-size: 16px; font-weight: 800; padding-top: 12px; margin-top: 8px; border-top: 2px solid #e2e8f0; }
.sum-total span:last-child { color: #f59e0b; }

/* Place order button */
.btn-order { width: 100%; background: #0f172a; color: #fff; border: none; border-radius: 12px; padding: 15px; font-size: 15px; font-weight: 700; cursor: pointer; transition: all .2s; font-family: inherit; display: flex; align-items: center; justify-content: center; gap: 8px; }
.btn-order:hover { background: #f59e0b; color: #000; }

/* Alerts */
.alert-e { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 10px; padding: 12px 16px; font-size: 13px; margin-bottom: 20px; }
</style>

<div class="co-wrap">

    <div class="co-title"><?php echo e(app()->getLocale()=='km' ? 'បញ្ចប់ការបញ្ជាទិញ' : 'Checkout'); ?></div>

    
    <div class="steps">
        <div class="step done">
            <div class="step-num">✓</div>
            <span><?php echo e(app()->getLocale()=='km' ? 'កន្ត្រក' : 'Cart'); ?></span>
        </div>
        <div class="step-line"></div>
        <div class="step active">
            <div class="step-num">2</div>
            <span><?php echo e(app()->getLocale()=='km' ? 'ព័ត៌មាន' : 'Details'); ?></span>
        </div>
        <div class="step-line"></div>
        <div class="step">
            <div class="step-num">3</div>
            <span><?php echo e(app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payment'); ?></span>
        </div>
        <div class="step-line"></div>
        <div class="step">
            <div class="step-num">4</div>
            <span><?php echo e(app()->getLocale()=='km' ? 'បញ្ជាក់' : 'Confirm'); ?></span>
        </div>
    </div>

    <?php if($errors->any()): ?>
    <div class="alert-e">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div>❌ <?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="alert-e">❌ <?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <form action="<?php echo e(route('order.place')); ?>" method="POST">
    <?php echo csrf_field(); ?>

    <div class="row g-4">

        
        <div class="col-lg-7">

            
            <div class="co-card">
                <div class="co-card-head">
                    <div class="co-card-title">
                        <?php echo e(app()->getLocale()=='km' ? 'ព័ត៌មានអ្នកទទួល' : 'Contact Information'); ?>

                    </div>
                </div>
                <div class="co-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="fl"><?php echo e(app()->getLocale()=='km' ? 'ឈ្មោះ' : 'Full Name'); ?></label>
                            <input type="text" class="fi" value="<?php echo e(Auth::user()->name); ?>" readonly/>
                        </div>
                        <div class="col-md-6">
                            <label class="fl">Email</label>
                            <input type="text" class="fi" value="<?php echo e(Auth::user()->email); ?>" readonly/>
                        </div>
                        <div class="col-md-6">
                            <label class="fl"><?php echo e(app()->getLocale()=='km' ? 'លេខទូរស័ព្ទ *' : 'Phone *'); ?></label>
                            <input type="text" name="phone" class="fi" required
                                   value="<?php echo e(old('phone', Auth::user()->phone)); ?>"/>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="co-card">
                <div class="co-card-head">
                    <div class="co-card-title">
                        <?php echo e(app()->getLocale()=='km' ? 'អាសយដ្ឋានដឹក' : 'Shipping Address'); ?>

                    </div>
                </div>
                <div class="co-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="fl"><?php echo e(app()->getLocale()=='km' ? 'អាសយដ្ឋានពេញ *' : 'Full Address *'); ?></label>
                            <textarea name="shipping_address" class="fi" required rows="3"
                                      style="resize:none;"><?php echo e(old('shipping_address', Auth::user()->address)); ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="fl"><?php echo e(app()->getLocale()=='km' ? 'កំណត់ចំណាំ' : 'Delivery Notes'); ?>

                                <span style="color:#94a3b8;font-weight:400;">(<?php echo e(app()->getLocale()=='km' ? 'ស្រេចចិត្ត' : 'optional'); ?>)</span>
                            </label>
                            <input type="text" name="notes" class="fi" value="<?php echo e(old('notes')); ?>"/>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="co-card">
                <div class="co-card-head">
                    <div class="co-card-title">
                        <?php echo e(app()->getLocale()=='km' ? 'វិធីទូទាត់' : 'Payment Method'); ?>

                    </div>
                </div>
                <div class="co-card-body">
                    <div style="display:flex;flex-direction:column;gap:10px;">

                        <div class="pm-option active" id="pm-COD" onclick="selectPM('COD')">
                            <input type="radio" name="payment_method" value="COD" checked/>
                            <div class="pm-radio"></div>
                            <div>
                                <div class="pm-name"><?php echo e(app()->getLocale()=='km' ? 'បង់ប្រាក់ពេលទទួល (COD)' : 'Cash on Delivery (COD)'); ?></div>
                                <div class="pm-desc"><?php echo e(app()->getLocale()=='km' ? 'បង់ប្រាក់ផ្ទាល់ពេលទំនិញមកដល់' : 'Pay cash when your order arrives'); ?></div>
                            </div>
                        </div>

                        <div class="pm-option" id="pm-BankTransfer" onclick="selectPM('BankTransfer')">
                            <input type="radio" name="payment_method" value="BankTransfer"/>
                            <div class="pm-radio"></div>
                            <div>
                                <div class="pm-name"><?php echo e(app()->getLocale()=='km' ? 'ផ្ទេរប្រាក់ធនាគារ' : 'Bank Transfer'); ?></div>
                                <div class="pm-desc">ACLEDA · Wing · True Money</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        
        <div class="col-lg-5">
            <div class="co-card" style="position:sticky;top:76px;">
                <div class="co-card-head">
                    <div class="co-card-title">
                        <span style="width:28px;height:28px;background:#f1f5f9;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;">📦</span>
                        <?php echo e(app()->getLocale()=='km' ? 'សង្ខេបការបញ្ជាទិញ' : 'Order Summary'); ?>

                    </div>
                    <span style="background:#f1f5f9;color:#374151;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">
                        <?php echo e($cartItems->sum('quantity')); ?> <?php echo e(app()->getLocale()=='km' ? 'មុខ' : 'items'); ?>

                    </span>
                </div>

                <div class="co-card-body">

                    
                    <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="sum-item">
                        <img class="sum-img"
                             src="<?php echo e($item->product->image_url ?? ''); ?>"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2248%22 height=%2248%22%3E%3Crect width=%2248%22 height=%2248%22 fill=%22%23f3f4f6%22/%3E%3C/svg%3E'"
                             alt="<?php echo e($item->product->name); ?>"/>
                        <div style="flex:1;min-width:0;">
                            <div class="sum-name"><?php echo e(Str::limit($item->product->name, 28)); ?></div>
                            <div class="sum-qty">x<?php echo e($item->quantity); ?> × $<?php echo e(number_format($item->product->effective_price, 2)); ?></div>
                        </div>
                        <div class="sum-price">$<?php echo e(number_format($item->product->effective_price * $item->quantity, 2)); ?></div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                    <div style="margin-top:16px;padding-top:4px;">
                        <?php
                            $sessCode      = session('coupon_code');
                            $sessDiscount  = 0;
                            $sessCoupon    = null;
                            $afterDiscount = $subtotal;
                            if ($sessCode) {
                                $sessCoupon = \App\Models\Coupon::where('code', $sessCode)
                                    ->where('is_active', true)
                                    ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                                    ->first();
                                if ($sessCoupon) {
                                    $sessDiscount = $sessCoupon->type === 'percent'
                                        ? ($subtotal * $sessCoupon->value / 100)
                                        : $sessCoupon->value;
                                    $sessDiscount  = min($sessDiscount, $subtotal);
                                    $afterDiscount = max(0, $subtotal - $sessDiscount);
                                }
                            }
                            $taxAmount = round($afterDiscount * ($taxRate / 100), 2);
                            $sessTotal = $afterDiscount + $taxAmount;
                        ?>

                        <div class="sum-row">
                            <span><?php echo e(app()->getLocale()=='km' ? 'តម្លៃរង' : 'Subtotal'); ?></span>
                            <span>$<?php echo e(number_format($subtotal, 2)); ?></span>
                        </div>

                        <div class="sum-row">
                            <span><?php echo e(app()->getLocale()=='km' ? 'ការដឹក' : 'Shipping'); ?></span>
                            <span style="color:#10b981;font-weight:600;"><?php echo e(app()->getLocale()=='km' ? 'ឥតគិតថ្លៃ' : 'Free'); ?></span>
                        </div>

                        <?php if($sessDiscount > 0): ?>
                        <div class="sum-row" id="discount-row">
                            <span style="color:#10b981;">
                                🎟 <?php echo e(app()->getLocale()=='km' ? 'បញ្ចុះតម្លៃ' : 'Discount'); ?>

                                <?php if($sessCoupon): ?>(<?php echo e($sessCoupon->code); ?>)<?php endif; ?>
                            </span>
                            <span id="discount-val" style="color:#10b981;font-weight:700;">-$<?php echo e(number_format($sessDiscount, 2)); ?></span>
                        </div>
                        <?php else: ?>
                        <div class="sum-row" id="discount-row" style="display:none;">
                            <span style="color:#10b981;">🎟 Discount</span>
                            <span id="discount-val" style="color:#10b981;font-weight:700;">-$0.00</span>
                        </div>
                        <?php endif; ?>

                        <?php if($taxRate > 0): ?>
                        <div class="sum-row">
                            <span>🧾 <?php echo e(app()->getLocale()=='km' ? 'ពន្ធ' : 'Tax'); ?> (<?php echo e($taxRate); ?>%)</span>
                            <span id="tax-val">$<?php echo e(number_format($taxAmount, 2)); ?></span>
                        </div>
                        <?php endif; ?>

                        <div class="sum-total">
                            <span><?php echo e(app()->getLocale()=='km' ? 'សរុបទាំងអស់' : 'Total'); ?></span>
                            <span id="total-val">$<?php echo e(number_format($sessTotal, 2)); ?></span>
                        </div>
                    </div>

                    
                    <button type="submit" class="btn-order" style="margin-top:20px;">
                        <?php echo e(app()->getLocale()=='km' ? 'ដាក់ការបញ្ជាទិញ' : 'Place Order'); ?>

                    </button>

                    <a href="<?php echo e(route('cart')); ?>" style="display:block;text-align:center;font-size:12px;color:#94a3b8;margin-top:12px;text-decoration:none;">
                        ← <?php echo e(app()->getLocale()=='km' ? 'ត្រឡប់ទៅកន្ត្រក' : 'Back to Cart'); ?>

                    </a>

                </div>
            </div>
        </div>

    </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
const subtotal = <?php echo e($subtotal); ?>;
const taxRate  = <?php echo e($taxRate); ?>;

function selectPM(method) {
    document.querySelectorAll('.pm-option').forEach(el => el.classList.remove('active'));
    const el = document.getElementById('pm-' + method);
    if (el) {
        el.classList.add('active');
        el.querySelector('input[type="radio"]').checked = true;
    }
}

function applyCoupon() {
    const code = document.getElementById('couponInput')?.value.trim().toUpperCase();
    const msg  = document.getElementById('coupon-msg');
    if (!code) {
        if (msg) msg.innerHTML = '<span style="color:#dc2626;">⚠️ <?php echo e(app()->getLocale()=="km" ? "សូមបញ្ចូលលេខកូដ" : "Please enter a coupon code"); ?></span>';
        return;
    }
    if (msg) msg.innerHTML = '<span style="color:#94a3b8;"><?php echo e(app()->getLocale()=="km" ? "កំពុងពិនិត្យ..." : "Checking..."); ?></span>';

    fetch('/coupon/check?code=' + encodeURIComponent(code))
        .then(r => r.json())
        .then(data => {
            if (data.valid) {
                const disc      = data.type === 'percent' ? (subtotal * data.value / 100) : parseFloat(data.value);
                const discFinal = Math.min(disc, subtotal);
                const afterDisc = subtotal - discFinal;
                const newTax    = Math.round(afterDisc * (taxRate / 100) * 100) / 100;
                const total     = afterDisc + newTax;

                document.getElementById('discount-row').style.display = 'flex';
                document.getElementById('discount-val').textContent = '-$' + discFinal.toFixed(2);
                const taxEl = document.getElementById('tax-val');
                if (taxEl) taxEl.textContent = '$' + newTax.toFixed(2);
                document.getElementById('total-val').textContent = '$' + total.toFixed(2);

                if (msg) msg.innerHTML = '<span style="color:#10b981;">✅ <?php echo e(app()->getLocale()=="km" ? "គូប៉ុងបានអនុវត្ត" : "Coupon applied!"); ?></span>';
            } else {
                if (msg) msg.innerHTML = '<span style="color:#dc2626;">❌ <?php echo e(app()->getLocale()=="km" ? "គូប៉ុងមិនត្រឹមត្រូវ ឬ ផុតកំណត់" : "Invalid or expired coupon"); ?></span>';
            }
        })
        .catch(() => {
            if (msg) msg.innerHTML = '<span style="color:#dc2626;">❌ Error checking coupon</span>';
        });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/orders/checkout.blade.php ENDPATH**/ ?>