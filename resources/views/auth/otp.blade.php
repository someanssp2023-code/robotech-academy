@extends('layouts.app')
@section('title', app()->getLocale()=='km' ? 'បញ្ជាក់ OTP' : 'Verify OTP')

@section('content')
<style>
    .auth-wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f8fafc; padding: 24px; }
    .auth-card { background: #fff; border-radius: 20px; padding: 40px; width: 100%; max-width: 420px; box-shadow: 0 4px 24px rgba(0,0,0,.07); border: 1px solid #e2e8f0; text-align: center; }
    .otp-icon { font-size: 48px; margin-bottom: 16px; }
    .auth-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
    .auth-sub { font-size: 13px; color: #64748b; margin-bottom: 28px; line-height: 1.6; }
    .email-highlight { font-weight: 700; color: #0f172a; }
    .otp-inputs { display: flex; gap: 10px; justify-content: center; margin: 24px 0; }
    .otp-input {
        width: 52px; height: 60px; border: 2px solid #e2e8f0;
        border-radius: 12px; font-size: 24px; font-weight: 800;
        text-align: center; outline: none; transition: all .15s;
        color: #0f172a; font-family: monospace;
    }
    .otp-input:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.1); }
    .otp-input.filled { border-color: #0f172a; background: #f8fafc; }
    .btn-verify { width: 100%; background: #0f172a; color: #fff; border: none; border-radius: 10px; padding: 13px; font-size: 14px; font-weight: 700; cursor: pointer; transition: all .2s; font-family: inherit; }
    .btn-verify:hover { background: #f59e0b; color: #000; }
    .btn-verify:disabled { background: #e2e8f0; color: #94a3b8; cursor: not-allowed; }
    .resend-wrap { margin-top: 20px; font-size: 13px; color: #94a3b8; }
    .btn-resend { background: none; border: none; color: #f59e0b; font-weight: 700; cursor: pointer; font-size: 13px; font-family: inherit; }
    .btn-resend:disabled { color: #94a3b8; cursor: not-allowed; }
    .alert-s { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 16px; font-size: 13px; margin-bottom: 20px; }
    .alert-e { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 10px; padding: 12px 16px; font-size: 13px; margin-bottom: 20px; }
    .timer { font-size: 13px; color: #ef4444; font-weight: 600; margin-top: 6px; }
</style>

<div class="auth-wrap">
    <div class="auth-card">
        <div class="otp-icon">📧</div>
        <div class="auth-title">{{ app()->getLocale()=='km' ? 'បញ្ជាក់អ៊ីមែល' : 'Verify Your Email' }}</div>
        <div class="auth-sub">
            {{ app()->getLocale()=='km' ? 'យើងបានផ្ញើលេខកូដ 6 ខ្ទង់ទៅ' : "We've sent a 6-digit code to" }}
            <br/>
            <span class="email-highlight">{{ session('otp_register.email') }}</span>
        </div>

        @if(session('success'))
            <div class="alert-s">✅ {{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert-e">
                @foreach($errors->all() as $err) <div>❌ {{ $err }}</div> @endforeach
            </div>
        @endif

        <form action="{{ route('register.verify-otp') }}" method="POST" id="otp-form">
            @csrf
            <input type="hidden" name="otp" id="otp-hidden"/>

            {{-- OTP Input Boxes --}}
            <div class="otp-inputs">
                @for($i = 1; $i <= 6; $i++)
                    <input type="text" class="otp-input" maxlength="1"
                           inputmode="numeric" pattern="[0-9]"
                           id="otp-{{ $i }}" autocomplete="off"/>
                @endfor
            </div>

            <div class="timer" id="timer-wrap">
                ⏱ {{ app()->getLocale()=='km' ? 'ផុតកំណត់ក្នុង' : 'Expires in' }}:
                <span id="timer">10:00</span>
            </div>

            <button type="submit" class="btn-verify" id="btn-verify" disabled>
                {{ app()->getLocale()=='km' ? 'បញ្ជាក់ OTP' : 'Verify OTP' }}
            </button>
        </form>

        <div class="resend-wrap">
            {{ app()->getLocale()=='km' ? 'មិនទទួលបានអ៊ីមែល?' : "Didn't receive the email?" }}
            <form action="{{ route('register.resend-otp') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn-resend" id="btn-resend" disabled>
                    {{ app()->getLocale()=='km' ? 'ផ្ញើ OTP ឡើងវិញ' : 'Resend OTP' }}
                    (<span id="resend-timer">60</span>s)
                </button>
            </form>
        </div>

        <p style="margin-top:20px;font-size:13px;color:#94a3b8;">
            <a href="{{ route('register') }}" style="color:#64748b;text-decoration:none;">
                ← {{ app()->getLocale()=='km' ? 'ត្រឡប់' : 'Back to Register' }}
            </a>
        </p>
    </div>
</div>

@push('scripts')
<script>
const inputs = document.querySelectorAll('.otp-input');
const hiddenInput = document.getElementById('otp-hidden');
const btnVerify   = document.getElementById('btn-verify');
const btnResend   = document.getElementById('btn-resend');

// Auto focus & move between inputs
inputs.forEach((input, idx) => {
    input.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value && idx < inputs.length - 1) {
            inputs[idx + 1].focus();
        }
        updateHidden();
        this.classList.toggle('filled', !!this.value);
    });

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !this.value && idx > 0) {
            inputs[idx - 1].focus();
            inputs[idx - 1].value = '';
            inputs[idx - 1].classList.remove('filled');
            updateHidden();
        }
    });

    input.addEventListener('paste', function(e) {
        e.preventDefault();
        const pasted = e.clipboardData.getData('text').replace(/[^0-9]/g, '').substring(0, 6);
        pasted.split('').forEach((char, i) => {
            if (inputs[i]) {
                inputs[i].value = char;
                inputs[i].classList.add('filled');
            }
        });
        updateHidden();
        if (pasted.length === 6) btnVerify.disabled = false;
    });
});

function updateHidden() {
    const otp = Array.from(inputs).map(i => i.value).join('');
    hiddenInput.value = otp;
    btnVerify.disabled = otp.length !== 6;
}

// Countdown timer (10 min)
let seconds = 600;
const timerEl = document.getElementById('timer');
const timerInterval = setInterval(() => {
    seconds--;
    if (seconds <= 0) {
        clearInterval(timerInterval);
        timerEl.textContent = '{{ app()->getLocale()=="km" ? "ផុតកំណត់" : "Expired" }}';
        timerEl.style.color = '#ef4444';
        btnVerify.disabled = true;
        return;
    }
    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
    const s = (seconds % 60).toString().padStart(2, '0');
    timerEl.textContent = `${m}:${s}`;
}, 1000);

// Resend countdown (60 sec)
let resendSec = 60;
const resendEl = document.getElementById('resend-timer');
const resendInterval = setInterval(() => {
    resendSec--;
    resendEl.textContent = resendSec;
    if (resendSec <= 0) {
        clearInterval(resendInterval);
        btnResend.disabled = false;
        resendEl.parentElement.innerHTML = '{{ app()->getLocale()=="km" ? "ផ្ញើ OTP ឡើងវិញ" : "Resend OTP" }}';
    }
}, 1000);

// Auto focus first input
inputs[0].focus();
</script>
@endpush
@endsection