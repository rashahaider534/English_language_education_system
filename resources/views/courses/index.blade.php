@extends('dashboard.layouts.app')

@push('styles')
<style>
    .course-field-wrap {
        border: 1.5px solid rgba(0,83,122,0.12);
        border-radius: 11px;
        padding: 0 4px;
        background: #F7FBFD;
        transition: border-color .15s, box-shadow .15s, background .15s;
    }
    .course-field-wrap:focus-within {
        border-color: #F5A201;
        box-shadow: 0 0 0 4px rgba(245,162,1,0.12);
        background: #EFFAFD;
    }
    .course-field-wrap.is-locked {
        border-color: rgba(0,83,122,0.06);
        background: rgba(0,83,122,0.04);
    }
    .modal-scroll::-webkit-scrollbar { width: 8px; }
    .modal-scroll::-webkit-scrollbar-track { background: transparent; }
    .modal-scroll::-webkit-scrollbar-thumb { background: rgba(1,60,88,0.14); border-radius: 999px; }
    .modal-scroll::-webkit-scrollbar-thumb:hover { background: rgba(1,60,88,0.24); }
    .modal-scroll { scrollbar-width: thin; scrollbar-color: rgba(1,60,88,0.18) transparent; }
</style>
@endpush

@section('content')
@php
    $statusLabels = ['pending' => 'قيد الانتظار', 'published' => 'منشور', 'closed' => 'مغلق', 'archived' => 'مؤرشف'];
    $statusColors = [
        'pending'   => ['bg' => 'rgba(255,211,91,0.92)', 'fg' => '#013C58', 'dot' => '#946200'],
        'published' => ['bg' => 'rgba(168,232,249,0.95)', 'fg' => '#013C58', 'dot' => '#00537A'],
        'closed'    => ['bg' => 'rgba(1,60,88,0.85)', 'fg' => '#fff', 'dot' => 'rgba(255,255,255,0.7)'],
        'archived'  => ['bg' => 'rgba(1,60,88,0.55)', 'fg' => '#fff', 'dot' => 'rgba(255,255,255,0.55)'],
    ];
    $avatarPalette = ['#00537A', '#0E6A96', '#146B93', '#1C7BA6', '#F5A201', '#C97F00'];
    $activeStatus = request('status');

    $currentUser = auth()->user();
    $isSuperAdmin = $currentUser->hasRole('super-admin', 'web');
    $canCreate = $level->status !== 'published' && ($isSuperAdmin || $level->created_by === $currentUser->id);

    $nameOf = function ($user) {
        if (! $user) return '—';
        $full = trim(($user->first_name ?? '').' '.($user->last_name ?? ''));
        return $full !== '' ? $full : $user->email;
    };
    $initialsOf = function ($user) {
        if (! $user) return '?';
        $initials = strtoupper(substr($user->first_name ?? '', 0, 1).substr($user->last_name ?? '', 0, 1));
        return $initials !== '' ? $initials : strtoupper(substr($user->email ?? '?', 0, 2));
    };
@endphp
<div
    x-data="{
        archiveModalOpen: false,
        archiveId: null,
        archiveName: '',
        archiveIsDelete: false,
        archiveHasInProgress: false,
        openArchive(id, name, isDelete, hasInProgress) {
            this.archiveId = id; this.archiveName = name;
            this.archiveIsDelete = isDelete; this.archiveHasInProgress = hasInProgress;
            this.archiveModalOpen = true;
        },
        createModalOpen: {{ ($errors->any() && old('form_type') === 'courses-create') ? 'true' : 'false' }},
        editModalOpen: {{ ($errors->any() && old('form_type') === 'courses-edit') ? 'true' : 'false' }},
        editTarget: {
            id: {{ (int) (old('editing_course_id') ?? 0) }},
            name_en: @js(old('name_en', '')),
            name_ar: @js(old('name_ar', '')),
            order: @js(old('order', '')),
            estimated_duration: @js(old('estimated_duration', '')),
            teacher_id: @js(old('teacher_id', '')),
            status: @js(old('editing_course_status', '')),
            image_url: @js(old('editing_course_image', '')),
        },
        get editIsPublished() { return this.editTarget.status === 'published'; },
        get editIsLocked() { return ['closed', 'archived'].includes(this.editTarget.status); },
        get editIsCoreLocked() { return this.editIsPublished || this.editIsLocked; },
        openEdit(course) {
            this.editTarget = course;
            this.editModalOpen = true;
        },
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

    {{-- generic error flash (e.g. archive rejected by the backend for a reason the UI doesn't already explain) --}}
    @if ($errors->has('course'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(255,138,101,0.14); color:#C2591A; border:1px solid rgba(255,138,101,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
            {{ $errors->first('course') }}
        </div>
    @endif

    {{-- ============ HERO HEAD ============ --}}
    <div style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.2);">
        <div style="position:absolute; width:380px; height:380px; right:-110px; top:-150px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.22) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>
        <div style="position:absolute; width:300px; height:300px; left:-80px; bottom:-140px; border-radius:50%; background:radial-gradient(circle, rgba(168,232,249,0.2) 0%, rgba(168,232,249,0) 70%); pointer-events:none;"></div>
        <div style="position:absolute; top:-60%; left:-30%; width:160%; height:140%; background:linear-gradient(115deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0) 26%); pointer-events:none; transform:rotate(-6deg);"></div>

        <div style="position:relative; display:flex; align-items:center; gap:8px; margin-bottom:16px; font-size:12.5px; color:rgba(168,232,249,0.65); font-weight:600;">
            <a href="{{ route('levels.index') }}" style="color:rgba(168,232,249,0.65); text-decoration:none;">المستويات</a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.5;"><path d="m9 18 6-6-6-6"></path></svg>
            <span style="color:#fff;">{{ $level->name_ar }}</span>
        </div>

        <div style="position:relative; display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap; margin-bottom:22px;">
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="position:relative; display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; font-family:'Poppins',sans-serif; font-weight:800; font-size:16px; flex-shrink:0;">
                    <span style="position:absolute; inset:-4px; border-radius:19px; border:1px solid rgba(255,211,91,0.25);"></span>
                    <span style="position:relative;">{{ strtoupper(substr($level->name_en, 0, 2)) }}</span>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">كورسات المستوى</p>
                    <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">{{ $level->name_en }} <span style="font-weight:500; font-size:14px; color:rgba(168,232,249,0.7); margin-inline-start:8px;">{{ $level->name_ar }}</span></h1>
                </div>
            </div>
            @if ($canCreate)
                <button type="button" @click="createModalOpen = true"
                   style="display:flex; align-items:center; gap:8px; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; border:none; border-radius:13px; padding:12.5px 22px; font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; cursor:pointer; box-shadow:0 12px 26px rgba(0,0,0,0.2);"
                   onmouseover="this.style.transform='translateY(-1px)';" onmouseout="this.style.transform='';">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"></path></svg>
                    إضافة كورس جديد
                </button>
            @else
                <div style="display:flex; align-items:center; gap:8px; background:rgba(255,211,91,0.14); color:#FFD35B; border-radius:12px; padding:11px 16px; font-size:12.5px; font-weight:600;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="10" rx="2.5"></rect><path d="M7.5 10.5V7a4.5 4.5 0 0 1 9 0v3.5"></path></svg>
                    المستوى منشور ولا يوجد لديك صلاحية إضافة كورس
                </div>
            @endif
        </div>

        <div style="position:relative; display:flex; gap:12px; flex-wrap:wrap;">
            @php
                $pillStyle = 'display:flex; align-items:center; gap:8px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.14); backdrop-filter:blur(6px); border-radius:12px; padding:9px 16px; font-size:12.5px; font-weight:600; color:rgba(168,232,249,0.85);';
                $bStyle = "font-family:'Poppins',sans-serif; color:#fff; font-weight:800; margin-inline-start:2px;";
                $dot = 'display:inline-block; width:7px; height:7px; border-radius:50%;';
            @endphp
            <div style="{{ $pillStyle }}"><span style="{{ $dot }} background:#A8E8F9;"></span>الكل <b style="{{ $bStyle }}">{{ $statistics->all_count ?? 0 }}</b></div>
            <div style="{{ $pillStyle }}"><span style="{{ $dot }} background:#FFD35B;"></span>قيد الانتظار <b style="{{ $bStyle }}">{{ $statistics->pending ?? 0 }}</b></div>
            <div style="{{ $pillStyle }}"><span style="{{ $dot }} background:#A8E8F9;"></span>منشورة <b style="{{ $bStyle }}">{{ $statistics->published ?? 0 }}</b></div>
            <div style="{{ $pillStyle }}"><span style="{{ $dot }} background:rgba(255,255,255,0.5);"></span>مغلقة <b style="{{ $bStyle }}">{{ $statistics->closed ?? 0 }}</b></div>
            <div style="{{ $pillStyle }}"><span style="{{ $dot }} background:rgba(255,255,255,0.32);"></span>مؤرشفة <b style="{{ $bStyle }}">{{ $statistics->archived ?? 0 }}</b></div>
        </div>
    </div>

    {{-- ============ FILTER TABS ============ --}}
    <div style="display:flex; gap:6px; background:#EFFAFD; border:1px solid rgba(0,83,122,0.07); border-radius:12px; padding:4px; flex-wrap:wrap; box-shadow:0 6px 18px rgba(0,83,122,0.04); margin-bottom:22px; width:fit-content;">
        @php
            $tabBase = "border:none; background:transparent; padding:8px 14px; border-radius:9px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:600; white-space:nowrap; text-decoration:none; display:inline-block;";
        @endphp
        <a href="{{ route('courses.index', $level) }}" style="{{ $tabBase }} {{ !$activeStatus ? 'background:#013C58; color:#fff;' : 'color:rgba(1,60,88,0.55);' }}">الكل</a>
        <a href="{{ route('courses.index', [$level, 'status' => 'pending']) }}" style="{{ $tabBase }} {{ $activeStatus === 'pending' ? 'background:#013C58; color:#fff;' : 'color:rgba(1,60,88,0.55);' }}">قيد الانتظار</a>
        <a href="{{ route('courses.index', [$level, 'status' => 'published']) }}" style="{{ $tabBase }} {{ $activeStatus === 'published' ? 'background:#013C58; color:#fff;' : 'color:rgba(1,60,88,0.55);' }}">منشورة</a>
        <a href="{{ route('courses.index', [$level, 'status' => 'closed']) }}" style="{{ $tabBase }} {{ $activeStatus === 'closed' ? 'background:#013C58; color:#fff;' : 'color:rgba(1,60,88,0.55);' }}">مغلقة</a>
        <a href="{{ route('courses.index', [$level, 'status' => 'archived']) }}" style="{{ $tabBase }} {{ $activeStatus === 'archived' ? 'background:#013C58; color:#fff;' : 'color:rgba(1,60,88,0.55);' }}">مؤرشفة</a>
    </div>

    {{-- ============ COURSE CARDS GRID ============ --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(270px, 1fr)); gap:18px;">
        @forelse ($courses as $i => $course)
            @php
                $sc = $statusColors[$course->status] ?? $statusColors['pending'];
                $canArchive = !in_array($course->status, ['closed', 'archived']) && ($isSuperAdmin || auth()->id() === $course->created_by);
                $hasInProgress = $course->usercourses()->wherePivot('status', 'in_progress')->exists();
                $hasLessons = $course->lessons()->exists();
                $isHardDelete = $course->status === 'pending' && !$hasLessons;
                $avatarColor = $avatarPalette[$i % count($avatarPalette)];
                $dimmed = in_array($course->status, ['closed', 'archived']);
                $canEditCourse = $isSuperAdmin || auth()->id() === $course->created_by;
                $imageUrl = $course->getFirstMediaUrl('course_image');
            @endphp
            <div style="position:relative; background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; overflow:hidden; box-shadow:0 10px 26px rgba(0,83,122,0.06); transition:transform 0.25s cubic-bezier(0.16,1,0.3,1), box-shadow 0.25s;"
                 onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 28px 56px rgba(0,83,122,0.16)';"
                 onmouseout="this.style.transform=''; this.style.boxShadow='0 10px 26px rgba(0,83,122,0.06)';">
                <div style="position:absolute; top:0; left:0; right:0; height:3px; background:{{ $sc['dot'] }}; z-index:1;"></div>
                <div style="position:relative; width:100%; height:138px; background:linear-gradient(135deg,#013C58,#0E6A96,#146B93); overflow:hidden;">
                    @if ($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $course->name_en }}" style="width:100%; height:100%; object-fit:cover;">
                    @else
                        <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; color:rgba(255,255,255,0.3);">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="3"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-4 4-3-3-4 4"></path></svg>
                        </div>
                    @endif
                    <div style="position:absolute; inset:0; background:linear-gradient(180deg, rgba(1,42,63,0) 45%, rgba(1,42,63,0.35) 100%); pointer-events:none;"></div>
                    <span style="position:absolute; top:10px; right:10px; display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:999px; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }}; font-size:10.5px; font-weight:700; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                        <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:{{ $sc['dot'] }};"></span>{{ $statusLabels[$course->status] ?? $course->status }}
                    </span>
                    <div style="position:absolute; bottom:10px; left:10px; background:rgba(1,42,63,0.6); backdrop-filter:blur(4px); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:11px; padding:3px 10px; border-radius:999px; border:1px solid rgba(255,255,255,0.14);">#{{ $course->order }}</div>
                </div>

                <div style="padding:17px 18px 18px;">
                    <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:15px; color:#013C58;">{{ $course->name_en }}</div>
                    <div style="font-size:12.5px; color:rgba(1,60,88,0.5); margin-top:3px;">{{ $course->name_ar }}</div>

                    <div style="display:flex; align-items:center; gap:9px; margin-top:13px;">
                        <span style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:9px; background:{{ $avatarColor }}; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:10.5px; flex-shrink:0; opacity:{{ $dimmed ? 0.5 : 1 }};">{{ $initialsOf($course->teacher) }}</span>
                        <span style="font-size:12.5px; font-weight:600; color:#013C58;">{{ $nameOf($course->teacher) }}</span>
                    </div>

                    @php
                        $avgStars = round($course->rates_avg_stars ?? 0);
                    @endphp
                    <div style="display:flex; align-items:center; gap:2px; margin-top:8px;">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $avgStars)
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="#F5A201" stroke="#F5A201" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9L12 3Z"></path></svg>
                            @else
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#F5A201" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.4;"><path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9L12 3Z"></path></svg>
                            @endif
                        @endfor
                        @if ($course->rates_avg_stars)
                            <span style="font-size:11px; color:rgba(1,60,88,0.5); margin-inline-start:4px;">({{ number_format($course->rates_avg_stars, 1) }})</span>
                        @endif
                    </div>

                    <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; margin-top:11px; flex-wrap:wrap;">
                        <div style="display:flex; align-items:center; gap:5px; font-size:11.5px; color:#00537A; font-weight:700; background:rgba(0,83,122,0.06); border-radius:999px; padding:5px 10px;">
                            <svg width="12.5" height="12.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
                            <span dir="ltr">{{ $course->estimated_duration }} أسابيع</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:4px; font-size:10.5px; color:rgba(1,60,88,0.4);">
                            <svg width="10.5" height="10.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                            أنشأه {{ $nameOf($course->creator) }}
                        </div>
                    </div>

                    <div style="display:flex; gap:8px; margin-top:14px; padding-top:14px; border-top:1px solid rgba(0,83,122,0.06);">
                        <a href="{{ route('lessons.index', $course) }}" title="عرض الدروس"
                           style="display:flex; align-items:center; justify-content:center; width:37px; height:37px; border-radius:10px; background:rgba(168,232,249,0.18); color:#00537A; text-decoration:none; flex-shrink:0;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h9"></path><path d="M4 12h9"></path><path d="M4 18h5"></path><path d="M15 15.5v-5l4.5 2.5-4.5 2.5Z" fill="currentColor" stroke="none"></path></svg>
                        </a>
                        <a href="{{ route('courses.tests', $course) }}" title="اختبارات الكورس"
                           style="display:flex; align-items:center; justify-content:center; width:37px; height:37px; border-radius:10px; background:rgba(255,211,91,0.22); color:#8A5A00; text-decoration:none; flex-shrink:0;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M8 2v4M16 2v4M3 10h18"></path></svg>
                        </a>
                        @if ($canEditCourse && !in_array($course->status, ['closed', 'archived']))
                            <button type="button"
                               @click="openEdit({{ Illuminate\Support\Js::from([
                                    'id' => $course->id,
                                    'name_en' => $course->name_en,
                                    'name_ar' => $course->name_ar,
                                    'order' => $course->order,
                                    'estimated_duration' => $course->estimated_duration,
                                    'teacher_id' => $course->teacher_id,
                                    'status' => $course->status,
                                    'image_url' => $imageUrl,
                               ]) }})"
                               style="flex:1; display:flex; align-items:center; justify-content:center; gap:6px; padding:9px; border-radius:10px; border:none; background:rgba(0,83,122,0.07); color:#00537A; font-family:'Poppins',sans-serif; font-weight:600; font-size:12px; cursor:pointer;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                                تعديل
                            </button>
                        @endif
                        <button type="button"
                            @if($canArchive) @click="openArchive({{ $course->id }}, '{{ addslashes($course->name_ar) }}', {{ $isHardDelete ? 'true' : 'false' }}, {{ $hasInProgress ? 'true' : 'false' }})" @else disabled @endif
                            title="{{ !$isSuperAdmin && auth()->id() !== $course->created_by ? 'ما فيك تؤرشفي هالكورس لأنه مش من إنشائك' : (!$canArchive ? 'مغلق أو مؤرشف من قبل' : ($isHardDelete ? 'حذف نهائي' : 'أرشفة')) }}"
                            style="flex:1; display:flex; align-items:center; justify-content:center; gap:6px; padding:9px; border-radius:10px; border:none; cursor:{{ $canArchive ? 'pointer' : 'not-allowed' }}; opacity:{{ $canArchive ? 1 : 0.35 }}; background:rgba(245,162,1,0.1); color:#C97F00; font-family:'Poppins',sans-serif; font-weight:600; font-size:12px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l1.5-3h15L21 7"></path><path d="M4.5 7h15v12a1 1 0 0 1-1 1h-13a1 1 0 0 1-1-1V7Z"></path><path d="M9 12h6"></path></svg>
                            {{ $isHardDelete ? 'حذف' : 'أرشفة' }}
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px; padding:60px 20px;">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="color:rgba(1,60,88,0.25);"><rect x="3" y="4" width="18" height="16" rx="3"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-4 4-3-3-4 4"></path></svg>
                <p style="margin:0; font-size:14px; color:rgba(1,60,88,0.45); font-weight:600;">  لايوجد كورسات بهذا القسم  </p>
            </div>
        @endforelse
    </div>

    @if ($courses instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div style="margin-top:22px;">{{ $courses->appends(request()->query())->links() }}</div>
    @endif

    {{-- ============ ARCHIVE / DELETE CONFIRM MODAL ============ --}}
    <div x-show="archiveModalOpen" x-cloak
         class="modal-scroll" style="position:fixed; inset:0; z-index:50; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px); overflow-y:auto;"
         @click="archiveModalOpen = false">
      <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
        <div @click.stop style="width:100%; max-width:400px; background:#EFFAFD; border-radius:22px; padding:30px 26px; box-shadow:0 44px 100px rgba(1,42,63,0.4); text-align:center;">
            <div style="width:58px; height:58px; border-radius:16px; background:rgba(245,162,1,0.1); color:#C97F00; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l1.5-3h15L21 7"></path><path d="M4.5 7h15v12a1 1 0 0 1-1 1h-13a1 1 0 0 1-1-1V7Z"></path><path d="M9 12h6"></path></svg>
            </div>
            <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:17px; color:#013C58;">
                <span x-text="archiveIsDelete ? 'حذف كورس \"' + archiveName + '\" نهائياً؟' : 'أرشفة كورس \"' + archiveName + '\"؟'"></span>
            </h3>
            <p style="margin:10px 0 0; font-size:13px; color:rgba(1,60,88,0.6); line-height:1.7;">
                <span x-show="archiveIsDelete">هالكورس لسا ما فيه دروس أو طلاب، فبينحذف نهائياً من قاعدة البيانات بدل الأرشفة.</span>
                <span x-show="!archiveIsDelete && archiveHasInProgress">في طلاب عم يدرسو هلق هالكورس، فبيصير "مغلق" لحتى يخلّصو. ما رح يقبل طلاب جدد.</span>
                <span x-show="!archiveIsDelete && !archiveHasInProgress"> </span>
            </p>
            <div style="display:flex; gap:10px; margin-top:22px;">
                <button type="button" @click="archiveModalOpen = false" style="flex:1; padding:11px; border-radius:11px; border:1.5px solid rgba(0,83,122,0.12); background:#EFFAFD; color:#013C58; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer;">إلغاء</button>
                <form :action="'/courses/' + archiveId + '/archive'" method="POST" style="flex:1;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" style="width:100%; padding:11px; border-radius:11px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;" x-text="archiveIsDelete ? 'حذف نهائي' : 'تأكيد'"></button>
                </form>
            </div>
        </div>
      </div>
    </div>

    {{-- ============ CREATE MODAL ============ --}}
    <div x-show="createModalOpen" x-cloak
         class="modal-scroll" style="position:fixed; inset:0; z-index:50; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px); overflow-y:auto;"
         @click="createModalOpen = false">
      <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
        <div @click.stop class="modal-scroll" style="position:relative; width:100%; max-width:640px; max-height:88vh; overflow-y:auto; background:#EFFAFD; border-radius:28px; padding:32px 28px 28px; box-shadow:0 50px 110px rgba(1,42,63,0.42); font-family:'Tajawal',sans-serif;" dir="rtl">
            <button type="button" @click="createModalOpen = false" style="position:absolute; top:16px; left:16px; width:30px; height:30px; border-radius:50%; border:none; background:rgba(0,83,122,0.06); color:rgba(1,60,88,0.6); display:flex; align-items:center; justify-content:center; cursor:pointer;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
            </button>

            <div style="text-align:center; margin-bottom:22px;">
                <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#013C58;">إضافة كورس جديد</h1>
                <p style="margin:6px 0 0; font-size:13px; color:rgba(1,60,88,0.5);">لمستوى {{ $level->name_ar }} ({{ $level->name_en }})</p>
            </div>

            @if ($errors->any() && old('form_type') === 'courses-create')
                <div style="display:flex; align-items:flex-start; gap:9px; margin-bottom:18px; padding:13px 16px; border-radius:12px; background:rgba(148,98,0,0.08); color:#946200; font-size:13px; font-weight:600;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
                    <ul style="margin:0; padding-inline-start:16px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('courses.store', $level) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="form_type" value="courses-create">

                @php
                    $label = 'display:block; font-size:12px; font-weight:600; color:rgba(1,60,88,0.6); margin-bottom:7px;';
                    $wrap = 'course-field-wrap';
                    $input = "width:100%; background:transparent; border:none; outline:none; padding:11px 11px; font-size:13.5px; color:#013C58; font-family:'Tajawal',sans-serif;";
                    $section = 'display:inline-flex; margin:0 0 12px; font-size:10.5px; font-weight:800; letter-spacing:0.8px; text-transform:uppercase; color:#00537A; background:rgba(168,232,249,0.3); padding:5px 12px; border-radius:999px;';
                @endphp

                <p style="{{ $section }}">الاسم</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label style="{{ $label }}">بالإنكليزي</label>
                        <div class="{{ $wrap }}">
                            <input name="name_en" value="{{ old('name_en') }}" placeholder="e.g. Grammar Basics" style="{{ $input }}">
                        </div>
                    </div>
                    <div>
                        <label style="{{ $label }}">بالعربي</label>
                        <div class="{{ $wrap }}">
                            <input name="name_ar" value="{{ old('name_ar') }}" placeholder="مثال: أساسيات القواعد" style="{{ $input }}">
                        </div>
                    </div>
                </div>

                <p style="{{ $section }} margin-top:22px;">الترتيب والمدة</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label style="{{ $label }}">الترتيب</label>
                        <div class="{{ $wrap }}">
                            <input type="number" name="order" value="{{ old('order') }}" placeholder="1" style="{{ $input }}">
                        </div>
                    </div>
                    <div>
                        <label style="{{ $label }}">المدة المتوقعة (أسابيع)</label>
                        <div class="{{ $wrap }}">
                            <input type="number" name="estimated_duration" value="{{ old('estimated_duration') }}" placeholder="4" style="{{ $input }}">
                        </div>
                    </div>
                </div>

                <p style="{{ $section }} margin-top:22px;">الأستاذ</p>
                <div class="{{ $wrap }}">
                    <select name="teacher_id" style="{{ $input }}">
                        <option value="">اختاري الأستاذ...</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}" @selected(old('teacher_id') == $teacher->id)>{{ $teacher->email }}</option>
                        @endforeach
                    </select>
                </div>

                <p style="{{ $section }} margin-top:22px;">صورة الكورس</p>
                <div style="display:flex; align-items:center; gap:14px; border:1.5px dashed rgba(0,83,122,0.18); border-radius:14px; padding:14px;">
                    <div style="width:64px; height:64px; border-radius:12px; background:rgba(0,83,122,0.05); display:flex; align-items:center; justify-content:center; color:rgba(1,60,88,0.3); flex-shrink:0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="3"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-4 4-3-3-4 4"></path></svg>
                    </div>
                    <div style="flex:1;">
                        <input type="file" name="image" accept="image/*" style="font-size:12.5px; color:rgba(1,60,88,0.6); width:100%;">
                        <p style="margin:6px 0 0; font-size:11px; color:rgba(1,60,88,0.4);">JPG أو PNG أو WEBP، حتى 2 ميغابايت</p>
                    </div>
                </div>

                <div style="display:flex; flex-direction:row-reverse; gap:10px; margin-top:26px; padding-top:20px; border-top:1px solid rgba(0,83,122,0.06);">
                    <button type="submit" style="display:flex; align-items:center; gap:7px; padding:12px 24px; border-radius:999px; border:none; background:linear-gradient(90deg,#013C58,#00537A); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; cursor:pointer; box-shadow:0 14px 28px rgba(1,60,88,0.28);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                        إضافة الكورس
                    </button>
                    <button type="button" @click="createModalOpen = false" style="padding:12px 20px; border:none; background:transparent; color:rgba(1,60,88,0.5); font-family:'Poppins',sans-serif; font-weight:600; font-size:13.5px; cursor:pointer;">إلغاء</button>
                </div>
            </form>
        </div>
      </div>
    </div>

    {{-- ============ EDIT MODAL ============ --}}
    <div x-show="editModalOpen" x-cloak
         class="modal-scroll" style="position:fixed; inset:0; z-index:50; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px); overflow-y:auto;"
         @click="editModalOpen = false">
      <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
        <div @click.stop class="modal-scroll" style="position:relative; width:100%; max-width:640px; max-height:88vh; overflow-y:auto; background:#EFFAFD; border-radius:28px; padding:32px 28px 28px; box-shadow:0 50px 110px rgba(1,42,63,0.42); font-family:'Tajawal',sans-serif;" dir="rtl">
            <button type="button" @click="editModalOpen = false" style="position:absolute; top:16px; left:16px; width:30px; height:30px; border-radius:50%; border:none; background:rgba(0,83,122,0.06); color:rgba(1,60,88,0.6); display:flex; align-items:center; justify-content:center; cursor:pointer;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
            </button>

            <div style="text-align:center; margin-bottom:22px;">
                <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#013C58;">تعديل الكورس</h1>
                <p style="margin:6px 0 0; font-size:13px; color:rgba(1,60,88,0.5);">{{ $level->name_ar }} ({{ $level->name_en }})</p>
            </div>

            @if ($errors->any() && old('form_type') === 'courses-edit')
                <div style="display:flex; align-items:flex-start; gap:9px; margin-bottom:18px; padding:13px 16px; border-radius:12px; background:rgba(148,98,0,0.08); color:#946200; font-size:13px; font-weight:600;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
                    <ul style="margin:0; padding-inline-start:16px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <template x-if="editTarget.id">
                <form method="POST" :action="'/courses/' + editTarget.id" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_type" value="courses-edit">
                    <input type="hidden" name="editing_course_id" :value="editTarget.id">
                    <input type="hidden" name="editing_course_status" :value="editTarget.status">

                    @php
                        $label = 'display:block; font-size:12px; font-weight:600; color:rgba(1,60,88,0.6); margin-bottom:7px;';
                        $input = "width:100%; background:transparent; border:none; outline:none; padding:11px 11px; font-size:13.5px; color:#013C58; font-family:'Tajawal',sans-serif;";
                        $section = 'display:inline-flex; margin:0 0 12px; font-size:10.5px; font-weight:800; letter-spacing:0.8px; text-transform:uppercase; color:#00537A; background:rgba(168,232,249,0.3); padding:5px 12px; border-radius:999px;';
                    @endphp

                    <div style="display:flex; align-items:center; justify-content:space-between; background:rgba(0,83,122,0.04); border-radius:12px; padding:12px 16px; margin-bottom:18px;">
                        <span style="font-size:12.5px; font-weight:600; color:rgba(1,60,88,0.6);">حالة الكورس الحالية</span>
                        <span x-text="editTarget.status" style="display:inline-flex; align-items:center; gap:6px; padding:5px 12px; border-radius:999px; background:rgba(0,83,122,0.08); color:#013C58; font-size:11.5px; font-weight:700;"></span>
                    </div>

                    <div x-show="editIsLocked" style="display:flex; align-items:center; gap:9px; background:rgba(1,60,88,0.06); color:#00537A; border-radius:12px; padding:12px 16px; font-size:13px; font-weight:600; margin-bottom:18px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="10" rx="2.5"></rect><path d="M7.5 10.5V7a4.5 4.5 0 0 1 9 0v3.5"></path></svg>
                        <span>هالكورس مغلق أو مؤرشف، فما فيك تعدّلي عليه.</span>
                    </div>
                    <div x-show="!editIsLocked && editIsPublished" style="display:flex; align-items:center; gap:9px; background:rgba(255,211,91,0.14); color:#946200; border-radius:12px; padding:12px 16px; font-size:13px; font-weight:600; margin-bottom:18px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
                        <span>  هذا الكورس منشور يمكن تعديل فقط الاسم,المدة,الصورة</span>
                    </div>

                    <p style="{{ $section }}">الاسم</p>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div>
                            <label style="{{ $label }}">بالإنكليزي</label>
                            <div :class="editIsLocked ? 'course-field-wrap is-locked' : 'course-field-wrap'">
                                <input name="name_en" x-model="editTarget.name_en" :disabled="editIsLocked" style="{{ $input }}">
                            </div>
                        </div>
                        <div>
                            <label style="{{ $label }}">بالعربي</label>
                            <div :class="editIsLocked ? 'course-field-wrap is-locked' : 'course-field-wrap'">
                                <input name="name_ar" x-model="editTarget.name_ar" :disabled="editIsLocked" style="{{ $input }}">
                            </div>
                        </div>
                    </div>

                    <p style="{{ $section }} margin-top:22px;">الترتيب والمدة</p>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div>
                            <label style="{{ $label }}">الترتيب</label>
                            <div :class="editIsCoreLocked ? 'course-field-wrap is-locked' : 'course-field-wrap'">
                                <input type="number" name="order" x-model="editTarget.order" :disabled="editIsCoreLocked" style="{{ $input }}">
                            </div>
                        </div>
                        <div>
                            <label style="{{ $label }}">المدة المتوقعة (أسابيع)</label>
                            <div :class="editIsLocked ? 'course-field-wrap is-locked' : 'course-field-wrap'">
                                <input type="number" name="estimated_duration" x-model="editTarget.estimated_duration" :disabled="editIsLocked" style="{{ $input }}">
                            </div>
                        </div>
                    </div>

                    <p style="{{ $section }} margin-top:22px;">الأستاذ</p>
                    <div :class="editIsCoreLocked ? 'course-field-wrap is-locked' : 'course-field-wrap'">
                        <select name="teacher_id" x-model="editTarget.teacher_id" :disabled="editIsCoreLocked" style="{{ $input }}">
                            <option value="">اختاري الأستاذ...</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->email }}</option>
                            @endforeach
                        </select>
                    </div>

                    <p style="{{ $section }} margin-top:22px;">صورة الكورس</p>
                    <div style="display:flex; align-items:center; gap:14px; border:1.5px dashed rgba(0,83,122,0.18); border-radius:14px; padding:14px;">
                        <template x-if="editTarget.image_url">
                            <img :src="editTarget.image_url" style="width:64px; height:64px; border-radius:12px; object-fit:cover; flex-shrink:0;">
                        </template>
                        <template x-if="!editTarget.image_url">
                            <div style="width:64px; height:64px; border-radius:12px; background:rgba(0,83,122,0.05); display:flex; align-items:center; justify-content:center; color:rgba(1,60,88,0.3); flex-shrink:0;">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="3"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-4 4-3-3-4 4"></path></svg>
                            </div>
                        </template>
                        <div style="flex:1;">
                            <input type="file" name="image" accept="image/*" :disabled="editIsLocked" style="font-size:12.5px; color:rgba(1,60,88,0.6); width:100%;">
                            <p style="margin:6px 0 0; font-size:11px; color:rgba(1,60,88,0.4);">JPG أو PNG أو WEBP، حتى 2 ميغابايت — قابلة للتعديل حتى لو الكورس منشور</p>
                        </div>
                    </div>

                    <div style="display:flex; flex-direction:row-reverse; gap:10px; margin-top:26px; padding-top:20px; border-top:1px solid rgba(0,83,122,0.06);">
                        <button type="submit" x-show="!editIsLocked" style="display:flex; align-items:center; gap:7px; padding:12px 24px; border-radius:999px; border:none; background:linear-gradient(90deg,#013C58,#00537A); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; cursor:pointer; box-shadow:0 14px 28px rgba(1,60,88,0.28);">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                            حفظ التعديلات
                        </button>
                        <button type="button" @click="editModalOpen = false" style="padding:12px 20px; border:none; background:transparent; color:rgba(1,60,88,0.5); font-family:'Poppins',sans-serif; font-weight:600; font-size:13.5px; cursor:pointer;">إلغاء</button>
                    </div>
                </form>
            </template>
        </div>
      </div>
    </div>
</div>
@endsection
