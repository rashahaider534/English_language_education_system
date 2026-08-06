@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .t-panel { animation: lessonsFadeUp 0.4s ease both; }

    .t-q-row { transition: background 0.15s ease; }
    .t-q-row:hover { background: rgba(168,232,249,0.1); }
</style>
@endpush

@section('content')
@php
    $typeLabels = ['MCQ' => 'اختيار من متعدد', 'FILL' => 'ملء فراغ', 'ARRANGE' => 'ترتيب كلمات', 'PAIR' => 'توصيل'];
    $difficultyLabels = ['EASY' => 'سهل', 'MEDIUM' => 'متوسط', 'HARD' => 'صعب'];
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
    $statusVal = $test->status?->value ?? $test->status;
    $sc = $statusColors[$statusVal] ?? $statusColors['draft'];
    $orderedQuestions = $test->questions->sortBy(fn($q) => $q->pivot->order)->values();
    $totalScore = $orderedQuestions->sum('score');
@endphp
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    @if (session('success'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(168,232,249,0.18); color:#00537A; border:1px solid rgba(0,83,122,0.14); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-bottom:18px;">
        <a href="{{ route('levels.index') }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            العودة إلى المستويات
        </a>
    </div>

    {{-- ============ HERO ============ --}}
    <div style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.2) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
            <div>
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                    <span style="display:inline-flex; padding:5px 12px; border-radius:999px; background:rgba(255,255,255,0.12); color:#FFD35B; font-size:11px; font-weight:700; border:1px solid rgba(255,255,255,0.18);">#{{ $test->id }}</span>
                    <span style="display:inline-flex; padding:5px 12px; border-radius:999px; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }}; font-size:11px; font-weight:700;">{{ $statusLabels[$statusVal] ?? $statusVal }}</span>
                    <span style="display:inline-flex; padding:5px 12px; border-radius:999px; background:rgba(255,255,255,0.1); color:#A8E8F9; font-size:11px; font-weight:700; border:1px solid rgba(255,255,255,0.18);">مستوى: {{ $level->name_ar ?? $level->name_en }}</span>
                </div>
                <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:21px; color:#fff;">{{ $test->title_en }}</h1>
                <p style="margin:6px 0 0; font-size:14px; color:rgba(168,232,249,0.85);">{{ $test->title_ar }}</p>
            </div>
            <div style="display:flex; align-items:center; gap:14px;">
                <div style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.16); border-radius:16px; padding:14px 22px; text-align:center;">
                    <p style="margin:0; font-size:10px; font-weight:700; letter-spacing:0.6px; text-transform:uppercase; color:rgba(168,232,249,0.7);">درجة النجاح</p>
                    <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $test->passing_score ?? '—' }}</p>
                </div>
                <div style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.16); border-radius:16px; padding:14px 22px; text-align:center;">
                    <p style="margin:0; font-size:10px; font-weight:700; letter-spacing:0.6px; text-transform:uppercase; color:rgba(168,232,249,0.7);">عدد الأسئلة</p>
                    <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $orderedQuestions->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ ELIGIBILITY BANNER ============ --}}
    <div style="display:flex; align-items:center; gap:14px; border-radius:18px; padding:18px 24px; margin-bottom:22px; border:1.5px solid {{ $isEligible ? 'rgba(76,175,120,0.35)' : 'rgba(229,72,77,0.35)' }}; background:{{ $isEligible ? 'rgba(76,175,120,0.1)' : 'rgba(229,72,77,0.08)' }};">
        <div style="display:flex; align-items:center; justify-content:center; width:42px; height:42px; border-radius:12px; background:{{ $isEligible ? '#4CAF78' : '#E5484D' }}; color:#fff; flex-shrink:0;">
            @if ($isEligible)
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
            @else
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="M6 6l12 12"></path></svg>
            @endif
        </div>
        <div>
            <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:15px; color:{{ $isEligible ? '#2E7D55' : '#C2323A' }};">{{ $isEligible ? 'مؤهل' : 'غير مؤهل' }}</p>
            <p style="margin:4px 0 0; font-size:12.5px; color:rgba(1,60,88,0.6); line-height:1.6;">
                {{ $isEligible
                    ? 'كل أسئلة هالاختبار لسا مؤهلة (منتمية لكورسات هالمستوى ومعتمدة/منشورة).'
                    : 'واحد أو أكثر من أسئلة هالاختبار ما عاد مؤهل (مثلاً تم حذفه من درس المصدر أو تغيرت حالته). قد يحتاج الاختبار مراجعة أو تعديل.' }}
            </p>
        </div>
    </div>

    {{-- ============ QUESTIONS LIST ============ --}}
    <div class="t-panel" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; padding:26px; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
            <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">أسئلة الاختبار (بالترتيب)</h3>
            <span style="font-size:12px; font-weight:700; color:rgba(1,60,88,0.55);">إجمالي النقاط: {{ $totalScore }}</span>
        </div>
        <div style="display:flex; flex-direction:column; gap:10px;">
            @forelse ($orderedQuestions as $q)
                @php $qType = $q->type instanceof \BackedEnum ? $q->type->value : $q->type; @endphp
                <a href="{{ route('questions.show', $q) }}" class="t-q-row" style="display:flex; align-items:center; gap:14px; padding:13px 16px; border-radius:13px; background:rgba(0,83,122,0.03); text-decoration:none;">
                    <span style="display:flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:9px; background:#00537A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:12.5px; flex-shrink:0;">{{ $q->pivot->order }}</span>
                    <div style="flex:1; min-width:0;">
                        <p style="margin:0; font-size:13.5px; font-weight:700; color:#013C58; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $q->title_question_en }}</p>
                        <p style="margin:2px 0 0; font-size:12px; color:rgba(1,60,88,0.55);">{{ $q->title_question_ar }}</p>
                    </div>
                    <span style="display:inline-flex; padding:5px 11px; border-radius:999px; background:rgba(14,106,150,0.12); color:#0E6A96; font-size:11px; font-weight:700; flex-shrink:0;">{{ $typeLabels[$qType] ?? $qType }}</span>
                    <span style="display:inline-flex; padding:5px 11px; border-radius:999px; background:rgba(255,186,66,0.16); color:#8A5A00; font-size:11px; font-weight:700; flex-shrink:0;">{{ $difficultyLabels[$q->difficulty] ?? $q->difficulty }}</span>
                    <span style="font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; color:rgba(1,60,88,0.7); flex-shrink:0;">{{ $q->score }} نقطة</span>
                </a>
            @empty
                <p style="text-align:center; color:rgba(1,60,88,0.45); font-weight:600; font-size:13px; padding:30px 0;">ما في أسئلة بهالاختبار</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
