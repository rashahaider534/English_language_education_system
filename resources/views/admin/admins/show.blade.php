@extends('dashboard.layouts.app')

@section('content')
@php
    $adminName = trim($admin->first_name.' '.$admin->last_name) ?: $admin->email;

    $topicStatusLabels = ['published' => 'منشور', 'pending' => 'قيد الانتظار'];
    $topicStatusColors = [
        'published' => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55'],
        'pending'   => ['bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00'],
    ];

    $levelExceptionStatusLabels = ['pending' => 'قيد الانتظار', 'in_review' => 'قيد المراجعة', 'approved' => 'موافق عليه', 'rejected' => 'مرفوض'];
    $levelExceptionStatusColors = [
        'pending'    => ['bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00'],
        'in_review'  => ['bg' => 'rgba(14,106,150,0.14)', 'fg' => '#0E6A96'],
        'approved'   => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55'],
        'rejected'   => ['bg' => 'rgba(255,138,101,0.18)', 'fg' => '#C2591A'],
    ];

    $reviewStatusLabels = ['in_review' => 'قيد المراجعة', 'approved' => 'تمت الموافقة', 'changes_requested' => 'طُلب تعديل', 'released' => 'مُعاد للطابور'];
    $reviewStatusColors = [
        'in_review'         => ['bg' => 'rgba(14,106,150,0.14)', 'fg' => '#0E6A96'],
        'approved'          => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55'],
        'changes_requested' => ['bg' => 'rgba(255,138,101,0.18)', 'fg' => '#C2591A'],
        'released'          => ['bg' => 'rgba(1,60,88,0.1)', 'fg' => 'rgba(1,60,88,0.55)'],
    ];

    $genericStatusLabels = ['pending' => 'قيد الانتظار', 'published' => 'منشور', 'closed' => 'مغلق', 'archived' => 'مؤرشف'];
    $genericStatusColors = [
        'pending'   => ['bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00'],
        'published' => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55'],
        'closed'    => ['bg' => 'rgba(1,60,88,0.1)', 'fg' => 'rgba(1,60,88,0.6)'],
        'archived'  => ['bg' => 'rgba(1,60,88,0.08)', 'fg' => 'rgba(1,60,88,0.55)'],
    ];

    $pill = function ($label, $colors) {
        return '<span style="display:inline-flex; padding:4px 10px; border-radius:999px; background:'.($colors['bg']).'; color:'.($colors['fg']).'; font-size:10.5px; font-weight:700; white-space:nowrap;">'.$label.'</span>';
    };
@endphp
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    <div style="display:flex; align-items:center; gap:14px; margin-bottom:22px; flex-wrap:wrap;">
        <a href="{{ route('admin.admins.index') }}" style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); color:#00537A; text-decoration:none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"></path></svg>
        </a>
        <div style="display:flex; align-items:center; gap:12px; flex:1;">
            <div style="display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:14px; background:#00537A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:14px;">{{ strtoupper(substr($admin->first_name ?? $admin->email, 0, 1).substr($admin->last_name ?? '', 0, 1)) }}</div>
            <div>
                <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:rgba(1,60,88,0.5);">إدارة ومتابعة / الأدمنز / أعمال الأدمن</p>
                <h1 style="margin:4px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#013C58;">{{ $adminName }}</h1>
            </div>
        </div>
        @if ($admin->deleted_at)
            <span style="display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:999px; background:rgba(255,138,101,0.18); color:#C2591A; font-size:12px; font-weight:700;">معطّل</span>
        @else
            <span style="display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:999px; background:rgba(76,175,120,0.16); color:#2E7D55; font-size:12px; font-weight:700;">نشط</span>
        @endif
    </div>

    <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; padding:26px; box-shadow:0 18px 44px rgba(0,83,122,0.06); margin-bottom:18px;">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div>
                <p style="margin:0 0 4px; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.5); text-transform:uppercase;">البريد الإلكتروني</p>
                <p style="margin:0; font-size:13.5px; font-weight:700; color:#013C58;">{{ $admin->email }}</p>
            </div>
            <div>
                <p style="margin:0 0 4px; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.5); text-transform:uppercase;">تاريخ الانضمام</p>
                <p style="margin:0; font-size:13.5px; font-weight:700; color:#013C58;">{{ $admin->created_at?->format('Y-m-d') }}</p>
            </div>
        </div>
    </div>

    <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; padding:26px; box-shadow:0 18px 44px rgba(0,83,122,0.06); margin-bottom:26px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
            <p style="margin:0; font-size:12.5px; font-weight:700; color:#00537A;">الصلاحيات </p>
             @unless ($admin->deleted_at)
            <a href="{{ route('admin.permissions', $admin->id) }}" style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:999px; background:linear-gradient(90deg,#013C58,#00537A); color:#fff; text-decoration:none; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px;">
                تعديل الصلاحيات
            </a>
            @endunless
        </div>
        <div>
            @forelse ($admin->permissions as $permission)
                <span style="display:inline-flex; margin:3px; padding:6px 14px; border-radius:999px; background:rgba(14,106,150,0.1); color:#0E6A96; font-size:12px; font-weight:700;">{{ $permission->name }}</span>
            @empty
                <p style="margin:0; font-size:13px; color:rgba(1,60,88,0.45);">لايوجد صلاحيات ممنوحة للادمن</p>
            @endforelse
        </div>
    </div>

    <h2 style="margin:0 0 16px; font-family:'Poppins',sans-serif; font-weight:800; font-size:16px; color:#013C58;">أعمال الأدمن</h2>

    <div style="display:flex; flex-direction:column; gap:16px;">

        {{-- ============ REVIEWS ============ --}}
        <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:22px; box-shadow:0 14px 34px rgba(0,83,122,0.05);">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                <span style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:10px; background:rgba(14,106,150,0.12); color:#0E6A96;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"></path><circle cx="12" cy="12" r="10"></circle></svg>
                </span>
                <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#013C58;">تدقيقات قام بها</p>
                <span style="margin-inline-start:auto; font-family:'Poppins',sans-serif; font-weight:800; font-size:13px; color:rgba(1,60,88,0.4);">{{ $activity['reviews']->count() }}</span>
            </div>
            @forelse ($activity['reviews'] as $review)
                @php
                    $reviewable = $review->reviewable;
                    $reviewableTitle = $reviewable ? ($reviewable->title_ar ?? $reviewable->title_en ?? '—') : 'محتوى محذوف';
                    $reviewableType = $review->reviewable_type === \App\Models\Lesson::class ? 'درس' : 'اختبار';
                    $rsVal = $review->status instanceof \BackedEnum ? $review->status->value : $review->status;
                    $rc = $reviewStatusColors[$rsVal] ?? $reviewStatusColors['in_review'];
                    $reviewLink = $reviewable ? ($review->reviewable_type === \App\Models\Lesson::class ? route('lessons.show', $reviewable->id) : route('test.show', $reviewable->id)) : null;
                @endphp
                <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; padding:11px 0; border-top:1px solid rgba(0,83,122,0.06);">
                    <div style="min-width:0;">
                        @if ($reviewLink)
                            <a href="{{ $reviewLink }}" style="font-size:13px; font-weight:700; color:#00537A; text-decoration:none;">{{ $reviewableTitle }}</a>
                        @else
                            <span style="font-size:13px; font-weight:700; color:rgba(1,60,88,0.5);">{{ $reviewableTitle }}</span>
                        @endif
                        <span style="font-size:11px; color:rgba(1,60,88,0.4); margin-inline-start:6px;">({{ $reviewableType }})</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
                        {!! $pill($reviewStatusLabels[$rsVal] ?? $rsVal, $rc) !!}
                        <span style="font-size:11px; color:rgba(1,60,88,0.4);">{{ ($review->completed_at ?? $review->claimed_at)?->format('Y-m-d') }}</span>
                    </div>
                </div>
            @empty
                <p style="margin:0; font-size:13px; color:rgba(1,60,88,0.45);">لم يقم هذا الادمن بقيام تدقيق </p>
            @endforelse
        </div>

        {{-- ============ LEVELS ============ --}}
        @if ($admin->can('manage_levels', 'web'))
        <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:22px; box-shadow:0 14px 34px rgba(0,83,122,0.05);">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                <span style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:10px; background:rgba(168,232,249,0.3); color:#00537A;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 9 4.5-9 4.5-9-4.5Z"></path><path d="m3 11.5 9 4.5 9-4.5"></path><path d="m3 16.5 9 4.5 9-4.5"></path></svg>
                </span>
                <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#013C58;">مستويات أنشأها</p>
                <span style="margin-inline-start:auto; font-family:'Poppins',sans-serif; font-weight:800; font-size:13px; color:rgba(1,60,88,0.4);">{{ $activity['levels']->count() }}</span>
            </div>
            @forelse ($activity['levels'] as $level)
                @php $sc = $genericStatusColors[$level->status] ?? $genericStatusColors['pending']; @endphp
                <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; padding:11px 0; border-top:1px solid rgba(0,83,122,0.06);">
                    <span style="font-size:13px; font-weight:700; color:#013C58;">{{ $level->name_ar }} <span style="font-weight:500; color:rgba(1,60,88,0.45);">({{ $level->name_en }})</span></span>
                    <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
                        {!! $pill($genericStatusLabels[$level->status] ?? $level->status, $sc) !!}
                        <span style="font-size:11px; color:rgba(1,60,88,0.4);">{{ $level->created_at?->format('Y-m-d') }}</span>
                    </div>
                </div>
            @empty
                <p style="margin:0; font-size:13px; color:rgba(1,60,88,0.45);">لم يقم الادمن بانشاء اي مستوى للان </p>
            @endforelse
        </div>
        @endif

        {{-- ============ COURSES ============ --}}
        @if ($admin->can('manage_courses', 'web'))
        <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:22px; box-shadow:0 14px 34px rgba(0,83,122,0.05);">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                <span style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:10px; background:rgba(168,232,249,0.3); color:#00537A;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="3"></rect><path d="M8 2v4M16 2v4M3 10h18"></path></svg>
                </span>
                <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#013C58;">كورسات أنشأها</p>
                <span style="margin-inline-start:auto; font-family:'Poppins',sans-serif; font-weight:800; font-size:13px; color:rgba(1,60,88,0.4);">{{ $activity['courses']->count() }}</span>
            </div>
            @forelse ($activity['courses'] as $course)
                @php $sc = $genericStatusColors[$course->status] ?? $genericStatusColors['pending']; @endphp
                <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; padding:11px 0; border-top:1px solid rgba(0,83,122,0.06);">
                    <div style="min-width:0;">
                        <a href="{{ route('lessons.index', $course) }}" style="font-size:13px; font-weight:700; color:#00537A; text-decoration:none;">{{ $course->name_ar }}</a>
                        <span style="font-size:11px; color:rgba(1,60,88,0.4); margin-inline-start:6px;">{{ $course->level->name_ar ?? '' }}</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
                        {!! $pill($genericStatusLabels[$course->status] ?? $course->status, $sc) !!}
                        <span style="font-size:11px; color:rgba(1,60,88,0.4);">{{ $course->created_at?->format('Y-m-d') }}</span>
                    </div>
                </div>
            @empty
                <p style="margin:0; font-size:13px; color:rgba(1,60,88,0.45);">لم يقم الادمن  بأنشأ كورس للان </p>
            @endforelse
        </div>
        @endif

        {{-- ============ TOPICS ============ --}}
        @if ($admin->can('manage_podcasts', 'web'))
        <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:22px; box-shadow:0 14px 34px rgba(0,83,122,0.05);">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                <span style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:10px; background:rgba(255,211,91,0.22); color:#8A5A00;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3ZM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3Z"></path></svg>
                </span>
                <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#013C58;">مواضيع بودكاست أنشأها</p>
                <span style="margin-inline-start:auto; font-family:'Poppins',sans-serif; font-weight:800; font-size:13px; color:rgba(1,60,88,0.4);">{{ $activity['topics']->count() }}</span>
            </div>
            @forelse ($activity['topics'] as $topic)
                @php
                    $tsVal = $topic->status instanceof \BackedEnum ? $topic->status->value : $topic->status;
                    $sc = $topicStatusColors[$tsVal] ?? $topicStatusColors['pending'];
                @endphp
                <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; padding:11px 0; border-top:1px solid rgba(0,83,122,0.06);">
                    <a href="{{ route('podcasts.index', $topic) }}" style="font-size:13px; font-weight:700; color:#00537A; text-decoration:none;">{{ $topic->name_ar }}</a>
                    <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
                        {!! $pill($topicStatusLabels[$tsVal] ?? $tsVal, $sc) !!}
                        <span style="font-size:11px; color:rgba(1,60,88,0.4);">{{ $topic->created_at?->format('Y-m-d') }}</span>
                    </div>
                </div>
            @empty
                <p style="margin:0; font-size:13px; color:rgba(1,60,88,0.45);">لم يقم الادمن بعمل بودكاست للان</p>
            @endforelse
        </div>
        @endif

        {{-- ============ LEVEL EXCEPTIONS ============ --}}
        @if ($admin->hasRole('super-admin', 'web'))
        <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:22px; box-shadow:0 14px 34px rgba(0,83,122,0.05);">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                <span style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:10px; background:rgba(255,138,101,0.14); color:#C2591A;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.3 3.9 1.8 18a1.5 1.5 0 0 0 1.3 2.2h17.8a1.5 1.5 0 0 0 1.3-2.2L13.7 3.9a1.5 1.5 0 0 0-2.6 0Z"></path></svg>
                </span>
                <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#013C58;">استثناءات مستوى نفّذها</p>
                <span style="margin-inline-start:auto; font-family:'Poppins',sans-serif; font-weight:800; font-size:13px; color:rgba(1,60,88,0.4);">{{ $activity['levelExceptions']->count() }}</span>
            </div>
            @forelse ($activity['levelExceptions'] as $exception)
                @php
                    $esVal = $exception->status instanceof \BackedEnum ? $exception->status->value : $exception->status;
                    $sc = $levelExceptionStatusColors[$esVal] ?? $levelExceptionStatusColors['pending'];
                @endphp
                <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; padding:11px 0; border-top:1px solid rgba(0,83,122,0.06);">
                    <a href="{{ route('levelException.show', $exception) }}" style="font-size:13px; font-weight:700; color:#00537A; text-decoration:none;">
                        {{ $exception->requestedLevel->name_ar ?? '—' }} ← {{ $exception->recommendedLevel->name_ar ?? '—' }}
                    </a>
                    <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
                        {!! $pill($levelExceptionStatusLabels[$esVal] ?? $esVal, $sc) !!}
                        <span style="font-size:11px; color:rgba(1,60,88,0.4);">{{ $exception->executed_at ? \Illuminate\Support\Carbon::parse($exception->executed_at)->format('Y-m-d') : '—' }}</span>
                    </div>
                </div>
            @empty
                <p style="margin:0; font-size:13px; color:rgba(1,60,88,0.45);">لم ينفذ الادمن أي طلب استثناء مستوى للان </p>
            @endforelse
        </div>
        @endif

        {{-- ============ PLACEMENT TESTS ============ --}}
        @if ($admin->can('manage_placement_tests', 'web'))
        <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:22px; box-shadow:0 14px 34px rgba(0,83,122,0.05);">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                <span style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:10px; background:rgba(255,211,91,0.22); color:#8A5A00;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M8 2v4M16 2v4M3 10h18"></path></svg>
                </span>
                <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#013C58;">اختبارات تحديد مستوى أنشأها</p>
                <span style="margin-inline-start:auto; font-family:'Poppins',sans-serif; font-weight:800; font-size:13px; color:rgba(1,60,88,0.4);">{{ $activity['placementTests']->count() }}</span>
            </div>
            @forelse ($activity['placementTests'] as $placementTest)
                @php
                    $latestTest = $placementTest->tests->sortByDesc('created_at')->first();
                    $ptsVal = $latestTest ? ($latestTest->status instanceof \BackedEnum ? $latestTest->status->value : $latestTest->status) : null;
                    $sc = $genericStatusColors[$ptsVal] ?? $genericStatusColors['pending'];
                @endphp
                <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; padding:11px 0; border-top:1px solid rgba(0,83,122,0.06);">
                    @if ($latestTest)
                        <a href="{{ route('tests.placement.placement.show', $latestTest) }}" style="font-size:13px; font-weight:700; color:#00537A; text-decoration:none;">{{ $latestTest->title_ar ?? $latestTest->title_en }}</a>
                    @else
                        <span style="font-size:13px; font-weight:700; color:rgba(1,60,88,0.5);">اختبار تحديد مستوى (بدون نسخة بعد)</span>
                    @endif
                    <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
                        @if ($latestTest)
                            {!! $pill($genericStatusLabels[$ptsVal] ?? $ptsVal, $sc) !!}
                        @endif
                        <span style="font-size:11px; color:rgba(1,60,88,0.4);">{{ $placementTest->created_at?->format('Y-m-d') }}</span>
                    </div>
                </div>
            @empty
                <p style="margin:0; font-size:13px; color:rgba(1,60,88,0.45);">لم يقم الادمن   أي اختبار تحديد مستوى للان </p>
            @endforelse
        </div>
        @endif

    </div>
</div>
@endsection
