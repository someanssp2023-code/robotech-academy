@extends('layouts.admin')
@section('title', app()->getLocale()=='km' ? 'គ្រប់គ្រងអ្នកប្រើប្រាស់' : 'Manage Users')

@section('content')

{{-- Alerts --}}
@if(session('success'))
<div style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;border-radius:8px;padding:10px 16px;font-size:13px;margin-bottom:16px;">
    ✅ {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:8px;padding:10px 16px;font-size:13px;margin-bottom:16px;">
    ❌ {{ session('error') }}
</div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;text-align:center;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;">{{ app()->getLocale()=='km' ? 'សរុប' : 'Total' }}</div>
            <div style="font-size:28px;font-weight:900;color:#0f172a;">{{ $totalUsers }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;text-align:center;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;">Admin</div>
            <div style="font-size:28px;font-weight:900;color:#111;">{{ $totalAdmins }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;text-align:center;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;">{{ app()->getLocale()=='km' ? 'បុគ្គលិក' : 'Staff' }}</div>
            <div style="font-size:28px;font-weight:900;color:#111;">{{ $totalStaff }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;text-align:center;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:6px;">{{ app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer' }}</div>
            <div style="font-size:28px;font-weight:900;color:#111;">{{ $totalCustomers }}</div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px;">
    @foreach([
        [null,       app()->getLocale()=='km' ? 'ទាំងអស់' : 'All',      $totalUsers,   '#0f172a', '#fff'],
        ['admin',    'Admin',                                              $totalAdmins,  '#f59e0b', '#000'],
        ['staff',    app()->getLocale()=='km' ? 'បុគ្គលិក' : 'Staff',   $totalStaff,   '#8b5cf6', '#fff'],
        ['customer', app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer', $totalCustomers,'#6366f1','#fff'],
        ['inactive', app()->getLocale()=='km' ? 'អសកម្ម' : 'Inactive',  $totalInactive, '#ef4444', '#fff'],
    ] as [$tab, $label, $count, $bg, $color])
    @php $active = request('tab') === $tab; @endphp
    <a href="{{ route('admin.users', $tab ? ['tab'=>$tab] : []) }}"
       style="padding:7px 16px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;transition:all .15s;
       {{ $active ? "background:{$bg};color:{$color};" : 'background:#fff;color:#64748b;border:1px solid #e2e8f0;' }}">
        {{ $label }} ({{ $count }})
    </a>
    @endforeach
</div>

{{-- Search + Filters --}}
<form method="GET" action="{{ route('admin.users') }}" class="panel p-3 mb-4">
    <input type="hidden" name="tab" value="{{ request('tab') }}"/>
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <input type="text" name="search" class="form-ctrl" style="width:220px;font-size:13px;"
               placeholder="{{ app()->getLocale()=='km' ? 'ស្វែងរក...' : 'Search name or email...' }}"
               value="{{ request('search') }}"/>

        <select name="status" class="form-ctrl" style="width:140px;font-size:13px;" onchange="this.form.submit()">
            <option value="">{{ app()->getLocale()=='km' ? 'ស្ថានភាពទាំងអស់' : 'All Status' }}</option>
            <option value="active"   {{ request('status')=='active'   ? 'selected':'' }}>{{ app()->getLocale()=='km' ? 'សកម្ម' : 'Active' }}</option>
            <option value="inactive" {{ request('status')=='inactive' ? 'selected':'' }}>{{ app()->getLocale()=='km' ? 'អសកម្ម' : 'Inactive' }}</option>
        </select>



        <button type="submit" class="qa-btn qa-primary" style="font-size:13px;">
            {{ app()->getLocale()=='km' ? 'ស្វែងរក' : 'Search' }}
        </button>

        @if(request()->hasAny(['search','status','date_from','date_to']))
        <a href="{{ route('admin.users', request('tab') ? ['tab'=>request('tab')] : []) }}" class="qa-btn" style="font-size:13px;">
            {{ app()->getLocale()=='km' ? 'លប់ចោល' : 'Clear' }}
        </a>
        @endif

        {{-- Export buttons --}}
        <a href="{{ route('admin.users.export.excel', request()->query()) }}"
           class="qa-btn" style="border-color:#16a34a;color:#16a34a;">
            ⬇ Excel
        </a>
        <a href="{{ route('admin.users.export.pdf', request()->query()) }}"
           target="_blank"
           class="qa-btn" style="border-color:#dc2626;color:#dc2626;">
            ⬇ PDF
        </a>
    </div>
</form>

{{-- Table --}}
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:13px;font-weight:700;color:#0f172a;">
            {{ app()->getLocale()=='km' ? 'អ្នកប្រើប្រាស់' : 'Users' }} ({{ $users->total() }})
        </span>
    </div>

    <div class="table-responsive">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:10px 18px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;border-bottom:1px solid #e2e8f0;">
                        {{ app()->getLocale()=='km' ? 'អ្នកប្រើប្រាស់' : 'User' }}
                    </th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;border-bottom:1px solid #e2e8f0;">
                        {{ app()->getLocale()=='km' ? 'ប្រភេទ' : 'Role' }}
                    </th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;border-bottom:1px solid #e2e8f0;">
                        {{ app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status' }}
                    </th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;border-bottom:1px solid #e2e8f0;">
                        {{ app()->getLocale()=='km' ? 'ចូលរួម' : 'Joined' }}
                    </th>
                    <th style="padding:10px 18px;text-align:right;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;border-bottom:1px solid #e2e8f0;">
                        {{ app()->getLocale()=='km' ? 'សកម្មភាព' : 'Actions' }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr style="border-bottom:1px solid #f8fafc;transition:background .1s;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">

                    {{-- User --}}
                    <td style="padding:12px 18px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff;background:
                                {{ $user->isMainAdmin() ? '#dc2626' : ($user->role=='Admin' ? '#f59e0b' : ($user->role=='Staff' ? '#8b5cf6' : '#6366f1')) }};">
                                {{ strtoupper(substr($user->name,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:700;color:#0f172a;display:flex;align-items:center;gap:6px;">
                                    {{ $user->name }}
                                    @if($user->isMainAdmin())
                                    <span style="font-size:10px;background:#fef3c7;color:#92400e;padding:1px 6px;border-radius:4px;"></span>
                                    @endif
                                </div>
                                <div style="font-size:11px;color:#94a3b8;">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Role --}}
                    <td style="padding:12px 14px;">
                        @if($user->isMainAdmin())
                            <span style="background:#fee2e2;color:#dc2626;border:1px solid #fecaca;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">Main Admin</span>
                        @elseif($user->role=='Admin')
                            <span style="background:#fef3c7;color:#d97706;border:1px solid #fde68a;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">Admin</span>
                        @elseif($user->role=='Staff')
                            <span style="background:#ede9fe;color:#7c3aed;border:1px solid #ddd6fe;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">{{ app()->getLocale()=='km' ? 'បុគ្គលិក' : 'Staff' }}</span>
                        @else
                            <span style="background:#e0e7ff;color:#4338ca;border:1px solid #c7d2fe;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">{{ app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer' }}</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td style="padding:12px 14px;">
                        @if($user->is_active)
                            <span style="background:#dcfce7;color:#166534;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">{{ app()->getLocale()=='km' ? 'សកម្ម' : 'Active' }}</span>
                        @else
                            <span style="background:#fee2e2;color:#991b1b;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">{{ app()->getLocale()=='km' ? 'អសកម្ម' : 'Inactive' }}</span>
                        @endif
                    </td>

                    {{-- Joined --}}
                    <td style="padding:12px 14px;font-size:12px;color:#94a3b8;">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>

                    {{-- Actions --}}
                    <td style="padding:12px 18px;text-align:right;">
                        @if($user->isMainAdmin())
                            <span style="font-size:11px;color:#cbd5e1;">{{ app()->getLocale()=='km' ? 'ការពារ' : 'Protected' }}</span>
                        @else
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;">

                            {{-- Toggle --}}
                            <form action="{{ route('admin.user.toggle', $user->id) }}" method="POST" style="margin:0;">
                                @csrf
                                <button type="submit" style="font-size:12px;font-weight:600;padding:5px 12px;border-radius:7px;border:1.5px solid {{ $user->is_active ? '#fecaca' : '#bbf7d0' }};color:{{ $user->is_active ? '#dc2626' : '#166534' }};background:#fff;cursor:pointer;font-family:inherit;transition:all .15s;">
                                    {{ $user->is_active ? (app()->getLocale()=='km' ? 'បិទ' : 'Deactivate') : (app()->getLocale()=='km' ? 'បើក' : 'Activate') }}
                                </button>
                            </form>

                            {{-- Role Dropdown --}}
                            <form action="{{ route('admin.user.promote', $user->id) }}" method="POST" style="margin:0;">
                                @csrf
                                <select name="role"
                                        style="font-size:12px;font-weight:600;padding:5px 10px;border-radius:7px;border:1.5px solid #e2e8f0;background:#fff;cursor:pointer;outline:none;font-family:inherit;color:#374151;"
                                        onchange="this.form.submit()">
                                    <option value="Customer" {{ $user->role=='Customer' ? 'selected':'' }}>{{ app()->getLocale()=='km' ? 'អតិថិជន' : 'Customer' }}</option>
                                    <option value="Staff"    {{ $user->role=='Staff'    ? 'selected':'' }}>{{ app()->getLocale()=='km' ? 'បុគ្គលិក' : 'Staff' }}</option>
                                    <option value="Admin"    {{ $user->role=='Admin'    ? 'selected':'' }}>Admin</option>
                                </select>
                            </form>

                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:48px;color:#94a3b8;">
                        {{ app()->getLocale()=='km' ? 'រកមិនឃើញអ្នកប្រើប្រាស់' : 'No users found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div style="padding:12px 18px;border-top:1px solid #f1f5f9;">
        {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection