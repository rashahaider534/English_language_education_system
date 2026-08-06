@extends('dashboard.layouts.app')

@push('styles')
<style>
    .modal-scroll::-webkit-scrollbar { width: 8px; }
    .modal-scroll::-webkit-scrollbar-track { background: transparent; }
    .modal-scroll::-webkit-scrollbar-thumb { background: rgba(1,60,88,0.14); border-radius: 999px; }
    .modal-scroll::-webkit-scrollbar-thumb:hover { background: rgba(1,60,88,0.24); }
    .modal-scroll { scrollbar-width: thin; scrollbar-color: rgba(1,60,88,0.18) transparent; }

    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .lessons-hero, .lessons-stat, .lessons-tabs, .lessons-table-wrap { animation: lessonsFadeUp 0.45s ease both; }
    .lessons-stat:nth-child(1) { animation-delay: 0.02s; }
    .lessons-stat:nth-child(2) { animation-delay: 0.06s; }
    .lessons-stat:nth-child(3) { animation-delay: 0.1s; }
    .lessons-stat:nth-child(4) { animation-delay: 0.14s; }
    .lessons-stat:nth-child(5) { animation-delay: 0.18s; }
    .lessons-tabs { animation-delay: 0.08s; }
    .lessons-table-wrap { animation-delay: 0.12s; }

    .lessons-stat { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .lessons-stat:hover { transform: translateY(-3px); box-shadow: 0 10px 22px rgba(1,60,88,0.14); }

    .lesson-tab { transition: transform 0.15s ease, background 0.15s ease, color 0.15s ease; }
    .lesson-tab:hover { transform: translateY(-1px); }

    .lesson-row { transition: background 0.15s ease; }
    .lesson-row:hover { background: rgba(168,232,249,0.1); }

    .lesson-icon-btn { transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease; }
    .lesson-icon-btn:hover:not(:disabled) { transform: translateY(-2px) scale(1.05); box-shadow: 0 8px 16px rgba(1,60,88,0.16); }
    .lesson-icon-btn:active:not(:disabled) { transform: translateY(0) scale(0.97); }

    .lesson-star { transition: transform 0.15s ease; }
    .lesson-row:hover .lesson-star { transform: scale(1.08); }

    @keyframes lessonsShimmer { 0% { background-position: -300px 0; } 100% { background-position: 300px 0; } }
    .lesson-skeleton-bar { height: 12px; border-radius: 6px; background: linear-gradient(90deg, rgba(0,83,122,0.06) 25%, rgba(0,83,122,0.12) 37%, rgba(0,83,122,0.06) 63%); background-size: 600px 100%; animation: lessonsShimmer 1.3s ease-in-out infinite; }
</style>
@endpush

@section('content')
@php
    $statusLabels = [
        'pending' => 'قيد الانتظار', 'in_review' => 'قيد المراجعة', 'request_changes' => 'طلب تعديل',
        'published' => 'منشور', 'closed' => 'مغلق', 'archived' => 'مؤرشف',
    ];
    // Soft, muted palette — each status has its own distinct hue, always visible (not only on hover/active)
    $statusColors = [
        'pending'         => ['bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00', 'dot' => '#F5A201', 'tabFg' => '#013C58'],
        'in_review'       => ['bg' => 'rgba(14,106,150,0.14)', 'fg' => '#0E6A96', 'dot' => '#0E6A96', 'tabFg' => '#fff'],
        'request_changes' => ['bg' => 'rgba(255,138,101,0.13)', 'fg' => '#C2591A', 'dot' => '#FF8A65', 'tabFg' => '#fff'],
        'published'       => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55', 'dot' => '#4CAF78', 'tabFg' => '#fff'],
        'closed'          => ['bg' => 'rgba(1,60,88,0.12)', 'fg' => '#013C58', 'dot' => '#013C58', 'tabFg' => '#fff'],
        'archived'        => ['bg' => 'rgba(1,60,88,0.05)', 'fg' => 'rgba(1,60,88,0.4)', 'dot' => 'rgba(1,60,88,0.65)', 'tabFg' => '#fff'],
    ];
    $activeStatus = request()->route('status');
    $teacher = $course->teacher;
    $teacherName = $teacher ? (trim(($teacher->first_name ?? '').' '.($teacher->last_name ?? '')) ?: $teacher->email) : '—';
@endphp
<div
    x-data="{
        archiveModalOpen: false,
        archiveId: null,
        archiveName: '',
    }"
    class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8"
    style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl"
>
    @if (session('success'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(168,232,249,0.18); color:#00537A; border:1px solid rgba(0,83,122,0.14); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('lesson'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(255,138,101,0.14); color:#C2591A; border:1px solid rgba(255,138,101,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
            {{ $errors->first('lesson') }}
        </div>
    @endif

    {{-- ============ HERO HEAD ============ --}}
    <div class="lessons-hero" style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(168,232,249,0.25) 0%, rgba(168,232,249,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:center; gap:8px; margin-bottom:16px; font-size:12.5px; color:rgba(168,232,249,0.65); font-weight:600;">
            <a href="{{ route('courses.index', $course->level) }}" style="color:rgba(168,232,249,0.65); text-decoration:none;">الكورسات</a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.5;"><path d="m9 18 6-6-6-6"></path></svg>
            <span style="color:#fff;">{{ $course->name_ar }}</span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.5;"><path d="m9 18 6-6-6-6"></path></svg>
            <span style="color:#fff;">الدروس</span>
        </div>

        <div style="position:relative; display:flex; align-items:center; gap:16px; margin-bottom:22px;">
            <div style="position:relative; display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; font-family:'Poppins',sans-serif; font-weight:800; font-size:16px; flex-shrink:0;">
                <span style="position:absolute; inset:-4px; border-radius:19px; border:1px solid rgba(255,211,91,0.25);"></span>
                <span style="position:relative;">{{ strtoupper(substr($course->name_en, 0, 2)) }}</span>
            </div>
            <div>
                <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">دروس الكورس</p>
                <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">{{ $course->name_en }} <span style="font-weight:500; font-size:14px; color:rgba(168,232,249,0.7); margin-inline-start:8px;">{{ $course->name_ar }}</span></h1>
            </div>
        </div>

        <div style="position:relative; display:flex; gap:14px; flex-wrap:wrap;">
            @php
                $statCard = 'display:flex; align-items:center; gap:13px; background:linear-gradient(135deg, rgba(255,211,91,0.1), rgba(255,186,66,0.04)); border:1px solid rgba(255,211,91,0.22); border-radius:16px; padding:14px 18px; flex:1; min-width:150px;';
                $iconWrapBase = 'display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.14); flex-shrink:0;';
            @endphp
            <div class="lessons-stat" style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#FFD35B;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M3 10h18"></path><path d="M8 2v4M16 2v4"></path></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,236,176,0.85);">الكل</p>
                    <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $statistics->all_count ?? 0 }}</p>
                </div>
            </div>
            <div class="lessons-stat" style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#FFD35B;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,236,176,0.85);">قيد الانتظار</p>
                    <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $statistics->pending ?? 0 }}</p>
                </div>
            </div>
            <div class="lessons-stat" style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#FFD35B;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,236,176,0.85);">قيد المراجعة</p>
                    <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $statistics->in_review ?? 0 }}</p>
                </div>
            </div>
            <div class="lessons-stat" style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#FFD35B;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,236,176,0.85);">طلب تعديل</p>
                    <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $statistics->request_changes ?? 0 }}</p>
                </div>
            </div>
            <div class="lessons-stat" style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#FFD35B;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,236,176,0.85);">منشورة</p>
                    <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $statistics->published ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ FILTER TABS ============ --}}
    <div class="lessons-tabs" style="display:flex; gap:6px; padding:4px 0; flex-wrap:wrap; margin-bottom:22px; width:fit-content;">
        @php
            $tabBase = "border:none; padding:8px 14px; border-radius:9px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:600; white-space:nowrap; text-decoration:none; display:inline-block;";
            $tabs = [
                ['status' => null, 'label' => 'الكل', 'bg' => 'rgba(1,60,88,0.06)', 'fg' => 'rgba(1,60,88,0.6)', 'activeBg' => '#013C58', 'activeFg' => '#fff'],
                ['status' => 'pending', 'label' => 'قيد الانتظار', 'bg' => $statusColors['pending']['bg'], 'fg' => $statusColors['pending']['fg'], 'activeBg' => $statusColors['pending']['dot'], 'activeFg' => $statusColors['pending']['tabFg']],
                ['status' => 'in_review', 'label' => 'قيد المراجعة', 'bg' => $statusColors['in_review']['bg'], 'fg' => $statusColors['in_review']['fg'], 'activeBg' => $statusColors['in_review']['dot'], 'activeFg' => $statusColors['in_review']['tabFg']],
                ['status' => 'request_changes', 'label' => 'طلب تعديل', 'bg' => $statusColors['request_changes']['bg'], 'fg' => $statusColors['request_changes']['fg'], 'activeBg' => $statusColors['request_changes']['dot'], 'activeFg' => $statusColors['request_changes']['tabFg']],
                ['status' => 'published', 'label' => 'منشورة', 'bg' => $statusColors['published']['bg'], 'fg' => $statusColors['published']['fg'], 'activeBg' => $statusColors['published']['dot'], 'activeFg' => $statusColors['published']['tabFg']],
                ['status' => 'closed', 'label' => 'مغلقة', 'bg' => $statusColors['closed']['bg'], 'fg' => $statusColors['closed']['fg'], 'activeBg' => $statusColors['closed']['dot'], 'activeFg' => $statusColors['closed']['tabFg']],
                ['status' => 'archived', 'label' => 'مؤرشفة', 'bg' => $statusColors['archived']['bg'], 'fg' => $statusColors['archived']['fg'], 'activeBg' => $statusColors['archived']['dot'], 'activeFg' => $statusColors['archived']['tabFg']],
            ];
        @endphp
        @foreach ($tabs as $tab)
            @php $isActive = $activeStatus === $tab['status']; @endphp
            <a href="{{ $tab['status'] ? route('lessons.index', [$course, $tab['status']]) : route('lessons.index', $course) }}"
               class="lesson-tab"
               style="{{ $tabBase }} background:{{ $tab['bg'] }}; color:{{ $tab['fg'] }}; {{ $isActive ? 'box-shadow: inset 0 0 0 1.5px '.$tab['activeBg'].';' : '' }}">{{ $tab['label'] }}</a>
        @endforeach
    </div>

    {{-- ============ LESSONS TABLE ============ --}}
    <div class="lessons-table-wrap" x-data="{ ready: false }" x-init="setTimeout(() => ready = true, 220)" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; overflow:hidden; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
            <thead>
                <tr>
                    <th style="text-align:center; font-size:13.5px; font-weight:800; color:#013C58; text-transform:uppercase; letter-spacing:0.5px; padding:13px 12px; background:rgba(168,232,249,0.22); width:6%;">#</th>
                    <th style="text-align:right; font-size:13.5px; font-weight:800; color:#013C58; text-transform:uppercase; letter-spacing:0.5px; padding:13px 12px; background:rgba(168,232,249,0.22); width:27%;">اسم الدرس</th>
                    <th style="text-align:center; font-size:13.5px; font-weight:800; color:#013C58; text-transform:uppercase; letter-spacing:0.5px; padding:13px 12px; background:rgba(168,232,249,0.22); width:19%;">المعلم</th>
                    <th style="text-align:center; font-size:13.5px; font-weight:800; color:#013C58; text-transform:uppercase; letter-spacing:0.5px; padding:13px 12px; background:rgba(168,232,249,0.22); width:15%;">نقاط الخبرة</th>
                    <th style="text-align:center; font-size:13.5px; font-weight:800; color:#013C58; text-transform:uppercase; letter-spacing:0.5px; padding:13px 12px; background:rgba(168,232,249,0.22); width:15%;">الحالة</th>
                    <th style="text-align:center; font-size:13.5px; font-weight:800; color:#013C58; text-transform:uppercase; letter-spacing:0.5px; padding:13px 12px; background:rgba(168,232,249,0.22); width:18%;">الإجراءات</th>
                </tr>
            </thead>
            <tbody x-show="!ready" x-cloak>
                @for ($i = 0; $i < 4; $i++)
                    <tr>
                        <td style="padding:14px 12px;"><div class="lesson-skeleton-bar" style="width:20px;"></div></td>
                        <td style="padding:14px 12px;"><div class="lesson-skeleton-bar" style="width:70%; margin-bottom:6px;"></div><div class="lesson-skeleton-bar" style="width:45%; height:9px;"></div></td>
                        <td style="padding:14px 12px;"><div class="lesson-skeleton-bar" style="width:60%; margin:0 auto;"></div></td>
                        <td style="padding:14px 12px;"><div class="lesson-skeleton-bar" style="width:50%; margin:0 auto;"></div></td>
                        <td style="padding:14px 12px;"><div class="lesson-skeleton-bar" style="width:65%; margin:0 auto;"></div></td>
                        <td style="padding:14px 12px;"><div class="lesson-skeleton-bar" style="width:50%; margin:0 auto;"></div></td>
                    </tr>
                @endfor
            </tbody>
            <tbody x-show="ready" x-cloak
                   x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                @forelse ($lessons as $lesson)
                    @php
                        $sc = $statusColors[$lesson->status] ?? $statusColors['pending'];
                        $isPublished = $lesson->status === 'published';
                        $canArchivePermission = auth()->user()->hasRole('super-admin', 'web') || auth()->user()->can('archive lesson');
                        $canArchive = $isPublished && $canArchivePermission;
                        $hasStudents = $lesson->users()->exists();
                    @endphp
                    <tr class="lesson-row">
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); vertical-align:middle; text-align:center;">
                            <div style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:rgba(0,83,122,0.06); color:rgba(1,60,88,0.6); font-family:'Poppins',sans-serif; font-weight:700; font-size:12px;">{{ $lesson->order }}</div>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); vertical-align:middle;">
                            <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; color:#00537A;">{{ $lesson->title_en }}</div>
                            <div style="font-size:12px; color:#0E6A96; opacity:0.75; margin-top:2px;">{{ $lesson->title_ar }}</div>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); vertical-align:middle; text-align:center;">
                            <span style="font-size:12.5px; color:#0E6A96; font-weight:600;">{{ $teacherName }}</span>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); vertical-align:middle; text-align:center;">
                            <div class="lesson-star" style="display:inline-flex; align-items:center; gap:5px; padding:5px 11px; border-radius:9px; background:rgba(255,211,91,0.16); border:1px solid rgba(255,186,66,0.3);">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="#F5A201" stroke="none"><path d="M13 2 3 14h7l-1 8 11-14h-7l1-6Z"></path></svg>
                                <span style="font-family:'Poppins',sans-serif; font-weight:800; font-size:13px; color:#8A5A00;" dir="ltr">{{ $lesson->xp_points }}</span>
                                <span style="font-size:9.5px; font-weight:700; color:rgba(138,90,0,0.55); text-transform:uppercase;">XP</span>
                            </div>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); vertical-align:middle; text-align:center;">
                            <span style="display:inline-flex; align-items:center; gap:6px; padding:5px 12px; border-radius:999px; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }}; font-size:11.5px; font-weight:700;">
                                <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:{{ $sc['dot'] }};"></span>
                                {{ $statusLabels[$lesson->status] ?? $lesson->status }}
                            </span>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); vertical-align:middle; text-align:center;">
                            <div style="display:flex; gap:8px; justify-content:center;">
                                <a href="{{ route('lessons.show', $lesson) }}" title="عرض" class="lesson-icon-btn"
                                   style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; background:rgba(168,232,249,0.22); color:#00537A; text-decoration:none;">
                                    <svg width="15.5" height="15.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </a>
                                <button
                                    type="button"
                                    class="lesson-icon-btn"
                                    @if($canArchive) @click="archiveModalOpen = true; archiveId = {{ $lesson->id }}; archiveName = '{{ addslashes($lesson->title_ar) }}';" @else disabled @endif
                                    title="{{ !$isPublished ? 'الأرشفة متاحة بس للدروس المنشورة' : (!$canArchivePermission ? 'ما عندك صلاحية أرشفة الدروس' : ($hasStudents ? 'أرشفة (رح يصير مغلق)' : 'أرشفة')) }}"
                                    style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; border:none; background:rgba(230,126,34,0.14); color:#C1650A; cursor:{{ $canArchive ? 'pointer' : 'not-allowed' }}; opacity:{{ $canArchive ? 1 : 0.35 }};"
                                >
                                    <svg width="15.5" height="15.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l1.5-3h15L21 7"></path><path d="M4.5 7h15v12a1 1 0 0 1-1 1h-13a1 1 0 0 1-1-1V7Z"></path><path d="M9 12h6"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:0;">
                            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:14px; padding:64px 20px;">
                                <div style="display:flex; align-items:center; justify-content:center; width:64px; height:64px; border-radius:18px; background:rgba(168,232,249,0.25); color:#0E6A96;">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                </div>
                                <div style="text-align:center;">
                                    <p style="margin:0; font-size:14.5px; font-weight:700; color:#013C58;">ما في دروس بهالفلتر</p>
                                    <p style="margin:5px 0 0; font-size:12.5px; color:rgba(1,60,88,0.45);">جرّبي تبدّلي التصنيف من فوق، أو ضيفي درس جديد للكورس</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($lessons instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div style="padding:16px 22px; border-top:1px solid rgba(0,83,122,0.06);">
                {{ $lessons->appends(request()->query())->links('vendor.pagination.lessons') }}
            </div>
        @endif
    </div>

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
            <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:17px; color:#013C58;">أرشفة درس "<span x-text="archiveName"></span>"؟</h3>
            <p style="margin:10px 0 0; font-size:13px; color:rgba(1,60,88,0.6); line-height:1.7;">هيصير الدرس مؤرشف أو مغلق (إذا في طلاب مسجلين فيه)، ومش رح يظهر للطلاب الجدد.</p>
            <div style="display:flex; gap:10px; margin-top:22px;">
                <button type="button" @click="archiveModalOpen = false" style="flex:1; padding:11px; border-radius:11px; border:1.5px solid rgba(0,83,122,0.12); background:#EFFAFD; color:#013C58; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer;">إلغاء</button>
                <form :action="'/lessons/' + archiveId + '/archive'" method="POST" style="flex:1;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" style="width:100%; padding:11px; border-radius:11px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">تأكيد</button>
                </form>
            </div>
        </div>
      </div>
    </div>
</div>
@endsection
