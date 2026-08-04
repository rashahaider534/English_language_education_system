@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .q-panel { animation: lessonsFadeUp 0.4s ease both; }
    .q-panel:nth-child(1) { animation-delay: 0.02s; }
    .q-panel:nth-child(2) { animation-delay: 0.06s; }

    .q-answer-row { transition: background 0.15s ease; }
    .q-answer-row:hover { background: rgba(168,232,249,0.08); }

    .q-action-btn { transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease; }
    .q-action-btn:not(:disabled):hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(1,60,88,0.16); }
</style>
@endpush

@section('content')
@php
    $question = $questions; // controller passes the single Question model under this key
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
    $tc = $typeColors[$question->type] ?? $typeColors['MCQ'];
    $dc = $difficultyColors[$question->difficulty] ?? $difficultyColors['EASY'];

    $isPublished = $question->isUsedInPublishedTests();
    $isArchived = $question->isUsedInArchivedTests();
    $isClosed = $question->isUsedInClosedTests();
    $canDelete = !$isPublished && !$isClosed;

    $relation = $question->getAnswersRelationName();
    $answers = $question->{$relation};

    $imageUrl = $question->getFirstMediaUrl('image');
    $audioUrl = $question->getFirstMediaUrl('audio');
@endphp
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

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

    {{-- ============ HERO ============ --}}
    <div style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.2) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
            <div>
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                    <span style="display:inline-flex; padding:5px 12px; border-radius:999px; background:rgba(255,255,255,0.12); color:#FFD35B; font-size:11px; font-weight:700; border:1px solid rgba(255,255,255,0.18);">#{{ $question->id }}</span>
                    <span style="display:inline-flex; padding:5px 12px; border-radius:999px; background:{{ $tc['bg'] }}; color:{{ $tc['fg'] }}; font-size:11px; font-weight:700;">{{ $typeLabels[$question->type] ?? $question->type }}</span>
                    <span style="display:inline-flex; padding:5px 12px; border-radius:999px; background:{{ $dc['bg'] }}; color:{{ $dc['fg'] }}; font-size:11px; font-weight:700;">{{ $difficultyLabels[$question->difficulty] ?? $question->difficulty }}</span>
                </div>
                <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:21px; color:#fff;">{{ $question->title_question_en }}</h1>
                <p style="margin:6px 0 0; font-size:14px; color:rgba(168,232,249,0.85);">{{ $question->title_question_ar }}</p>
            </div>
            <div style="display:flex; align-items:center; gap:14px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.16); border-radius:16px; padding:14px 22px;">
                <div>
                    <p style="margin:0; font-size:10px; font-weight:700; letter-spacing:0.6px; text-transform:uppercase; color:rgba(168,232,249,0.7);">النقاط</p>
                    <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff; text-align:center;">{{ $question->score }}</p>
                </div>
            </div>
        </div>

        <div style="position:relative; display:flex; gap:10px; margin-top:20px;">
            <a href="{{ route('questions.edit', $question) }}" class="q-action-btn" style="display:inline-flex; align-items:center; gap:8px; padding:11px 20px; border-radius:12px; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; text-decoration:none; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                تعديل السؤال
            </a>
            <form action="{{ route('questions.delete', $question) }}" method="POST" onsubmit="return confirm('حذف هالسؤال؟');">
                @csrf
                @method('DELETE')
                <button type="submit" @disabled(!$canDelete) title="{{ $canDelete ? 'حذف' : 'ما فيك تحذفي سؤال مستخدم بامتحان منشور أو مغلق' }}" class="q-action-btn" style="display:inline-flex; align-items:center; gap:8px; padding:11px 20px; border-radius:12px; border:1px solid rgba(255,255,255,0.2); background:rgba(229,72,77,0.18); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:{{ $canDelete ? 'pointer' : 'not-allowed' }}; opacity:{{ $canDelete ? 1 : 0.45 }};">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                    حذف
                </button>
            </form>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 320px; gap:24px; align-items:start;">
        {{-- ============ LEFT: ANSWERS ============ --}}
        <div style="display:flex; flex-direction:column; gap:22px;">

            @if ($question->type === 'FILL')
                <div class="q-panel" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:26px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
                    <h3 style="margin:0 0 14px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">نص السؤال</h3>
                    <div style="background:rgba(0,83,122,0.04); border:1px solid rgba(0,83,122,0.1); border-radius:12px; padding:16px 18px; font-size:15px; color:#013C58; line-height:1.9; margin-bottom:18px;">{{ $question->text_question }}</div>
                </div>
            @endif

            <div class="q-panel" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:26px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
                <h3 style="margin:0 0 16px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">الإجابات</h3>

                @if ($question->type === 'MCQ')
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @foreach ($answers as $a)
                            <div class="q-answer-row" style="display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:11px; background:{{ $a->is_correct ? 'rgba(76,175,120,0.1)' : 'rgba(0,83,122,0.03)' }}; border:1px solid {{ $a->is_correct ? 'rgba(76,175,120,0.3)' : 'transparent' }};">
                                <div style="width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; background:{{ $a->is_correct ? '#4CAF78' : 'rgba(0,83,122,0.1)' }}; color:{{ $a->is_correct ? '#fff' : 'rgba(1,60,88,0.4)' }};">
                                    @if ($a->is_correct)
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                                    @endif
                                </div>
                                <span style="font-size:14px; color:#013C58; font-weight:{{ $a->is_correct ? '700' : '500' }};">{{ $a->text_answer }}</span>
                                @if ($a->is_correct)
                                    <span style="margin-inline-start:auto; font-size:11px; font-weight:700; color:#2E7D55;">الإجابة الصحيحة</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @elseif ($question->type === 'FILL')
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @foreach ($answers->sortBy('blank_order') as $a)
                            <div class="q-answer-row" style="display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:11px; background:rgba(0,83,122,0.03);">
                                <span style="display:flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:8px; background:rgba(0,83,122,0.08); color:#00537A; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px; flex-shrink:0;">{{ '{'.$a->blank_order.'}' }}</span>
                                <span style="font-size:14px; color:#013C58; font-weight:700;">{{ $a->text_answer }}</span>
                            </div>
                        @endforeach
                    </div>
                @elseif ($question->type === 'ARRANGE')
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @foreach ($answers->sortBy('order') as $a)
                            <div class="q-answer-row" style="display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:11px; background:rgba(0,83,122,0.03);">
                                <span style="display:flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; background:#00537A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px; flex-shrink:0;">{{ $a->order }}</span>
                                <span style="font-size:14px; color:#013C58; font-weight:700;">{{ $a->text_answer }}</span>
                            </div>
                        @endforeach
                    </div>
                @elseif ($question->type === 'PAIR')
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @foreach ($answers as $a)
                            <div class="q-answer-row" style="display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:11px; background:rgba(0,83,122,0.03);">
                                <div style="flex:1; font-size:14px; color:#013C58; font-weight:700; text-align:center;">{{ $a->left_text }}</div>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(1,60,88,0.35)" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg>
                                <div style="flex:1; font-size:14px; color:#013C58; font-weight:700; text-align:center;">{{ $a->right_text }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($imageUrl || $audioUrl)
                <div class="q-panel" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:26px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
                    <h3 style="margin:0 0 16px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">الوسائط المرفقة</h3>
                    @if ($imageUrl)
                        <img src="{{ $imageUrl }}" alt="" style="max-width:100%; border-radius:14px; border:1px solid rgba(0,83,122,0.12);">
                    @endif
                    @if ($audioUrl)
                        <audio controls src="{{ $audioUrl }}" style="width:100%;"></audio>
                    @endif
                </div>
            @endif
        </div>

        {{-- ============ RIGHT: META ============ --}}
        <div style="display:flex; flex-direction:column; gap:20px;">
            <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:18px; padding:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
                <h3 style="margin:0 0 14px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">حالة الاستخدام</h3>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; font-size:12.5px;">
                        <span style="color:rgba(1,60,88,0.55); font-weight:600;">مستخدم بامتحان منشور</span>
                        <span style="font-weight:700; color:{{ $isPublished ? '#2E7D55' : 'rgba(1,60,88,0.4)' }};">{{ $isPublished ? 'نعم' : 'لا' }}</span>
                    </div>
                    <div style="display:flex; align-items:center; justify-content:space-between; font-size:12.5px;">
                        <span style="color:rgba(1,60,88,0.55); font-weight:600;">مستخدم بامتحان مؤرشف</span>
                        <span style="font-weight:700; color:{{ $isArchived ? '#8A5A00' : 'rgba(1,60,88,0.4)' }};">{{ $isArchived ? 'نعم' : 'لا' }}</span>
                    </div>
                    <div style="display:flex; align-items:center; justify-content:space-between; font-size:12.5px;">
                        <span style="color:rgba(1,60,88,0.55); font-weight:600;">مستخدم بامتحان مغلق</span>
                        <span style="font-weight:700; color:{{ $isClosed ? '#C2591A' : 'rgba(1,60,88,0.4)' }};">{{ $isClosed ? 'نعم' : 'لا' }}</span>
                    </div>
                </div>
                @if ($isPublished || $isClosed)
                    <p style="margin:14px 0 0; font-size:11.5px; color:#8A5A00; background:rgba(255,186,66,0.14); border-radius:10px; padding:10px 12px; line-height:1.6;">تعديل هالسؤال رح ينشئ نسخة جديدة منه تلقائيًا بدل التعديل المباشر، لأنه مرتبط بامتحان منشور أو مغلق.</p>
                @endif
            </div>

            @if ($question->previous_question_id || $question->nextVersion)
                <div style="background:rgba(0,83,122,0.04); border:1px solid rgba(0,83,122,0.1); border-radius:16px; padding:18px 20px;">
                    <h3 style="margin:0 0 12px; font-family:'Poppins',sans-serif; font-weight:800; font-size:13px; color:#013C58;">سجل النسخ</h3>
                    @if ($question->previous_question_id)
                        <a href="{{ route('questions.show', $question->previous_question_id) }}" style="display:block; font-size:12.5px; color:#00537A; font-weight:600; text-decoration:none; margin-bottom:6px;">← عرض النسخة السابقة (#{{ $question->previous_question_id }})</a>
                    @endif
                    @if ($question->nextVersion)
                        <a href="{{ route('questions.show', $question->nextVersion->id) }}" style="display:block; font-size:12.5px; color:#00537A; font-weight:600; text-decoration:none;">عرض النسخة الأحدث (#{{ $question->nextVersion->id }}) →</a>
                    @endif
                </div>
            @endif

            <div style="background:rgba(0,83,122,0.04); border:1px solid rgba(0,83,122,0.1); border-radius:16px; padding:18px 20px;">
                <div style="display:flex; align-items:center; justify-content:space-between; font-size:12.5px; margin-bottom:8px;">
                    <span style="color:rgba(1,60,88,0.6); font-weight:600;">سؤال تحديد مستوى</span>
                    <span style="font-weight:700; color:#013C58;">{{ $question->is_placement_question ? 'نعم' : 'لا' }}</span>
                </div>
                <div style="display:flex; align-items:center; justify-content:space-between; font-size:12.5px;">
                    <span style="color:rgba(1,60,88,0.6); font-weight:600;">تاريخ الإنشاء</span>
                    <span style="font-weight:700; color:#013C58;">{{ $question->created_at?->format('Y-m-d') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
