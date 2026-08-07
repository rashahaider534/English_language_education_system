@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes dashFadeUp { from { opacity:0; transform:translateY(12px);} to { opacity:1; transform:translateY(0);} }

    .db2 { --navy-900:#013C58; --navy-700:#00537A; --blue-500:#0E6A96; --sky-300:#A8E8F9; --sky-100:#EAF6FC;
          --orange-600:#F5A201; --orange-400:#FFBA42; --yellow-300:#FFD35B;
          --green-600:#1E8A57; --green-100:#E3F6EC;
          --ink:#0B2436; --muted:#5D7C8D; --muted-soft:#8FA6B3;
          --line:rgba(0,83,122,0.08); --bg:#DFF2F9; --card:#FFFFFF;
          --shadow-sm:0 1px 2px rgba(2,32,71,.04), 0 1px 1px rgba(2,32,71,.03);
          --shadow-md:0 14px 30px rgba(2,32,71,.07);
          --shadow-lg:0 22px 48px rgba(1,60,88,.18);
          --r-lg:20px; --r-md:16px;
          background:var(--bg); font-family:'Tajawal',sans-serif; min-height:100vh; color:var(--ink); }

    .db2 .num { font-family:'Poppins','Tajawal',sans-serif; }

    .db2-card { background:var(--card); border:1px solid var(--line); border-radius:var(--r-lg); box-shadow:var(--shadow-sm); transition:box-shadow .2s ease, transform .2s ease; animation:dashFadeUp .45s ease both; }
    .db2-card:hover { box-shadow:var(--shadow-md); }
    .db2-grid-4 > .db2-card:nth-child(2) { animation-delay:.05s; }
    .db2-grid-4 > .db2-card:nth-child(3) { animation-delay:.1s; }
    .db2-grid-4 > .db2-card:nth-child(4) { animation-delay:.15s; }

    .db2-stat { padding:20px 22px; }
    .db2-stat:hover { transform:translateY(-3px); }

    .db2-icon-circle { display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:50%; flex-shrink:0; }

    .db2-stat-cream { background:linear-gradient(135deg,#FFF8E9 0%, #FFEBC3 100%); }
    .db2-stat-blue { background:linear-gradient(135deg,#EAF6FC 0%, #CDEAF7 100%); }
    .db2-stat-orange { background:linear-gradient(135deg,#FFF3E3 0%, #FFDDB0 100%); }
    .db2-stat-green { background:linear-gradient(135deg,#E9FBF3 0%, #C9F0DC 100%); }

    .db2-date-pill { display:inline-flex; align-items:center; gap:8px; background:#fff; border:1px solid var(--line); border-radius:999px; padding:10px 18px; box-shadow:var(--shadow-sm); font-size:13px; font-weight:700; color:var(--navy-900); }

    .db2-card-head { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:18px; }
    .db2-card-title { display:flex; align-items:center; gap:7px; margin:0; font-family:'Poppins','Tajawal',sans-serif; font-weight:700; font-size:14.5px; color:var(--navy-900); }

    .db2-legend-dot { display:inline-block; width:9px; height:9px; border-radius:50%; flex-shrink:0; }

    .db2-mini-bar-col { display:flex; flex-direction:column; align-items:center; gap:6px; flex:1; min-width:0; }
    .db2-mini-bar-fill { width:100%; max-width:34px; border-radius:8px 8px 3px 3px; transition:height .7s cubic-bezier(.16,1,.3,1); }

    .db2-activity-row { display:flex; align-items:flex-start; gap:10px; padding:11px 0; }
    .db2-activity-row + .db2-activity-row { border-top:1px solid var(--line); }
    .db2-activity-dot { width:9px; height:9px; border-radius:50%; margin-top:5px; flex-shrink:0; }

    .db2-list-row { display:flex; align-items:center; gap:10px; padding:10px 0; }
    .db2-list-row + .db2-list-row { border-top:1px solid var(--line); }
    .db2-avatar { display:flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:50%; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; color:#fff; flex-shrink:0; }

    .db2-footer-link { display:flex; align-items:center; gap:6px; justify-content:center; margin-top:12px; padding-top:12px; border-top:1px solid var(--line); font-size:12.5px; font-weight:700; color:var(--blue-500); text-decoration:none; }

    @media (max-width:900px) {
        .db2-grid-2 { grid-template-columns:1fr !important; }
        .db2-grid-3 { grid-template-columns:1fr !important; }
    }
</style>
@endpush

@section('content')
@php
    // ---- sparkline helper: turns a small list of real numbers into an SVG polyline ----
    $db2Sparkline = function (array $values, int $w = 140, int $h = 40): string {
        $max = max(1, max($values));
        $min = min($values);
        $range = max(1, $max - $min);
        $n = count($values);
        $pts = [];
        foreach ($values as $i => $v) {
            $x = $n > 1 ? ($i / ($n - 1)) * $w : 0;
            $y = $h - ((($v - $min) / $range) * ($h - 6)) - 3;
            $pts[] = round($x, 1).','.round($y, 1);
        }
        return implode(' ', $pts);
    };

    $studentsSpark = $db2Sparkline([max(0, $totalStudents - $newStudentsThisMonth), $totalStudents]);
    $coursesSpark = $db2Sparkline([$coursesPublished - ($coursesPublishedGrowthPct > 0 ? 1 : 0), $coursesPublished]);
    $pendingSpark = $db2Sparkline($trendDays->pluck('pending')->all());

    // ---- main 7-day line chart geometry ----
    $chartW = 640; $chartH = 170; $chartX0 = 10; $chartBottom = $chartH - 20; $chartTop = 10;
    $allVals = $trendDays->flatMap(fn ($d) => [$d['published'], $d['pending'], $d['reviewed']]);
    $chartMax = max(1, $allVals->max());
    $n = $trendDays->count();
    $seriesLine = function (string $key) use ($trendDays, $n, $chartX0, $chartW, $chartBottom, $chartTop, $chartMax) {
        $pts = [];
        foreach ($trendDays->values() as $i => $row) {
            $x = $n > 1 ? $chartX0 + ($i * (($chartW - $chartX0) / ($n - 1))) : $chartX0;
            $y = $chartBottom - (($row[$key] / $chartMax) * ($chartBottom - $chartTop));
            $pts[] = round($x, 1).','.round($y, 1);
        }
        return implode(' ', $pts);
    };
    $publishedLine = $seriesLine('published');
    $pendingLine = $seriesLine('pending');
    $reviewedLine = $seriesLine('reviewed');

    // ---- donut geometry (courses by status) ----
    $coursesTotal = max(1, $coursesPublished + $coursesPending + $coursesClosed);
    $pubPct = round(($coursesPublished / $coursesTotal) * 100);
    $pendPct = round(($coursesPending / $coursesTotal) * 100);
    $closedPct = max(0, 100 - $pubPct - $pendPct);

    $maxTeacherLessons = max(1, $lessonsByTeacher->max('count') ?? 1);

    $urgencyStyles = [
        'high' => ['label' => 'أولوية عالية', 'bg' => 'rgba(255,138,101,0.16)', 'fg' => '#C2591A'],
        'medium' => ['label' => 'أولوية متوسطة', 'bg' => 'rgba(255,186,66,0.18)', 'fg' => '#8A5A00'],
        'low' => ['label' => 'أولوية منخفضة', 'bg' => 'rgba(1,60,88,0.07)', 'fg' => 'var(--muted)'],
    ];

    $activityStyles = [
        'lesson_published' => ['dot' => '#4CAF78'],
        'course_created' => ['dot' => '#0E6A96'],
        'lesson_pending' => ['dot' => '#F5A201'],
    ];

    $reviewPerf = $lessonsReviewedPct >= 75 ? 'أداء ممتاز هذا الأسبوع' : ($lessonsReviewedPct >= 50 ? 'أداء جيد، استمر' : 'بحاجة لمتابعة أكثر');
    $recentStudents = $topStudents->sortByDesc(fn ($u) => $u->created_at)->take(3)->values();
@endphp
<div class="db2 -mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" dir="rtl">

    <div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:14px; margin-bottom:24px;">
        <div>
            <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:var(--navy-900);">مرحبًا بك، {{ $dashboardUser['name'] }}</h1>
            <p style="margin:10px 0 0; font-size:13px; color:var(--muted); font-weight:600;">نظرة عامة على أداء النظام</p>
        </div>
        <span class="db2-date-pill">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--orange-600)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M3 10h18"></path><path d="M8 2v4M16 2v4"></path></svg>
            {{ now()->translatedFormat('j F Y') }}
        </span>
    </div>

    {{-- ============ ROW 1: TOP STATS ============ --}}
    <div class="db2-grid-4" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(230px, 1fr)); gap:18px; margin-bottom:20px;">

        <div class="db2-card db2-stat db2-stat-blue">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:14px;">
                <p style="margin:0; font-size:12.5px; font-weight:700; color:var(--navy-900);">إجمالي الطلاب</p>
                <div class="db2-icon-circle" style="background:rgba(255,255,255,.65); color:var(--blue-500);">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7.5 12 3l10 4.5-10 4.5-10-4.5Z"></path><path d="M6 10v5c0 1.5 2.7 3 6 3s6-1.5 6-3v-5"></path></svg>
                </div>
            </div>
            <p style="margin:0; font-size:27px; font-weight:800; color:var(--navy-900);" class="num">{{ number_format($totalStudents) }}</p>
            <p style="margin:6px 0 10px; display:flex; align-items:center; gap:5px; font-size:11px; font-weight:700; color:{{ $studentsGrowthPct >= 0 ? 'var(--green-600)' : '#C2591A' }};">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">@if($studentsGrowthPct>=0)<path d="M12 19V5"></path><path d="m5 12 7-7 7 7"></path>@else<path d="M12 5v14"></path><path d="m5 12 7 7 7-7"></path>@endif</svg>
                {{ $studentsGrowthPct >= 0 ? '+' : '' }}{{ $studentsGrowthPct }}% من الشهر الماضي
            </p>
            <svg width="100%" height="34" viewBox="0 0 140 40" preserveAspectRatio="none"><polyline points="{{ $studentsSpark }}" fill="none" stroke="var(--blue-500)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>

        <div class="db2-card db2-stat db2-stat-cream">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:14px;">
                <p style="margin:0; font-size:12.5px; font-weight:700; color:var(--navy-900);">كورسات منشورة</p>
                <div class="db2-icon-circle" style="background:rgba(255,255,255,.65); color:#8A5A00;">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5V5.8c0-.9.6-1.6 1.4-1.8L18 2v16.5"></path><path d="M18 18.5H6a2 2 0 0 0-2 2"></path></svg>
                </div>
            </div>
            <p style="margin:0; font-size:27px; font-weight:800; color:var(--navy-900);" class="num">{{ number_format($coursesPublished) }}</p>
            <p style="margin:6px 0 10px; display:flex; align-items:center; gap:5px; font-size:11px; font-weight:700; color:{{ $coursesPublishedGrowthPct >= 0 ? 'var(--green-600)' : '#C2591A' }};">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">@if($coursesPublishedGrowthPct>=0)<path d="M12 19V5"></path><path d="m5 12 7-7 7 7"></path>@else<path d="M12 5v14"></path><path d="m5 12 7 7 7-7"></path>@endif</svg>
                {{ $coursesPublishedGrowthPct >= 0 ? '+' : '' }}{{ $coursesPublishedGrowthPct }}% من الشهر الماضي
            </p>
            <svg width="100%" height="34" viewBox="0 0 140 40" preserveAspectRatio="none"><polyline points="{{ $coursesSpark }}" fill="none" stroke="#C1650A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>

        <div class="db2-card db2-stat db2-stat-orange">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:14px;">
                <p style="margin:0; font-size:12.5px; font-weight:700; color:var(--navy-900);">دروس قيد المراجعة</p>
                <div class="db2-icon-circle" style="background:rgba(255,255,255,.65); color:#B25E00;">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
                </div>
            </div>
            <p style="margin:0; font-size:27px; font-weight:800; color:var(--navy-900);" class="num">{{ number_format($lessonsPending) }}</p>
            <p style="margin:6px 0 10px; display:flex; align-items:center; gap:5px; font-size:11px; font-weight:700; color:{{ $lessonsPendingGrowthPct >= 0 ? '#C2591A' : 'var(--green-600)' }};">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">@if($lessonsPendingGrowthPct>=0)<path d="M12 19V5"></path><path d="m5 12 7-7 7 7"></path>@else<path d="M12 5v14"></path><path d="m5 12 7 7 7-7"></path>@endif</svg>
                {{ $lessonsPendingGrowthPct >= 0 ? '+' : '' }}{{ $lessonsPendingGrowthPct }}% من الشهر الماضي
            </p>
            <svg width="100%" height="34" viewBox="0 0 140 40" preserveAspectRatio="none"><polyline points="{{ $pendingSpark }}" fill="none" stroke="#F5A201" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>

        <div class="db2-card db2-stat db2-stat-green">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:10px;">
                <p style="margin:0; font-size:12.5px; font-weight:700; color:var(--navy-900);">نسبة الدروس المراجعة</p>
                <div class="db2-icon-circle" style="background:rgba(255,255,255,.65); color:var(--green-600);">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
                </div>
            </div>
            <p style="margin:0; font-size:27px; font-weight:800; color:var(--navy-900);" class="num">{{ $lessonsReviewedPct }}%</p>
            <div style="height:6px; border-radius:999px; background:rgba(30,138,87,.14); overflow:hidden; margin:10px 0 8px;">
                <div style="height:100%; width:{{ $lessonsReviewedPct }}%; background:var(--green-600); border-radius:999px;"></div>
            </div>
            <p style="margin:0; display:flex; align-items:center; gap:5px; font-size:11px; font-weight:700; color:var(--green-600);">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                {{ $reviewPerf }}
            </p>
        </div>
    </div>

    {{-- ============ ROW 2: LINE CHART + DONUT ============ --}}
    <div class="db2-grid-2" style="display:grid; grid-template-columns:1.4fr 1fr; gap:18px; margin-bottom:18px;">

        <div class="db2-card" style="padding:22px;">
            <div class="db2-card-head">
                <h3 class="db2-card-title">نظرة عامة على الأداء</h3>
                <span style="font-size:11.5px; font-weight:700; color:var(--muted); background:var(--sky-100); padding:6px 12px; border-radius:999px;">آخر 7 أيام</span>
            </div>
            <div style="display:flex; gap:16px; margin-bottom:14px; flex-wrap:wrap;">
                <span style="display:flex; align-items:center; gap:6px; font-size:11.5px; font-weight:600; color:var(--muted);"><span class="db2-legend-dot" style="background:var(--blue-500);"></span>دروس منشورة</span>
                <span style="display:flex; align-items:center; gap:6px; font-size:11.5px; font-weight:600; color:var(--muted);"><span class="db2-legend-dot" style="background:var(--orange-600);"></span>دروس قيد المراجعة</span>
                <span style="display:flex; align-items:center; gap:6px; font-size:11.5px; font-weight:600; color:var(--muted);"><span class="db2-legend-dot" style="background:var(--green-600);"></span>دروس تمّت مراجعتها</span>
            </div>
            <div style="overflow-x:auto;">
                <svg viewBox="0 0 {{ $chartW }} {{ $chartH }}" style="width:100%; height:180px; min-width:480px;" preserveAspectRatio="none">
                    @for ($g = 0; $g <= 4; $g++)
                        <line x1="0" x2="{{ $chartW }}" y1="{{ $chartTop + ($g * (($chartBottom-$chartTop)/4)) }}" y2="{{ $chartTop + ($g * (($chartBottom-$chartTop)/4)) }}" stroke="rgba(0,83,122,0.06)" stroke-width="1"/>
                    @endfor
                    <polyline points="{{ $publishedLine }}" fill="none" stroke="var(--blue-500)" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <polyline points="{{ $pendingLine }}" fill="none" stroke="var(--orange-600)" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <polyline points="{{ $reviewedLine }}" fill="none" stroke="var(--green-600)" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div style="display:flex; justify-content:space-between; padding:0 4px; min-width:480px;">
                    @foreach ($trendDays as $row)
                        <span style="font-size:10.5px; color:var(--muted-soft); font-weight:600;">{{ $row['label'] }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="db2-card" style="padding:22px;">
            <div class="db2-card-head">
                <h3 class="db2-card-title">توزيع الكورسات حسب الحالة</h3>
            </div>
            <div style="display:flex; justify-content:center; margin-bottom:22px;">
                <div style="position:relative; width:150px; height:150px; flex-shrink:0; border-radius:50%; background:conic-gradient(var(--navy-900) 0% {{ $pubPct }}%, var(--blue-500) {{ $pubPct }}% {{ $pubPct + $pendPct }}%, rgba(1,60,88,0.12) {{ $pubPct + $pendPct }}% 100%);">
                    <div style="position:absolute; inset:20px; background:#fff; border-radius:50%; display:flex; flex-direction:column; align-items:center; justify-content:center; box-shadow:inset 0 0 0 1px var(--line);">
                        <span style="font-size:23px; font-weight:800; color:var(--navy-900);" class="num">{{ $coursesPublished + $coursesPending + $coursesClosed }}</span>
                        <span style="font-size:10.5px; color:var(--muted-soft); font-weight:600;">إجمالي</span>
                    </div>
                </div>
            </div>
            <div style="display:flex; flex-direction:column; gap:4px;">
                <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 10px;">
                    <span style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--navy-900); font-weight:600;"><span class="db2-legend-dot" style="background:var(--navy-900);"></span>منشورة</span>
                    <span class="num" style="font-weight:700; font-size:13px; color:var(--muted);">{{ $coursesPublished }} ({{ $pubPct }}%)</span>
                </div>
                <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 10px;">
                    <span style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--navy-900); font-weight:600;"><span class="db2-legend-dot" style="background:var(--blue-500);"></span>قيد الإنشاء</span>
                    <span class="num" style="font-weight:700; font-size:13px; color:var(--muted);">{{ $coursesPending }} ({{ $pendPct }}%)</span>
                </div>
                <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 10px;">
                    <span style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--navy-900); font-weight:600;"><span class="db2-legend-dot" style="background:rgba(1,60,88,0.25);"></span>مغلقة / مؤرشفة</span>
                    <span class="num" style="font-weight:700; font-size:13px; color:var(--muted);">{{ $coursesClosed }} ({{ $closedPct }}%)</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ ROW 3: LESSON TILES + TEACHER BAR CHART ============ --}}
    <div class="db2-grid-2" style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:18px;">

        <div class="db2-card" style="padding:22px;">
            <div class="db2-card-head">
                <h3 class="db2-card-title">إجمالي الدروس</h3>
            </div>
            <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:14px;">
                @foreach ([
                    ['label' => 'إجمالي الدروس', 'value' => $lessonsTotalAll, 'icon' => 'M4 19.5V5.8c0-.9.6-1.6 1.4-1.8L18 2v16.5', 'bg' => 'var(--sky-100)', 'fg' => 'var(--blue-500)'],
                    ['label' => 'دروس منشورة', 'value' => $lessonsPublished, 'icon' => 'M20 6 9 17l-5-5', 'bg' => 'var(--green-100)', 'fg' => 'var(--green-600)'],
                    ['label' => 'دروس قيد المراجعة', 'value' => $lessonsPending, 'icon' => 'M12 8v4l3 3', 'bg' => '#FFF3E3', 'fg' => '#B25E00'],
                    ['label' => 'مؤرشفة', 'value' => $lessonsArchived, 'icon' => 'M3 10h18', 'bg' => 'rgba(1,60,88,0.06)', 'fg' => 'var(--muted)'],
                ] as $tile)
                    <div style="background:rgba(2,32,71,0.02); border:1px solid var(--line); border-radius:14px; padding:14px;">
                        <div class="db2-icon-circle" style="width:34px; height:34px; background:{{ $tile['bg'] }}; color:{{ $tile['fg'] }}; margin-bottom:10px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $tile['icon'] }}"></path></svg>
                        </div>
                        <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:19px; color:var(--navy-900);" class="num">{{ $tile['value'] }}</p>
                        <p style="margin:4px 0 0; font-size:11px; color:var(--muted); font-weight:600;">{{ $tile['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="db2-card" style="padding:22px;">
            <div class="db2-card-head">
                <h3 class="db2-card-title">الدروس حسب الأستاذ</h3>
            </div>
            @if ($lessonsByTeacher->isEmpty())
                <p style="text-align:center; color:var(--muted-soft); font-size:12.5px; padding:50px 0;">ما في دروس مرتبطة بأساتذة حاليًا</p>
            @else
                <div style="display:flex; align-items:flex-end; gap:16px; height:170px; padding:4px 4px 0;">
                    @foreach ($lessonsByTeacher as $row)
                        @php
                            $teacherName = $row['teacher'] ? (trim(($row['teacher']->first_name ?? '').' '.($row['teacher']->last_name ?? '')) ?: $row['teacher']->email) : 'بدون أستاذ';
                            $barHeight = max(14, round(($row['count'] / $maxTeacherLessons) * 120));
                        @endphp
                        <div class="db2-mini-bar-col">
                            <span class="num" style="font-weight:700; font-size:13px; color:var(--navy-900);">{{ $row['count'] }}</span>
                            <div class="db2-mini-bar-fill" style="background:linear-gradient(180deg,var(--blue-500),var(--navy-900)); height:{{ $barHeight }}px;"></div>
                            <span style="font-size:10.5px; color:var(--muted); font-weight:600; text-align:center; max-width:80px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $teacherName }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ============ ROW 4: RECENT STUDENTS + PENDING LESSONS + ACTIVITY ============ --}}
    <div class="db2-grid-3" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:18px;">

        <div class="db2-card" style="padding:22px;">
            <div class="db2-card-head">
                <h3 class="db2-card-title">أحدث الطلاب</h3>
            </div>
            @forelse ($recentStudents as $student)
                @php
                    $sName = trim(($student->first_name ?? '').' '.($student->last_name ?? '')) ?: $student->email;
                    $sInitial = strtoupper(substr($student->first_name ?? $student->email, 0, 1));
                @endphp
                <div class="db2-list-row">
                    <div class="db2-avatar" style="background:var(--navy-700);">{{ $sInitial }}</div>
                    <div style="flex:1; min-width:0;">
                        <p style="margin:0; font-size:13px; font-weight:700; color:var(--navy-900); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $sName }}</p>
                        <p style="margin:2px 0 0; font-size:11px; color:var(--muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $student->email }}</p>
                    </div>
                    <span style="font-size:10.5px; color:var(--muted-soft); font-weight:600; flex-shrink:0;">{{ $student->created_at->diffForHumans(null, true) }}</span>
                </div>
            @empty
                <p style="text-align:center; color:var(--muted-soft); font-size:12.5px; padding:30px 0;">ما في طلاب حاليًا</p>
            @endforelse
            @if (Route::has('admin.students.index'))
                <a href="{{ route('admin.students.index') }}" class="db2-footer-link">
                    عرض جميع الطلاب
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
                </a>
            @endif
        </div>

        <div class="db2-card" style="padding:22px;">
            <div class="db2-card-head">
                <h3 class="db2-card-title">الدروس قيد المراجعة</h3>
            </div>
            @forelse ($pendingLessonsList as $row)
                @php $u = $urgencyStyles[$row['urgency']]; @endphp
                <div class="db2-list-row" style="align-items:flex-start;">
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:5px;">
                            <p style="margin:0; font-size:12.5px; font-weight:700; color:var(--navy-900); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $row['lesson']->title_ar }}</p>
                            <span style="flex-shrink:0; display:inline-flex; padding:3px 9px; border-radius:999px; background:{{ $u['bg'] }}; color:{{ $u['fg'] }}; font-size:10px; font-weight:700;">{{ $u['label'] }}</span>
                        </div>
                        <p style="margin:0; font-size:11px; color:var(--muted);">{{ $row['lesson']->course->level->name_ar ?? '—' }} · منذ {{ $row['days_pending'] }} يوم</p>
                    </div>
                </div>
            @empty
                <p style="text-align:center; color:var(--muted-soft); font-size:12.5px; padding:30px 0;">ما في دروس بانتظار المراجعة</p>
            @endforelse
            <a href="{{ route('lessons.pending') }}" class="db2-footer-link">
                عرض جميع الدروس
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            </a>
        </div>

        <div class="db2-card" style="padding:22px;">
            <div class="db2-card-head">
                <h3 class="db2-card-title">النشاط الأخير</h3>
            </div>
            @forelse ($recentActivity as $event)
                <div class="db2-activity-row">
                    <span class="db2-activity-dot" style="background:{{ $activityStyles[$event['type']]['dot'] }};"></span>
                    <div style="flex:1; min-width:0;">
                        <p style="margin:0; font-size:12.5px; font-weight:700; color:var(--navy-900);">{{ $event['label'] }}</p>
                        <p style="margin:3px 0 0; font-size:11px; color:var(--muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $event['detail'] }}</p>
                    </div>
                    <span style="font-size:10px; color:var(--muted-soft); font-weight:600; flex-shrink:0;">{{ $event['at']->diffForHumans(null, true) }}</span>
                </div>
            @empty
                <p style="text-align:center; color:var(--muted-soft); font-size:12.5px; padding:30px 0;">ما في نشاط حديث</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
