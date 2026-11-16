<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ app()->getLocale()=='km' ? 'ចុះឈ្មោះ' : 'Register' }} — RoboTech</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700;800&display=swap" rel="stylesheet"/>
</head>
<style>body { font-family: 'Kantumruy Pro', sans-serif !important; }</style>
<body style="margin:0;padding:0;font-family:'Space Grotesk',sans-serif;">
<style>
.auth-wrap {
    min-height: calc(100vh - 72px);
    display: flex; align-items: center; justify-content: center;
    padding: 40px 16px; background:#5f5a5a;
}
.auth-card {
    background: #fff; border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 16px rgba(0,0,0,.06);
    width: 100%; max-width: 440px;
    padding: 36px;
}
.auth-logo {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    margin-bottom: 24px; text-decoration: none;
}
.auth-logo-box {
    width: 36px; height: 36px; background: #0f172a; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 900; color: #f59e0b; font-size: 18px;
}
.auth-logo-name { font-size: 18px; font-weight: 800; color: #0f172a; }
.auth-logo-name span { color: #f59e0b; }
.auth-title { font-size: 20px; font-weight: 800; color: #0f172a; text-align: center; margin-bottom: 4px; }
.auth-sub { font-size: 13px; color: #94a3b8; text-align: center; margin-bottom: 24px; }
.fl { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px; display: block; }
.fi {
    width: 100%; border: 1.5px solid #e2e8f0; border-radius: 10px;
    padding: 10px 14px; font-size: 13px; font-family: inherit;
    outline: none; transition: border-color .15s; color: #0f172a;
    margin-bottom: 14px; background: #fff;
}
.fi:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.08); }
.fi.is-invalid { border-color: #ef4444; margin-bottom: 4px; }
.err-msg { font-size: 11px; color: #ef4444; margin-bottom: 10px; display: block; }
.pw-wrap { position: relative; }
.pw-wrap .fi { padding-right: 40px; }
.pw-eye { position: absolute; right: 12px; top: 13px; background: none; border: none; cursor: pointer; color: #94a3b8; font-size: 15px; padding: 0; }
.btn-register {
    width: 100%; background: #0f172a; color: #fff;
    border: none; border-radius: 10px; padding: 12px;
    font-size: 14px; font-weight: 700; cursor: pointer;
    font-family: inherit; transition: all .2s; margin-top: 6px;
}
.btn-register:hover { background: #f59e0b; color: #000; }
.divider { display: flex; align-items: center; gap: 10px; margin: 18px 0; color: #94a3b8; font-size: 12px; }
.divider::before, .divider::after { content:''; flex:1; height:1px; background:#e2e8f0; }

.alert-e { background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:8px;padding:10px 14px;font-size:13px;margin-bottom:16px; }
.login-link { text-align:center;font-size:13px;color:#64748b;margin-top:18px; }
.login-link a { color:#f59e0b;font-weight:700;text-decoration:none; }
</style>

<div class="auth-wrap">
<div class="auth-card">


    <div class="auth-title">{{ app()->getLocale()=='km' ? 'ចុះឈ្មោះ' : 'Create Account' }}</div>
    

    {{-- Errors --}}
    @if($errors->any())
    <div class="alert-e">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name --}}
        <label class="fl">{{ app()->getLocale()=='km' ? 'ឈ្មោះពេញ *' : 'Full Name *' }}</label>
        <input type="text" name="name" class="fi {{ $errors->has('name') ? 'is-invalid':'' }}"
               value="{{ old('name') }}" required autofocus
               placeholder="{{ app()->getLocale()=='km' ? 'បញ្ចូលឈ្មោះ...' : '' }}"/>
        @error('name')<span class="err-msg">{{ $message }}</span>@enderror

        {{-- Email --}}
        <label class="fl">Email *</label>
        <input type="email" name="email" class="fi {{ $errors->has('email') ? 'is-invalid':'' }}"
               value="{{ old('email') }}" required
               placeholder=""/>
        @error('email')<span class="err-msg">{{ $message }}</span>@enderror

        {{-- Phone --}}
        <label class="fl">{{ app()->getLocale()=='km' ? 'លេខទូរស័ព្ទ' : 'Phone' }} <span style="color:#94a3b8;font-weight:400;">({{ app()->getLocale()=='km' ? 'ស្រេចចិត្ត' : 'optional' }})</span></label>
        <input type="text" name="phone" class="fi"
               value="{{ old('phone') }}"
               placeholder=""/>

        {{-- Password --}}
        <label class="fl">{{ app()->getLocale()=='km' ? 'ពាក្យសម្ងាត់ *' : 'Password *' }}</label>
        <div class="pw-wrap">
            <input type="password" name="password" id="pw1" class="fi {{ $errors->has('password') ? 'is-invalid':'' }}"
                   required placeholder="" style="margin-bottom:{{ $errors->has('password') ? '4px':'14px' }};"/>
            <button type="button" class="pw-eye" onclick="togglePw('pw1','eye1')" id="eye1">👁</button>
        </div>
        @error('password')<span class="err-msg">{{ $message }}</span>@enderror

        {{-- Confirm Password --}}
        <label class="fl">{{ app()->getLocale()=='km' ? 'បញ្ជាក់ពាក្យសម្ងាត់ *' : 'Confirm Password *' }}</label>
        <div class="pw-wrap">
            <input type="password" name="password_confirmation" id="pw2"
                   class="fi" required placeholder=""/>
            <button type="button" class="pw-eye" onclick="togglePw('pw2','eye2')" id="eye2">👁</button>
        </div>

        {{-- Terms --}}
        <label style="display:flex;align-items:flex-start;gap:8px;font-size:12px;color:#64748b;cursor:pointer;margin-bottom:16px;">
            <input type="checkbox" name="terms" required style="accent-color:#f59e0b;margin-top:2px;flex-shrink:0;"/>
            <span>
                {{ app()->getLocale()=='km' ? 'ខ្ញុំយល់ព្រមនឹង' : 'I agree to the' }}
                <a href="#" style="color:#f59e0b;font-weight:700;text-decoration:none;">{{ app()->getLocale()=='km' ? 'លក្ខខណ្ឌ' : 'Terms' }}</a>
                {{ app()->getLocale()=='km' ? 'និង' : 'and' }}
                <a href="#" style="color:#f59e0b;font-weight:700;text-decoration:none;">{{ app()->getLocale()=='km' ? 'គោលការណ៍' : 'Privacy Policy' }}</a>
            </span>
        </label>

        <button type="submit" class="btn-register">
            {{ app()->getLocale()=='km' ? 'ចុះឈ្មោះ' : 'Create Account' }}
        </button>
    </form>



    <div class="login-link">
        {{ app()->getLocale()=='km' ? 'មានគណនីហើយ?' : 'Already have an account?' }}
        <a href="{{ route('login') }}">{{ app()->getLocale()=='km' ? 'ចូលប្រព័ន្ធ' : 'Sign in' }}</a>
    </div>

</div>
</div>

<script>
<script>
function togglePw(id, eyeId) {
    const pw  = document.getElementById(id);
    const eye = document.getElementById(eyeId);
    pw.type   = pw.type === 'password' ? 'text' : 'password';
    eye.textContent = pw.type === 'password' ? '👁' : '🙈';
}
</script>
</script>
</body>
</html>