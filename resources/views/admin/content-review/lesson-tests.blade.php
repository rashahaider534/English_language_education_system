@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .t-panel, .t-row { animation: lessonsFadeUp 0.4s ease both; }
    .t-row { transition: background 0.15s ease; }
    .t-row:hover { background: rgba(168,232,249,0.1); }
</style>
@endpush

@section('content')
@php
    $statusLabels = [
        'draft' => 'مسودة', 'pending' => 'قيد الإرسال', 'in_review' => 'قيد المراجعة',
        'changes_requested' => 'مطلوب تعديل', 'approved' => 'موافق عليه', 'published' => 'منشور',
        'archived' => 'مؤرشف', 'closed' => 'مغلق',
    ];
    $statusColors = [
        'draft'              => ['bg' => 'rgba(0,83,122,0.1)', 'fg' => '#00537A'],
        'pending'            => ['bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00'],
        'in_review'          => ['bg' => 'rgba(14,106,150,0.14)', 'fg' => '#0E6A96'],
        'changes_requested'  => ['bg' => 'rgba(255,138,101,0.18)', 'fg' => '#C2591A'],
        'approved'           => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55'],
        'published'          => ['bg' => 'rgba(76,175,120,0.22)', 'fg' => '#1E5C3B'],
        'archived'           => ['bg' => 'rgba(1,60,88,0.1)', 'fg' => 'rgba(1,60,88,0.55)'],
        'closed'             => ['bg' => 'rgba(229,72,77,0.14)', 'fg' => '#C2323A'],
    ];
@endphp
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    <div style="margin-bottom:18px;">
        <a href="{{ route('lessons.show', $lesson) }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            العودة للدرس
        </a>
    </div>

    <div style="background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:22px; padding:26px 32px; margin-bottom:22px; box-shadow:0 20px 44px rgba(1,60,88,0.2);">
        <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">اختبارات الدرس (كل النسخ)</p>
        <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:21px; color:#fff;">{{ $lesson->title_ar }}</h1>
    </div>

    <div class="t-panel" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; overflow:hidden; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        @forelse ($testVersions as $test)
            @php
                $statusVal = $test->status?->value ?? $test->status;
                $sc = $statusColors[$statusVal] ?? $statusColors['draft'];
            @endphp
            <a href="{{ route('test.show', $test) }}" class="t-row" style="display:flex; align-items:center; gap:16px; padding:18px 24px; border-bottom:1px solid rgba(0,83,122,0.06); text-decoration:none;">
                <div style="flex:1; min-width:0;">
                    <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#013C58;">{{ $test->title_en }}</p>
                    <p style="margin:2px 0 0; font-size:12px; color:rgba(1,60,88,0.6);">{{ $test->title_ar }}</p>
                </div>
                @if ($test->previous_test_id)
                    <span style="font-size:11px; font-weight:600; color:rgba(1,60,88,0.5);">نسخة معدَّلة</span>
                @endif
                <span style="display:inline-flex; padding:5px 11px; border-radius:999px; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }}; font-size:11px; font-weight:700; flex-shrink:0;">{{ $statusLabels[$statusVal] ?? $statusVal }}</span>
                <span style="font-size:12px; color:rgba(1,60,88,0.5); flex-shrink:0;">{{ $test->created_at?->format('Y-m-d') }}</span>
            </a>
        @empty
            <p style="text-align:center; color:rgba(1,60,88,0.45); font-weight:600; font-size:13px; padding:50px 0;">لايوجد اختبارات لهذا الدرس </p>
        @endforelse
    </div>
</div>
@endsection
