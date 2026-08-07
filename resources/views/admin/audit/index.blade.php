@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes auditFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .adt-hero, .adt-stat, .adt-card, .adt-lb-row, .adt-level-card { animation: auditFadeUp 0.4s ease both; }
    .adt-level-card:nth-child(2) { animation-delay:.04s; }
    .adt-level-card:nth-child(3) { animation-delay:.08s; }
    .adt-level-card:nth-child(4) { animation-delay:.12s; }
    .adt-level-card:nth-child(5) { animation-delay:.16s; }
    .adt-level-card:nth-child(6) { animation-delay:.2s; }

    .adt-stat { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .adt-stat:hover { transform: translateY(-3px); }

    .adt-lb-row { display:flex; align-items:center; gap:14px; padding:13px 14px; border-radius:14px; background:rgba(255,255,255,0.5); border:1px solid rgba(0,83,122,0.07); transition:background 0.2s ease, transform 0.2s ease; }
    .adt-lb-row:hover { background:rgba(255,255,255,0.9); transform:translateX(-2px); }
    .adt-lb-rank { display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px; color:#fff; flex-shrink:0; box-shadow:0 3px 8px rgba(1,60,88,0.18); }
    .adt-avatar { display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:50%; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; color:#fff; flex-shrink:0; box-shadow:0 3px 10px rgba(1,60,88,0.16); }

    .adt-level-card { display:block; text-decoration:none; background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.14); border-radius:20px; padding:20px; box-shadow:0 10px 24px rgba(0,83,122,0.06); transition:transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease; }
    .adt-level-card:hover { transform:translateY(-4px); box-shadow:0 20px 40px rgba(0,83,122,0.12); border-color:rgba(14,106,150,0.35); }
</style>
@endpush

@section('content')
@php
    $rankStyles = [
        1 => ['badge' => '#F5A201', 'avatar' => 'linear-gradient(135deg,#F5A201,#FFD35B)'],
        2 => ['badge' => '#8FA6B3', 'avatar' => 'linear-gradient(135deg,#8FA6B3,#B9C9D2)'],
        3 => ['badge' => '#B8703C', 'avatar' => 'linear-gradient(135deg,#B8703C,#D69A6C)'],
    ];
    $defaultRankStyle = ['badge' => '#013C58', 'avatar' => 'linear-gradient(135deg,#013C58,#0E6A96)'];
    $medals = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
@endphp
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    {{-- ============ HERO ============ --}}
    <div class="adt-hero" style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.2) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>
        <svg width="170" height="170" viewBox="0 0 24 24" fill="none" stroke="#A8E8F9" stroke-width="1" style="position:absolute; left:-30px; bottom:-40px; opacity:0.08; pointer-events:none;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path></svg>

        <div style="position:relative; display:flex; align-items:center; gap:16px; margin-bottom:22px;">
            <div style="display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path></svg>
            </div>
            <div>
                <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">SUPER ADMIN</p>
                <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">الرقابة وإدارة الأعمال</h1>
            </div>
        </div>

        <div style="position:relative; display:flex; gap:14px; flex-wrap:wrap;">
            <div class="adt-stat" style="display:flex; align-items:center; gap:13px; background:linear-gradient(135deg, rgba(126,224,178,0.22), rgba(126,224,178,0.08)); border:1px solid rgba(126,224,178,0.35); border-radius:16px; padding:14px 18px; flex:1; min-width:170px;">
                <div style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.18); color:#7EE0B2; flex-shrink:0;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></div>
                <div><p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,255,255,0.75);">أعمال تمّت مراجعتها</p><p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $totalReviewed }}</p></div>
            </div>
            <div class="adt-stat" style="display:flex; align-items:center; gap:13px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.14); border-radius:16px; padding:14px 18px; flex:1; min-width:170px;">
                <div style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.14); color:#A8E8F9; flex-shrink:0;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg></div>
                <div><p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,255,255,0.75);">معتمدة</p><p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $totalApproved }}</p></div>
            </div>
            <div class="adt-stat" style="display:flex; align-items:center; gap:13px; background:linear-gradient(135deg, rgba(255,211,91,0.22), rgba(255,211,91,0.06)); border:1px solid rgba(255,211,91,0.35); border-radius:16px; padding:14px 18px; flex:1; min-width:170px;">
                <div style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg></div>
                <div><p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,255,255,0.75);">بانتظار المراجعة</p><p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $totalPendingReview }}</p></div>
            </div>
        </div>
    </div>

    {{-- ============ SECTION 1: WORK REVIEWED BY ADMINS (LEADERBOARD STYLE) ============ --}}
    <div class="adt-card" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; padding:24px; margin-bottom:22px; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:15px; color:#013C58;">الأعمال المدققة حسب الأدمن</h3>
        <p style="margin:0 0 18px; font-size:12px; color:rgba(1,60,88,0.5);"> </p>

        @if ($reviewStats->isEmpty() || $reviewStats->sum('total') === 0)
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; padding:40px 10px; text-align:center;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="rgba(1,60,88,0.3)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                <p style="margin:0; color:rgba(1,60,88,0.45); font-weight:600; font-size:13px;"></p>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:8px;">
                @foreach ($reviewStats as $i => $row)
                    @php
                        $rank = $i + 1;
                        $adminName = trim($row['admin']->first_name.' '.$row['admin']->last_name) ?: $row['admin']->email;
                        $initials = strtoupper(substr($row['admin']->first_name ?? $row['admin']->email, 0, 1));
                        $maxTotal = max(1, $reviewStats->max('total'));
                        $pct = round(($row['total'] / $maxTotal) * 100);
                        $style = $rankStyles[$rank] ?? $defaultRankStyle;
                    @endphp
                    <div class="adt-lb-row">
                        <span class="adt-lb-rank" style="background:{{ $style['badge'] }};">{{ $medals[$rank] ?? $rank }}</span>
                        <div class="adt-avatar" style="background:{{ $style['avatar'] }};">{{ $initials }}</div>
                        <div style="flex:1; min-width:0;">
                            <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:6px;">
                                <span style="font-size:13px; font-weight:700; color:#013C58;">{{ $adminName }}</span>
                                <span style="font-size:11.5px; color:rgba(1,60,88,0.55); font-weight:600;">{{ $row['approved'] }} معتمد · {{ $row['rejected'] }} مرفوض · {{ $row['in_review'] }} قيد المراجعة</span>
                            </div>
                            <div style="height:6px; border-radius:999px; background:rgba(0,83,122,0.08); overflow:hidden;">
                                <div style="height:100%; width:{{ $pct }}%; background:linear-gradient(90deg,#0E6A96,#A8E8F9); border-radius:999px;"></div>
                            </div>
                        </div>
                        <span style="font-family:'Poppins',sans-serif; font-weight:800; font-size:17px; color:#013C58; flex-shrink:0;">{{ $row['total'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ============ SECTION 2: LEVELS WITH VIEW RATES (CARD GRID) ============ --}}
    <div class="adt-card" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; padding:24px; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:15px; color:#013C58;">المستويات ونسب المشاهدة</h3>
        <p style="margin:0 0 18px; font-size:12px; color:rgba(1,60,88,0.5);"></p>

        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(230px, 1fr)); gap:16px;">
            @foreach ($levels as $level)
                @php
                    $rate = $level->example_view_rate;
                    $ringColor = $rate >= 70 ? '#2E7D55' : ($rate >= 40 ? '#8A5A00' : '#C2591A');
                @endphp
                <a href="{{ route('admin.audit.level', $level) }}" class="adt-level-card">
                    <div style="display:flex; align-items:center; gap:14px; margin-bottom:14px;">
                        <div style="position:relative; width:56px; height:56px; flex-shrink:0; border-radius:50%; background:conic-gradient({{ $ringColor }} 0% {{ $rate }}%, rgba(0,83,122,0.1) {{ $rate }}% 100%);">
                            <div style="position:absolute; inset:5px; background:#EFFAFD; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                <span style="font-family:'Poppins',sans-serif; font-weight:800; font-size:13px; color:{{ $ringColor }};">{{ $rate }}%</span>
                            </div>
                        </div>
                        <div style="min-width:0;">
                            <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; color:#013C58; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $level->name_ar }}</p>
                            <p style="margin:4px 0 0; font-size:11px; color:rgba(1,60,88,0.5); font-weight:600;">{{ $level->courses_count }} كورس</p>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; justify-content:space-between; padding-top:12px; border-top:1px solid rgba(0,83,122,0.08);">
                        <span style="font-size:11.5px; font-weight:700; color:#00537A;">عرض الكورسات</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00537A" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
                    </div>
                </a>
            @endforeach
        </div>

        <p style="margin:18px 2px 0; font-size:11.5px; color:rgba(1,60,88,0.45); font-weight:600;">
        </p>
    </div>
</div>
@endsection
