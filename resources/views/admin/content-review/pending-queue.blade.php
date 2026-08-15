@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .cr-hero, .cr-pill, .cr-row { animation: lessonsFadeUp 0.45s ease both; }

    .cr-row { transition: background 0.15s ease, box-shadow 0.15s ease; }
    .cr-row:hover { background: rgba(255,211,91,0.07); box-shadow: 0 0 16px rgba(255,186,66,0.16); }

    .cr-claim-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .cr-claim-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 16px rgba(245,162,1,0.26); }
</style>
@endpush

@section('content')
@php
    $typeLabels = ['lesson' => 'درس', 'test' => 'اختبار'];
    $typeColors = ['lesson' => ['bg' => 'rgba(168,232,249,0.28)', 'fg' => '#00537A'], 'test' => ['bg' => 'rgba(255,211,91,0.22)', 'fg' => '#8A5A00']];
    $testableLabels = ['course' => 'اختبار كورس', 'lesson' => 'اختبار درس', 'level' => 'اختبار مستوى', 'placement_test' => 'اختبار تحديد مستوى'];
@endphp
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    @if (session('success'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(168,232,249,0.18); color:#00537A; border:1px solid rgba(0,83,122,0.14); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error') || $errors->any())
        <div style="display:flex; align-items:center; gap:10px; background:rgba(255,138,101,0.14); color:#C2591A; border:1px solid rgba(255,138,101,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif

    {{-- ============ HERO HEAD ============ --}}
    <div class="cr-hero" style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.2);">
        <div style="position:absolute; width:380px; height:380px; right:-110px; top:-150px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.22) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:22px;">
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">المهام التي تحتاج الى مراجعة </p>
                    <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">محتوى بانتظار المراجعة</h1>
                </div>
            </div>
            <a href="{{ route('admin.content-review.my-sessions') }}" style="display:inline-flex; align-items:center; gap:8px; padding:12px 20px; border-radius:12px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); color:#fff; text-decoration:none; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                مراجعاتي الحالية
            </a>
        </div>

        <div style="position:relative; display:flex; gap:12px; flex-wrap:wrap;">
            @php
                $pillStyle = 'display:flex; align-items:center; gap:8px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.14); backdrop-filter:blur(6px); border-radius:12px; padding:9px 16px; font-size:12.5px; font-weight:600; color:rgba(168,232,249,0.85);';
                $bStyle = "font-family:'Poppins',sans-serif; color:#fff; font-weight:800; margin-inline-start:2px;";
                $dot = 'display:inline-block; width:7px; height:7px; border-radius:50%;';
                $lessonCount = collect($queue->items())->where('queue_type', 'lesson')->count();
                $testCount = collect($queue->items())->where('queue_type', 'test')->count();
            @endphp
            <div class="cr-pill" style="{{ $pillStyle }}"><span style="{{ $dot }} background:#A8E8F9;"></span>إجمالي <b style="{{ $bStyle }}">{{ $queue->total() }}</b></div>
            <div class="cr-pill" style="{{ $pillStyle }}"><span style="{{ $dot }} background:#A8E8F9;"></span>دروس <b style="{{ $bStyle }}">{{ $lessonCount }}</b></div>
            <div class="cr-pill" style="{{ $pillStyle }}"><span style="{{ $dot }} background:#FFD35B;"></span>اختبارات <b style="{{ $bStyle }}">{{ $testCount }}</b></div>
        </div>
    </div>

    {{-- ============ QUEUE LIST ============ --}}
    <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:10px; box-shadow:0 10px 26px rgba(0,83,122,0.06); display:flex; flex-direction:column; gap:8px;">
        @forelse ($queue as $item)
            @php
                $isLesson = $item->queue_type === 'lesson';
                if ($isLesson) {
                    $teacher = $item->course->teacher ?? null;
                    $contextLabel = $item->course->name_ar ?? '—';
                } else {
                    $testable = $item->testable;
                    $testableTypeVal = $item->testable_type;
                    if ($testableTypeVal === 'lesson') {
                        $teacher = $testable->course->teacher ?? null;
                        $contextLabel = ($testableLabels['lesson'] ?? 'اختبار درس') . ': ' . ($testable->title_ar ?? '—');
                    } elseif ($testableTypeVal === 'course') {
                        $teacher = $testable->teacher ?? null;
                        $contextLabel = ($testableLabels['course'] ?? 'اختبار كورس') . ': ' . ($testable->name_ar ?? '—');
                    } else {
                        $teacher = null;
                        $contextLabel = $testableLabels[$testableTypeVal] ?? $testableTypeVal;
                    }
                }
                $teacherName = $teacher ? (trim(($teacher->first_name ?? '').' '.($teacher->last_name ?? '')) ?: $teacher->email) : null;
                $tc = $typeColors[$item->queue_type];
            @endphp
            <div class="cr-row" style="display:flex; align-items:center; gap:18px; padding:20px 24px; border-radius:14px; background:linear-gradient(160deg, rgba(168,232,249,0.16), rgba(255,211,91,0.08)); border:1.5px solid rgba(14,106,150,0.65); box-shadow:0 0 10px rgba(255,186,66,0.2), 0 0 14px rgba(14,106,150,0.26);">
                <span style="display:inline-flex; padding:5px 11px; border-radius:999px; background:{{ $tc['bg'] }}; color:{{ $tc['fg'] }}; font-size:10.5px; font-weight:700; flex-shrink:0;">{{ $typeLabels[$item->queue_type] }}</span>

                <div style="flex:1; min-width:0;">
                    <div style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap;">
                        <span style="font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; color:#013C58;">{{ $item->title_en }}</span>
                        <span style="font-size:12px; color:rgba(1,60,88,0.65); font-weight:500;">{{ $item->title_ar }}</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px; margin-top:5px; flex-wrap:wrap;">
                        <span style="display:inline-flex; align-items:center; gap:4px; font-size:11.5px; color:#00537A; font-weight:600;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                            {{ $contextLabel }}
                        </span>
                        @if ($teacherName)
                            <span style="display:inline-flex; align-items:center; gap:4px; font-size:11.5px; color:rgba(1,60,88,0.7); font-weight:600;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="10" cy="7" r="4"></circle></svg>
                                {{ $teacherName }}
                            </span>
                        @endif
                    </div>
                </div>

                <form action="{{ $isLesson ? route('admin.content-review.lessons.claim', $item->id) : route('admin.content-review.tests.claim', $item->id) }}" method="POST" style="flex-shrink:0;">
                    @csrf
                    <button type="submit" class="cr-claim-btn" style="display:inline-flex; align-items:center; gap:7px; padding:10px 18px; border-radius:10px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:12.5px; cursor:pointer;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                        استلام للمراجعة
                    </button>
                </form>
            </div>
        @empty
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:14px; padding:64px 20px;">
                <div style="display:flex; align-items:center; justify-content:center; width:64px; height:64px; border-radius:18px; background:rgba(168,232,249,0.25); color:#0E6A96;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                </div>
                <div style="text-align:center;">
                    <p style="margin:0; font-size:14.5px; font-weight:700; color:#013C58;">لايوجد  محتوى بانتظار المراجعة حاليًا</p>
                    <p style="margin:5px 0 0; font-size:12.5px; color:rgba(1,60,88,0.45);">كل شيء متابَع،    أي درس أو اختبار جديد بحاجة مراجعة</p>
                </div>
            </div>
        @endforelse
    </div>

    @if ($queue instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div style="margin-top:22px;">{{ $queue->links('vendor.pagination.lessons') }}</div>
    @endif
</div>
@endsection
