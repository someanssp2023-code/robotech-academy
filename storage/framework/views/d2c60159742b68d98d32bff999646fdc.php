<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e(__('app.login')); ?> — Robotech Academy</title>
<link rel="stylesheet" href="<?php echo e(asset('css/bootstrap.min.css')); ?>">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<?php if(app()->getLocale() == 'km'): ?>
<link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>body { font-family: 'Kantumruy Pro', sans-serif !important; }</style>
<?php endif; ?>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { min-height:100vh; background:#5f5a5a; display:flex; align-items:center; justify-content:center; font-family:'Inter',sans-serif; padding:16px; }
  .card { background:#fff; border-radius:12px; padding:40px 36px; width:100%; max-width:400px; box-shadow:0 2px 16px rgba(0,0,0,0.08); border:1px solid #e8eaed; }
  .header { text-align:center; margin-bottom:28px; }
  .header h1 { font-size:20px; font-weight:600; color:#111827; margin-bottom:4px; }
  .header p  { font-size:13.5px; color:#6b7280; }
  .alert-error { background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:10px 13px; font-size:13px; color:#dc2626; margin-bottom:18px; }
  .form-group { margin-bottom:16px; }
  label { display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px; }
  .field-wrap { position:relative; }
  input[type="email"], input[type="password"], input[type="text"] { width:100%; border:1px solid #d1d5db; border-radius:8px; padding:10px 40px 10px 14px; font-size:14px; font-family:inherit; color:#111827; background:#fff; outline:none; transition:border-color 0.15s, box-shadow 0.15s; }
  input:focus { border-color:#4f46e5; box-shadow:0 0 0 3px rgba(79,70,229,0.1); }
  input::placeholder { color:#9ca3af; }
  .toggle-pw { position:absolute; right:11px; top:50%; transform:translateY(-50%); background:none; border:none; color:#9ca3af; cursor:pointer; font-size:13px; padding:2px; transition:color 0.15s; }
  .toggle-pw:hover { color:#4f46e5; }
  .form-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
  .check-wrap { display:flex; align-items:center; gap:7px; cursor:pointer; }
  .check-wrap input[type="checkbox"] { width:15px; height:15px; padding:0; accent-color:#4f46e5; cursor:pointer; }
  .check-wrap span { font-size:13px; color:#6b7280; }
  .forgot { font-size:13px; color:#4f46e5; text-decoration:none; }
  .forgot:hover { text-decoration:underline; }
  .btn-submit { width:100%; padding:11px; background:#4f46e5; color:#fff; border:none; border-radius:8px; font-family:inherit; font-size:14px; font-weight:600; cursor:pointer; transition:background 0.15s; }
  .btn-submit:hover { background:#4338ca; }
  .divider { display:flex; align-items:center; gap:10px; margin:22px 0; font-size:12px; color:#9ca3af; }
  .divider::before, .divider::after { content:''; flex:1; height:1px; background:#e5e7eb; }
  .btn-google { display:flex; align-items:center; justify-content:center; gap:10px; width:100%; padding:11px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:14px; font-weight:600; color:#374151; text-decoration:none; background:#fff; transition:all 0.15s; font-family:inherit; }
  .btn-google:hover { border-color:#4f46e5; color:#4f46e5; background:#f5f3ff; }
  .google-icon { width:18px; height:18px; }
  .footer { text-align:center; font-size:13.5px; color:#6b7280; margin-top:20px; }
  .footer a { color:#4f46e5; text-decoration:none; font-weight:500; }
  .footer a:hover { text-decoration:underline; }
  .lang-switch { display:flex; justify-content:center; gap:8px; margin-bottom:20px; }
  .lang-switch a { font-size:12px; font-weight:600; color:#9ca3af; text-decoration:none; padding:4px 10px; border-radius:6px; border:1px solid #e5e7eb; transition:all .15s; }
  .lang-switch a.active, .lang-switch a:hover { color:#4f46e5; border-color:#4f46e5; }
</style>
</head>
<body>
<div class="card">

 

  <div class="header">
    <h1>Login</h1>
    
  </div>

  <?php if($errors->any()): ?>
  <div class="alert-error"><?php echo e($errors->first()); ?></div>
  <?php endif; ?>

  <form method="POST" action="<?php echo e(route('login')); ?>">
    <?php echo csrf_field(); ?>
    <div class="form-group">
      <label><?php echo e(__('app.email')); ?></label>
      <div class="field-wrap">
        <input type="email" name="email" placeholder="you@gmail.com" value="<?php echo e(old('email')); ?>" required autocomplete="email">
      </div>
    </div>
    <div class="form-group">
      <label><?php echo e(__('app.password')); ?></label>
      <div class="field-wrap">
        <input type="password" name="password" id="password" placeholder="••••••••" required autocomplete="current-password">
        <button type="button" class="toggle-pw" onclick="togglePw('password')">👁</button>
      </div>
    </div>
    <div class="form-row">
      <label class="check-wrap">
        <input type="checkbox" name="remember">
        <span><?php echo e(__('app.remember_me')); ?></span>
      </label>
      <?php if(Route::has('password.request')): ?>
      <a href="<?php echo e(route('password.request')); ?>" class="forgot"><?php echo e(__('app.forgot_password')); ?></a>
      <?php endif; ?>
    </div>
    <button type="submit" class="btn-submit"><?php echo e(__('app.sign_in')); ?></button>
  </form>

  <div class="divider"><?php echo e(app()->getLocale() == 'km' ? 'ឬចូលដោយ' : 'or continue with'); ?></div>

  <a href="<?php echo e(route('auth.google')); ?>" class="btn-google">
    <svg class="google-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
      <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
      <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
      <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
    </svg>
    <?php echo e(__('app.google_signin')); ?>

   
  </a>

  <div class="footer">
    <?php echo e(__('app.no_account')); ?> <a href="<?php echo e(route('register')); ?>"><?php echo e(__('app.register')); ?></a>
  </div>
</div>
<script>
  function togglePw(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
  }
</script>
</body>
</html><?php /**PATH C:\xampp\htdocs\robotech-academy\robotech-academy\resources\views/auth/login.blade.php ENDPATH**/ ?>