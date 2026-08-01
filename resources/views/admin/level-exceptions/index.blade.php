@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .lex-hero, .lex-stat, .lex-tabs, .lex-card { animation: lessonsFadeUp 0.45s ease both; }
    .lex-stat:nth-child(1) { animation-delay: 0.02s; }
    .lex-stat:nth-child(2) { animation-delay: 0.06s; }
    .lex-stat:nth-child(3) { animation-delay: 0.1s; }
    .lex-stat:nth-child(4) { animation-delay: 0.14s; }
    .lex-tabs { animation-delay: 0.08s; }

    .lex-stat { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .lex-stat:hover { transform: translateY(-3px); box-shadow: 0 10px 22px rgba(1,60,88,0.14); }

    .lex-tab { transition: transform 0.15s ease, background 0.15s ease, color 0.15s ease; }
    .lex-tab:hover { transform: translateY(-1px); }

    .lex-card { transition: transform 0.25s cubic-bezier(0.16,1,0.3,1), box-shadow 0.25s ease; }
    .lex-card:hover { transform: translateY(-5px); box-shadow: 0 28px 56px rgba(0,83,122,0.16); }

    .lex-view-btn { transition: transform 0.15s ease, background 0.15s ease; }
    .lex-view-btn:hover { transform: translateY(-1px); background: rgba(0,83,122,0.12); }
</style>
@endpush

@section('content')
@php
    $statusLabels = ['pending' => 'قيد الانتظار', 'in_review' => 'قيد المراجعة', 'approved' => 'موافق عليه', 'rejected' => 'مرفوض'];
    $statusColors = [
        'pending'   => ['bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00', 'dot' => '#F5A201', 'tabBg' => '#F5A201', 'tabFg' => '#013C58'],
        'in_review' => ['bg' => 'rgba(14,106,150,0.14)', 'fg' => '#0E6A96', 'dot' => '#0E6A96', 'tabBg' => '#0E6A96', 'tabFg' => '#fff'],
        'approved'  => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55', 'dot' => '#4CAF78', 'tabBg' => '#4CAF78', 'tabFg' => '#fff'],
        'rejected'  => ['bg' => 'rgba(255,138,101,0.18)', 'fg' => '#C2591A', 'dot' => '#FF8A65', 'tabBg' => '#FF8A65', 'tabFg' => '#fff'],
    ];
    $activeStatus = request()->query('status');

    $totalCount = \App\Models\LevelException::count();
    $pendingCount = \App\Models\LevelException::where('status', 'pending')->count();
    $reviewCount = \App\Models\LevelException::where('status', 'in_review')->count();
    $approvedCount = \App\Models\LevelException::where('status', 'approved')->count();
@endphp
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    @if (session('success'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(168,232,249,0.18); color:#00537A; border:1px solid rgba(0,83,122,0.14); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ============ HERO ============ --}}
    <div class="lex-hero" style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.2) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>
        <div style="position:absolute; width:300px; height:300px; left:-80px; bottom:-140px; border-radius:50%; background:radial-gradient(circle, rgba(168,232,249,0.2) 0%, rgba(168,232,249,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:center; gap:16px; margin-bottom:22px;">
            <div style="display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="3"></rect><path d="M9 11l3 3 5-5"></path></svg>
            </div>
            <div>
                <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">إدارة الطلبات</p>
                <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">طلبات الاستثناء</h1>
            </div>
        </div>

        <div style="position:relative; display:flex; gap:14px; flex-wrap:wrap;">
            @php
                $statCard = 'display:flex; align-items:center; gap:13px; background:rgba(255,211,91,0.05); border:1px solid rgba(255,211,91,0.15); border-radius:16px; padding:14px 18px; flex:1; min-width:150px;';
                $iconWrapBase = 'display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.14); flex-shrink:0;';
            @endphp
            <div class="lex-stat" style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#FFD35B;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M3 10h18"></path></svg></div>
                <div><p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,236,176,0.85);">إجمالي</p><p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $totalCount }}</p></div>
            </div>
            <div class="lex-stat" style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#FFD35B;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg></div>
                <div><p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,236,176,0.85);">قيد الانتظار</p><p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $pendingCount }}</p></div>
            </div>
            <div class="lex-stat" style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#FFD35B;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></div>
                <div><p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,236,176,0.85);">قيد المراجعة</p><p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $reviewCount }}</p></div>
            </div>
            <div class="lex-stat" style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#FFD35B;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg></div>
                <div><p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,236,176,0.85);">موافق عليه</p><p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $approvedCount }}</p></div>
            </div>
        </div>
    </div>

    {{-- ============ FILTER TABS ============ --}}
    <div class="lex-tabs" style="display:flex; gap:6px; padding:4px 0; flex-wrap:wrap; margin-bottom:22px; width:fit-content;">
        @php
            $tabBase = "border:none; padding:8px 14px; border-radius:9px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:600; white-space:nowrap; text-decoration:none; display:inline-block;";
            $tabs = [
                ['status' => null, 'label' => 'جميع الطلبات', 'bg' => 'rgba(1,60,88,0.06)', 'fg' => 'rgba(1,60,88,0.6)', 'activeBg' => '#013C58', 'activeFg' => '#fff'],
                ['status' => 'pending', 'label' => 'قيد الانتظار', 'bg' => $statusColors['pending']['bg'], 'fg' => $statusColors['pending']['fg'], 'activeBg' => $statusColors['pending']['tabBg'], 'activeFg' => $statusColors['pending']['tabFg']],
                ['status' => 'in_review', 'label' => 'قيد المراجعة', 'bg' => $statusColors['in_review']['bg'], 'fg' => $statusColors['in_review']['fg'], 'activeBg' => $statusColors['in_review']['tabBg'], 'activeFg' => $statusColors['in_review']['tabFg']],
                ['status' => 'approved', 'label' => 'موافق عليه', 'bg' => $statusColors['approved']['bg'], 'fg' => $statusColors['approved']['fg'], 'activeBg' => $statusColors['approved']['tabBg'], 'activeFg' => $statusColors['approved']['tabFg']],
                ['status' => 'rejected', 'label' => 'مرفوض', 'bg' => $statusColors['rejected']['bg'], 'fg' => $statusColors['rejected']['fg'], 'activeBg' => $statusColors['rejected']['tabBg'], 'activeFg' => $statusColors['rejected']['tabFg']],
            ];
        @endphp
        @foreach ($tabs as $tab)
            @php $isActive = $activeStatus === $tab['status']; @endphp
            <a href="{{ $tab['status'] ? route('levelException.index', ['status' => $tab['status']]) : route('levelException.index') }}"
               class="lex-tab"
               style="{{ $tabBase }} background:{{ $tab['bg'] }}; color:{{ $tab['fg'] }}; {{ $isActive ? 'box-shadow: inset 0 0 0 1.5px '.$tab['activeBg'].';' : '' }}">{{ $tab['label'] }}</a>
        @endforeach
    </div>

    {{-- ============ REQUESTS: DOSSIER / SEAL STYLE ============ --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:22px;">
        @forelse ($levelExceptions as $exception)
            @php
                $sc = $statusColors[$exception->status->value] ?? $statusColors['pending'];
                $student = $exception->user;
                $studentName = $student ? (trim(($student->first_name ?? '').' '.($student->last_name ?? '')) ?: $student->email) : '—';
                $studentInitials = $student ? strtoupper(substr($student->first_name ?? $student->email, 0, 1).substr($student->last_name ?? '', 0, 1)) : '?';
                $attachmentCount = $exception->getMedia('attachments')->count();
                $sealIcons = [
                    'pending'   => '<circle cx="12" cy="12" r="8"></circle><path d="M12 8v4l2.5 2.5"></path>',
                    'in_review' => '<circle cx="10.5" cy="10.5" r="6.5"></circle><path d="m19 19-4-4"></path>',
                    'approved'  => '<path d="M9 11l3 3 5-5"></path>',
                    'rejected'  => '<path d="M18 6 6 18"></path><path d="M6 6l12 12"></path>',
                ];
                $sealIcon = $sealIcons[$exception->status->value] ?? $sealIcons['pending'];
            @endphp
            <div style="filter:drop-shadow(0 0 7px rgba(255,186,66,0.16)) drop-shadow(0 10px 20px rgba(0,83,122,0.08));">
             <div style="position:relative; background:linear-gradient(135deg, rgba(255,211,91,0.28), rgba(14,106,150,0.3)); clip-path:polygon(0 0, calc(100% - 66px) 0, 100% 66px, 100% 100%, 0 100%);">
              <div class="lex-card" style="position:relative; background:#E4F4FB; clip-path:polygon(0 0, calc(100% - 64.5px) 0, 100% 64.5px, 100% 100%, 0 100%); margin:1.5px; padding:22px 24px 20px;">
                {{-- corner notch (seal) --}}
                <div style="position:absolute; top:0; right:0; width:64.5px; height:64.5px; clip-path:polygon(100% 0, 0 0, 100% 100%); background:{{ $sc['bg'] }};"></div>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $sc['fg'] }}" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute; top:11px; right:11px;">{!! $sealIcon !!}</svg>

                <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:16px;">
                    <span style="display:inline-flex; align-items:center; gap:5px; padding:4px 11px; border-radius:999px; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }}; font-size:10.5px; font-weight:700; margin-inline-start:26px;">{{ $statusLabels[$exception->status->value] ?? $exception->status->value }}</span>
                    <span style="display:inline-flex; align-items:center; gap:4px; font-size:10.5px; color:rgba(1,60,88,0.55); font-weight:700; background:rgba(1,60,88,0.06); padding:4px 10px; border-radius:999px;">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
                        {{ $exception->created_at->diffForHumans() }}
                    </span>
                </div>

                <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                    <div style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:11px; background:#00537A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; flex-shrink:0;">{{ $studentInitials }}</div>
                    <div style="flex:1; min-width:0;">
                        <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#013C58;">{{ $studentName }}</p>
                        <p style="margin:2px 0 0; font-size:11px; color:rgba(1,60,88,0.5);">{{ $student->email ?? '' }}</p>
                    </div>
                </div>

                <div style="display:flex; align-items:center; justify-content:flex-end; gap:8px; margin-bottom:14px; flex-wrap:wrap;">
                    <span style="display:inline-flex; padding:4px 10px; background:rgba(0,83,122,0.08); border-radius:8px; color:#00537A; font-size:11px; font-weight:700;">{{ $exception->requestedLevel->name_ar ?? '—' }}</span>
                    @if ($exception->recommendedLevel)
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#013C58" stroke-width="2.4"><path d="m9 18 6-6-6-6"></path></svg>
                        <span style="display:inline-flex; padding:4px 10px; background:rgba(255,211,91,0.14); border-radius:8px; color:#8A5A00; font-size:11px; font-weight:700;">{{ $exception->recommendedLevel->name_ar }}</span>
                    @endif
                </div>

                <p style="margin:0 0 16px; font-size:13.5px; color:rgba(1,60,88,0.8); font-weight:500; line-height:1.6; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;" title="{{ $exception->reason }}"><span style="color:#00537A; font-weight:800; font-size:16px;">"</span>{{ $exception->reason }}<span style="color:#00537A; font-weight:800; font-size:16px;">"</span></p>

                <div style="height:2px; border-radius:999px; background:linear-gradient(90deg, #F5A201, #0E6A96); opacity:0.5; margin-bottom:14px;"></div>

                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <a href="{{ route('levelException.show', $exception) }}" class="lex-view-btn" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:9px; background:#00537A; text-decoration:none; color:#fff; font-family:'Poppins',sans-serif; font-weight:600; font-size:11.5px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        تفاصيل
                    </a>
                    <span style="display:inline-flex; align-items:center; padding:5px 10px; border-radius:8px; background:rgba(255,211,91,0.1); color:#8A5A00; font-size:10px; font-weight:700;">{{ $attachmentCount }} ملف</span>
                </div>
              </div>
             </div>
            </div>
        @empty
            <div style="grid-column:1/-1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:14px; padding:64px 20px;">
                <div style="display:flex; align-items:center; justify-content:center; width:64px; height:64px; border-radius:18px; background:rgba(168,232,249,0.25); color:#0E6A96;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="3"></rect><path d="M9 11l3 3 5-5"></path></svg>
                </div>
                <div style="text-align:center;">
                    <p style="margin:0; font-size:14.5px; font-weight:700; color:#013C58;">لا توجد طلبات بهذا الفلتر</p>
                </div>
            </div>
        @endforelse
    </div>

    @if ($levelExceptions instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div style="margin-top:22px;">{{ $levelExceptions->appends(request()->query())->links('vendor.pagination.lessons') }}</div>
    @endif
</div>
@endsection
