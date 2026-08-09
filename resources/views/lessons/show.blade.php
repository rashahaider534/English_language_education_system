@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .show-video-card, .show-meta-card, .show-panel, .show-actionbar { animation: lessonsFadeUp 0.4s ease both; }
    .show-meta-card:nth-child(1) { animation-delay: 0.05s; }
    .show-meta-card:nth-child(2) { animation-delay: 0.09s; }
    .show-meta-card:nth-child(3) { animation-delay: 0.13s; }
    .show-meta-card:nth-child(4) { animation-delay: 0.17s; }
    .show-panel { animation-delay: 0.1s; }
    .show-actionbar { animation-delay: 0.18s; }

    .show-meta-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .show-meta-card:hover { transform: translateY(-3px); box-shadow: 0 10px 22px rgba(1,60,88,0.1); }

    .show-archive-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .show-archive-btn:hover:not(:disabled) { transform: translateY(-2px) scale(1.03); box-shadow: 0 8px 16px rgba(1,60,88,0.18); }

    .show-back-btn { transition: background 0.15s ease, transform 0.15s ease; }
    .show-back-btn:hover { background: rgba(0,83,122,0.1); transform: translateY(-1px); }

    .show-comment { transition: background 0.15s ease, transform 0.15s ease; }
    .show-comment:hover { background: rgba(255,211,91,0.1); transform: translateX(-2px); }

    .show-comment-delete { transition: transform 0.15s ease, background 0.15s ease; }
    .show-comment-delete:hover { transform: scale(1.08); background: rgba(255,100,100,0.16); }

    .show-comment-block { transition: transform 0.15s ease, background 0.15s ease; }
    .show-comment-block:hover:not(:disabled) { transform: scale(1.08); background: rgba(1,60,88,0.16); }

    .show-word-audio { transition: transform 0.15s ease, background 0.15s ease; }
    .show-word-audio:hover { transform: scale(1.1); }
</style>
@endpush

@section('content')
@php
    $lessonModel = $lesson['lesson'];
    $comments = $lesson['comments'];
    $course = $lessonModel->course;
    $level = $course->level ?? null;
    $teacher = $course->teacher ?? null;
    $teacherName = $teacher ? (trim(($teacher->first_name ?? '').' '.($teacher->last_name ?? '')) ?: $teacher->email) : '—';
    $teacherInitial = $teacher ? strtoupper(substr($teacher->first_name ?? $teacher->email, 0, 1)) : '?';

    $statusLabels = [
        'pending' => 'قيد الانتظار', 'in_review' => 'قيد المراجعة', 'changes_requested' => 'طلب تعديل',
        'approved' => 'معتمَد', 'published' => 'منشور', 'closed' => 'مغلق', 'archived' => 'مؤرشف',
    ];
    // Same soft palette used on the per-course lessons table
    $statusColors = [
        'pending'         => ['bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00', 'dot' => '#F5A201'],
        'in_review'       => ['bg' => 'rgba(14,106,150,0.14)', 'fg' => '#0E6A96', 'dot' => '#0E6A96'],
        'changes_requested' => ['bg' => 'rgba(255,138,101,0.13)', 'fg' => '#C2591A', 'dot' => '#FF8A65'],
        'approved'        => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55', 'dot' => '#4CAF78'],
        'published'       => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55', 'dot' => '#4CAF78'],
        'closed'          => ['bg' => 'rgba(1,60,88,0.07)', 'fg' => 'rgba(1,60,88,0.55)', 'dot' => '#013C58'],
        'archived'        => ['bg' => 'rgba(1,60,88,0.05)', 'fg' => 'rgba(1,60,88,0.4)', 'dot' => 'rgba(1,60,88,0.65)'],
    ];
    $lessonStatusVal = $lessonModel->status instanceof \BackedEnum ? $lessonModel->status->value : $lessonModel->status;
    $sc = $statusColors[$lessonStatusVal] ?? $statusColors['pending'];
    $canArchive = $lessonStatusVal === 'published';
    $hasStudents = $lessonModel->users()->exists();
    $videoUrl = $lessonModel->getFirstMediaUrl('videos');
@endphp
<div
    x-data="{ archiveModalOpen: false, blockModalOpen: false, blockTarget: null, deleteModalOpen: false, deleteTarget: null, deleteError: null, deleting: false }"
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
        <a href="{{ url()->previous() }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            رجوع
        </a>
    </div>

    {{-- ============ TITLE + STATUS BAR ============ --}}
    <div style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.4); border-radius:20px; padding:20px 24px; box-shadow:0 0 26px rgba(14,106,150,0.32), 0 0 6px rgba(14,106,150,0.2), 0 6px 16px rgba(0,83,122,0.05); margin-bottom:20px; display:grid; grid-template-columns:1fr auto 1fr; align-items:center; gap:16px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; justify-self:start;">
            @if ($level)
                <span style="display:inline-block; padding:6px 12px; border-radius:999px; background:rgba(0,83,122,0.06); color:#00537A; font-size:11px; font-weight:700; letter-spacing:0.5px; text-transform:uppercase;">{{ $level->name_ar }}</span>
            @endif
            <span style="display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:999px; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }}; font-size:12px; font-weight:700;">
                <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:{{ $sc['dot'] }};"></span>{{ $statusLabels[$lessonStatusVal] ?? $lessonStatusVal }}
            </span>
        </div>

        <div style="text-align:center;">
            <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#013C58;">{{ $lessonModel->title_en }}</h1>
            <p style="margin:4px 0 0; font-size:15.5px; font-weight:600; color:rgba(1,60,88,0.55);">{{ $lessonModel->title_ar }}</p>
        </div>

        <div style="justify-self:end;">
            <div style="position:relative; display:flex; align-items:center; justify-content:center; width:42px; height:42px; border-radius:12px; background:rgba(0,83,122,0.07); border:1px solid rgba(0,83,122,0.12); color:#00537A; font-family:'Poppins',sans-serif; font-weight:700; flex-shrink:0;">
                <span style="font-size:12px;">{{ $lessonModel->order }}</span>
            </div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:260px 1fr; gap:20px; align-items:start; margin-bottom:20px;">

        {{-- ============ LEFT: slim meta sidebar ============ --}}
        <div style="display:flex; flex-direction:column; gap:14px;">
            <div class="show-meta-card" style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.4); border-radius:14px; padding:11px 14px; box-shadow:0 0 26px rgba(14,106,150,0.32), 0 0 6px rgba(14,106,150,0.2), 0 6px 16px rgba(0,83,122,0.05);">
                <p style="margin:0 0 6px; font-size:11.5px; font-weight:800; letter-spacing:0.3px; text-transform:uppercase; color:#013C58;">الأستاذ</p>
                <div style="display:flex; align-items:center; gap:8px;">
                    <div style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:#00537A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:10.5px; flex-shrink:0;">{{ $teacherInitial }}</div>
                    <span style="font-size:12.5px; font-weight:700; color:#013C58; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $teacherName }}</span>
                </div>
            </div>
            <div class="show-meta-card" style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.4); border-radius:14px; padding:11px 14px; box-shadow:0 0 26px rgba(14,106,150,0.32), 0 0 6px rgba(14,106,150,0.2), 0 6px 16px rgba(0,83,122,0.05);">
                <p style="margin:0 0 6px; font-size:11.5px; font-weight:800; letter-spacing:0.3px; text-transform:uppercase; color:#013C58;">الكورس</p>
                <div style="display:flex; align-items:center; gap:8px;">
                    <div style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:rgba(168,232,249,0.4); color:#00537A; flex-shrink:0;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    </div>
                    <span style="font-size:12.5px; font-weight:700; color:#013C58; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $course->name_ar }}</span>
                </div>
            </div>
            <div class="show-meta-card" style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.4); border-radius:14px; padding:11px 14px; box-shadow:0 0 26px rgba(14,106,150,0.32), 0 0 6px rgba(14,106,150,0.2), 0 6px 16px rgba(0,83,122,0.05);">
                <p style="margin:0 0 6px; font-size:11.5px; font-weight:800; letter-spacing:0.3px; text-transform:uppercase; color:#013C58;">نقاط الخبرة</p>
                <div style="display:inline-flex; align-items:center; gap:5px; padding:5px 11px; border-radius:9px; background:rgba(255,211,91,0.16); border:1px solid rgba(255,186,66,0.3);">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="#F5A201" stroke="none"><path d="M13 2 3 14h7l-1 8 11-14h-7l1-6Z"></path></svg>
                    <span style="font-family:'Poppins',sans-serif; font-weight:800; font-size:13px; color:#8A5A00;" dir="ltr">{{ $lessonModel->xp_points }}</span>
                    <span style="font-size:9.5px; font-weight:700; color:rgba(138,90,0,0.55); text-transform:uppercase;">XP</span>
                </div>
            </div>
            <div class="show-meta-card" style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.4); border-radius:14px; padding:11px 14px; box-shadow:0 0 26px rgba(14,106,150,0.32), 0 0 6px rgba(14,106,150,0.2), 0 6px 16px rgba(0,83,122,0.05);">
                <p style="margin:0 0 6px; font-size:11.5px; font-weight:800; letter-spacing:0.3px; text-transform:uppercase; color:#013C58;">تاريخ الإنشاء</p>
                <p style="margin:0; font-size:12.5px; font-weight:700; color:#013C58;">{{ $lessonModel->created_at->format('Y-m-d') }}</p>
            </div>
        </div>

        {{-- ============ RIGHT: Video (centered, wide) ============ --}}
        <div class="show-video-card" style="border:2px solid rgba(14,106,150,0.35); border-radius:18px; overflow:hidden; box-shadow:0 18px 40px rgba(0,83,122,0.08); line-height:0;">
            @if ($videoUrl)
                <video controls preload="metadata" style="width:100%; max-height:520px; background:#012A3F; display:block;">
                    <source src="{{ $videoUrl }}">
                </video>
            @else
                <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; height:360px; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%);">
                    <div style="display:flex; align-items:center; justify-content:center; width:56px; height:56px; border-radius:50%; background:rgba(255,255,255,0.12); color:#FFD35B;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M23 7 16 12l7 5V7Z"></path><rect x="1" y="5" width="15" height="14" rx="2"></rect></svg>
                    </div>
                    <p style="margin:0; font-size:13px; font-weight:600; color:rgba(255,255,255,0.75);">ما تم رفع فيديو لهذا الدرس بعد</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ============ BELOW: Lesson words (read-only) ============ --}}
    @php $words = $lesson['words']; @endphp
    <div class="show-panel" style="background:linear-gradient(160deg, rgba(168,232,249,0.16), rgba(255,211,91,0.08)); border:1.5px solid rgba(14,106,150,0.35); border-radius:20px; padding:22px 24px; box-shadow:0 0 26px rgba(14,106,150,0.28), 0 0 6px rgba(14,106,150,0.18), 0 10px 26px rgba(0,83,122,0.06); margin-bottom:20px;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
            <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:18px; color:#013C58;">كلمات الدرس</h3>
            <span style="display:inline-flex; align-items:center; justify-content:center; min-width:26px; height:26px; padding:0 6px; border-radius:8px; background:rgba(0,83,122,0.07); color:#00537A; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px;">{{ $words->count() }}</span>
        </div>

        @if ($words->isEmpty())
            <p style="margin:0; font-size:12.5px; color:rgba(1,60,88,0.45); font-weight:600;"> لم يتم اضافة كلمات لهذا الدرس بعد  </p>
        @else
            <div style="display:flex; flex-wrap:wrap; gap:10px;">
                @foreach ($words as $word)
                    @php $audioUrl = $word->getFirstMediaUrl('audios'); @endphp
                    <div @if($audioUrl) x-data="{ playing: false }" @endif style="display:flex; align-items:center; gap:8px; padding:8px 14px; border-radius:11px; background:rgba(255,211,91,0.08); border:1px solid rgba(255,186,66,0.32);">
                        <span style="font-size:12.5px; font-weight:700; color:#013C58;">{{ $word->word_en }}</span>
                        <span style="width:1px; height:12px; background:rgba(138,90,0,0.2);"></span>
                        <span style="font-size:12.5px; font-weight:600; color:#8A5A00;">{{ $word->word_ar }}</span>
                        @if ($audioUrl)
                            <span style="width:1px; height:12px; background:rgba(138,90,0,0.2);"></span>
                            <button
                                type="button"
                                class="show-word-audio"
                                title="تشغيل النطق"
                                @click="if (playing) { $refs.audio.pause(); } else { $refs.audio.play(); }"
                                :style="playing ? 'background:#F5A201; color:#fff;' : 'background:rgba(245,162,1,0.16); color:#8A5A00;'"
                                style="display:flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:50%; border:none; cursor:pointer; flex-shrink:0;">
                                <svg x-show="!playing" width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg>
                                <svg x-show="playing" x-cloak width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M6 5h4v14H6zM14 5h4v14h-4z"></path></svg>
                            </button>
                            <audio x-ref="audio" preload="none" style="display:none" @play="playing = true" @pause="playing = false" @ended="playing = false">
                                <source src="{{ $audioUrl }}">
                            </audio>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ============ BELOW: Comments panel (full width) ============ --}}
    <div class="show-panel" style="background:linear-gradient(160deg, rgba(168,232,249,0.16), rgba(255,211,91,0.08)); border:1.5px solid rgba(14,106,150,0.35); border-radius:20px; padding:22px 24px; box-shadow:0 0 26px rgba(14,106,150,0.28), 0 0 6px rgba(14,106,150,0.18), 0 10px 26px rgba(0,83,122,0.06);">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:16px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:15px; color:#013C58;">التعليقات</h3>
            </div>
            <span style="display:inline-flex; align-items:center; justify-content:center; min-width:26px; height:26px; padding:0 6px; border-radius:8px; background:rgba(0,83,122,0.07); color:#00537A; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px;">{{ $comments->total() }}</span>
        </div>
        <div style="height:2px; border-radius:999px; background:linear-gradient(90deg, #0E6A96, #F5A201); margin-bottom:18px; opacity:0.55;"></div>

        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:14px;">
            @forelse ($comments as $comment)
                @php
                    $author = $comment->user;
                    $authorName = $author ? (trim(($author->first_name ?? '').' '.($author->last_name ?? '')) ?: $author->email) : 'مستخدم محذوف';
                    $authorInitial = $author ? strtoupper(substr($author->first_name ?? $author->email, 0, 1)) : '?';
                    $authorBlocked = $author && $author->studentProfile && ! $author->studentProfile->can_comment;
                @endphp
                <div id="comment-{{ $comment->id }}" class="show-comment" style="padding:14px; border-radius:13px; background:rgba(255,211,91,0.05); border:1.5px solid rgba(255,186,66,0.2); box-shadow:0 0 10px rgba(255,186,66,0.08);">
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:9px;">
                        <span style="display:inline-flex; align-items:center; gap:6px; padding:3px 10px; border-radius:999px; background:rgba(0,83,122,0.06); font-size:10.5px; font-weight:700; color:rgba(1,60,88,0.55); font-family:'Poppins',sans-serif;">
                            {{ $comment->created_at->format('H:i') }} · {{ $comment->created_at->format('Y-m-d') }}
                        </span>
                        <div style="display:flex; align-items:center; gap:6px;">
                            @if ($author)
                                <button
                                    type="button"
                                    title="{{ $authorBlocked ? 'الطالب محظور من التعليق' : 'حظر الطالب من التعليق' }}"
                                    class="show-comment-block"
                                    :disabled="{{ $authorBlocked ? 'true' : 'false' }}"
                                    @click="blockTarget = { id: {{ $comment->id }}, name: {{ \Illuminate\Support\Js::from($authorName) }} }; blockModalOpen = true;"
                                    style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:7px; border:none; background:{{ $authorBlocked ? 'rgba(1,60,88,0.04)' : 'rgba(1,60,88,0.08)' }}; color:{{ $authorBlocked ? 'rgba(1,60,88,0.3)' : 'rgba(1,60,88,0.75)' }}; cursor:{{ $authorBlocked ? 'not-allowed' : 'pointer' }};">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="m4.9 4.9 14.2 14.2"></path></svg>
                                </button>
                            @endif
                            <button
                                type="button"
                                title="حذف التعليق"
                                class="show-comment-delete"
                                @click="deleteTarget = { id: {{ $comment->id }} }; deleteError = null; deleteModalOpen = true;"
                                style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:7px; border:none; background:rgba(255,100,100,0.08); color:rgba(200,60,60,0.75); cursor:pointer;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                    <p style="margin:0 0 9px; font-size:13px; color:rgba(1,60,88,0.75); line-height:1.6;">{{ $comment->comment }}</p>
                    <div style="display:flex; align-items:center; gap:7px;">
                        <div style="display:flex; align-items:center; justify-content:center; width:20px; height:20px; border-radius:6px; background:#00537A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:9.5px; flex-shrink:0;">{{ $authorInitial }}</div>
                        <span style="font-size:11px; font-weight:600; color:rgba(1,60,88,0.5);">{{ $authorName }}</span>
                    </div>
                </div>
            @empty
                <div style="grid-column:1/-1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; padding:40px 20px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="color:rgba(1,60,88,0.25);"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    <p style="margin:0; font-size:13px; color:rgba(1,60,88,0.45); font-weight:600;">ما في تعليقات حتى الآن</p>
                </div>
            @endforelse
        </div>

        @if ($comments instanceof \Illuminate\Pagination\LengthAwarePaginator && $comments->hasPages())
            <div style="margin-top:16px;">{{ $comments->links('vendor.pagination.lessons') }}</div>
        @endif
    </div>

    {{-- ============ BOTTOM ACTION BAR ============ --}}
    <div style="height:76px;"></div>
    <template x-teleport="body">
        <div class="show-actionbar" dir="rtl" style="position:fixed; bottom:20px; right:24px; z-index:9999; display:flex; align-items:center; justify-content:space-between; gap:14px; background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:18px; padding:14px 18px; box-shadow:0 20px 44px rgba(1,60,88,0.22); font-family:'Tajawal',sans-serif;">
            <a href="{{ url()->previous() }}" class="show-back-btn" style="display:inline-flex; align-items:center; gap:7px; padding:11px 18px; border-radius:11px; color:#013C58; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; text-decoration:none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
                رجوع
            </a>
            <button type="button"
                class="show-archive-btn"
                @if($canArchive) @click="archiveModalOpen = true" @else disabled @endif
                title="{{ !$canArchive ? 'الأرشفة متاحة بس للدروس المنشورة' : ($hasStudents ? 'أرشفة (رح يصير مغلق)' : 'أرشفة') }}"
                style="display:inline-flex; align-items:center; gap:8px; padding:11px 22px; border-radius:11px; border:none; cursor:{{ $canArchive ? 'pointer' : 'not-allowed' }}; opacity:{{ $canArchive ? 1 : 0.4 }}; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l1.5-3h15L21 7"></path><path d="M4.5 7h15v12a1 1 0 0 1-1 1h-13a1 1 0 0 1-1-1V7Z"></path><path d="M9 12h6"></path></svg>
                أرشفة الدرس
            </button>
        </div>
    </template>

    {{-- ============ ARCHIVE CONFIRM MODAL ============ --}}
    <div x-show="archiveModalOpen" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="position:fixed; inset:0; z-index:50; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px); overflow-y:auto;"
         @click="archiveModalOpen = false">
      <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
        <div @click.stop
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             style="width:100%; max-width:400px; background:#EFFAFD; border-radius:22px; padding:30px 26px; box-shadow:0 44px 100px rgba(1,42,63,0.4); text-align:center;">
            <div style="width:58px; height:58px; border-radius:16px; background:rgba(230,126,34,0.14); color:#C1650A; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l1.5-3h15L21 7"></path><path d="M4.5 7h15v12a1 1 0 0 1-1 1h-13a1 1 0 0 1-1-1V7Z"></path><path d="M9 12h6"></path></svg>
            </div>
            <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:17px; color:#013C58;">أرشفة درس "{{ $lessonModel->title_ar }}"؟</h3>
            <p style="margin:10px 0 0; font-size:13px; color:rgba(1,60,88,0.6); line-height:1.7;">هيصير الدرس مؤرشف أو مغلق (إذا في طلاب مسجلين فيه)، ومش رح يظهر للطلاب الجدد.</p>
            <div style="display:flex; gap:10px; margin-top:22px;">
                <button type="button" @click="archiveModalOpen = false" style="flex:1; padding:11px; border-radius:11px; border:1.5px solid rgba(0,83,122,0.12); background:#EFFAFD; color:#013C58; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer;">إلغاء</button>
                <form action="/lessons/{{ $lessonModel->id }}/archive" method="POST" style="flex:1;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" style="width:100%; padding:11px; border-radius:11px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">تأكيد</button>
                </form>
            </div>
        </div>
      </div>
    </div>

    {{-- ============ BLOCK STUDENT CONFIRM MODAL ============ --}}
    <div x-show="blockModalOpen" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="position:fixed; inset:0; z-index:50; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px); overflow-y:auto;"
         @click="blockModalOpen = false">
      <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
        <div @click.stop
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             style="width:100%; max-width:400px; background:#EFFAFD; border-radius:22px; padding:30px 26px; box-shadow:0 44px 100px rgba(1,42,63,0.4); text-align:center;">
            <div style="width:58px; height:58px; border-radius:16px; background:rgba(200,60,60,0.14); color:#B23A3A; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="m4.9 4.9 14.2 14.2"></path></svg>
            </div>
            <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:17px; color:#013C58;">حظر <span x-text="blockTarget?.name"></span> من التعليق؟</h3>
            <p style="margin:10px 0 0; font-size:13px; color:rgba(1,60,88,0.6); line-height:1.7;">سيتم حظر الطالب من التعليقات </p>
            <div style="display:flex; gap:10px; margin-top:22px;">
                <button type="button" @click="blockModalOpen = false" style="flex:1; padding:11px; border-radius:11px; border:1.5px solid rgba(0,83,122,0.12); background:#EFFAFD; color:#013C58; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer;">إلغاء</button>
                <form :action="'/comments/' + blockTarget?.id + '/block'" method="POST" style="flex:1;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" style="width:100%; padding:11px; border-radius:11px; border:none; background:linear-gradient(90deg,#C1392B,#E05C4E); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">تأكيد الحظر</button>
                </form>
            </div>
        </div>
      </div>
    </div>

    {{-- ============ DELETE COMMENT CONFIRM MODAL ============ --}}
    <div x-show="deleteModalOpen" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="position:fixed; inset:0; z-index:50; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px); overflow-y:auto;"
         @click="deleteModalOpen = false">
      <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
        <div @click.stop
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             style="width:100%; max-width:400px; background:#EFFAFD; border-radius:22px; padding:30px 26px; box-shadow:0 44px 100px rgba(1,42,63,0.4); text-align:center;">
            <div style="width:58px; height:58px; border-radius:16px; background:rgba(200,60,60,0.14); color:#B23A3A; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
            </div>
            <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:17px; color:#013C58;">حذف هالتعليق؟</h3>

            <template x-if="deleteError">
                <p style="margin:10px 0 0; font-size:12.5px; color:#B23A3A; background:rgba(200,60,60,0.08); border-radius:10px; padding:10px 12px; line-height:1.7;" x-text="deleteError"></p>
            </template>

            <div style="display:flex; gap:10px; margin-top:22px;">
                <button type="button" @click="deleteModalOpen = false" style="flex:1; padding:11px; border-radius:11px; border:1.5px solid rgba(0,83,122,0.12); background:#EFFAFD; color:#013C58; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer;">إلغاء</button>
                <button
                    type="button"
                    :disabled="deleting"
                    @click="
                        deleting = true; deleteError = null;
                        fetch('/comments/' + deleteTarget.id + '/destroy', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                        }).then(res => {
                            deleting = false;
                            if (res.ok) {
                                deleteModalOpen = false;
                                const el = document.getElementById('comment-' + deleteTarget.id);
                                if (el) el.remove();
                            } else if (res.status === 403) {
                                deleteError = 'لاتملك صلاحية حذف هذا التعليق.';
                            } else {
                                deleteError = 'يرجى اعادة المحاولة .';
                            }
                        }).catch(() => { deleting = false; deleteError = 'صار في خطأ بالاتصال، جربي كمان مرة.'; });
                    "
                    style="flex:1; padding:11px; border-radius:11px; border:none; background:linear-gradient(90deg,#C1392B,#E05C4E); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer; opacity:1;">
                    <span x-text="deleting ? 'جاري الحذف...' : 'تأكيد الحذف'"></span>
                </button>
            </div>
        </div>
      </div>
    </div>
</div>
@endsection
