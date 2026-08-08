@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes permFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .perm-hero, .perm-panel { animation: permFadeUp 0.4s ease both; }

    .perm-tabs { display:inline-flex; gap:6px; margin-bottom:20px; background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:16px; padding:6px; }
    .perm-tab { display:inline-flex; align-items:center; justify-content:center; white-space:nowrap; flex-shrink:0; padding:10px 24px; border-radius:11px; border:none; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer; background:transparent; color:rgba(1,60,88,0.55); transition:background 0.15s ease, color 0.15s ease; }
    .perm-tab.is-active { background:#013C58; color:#fff; }

    .perm-checkbox { appearance:none; width:20px; height:20px; border-radius:6px; border:1.5px solid rgba(0,83,122,0.25); background:#fff; cursor:pointer; position:relative; transition:background 0.15s ease, border-color 0.15s ease; }
    .perm-checkbox:checked { background:#0E6A96; border-color:#0E6A96; }
    .perm-checkbox:checked::after { content:''; position:absolute; inset:0; display:flex; align-items:center; justify-content:center; color:#fff; font-size:13px; font-weight:800; content:'✓'; }

    .perm-row:hover { background: rgba(168,232,249,0.1); }
</style>
@endpush

@section('content')
<div x-data="{ tab: 'admins' }" class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    @if (session('info'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(168,232,249,0.18); color:#00537A; border:1px solid rgba(0,83,122,0.14); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
            {{ session('info') }}
        </div>
    @endif

    {{-- ============ HERO ============ --}}
    <div class="perm-hero" style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.2) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>
        <div style="position:relative; display:flex; align-items:center; gap:16px;">
            <div style="display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </div>
            <div>
                <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">صلاحيات النظام</p>
                <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">إدارة الصلاحيات</h1>
                <p style="margin:8px 0 0; font-size:13px; color:rgba(168,232,249,0.75);">      </p>
            </div>
        </div>
    </div>

    {{-- ============ TABS ============ --}}
    <div class="perm-tabs">
        <button type="button" @click="tab = 'admins'" class="perm-tab" :class="tab === 'admins' ? 'is-active' : ''">الأدمنز</button>
        <button type="button" @click="tab = 'teachers'" class="perm-tab" :class="tab === 'teachers' ? 'is-active' : ''">الأساتذة</button>
    </div>

    {{-- ============ ADMINS PERMISSION MATRIX ============ --}}
    <div x-show="tab === 'admins'" x-cloak class="perm-panel">
        <form method="POST" action="{{ route('admin.permissions.update') }}">
            @csrf
            <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; overflow-x:auto; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
                <table style="width:100%; border-collapse:collapse; min-width:720px;">
                    <thead>
                        <tr>
                            <th style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 16px; background:rgba(168,232,249,0.22); position:sticky; right:0;">الأدمن</th>
                            @foreach ($permissions as $permission)
                                <th style="text-align:center; font-size:10.5px; font-weight:700; color:rgba(1,60,88,0.45); padding:13px 10px; background:rgba(168,232,249,0.22); min-width:110px;">{{ $permissionLabels[$permission->name] ?? $permission->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($admins as $admin)
                            @php
                                $adminName = trim($admin->first_name.' '.$admin->last_name) ?: $admin->email;
                                $initials = strtoupper(substr($admin->first_name ?? $admin->email, 0, 1).substr($admin->last_name ?? '', 0, 1));
                            @endphp
                            <tr class="perm-row" style="transition:background 0.15s ease;">
                                <td style="padding:12px 16px; border-bottom:1px solid rgba(0,83,122,0.05); background:#EFFAFD; position:sticky; right:0;">
                                    <div style="display:flex; align-items:center; gap:9px;">
                                        <div style="display:flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:9px; background:#00537A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:11px; flex-shrink:0;">{{ $initials }}</div>
                                        <span style="font-size:13px; font-weight:700; color:#013C58; white-space:nowrap;">{{ $adminName }}</span>
                                    </div>
                                </td>
                                @foreach ($permissions as $permission)
                                    <td style="padding:12px 10px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                                        <input type="checkbox" class="perm-checkbox" name="grants[{{ $admin->id }}][]" value="{{ $permission->name }}" @checked($admin->hasPermissionTo($permission->name, 'web'))>
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="{{ $permissions->count() + 1 }}" style="padding:40px; text-align:center; color:rgba(1,60,88,0.45); font-weight:600;">ما في أدمنز</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="display:flex; justify-content:flex-end; margin-top:18px;">
                <button type="submit" style="display:inline-flex; align-items:center; gap:8px; padding:12px 26px; border-radius:12px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                    حفظ الصلاحيات
                </button>
            </div>
        </form>
        <p style="margin:14px 2px 0; font-size:12px; color:rgba(1,60,88,0.45); font-weight:600;">
           
        </p>
    </div>

    {{-- ============ TEACHERS PERMISSION MATRIX ============ --}}
    <div x-show="tab === 'teachers'" x-cloak class="perm-panel">
        <form method="POST" action="{{ route('admin.permissions.update') }}">
            @csrf
            <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; overflow-x:auto; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
                <table style="width:100%; border-collapse:collapse; min-width:720px;">
                    <thead>
                        <tr>
                            <th style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 16px; background:rgba(168,232,249,0.22); position:sticky; right:0;">الأستاذ</th>
                            @foreach ($permissions as $permission)
                                <th style="text-align:center; font-size:10.5px; font-weight:700; color:rgba(1,60,88,0.45); padding:13px 10px; background:rgba(168,232,249,0.22); min-width:110px;">{{ $permissionLabels[$permission->name] ?? $permission->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teachers as $teacher)
                            @php
                                $teacherName = trim($teacher->first_name.' '.$teacher->last_name) ?: $teacher->email;
                                $initials = strtoupper(substr($teacher->first_name ?? $teacher->email, 0, 1).substr($teacher->last_name ?? '', 0, 1));
                            @endphp
                            <tr class="perm-row" style="transition:background 0.15s ease;">
                                <td style="padding:12px 16px; border-bottom:1px solid rgba(0,83,122,0.05); background:#EFFAFD; position:sticky; right:0;">
                                    <div style="display:flex; align-items:center; gap:9px;">
                                        <div style="display:flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:9px; background:#0E6A96; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:11px; flex-shrink:0;">{{ $initials }}</div>
                                        <span style="font-size:13px; font-weight:700; color:#013C58; white-space:nowrap;">{{ $teacherName }}</span>
                                    </div>
                                </td>
                                @foreach ($permissions as $permission)
                                    <td style="padding:12px 10px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                                        <input type="checkbox" class="perm-checkbox" name="grants[{{ $teacher->id }}][]" value="{{ $permission->name }}" @checked($teacher->hasPermissionTo($permission->name, 'web'))>
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="{{ $permissions->count() + 1 }}" style="padding:40px; text-align:center; color:rgba(1,60,88,0.45); font-weight:600;">ما في أساتذة</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="display:flex; justify-content:flex-end; margin-top:18px;">
                <button type="submit" style="display:inline-flex; align-items:center; gap:8px; padding:12px 26px; border-radius:12px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                    حفظ الصلاحيات
                </button>
            </div>
        </form>
        <p style="margin:14px 2px 0; font-size:12px; color:rgba(1,60,88,0.45); font-weight:600;">
        </p>
    </div>
</div>
@endsection
