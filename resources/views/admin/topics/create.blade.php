@extends('dashboard.layouts.app')

@push('styles')
<style>
    .tp-field-wrap { border:1.5px solid rgba(0,83,122,0.14); border-radius:11px; background:#FBFEFF; }
    .tp-field-wrap input { width:100%; background:transparent; border:none; outline:none; padding:11px 13px; font-size:13px; color:#013C58; font-family:'Tajawal',sans-serif; }
    .tp-field-wrap input:focus, .tp-field-wrap input:focus-visible { outline:none !important; box-shadow:none !important; border:none !important; }
    .tp-field-wrap:focus-within { border-color: rgba(14,106,150,0.4) !important; }

    .tp-field-wrap input[type="file"] { padding:7px; font-size:12.5px; color:rgba(1,60,88,0.55); }
    .tp-field-wrap input[type="file"]::file-selector-button {
        margin-inline-end:10px; padding:8px 16px; border:none; border-radius:8px;
        background:linear-gradient(90deg,#0E6A96,#146B93); color:#fff; font-family:'Tajawal',sans-serif;
        font-weight:700; font-size:12.5px; cursor:pointer; transition:transform 0.15s ease, box-shadow 0.15s ease;
    }
    .tp-field-wrap input[type="file"]::file-selector-button:hover {
        transform:translateY(-1px); box-shadow:0 6px 14px rgba(14,106,150,0.28);
    }

    .tp-submit-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .tp-submit-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(245,162,1,0.28); }
</style>
@endpush

@section('content')
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    <div style="margin-bottom:18px;">
        <a href="{{ route('topics.index') }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            العودة للتوبك
        </a>
    </div>

    <div style="background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:22px; padding:26px 32px; margin-bottom:22px; box-shadow:0 20px 44px rgba(1,60,88,0.2);">
        <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:22px; color:#fff;">توبك جديد</h1>
        <p style="margin:6px 0 0; font-size:13px; color:rgba(168,232,249,0.8);">        </p>
    </div>

    @if ($errors->any())
        <div style="background:rgba(255,138,101,0.14); color:#C2591A; border:1px solid rgba(255,138,101,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13px; font-weight:600;">
            @foreach ($errors->all() as $error)
                <p style="margin:0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('topics.store') }}" method="POST" enctype="multipart/form-data" x-data="{ imageFileName: '' }">
        @csrf

        <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:26px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
            <h3 style="margin:0 0 18px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">معلومات التوبك</h3>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">الاسم بالإنكليزي</label>
                    <div class="tp-field-wrap"><input type="text" name="name_en" value="{{ old('name_en') }}" placeholder="e.g. Daily Conversations" required></div>
                </div>
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">الاسم بالعربي</label>
                    <div class="tp-field-wrap"><input type="text" name="name_ar" value="{{ old('name_ar') }}" placeholder="مثال: محادثات يومية" required></div>
                </div>
            </div>

            <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">صورة التوبك (اختياري)</label>
            <div style="display:flex; align-items:center; gap:10px;">
                <div class="tp-field-wrap" style="flex:1;">
                    <input type="file" name="image" accept="image/*" x-ref="imageInput" @change="imageFileName = $refs.imageInput.files.length ? $refs.imageInput.files[0].name : ''">
                </div>
                <button type="button" x-show="imageFileName" x-cloak @click="imageFileName = ''; $refs.imageInput.value = ''"
                    style="position:relative; width:34px; height:34px; padding:0; margin:0; box-sizing:border-box; border-radius:9px; border:1.5px solid rgba(226,60,60,0.25); background:rgba(226,60,60,0.08); color:#C23A3A; flex-shrink:0; cursor:pointer; line-height:0;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
                </button>
            </div>
            <p style="margin:8px 0 0; font-size:11px; color:rgba(1,60,88,0.45);">JPG أو PNG أو WEBP، حتى 2 ميغابايت.</p>
        </div>

        <div style="margin-top:22px;">
            <button type="submit" class="tp-submit-btn" style="display:inline-flex; align-items:center; gap:8px; padding:13px 30px; border-radius:12px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; cursor:pointer;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                إضافة التوبك
            </button>
        </div>
    </form>
</div>
@endsection
