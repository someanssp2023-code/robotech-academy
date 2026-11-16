
<?php $__env->startSection('title', app()->getLocale()=='km' ? 'កន្ត្រក' : 'Shopping Cart'); ?>

<?php $__env->startSection('content'); ?>
<style>
.cart-wrap { max-width: 1080px; margin: 0 auto; padding: 36px 16px 80px; }
.cart-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 24px; }
.cart-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
.cart-head { padding: 14px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 8px; }
.cart-head-title { font-size: 13px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px; }
.cart-item { display: flex; align-items: center; gap: 16px; padding: 16px 20px; border-bottom: 1px solid #f1f5f9; transition: background .15s; }
.cart-item:hover { background: #fafafa; }
.cart-item:last-child { border-bottom: none; }
.cart-img { width: 72px; height: 72px; border-radius: 10px; object-fit: contain; background: #f8fafc; border: 1px solid #e2e8f0; padding: 6px; flex-shrink: 0; }
.cart-name { font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 3px; }
.cart-brand { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #f59e0b; }
.cart-price { font-size: 15px; font-weight: 800; color: #f59e0b; }
.cart-price-old { font-size: 12px; color: #94a3b8; text-decoration: line-through; }
.qty-box { display: flex; align-items: center; border: 1.5px solid #e2e8f0; border-radius: 9px; overflow: hidden; }
.qty-btn { width: 34px; height: 34px; border: none; background: #f8fafc; cursor: pointer; font-size: 16px; font-weight: 700; color: #374151; transition: background .15s; display: flex; align-items: center; justify-content: center; }
.qty-btn:hover { background: #f1f5f9; color: #0f172a; }
.qty-num { width: 40px; height: 34px; border: none; border-left: 1.5px solid #e2e8f0; border-right: 1.5px solid #e2e8f0; text-align: center; font-size: 13px; font-weight: 700; color: #0f172a; outline: none; background: #fff; }
.btn-remove { background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 18px; padding: 6px; border-radius: 8px; transition: all .15s; line-height: 1; }
.btn-remove:hover { background: #fee2e2; color: #dc2626; }
.sum-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,.04); position: sticky; top: 76px; }
.sum-title { font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; }
.sum-row { display: flex; justify-content: space-between; font-size: 13px; padding: 6px 0; color: #64748b; }
.sum-total { display: flex; justify-content: space-between; font-size: 16px; font-weight: 800; padding-top: 12px; margin-top: 8px; border-top: 2px solid #e2e8f0; }
.sum-total span:last-child { color: #f59e0b; }
.btn-checkout { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; background: #0f172a; color: #fff; border: none; border-radius: 12px; padding: 14px; font-size: 15px; font-weight: 700; cursor: pointer; text-decoration: none; transition: all .2s; margin-top: 16px; font-family: inherit; }
.btn-checkout:hover { background: #f59e0b; color: #000; }
.btn-continue { display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; background: transparent; color: #374151; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 12px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all .2s; margin-top: 8px; }
.btn-continue:hover { border-color: #0f172a; color: #0f172a; }
.coupon-row { display: flex; gap: 8px; margin-top: 16px; padding-top: 16px; border-top: 1px solid #f1f5f9; }
.coupon-input { flex: 1; border: 1.5px solid #e2e8f0; border-radius: 9px; padding: 9px 12px; font-size: 12px; font-family: inherit; outline: none; text-transform: uppercase; transition: border-color .15s; }
.coupon-input:focus { border-color: #f59e0b; }
.btn-coupon { background: #0f172a; color: #fff; border: none; border-radius: 9px; padding: 9px 14px; font-size: 12px; font-weight: 700; cursor: pointer; white-space: nowrap; font-family: inherit; transition: all .2s; }
.btn-coupon:hover { background: #f59e0b; color: #000; }
.empty-state { text-align: center; padding: 80px 20px; background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; }
.alert-s { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 16px; font-size: 13px; margin-bottom: 16px; }
.alert-e { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 10px; padding: 12px 16px; font-size: 13px; margin-bottom: 16px; }
</style>

<div class="cart-wrap">
    <div class="cart-title"><?php echo e(app()->getLocale()=='km' ? 'កន្ត្រករបស់ខ្ញុំ' : 'My Cart'); ?>

        <?php if($cartItems->isNotEmpty()): ?>
        <span style="font-size:14px;font-weight:500;color:#94a3b8;">
            (<?php echo e($cartItems->sum('quantity')); ?> <?php echo e(app()->getLocale()=='km' ? 'មុខ' : 'items'); ?>)
        </span>
        <?php endif; ?>
    </div>

    <?php if(session('success')): ?>
    <div class="alert-s">✅ <?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="alert-e">❌ <?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <?php if($cartItems->isEmpty()): ?>
    
    <div class="empty-state">
        <div style="font-size:64px;margin-bottom:16px;">🛒</div>
        <div style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:8px;">
            <?php echo e(app()->getLocale()=='km' ? 'កន្ត្រករបស់អ្នកទទេ' : 'Your cart is empty'); ?>

        </div>
        <div style="font-size:13px;color:#94a3b8;margin-bottom:24px;">
            <?php echo e(app()->getLocale()=='km' ? 'ចាប់ផ្តើមមើលផលិតផលរបស់យើង!' : 'Start exploring our products!'); ?>

        </div>
        <a href="<?php echo e(route('products')); ?>" style="display:inline-flex;align-items:center;gap:8px;background:#0f172a;color:#fff;text-decoration:none;padding:12px 28px;border-radius:10px;font-size:14px;font-weight:700;transition:all .2s;" onmouseover="this.style.background='#f59e0b';this.style.color='#000'" onmouseout="this.style.background='#0f172a';this.style.color='#fff'">
            🛍️ <?php echo e(app()->getLocale()=='km' ? 'ទៅទិញទំនិញ' : 'Shop Now'); ?>

        </a>
    </div>
    <?php else: ?>
    <div class="row g-4">

        
        <div class="col-lg-7">
            <div class="cart-card">
                <div class="cart-head">
                    <div class="cart-head-title">
                        
                        <?php echo e(app()->getLocale()=='km' ? 'ទំនិញក្នុងកន្ត្រក' : 'Cart Items'); ?>

                    </div>
                    
                    <form action="<?php echo e(route('cart.clear')); ?>" method="POST" onsubmit="return confirm('<?php echo e(app()->getLocale()=='km' ? 'លប់ទំនិញទាំងអស់?' : 'Clear all items?'); ?>')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" style="background:none;border:none;font-size:12px;color:#94a3b8;cursor:pointer;font-family:inherit;transition:color .15s;" onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#94a3b8'">
                            🗑 <?php echo e(app()->getLocale()=='km' ? 'លប់ទាំងអស់' : 'Clear All'); ?>

                        </button>
                    </form>
                </div>

                <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="cart-item">
                    
                    <a href="<?php echo e(route('product.detail', $item->product->slug)); ?>">
                        <img class="cart-img"
                             src="<?php echo e($item->product->image_url ?? ''); ?>"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2272%22 height=%2272%22%3E%3Crect width=%2272%22 height=%2272%22 fill=%22%23f3f4f6%22/%3E%3C/svg%3E'"
                             alt="<?php echo e($item->product->name); ?>"/>
                    </a>

                    
                    <div style="flex:1;min-width:0;">
                        <?php if($item->product->brand): ?>
                        <div class="cart-brand"><?php echo e($item->product->brand->name); ?></div>
                        <?php endif; ?>
                        <a href="<?php echo e(route('product.detail', $item->product->slug)); ?>" style="text-decoration:none;">
                            <div class="cart-name"><?php echo e(Str::limit($item->product->name, 40)); ?></div>
                        </a>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="cart-price">$<?php echo e(number_format($item->product->effective_price, 2)); ?></span>
                            <?php if($item->product->is_on_sale): ?>
                            <span class="cart-price-old">$<?php echo e(number_format($item->product->price, 2)); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($item->product->stock < 5): ?>
                        <div style="font-size:11px;color:#d97706;font-weight:600;margin-top:3px;">
                            ⚠️ <?php echo e(app()->getLocale()=='km' ? 'នៅសល់ '.$item->product->stock.' ប៉ុណ្ណោះ' : 'Only '.$item->product->stock.' left'); ?>

                        </div>
                        <?php endif; ?>
                    </div>

                    
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <form action="<?php echo e(route('cart.update')); ?>" method="POST" class="d-flex align-items-center gap-2">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="cart_item_id" value="<?php echo e($item->id); ?>"/>
                            <div class="qty-box">
                                <button type="button" class="qty-btn" onclick="changeQty(<?php echo e($item->id); ?>, -1)">−</button>
                                <input type="number" name="quantity" id="qty-<?php echo e($item->id); ?>"
                                       class="qty-num" value="<?php echo e($item->quantity); ?>"
                                       min="1" max="<?php echo e($item->product->stock); ?>"
                                       onchange="submitQty(<?php echo e($item->id); ?>)"/>
                                <button type="button" class="qty-btn" onclick="changeQty(<?php echo e($item->id); ?>, 1)">+</button>
                            </div>
                        </form>

                        
                        <div style="min-width:60px;text-align:right;">
                            <div style="font-size:14px;font-weight:800;color:#0f172a;">
                                $<?php echo e(number_format($item->product->effective_price * $item->quantity, 2)); ?>

                            </div>
                        </div>

                        
                        <form action="<?php echo e(route('cart.remove', $item->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn-remove" title="<?php echo e(app()->getLocale()=='km' ? 'លប់' : 'Remove'); ?>">×</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="col-lg-5">
            <div class="sum-card">
                <div class="sum-title">
                    <?php echo e(app()->getLocale()=='km' ? 'សង្ខេបការបញ្ជាទិញ' : 'Order Summary'); ?>

                </div>

                <div class="sum-row">
                    <span><?php echo e(app()->getLocale()=='km' ? 'តម្លៃរង' : 'Subtotal'); ?> (<?php echo e($cartItems->sum('quantity')); ?> <?php echo e(app()->getLocale()=='km' ? 'មុខ' : 'items'); ?>)</span>
                    <span>$<?php echo e(number_format($subtotal, 2)); ?></span>
                </div>
                <?php if($discount > 0): ?>
                <div class="sum-row" style="color:#10b981;">
                    <span>🎟 <?php echo e(app()->getLocale()=='km' ? 'បញ្ចុះតម្លៃ' : 'Discount'); ?> (<?php echo e($coupon->code); ?>)</span>
                    <span>-$<?php echo e(number_format($discount, 2)); ?></span>
                </div>
                <?php endif; ?>
                <div class="sum-row">
                    <span><?php echo e(app()->getLocale()=='km' ? 'ការដឹក' : 'Shipping'); ?></span>
                    <span style="color:#10b981;font-weight:600;"><?php echo e(app()->getLocale()=='km' ? 'ឥតគិតថ្លៃ' : 'Free'); ?></span>
                </div>
                <div class="sum-total">
                    <span><?php echo e(app()->getLocale()=='km' ? 'សរុបទាំងអស់' : 'Total'); ?></span>
                    <span>$<?php echo e(number_format($total, 2)); ?></span>
                </div>

                
                <a href="<?php echo e(route('checkout')); ?>" class="btn-checkout">
                    <?php echo e(app()->getLocale()=='km' ? 'ទៅបញ្ចប់ការបញ្ជាទិញ' : 'Checkout'); ?>

                </a>
                <a href="<?php echo e(route('products')); ?>" class="btn-continue">
                    ←<?php echo e(app()->getLocale()=='km' ? 'ទិញបន្ថែម' : 'Back'); ?>

                </a>

                
                <div class="coupon-row">
                    <?php if($coupon): ?>
                    
                    <div style="display:flex;align-items:center;justify-content:space-between;width:100%;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;padding:10px 14px;">
                        <div>
                            <span style="font-size:12px;font-weight:700;color:#166534;">🎟 <?php echo e($coupon->code); ?></span>
                            <span style="font-size:11px;color:#166534;margin-left:6px;">
                                -<?php echo e($coupon->type === 'percent' ? $coupon->value.'%' : '$'.$coupon->value); ?>

                            </span>
                        </div>
                        <form action="<?php echo e(route('cart.coupon.remove')); ?>" method="POST" style="margin:0;">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" style="background:none;border:none;color:#dc2626;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;">
                                ✕ <?php echo e(app()->getLocale()=='km' ? 'លុប' : 'Remove'); ?>

                            </button>
                        </form>
                    </div>
                    <?php else: ?>
                    
                    <input type="text" id="couponCode" class="coupon-input"
                           placeholder="<?php echo e(app()->getLocale()=='km' ? 'បញ្ចូលលេខកូដ...' : 'Enter coupon code...'); ?>"
                           style="text-transform:uppercase;"/>
                    <form action="<?php echo e(route('cart.coupon.apply')); ?>" method="POST" style="margin:0;">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="code" id="couponHidden"/>
                        <button type="button" class="btn-coupon" onclick="submitCoupon()">
                            <?php echo e(app()->getLocale()=='km' ? 'ប្រើ' : 'Apply'); ?>

                        </button>
                    </form>
                    <?php endif; ?>
                </div>

               
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function changeQty(itemId, delta) {
    const input = document.getElementById('qty-' + itemId);
    const max   = parseInt(input.max) || 99;
    const newVal = Math.max(1, Math.min(max, parseInt(input.value || 1) + delta));
    input.value = newVal;
    // Auto submit
    input.closest('form').submit();
}

function submitQty(itemId) {
    document.getElementById('qty-' + itemId).closest('form').submit();
}

function submitCoupon() {
    const code = document.getElementById('couponCode').value.trim().toUpperCase();
    if (!code) return;
    document.getElementById('couponHidden').value = code;
    document.getElementById('couponHidden').closest('form').submit();
}

// Enter key on coupon input
document.getElementById('couponCode')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); submitCoupon(); }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/cart/index.blade.php ENDPATH**/ ?>