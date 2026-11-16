<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title', 'Admin') — Robotech</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    @if(app()->getLocale() == 'km')
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <style>body, * { font-family: 'Kantumruy Pro', sans-serif !important; }</style>
    @endif
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f1f5f9; font-family: 'Inter', system-ui, sans-serif; font-size: 14px; color: #1e293b; min-height: 100vh; }

        /* ══ SIDEBAR ══ */
        #sidebar {
            width: 220px;
            background: #1a1f2e;
            min-height: 100vh;
            position: fixed; top: 0; left: 0;
            display: flex; flex-direction: column;
            z-index: 1000; overflow-y: auto;
        }
        #sidebar::-webkit-scrollbar { width: 0; }

        /* Brand */
        .brand-box {
            padding: 18px 16px 14px;
            display: flex; align-items: center; gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }
        .brand-logo {
            width: 36px; height: 36px;
            background: #f59e0b;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 900; color: #000; flex-shrink: 0;
        }
        .brand-name { font-size: 20px; font-weight: 800; color: #fff; }
        .brand-sub  { font-size: 10px; color: #64748b; margin-top: 1px; }

        /* Nav */
        .nav-wrap { padding: 8px 10px; flex: 1; }

        /* Single nav item */
        .s-link-single {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 10px; border-radius: 7px;
            font-size: 16px; font-weight: 500;
            color: #ffffff; text-decoration: none;
            transition: all .15s; margin-bottom: 1px;
        }
        .s-link-single:hover { background: rgba(255,255,255,.1); color: #ffffff; }
        .s-link-single.active { background: rgba(255,255,255,.08); color: #fff; font-weight: 600; }
        .s-link-single svg { width: 16px; height: 16px; flex-shrink: 0; opacity: .5; }
        .s-link-single:hover svg, .s-link-single.active svg { opacity: .9; }

        /* Group button */
        .nav-group-item { margin-bottom: 1px; }
        .nav-group-btn {
            display: flex; align-items: center; gap: 10px;
            width: 100%; padding: 8px 10px; border-radius: 7px;
            font-size: 16px; font-weight: 500; color: #ffffff;
            background: none; border: none; cursor: pointer;
            font-family: inherit; transition: all .15s; text-align: left;
        }
        .nav-group-btn:hover { background: rgba(255,255,255,.1); color: #ffffff; }
        .nav-group-btn.open  { color: #ffffff; }
        .nav-group-btn svg.icon { width: 16px; height: 16px; flex-shrink: 0; opacity: .5; }
        .nav-group-btn:hover svg.icon, .nav-group-btn.open svg.icon { opacity: .9; }
        .chevron { margin-left: auto; width: 12px; height: 12px; transition: transform .2s; color: #475569; flex-shrink: 0; }
        .nav-group-btn.open .chevron { transform: rotate(180deg); }

        /* Dropdown children */
        .nav-children { display: none; padding: 2px 0 2px 22px; }
        .nav-children.open { display: block; }
        .s-link {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 10px; border-radius: 6px;
            font-size: 14px; font-weight: 500; color: #ffffff;
            text-decoration: none; transition: all .15s; margin-bottom: 1px;
        }
        .s-link:hover { color: #ffffff; background: rgba(255,255,255,.06); }
        .s-link.active { color: #f59e0b; font-weight: 600; }
        .s-dot { font-size: 6px; flex-shrink: 0; opacity: .8; color: #ffffff; }
        .s-link.active .s-dot { opacity: 1; color: #f59e0b; }
        .s-badge { margin-left: auto; background: #ef4444; color: #fff; font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 20px; }

        /* Section divider */
        .s-divider { display: none; }

        /* User card */
        .sidebar-user {
            margin: 6px 10px 12px;
            padding: 10px 12px;
            background: rgba(255,255,255,.04);
            border-radius: 10px;
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-av {
            width: 32px; height: 32px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 12px; font-weight: 700; flex-shrink: 0;
        }
        .sidebar-user-name { font-size: 12px; font-weight: 700; color: #e2e8f0; }
        .sidebar-user-role { font-size: 10px; color: #475569; margin-top: 1px; }
        .btn-logout-icon { background: none; border: none; cursor: pointer; color: #475569; padding: 4px; transition: color .15s; }
        .btn-logout-icon:hover { color: #f87171; }

        /* ══ MAIN ══ */
        #main { margin-left: 220px; min-height: 100vh; background: #f1f5f9; }

        .topbar {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 99;
        }
        .topbar-title { font-size: 17px; font-weight: 800; color: #0f172a; }
        .breadcrumb-text { font-size: 11px; color: #94a3b8; margin-top: 2px; }

        .lang-wrap { position: relative; }
        .lang-btn { display: flex; align-items: center; gap: 5px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 10px; font-size: 12px; font-weight: 600; color: #64748b; cursor: pointer; transition: all .15s; font-family: inherit; }
        .lang-btn:hover { border-color: #f59e0b; }
        .lang-menu { display: none; position: absolute; top: calc(100% + 6px); right: 0; background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 6px; min-width: 130px; box-shadow: 0 10px 30px rgba(0,0,0,.08); z-index: 999; }
        .lang-menu a { display: flex; align-items: center; gap: 8px; padding: 7px 10px; border-radius: 7px; text-decoration: none; font-size: 12px; font-weight: 600; color: #64748b; transition: background .1s; }
        .lang-menu a:hover { background: #f8fafc; color: #0f172a; }
        .lang-menu a.active { color: #d97706; background: #fffbeb; }

        .page-body { padding: 22px 24px; }

        /* Components */
        .panel { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
        .panel-head { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
        .panel-head h6 { margin: 0; font-weight: 700; font-size: 13px; color: #0f172a; }
        .stat-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 20px; transition: box-shadow .2s, transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
        .stat-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.07); transform: translateY(-2px); }
        .stat-lbl { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #94a3b8; margin-bottom: 8px; }
        .stat-val { font-size: 32px; font-weight: 900; color: #0f172a; line-height: 1; }
        .stat-footer { margin-top: 12px; padding-top: 12px; border-top: 1px solid #f1f5f9; font-size: 11px; }
        .stat-footer a { color: #94a3b8; text-decoration: none; font-size: 11px; }
        .stat-footer a:hover { color: #f59e0b; }
        .ra-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .ra-table thead th { padding: 10px 14px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
        .ra-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .1s; }
        .ra-table tbody tr:hover { background: #f8fafc; }
        .ra-table tbody td { padding: 12px 14px; vertical-align: middle; color: #334155; }
        .ra-table tbody tr:last-child { border-bottom: none; }
        .pill { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; white-space: nowrap; }
        .pill-pending    { background: #fef3c7; color: #92400e; }
        .pill-processing { background: #dbeafe; color: #1e40af; }
        .pill-shipped    { background: #e0f2fe; color: #0369a1; }
        .pill-delivered  { background: #dcfce7; color: #166534; }
        .pill-cancelled  { background: #fee2e2; color: #991b1b; }
        .qa-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; transition: all .15s; border: 1px solid #e2e8f0; color: #475569; background: #fff; cursor: pointer; font-family: inherit; }
        .qa-btn:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }
        .qa-btn.qa-primary { background: #0f172a; color: #fff; border-color: #0f172a; }
        .qa-btn.qa-primary:hover { background: #f59e0b; border-color: #f59e0b; color: #000; }
        .view-btn { font-size: 11px; font-weight: 600; padding: 5px 12px; border-radius: 7px; border: 1px solid #e2e8f0; background: #fff; color: #475569; text-decoration: none; transition: all .15s; cursor: pointer; font-family: inherit; }
        .view-btn:hover { background: #f8fafc; border-color: #cbd5e1; }
        .av { width: 30px; height: 30px; border-radius: 50%; background: #6366f1; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 11px; font-weight: 700; flex-shrink: 0; }
        .form-ctrl { border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 9px 12px; font-size: 13px; width: 100%; font-family: inherit; transition: border-color .15s; outline: none; background: #fff; color: #0f172a; }
        .form-ctrl:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.1); }
        .flash-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 10px; }
        .flash-error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 10px; }
    </style>
</head>
<body>

@php
$user        = Auth::user();
$role        = $user->role;
$isAdmin     = $user->isAdmin();
$isStaff     = $user->isStaff();
$isMainAdmin = $user->isMainAdmin();

$roleColor = $isMainAdmin ? '#dc2626' : ($role=='Admin' ? '#f59e0b' : ($role=='Staff' ? '#8b5cf6' : '#6366f1'));
$roleLabel = $isMainAdmin ? 'Main Admin' : ($role=='Admin'
    ? (app()->getLocale()=='km' ? 'អ្នកគ្រប់គ្រង' : 'Admin')
    : ($role=='Staff'
        ? (app()->getLocale()=='km' ? 'បុគ្គលិក' : 'Staff')
        : (app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer')));

// Staff: dashboard, products, categories, brands, stock, promotions only
// Admin + MainAdmin: everything
$canProducts   = $isAdmin || $isStaff;
$canCategories = $isAdmin || $isStaff;
$canBrands     = $isAdmin || $isStaff;
$canBanners    = $isAdmin || $isStaff;
$canCoupons    = $isAdmin || $isStaff;
$canStock      = $isAdmin || $isStaff;
$canOrders     = $isAdmin;
$canPayments   = $isAdmin;
$canUsers      = $isAdmin;
$canReviews    = $isAdmin;
$canReports    = $isAdmin;
$canSettings   = $isAdmin;
$canBackup     = $isAdmin;
@endphp

{{-- SIDEBAR --}}
<div id="sidebar">
    <div class="brand-box">
        <div class="brand-logo">R</div>
        <div>
            <div class="brand-name">RoboTech</div>
            <div class="brand-sub">Admin Panel</div>
        </div>
    </div>

    <div class="nav-wrap">

        {{-- Dashboard --}}
        <a class="s-link-single {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            {{ app()->getLocale()=='km' ? 'ផ្ទាំងដើម' : 'Dashboard' }}
        </a>

        {{-- CATALOG --}}
        @if($canProducts || $canCategories || $canBrands)
        <div class="s-divider"></div>
        <div class="nav-group-item">
            <button class="nav-group-btn {{ request()->routeIs('admin.products*','admin.categories*','admin.brands*') ? 'open' : '' }}" onclick="toggleGroup(this)">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                {{ app()->getLocale()=='km' ? 'ផលិតផល' : 'Products' }}
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div class="nav-children {{ request()->routeIs('admin.products*','admin.categories*','admin.brands*') ? 'open' : '' }}">
                @if($canProducts)
                <a class="s-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}" href="{{ route('admin.products') }}">
                    <span class="s-dot">●</span>{{ app()->getLocale()=='km' ? 'ផលិតផល' : 'All Products' }}
                </a>
                @endif
                @if($canCategories)
                <a class="s-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" href="{{ route('admin.categories') }}">
                    <span class="s-dot">●</span>{{ app()->getLocale()=='km' ? 'ប្រភេទ' : 'Categories' }}
                </a>
                @endif
                @if($canBrands)
                <a class="s-link {{ request()->routeIs('admin.brands*') ? 'active' : '' }}" href="{{ route('admin.brands') }}">
                    <span class="s-dot">●</span>{{ app()->getLocale()=='km' ? 'ម៉ាក' : 'Brands' }}
                </a>
                @endif
            </div>
        </div>
        @endif

        {{-- STOCK --}}
        @if($canStock)
        <div class="s-divider"></div>
        <div class="nav-group-item">
            <button class="nav-group-btn {{ request()->routeIs('admin.stock*') ? 'open' : '' }}" onclick="toggleGroup(this)">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M5 8h14M5 8a2 2 0 1 0 0-4h14a2 2 0 1 0 0 4M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8m-9 4h4"/></svg>
                {{ app()->getLocale()=='km' ? 'ស្តុក' : 'Stock' }}
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div class="nav-children {{ request()->routeIs('admin.stock*') ? 'open' : '' }}">
                <a class="s-link {{ request()->routeIs('admin.stock') ? 'active' : '' }}" href="{{ route('admin.stock') }}">
                    <span class="s-dot">●</span>{{ app()->getLocale()=='km' ? 'គ្រប់គ្រង' : 'Manage Stock' }}
                </a>
                <a class="s-link {{ request()->routeIs('admin.stock.history') ? 'active' : '' }}" href="{{ route('admin.stock.history') }}">
                    <span class="s-dot">●</span>{{ app()->getLocale()=='km' ? 'ប្រវត្តិ' : 'History' }}
                </a>
            </div>
        </div>
        @endif

        {{-- SALES --}}
        @if($canOrders || $canPayments)
        <div class="s-divider"></div>
        <div class="nav-group-item">
            <button class="nav-group-btn {{ request()->routeIs('admin.orders*','admin.payments') ? 'open' : '' }}" onclick="toggleGroup(this)">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                {{ app()->getLocale()=='km' ? 'ការលក់' : 'Sales' }}
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div class="nav-children {{ request()->routeIs('admin.orders*','admin.payments') ? 'open' : '' }}">
                @if($canOrders)
                <a class="s-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}" href="{{ route('admin.orders') }}">
                    <span class="s-dot">●</span>{{ app()->getLocale()=='km' ? 'ការបញ្ជាទិញ' : 'Orders' }}
                    @php $pc = \App\Models\Order::where('status','Pending')->count(); @endphp
                    @if($pc > 0)<span class="s-badge">{{ $pc }}</span>@endif
                </a>
                @endif
                @if($canPayments)
                <a class="s-link {{ request()->routeIs('admin.payments') ? 'active' : '' }}" href="{{ route('admin.payments') }}">
                    <span class="s-dot">●</span>{{ app()->getLocale()=='km' ? 'ការទូទាត់' : 'Payments' }}
                </a>
                @endif
            </div>
        </div>
        @endif

        {{-- PROMOTIONS --}}
        @if($canBanners || $canCoupons)
        <div class="s-divider"></div>
        <div class="nav-group-item">
            <button class="nav-group-btn {{ request()->routeIs('admin.banners*','admin.coupons*') ? 'open' : '' }}" onclick="toggleGroup(this)">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                {{ app()->getLocale()=='km' ? 'ការផ្សព្វផ្សាយ' : 'Promotions' }}
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div class="nav-children {{ request()->routeIs('admin.banners*','admin.coupons*') ? 'open' : '' }}">
                @if($canBanners)
                <a class="s-link {{ request()->routeIs('admin.banners*') ? 'active' : '' }}" href="{{ route('admin.banners') }}">
                    <span class="s-dot">●</span>{{ app()->getLocale()=='km' ? 'បដា' : 'Banners' }}
                </a>
                @endif
                @if($canCoupons)
                <a class="s-link {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}" href="{{ route('admin.coupons') }}">
                    <span class="s-dot">●</span>{{ app()->getLocale()=='km' ? 'គូប៉ុង' : 'Coupons' }}
                </a>
                @endif
            </div>
        </div>
        @endif

        {{-- MANAGEMENT --}}
        @if($canUsers || $canReviews)
        <div class="s-divider"></div>
        <div class="nav-group-item">
            <button class="nav-group-btn {{ request()->routeIs('admin.users*','admin.reviews*') ? 'open' : '' }}" onclick="toggleGroup(this)">
                <svg class="icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                {{ app()->getLocale()=='km' ? 'ការគ្រប់គ្រង' : 'Management' }}
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div class="nav-children {{ request()->routeIs('admin.users*','admin.reviews*') ? 'open' : '' }}">
                @if($canUsers)
                <a class="s-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                    <span class="s-dot">●</span>{{ app()->getLocale()=='km' ? 'អ្នកប្រើប្រាស់' : 'Users' }}
                </a>
                @endif
                @if($canReviews)
                <a class="s-link {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}" href="{{ route('admin.reviews') }}">
                    <span class="s-dot">●</span>{{ app()->getLocale()=='km' ? 'មតិ' : 'Reviews' }}
                </a>
                @endif
            </div>
        </div>
        @endif

        {{-- OTHER --}}
        @if($canReports || $canSettings || $canBackup)
        <div class="s-divider"></div>
        @if($canReports)
        <a class="s-link-single {{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{ route('admin.reports') }}">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            {{ app()->getLocale()=='km' ? 'របាយការណ៍' : 'Reports' }}
        </a>
        @endif
        @if($canSettings)
        <a class="s-link-single {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            {{ app()->getLocale()=='km' ? 'ការកំណត់' : 'Settings' }}
        </a>
        @endif
        @if($canBackup)
        <a class="s-link-single {{ request()->routeIs('admin.backup*') ? 'active' : '' }}" href="{{ route('admin.backup') }}">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            {{ app()->getLocale()=='km' ? 'បម្រុងទុក' : 'Backup' }}
        </a>
        @endif
        @endif

    </div>

    {{-- User card --}}
    <div class="sidebar-user">
        <div class="sidebar-av" style="background:{{ $roleColor }};">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        <div style="flex:1;min-width:0;">
            <div class="sidebar-user-name">{{ Str::limit(Auth::user()->name, 14) }}</div>
            <div class="sidebar-user-role">{{ $roleLabel }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout-icon" title="Logout">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
            </button>
        </form>
    </div>
</div>

{{-- MAIN --}}
<div id="main">
    <div class="topbar">
        <div>
            <div class="topbar-title">@yield('title', 'Dashboard')</div>
            <div class="breadcrumb-text">{{ app()->getLocale()=='km' ? 'ផ្ទាំងគ្រប់គ្រង' : 'Admin Panel' }} / @yield('title')</div>
        </div>
        <div class="d-flex align-items-center gap-3">

            {{-- ── Notification Bell ── --}}
            <div class="notif-wrap" id="adminNotifWrap" style="position:relative;">
                <button onclick="toggleAdminNotif()" title="Notifications" style="
                    display:flex;align-items:center;justify-content:center;
                    width:36px;height:36px;border-radius:8px;
                    background:#f8fafc;border:1px solid #e2e8f0;
                    color:#475569;cursor:pointer;position:relative;
                    transition:all .15s;font-family:inherit;
                ">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <span id="adminNotifBadge" style="
                        display:none;position:absolute;top:-4px;right:-4px;
                        background:#ef4444;color:#fff;font-size:9px;font-weight:800;
                        min-width:16px;height:16px;border-radius:8px;
                        align-items:center;justify-content:center;padding:0 3px;
                    ">0</span>
                </button>
                <div id="adminNotifDropdown" style="
                    display:none;position:absolute;top:calc(100% + 8px);right:0;
                    width:340px;background:#fff;border:1px solid #e2e8f0;
                    border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,.1);
                    z-index:9999;overflow:hidden;
                ">
                    <div style="padding:12px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:13px;font-weight:700;color:#0f172a;">Notifications</span>
                        <div style="display:flex;gap:8px;">
                            <button onclick="adminMarkAllRead()" style="font-size:11px;color:#3b82f6;background:none;border:none;cursor:pointer;font-weight:600;">Mark all read</button>
                            <button onclick="adminClearAll()" style="font-size:11px;color:#ef4444;background:none;border:none;cursor:pointer;font-weight:600;">Clear all</button>
                        </div>
                    </div>
                    <div id="adminNotifList" style="max-height:360px;overflow-y:auto;">
                        <div style="padding:32px 16px;text-align:center;color:#94a3b8;font-size:13px;">No notifications</div>
                    </div>
                </div>
            </div>

            <div class="lang-wrap">
                <button class="lang-btn" onclick="toggleLang()" style="display:flex;align-items:center;gap:6px;">
                    @if(app()->getLocale()=='km')
                        <img src="https://flagcdn.com/w20/kh.png" width="20" height="14" style="border-radius:2px;object-fit:cover;" alt="KH"/>
                        <span>ខ្មែរ</span>
                    @else
                        <img src="https://flagcdn.com/w20/us.png" width="20" height="14" style="border-radius:2px;object-fit:cover;" alt="US"/>
                        <span>EN</span>
                    @endif
                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="lang-menu" id="lang-menu">
                    <a href="{{ route('language.switch', 'en') }}" class="{{ app()->getLocale()=='en' ? 'active' : '' }}" style="display:flex;align-items:center;gap:8px;">
                        <img src="https://flagcdn.com/w20/us.png" width="20" height="14" style="border-radius:2px;object-fit:cover;" alt="US"/>
                        English @if(app()->getLocale()=='en') ✓ @endif
                    </a>
                    <a href="{{ route('language.switch', 'km') }}" class="{{ app()->getLocale()=='km' ? 'active' : '' }}" style="display:flex;align-items:center;gap:8px;">
                        <img src="https://flagcdn.com/w20/kh.png" width="20" height="14" style="border-radius:2px;object-fit:cover;" alt="KH"/>
                        ខ្មែរ @if(app()->getLocale()=='km') ✓ @endif
                    </a>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div style="width:34px;height:34px;border-radius:50%;background:{{ $roleColor }};display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-size:13px;font-weight:700;color:#0f172a;">{{ Str::limit(Auth::user()->name, 14) }}</div>
                    <div style="font-size:10px;color:#94a3b8;">{{ $roleLabel }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 pt-3">
        @if(session('success'))
            <div class="alert flash-success alert-dismissible fade show" style="font-size:13px;">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert flash-error alert-dismissible fade show" style="font-size:13px;">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
    </div>

    <div class="page-body">@yield('content')</div>
</div>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script>
function toggleGroup(btn) { btn.classList.toggle('open'); btn.nextElementSibling.classList.toggle('open'); }
function toggleLang() { const m = document.getElementById('lang-menu'); m.style.display = m.style.display === 'block' ? 'none' : 'block'; }
document.addEventListener('click', function(e) { if (!e.target.closest('.lang-wrap')) { const m = document.getElementById('lang-menu'); if (m) m.style.display = 'none'; } });
</script>

{{-- ── Admin Notification JS ── --}}
<script>
(function() {
    const COUNT_URL   = '{{ route("admin.notifications.count") }}';
    const LIST_URL    = '{{ route("admin.notifications.list") }}';
    const READ_URL    = '/admin/notifications/{id}/read';
    const READALL_URL = '{{ route("admin.notifications.read-all") }}';
    const CLEAR_URL   = '{{ route("admin.notifications.clear-all") }}';
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    function fetchCount() {
        fetch(COUNT_URL).then(r=>r.json()).then(d=>{
            const b = document.getElementById('adminNotifBadge');
            if (d.count > 0) { b.textContent = d.count > 99 ? '99+' : d.count; b.style.display='flex'; }
            else b.style.display='none';
        }).catch(()=>{});
    }

    function fetchList() {
        fetch(LIST_URL).then(r=>r.json()).then(d=>{
            const list = document.getElementById('adminNotifList');
            if (!d.notifications || d.notifications.length === 0) {
                list.innerHTML = '<div style="padding:32px 16px;text-align:center;color:#94a3b8;font-size:13px;">No notifications</div>';
                return;
            }
            list.innerHTML = d.notifications.map(n => `
                <div onclick="adminClickNotif(${n.id},'${n.url||''}')" style="
                    padding:12px 16px;display:flex;gap:10px;cursor:pointer;
                    border-bottom:1px solid #f8fafc;transition:background .1s;
                    background:${n.is_read?'#fff':'#fefce8'};
                " onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='${n.is_read?'#fff':'#fefce8'}'">
                    <div style="font-size:20px;flex-shrink:0;line-height:1;">${n.icon}</div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;font-weight:700;color:#0f172a;">${n.title}</div>
                        <div style="font-size:11px;color:#64748b;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${n.message}</div>
                        <div style="font-size:10px;color:#94a3b8;margin-top:3px;">${n.time}</div>
                    </div>
                    ${!n.is_read?'<div style="width:7px;height:7px;border-radius:50%;background:#3b82f6;flex-shrink:0;margin-top:4px;"></div>':''}
                </div>
            `).join('');
        }).catch(()=>{});
    }

    window.toggleAdminNotif = function() {
        const d = document.getElementById('adminNotifDropdown');
        const open = d.style.display === 'block';
        d.style.display = open ? 'none' : 'block';
        if (!open) fetchList();
    };
    window.adminClickNotif = function(id, url) {
        fetch(READ_URL.replace('{id}',id),{method:'POST',headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json'}})
            .then(()=>{ fetchCount(); if(url) window.location.href=url; else fetchList(); }).catch(()=>{});
    };
    window.adminMarkAllRead = function() {
        fetch(READALL_URL,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json'}})
            .then(()=>{ fetchCount(); fetchList(); }).catch(()=>{});
    };
    window.adminClearAll = function() {
        if (!confirm('Clear all notifications?')) return;
        fetch(CLEAR_URL,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json'}})
            .then(()=>{ fetchCount(); fetchList(); }).catch(()=>{});
    };
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#adminNotifWrap')) {
            const d = document.getElementById('adminNotifDropdown');
            if (d) d.style.display = 'none';
        }
    });
    fetchCount();
    setInterval(fetchCount, 30000);
})();
</script>

@stack('scripts')
</body>
</html>