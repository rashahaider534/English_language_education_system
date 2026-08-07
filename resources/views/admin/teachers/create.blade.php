@extends('dashboard.layouts.app')

@section('content')
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    <div style="display:flex; align-items:center; gap:14px; margin-bottom:22px;">
        <a href="{{ route('admin.teachers.index') }}" style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); color:#00537A; text-decoration:none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"></path></svg>
        </a>
        <div>
            <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:rgba(1,60,88,0.5);">إدارة ومتابعة / أساتذة</p>
            <h1 style="margin:4px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#013C58;">إضافة أستاذ جديد</h1>
        </div>
    </div>

    @if ($errors->any())
        <div style="display:flex; align-items:flex-start; gap:9px; margin-bottom:18px; padding:13px 16px; border-radius:12px; background:rgba(148,98,0,0.08); color:#946200; font-size:13px; font-weight:600; max-width:640px;">
            <ul style="margin:0; padding-inline-start:16px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('teachers.store') }}" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; padding:26px; max-width:640px; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        @csrf

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
            <div>
                <label style="display:block; font-size:12.5px; font-weight:700; color:#013C58; margin-bottom:6px;">الاسم الأول</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="مثال: أحمد" style="width:100%; padding:10px 14px; border:1.5px solid rgba(0,83,122,0.14); border-radius:10px; background:#FBFEFF; color:#013C58; font-size:13px; font-family:'Tajawal',sans-serif; outline:none;">
            </div>
            <div>
                <label style="display:block; font-size:12.5px; font-weight:700; color:#013C58; margin-bottom:6px;">الاسم الأخير</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="مثال: خالد" style="width:100%; padding:10px 14px; border:1.5px solid rgba(0,83,122,0.14); border-radius:10px; background:#FBFEFF; color:#013C58; font-size:13px; font-family:'Tajawal',sans-serif; outline:none;">
            </div>
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; font-size:12.5px; font-weight:700; color:#013C58; margin-bottom:6px;">البريد الإلكتروني</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="teacher@example.com" style="width:100%; padding:10px 14px; border:1.5px solid rgba(0,83,122,0.14); border-radius:10px; background:#FBFEFF; color:#013C58; font-size:13px; font-family:'Tajawal',sans-serif; outline:none;">
        </div>

        <div style="margin-bottom:22px;">
            <label style="display:block; font-size:12.5px; font-weight:700; color:#013C58; margin-bottom:6px;">كلمة المرور المبدئية</label>
            <input type="text" name="password" placeholder="8 أحرف على الأقل" style="width:100%; padding:10px 14px; border:1.5px solid rgba(0,83,122,0.14); border-radius:10px; background:#FBFEFF; color:#013C58; font-size:13px; font-family:'Tajawal',sans-serif; outline:none;">
            <p style="margin:6px 0 0; font-size:11.5px; color:rgba(1,60,88,0.45);">رح تنبعت هالكلمة تلقائيًا للأستاذ عبر إيميل ترحيبي.</p>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('admin.teachers.index') }}" style="display:inline-flex; align-items:center; padding:10px 20px; border-radius:10px; background:rgba(0,83,122,0.08); color:#00537A; text-decoration:none; font-family:'Poppins',sans-serif; font-weight:600; font-size:12.5px;">إلغاء</a>
            <button type="submit" style="display:inline-flex; align-items:center; padding:10px 24px; border-radius:10px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:12.5px; cursor:pointer;">حفظ</button>
        </div>
    </form>
</div>
@endsection
