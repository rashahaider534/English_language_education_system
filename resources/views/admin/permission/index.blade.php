@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes permIdxFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .pmi-hero, .pmi-row { animation: permIdxFadeUp 0.4s ease both; }
    .pmi-row { transition: background 0.15s ease; }
    .pmi-row:hover { background: rgba(168,232,249,0.1); }
    .pmi-icon-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .pmi-icon-btn:hover { transform: translateY(-2px) scale(1.05); box-shadow: 0 8px 16px rgba(1,60,88,0.16); }
    .pmi-create-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .pmi-create-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(245,162,1,0.28); }
    .pmi-tab { border:none; background:transparent; padding:9px 18px; border-radius:10px; font-family:'Poppins',sans-serif; font-size:12.5px; font-weight:700; cursor:pointer; color:rgba(1,60,88,0.55); }
    .pmi-tab.is-active { background:#013C58; color:#fff; }
</style>
@endpush

@section('content')
<div x-data="{ tab: 'admins' }" class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    {{-- ============ HERO ============ --}}
    <div class="pmi-hero" style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.2) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>
        <div style="position:relative; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"></path><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"></path></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">إدارة ومتابعة</p>
                    <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">الأدمنز والأساتذة</h1>
                </div>
            </div>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('admins.permission.create') }}" class="pmi-create-btn" style="display:inline-flex; align-items:center; gap:8px; padding:12px 20px; border-radius:12px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer; text-decoration:none;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"></path></svg>
                    إضافة أدمن
                </a>
                <a href="{{ route('admin.permission.teacher.create') }}" class="pmi-create-btn" style="display:inline-flex; align-items:center; gap:8px; padding:12px 20px; border-radius:12px; border:1.5px solid rgba(255,255,255,0.3); background:rgba(255,255,255,0.08); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer; text-decoration:none;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"></path></svg>
                    إضافة أستاذ
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(76,175,120,0.14); color:#2E7D55; border:1px solid rgba(76,175,120,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            {{ session('success') }}
        </div>
    @endif

    {{-- ============ TABS ============ --}}
    <div style="display:flex; gap:6px; background:rgba(0,83,122,0.05); border-radius:12px; padding:4px; margin-bottom:18px; width:fit-content;">
        <button type="button" class="pmi-tab" :class="tab === 'admins' ? 'is-active' : ''" @click="tab = 'admins'">الأدمنز ({{ $admins->total() }})</button>
        <button type="button" class="pmi-tab" :class="tab === 'teachers' ? 'is-active' : ''" @click="tab = 'teachers'">الأساتذة ({{ $teachers->total() }})</button>
    </div>

    {{-- ============ ADMINS TABLE ============ --}}
    <div x-show="tab === 'admins'" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; overflow:hidden; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
            <thead>
                <tr>
                    <th style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 16px; background:rgba(168,232,249,0.22); width:22%;">الاسم</th>
                    <th style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:24%;">الإيميل</th>
                    <th style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:34%;">الصلاحيات</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:20%;">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($admins as $admin)
                    @php
                        $adminName = trim($admin->first_name.' '.$admin->last_name) ?: $admin->email;
                        $initials = strtoupper(substr($admin->first_name ?? $admin->email, 0, 1).substr($admin->last_name ?? '', 0, 1));
                    @endphp
                    <tr class="pmi-row">
                        <td style="padding:14px 16px; border-bottom:1px solid rgba(0,83,122,0.05);">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; background:#00537A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px; flex-shrink:0;">{{ $initials }}</div>
                                <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#013C58; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $adminName }}</div>
                            </div>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); font-size:12.5px; color:rgba(1,60,88,0.65); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $admin->email }}</td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05);">
                            @forelse ($admin->permissions as $permission)
                                <span style="display:inline-flex; margin:2px; padding:4px 10px; border-radius:999px; background:rgba(14,106,150,0.1); color:#0E6A96; font-size:10.5px; font-weight:700;">{{ $permission->name }}</span>
                            @empty
                                <span style="font-size:12px; color:rgba(1,60,88,0.4);">ما في صلاحيات محددة</span>
                            @endforelse
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                            <div style="display:flex; gap:8px; justify-content:center; align-items:center;">
                                <a href="{{ route('admin.permission.show', $admin) }}" title="عرض التفاصيل" class="pmi-icon-btn" style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; background:rgba(168,232,249,0.22); color:#00537A; text-decoration:none;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="{{ route('admin.permissions', $admin) }}" title="إدارة الصلاحيات" class="pmi-icon-btn" style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; background:rgba(255,211,91,0.16); color:#8A5A00; text-decoration:none;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                                </a>
                                <form action="{{ route('admins.permission.destroy', $admin) }}" method="POST" onsubmit="return confirm('متأكدة إنك بدك تعطّلي هالحساب؟ الشخص مارح يقدر يسجل دخول بعدها.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="تعطيل الحساب" style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; border:none; background:rgba(255,138,101,0.14); color:#C2591A; cursor:pointer;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m5.5 5.5 13 13"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="padding:60px 20px; text-align:center; color:rgba(1,60,88,0.45); font-weight:600; font-size:14px;">ما في أدمنز لهلق</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($admins->hasPages())
            <div style="padding:16px 22px; border-top:1px solid rgba(0,83,122,0.06);">{{ $admins->links('vendor.pagination.lessons') }}</div>
        @endif
    </div>

    {{-- ============ TEACHERS TABLE ============ --}}
    <div x-show="tab === 'teachers'" x-cloak style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; overflow:hidden; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
            <thead>
                <tr>
                    <th style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 16px; background:rgba(168,232,249,0.22); width:30%;">الاسم</th>
                    <th style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:35%;">الإيميل</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:20%;">تاريخ الانضمام</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:15%;">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($teachers as $teacher)
                    @php
                        $teacherName = trim($teacher->first_name.' '.$teacher->last_name) ?: $teacher->email;
                        $initials = strtoupper(substr($teacher->first_name ?? $teacher->email, 0, 1).substr($teacher->last_name ?? '', 0, 1));
                    @endphp
                    <tr class="pmi-row">
                        <td style="padding:14px 16px; border-bottom:1px solid rgba(0,83,122,0.05);">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; background:#00537A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px; flex-shrink:0;">{{ $initials }}</div>
                                <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#013C58; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $teacherName }}</div>
                            </div>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); font-size:12.5px; color:rgba(1,60,88,0.65); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $teacher->email }}</td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center; font-size:12.5px; color:rgba(1,60,88,0.65);">{{ $teacher->created_at?->format('Y-m-d') }}</td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                            <form action="{{ route('admins.permission.destroy', $teacher) }}" method="POST" onsubmit="return confirm('متأكدة إنك بدك تعطّلي هالحساب؟ الشخص مارح يقدر يسجل دخول بعدها.');" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="تعطيل الحساب" style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; border:none; background:rgba(255,138,101,0.14); color:#C2591A; cursor:pointer; margin:0 auto;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m5.5 5.5 13 13"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="padding:60px 20px; text-align:center; color:rgba(1,60,88,0.45); font-weight:600; font-size:14px;">ما في أساتذة لهلق</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($teachers->hasPages())
            <div style="padding:16px 22px; border-top:1px solid rgba(0,83,122,0.06);">{{ $teachers->links('vendor.pagination.lessons') }}</div>
        @endif
    </div>
</div>
@endsection
