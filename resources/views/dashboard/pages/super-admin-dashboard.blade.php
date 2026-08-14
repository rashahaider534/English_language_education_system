@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes saFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }

    .sa { --navy-900:#013C58; --navy-700:#00537A; --blue-500:#0E6A96; --sky-300:#A8E8F9; --sky-100:#EAF6FC;
          --orange-600:#F5A201; --orange-400:#FFBA42; --yellow-300:#FFD35B;
          --green-600:#1E8A57; --green-100:#E3F6EC;
          --ink:#0B2436; --muted:#5D7C8D; --muted-soft:#8FA6B3;
          --line:rgba(0,83,122,0.08); --bg:#DFF2F9; --card:#FFFFFF;
          --shadow-sm:0 1px 2px rgba(2,32,71,.04), 0 1px 1px rgba(2,32,71,.03);
          --shadow-md:0 14px 30px rgba(2,32,71,.07);
          --shadow-lg:0 22px 48px rgba(1,60,88,.18);
          --r-lg:20px; --r-md:16px;
          background:var(--bg); font-family:'Tajawal',sans-serif; min-height:100vh; color:var(--ink); }

    .sa .num { font-family:'Poppins','Tajawal',sans-serif; }

    .sa-card { background:var(--card); border:1px solid var(--line); border-radius:var(--r-lg); box-shadow:var(--shadow-sm); transition:box-shadow .2s ease, transform .2s ease; animation:saFadeUp .45s ease both; }
    .sa-card:hover { box-shadow:var(--shadow-md); }
    .sa-grid-4 > .sa-card:nth-child(2) { animation-delay:.05s; }
    .sa-grid-4 > .sa-card:nth-child(3) { animation-delay:.1s; }
    .sa-grid-4 > .sa-card:nth-child(4) { animation-delay:.15s; }

    .sa-stat { padding:20px 22px; }
    .sa-stat:hover { transform:translateY(-3px); }

    .sa-icon-circle { display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:50%; flex-shrink:0; }

    .sa-hero { background:linear-gradient(135deg,var(--navy-900) 0%, var(--navy-700) 55%, var(--blue-500) 130%); border:none; box-shadow:var(--shadow-lg); position:relative; overflow:hidden; }
    .sa-hero::before { content:''; position:absolute; width:180px; height:180px; left:-55px; top:-65px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,.22) 0%, rgba(255,211,91,0) 70%); pointer-events:none; }

    .sa-stat-cream { background:linear-gradient(135deg,#FFF8E9 0%, #FFEBC3 100%); }
    .sa-stat-blue { background:linear-gradient(135deg,#EAF6FC 0%, #CDEAF7 100%); }
    .sa-stat-green { background:linear-gradient(135deg,#E9FBF3 0%, #C9F0DC 100%); }

    .sa-date-pill { display:inline-flex; align-items:center; gap:8px; background:#fff; border:1px solid var(--line); border-radius:999px; padding:10px 18px; box-shadow:var(--shadow-sm); font-size:13px; font-weight:700; color:var(--navy-900); }

    .sa-card-head { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:18px; }
    .sa-card-title { display:flex; align-items:center; gap:7px; margin:0; font-family:'Poppins','Tajawal',sans-serif; font-weight:700; font-size:14.5px; color:var(--navy-900); }

    .sa-legend-dot { display:inline-block; width:9px; height:9px; border-radius:50%; flex-shrink:0; }

    .sa-mini-bar-col { display:flex; flex-direction:column; align-items:center; gap:6px; flex:1; min-width:0; }
    .sa-mini-bar-fill { width:100%; max-width:26px; border-radius:6px 6px 3px 3px; transition:height .7s cubic-bezier(.16,1,.3,1); }

    .sa-activity-row { display:flex; align-items:flex-start; gap:10px; padding:10px 0; }
    .sa-activity-row + .sa-activity-row { border-top:1px solid var(--line); }
    .sa-activity-avatar { display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:50%; background:var(--sky-100); color:var(--blue-500); flex-shrink:0; }

    .sa-grid-4 { grid-template-columns:repeat(4, minmax(0, 1fr)); }
    .sa-grid-3 { grid-template-columns:repeat(3, minmax(0, 1fr)); }

    @media (max-width:1100px) {
        .sa-grid-4 { grid-template-columns:repeat(2, minmax(0, 1fr)); }
        .sa-grid-3 { grid-template-columns:repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width:560px) {
        .sa-grid-4 { grid-template-columns:1fr; }
        .sa-grid-3 { grid-template-columns:1fr; }
    }
    @media (max-width:900px) {
        .sa-grid-2 { grid-template-columns:1fr !important; }
    }
</style>
@endpush

@section('content')
@php
    $maxSignup = max(1, $studentsByLevel->max('count'));
    $chartN = $studentsByLevel->count();
    $chartW = 680; $chartX0 = 20; $chartBottom = 165; $chartTop = 15;
    $chartPts = [];
    foreach ($studentsByLevel->values() as $i => $row) {
        $x = $chartN > 1 ? $chartX0 + ($i * (($chartW - $chartX0) / ($chartN - 1))) : $chartX0;
        $y = $chartBottom - (($row['count'] / $maxSignup) * ($chartBottom - $chartTop));
        $chartPts[] = ['x' => round($x, 1), 'y' => round($y, 1), 'label' => $row['label'], 'count' => $row['count']];
    }
    $linePoints = collect($chartPts)->map(fn ($p) => "{$p['x']},{$p['y']}")->implode(' ');
    $areaPath = 'M '.$chartPts[0]['x'].','.$chartBottom.' L '.collect($chartPts)->map(fn ($p) => "{$p['x']},{$p['y']}")->implode(' L ').' L '.end($chartPts)['x'].','.$chartBottom.' Z';

    $revenueYearly = (float) $revenueYearly;
    $revenueMonthly = (float) $revenueMonthly;
    $donutPct = $revenueYearly > 0 ? min(100, round(($revenueMonthly / $revenueYearly) * 100)) : 0;
@endphp
<div class="sa -mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" dir="rtl">

    <div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:14px; margin-bottom:24px;">
        <div>
            <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:var(--navy-900);">مرحبًا بك: {{ $dashboardUser['name'] }}</h1>
            <span style="display:block; width:74px; height:4px; margin-top:10px; border-radius:999px; background:linear-gradient(90deg, var(--yellow-300), var(--blue-500));"></span>
            <p style="margin:10px 0 0; font-size:13px; color:var(--muted); font-weight:600;">لمحة عامة على النظام</p>
        </div>
        <span class="sa-date-pill">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--orange-600)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M3 10h18"></path><path d="M8 2v4M16 2v4"></path></svg>
            {{ now()->locale('ar')->translatedFormat('j F Y') }}
        </span>
    </div>

    {{-- ============ ROW 1: TOP STATS ============ --}}
    <div class="sa-grid-4" style="display:grid; gap:18px; margin-bottom:20px;">

        <div class="sa-card sa-stat sa-stat-cream">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:14px;">
                <p style="margin:0; font-size:12.5px; font-weight:700; color:var(--navy-900);">شهادات صادرة</p>
                <div class="sa-icon-circle" style="background:rgba(255,255,255,.65); color:#8A5A00;">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"></circle><path d="M8.5 12.5 7 21l5-2.5L17 21l-1.5-8.5"></path></svg>
                </div>
            </div>
            <p style="margin:0; font-size:27px; font-weight:800; color:var(--navy-900);" class="num">{{ number_format($totalCertificates) }}</p>
            <p style="margin:6px 0 0; font-size:11.5px; font-weight:600; color:var(--muted);">إجمالي الشهادات</p>
        </div>

        <div class="sa-card sa-stat sa-stat-blue">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:14px;">
                <p style="margin:0; font-size:12.5px; font-weight:700; color:var(--navy-900);">إجمالي الأساتذة</p>
                <div class="sa-icon-circle" style="background:rgba(255,255,255,.65); color:var(--blue-500);">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7.5 12 3l10 4.5-10 4.5-10-4.5Z"></path><path d="M6 10v5c0 1.5 2.7 3 6 3s6-1.5 6-3v-5"></path></svg>
                </div>
            </div>
            <p style="margin:0; font-size:27px; font-weight:800; color:var(--navy-900);" class="num">{{ number_format($totalTeachers) }}</p>
            <p style="margin:6px 0 0; font-size:11.5px; font-weight:600; color:var(--muted);">مدرّسين نشطين</p>
        </div>

        <div class="sa-card sa-stat sa-hero">
            <div style="position:relative; display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:14px;">
                <p style="margin:0; font-size:12.5px; font-weight:700; color:rgba(168,232,249,.9);">إجمالي  الطلاب</p>
                <div class="sa-icon-circle" style="background:rgba(255,255,255,.14); color:var(--yellow-300);">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10 12 5 2 10l10 5 10-5Z"></path><path d="M6 12v5c0 1.5 2.7 3 6 3s6-1.5 6-3v-5"></path></svg>
                </div>
            </div>
            <p style="position:relative; margin:0; font-size:27px; font-weight:800; color:#fff;" class="num">{{ number_format($totalStudents) }}</p>
            <p style="position:relative; margin:6px 0 0; font-size:11.5px; font-weight:600; color:rgba(168,232,249,.9);">طالب مسجل</p>
        </div>

        <div class="sa-card sa-stat sa-stat-green">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:14px;">
                <p style="margin:0; font-size:12.5px; font-weight:700; color:var(--navy-900);">إجمالي عدد الأدمن</p>
                <div class="sa-icon-circle" style="background:rgba(255,255,255,.65); color:var(--green-600);">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
            </div>
            <p style="margin:0; font-size:27px; font-weight:800; color:var(--navy-900);" class="num">{{ number_format($totalAdmins) }}</p>
            <p style="margin:6px 0 0; font-size:11.5px; font-weight:600; color:var(--muted);">حسابات الأدمن </p>
        </div>
    </div>

    {{-- ============ ROW 2: FINANCE DONUT + PERFORMANCE CHART ============ --}}
    <div class="sa-grid-2" style="display:grid; grid-template-columns:0.85fr 1.15fr; gap:18px; margin-bottom:18px;">

        <div class="sa-card" style="padding:22px;">
            <div class="sa-card-head">
                <h3 class="sa-card-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--orange-600)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    القسم المالي
                </h3>
            </div>
            <div style="display:flex; justify-content:center; margin-bottom:20px;">
                <div style="position:relative; width:150px; height:150px; flex-shrink:0; border-radius:50%; background:conic-gradient(var(--navy-900) 0% {{ $donutPct }}%, var(--sky-300) {{ $donutPct }}% 100%);">
                    <div style="position:absolute; inset:20px; background:var(--card); border-radius:50%; display:flex; flex-direction:column; align-items:center; justify-content:center; box-shadow:inset 0 0 0 1px var(--line); text-align:center; padding:0 10px;">
                        <span style="font-size:19px; font-weight:800; color:var(--navy-900);" class="num" dir="ltr">${{ number_format($revenueMonthly, 2) }}</span>
                        <span style="font-size:9.5px; color:var(--muted-soft); font-weight:600; margin-top:3px;">إجمالي الربح (هذا الشهر)</span>
                    </div>
                </div>
            </div>
            <div style="display:flex; flex-direction:column; gap:4px;">
                <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 10px;">
                    <span style="display:flex; align-items:center; gap:8px; font-size:12.5px; color:var(--navy-900); font-weight:600;"><span class="sa-legend-dot" style="background:var(--navy-900);"></span>الشهر الحالي</span>
                    <span class="num" dir="ltr" style="font-weight:700; font-size:12.5px; color:var(--muted);">${{ number_format($revenueMonthly, 2) }}</span>
                </div>
                <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 10px;">
                    <span style="display:flex; align-items:center; gap:8px; font-size:12.5px; color:var(--navy-900); font-weight:600;"><span class="sa-legend-dot" style="background:var(--sky-300);"></span>إجمالي السنة</span>
                    <span class="num" dir="ltr" style="font-weight:700; font-size:12.5px; color:var(--muted);">${{ number_format($revenueYearly, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="sa-card" style="padding:22px;">
            <div class="sa-card-head">
                <h3 class="sa-card-title">نظرة عامة على الأداء</h3>
                <span style="font-size:11px; font-weight:600; color:var(--muted-soft);">توزيع الطلاب حسب المستوى</span>
            </div>
            <div dir="ltr">
                <svg viewBox="0 0 700 180" style="width:100%; height:auto; display:block;" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="saAreaGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#0E6A96" stop-opacity="0.35"></stop>
                            <stop offset="100%" stop-color="#0E6A96" stop-opacity="0"></stop>
                        </linearGradient>
                    </defs>
                    @foreach ([0,1,2,3,4] as $g)
                        <line x1="20" y1="{{ 15 + $g * 37.5 }}" x2="680" y2="{{ 15 + $g * 37.5 }}" stroke="rgba(0,83,122,0.06)" stroke-width="1"></line>
                    @endforeach
                    <path d="{{ $areaPath }}" fill="url(#saAreaGrad)"></path>
                    <polyline points="{{ $linePoints }}" fill="none" stroke="#0E6A96" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></polyline>
                    @foreach ($chartPts as $p)
                        <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="4" fill="#fff" stroke="#0E6A96" stroke-width="2.5"></circle>
                    @endforeach
                </svg>
                <div style="display:flex; justify-content:space-between; margin-top:6px; padding:0 4px;">
                    @foreach ($chartPts as $p)
                        <span style="font-size:10px; color:var(--muted-soft); font-weight:600;">{{ $p['label'] }}</span>
                    @endforeach
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:8px; margin-top:16px; padding-top:14px; border-top:1px solid var(--line);">
                <span class="sa-legend-dot" style="background:#0E6A96;"></span>
                <span style="font-size:12px; color:var(--navy-900); font-weight:600;">عدد الطلاب المسجلين بكل مستوى</span>
            </div>
        </div>
    </div>

    {{-- ============ ROW 3: BEST-SELLING LEVEL + PUBLISHED LEVELS + ACTIVITY ============ --}}
    <div class="sa-grid-3" style="display:grid; gap:18px;">

        <div class="sa-card" style="padding:22px; display:flex; flex-direction:column;">
            <h3 class="sa-card-title" style="margin-bottom:16px;">المستوى الأكثر مبيعًا</h3>
            <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px; text-align:center; padding:10px 0;">
                <div style="width:64px; height:64px; border-radius:50%; background:var(--sky-100); color:var(--blue-500); display:flex; align-items:center; justify-content:center;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="{{ $bestSellingLevel ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 2.9 6 6.6.9-4.8 4.6 1.1 6.5-5.8-3-5.8 3 1.1-6.5-4.8-4.6 6.6-.9L12 2Z"></path></svg>
                </div>
                @if ($bestSellingLevel && $bestSellingLevel->level)
                    <div>
                        <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:15px; color:var(--navy-900);">{{ $bestSellingLevel->level->name_ar }}</p>
                        <p style="margin:4px 0 0; font-size:11.5px; color:var(--muted); font-weight:600;">{{ $bestSellingLevel->sales_count }} عملية شراء</p>
                    </div>
                @else
                    <p style="margin:0; font-size:12.5px; color:var(--muted-soft); font-weight:600;">لايوجد مبيعات مستويات لعرضها </p>
                @endif
            </div>
        </div>

        <div class="sa-card" style="padding:22px; display:flex; flex-direction:column;">
            <h3 class="sa-card-title" style="margin-bottom:16px;">إجمالي المستويات المنشورة</h3>
            <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span class="num" style="font-size:30px; font-weight:800; color:var(--navy-900);">{{ number_format($totalPublishedLevels) }}</span>
                    <span style="display:flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:50%; background:var(--sky-100); color:var(--blue-500);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 9 4.5-9 4.5-9-4.5Z"></path><path d="m3 11.5 9 4.5 9-4.5"></path><path d="m3 16.5 9 4.5 9-4.5"></path></svg>
                    </span>
                </div>
                <p style="margin:0; font-size:11.5px; color:var(--muted); font-weight:600; text-align:center;">مستوى منشور ومتاح للطلاب</p>
            </div>
        </div>

        <div class="sa-card" style="padding:22px;">
            <div class="sa-card-head" style="margin-bottom:10px;">
                <h3 class="sa-card-title">الأنشطة الأخيرة</h3>
            </div>
            <div>
                @forelse ($recentActivity as $activity)
                    <div class="sa-activity-row">
                        <div class="sa-activity-avatar"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21a8 8 0 1 0-16 0"></path><circle cx="12" cy="7" r="4"></circle></svg></div>
                        <div style="flex:1; min-width:0;">
                            <p style="margin:0; font-size:12.5px; font-weight:700; color:var(--navy-900);">{{ $activity['label'] }}</p>
                            <p style="margin:2px 0 0; font-size:11px; color:var(--muted-soft);">{{ $activity['detail'] }} · {{ $activity['at']?->diffForHumans() }}</p>
                        </div>
                        <span class="sa-legend-dot" style="background:{{ $activity['dot'] }}; margin-top:4px;"></span>
                    </div>
                @empty
                    <p style="margin:0; padding:10px 0; font-size:12.5px; color:var(--muted-soft); font-weight:600; text-align:center;">ما في أنشطة لعرضها لهلق</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
