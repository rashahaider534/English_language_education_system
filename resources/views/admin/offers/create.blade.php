@extends('dashboard.layouts.app')

@push('styles')
<style>
    .ofc-field-wrap { border:1.5px solid rgba(0,83,122,0.14); border-radius:11px; background:#FBFEFF; }
    .ofc-field-wrap input, .ofc-field-wrap select { width:100%; background:transparent; border:none; outline:none; padding:11px 13px; font-size:13px; color:#013C58; font-family:'Tajawal',sans-serif; }
    .ofc-submit-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .ofc-submit-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(245,162,1,0.28); }
</style>
@endpush

@section('content')
<div x-data="{ saved: false }" class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    <div style="margin-bottom:18px;">
        <a href="{{ route('admin.offers.index') }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            العودة للعروض
        </a>
    </div>

    <div style="background:linear-gradient(135deg,#013C58 0%, #00537A 55%, #C2591A 150%); border-radius:22px; padding:26px 32px; margin-bottom:22px; box-shadow:0 20px 44px rgba(1,60,88,0.2);">
        <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:22px; color:#fff;">إنشاء عرض / خصم جديد</h1>
        <p style="margin:6px 0 0; font-size:13px; color:rgba(255,211,91,0.85);">حددي الكورس ونسبة الخصم ومدة العرض</p>
    </div>

    <form method="POST" action="{{ route('admin.offers.store') }}" @submit.prevent="saved = true" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:26px; max-width:640px; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        @csrf

        <div style="margin-bottom:16px;">
            <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">الكورس</label>
            <div class="ofc-field-wrap">
                <select name="course_id" required>
                    <option value="" disabled selected>اختاري الكورس...</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name_ar }} ({{ $course->name_en }}) — {{ $course->level->name_ar ?? '' }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
            <div>
                <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">نسبة الخصم %</label>
                <div class="ofc-field-wrap"><input type="number" name="discount_percent" min="1" max="90" placeholder="مثال: 25" required></div>
            </div>
            <div></div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:22px;">
            <div>
                <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">تاريخ البداية</label>
                <div class="ofc-field-wrap"><input type="date" name="starts_at" required></div>
            </div>
            <div>
                <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">تاريخ النهاية</label>
                <div class="ofc-field-wrap"><input type="date" name="ends_at" required></div>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; padding-top:18px; border-top:1px solid rgba(0,83,122,0.06);">
            <a href="{{ route('admin.offers.index') }}" style="display:inline-flex; align-items:center; padding:12px 22px; border-radius:12px; background:rgba(0,83,122,0.08); color:#00537A; text-decoration:none; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px;">إلغاء</a>
            <button type="submit" class="ofc-submit-btn" style="display:inline-flex; align-items:center; gap:7px; padding:12px 26px; border-radius:12px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                حفظ العرض
            </button>
        </div>
    </form>

    <div x-show="saved" x-cloak x-transition style="display:flex; align-items:center; gap:10px; background:rgba(76,175,120,0.14); color:#2E7D55; border:1px solid rgba(76,175,120,0.3); border-radius:14px; padding:12px 18px; margin-top:16px; max-width:640px; font-size:12.5px; font-weight:600;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
        تم استلام بيانات العرض بالواجهة (ما في ربط بالباك-إند لهلق).
    </div>
</div>
@endsection
