@extends('dashboard.layouts.app')

@section('content')
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    <div style="display:flex; align-items:center; gap:14px; margin-bottom:22px;">
        <a href="{{ route('admin.permission.index') }}" style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); color:#00537A; text-decoration:none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"></path></svg>
        </a>
        <div>
            <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:rgba(1,60,88,0.5);">إدارة ومتابعة / الأدمنز</p>
            <h1 style="margin:4px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#013C58;">{{ trim($admin->first_name.' '.$admin->last_name) ?: $admin->email }}</h1>
        </div>
    </div>

    <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; padding:26px; max-width:640px; box-shadow:0 18px 44px rgba(0,83,122,0.06); margin-bottom:18px;">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:8px;">
            <div>
                <p style="margin:0 0 4px; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.5); text-transform:uppercase;">البريد الإلكتروني</p>
                <p style="margin:0; font-size:13.5px; font-weight:700; color:#013C58;">{{ $admin->email }}</p>
            </div>
            <div>
                <p style="margin:0 0 4px; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.5); text-transform:uppercase;">تاريخ الانضمام</p>
                <p style="margin:0; font-size:13.5px; font-weight:700; color:#013C58;">{{ $admin->created_at?->format('Y-m-d') }}</p>
            </div>
        </div>
    </div>

    <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; padding:26px; max-width:640px; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
            <p style="margin:0; font-size:12.5px; font-weight:700; color:#00537A;">الصلاحيات الحالية</p>
            <a href="{{ route('admin.permissions', $admin) }}" style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:999px; background:linear-gradient(90deg,#013C58,#00537A); color:#fff; text-decoration:none; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px;">
                تعديل الصلاحيات
            </a>
        </div>
        <div>
            @forelse ($admin->permissions as $permission)
                <span style="display:inline-flex; margin:3px; padding:6px 14px; border-radius:999px; background:rgba(14,106,150,0.1); color:#0E6A96; font-size:12px; font-weight:700;">{{ $permission->name }}</span>
            @empty
                <p style="margin:0; font-size:13px; color:rgba(1,60,88,0.45);">ما في صلاحيات ممنوحة لهالأدمن لهلق.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
