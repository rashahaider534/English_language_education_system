@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .q-hero, .q-stat, .q-filters, .q-row { animation: lessonsFadeUp 0.4s ease both; }
    .q-stat:nth-child(1) { animation-delay: 0.02s; }
    .q-stat:nth-child(2) { animation-delay: 0.06s; }
    .q-stat:nth-child(3) { animation-delay: 0.1s; }
    .q-stat:nth-child(4) { animation-delay: 0.14s; }

    .q-row { transition: background 0.15s ease; }
    .q-row:hover { background: rgba(168,232,249,0.1); }

    .q-icon-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .q-icon-btn:hover { transform: translateY(-2px) scale(1.05); box-shadow: 0 8px 16px rgba(1,60,88,0.16); }

    .q-create-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .q-create-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(245,162,1,0.28); }
</style>
@endpush

@section('content')
@php
    $typeLabels = ['MCQ' => 'اختيار من متعدد', 'FILL' => 'ملء فراغ', 'ARRANGE' => 'ترتيب كلمات', 'PAIR' => 'توصيل'];
    $typeColors = [
        'MCQ'     => ['bg' => 'rgba(14,106,150,0.14)', 'fg' => '#0E6A96'],
        'FILL'    => ['bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00'],
        'ARRANGE' => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55'],
        'PAIR'    => ['bg' => 'rgba(255,138,101,0.18)', 'fg' => '#C2591A'],
    ];
    $difficultyLabels = ['EASY' => 'سهل', 'MEDIUM' => 'متوسط', 'HARD' => 'صعب'];
    $difficultyColors = [
        'EASY'   => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55'],
        'MEDIUM' => ['bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00'],
        'HARD'   => ['bg' => 'rgba(255,138,101,0.18)', 'fg' => '#C2591A'],
    ];
    $currentType = request()->query('type');
    $currentDifficulty = request()->query('difficulty');
    $currentSearch = request()->query('search');

    $totalCount = $questions->total();
@endphp
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    @if (session('success'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(168,232,249,0.18); color:#00537A; border:1px solid rgba(0,83,122,0.14); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-bottom:18px;">
        <a href="{{ route('tests.level.levelTest.index', $level) }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            العودة لاختبارات هالمستوى
        </a>
    </div>

    {{-- ============ HERO ============ --}}
    <div class="q-hero" style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.2) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:22px;">
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11H7.82a48.149 48.149 0 0 1 2.088-.722"></path><path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"></path></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">بنك الأسئلة</p>
                    <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">الأسئلة المؤهلة لمستوى: {{ $level->name_ar }}</h1>
                </div>
            </div>
            <a href="{{ route('tests.level.levelTest.create', $level) }}" class="q-create-btn" style="display:inline-flex; align-items:center; gap:8px; padding:12px 22px; border-radius:12px; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; text-decoration:none; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"></path></svg>
                إنشاء اختبار لهالمستوى
            </a>
        </div>

        <div style="position:relative; display:flex; gap:14px; flex-wrap:wrap;">
            @php
                $statCard = 'display:flex; align-items:center; gap:13px; background:rgba(255,211,91,0.08); border:1px solid rgba(255,211,91,0.22); border-radius:16px; padding:14px 18px; flex:1; min-width:150px;';
                $iconWrapBase = 'display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.14); flex-shrink:0;';
            @endphp
            <div class="q-stat" style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#FFD35B;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M3 10h18"></path></svg></div>
                <div><p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,236,176,0.85);">إجمالي الأسئلة المؤهلة</p><p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $totalCount }}</p></div>
            </div>
        </div>
    </div>

    {{-- ============ FILTERS ============ --}}
    <form method="GET" action="{{ route('tests.level.questions.index', $level) }}" class="q-filters" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:22px; background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:16px; padding:14px 18px;">
        <input type="text" name="search" value="{{ $currentSearch }}" placeholder="ابحث بنص السؤال..." style="flex:1; min-width:180px; padding:9px 14px; border:1.5px solid rgba(0,83,122,0.14); border-radius:10px; background:#FBFEFF; color:#013C58; font-size:13px; font-family:'Tajawal',sans-serif; outline:none;">

        <select name="type" style="padding:9px 14px; border:1.5px solid rgba(0,83,122,0.14); border-radius:10px; background:#FBFEFF; color:#013C58; font-size:12.5px; font-family:'Tajawal',sans-serif; outline:none;">
            <option value="" style="color:#013C58;">{{ !$currentType ? '✓' : '○' }} كل الأنواع</option>
            @foreach ($typeLabels as $val => $label)
                <option value="{{ $val }}" @selected($currentType === $val) style="color:{{ $typeColors[$val]['fg'] }};">{{ $currentType === $val ? '✓' : '●' }} {{ $label }}</option>
            @endforeach
        </select>

        <select name="difficulty" style="padding:9px 14px; border:1.5px solid rgba(0,83,122,0.14); border-radius:10px; background:#FBFEFF; color:#013C58; font-size:12.5px; font-family:'Tajawal',sans-serif; outline:none;">
            <option value="" style="color:#013C58;">{{ !$currentDifficulty ? '✓' : '○' }} كل مستويات الصعوبة</option>
            @foreach ($difficultyLabels as $val => $label)
                <option value="{{ $val }}" @selected($currentDifficulty === $val) style="color:{{ $difficultyColors[$val]['fg'] }};">{{ $currentDifficulty === $val ? '✓' : '●' }} {{ $label }}</option>
            @endforeach
        </select>

        <button type="submit" style="display:inline-flex; align-items:center; gap:6px; padding:9px 18px; border-radius:10px; border:none; background:#013C58; color:#fff; font-family:'Poppins',sans-serif; font-weight:600; font-size:12.5px; cursor:pointer;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3"></path></svg>
            فلترة
        </button>
        @if ($currentType || $currentDifficulty || $currentSearch)
            <a href="{{ route('tests.level.questions.index', $level) }}" style="font-size:12px; color:rgba(1,60,88,0.5); font-weight:600; text-decoration:none;">إلغاء الفلترة</a>
        @endif
    </form>

    {{-- ============ QUESTIONS TABLE ============ --}}
    <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; overflow:hidden; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
            <thead>
                <tr>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:6%;">#</th>
                    <th style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:34%;">نص السؤال</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:16%;">النوع</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:14%;">الصعوبة</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:10%;">النقاط</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:20%;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($questions as $question)
                    @php
                        $questionType = $question->type instanceof \BackedEnum ? $question->type->value : $question->type;
                        $tc = $typeColors[$questionType] ?? $typeColors['MCQ'];
                        $dc = $difficultyColors[$question->difficulty] ?? $difficultyColors['EASY'];
                        $isPublished = $question->isUsedInPublishedTests();
                        $isClosed = $question->isUsedInClosedTests();
                        $canDelete = !$isPublished && !$isClosed;
                    @endphp
                    <tr class="q-row">
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                            <div style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:rgba(0,83,122,0.06); color:rgba(1,60,88,0.6); font-family:'Poppins',sans-serif; font-weight:700; font-size:12px;">{{ $question->id }}</div>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05);">
                            <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#00537A; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $question->title_question_en }}</div>
                            <div style="font-size:12px; color:#0E6A96; opacity:0.75; margin-top:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $question->title_question_ar }}</div>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                            <span style="display:inline-flex; padding:5px 11px; border-radius:999px; background:{{ $tc['bg'] }}; color:{{ $tc['fg'] }}; font-size:11px; font-weight:700;">{{ $typeLabels[$questionType] ?? $questionType }}</span>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                            <span style="display:inline-flex; padding:5px 11px; border-radius:999px; background:{{ $dc['bg'] }}; color:{{ $dc['fg'] }}; font-size:11px; font-weight:700;">{{ $difficultyLabels[$question->difficulty] ?? $question->difficulty }}</span>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                            <span style="font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; color:rgba(1,60,88,0.7);">{{ $question->score }}</span>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                            <div style="display:flex; gap:8px; justify-content:center;">
                                <a href="{{ route('questions.show', $question) }}" title="عرض" class="q-icon-btn" style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; background:rgba(168,232,249,0.22); color:#00537A; text-decoration:none;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </a>
                                <a href="{{ route('questions.edit', $question) }}" title="تعديل" class="q-icon-btn" style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; background:rgba(255,211,91,0.16); color:#8A5A00; text-decoration:none;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                                </a>
                                <form action="{{ route('questions.delete', $question) }}" method="POST" onsubmit="return confirm('حذف هالسؤال؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" @disabled(!$canDelete) title="{{ $canDelete ? 'حذف' : 'ما فيك تحذفي سؤال مستخدم بامتحان منشور أو مغلق' }}" class="q-icon-btn" style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; border:none; background:rgba(229,72,77,0.1); color:#C2591A; cursor:{{ $canDelete ? 'pointer' : 'not-allowed' }}; opacity:{{ $canDelete ? 1 : 0.35 }};">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:60px 20px; text-align:center; color:rgba(1,60,88,0.45); font-weight:600; font-size:14px;">لايوجد اسئلة داخل هذا القسم   </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($questions instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div style="padding:16px 22px; border-top:1px solid rgba(0,83,122,0.06);">
                {{ $questions->appends(request()->query())->links('vendor.pagination.lessons') }}
            </div>
        @endif
    </div>
</div>
@endsection
