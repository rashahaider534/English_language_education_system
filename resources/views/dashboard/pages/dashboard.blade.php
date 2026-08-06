@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes dashFadeUp { from { opacity:0; transform:translateY(12px);} to { opacity:1; transform:translateY(0);} }

    .db { --navy-900:#013C58; --navy-700:#00537A; --blue-500:#0E6A96; --sky-300:#A8E8F9; --sky-100:#EFFAFD;
          --orange-600:#F5A201; --orange-400:#FFBA42; --yellow-300:#FFD35B;
          --green-600:#1E8A57; --green-100:#E3F6EC;
          --ink:#0B2436; --muted:#5D7C8D; --muted-soft:#8FA6B3;
          --line:rgba(0,83,122,0.16); --bg:#DFF2F9; --card:#FBFEFF; --card-sky:#EFFAFD;
          --border-accent:rgba(14,106,150,.32); --border-accent-hover:rgba(14,106,150,.5);
          --glow-yellow:0 0 0 3px rgba(255,211,91,.22);
          --glow-yellow-hover:0 0 0 4px rgba(255,211,91,.32);
          --shadow-elev:0 16px 34px rgba(1,60,88,.10);
          --shadow-elev-hover:0 22px 44px rgba(1,60,88,.16);
          --shadow-lg:0 22px 48px rgba(1,60,88,.20);
          --r-lg:20px; --r-md:16px; --r-sm:12px;
          background:var(--bg); font-family:'Tajawal',sans-serif; min-height:100vh; color:var(--ink); }

    .db .num { font-family:'Poppins','Tajawal',sans-serif; }

    .db-card, .db-stat { border-radius:var(--r-lg); border:1.5px solid var(--border-accent); box-shadow:var(--glow-yellow), var(--shadow-elev); transition:box-shadow .2s ease, transform .2s ease, border-color .2s ease; animation:dashFadeUp .45s ease both; }
    .db-card { background:var(--card-sky); }
    .db-card:hover, .db-stat:hover { transform:translateY(-3px); border-color:var(--border-accent-hover); box-shadow:var(--glow-yellow-hover), var(--shadow-elev-hover); }
    .db-grid-4 > .db-card:nth-child(2) { animation-delay:.05s; }
    .db-grid-4 > .db-card:nth-child(3) { animation-delay:.1s; }
    .db-grid-4 > .db-card:nth-child(4) { animation-delay:.15s; }

    .db-stat { padding:20px 22px; }

    .db-stat-orange { background:radial-gradient(circle at 102% -12%, rgba(255,255,255,.8) 0%, rgba(255,255,255,0) 55%), linear-gradient(135deg,#FFFBF3 0%, #FFF3DC 55%, #FFE9C3 100%); }
    .db-stat-blue { background:radial-gradient(circle at 102% -12%, rgba(255,255,255,.7) 0%, rgba(255,255,255,0) 55%), linear-gradient(135deg,#E9F7FC 0%, #C9EAF7 55%, #A9DBF0 100%); }
    .db-stat-navy { background:radial-gradient(circle at 102% -12%, rgba(255,211,91,.45) 0%, rgba(255,211,91,0) 55%), linear-gradient(135deg,#E7F2F9 0%, #CBE1F0 55%, #A9CBE2 100%); }

    .db-icon-box { display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; flex-shrink:0; }

    .db-hero { background:linear-gradient(135deg,var(--navy-900) 0%, var(--navy-700) 55%, var(--blue-500) 130%); border:1.5px solid rgba(168,232,249,.35); box-shadow:var(--glow-yellow), var(--shadow-lg); position:relative; overflow:hidden; }
    .db-hero:hover { border-color:rgba(168,232,249,.55); }
    .db-hero::before { content:''; position:absolute; width:190px; height:190px; left:-60px; top:-70px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,.24) 0%, rgba(255,211,91,0) 70%); pointer-events:none; }
    .db-hero::after { content:''; position:absolute; width:140px; height:140px; right:-40px; bottom:-60px; border-radius:50%; background:radial-gradient(circle, rgba(168,232,249,.14) 0%, rgba(168,232,249,0) 70%); pointer-events:none; }

    .db-pill { display:inline-flex; align-items:center; gap:6px; padding:5px 11px; border-radius:999px; font-size:11.5px; font-weight:700; }
    .db-trend { display:inline-flex; align-items:center; gap:5px; font-size:11.5px; font-weight:700; color:var(--green-600); }

    .db-progress-track { height:6px; border-radius:999px; overflow:hidden; }
    .db-progress-fill { height:100%; border-radius:999px; transition:width .7s cubic-bezier(.16,1,.3,1); }

    .db-card-head { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:20px; }
    .db-card-title { display:flex; align-items:center; gap:7px; margin:0; font-family:'Poppins','Tajawal',sans-serif; font-weight:700; font-size:14.5px; color:var(--navy-900); }

    .db-menu-dot { display:flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:9px; color:var(--muted-soft); transition:background .15s ease; }
    .db-menu-dot:hover { background:rgba(1,60,88,.06); }

    .db-legend-row { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:8px 10px; border-radius:11px; transition:background .15s ease; }
    .db-legend-row:hover { background:rgba(168,232,249,.14); }

    .db-bars { display:flex; align-items:flex-end; gap:18px; height:196px; padding:4px 4px 0; overflow-x:auto; }
    .db-bar-col { display:flex; flex-direction:column; align-items:center; gap:8px; flex:1; min-width:56px; }
    .db-bar-fill { width:100%; max-width:38px; border-radius:10px 10px 4px 4px; transition:height .7s cubic-bezier(.16,1,.3,1); }

    .db-lb-row { display:flex; align-items:center; gap:12px; padding:12px 12px; border-radius:14px; background:rgba(255,255,255,.45); border:1px solid rgba(0,83,122,.07); margin-bottom:6px; transition:background .2s ease, transform .2s ease, border-color .2s ease; }
    .db-lb-row:hover { background:rgba(255,255,255,.85); transform:translateX(-2px); border-color:rgba(14,106,150,.2); }
    .db-lb-rank { display:flex; align-items:center; justify-content:center; width:24px; height:24px; border-radius:8px; font-family:'Poppins',sans-serif; font-weight:700; font-size:11.5px; color:#fff; flex-shrink:0; box-shadow:0 3px 8px rgba(1,60,88,.18); }
    .db-avatar { display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:50%; font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#fff; flex-shrink:0; box-shadow:0 3px 10px rgba(1,60,88,.16); }
    .db-lb-meta { display:flex; align-items:center; gap:5px; font-size:10.5px; font-weight:600; color:var(--muted); margin-top:5px; }
    .db-lb-row-empty { display:flex; align-items:center; gap:12px; padding:12px 12px; border-radius:14px; border:1.5px dashed rgba(0,83,122,.18); margin-bottom:6px; opacity:.6; }

    .db-status-row { display:flex; align-items:center; gap:12px; padding:11px 12px; border-radius:13px; transition:background .15s ease; }
    .db-status-row:hover { background:rgba(168,232,249,.14); }

    .db-status-row-v2 { padding:13px 14px; border-radius:14px; background:rgba(255,255,255,.5); border:1px solid rgba(0,83,122,.08); transition:background .2s ease, transform .2s ease; }
    .db-status-row-v2:hover { background:rgba(255,255,255,.85); transform:translateX(-2px); }
    .db-status-row-v2 .db-progress-fill { animation:dashGrow 1s cubic-bezier(.16,1,.3,1) both; }
    @keyframes dashGrow { from { width:0%; } }

    @media (max-width:900px) {
        .db-grid-2 { grid-template-columns:1fr !important; }
    }
</style>
@endpush

@section('content')
@php
    $lessonsStatusTotal = max(1, $lessonsDraft + $lessonsPending + $lessonsReviewed);
    $reviewedRowPct = round(($lessonsReviewed / $lessonsStatusTotal) * 100);
    $pendingRowPct = round(($lessonsPending / $lessonsStatusTotal) * 100);
    $draftRowPct = round(($lessonsDraft / $lessonsStatusTotal) * 100);

    $coursesTotal = max(1, $coursesPublished + $coursesPending + $coursesClosed);
    $pubPct = round(($coursesPublished / $coursesTotal) * 100);
    $pendPct = round(($coursesPending / $coursesTotal) * 100);
    $closedPct = max(0, 100 - $pubPct - $pendPct);

    $teacherRows = $coursesByTeacher->take(8);
    $maxTeacherCount = $teacherRows->max('count') ?: 1;

    $leaderboard = $topStudents->take(5)->values();
    $maxPoints = $leaderboard->max(fn ($u) => $u->studentProfile->points ?? 0) ?: 1;

    $rankStyles = [
        1 => ['badge' => '#F5A201', 'avatar' => 'linear-gradient(135deg,#F5A201,#FFD35B)', 'bar' => 'linear-gradient(90deg,#F5A201,#FFBA42)'],
        2 => ['badge' => '#8FA6B3', 'avatar' => 'linear-gradient(135deg,#8FA6B3,#B9C9D2)', 'bar' => 'linear-gradient(90deg,#0E6A96,#3F92B8)'],
        3 => ['badge' => '#B8703C', 'avatar' => 'linear-gradient(135deg,#B8703C,#D69A6C)', 'bar' => 'linear-gradient(90deg,#00537A,#0E6A96)'],
    ];
    $defaultRankStyle = ['badge' => '#013C58', 'avatar' => 'linear-gradient(135deg,#013C58,#0E6A96)', 'bar' => 'linear-gradient(90deg,#013C58,#0E6A96)'];

    $reviewPerf = $lessonsReviewedPct >= 75 ? 'أداء ممتاز هذا الأسبوع' : ($lessonsReviewedPct >= 50 ? 'أداء جيد، استمر' : 'بحاجة لمتابعة أكثر');
@endphp
<div class="db -mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" dir="rtl">

    <div style="margin-bottom:26px; text-align:center;">
        <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:24px; color:var(--navy-900);">مرحبًا بك، {{ $dashboardUser['name'] }}</h1>
        <span style="display:block; width:84px; height:4px; margin:12px auto 0; border-radius:999px; background:linear-gradient(90deg, var(--yellow-300), var(--blue-500));"></span>
    </div>

    {{-- ============ ROW 1: TOP STATS ============ --}}
    <div class="db-grid-4" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(230px, 1fr)); gap:18px; margin-bottom:20px;">

        <div class="db-card db-stat db-hero">
            <div style="position:relative; display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:14px;">
                <div class="db-icon-box" style="background:rgba(255,255,255,.14); color:var(--yellow-300);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"></circle><path d="M8.5 12.5 7 21l5-2.5L17 21l-1.5-8.5"></path></svg>
                </div>
                <p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(168,232,249,.85);">نسبة الدروس المراجعة</p>
            </div>
            <p style="position:relative; margin:0; font-size:31px; font-weight:800; color:#fff;" class="num">{{ $lessonsReviewedPct }}%</p>
            <p style="position:relative; margin:10px 0 0; display:flex; align-items:center; gap:6px; font-size:11.5px; font-weight:600; color:var(--yellow-300);">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                {{ $reviewPerf }}
            </p>
        </div>

        <div class="db-card db-stat db-stat-orange">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                <div class="db-icon-box" style="background:rgba(255,255,255,.6); color:#8A5A00;">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
                </div>
                <p style="margin:0; font-size:12px; font-weight:600; color:var(--muted);">دروس قيد المراجعة</p>
            </div>
            <p style="margin:0; font-size:27px; font-weight:800; color:var(--navy-900);" class="num">{{ $lessonsPending }}</p>
            <div class="db-progress-track" style="margin-top:12px; background:rgba(255,255,255,.55);">
                <div class="db-progress-fill" style="width:{{ round(($lessonsPending / $lessonsStatusTotal) * 100) }}%; background:var(--orange-600);"></div>
            </div>
        </div>

        <div class="db-card db-stat db-stat-blue">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                <div class="db-icon-box" style="background:rgba(255,255,255,.6); color:var(--blue-500);">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M3 10h18"></path></svg>
                </div>
                <p style="margin:0; font-size:12px; font-weight:600; color:var(--muted);">كورسات منشورة</p>
            </div>
            <p style="margin:0; font-size:27px; font-weight:800; color:var(--navy-900);" class="num">{{ $coursesPublished }}</p>
            <span class="db-pill" style="margin-top:12px; background:rgba(255,255,255,.6); color:var(--green-600);">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                {{ round(($coursesPublished / $coursesTotal) * 100) }}% من إجمالي الكورسات
            </span>
        </div>

        <div class="db-card db-stat db-stat-navy">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                <div class="db-icon-box" style="background:rgba(255,255,255,.6); color:var(--navy-700);">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7.5 12 3l10 4.5-10 4.5-10-4.5Z"></path><path d="M6 10v5c0 1.5 2.7 3 6 3s6-1.5 6-3v-5"></path></svg>
                </div>
                <p style="margin:0; font-size:12px; font-weight:600; color:var(--muted);">إجمالي الطلاب</p>
            </div>
            <p style="margin:0; font-size:27px; font-weight:800; color:var(--navy-900);" class="num">{{ number_format($totalStudents) }}</p>
            <p style="margin:12px 0 0;" class="db-trend">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"></path><path d="m5 12 7-7 7 7"></path></svg>
                +{{ $newStudentsThisMonth }} طالب جديد هالشهر
            </p>
        </div>
    </div>

    {{-- ============ ROW 2: DONUT + COURSES-PER-TEACHER BAR CHART ============ --}}
    <div class="db-grid-2" style="display:grid; grid-template-columns:0.85fr 1.15fr; gap:18px; margin-bottom:18px;">

        <div class="db-card" style="padding:22px;">
            <div class="db-card-head">
                <h3 class="db-card-title">توزيع الكورسات</h3>
                <span class="db-menu-dot">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="1.6"></circle><circle cx="12" cy="12" r="1.6"></circle><circle cx="19" cy="12" r="1.6"></circle></svg>
                </span>
            </div>
            <div style="display:flex; justify-content:center; margin-bottom:24px;">
                <div style="position:relative; width:150px; height:150px; flex-shrink:0; border-radius:50%; background:conic-gradient(var(--navy-900) 0% {{ $pubPct }}%, var(--sky-300) {{ $pubPct }}% {{ $pubPct + $pendPct }}%, rgba(1,60,88,0.10) {{ $pubPct + $pendPct }}% 100%);">
                    <div style="position:absolute; inset:20px; background:var(--card-sky); border-radius:50%; display:flex; flex-direction:column; align-items:center; justify-content:center; box-shadow:inset 0 0 0 1px var(--line);">
                        <span style="font-size:23px; font-weight:800; color:var(--navy-900);" class="num">{{ $coursesPublished + $coursesPending + $coursesClosed }}</span>
                        <span style="font-size:10.5px; color:var(--muted-soft); font-weight:600;">إجمالي</span>
                    </div>
                </div>
            </div>
            <div style="display:flex; flex-direction:column; gap:4px;">
                <div class="db-legend-row">
                    <span style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--navy-900); font-weight:600;"><span style="width:10px; height:10px; border-radius:50%; background:var(--navy-900); flex-shrink:0;"></span>منشورة</span>
                    <span class="num" style="font-weight:700; font-size:13px; color:var(--muted);">{{ $pubPct }}%</span>
                </div>
                <div class="db-legend-row">
                    <span style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--navy-900); font-weight:600;"><span style="width:10px; height:10px; border-radius:50%; background:var(--sky-300); flex-shrink:0;"></span>قيد الإنشاء</span>
                    <span class="num" style="font-weight:700; font-size:13px; color:var(--muted);">{{ $pendPct }}%</span>
                </div>
                <div class="db-legend-row">
                    <span style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--navy-900); font-weight:600;"><span style="width:10px; height:10px; border-radius:50%; background:rgba(1,60,88,0.22); flex-shrink:0;"></span>مغلقة / مؤرشفة</span>
                    <span class="num" style="font-weight:700; font-size:13px; color:var(--muted);">{{ $closedPct }}%</span>
                </div>
            </div>
        </div>

        <div class="db-card" style="padding:22px;">
            <div class="db-card-head">
                <h3 class="db-card-title">كورسات قيد الإنشاء حسب الأستاذ</h3>
                <span class="db-pill" style="background:var(--sky-100); color:var(--navy-700);">أعلى {{ $teacherRows->count() }} أساتذة</span>
            </div>
            @if ($teacherRows->isEmpty())
                <p style="text-align:center; color:var(--muted-soft); font-size:12.5px; padding:60px 0;">ما في كورسات قيد الإنشاء حاليًا</p>
            @else
                <div class="db-bars">
                    @foreach ($teacherRows as $row)
                        @php
                            $teacher = $row['teacher'];
                            $teacherName = $teacher ? (trim(($teacher->first_name ?? '').' '.($teacher->last_name ?? '')) ?: $teacher->email) : 'بدون أستاذ';
                            $initial = $teacher ? strtoupper(substr($teacher->first_name ?? $teacher->email, 0, 1)) : '؟';
                            $barHeight = max(14, round(($row['count'] / $maxTeacherCount) * 140));
                        @endphp
                        <div class="db-bar-col">
                            <span class="num" style="font-weight:700; font-size:12px; color:var(--navy-900);">{{ $row['count'] }}</span>
                            <div class="db-bar-fill" style="background:linear-gradient(180deg,var(--orange-400),var(--orange-600)); height:{{ $barHeight }}px;"></div>
                            <div class="db-avatar" style="width:26px; height:26px; font-size:10.5px; background:var(--navy-700);">{{ $initial }}</div>
                            <span style="font-size:10.5px; color:var(--muted); font-weight:600; text-align:center; max-width:72px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $teacherName }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ============ ROW 3: TOP STUDENTS LEADERBOARD + LESSON STATUS ============ --}}
    <div class="db-grid-2" style="display:grid; grid-template-columns:1.15fr 0.85fr; gap:18px;">

        <div class="db-card" style="padding:22px;">
            <div class="db-card-head" style="margin-bottom:14px;">
                <h3 class="db-card-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--orange-600)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v2"></path><path d="M12 20a7 7 0 0 0 7-7c0-2-1-3-1-5a6 6 0 0 0-12 0c0 2-1 3-1 5a7 7 0 0 0 7 7Z"></path></svg>
                    أفضل 5 طلاب
                </h3>
                @if (Route::has('admin.students.index'))
                    <a href="{{ route('admin.students.index') }}" style="font-size:12px; font-weight:600; color:var(--blue-500); text-decoration:none;">عرض الكل</a>
                @endif
            </div>
            @forelse ($leaderboard as $i => $student)
                @php
                    $rank = $i + 1;
                    $points = $student->studentProfile->points ?? 0;
                    $streak = $student->studentProfile->streak ?? 0;
                    $studentName = trim(($student->first_name ?? '').' '.($student->last_name ?? '')) ?: $student->email;
                    $initial = strtoupper(substr($student->first_name ?? $student->email, 0, 1));
                    $style = $rankStyles[$rank] ?? $defaultRankStyle;
                    $barPct = max(6, round(($points / $maxPoints) * 100));
                    $medals = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
                @endphp
                <div class="db-lb-row">
                    <span class="db-lb-rank" style="background:{{ $style['badge'] }};">{{ $medals[$rank] ?? $rank }}</span>
                    <div class="db-avatar" style="background:{{ $style['avatar'] }};">{{ $initial }}</div>
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:baseline; justify-content:space-between; gap:8px; margin-bottom:6px;">
                            <span style="font-size:13px; font-weight:700; color:var(--navy-900); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $studentName }}</span>
                            <span class="num" dir="ltr" style="font-size:11.5px; font-weight:700; color:var(--orange-600); flex-shrink:0;">XP {{ number_format($points) }}</span>
                        </div>
                        <div class="db-progress-track" style="background:rgba(1,60,88,.08);">
                            <div class="db-progress-fill" style="width:{{ $barPct }}%; background:{{ $style['bar'] }};"></div>
                        </div>
                        <div class="db-lb-meta">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--orange-600)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2c1 3-2 4-2 7a4 4 0 0 0 8 0c0-1-.5-2-1-2 .5 2-1 3-2 3-2 0-2-2-1-3-3 0-3-3-2-5Z"></path></svg>
                            <span>{{ $streak }} يوم متتالي</span>
                            <span style="opacity:.5;">•</span>
                            <span>{{ $barPct }}% من الأعلى نقاطًا</span>
                        </div>
                    </div>
                </div>
            @empty
                <p style="text-align:center; color:var(--muted-soft); font-size:12.5px; padding:40px 0;">ما في طلاب حاليًا</p>
            @endforelse
            @for ($p = $leaderboard->count() + 1; $p <= 5; $p++)
                <div class="db-lb-row-empty">
                    <span class="db-lb-rank" style="background:rgba(1,60,88,.14); color:var(--muted-soft); box-shadow:none;">{{ $p }}</span>
                    <div class="db-avatar" style="background:rgba(1,60,88,.08); color:var(--muted-soft); box-shadow:none;">؟</div>
                    <span style="flex:1; font-size:12.5px; font-weight:600; color:var(--muted-soft);">بانتظار طالب جديد ينضم للمنافسة</span>
                </div>
            @endfor
        </div>

        <div class="db-card" style="padding:22px;">
            <h3 class="db-card-title" style="margin-bottom:16px;">حالة الدروس</h3>
            <div style="display:flex; flex-direction:column; gap:6px;">
                <div class="db-status-row">
                    <div class="db-icon-box" style="background:var(--green-100); color:var(--green-600);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                    </div>
                    <span style="flex:1; font-size:13px; font-weight:600; color:var(--navy-900);">دروس تمّت مراجعتها</span>
                    <span class="num" style="font-weight:800; font-size:15px; color:var(--navy-900);">{{ $lessonsReviewed }}</span>
                </div>
                <div class="db-status-row">
                    <div class="db-icon-box" style="background:rgba(255,186,66,.16); color:#8A5A00;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
                    </div>
                    <span style="flex:1; font-size:13px; font-weight:600; color:var(--navy-900);">دروس بانتظار المراجعة</span>
                    <span class="num" style="font-weight:800; font-size:15px; color:var(--navy-900);">{{ $lessonsPending }}</span>
                </div>
                <div class="db-status-row">
                    <div class="db-icon-box" style="background:rgba(1,60,88,.07); color:var(--muted);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2Z"></path></svg>
                    </div>
                    <span style="flex:1; font-size:13px; font-weight:600; color:var(--navy-900);">مسوّدات</span>
                    <span class="num" style="font-weight:800; font-size:15px; color:var(--navy-900);">{{ $lessonsDraft }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
