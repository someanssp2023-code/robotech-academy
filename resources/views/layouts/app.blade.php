<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title', 'Robotech Academy')</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    @if(app()->getLocale() == 'km')
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <style>body { font-family: 'Kantumruy Pro', sans-serif !important; }</style>
    @endif
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8f8f6; color: #111; margin: 0; }

        /* ── Navbar ── */
        .navbar {
            background: #000;
            padding: 14px 0;
            position: sticky; top: 0; z-index: 999;
            border-bottom: 1px solid #1a1a1a;
        }
        .navbar-inner {
            display: flex;
            align-items: center;
            gap: 16px;
            width: 100%;
        }

        /* Brand — left anchor */
        .navbar-brand {
            font-size: 20px; font-weight: 900;
            color: #fff; text-decoration: none;
            letter-spacing: -0.02em;
            flex-shrink: 0;
        }
        .navbar-brand span { color: #f59e0b; }

        /* Nav links — next to brand */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 2px;
        }
        .nav-link {
            color: #9ca3af !important;
            font-size: 13px; font-weight: 500;
            text-decoration: none; padding: 6px 12px !important;
            transition: color .15s;
            white-space: nowrap;
        }
        .nav-link:hover { color: #fff !important; }
        .nav-link.active { color: #f59e0b !important; }

        /* Search — center, grows to fill space */
        .search-wrap {
            position: relative;
            flex: 1;
            max-width: 320px;
            margin: 0 8px;
        }
        .search-input {
            background: #111; border: 1px solid #222;
            border-radius: 8px; padding: 7px 14px 7px 36px;
            font-size: 13px; color: #fff; width: 100%;
            outline: none; transition: border-color .15s;
            font-family: inherit;
        }
        .search-input::placeholder { color: #555; }
        .search-input:focus { border-color: #f59e0b; }
        .search-icon {
            position: absolute; left: 11px; top: 50%;
            transform: translateY(-50%); color: #555; font-size: 13px;
            pointer-events: none;
        }

        /* Right side — pushed to far right with margin-left:auto */
        .nav-right {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
            flex-shrink: 0;
        }

        .nav-icon-btn {
            display: flex; align-items: center; justify-content: center;
            width: 36px; height: 36px;
            border-radius: 8px; background: #111;
            border: 1px solid #222; color: #9ca3af;
            text-decoration: none; font-size: 16px;
            transition: all .15s; position: relative;
            flex-shrink: 0;
        }
        .nav-icon-btn:hover { border-color: #f59e0b; color: #f59e0b; }

        .nav-badge {
            position: absolute; top: -5px; right: -5px;
            background: #f59e0b; color: #000;
            font-size: 9px; font-weight: 800;
            width: 17px; height: 17px;
            border-radius: 50%; display: flex;
            align-items: center; justify-content: center;
        }

        .btn-nav-login {
            background: transparent; border: 1px solid #222;
            color: #9ca3af; border-radius: 8px;
            padding: 7px 16px; font-size: 13px; font-weight: 600;
            text-decoration: none; transition: all .15s;
            white-space: nowrap;
        }
        .btn-nav-login:hover { border-color: #f59e0b; color: #f59e0b; }

        .btn-nav-register {
            background: #f59e0b; color: #000;
            border-radius: 8px; padding: 7px 16px;
            font-size: 13px; font-weight: 700;
            text-decoration: none; transition: all .15s;
            border: 1px solid #f59e0b;
            white-space: nowrap;
        }
        .btn-nav-register:hover { background: #e09614; }

        /* Lang switcher */
        .lang-dropdown { position: relative; }
        .lang-btn {
            display: flex; align-items: center; gap: 5px;
            background: #111; border: 1px solid #222;
            border-radius: 8px; padding: 6px 10px;
            font-size: 12px; font-weight: 600; color: #9ca3af;
            cursor: pointer; transition: all .15s; font-family: inherit;
            white-space: nowrap;
        }
        .lang-btn:hover { border-color: #f59e0b; color: #fff; }
        .lang-menu {
            display: none; position: absolute;
            top: calc(100% + 6px); right: 0;
            background: #111; border: 1px solid #222;
            border-radius: 10px; padding: 6px; min-width: 130px;
            box-shadow: 0 8px 24px rgba(0,0,0,.4); z-index: 999;
        }
        .lang-menu a {
            display: flex; align-items: center; gap: 8px;
            padding: 7px 10px; border-radius: 7px;
            text-decoration: none; font-size: 12px; font-weight: 600;
            color: #9ca3af; transition: background .1s;
        }
        .lang-menu a:hover { background: #1a1a1a; color: #fff; }
        .lang-menu a.active { color: #fff; background: #1a1a1a; }

        /* User dropdown */
        .user-dropdown { position: relative; }
        .user-btn {
            display: flex; align-items: center; gap: 7px;
            background: #111; border: 1px solid #222;
            border-radius: 8px; padding: 6px 12px;
            font-size: 12px; font-weight: 600; color: #9ca3af;
            cursor: pointer; transition: all .15s; font-family: inherit;
            white-space: nowrap;
        }
        .user-btn:hover { border-color: #f59e0b; color: #fff; }
        .user-av {
            width: 22px; height: 22px; border-radius: 50%;
            background: #6366f1; display: flex;
            align-items: center; justify-content: center;
            font-size: 10px; font-weight: 800; color: #fff;
            flex-shrink: 0;
        }
        .user-menu {
            display: none; position: absolute;
            top: calc(100% + 6px); right: 0;
            background: #111; border: 1px solid #222;
            border-radius: 10px; padding: 6px; min-width: 160px;
            box-shadow: 0 8px 24px rgba(0,0,0,.4); z-index: 999;
        }
        .user-menu a, .user-menu button {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 10px; border-radius: 7px;
            text-decoration: none; font-size: 12px; font-weight: 600;
            color: #9ca3af; transition: background .1s; width: 100%;
            background: none; border: none; cursor: pointer;
            font-family: inherit; text-align: left;
        }
        .user-menu a:hover, .user-menu button:hover { background: #1a1a1a; color: #fff; }
        .user-menu .divider { height: 1px; background: #1e1e1e; margin: 4px 0; }

        /* ── Footer ── */
        .footer {
            background: #000; color: #9ca3af;
            padding: 48px 0 24px;
            border-top: 1px solid #1a1a1a;
            margin-top: 64px;
        }
        .footer-brand { font-size: 18px; font-weight: 800; color: #fff; margin-bottom: 6px; }
        .footer-sub   { font-size: 12px; color: #4b5563; }
        .footer-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #555; margin-bottom: 14px; }
        .footer-link  { display: block; font-size: 13px; color: #6b7280; text-decoration: none; margin-bottom: 8px; transition: color .15s; }
        .footer-link:hover { color: #f59e0b; }
        .footer-bottom { border-top: 1px solid #111; margin-top: 40px; padding-top: 20px; font-size: 12px; color: #4b5563; }

        /* Flash */
        .flash-success { background: #d1fae5; color: #065f46; border: none; border-radius: 10px; }
        .flash-error   { background: #fee2e2; color: #991b1b; border: none; border-radius: 10px; }
    </style>
    @stack('styles')
</head>
<body>

{{-- ── NAVBAR ── --}}
<nav class="navbar">
    <div class="container">
        <div class="navbar-inner">

            {{-- Brand --}}
            <a href="{{ route('home') }}" class="navbar-brand">
                ROBO<span>TECH</span>
            </a>

            {{-- Nav links --}}
            <div class="nav-links d-none d-lg-flex">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    {{ __('app.home') }}
                </a>
                <a href="{{ route('products') }}" class="nav-link {{ request()->routeIs('products*') ? 'active' : '' }}">
                    {{ __('app.products') }}
                </a>
            </div>

            {{-- Search — grows to fill gap --}}
            <form action="{{ route('products') }}" method="GET" class="search-wrap d-none d-md-flex">
                <span class="search-icon">🔍</span>
                <input type="text" name="search" class="search-input"
                       placeholder="{{ __('app.search') }}..."
                       value="{{ request('search') }}"/>
            </form>

            {{-- ★ RIGHT SIDE ★ --}}
            <div class="nav-right">

                @auth
                {{-- Cart --}}
                <a href="{{ route('cart') }}" class="nav-icon-btn" title="{{ __('app.cart') }}">
                    🛒
                    @if(Auth::user()->cartItems->sum('quantity') > 0)
                        <span class="nav-badge">{{ Auth::user()->cartItems->sum('quantity') }}</span>
                    @endif
                </a>

                {{-- Wishlist --}}
                <a href="{{ route('wishlist') }}" class="nav-icon-btn" title="{{ __('app.wishlist') }}">
                    ♡
                    @if(Auth::user()->wishlists->count() > 0)
                        <span class="nav-badge">{{ Auth::user()->wishlists->count() }}</span>
                    @endif
                </a>

                {{-- Notification Bell --}}
                <div class="notif-wrap" id="userNotifWrap" style="position:relative;">
                    <button class="nav-icon-btn" id="userNotifBtn" onclick="toggleUserNotif()" title="Notifications" style="background:none;border:1px solid #222;cursor:pointer;font-family:inherit;">
                        🔔
                        <span id="userNotifBadge" style="
                            display:none;position:absolute;top:-5px;right:-5px;
                            background:#f59e0b;color:#000;font-size:9px;font-weight:800;
                            min-width:17px;height:17px;border-radius:50%;
                            align-items:center;justify-content:center;padding:0 3px;
                        ">0</span>
                    </button>
                    <div id="userNotifDropdown" style="
                        display:none;position:absolute;top:calc(100% + 8px);right:0;
                        width:300px;background:#111;border:1px solid #222;
                        border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.4);
                        z-index:9999;overflow:hidden;
                    ">
                        <div style="padding:10px 14px;border-bottom:1px solid #1e1e1e;display:flex;align-items:center;justify-content:space-between;">
                            <span style="font-size:13px;font-weight:700;color:#fff;">Notifications</span>
                            <button onclick="userMarkAllRead()" style="font-size:11px;color:#f59e0b;background:none;border:none;cursor:pointer;font-weight:600;">Mark all read</button>
                        </div>
                        <div id="userNotifList" style="max-height:300px;overflow-y:auto;">
                            <div style="padding:24px 14px;text-align:center;color:#555;font-size:13px;">No notifications</div>
                        </div>
                    </div>
                </div>

                {{-- User dropdown --}}
                <div class="user-dropdown">
                    <button class="user-btn" onclick="toggleUser()">
                        <div class="user-av">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                        {{ Str::limit(Auth::user()->name, 12) }}
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="user-menu" id="user-menu">
                        <a href="{{ route('my.orders') }}">{{ __('app.my_orders') }}</a>
                        <div class="divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">{{ __('app.logout') }}</button>
                        </form>
                    </div>
                </div>

                @else
                <a href="{{ route('login') }}" class="btn-nav-login">{{ __('app.login') }}</a>
                <a href="{{ route('register') }}" class="btn-nav-register">{{ __('app.register') }}</a>
                @endauth

                {{-- Language switcher --}}
                <div class="lang-dropdown">
                    <button class="lang-btn" onclick="toggleLang()">
                        @if(app()->getLocale() == 'km')
                            ខ្មែរ
                        @else
                            EN
                        @endif
                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="lang-menu" id="lang-menu">
                        <a href="{{ route('language.switch', 'en') }}" class="{{ app()->getLocale() == 'en' ? 'active' : '' }}">
                            English @if(app()->getLocale() == 'en') ✓ @endif
                        </a>
                        <a href="{{ route('language.switch', 'km') }}" class="{{ app()->getLocale() == 'km' ? 'active' : '' }}">
                            ខ្មែរ @if(app()->getLocale() == 'km') ✓ @endif
                        </a>
                    </div>
                </div>

            </div>{{-- /nav-right --}}

        </div>
    </div>
</nav>

{{-- Flash --}}
<div class="container mt-3">
    @if(session('success'))
        <div class="alert flash-success alert-dismissible fade show" style="font-size:13px;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert flash-error alert-dismissible fade show" style="font-size:13px;">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

{{-- Content --}}
@yield('content')

{{-- ── FOOTER ── --}}
<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand">ROBOTECH</div>
                <div class="footer-sub">Siem Reap, Cambodia</div>
            </div>
            <div class="col-6 col-lg-2">
                <div class="footer-title">{{ __('app.products') }}</div>
                <a href="{{ route('products') }}?on_sale=1" class="footer-link">{{ __('app.sale_items') }}</a>
                <a href="{{ route('products') }}?featured=1" class="footer-link">{{ __('app.featured_products') }}</a>
            </div>
            <div class="col-6 col-lg-2">
                <div class="footer-title">{{ __('app.account') }}</div>
                @auth
                    <a href="{{ route('my.orders') }}" class="footer-link">{{ __('app.my_orders') }}</a>
                    <a href="{{ route('wishlist') }}" class="footer-link">{{ __('app.wishlist') }}</a>
                    <a href="{{ route('cart') }}" class="footer-link">{{ __('app.cart') }}</a>
                @else
                    <a href="{{ route('login') }}" class="footer-link">{{ __('app.login') }}</a>
                    <a href="{{ route('register') }}" class="footer-link">{{ __('app.register') }}</a>
                @endauth
            </div>
            <div class="col-lg-3">
                <div class="footer-title">{{ __('app.contact') }}</div>
                <p style="font-size:13px;color:#6b7280;line-height:1.7;margin:0;">
                    Siem Reap, Cambodia<br>
                    robotech@gmail.com
                </p>
            </div>
        </div>
        <div class="footer-bottom d-flex justify-content-between flex-wrap gap-2">
            <span>© {{ date('Y') }} Robotech Academy. {{ __('app.rights_reserved') }}.</span>
        </div>
    </div>
</footer>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script>
function toggleLang() {
    const m = document.getElementById('lang-menu');
    m.style.display = m.style.display === 'block' ? 'none' : 'block';
}
function toggleUser() {
    const m = document.getElementById('user-menu');
    m.style.display = m.style.display === 'block' ? 'none' : 'block';
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.lang-dropdown')) {
        const m = document.getElementById('lang-menu');
        if (m) m.style.display = 'none';
    }
    if (!e.target.closest('.user-dropdown')) {
        const m = document.getElementById('user-menu');
        if (m) m.style.display = 'none';
    }
});
</script>

{{-- ── User Notification JS (only for logged-in users) ── --}}
@auth
<script>
(function() {
    const COUNT_URL   = '{{ route("user.notifications.count") }}';
    const LIST_URL    = '{{ route("user.notifications.list") }}';
    const READ_URL    = '/my-notifications/{id}/read';
    const READALL_URL = '{{ route("user.notifications.read-all") }}';
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    function fetchCount() {
        fetch(COUNT_URL).then(r=>r.json()).then(d=>{
            const b = document.getElementById('userNotifBadge');
            if (d.count > 0) { b.textContent = d.count > 99 ? '99+' : d.count; b.style.display='flex'; }
            else b.style.display='none';
        }).catch(()=>{});
    }

    function fetchList() {
        fetch(LIST_URL).then(r=>r.json()).then(d=>{
            const list = document.getElementById('userNotifList');
            if (!d.notifications || d.notifications.length === 0) {
                list.innerHTML = '<div style="padding:24px 14px;text-align:center;color:#555;font-size:13px;">No notifications</div>';
                return;
            }
            list.innerHTML = d.notifications.map(n => `
                <div onclick="userClickNotif(${n.id},'${n.url||''}')" style="
                    padding:10px 14px;display:flex;gap:10px;cursor:pointer;
                    border-bottom:1px solid #1a1a1a;transition:background .1s;
                    background:${n.is_read?'transparent':'rgba(245,158,11,.06)'};
                " onmouseover="this.style.background='#1a1a1a'" onmouseout="this.style.background='${n.is_read?'transparent':'rgba(245,158,11,.06)'}'">
                    <div style="font-size:18px;flex-shrink:0;line-height:1;">${n.icon}</div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;font-weight:700;color:#fff;">${n.title}</div>
                        <div style="font-size:11px;color:#6b7280;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${n.message}</div>
                        <div style="font-size:10px;color:#4b5563;margin-top:3px;">${n.time}</div>
                    </div>
                    ${!n.is_read?'<div style="width:6px;height:6px;border-radius:50%;background:#f59e0b;flex-shrink:0;margin-top:4px;"></div>':''}
                </div>
            `).join('');
        }).catch(()=>{});
    }

    window.toggleUserNotif = function() {
        const d = document.getElementById('userNotifDropdown');
        const open = d.style.display === 'block';
        d.style.display = open ? 'none' : 'block';
        if (!open) fetchList();
    };
    window.userClickNotif = function(id, url) {
        fetch(READ_URL.replace('{id}',id),{method:'POST',headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json'}})
            .then(()=>{ fetchCount(); if(url) window.location.href=url; else fetchList(); }).catch(()=>{});
    };
    window.userMarkAllRead = function() {
        fetch(READALL_URL,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json'}})
            .then(()=>{ fetchCount(); fetchList(); }).catch(()=>{});
    };
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#userNotifWrap')) {
            const d = document.getElementById('userNotifDropdown');
            if (d) d.style.display = 'none';
        }
    });
    fetchCount();
    setInterval(fetchCount, 30000);
})();
</script>
@endauth

@stack('scripts')

</body>
</html>