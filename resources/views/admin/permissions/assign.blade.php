@extends('dashboard.layouts.app')

@section('content')
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    <div style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.2) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>
        <div style="position:relative; display:flex; align-items:center; gap:16px;">
            <div style="display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"></path><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"></path></svg>
            </div>
            <div>
                <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">إدارة الصلاحيات</p>
                <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">
                    صلاحيات {{ trim($user->first_name.' '.$user->last_name) ?: $user->email }}
                </h1>
                <p style="margin:6px 0 0; font-size:12.5px; color:rgba(168,232,249,0.85);">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(76,175,120,0.14); color:#2E7D55; border:1px solid rgba(76,175,120,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="display:flex; align-items:flex-start; gap:9px; margin-bottom:20px; padding:13px 16px; border-radius:12px; background:rgba(148,98,0,0.08); color:#946200; font-size:13px; font-weight:600;">
            <ul style="margin:0; padding-inline-start:16px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.permission.assignPermissions', $user->id) }}" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; padding:28px; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        @csrf

        <p style="margin:0 0 18px; font-size:12.5px; font-weight:700; color:#00537A;">حدد الصلاحيات التي تريد منحها لهذا الأدمن:</p>

        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:12px; margin-bottom:24px;">
            @foreach ($permissions as $permission)
                @php
                    $label = match($permission->name) {
                        'manage_levels' => 'إدارة المستويات',
                        'manage_courses' => 'إدارة الكورسات',
                        'manage_level_tests' => 'إدارة اختبارات تحديد المستوى',
                        'manage_placement_tests' => 'إدارة اختبارات القبول',
                        'manage_placement_questions' => 'إدارة بنك أسئلة تحديد المستوى',
                        'manage_podcasts' => 'إدارة البودكاست والتوبك',
                        'publish_levels' => 'نشر المستويات',
                        default => $permission->name,
                    };
                    $checked = old('permissions') ? in_array($permission->name, old('permissions')) : $user->hasPermissionTo($permission->name, 'web');
                @endphp
                <label style="display:flex; align-items:center; gap:10px; padding:13px 16px; border-radius:13px; border:1.5px solid rgba(0,83,122,0.14); background:#FBFEFF; cursor:pointer; font-size:13px; font-weight:600; color:#013C58;">
                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked($checked) style="width:17px; height:17px; accent-color:#0E6A96;">
                    {{ $label }}
                </label>
            @endforeach
        </div>

        <div style="display:flex; flex-direction:row-reverse; gap:10px; padding-top:18px; border-top:1px solid rgba(0,83,122,0.06);">
            <button type="submit" style="display:flex; align-items:center; gap:7px; padding:12px 24px; border-radius:999px; border:none; background:linear-gradient(90deg,#013C58,#00537A); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; cursor:pointer; box-shadow:0 14px 28px rgba(1,60,88,0.28);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                حفظ الصلاحيات
            </button>
            <a href="{{ route('admin.permission.show', $user->id) }}" style="display:inline-flex; align-items:center; padding:12px 20px; border-radius:999px; background:rgba(0,83,122,0.08); color:#00537A; text-decoration:none; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px;">تخطي الآن</a>
        </div>
    </form>
</div>
@endsection
