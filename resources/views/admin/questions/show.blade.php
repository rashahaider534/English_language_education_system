@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .show-video-card, .show-meta-card, .show-panel, .show-actionbar { animation: lessonsFadeUp 0.4s ease both; }
    .show-meta-card:nth-child(1) { animation-delay: 0.05s; }
    .show-meta-card:nth-child(2) { animation-delay: 0.09s; }
    .show-meta-card:nth-child(3) { animation-delay: 0.13s; }
    .show-meta-card:nth-child(4) { animation-delay: 0.17s; }
    .show-video-card { animation-delay: 0.1s; }
    .show-panel { animation-delay: 0.1s; }
    .show-actionbar { animation-delay: 0.18s; }

    .show-meta-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .show-meta-card:hover { transform: translateY(-3px) translateX(-2px); box-shadow: 0 10px 22px rgba(1,60,88,0.1); }

    .show-archive-btn, .show-danger-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .show-archive-btn:hover:not(:disabled) { transform: translateY(-2px) scale(1.03); box-shadow: 0 8px 16px rgba(1,60,88,0.18); }
    .show-danger-btn:hover:not(:disabled) { transform: translateY(-2px); }

    .show-back-btn { transition: background 0.15s ease, transform 0.15s ease; }
    .show-back-btn:hover { background: rgba(0,83,122,0.1); transform: translateY(-1px); }

    .show-mcq-card { transition: transform 0.18s ease, box-shadow 0.18s ease; }
    .show-mcq-card:hover { transform: translateY(-3px); box-shadow: 0 12px 22px rgba(1,60,88,0.12); }

    .show-arrange-step { transition: transform 0.15s ease; }
    .show-arrange-step:hover { transform: translateX(-3px); }

    .show-pair-pill { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .show-pair-pill:hover { transform: translateY(-2px); box-shadow: 0 10px 18px rgba(1,60,88,0.1); }

    .show-status-row { transition: background 0.15s ease; }
    .show-status-row:hover { background: rgba(255,255,255,0.4); }
</style>
@endpush

@section('content')
@php
    $question = $questions; // controller passes the single Question model under this key
    $questionType = $question->type instanceof \BackedEnum ? $question->type->value : $question->type;
    $typeLabels = ['MCQ' => 'اختيار من متعدد', 'FILL' => 'ملء فراغ', 'ARRANGE' => 'ترتيب كلمات', 'PAIR' => 'توصيل'];
    $typeIcons = [
        'MCQ'     => '<path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>',
        'FILL'    => '<rect x="3" y="4" width="18" height="16" rx="3"></rect><path d="M7 9h10"></path><path d="M7 13h5"></path>',
        'ARRANGE' => '<path d="M11 5h10"></path><path d="M11 12h10"></path><path d="M11 19h10"></path><path d="m3 8 3-3 3 3"></path><path d="M6 5v13"></path>',
        'PAIR'    => '<path d="M9 17H7A5 5 0 0 1 7 7h2"></path><path d="M15 7h2a5 5 0 1 1 0 10h-2"></path><path d="M8 12h8"></path>',
    ];
    // Distinct identity color per question type — carries through the sidebar icon, the
    // main panel's tint/accent, and the title-bar watermark, so each type feels different.
    $typeColors = [
        'MCQ'     => ['solid' => '#0E6A96', 'tint' => 'rgba(14,106,150,0.05)', 'bg' => 'rgba(14,106,150,0.12)', 'fg' => '#0E6A96'],
        'FILL'    => ['solid' => '#C1650A', 'tint' => 'rgba(245,162,1,0.05)', 'bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00'],
        'ARRANGE' => ['solid' => '#2E7D55', 'tint' => 'rgba(76,175,120,0.05)', 'bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55'],
        'PAIR'    => ['solid' => '#C2591A', 'tint' => 'rgba(255,138,101,0.05)', 'bg' => 'rgba(255,138,101,0.16)', 'fg' => '#C2591A'],
    ];
    $tc = $typeColors[$questionType] ?? $typeColors['MCQ'];
    $difficultyLabels = ['EASY' => 'سهل', 'MEDIUM' => 'متوسط', 'HARD' => 'صعب'];
    $statusColors = [
        'EASY'   => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55', 'dot' => '#4CAF78'],
        'MEDIUM' => ['bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00', 'dot' => '#F5A201'],
        'HARD'   => ['bg' => 'rgba(255,138,101,0.13)', 'fg' => '#C2591A', 'dot' => '#FF8A65'],
    ];
    $sc = $statusColors[$question->difficulty] ?? $statusColors['EASY'];

    $isPublished = $question->isUsedInPublishedTests();
    $isArchived = $question->isUsedInArchivedTests();
    $isClosed = $question->isUsedInClosedTests();
    $canDelete = !$isPublished && !$isClosed;

    $relation = $question->getAnswersRelationName();
    $answers = $question->{$relation};

    $imageUrl = $question->getFirstMediaUrl('image');
    $audioUrl = $question->getFirstMediaUrl('audio');

    $filledSentence = null;
    if ($questionType === 'FILL' && $question->text_question) {
        $blankMap = $answers->keyBy('blank_order');
        $filledSentence = preg_replace_callback('/\{(\d+)\}/', function ($m) use ($blankMap) {
            $ans = $blankMap->get((int) $m[1]);
            $text = $ans ? e($ans->text_answer) : '؟';
            return '<span style="display:inline-flex; align-items:center; padding:4px 14px; margin:0 3px; border-radius:999px; background:linear-gradient(90deg,#2E7D55,#4CAF78); color:#fff; font-weight:700; box-shadow:0 4px 10px rgba(46,125,85,0.28);">' . $text . '</span>';
        }, e($question->text_question));
    }
@endphp
<div
    x-data="{}"
    class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8"
    style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl"
>
    @if (session('success'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(168,232,249,0.18); color:#00537A; border:1px solid rgba(0,83,122,0.14); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-bottom:18px;">
        <a href="{{ route('questions.placement.index') }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            العودة لبنك الأسئلة
        </a>
    </div>

    {{-- ============ TITLE + STATUS BAR ============ --}}
    <div style="position:relative; overflow:hidden; background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.4); border-radius:20px; padding:20px 24px; box-shadow:0 0 26px rgba(14,106,150,0.32), 0 0 6px rgba(14,106,150,0.2), 0 6px 16px rgba(0,83,122,0.05); margin-bottom:20px; display:grid; grid-template-columns:1fr auto 1fr; align-items:center; gap:16px;">
        <svg width="150" height="150" viewBox="0 0 24 24" fill="none" stroke="{{ $tc['solid'] }}" stroke-width="1" style="position:absolute; left:-30px; top:-40px; opacity:0.06; pointer-events:none;">{!! $typeIcons[$questionType] ?? $typeIcons['MCQ'] !!}</svg>

        <div style="position:relative; display:flex; align-items:center; gap:8px; flex-wrap:wrap; justify-self:start;">
            <span style="display:inline-block; padding:6px 12px; border-radius:999px; background:{{ $tc['bg'] }}; color:{{ $tc['fg'] }}; font-size:11px; font-weight:700; letter-spacing:0.5px; text-transform:uppercase;">{{ $typeLabels[$questionType] ?? $questionType }}</span>
            <span style="display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:999px; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }}; font-size:12px; font-weight:700;">
                <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:{{ $sc['dot'] }};"></span>{{ $difficultyLabels[$question->difficulty] ?? $question->difficulty }}
            </span>
            @if ($question->is_placement_question)
                <span style="display:inline-block; padding:6px 12px; border-radius:999px; background:rgba(168,232,249,0.35); color:#00537A; font-size:11px; font-weight:700;">سؤال تحديد مستوى</span>
            @endif
        </div>

        <div style="position:relative; text-align:center;">
            <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#013C58;">{{ $question->title_question_en }}</h1>
            <p style="margin:4px 0 0; font-size:15.5px; font-weight:600; color:rgba(1,60,88,0.55);">{{ $question->title_question_ar }}</p>
        </div>

        <div style="position:relative; justify-self:end;">
            <div style="display:flex; align-items:center; justify-content:center; width:42px; height:42px; border-radius:12px; background:{{ $tc['bg'] }}; border:1px solid {{ $tc['bg'] }}; color:{{ $tc['fg'] }}; font-family:'Poppins',sans-serif; font-weight:700; flex-shrink:0;">
                <span style="font-size:12px;">#{{ $question->id }}</span>
            </div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:260px 1fr; gap:20px; align-items:start; margin-bottom:20px;">

        {{-- ============ RIGHT (DOM-first): slim meta sidebar ============ --}}
        <div style="display:flex; flex-direction:column; gap:22px;">
            <div class="show-meta-card" style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.4); border-inline-start:4px solid {{ $tc['solid'] }}; border-radius:16px; padding:16px 18px; box-shadow:0 0 26px rgba(14,106,150,0.32), 0 0 6px rgba(14,106,150,0.2), 0 6px 16px rgba(0,83,122,0.05);">
                <p style="margin:0 0 10px; font-size:12px; font-weight:800; letter-spacing:0.3px; text-transform:uppercase; color:#013C58;">نوع السؤال</p>
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:9px; background:{{ $tc['solid'] }}; color:#fff; flex-shrink:0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">{!! $typeIcons[$questionType] ?? $typeIcons['MCQ'] !!}</svg>
                    </div>
                    <span style="font-size:13.5px; font-weight:700; color:#013C58; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $typeLabels[$questionType] ?? $questionType }}</span>
                </div>
            </div>
            <div class="show-meta-card" style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.4); border-inline-start:4px solid {{ $sc['dot'] }}; border-radius:16px; padding:16px 18px; box-shadow:0 0 26px rgba(14,106,150,0.32), 0 0 6px rgba(14,106,150,0.2), 0 6px 16px rgba(0,83,122,0.05);">
                <p style="margin:0 0 10px; font-size:12px; font-weight:800; letter-spacing:0.3px; text-transform:uppercase; color:#013C58;">مستوى الصعوبة</p>
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:9px; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }}; flex-shrink:0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 2.9 6 6.6.9-4.8 4.6 1.1 6.5-5.8-3-5.8 3 1.1-6.5-4.8-4.6 6.6-.9L12 2Z"></path></svg>
                    </div>
                    <span style="font-size:13.5px; font-weight:700; color:#013C58;">{{ $difficultyLabels[$question->difficulty] ?? $question->difficulty }}</span>
                </div>
            </div>
            <div class="show-meta-card" style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.4); border-inline-start:4px solid #F5A201; border-radius:16px; padding:16px 18px; box-shadow:0 0 26px rgba(14,106,150,0.32), 0 0 6px rgba(14,106,150,0.2), 0 6px 16px rgba(0,83,122,0.05);">
                <p style="margin:0 0 10px; font-size:12px; font-weight:800; letter-spacing:0.3px; text-transform:uppercase; color:#013C58;">النقاط</p>
                <div style="display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:10px; background:rgba(255,211,91,0.16); border:1px solid rgba(255,186,66,0.3);">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="#F5A201" stroke="none"><path d="M13 2 3 14h7l-1 8 11-14h-7l1-6Z"></path></svg>
                    <span style="font-family:'Poppins',sans-serif; font-weight:800; font-size:15px; color:#8A5A00;" dir="ltr">{{ $question->score }}</span>
                </div>
            </div>
            <div class="show-meta-card" style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.4); border-inline-start:4px solid rgba(1,60,88,0.3); border-radius:16px; padding:16px 18px; box-shadow:0 0 26px rgba(14,106,150,0.32), 0 0 6px rgba(14,106,150,0.2), 0 6px 16px rgba(0,83,122,0.05);">
                <p style="margin:0 0 10px; font-size:12px; font-weight:800; letter-spacing:0.3px; text-transform:uppercase; color:#013C58;">تاريخ الإنشاء</p>
                <p style="margin:0; font-size:13.5px; font-weight:700; color:#013C58;">{{ $question->created_at?->format('Y-m-d') }}</p>
            </div>
        </div>

        {{-- ============ LEFT (DOM-second): media — same slot/scale as the lesson video panel ============ --}}
        <div class="show-video-card" style="width:100%; max-width:520px; aspect-ratio:1/1; margin:0 auto; border-radius:18px; overflow:hidden; box-shadow:0 18px 40px rgba(0,83,122,0.08);">
            @if ($imageUrl)
                <img src="{{ $imageUrl }}" alt="" style="display:block; width:100%; height:100%; object-fit:fill;">
            @elseif ($audioUrl)
                <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:14px; width:100%; height:100%; background:linear-gradient(160deg,#013C58 0%, #00537A 60%, #0E6A96 130%); padding:20px;">
                    <div style="display:flex; align-items:center; justify-content:center; width:72px; height:72px; border-radius:50%; background:rgba(255,255,255,0.12); border:1px solid rgba(255,211,91,0.4); color:#FFD35B; flex-shrink:0;">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
                    </div>
                    <audio controls src="{{ $audioUrl }}" style="width:100%; max-width:280px;"></audio>
                </div>
            @else
                <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:14px; width:100%; height:100%; background:linear-gradient(160deg,#013C58 0%, #00537A 60%, #0E6A96 130%);">
                    <div style="display:flex; align-items:center; justify-content:center; width:72px; height:72px; border-radius:50%; background:rgba(255,255,255,0.12); color:#FFD35B;">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="3"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><path d="m21 15-5-5L5 21"></path></svg>
                    </div>
                    <p style="margin:0; font-size:13px; font-weight:600; color:rgba(255,255,255,0.75); text-align:center; padding:0 14px;">ما تم رفع صورة أو صوت لهذا السؤال</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ============ BELOW: answer options (moved down, full width, matches the lesson "words" panel) ============ --}}
    <div class="show-panel" style="background:linear-gradient(160deg, rgba(168,232,249,0.16), rgba(255,211,91,0.08)); border:1.5px solid rgba(14,106,150,0.35); border-radius:20px; padding:22px 24px; box-shadow:0 0 26px rgba(14,106,150,0.28), 0 0 6px rgba(14,106,150,0.18), 0 10px 26px rgba(0,83,122,0.06); margin-bottom:20px;">

        @if ($questionType === 'FILL')
            <div style="position:relative; background:#fff; border:1px solid rgba(0,83,122,0.1); border-radius:16px; padding:26px 28px; font-size:16px; color:#013C58; line-height:2.5; margin-bottom:24px; overflow:hidden;">
                <svg width="46" height="46" viewBox="0 0 24 24" fill="{{ $tc['solid'] }}" style="position:absolute; top:6px; inset-inline-start:10px; opacity:0.1;"><path d="M9.5 8.5C6.5 8.5 4 11 4 14s2.5 5.5 5.5 5.5c.3 0 .5-.2.5-.5s-.2-.5-.5-.5C7 18.5 5 16.5 5 14c0-1.6.9-3 2.2-3.8-.1.3-.2.5-.2.8 0 1.4 1.1 2.5 2.5 2.5s2.5-1.1 2.5-2.5S10.9 8.5 9.5 8.5zm10 0c-3 0-5.5 2.5-5.5 5.5s2.5 5.5 5.5 5.5c.3 0 .5-.2.5-.5s-.2-.5-.5-.5c-2.5 0-4.5-2-4.5-4.5 0-1.6.9-3 2.2-3.8-.1.3-.2.5-.2.8 0 1.4 1.1 2.5 2.5 2.5s2.5-1.1 2.5-2.5-1.1-2.5-2.5-2.5z"></path></svg>
                <span style="position:relative;">{!! $filledSentence !!}</span>
            </div>
        @endif

        <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
            <div style="width:28px; height:28px; border-radius:9px; background:{{ $tc['bg'] }}; color:{{ $tc['fg'] }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">{!! $typeIcons[$questionType] ?? $typeIcons['MCQ'] !!}</svg>
            </div>
            <p style="margin:0; font-size:13px; font-weight:800; letter-spacing:0.2px; color:#013C58;">
                @switch($questionType)
                    @case('MCQ') خيارات الإجابة @break
                    @case('FILL') الإجابات حسب ترتيب الفراغ @break
                    @case('ARRANGE') الترتيب الصحيح @break
                    @case('PAIR') أزواج التوصيل الصحيحة @break
                @endswitch
            </p>
        </div>

        @if ($questionType === 'MCQ')
            @php $letters = ['أ', 'ب', 'ج', 'د', 'هـ', 'و', 'ز', 'ح']; @endphp
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(230px, 1fr)); gap:12px;">
                @foreach ($answers as $i => $a)
                    <div class="show-mcq-card" style="position:relative; display:flex; align-items:center; gap:11px; padding:14px 16px; border-radius:14px; background:{{ $a->is_correct ? 'linear-gradient(135deg, rgba(76,175,120,0.16), rgba(76,175,120,0.06))' : '#fff' }}; border:1.5px solid {{ $a->is_correct ? '#4CAF78' : 'rgba(0,83,122,0.1)' }};">
                        @if ($a->is_correct)
                            <span style="position:absolute; top:-9px; inset-inline-end:12px; display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:999px; background:#4CAF78; color:#fff; font-size:10px; font-weight:700; box-shadow:0 4px 8px rgba(76,175,120,0.35);">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                                صحيحة
                            </span>
                        @endif
                        <span style="display:flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:50%; flex-shrink:0; background:{{ $a->is_correct ? '#4CAF78' : $tc['bg'] }}; color:{{ $a->is_correct ? '#fff' : $tc['fg'] }}; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px;">{{ $letters[$i] ?? ($i + 1) }}</span>
                        <span style="font-size:13.5px; color:#013C58; font-weight:{{ $a->is_correct ? '700' : '600' }};">{{ $a->text_answer }}</span>
                    </div>
                @endforeach
            </div>
        @elseif ($questionType === 'FILL')
            <div style="display:flex; flex-wrap:wrap; gap:10px;">
                @foreach ($answers->sortBy('blank_order') as $a)
                    <div style="display:flex; align-items:center; gap:9px; padding:10px 16px; border-radius:999px; background:#fff; border:1.5px solid {{ $tc['bg'] }};">
                        <span style="display:flex; align-items:center; justify-content:center; width:24px; height:24px; border-radius:50%; background:{{ $tc['solid'] }}; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:11px; flex-shrink:0;">{{ $a->blank_order }}</span>
                        <span style="font-size:13.5px; color:#013C58; font-weight:700;">{{ $a->text_answer }}</span>
                    </div>
                @endforeach
            </div>
        @elseif ($questionType === 'ARRANGE')
            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach ($answers->sortBy('order') as $i => $a)
                    <div class="show-arrange-step" style="display:flex; align-items:center; gap:12px; margin-inline-start:{{ $i * 22 }}px;">
                        <div style="display:flex; align-items:center; gap:12px; padding:11px 16px; border-radius:12px; background:#fff; border:1.5px solid {{ $tc['bg'] }}; box-shadow:0 4px 12px rgba(1,60,88,0.05);">
                            <span style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:{{ $tc['solid'] }}; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px; flex-shrink:0;">{{ $a->order }}</span>
                            <span style="font-size:13.5px; color:#013C58; font-weight:700;">{{ $a->text_answer }}</span>
                        </div>
                        @if (!$loop->last)
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="{{ $tc['solid'] }}" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.5; flex-shrink:0;"><path d="m6 9 6 6 6-6"></path></svg>
                        @endif
                    </div>
                @endforeach
            </div>
        @elseif ($questionType === 'PAIR')
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                <div style="flex:1; display:flex; justify-content:center;">
                    <span style="display:inline-flex; padding:4px 18px; border-radius:999px; background:{{ $tc['bg'] }}; color:{{ $tc['fg'] }}; font-size:12px; font-weight:700;">عربي</span>
                </div>
                <span style="width:24px; flex-shrink:0;"></span>
                <div style="flex:1; display:flex; justify-content:center;">
                    <span style="display:inline-flex; padding:4px 18px; border-radius:999px; background:{{ $tc['bg'] }}; color:{{ $tc['fg'] }}; font-size:11px; font-weight:700; letter-spacing:0.5px; text-transform:uppercase; font-family:'Poppins',sans-serif;">English</span>
                </div>
            </div>
            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach ($answers as $a)
                    <div class="show-pair-pill" style="display:flex; align-items:center; gap:12px;">
                        <div style="flex:1; font-size:13.5px; color:#013C58; font-weight:700; text-align:center; background:#fff; border:1.5px solid {{ $tc['bg'] }}; border-radius:999px; padding:10px 14px;">{{ $a->left_text }}</div>
                        <span style="flex-shrink:0; width:30px; height:30px; border-radius:50%; background:{{ $tc['solid'] }}; color:#fff; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px {{ $tc['bg'] }};">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
                        </span>
                        <div style="flex:1; font-size:13.5px; color:#013C58; font-weight:700; text-align:center; background:#fff; border:1.5px solid {{ $tc['bg'] }}; border-radius:999px; padding:10px 14px;">{{ $a->right_text }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ============ BELOW: usage status + version history ============ --}}
    <div class="show-panel" style="background:linear-gradient(160deg, rgba(168,232,249,0.16), rgba(255,211,91,0.08)); border:1.5px solid rgba(14,106,150,0.35); border-radius:20px; padding:22px 24px; box-shadow:0 0 26px rgba(14,106,150,0.28), 0 0 6px rgba(14,106,150,0.18), 0 10px 26px rgba(0,83,122,0.06);">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
            <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:15px; color:#013C58;">حالة الاستخدام</h3>
        </div>
        <div style="height:2px; border-radius:999px; background:linear-gradient(90deg, #0E6A96, #F5A201); margin-bottom:14px; opacity:0.55;"></div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:14px;">
            @foreach ([
                ['label' => 'مستخدم بامتحان منشور', 'value' => $isPublished, 'fg' => '#2E7D55'],
                ['label' => 'مستخدم بامتحان مؤرشف', 'value' => $isArchived, 'fg' => '#8A5A00'],
                ['label' => 'مستخدم بامتحان مغلق', 'value' => $isClosed, 'fg' => '#C2591A'],
            ] as $row)
                <div class="show-status-row" style="aspect-ratio:1/1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px; padding:16px; border-radius:16px; background:rgba(255,255,255,0.45); border:1px solid rgba(0,83,122,0.08); text-align:center;">
                    <span style="display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:50%; background:{{ $row['value'] ? $row['fg'] : 'rgba(1,60,88,0.08)' }}; color:{{ $row['value'] ? '#fff' : 'rgba(1,60,88,0.3)' }}; flex-shrink:0;">
                        @if ($row['value'])
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                        @else
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="M6 6l12 12"></path></svg>
                        @endif
                    </span>
                    <span style="color:rgba(1,60,88,0.65); font-weight:600; font-size:12px; line-height:1.5;">{{ $row['label'] }}</span>
                </div>
            @endforeach
        </div>

        @if ($isPublished || $isClosed)
            <p style="margin:16px 0 0; font-size:11.5px; color:#8A5A00; background:rgba(255,186,66,0.14); border-radius:10px; padding:10px 12px; line-height:1.6;">تعديل هالسؤال رح ينشئ نسخة جديدة منه تلقائيًا بدل التعديل المباشر، لأنه مرتبط بامتحان منشور أو مغلق.</p>
        @endif

        @if ($question->previous_question_id || $question->nextVersion)
            <div style="display:flex; gap:14px; flex-wrap:wrap; margin-top:16px; padding-top:16px; border-top:1px solid rgba(0,83,122,0.1);">
                @if ($question->previous_question_id)
                    <a href="{{ route('questions.show', $question->previous_question_id) }}" style="font-size:12.5px; color:#00537A; font-weight:700; text-decoration:none;">← عرض النسخة السابقة (#{{ $question->previous_question_id }})</a>
                @endif
                @if ($question->nextVersion)
                    <a href="{{ route('questions.show', $question->nextVersion->id) }}" style="font-size:12.5px; color:#00537A; font-weight:700; text-decoration:none;">عرض النسخة الأحدث (#{{ $question->nextVersion->id }}) →</a>
                @endif
            </div>
        @endif
    </div>

    {{-- ============ BOTTOM ACTION BAR ============ --}}
    <div style="height:76px;"></div>
    <template x-teleport="body">
        <div class="show-actionbar" dir="rtl" style="position:fixed; bottom:20px; right:24px; z-index:9999; display:flex; align-items:center; justify-content:space-between; gap:14px; background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:18px; padding:14px 18px; box-shadow:0 20px 44px rgba(1,60,88,0.22); font-family:'Tajawal',sans-serif;">
            <a href="{{ route('questions.placement.index') }}" class="show-back-btn" style="display:inline-flex; align-items:center; gap:7px; padding:11px 18px; border-radius:11px; color:#013C58; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; text-decoration:none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
                رجوع لبنك الأسئلة
            </a>
            <div style="display:flex; align-items:center; gap:10px;">
                <form action="{{ route('questions.delete', $question) }}" method="POST" onsubmit="return confirm('حذف هالسؤال؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" @disabled(!$canDelete) title="{{ $canDelete ? 'حذف' : 'ما فيك تحذفي سؤال مستخدم بامتحان منشور أو مغلق' }}" class="show-danger-btn" style="display:inline-flex; align-items:center; gap:7px; padding:11px 18px; border-radius:11px; border:1.5px solid rgba(229,72,77,0.3); background:transparent; color:#C2591A; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:{{ $canDelete ? 'pointer' : 'not-allowed' }}; opacity:{{ $canDelete ? 1 : 0.4 }};">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                        حذف
                    </button>
                </form>
                <a href="{{ route('questions.edit', $question) }}" class="show-archive-btn" style="display:inline-flex; align-items:center; gap:8px; padding:11px 22px; border-radius:11px; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; text-decoration:none;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                    تعديل السؤال
                </a>
            </div>
        </div>
    </template>
</div>
@endsection
