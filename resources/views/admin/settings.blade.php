@extends('layouts.admin')
@section('title', 'Settings')

@section('content')
<style>
.set-wrap { max-width: 800px; margin: 0 auto; padding: 28px 16px 80px; }
.set-title { font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
.set-sub   { font-size: 13px; color: #64748b; margin-bottom: 24px; }

/* Tabs */
.tab-nav  { display: flex; gap: 4px; border-bottom: 2px solid #e2e8f0; margin-bottom: 28px; flex-wrap: wrap; }
.tab-btn  { padding: 10px 22px; font-size: 13px; font-weight: 700; color: #64748b; cursor: pointer;
            border: none; background: none; border-bottom: 3px solid transparent; margin-bottom: -2px;
            border-radius: 8px 8px 0 0; transition: all .15s; display: flex; align-items: center; gap: 6px; }
.tab-btn:hover  { color: #0f172a; background: #f8fafc; }
.tab-btn.active { color: #f59e0b; border-bottom-color: #f59e0b; background: #fffbeb; }
.tab-pane { display: none; }
.tab-pane.active { display: block; }

/* Card */
.s-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0;
           box-shadow: 0 1px 4px rgba(0,0,0,.04); margin-bottom: 20px; overflow: hidden; }
.s-card-h    { padding: 16px 22px; border-bottom: 1px solid #f1f5f9; }
.s-card-title { font-size: 13px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px; }
.s-card-body { padding: 22px; }

/* Fields */
.fl { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px; display: block; }
.fi { width: 100%; border: 1.5px solid #e2e8f0; border-radius: 9px; padding: 9px 12px;
      font-size: 13px; font-family: inherit; outline: none; transition: border-color .15s; background: #fff; }
.fi:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.1); }

/* Button */
.btn-save { background: #0f172a; color: #fff; border: none; border-radius: 10px;
            padding: 10px 26px; font-size: 13px; font-weight: 700; cursor: pointer;
            transition: all .2s; font-family: inherit; }
.btn-save:hover { background: #f59e0b; color: #000; }

/* Alerts */
.alert-ok { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 16px; font-size: 13px; margin-bottom: 18px; }
.alert-er { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 10px; padding: 12px 16px; font-size: 13px; margin-bottom: 18px; }

/* Toggle */
.tog-wrap { display: flex; align-items: center; gap: 10px; }
.tog { position: relative; width: 44px; height: 24px; }
.tog input { opacity: 0; width: 0; height: 0; }
.tog-sl { position: absolute; inset: 0; background: #e2e8f0; border-radius: 24px; cursor: pointer; transition: .2s; }
.tog-sl::before { content:''; position:absolute; width:18px; height:18px; left:3px; top:3px; background:#fff; border-radius:50%; transition:.2s; }
.tog input:checked + .tog-sl { background: #f59e0b; }
.tog input:checked + .tog-sl::before { transform: translateX(20px); }
.tog-label { font-size: 13px; font-weight: 600; color: #374151; }

/* Preview box */
.preview-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-top: 4px; }
.preview-row { display: flex; justify-content: space-between; font-size: 13px; padding: 4px 0; color: #6b7280; }
.preview-total { display: flex; justify-content: space-between; font-size: 15px; font-weight: 800; padding-top: 10px; margin-top: 8px; border-top: 2px solid #e2e8f0; }
.preview-total span:last-child { color: #f59e0b; }

/* Password strength */
.pw-strength { height: 4px; border-radius: 4px; margin-top: 6px; transition: all .3s; background: #e2e8f0; width: 0; }
.pw-hint { font-size: 11px; color: #94a3b8; margin-top: 4px; min-height: 16px; }
</style>

<div class="set-wrap">
    <div class="set-title"> Settings</div>
    

    {{-- Alerts --}}
    @if(session('tax_success'))      <div class="alert-ok">✅ {{ session('tax_success') }}</div> @endif
    @if(session('tax_error'))        <div class="alert-er">❌ {{ session('tax_error') }}</div> @endif
    @if(session('profile_success'))  <div class="alert-ok">✅ {{ session('profile_success') }}</div> @endif
    @if(session('profile_error'))    <div class="alert-er">❌ {{ session('profile_error') }}</div> @endif
    @if(session('password_success')) <div class="alert-ok">✅ {{ session('password_success') }}</div> @endif
    @if(session('password_error'))   <div class="alert-er">❌ {{ session('password_error') }}</div> @endif
    @if($errors->any())
        <div class="alert-er">@foreach($errors->all() as $e)<div>❌ {{ $e }}</div>@endforeach</div>
    @endif

    {{-- Tab Navigation --}}
    <div class="tab-nav">
        <button class="tab-btn" id="tab-profile"  onclick="switchTab('profile')">👤 Profile</button>
        <button class="tab-btn" id="tab-password" onclick="switchTab('password')">🔒 Password</button>
        <button class="tab-btn" id="tab-tax"      onclick="switchTab('tax')">🧾 Tax</button>
    </div>

    {{-- ══════════════════════════════════════════════
         TAB 1: TAX
    ══════════════════════════════════════════════ --}}
    <div class="tab-pane" id="pane-tax">
        <form method="POST" action="{{ route('admin.settings.tax') }}">
        @csrf

        <div class="s-card">
            <div class="s-card-h"><div class="s-card-title">🧾 Tax Configuration</div></div>
            <div class="s-card-body">

                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="fl">Tax Rate (%)</label>
                        <input type="number" name="tax_rate" id="taxRateInput"
                               class="fi" step="0.01" min="0" max="100"
                               value="{{ old('tax_rate', $taxRate ?? 10) }}"
                               oninput="updateTaxPreview()"
                               style="font-size:18px;font-weight:700;padding:12px;"/>
                        <div style="font-size:11px;color:#94a3b8;margin-top:4px;">Enter 0 to disable tax</div>
                    </div>
                    <div class="col-md-7">
                        <label class="fl">Live Preview</label>
                        <div class="preview-box">
                            <div class="preview-row">
                                <span>Example Order</span><span>$100.00</span>
                            </div>
                            <div class="preview-row">
                                <span>Tax (<span id="prev-rate">{{ $taxRate ?? 10 }}</span>%)</span>
                                <span id="prev-tax">${{ number_format($taxRate ?? 10, 2) }}</span>
                            </div>
                            <div class="preview-total">
                                <span>Total</span>
                                <span id="prev-total">${{ number_format(100 + ($taxRate ?? 10), 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <div class="tog-wrap">
                            <label class="tog">
                                <input type="checkbox" name="tax_on_shipping" value="1"
                                       {{ ($settings['tax_on_shipping'] ?? false) ? 'checked' : '' }}/>
                                <span class="tog-sl"></span>
                            </label>
                            <span class="tog-label">Apply tax on shipping fee</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="tog-wrap">
                            <label class="tog">
                                <input type="checkbox" name="prices_include_tax" value="1"
                                       {{ ($settings['prices_include_tax'] ?? false) ? 'checked' : '' }}/>
                                <span class="tog-sl"></span>
                            </label>
                            <span class="tog-label">Product prices already include tax</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div style="display:flex;align-items:center;gap:12px;">
            <button type="submit" class="btn-save"> Save Tax Settings</button>
            <span>
        </div>

        </form>
    </div>

    {{-- ══════════════════════════════════════════════
         TAB 2: PROFILE (no photo)
    ══════════════════════════════════════════════ --}}
    <div class="tab-pane" id="pane-profile">
        <form method="POST" action="{{ route('admin.profile.update') }}">
        @csrf

        <div class="s-card">
            <div class="s-card-h"><div class="s-card-title">👤 Admin Profile</div></div>
            <div class="s-card-body">

                {{-- Info row --}}
                <div style="display:flex;align-items:center;gap:14px;padding:14px 0 22px;border-bottom:1px solid #f1f5f9;margin-bottom:22px;">
                    <div style="width:54px;height:54px;border-radius:50%;background:#fef9c3;border:2px solid #f59e0b;
                                display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;">
                        👤
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:800;color:#0f172a;">{{ auth()->user()->name }}</div>
                        <div style="font-size:13px;color:#64748b;margin-top:2px;">{{ auth()->user()->email }}</div>
                        <span style="display:inline-block;margin-top:5px;padding:2px 10px;background:#fef9c3;
                                     border-radius:20px;font-size:11px;font-weight:700;color:#92400e;">
                            {{ ucfirst(auth()->user()->role ?? 'Admin') }}
                        </span>
                    </div>
                </div>

                

            </div>
        </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════
         TAB 3: CHANGE PASSWORD
    ══════════════════════════════════════════════ --}}
    <div class="tab-pane" id="pane-password">
        <form method="POST" action="{{ route('admin.password.update') }}">
        @csrf

        <div class="s-card">
            <div class="s-card-h"><div class="s-card-title"> Change Password</div></div>
            <div class="s-card-body">

                <div class="row g-3">
                    <div class="col-12">
                        <label class="fl">Current Password</label>
                        <div style="position:relative;">
                            <input type="password" name="current_password" id="cur_pw" class="fi"
                                   placeholder="Enter your current password"
                                   style="padding-right:44px;" required/>
                            <button type="button" onclick="togglePw('cur_pw','eye_cur')"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);
                                           background:none;border:none;cursor:pointer;font-size:16px;color:#94a3b8;">
                                <span id="eye_cur">👁</span>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="fl">New Password</label>
                        <div style="position:relative;">
                            <input type="password" name="password" id="new_pw" class="fi"
                                   placeholder="Min 8 characters"
                                   style="padding-right:44px;"
                                   oninput="checkStrength(this.value)" required/>
                            <button type="button" onclick="togglePw('new_pw','eye_new')"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);
                                           background:none;border:none;cursor:pointer;font-size:16px;color:#94a3b8;">
                                <span id="eye_new">👁</span>
                            </button>
                        </div>
                        <div class="pw-strength" id="pw-bar"></div>
                        <div class="pw-hint" id="pw-hint">Enter a new password</div>
                    </div>

                    <div class="col-md-6">
                        <label class="fl">Confirm New Password</label>
                        <div style="position:relative;">
                            <input type="password" name="password_confirmation" id="conf_pw" class="fi"
                                   placeholder="Repeat new password"
                                   style="padding-right:44px;"
                                   oninput="checkMatch()" required/>
                            <button type="button" onclick="togglePw('conf_pw','eye_conf')"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);
                                           background:none;border:none;cursor:pointer;font-size:16px;color:#94a3b8;">
                                <span id="eye_conf">👁</span>
                            </button>
                        </div>
                        <div class="pw-hint" id="match-hint"></div>
                    </div>
                </div>

              
            </div>
        </div>

        <button type="submit" class="btn-save"> Update Password</button>
        </form>
    </div>

</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    document.getElementById('pane-' + tab).classList.add('active');
    history.replaceState(null, '', '?tab=' + tab);
}

function updateTaxPreview() {
    const rate = parseFloat(document.getElementById('taxRateInput').value) || 0;
    const tax  = (100 * rate / 100).toFixed(2);
    const tot  = (100 + parseFloat(tax)).toFixed(2);
    document.getElementById('prev-rate').textContent  = rate;
    document.getElementById('prev-tax').textContent   = '$' + tax;
    document.getElementById('prev-total').textContent = '$' + tot;
}

function togglePw(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const eye   = document.getElementById(eyeId);
    input.type  = input.type === 'password' ? 'text' : 'password';
    eye.textContent = input.type === 'password' ? '👁' : '🙈';
}

function checkStrength(val) {
    const bar  = document.getElementById('pw-bar');
    const hint = document.getElementById('pw-hint');
    let score  = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors = ['#e2e8f0','#ef4444','#f59e0b','#3b82f6','#22c55e'];
    const labels = ['','Weak','Fair','Good','Strong'];
    bar.style.width      = (score * 25) + '%';
    bar.style.background = colors[score];
    hint.textContent     = score > 0 ? labels[score] + ' password' : 'Enter a new password';
    hint.style.color     = colors[score];
}

function checkMatch() {
    const pw   = document.getElementById('new_pw').value;
    const conf = document.getElementById('conf_pw').value;
    const hint = document.getElementById('match-hint');
    if (!conf) { hint.textContent = ''; return; }
    if (pw === conf) {
        hint.textContent = '✅ Passwords match';
        hint.style.color = '#22c55e';
    } else {
        hint.textContent = '❌ Passwords do not match';
        hint.style.color = '#ef4444';
    }
}

(function () {
    @if(session('tax_success') || session('tax_error'))
        switchTab('tax');
    @elseif(session('profile_success') || session('profile_error'))
        switchTab('profile');
    @elseif(session('password_success') || session('password_error'))
        switchTab('password');
    @elseif(request()->get('tab'))
        switchTab('{{ request()->get('tab') }}');
    @else
        switchTab('tax');
    @endif
})();
</script>
@endsection