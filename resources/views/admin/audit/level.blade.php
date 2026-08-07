@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes auditLvlFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .adl-card { animation: auditLvlFadeUp 0.4s ease both; }
    .adl-card:hover { transform: translateY(-4px); box-shadow: 0 22px 44px rgba(1,60,88,0.14); }
    .adl-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
</style>
@endpush

@section('content')
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    <div style="margin-bottom:18px;">
        <a href="{{ route('admin.audit.index') }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            العودة للرقابة وإدارة الأعمال
        </a>
    </div>

    <div style="background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:22px; padding:26px 32px; margin-bottom:22px; box-shadow:0 20px 44px rgba(1,60,88,0.2);">
        <p style="margin:0; font-size:11px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:rgba(168,232,249,0.8);">مستوى</p>
        <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:22px; color:#fff;">{{ $level->name_ar }}</h1>
        <p style="margin:6px 0 0; font-size:13px; color:rgba(168,232,249,0.75);">الكورسات الموجودة ضمن هذا المستوى</p>
    </div>

    @if ($courses->isEmpty())
        <div style="background:#EFFAFD; border:1.5px dashed rgba(0,83,122,0.2); border-radius:22px; padding:50px 20px; text-align:center; color:rgba(1,60,88,0.45); font-weight:600; font-size:14px;"></div>
    @else
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:18px;">
            @foreach ($courses as $course)
                @php
                    $fullStars = floor($course->example_rating);
                    $hasHalf = ($course->example_rating - $fullStars) >= 0.5;
                @endphp
                <div class="adl-card" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:20px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
                    <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:700; font-size:14.5px; color:#013C58;">{{ $course->name_ar }}</p>
                    <p style="margin:4px 0 14px; font-size:11.5px; color:rgba(1,60,88,0.5);">{{ $course->name_en }}</p>

                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:14px; padding:10px 12px; background:rgba(255,211,91,0.1); border:1px solid rgba(255,211,91,0.25); border-radius:12px;">
                        <div style="display:flex; gap:2px;">
                            @for ($s = 1; $s <= 5; $s++)
                                @php
                                    $fill = $s <= $fullStars ? '#F5A201' : ($s === $fullStars + 1 && $hasHalf ? 'url(#adl-half-{{ $course->id }})' : 'rgba(1,60,88,0.14)');
                                @endphp
                                <svg width="14" height="14" viewBox="0 0 24 24">
                                    @if ($s === $fullStars + 1 && $hasHalf)
                                        <defs>
                                            <linearGradient id="adl-half-{{ $course->id }}">
                                                <stop offset="50%" stop-color="#F5A201"></stop>
                                                <stop offset="50%" stop-color="rgba(1,60,88,0.14)"></stop>
                                            </linearGradient>
                                        </defs>
                                    @endif
                                    <path d="m12 2 2.9 6 6.6.9-4.8 4.6 1.1 6.5-5.8-3-5.8 3 1.1-6.5-4.8-4.6 6.6-.9L12 2Z" fill="{{ $fill }}"></path>
                                </svg>
                            @endfor
                        </div>
                        <span style="font-family:'Poppins',sans-serif; font-weight:800; font-size:13px; color:#8A5A00;">{{ $course->example_rating }}</span>
                    </div>

                    <div style="display:flex; align-items:center; gap:6px; font-size:11.5px; color:rgba(1,60,88,0.55); font-weight:600;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"></path></svg>
                        {{ $course->lessons_count }} درس
                    </div>
                </div>
            @endforeach
        </div>
        <p style="margin:16px 2px 0; font-size:11.5px; color:rgba(1,60,88,0.45); font-weight:600;">
           
        </p>
    @endif
</div>
@endsection
