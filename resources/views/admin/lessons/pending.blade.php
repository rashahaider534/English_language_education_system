@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .pending-hero, .pending-pill, .pending-card { animation: lessonsFadeUp 0.45s ease both; }
    .pending-pill:nth-child(1) { animation-delay: 0.02s; }
    .pending-pill:nth-child(2) { animation-delay: 0.06s; }
    .pending-pill:nth-child(3) { animation-delay: 0.1s; }

    .pending-row { transition: background 0.15s ease, box-shadow 0.15s ease; }
    .pending-row:hover { background: rgba(255,211,91,0.07); box-shadow: 0 0 16px rgba(255,186,66,0.16); }

    .pending-view-btn { transition: transform 0.15s ease, background 0.15s ease; }
    .pending-view-btn:hover { transform: translateY(-1px); background: rgba(0,83,122,0.12); }
</style>
@endpush

@section('content')
@php
    $statusLabels = ['pending' => 'قيد الانتظار', 'in_review' => 'قيد المراجعة', 'changes_requested' => 'طلب تعديل'];
    // Same soft palette used on the per-course lessons table
    $statusColors = [
        'pending'         => ['bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00', 'dot' => '#F5A201'],
        'in_review'       => ['bg' => 'rgba(14,106,150,0.14)', 'fg' => '#0E6A96', 'dot' => '#0E6A96'],
        'changes_requested' => ['bg' => 'rgba(255,138,101,0.13)', 'fg' => '#C2591A', 'dot' => '#FF8A65'],
    ];
@endphp
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    {{-- ============ HERO HEAD ============ --}}
    <div class="pending-hero" style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.2);">
        <div style="position:absolute; width:380px; height:380px; right:-110px; top:-150px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.22) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>
        <div style="position:absolute; width:300px; height:300px; left:-80px; bottom:-140px; border-radius:50%; background:radial-gradient(circle, rgba(168,232,249,0.2) 0%, rgba(168,232,249,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:center; gap:16px; margin-bottom:22px;">
            <div style="position:relative; display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;">
                <span style="position:absolute; inset:-4px; border-radius:19px; border:1px solid rgba(255,211,91,0.25);"></span>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:relative;"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
            </div>
            <div>
                <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">مراجعة من المعلمين</p>
                <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">الدروس الجديدة</h1>
            </div>
        </div>

        <div style="position:relative; display:flex; gap:12px; flex-wrap:wrap;">
            @php
                $pillStyle = 'display:flex; align-items:center; gap:8px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.14); backdrop-filter:blur(6px); border-radius:12px; padding:9px 16px; font-size:12.5px; font-weight:600; color:rgba(168,232,249,0.85);';
                $bStyle = "font-family:'Poppins',sans-serif; color:#fff; font-weight:800; margin-inline-start:2px;";
                $dot = 'display:inline-block; width:7px; height:7px; border-radius:50%;';
                $pendingCount = $lessons->where('status', 'pending')->count();
                $reviewCount = $lessons->where('status', 'in_review')->count();
            @endphp
            <div class="pending-pill" style="{{ $pillStyle }}"><span style="{{ $dot }} background:#A8E8F9;"></span>إجمالي <b style="{{ $bStyle }}">{{ $lessons->total() }}</b></div>
            <div class="pending-pill" style="{{ $pillStyle }}"><span style="{{ $dot }} background:{{ $statusColors['pending']['dot'] }};"></span>قيد الانتظار <b style="{{ $bStyle }}">{{ $pendingCount }}</b></div>
            <div class="pending-pill" style="{{ $pillStyle }}"><span style="{{ $dot }} background:{{ $statusColors['in_review']['dot'] }};"></span>قيد المراجعة <b style="{{ $bStyle }}">{{ $reviewCount }}</b></div>
        </div>
    </div>

    {{-- ============ LESSON LIST/FEED ============ --}}
    <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:10px; box-shadow:0 10px 26px rgba(0,83,122,0.06); display:flex; flex-direction:column; gap:8px;">
        @forelse ($lessons as $lesson)
            @php
                $sc = $statusColors[$lesson->status] ?? $statusColors['pending'];
                $teacher = $lesson->course->teacher ?? null;
                $teacherName = $teacher ? trim(($teacher->first_name ?? '').' '.($teacher->last_name ?? '')) ?: $teacher->email : '—';
            @endphp
            <div class="pending-row" style="display:flex; align-items:center; gap:18px; padding:20px 24px; border-radius:14px; background:linear-gradient(160deg, rgba(168,232,249,0.16), rgba(255,211,91,0.08)); border:1.5px solid rgba(14,106,150,0.65); box-shadow:0 0 10px rgba(255,186,66,0.2), 0 0 14px rgba(14,106,150,0.26);">
                <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:{{ $sc['dot'] }}; flex-shrink:0;"></span>

                <div style="flex:1; min-width:0;">
                    <div style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap;">
                        <span style="font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; color:#013C58;">{{ $lesson->title_en }}</span>
                        <span style="font-size:12px; color:rgba(1,60,88,0.65); font-weight:500;">{{ $lesson->title_ar }}</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px; margin-top:5px; flex-wrap:wrap;">
                        <span style="display:inline-flex; align-items:center; gap:4px; font-size:11.5px; color:#00537A; font-weight:600;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                            {{ $lesson->course->name_ar ?? '—' }}
                        </span>
                        <span style="display:inline-flex; align-items:center; gap:4px; font-size:11.5px; color:rgba(1,60,88,0.7); font-weight:600;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="10" cy="7" r="4"></circle></svg>
                            {{ $teacherName }}
                        </span>
                    </div>
                </div>

                <span style="display:inline-flex; align-items:center; gap:5px; padding:4px 11px; border-radius:999px; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }}; font-size:10.5px; font-weight:700; flex-shrink:0;">
                    {{ $statusLabels[$lesson->status] ?? $lesson->status }}
                </span>

                <a href="{{ route('lessons.show', $lesson) }}" class="pending-view-btn" title="عرض" style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; background:rgba(0,83,122,0.07); text-decoration:none; color:#00537A; flex-shrink:0;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </a>
            </div>
        @empty
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:14px; padding:64px 20px;">
                <div style="display:flex; align-items:center; justify-content:center; width:64px; height:64px; border-radius:18px; background:rgba(168,232,249,0.25); color:#0E6A96;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                </div>
                <div style="text-align:center;">
                    <p style="margin:0; font-size:14.5px; font-weight:700; color:#013C58;">ما في دروس قيد الانتظار حاليًا</p>
                    <p style="margin:5px 0 0; font-size:12.5px; color:rgba(1,60,88,0.45);">كل شي متابَع، رح يطلع هون أي درس جديد بحاجة مراجعة</p>
                </div>
            </div>
        @endforelse
    </div>

    @if ($lessons instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div style="margin-top:22px;">{{ $lessons->appends(request()->query())->links('vendor.pagination.lessons') }}</div>
    @endif
</div>
@endsection
