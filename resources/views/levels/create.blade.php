@extends('dashboard.layouts.app')

@section('content')
<div style="font-family:'Tajawal',sans-serif; max-width:680px; margin:0 auto;" dir="rtl">

    <div style="display:flex; align-items:center; gap:10px; margin-bottom:22px;">
        <a href="{{ route('levels.index') }}" style="display:flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:11px; background:rgba(0,83,122,0.07); color:#00537A; text-decoration:none;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"></path><path d="M11 18l-6-6 6-6"></path></svg>
        </a>
        <div>
            <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:22px; color:#013C58;">إضافة مستوى جديد</h1>
            <p style="margin:4px 0 0; font-size:13px; color:rgba(1,60,88,0.55);">عبّي التفاصيل لإنشاء مستوى تعلّم جديد</p>
        </div>
    </div>

    @if ($errors->any())
        <div style="display:flex; align-items:flex-start; gap:9px; margin-bottom:18px; padding:13px 16px; border-radius:12px; background:rgba(148,98,0,0.08); color:#946200; font-size:13px; font-weight:600;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
            <ul style="margin:0; padding-inline-start:16px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('levels.store') }}" style="background:#fff; border:1px solid rgba(0,83,122,0.08); border-radius:22px; padding:28px 26px; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        @csrf

        @php
            $label = 'display:block; font-size:12px; font-weight:600; color:rgba(1,60,88,0.6); margin-bottom:7px;';
            $wrap = 'border:1.5px solid rgba(0,83,122,0.1); border-radius:11px; padding:0 4px; background:#F7FBFD;';
            $input = "width:100%; background:transparent; border:none; outline:none; padding:11px 11px; font-size:13.5px; color:#013C58; font-family:'Tajawal',sans-serif;";
            $section = 'margin:0 0 12px; font-size:11.5px; font-weight:700; letter-spacing:0.5px; text-transform:uppercase; color:rgba(1,60,88,0.4);';
        @endphp

        <p style="{{ $section }}">الاسم</p>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div>
                <label style="{{ $label }}">بالإنكليزي</label>
                <div style="{{ $wrap }}">
                    <input name="name_en" value="{{ old('name_en') }}" placeholder="e.g. Beginner A1" style="{{ $input }}">
                </div>
            </div>
            <div>
                <label style="{{ $label }}">بالعربي</label>
                <div style="{{ $wrap }}">
                    <input name="name_ar" value="{{ old('name_ar') }}" placeholder="مثال: مبتدئ A1" style="{{ $input }}">
                </div>
            </div>
        </div>

        <p style="{{ $section }} margin-top:22px;">الترتيب ونطاق الدرجات</p>
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px;">
            <div>
                <label style="{{ $label }}">الترتيب</label>
                <div style="{{ $wrap }}">
                    <input type="number" name="order" value="{{ old('order') }}" placeholder="1" style="{{ $input }}">
                </div>
            </div>
            <div>
                <label style="{{ $label }}">أدنى علامة</label>
                <div style="{{ $wrap }}">
                    <input type="number" name="minimum_score" value="{{ old('minimum_score') }}" placeholder="0" style="{{ $input }}">
                </div>
            </div>
            <div>
                <label style="{{ $label }}">أعلى علامة</label>
                <div style="{{ $wrap }}">
                    <input type="number" name="maximum_score" value="{{ old('maximum_score') }}" placeholder="100" style="{{ $input }}">
                </div>
            </div>
        </div>

        <p style="{{ $section }} margin-top:22px;">السعر والمدة</p>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div>
                <label style="{{ $label }}">السعر (بالدولار)</label>
                <div style="{{ $wrap }}">
                    <input type="number" name="price" value="{{ old('price') }}" placeholder="49" style="{{ $input }}">
                </div>
            </div>
            <div>
                <label style="{{ $label }}">المدة المتوقعة (أسابيع)</label>
                <div style="{{ $wrap }}">
                    <input type="number" name="estimated_duration" value="{{ old('estimated_duration') }}" placeholder="8" style="{{ $input }}">
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:26px; padding-top:20px; border-top:1px solid rgba(0,83,122,0.06);">
            <a href="{{ route('levels.index') }}" style="padding:11px 20px; border-radius:11px; border:1.5px solid rgba(0,83,122,0.12); background:#fff; color:#013C58; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; text-decoration:none;">إلغاء</a>
            <button type="submit" style="display:flex; align-items:center; gap:7px; padding:11px 22px; border-radius:11px; border:none; background:#013C58; color:#fff; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer; box-shadow:0 10px 22px rgba(1,60,88,0.2);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                إضافة المستوى
            </button>
        </div>
    </form>
</div>
@endsection
