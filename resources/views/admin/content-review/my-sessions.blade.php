@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .cr-hero, .cr-row, .cr-tab { animation: lessonsFadeUp 0.45s ease both; }

    .cr-row { transition: background 0.15s ease, box-shadow 0.15s ease; }
    .cr-row:hover { background: rgba(168,232,249,0.1); }

    .cr-tab { transition: background 0.15s ease, color 0.15s ease; }
    .cr-action-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .cr-action-btn:hover { transform: translateY(-1px); }

    .modal-scroll::-webkit-scrollbar { width: 8px; }
    .modal-scroll::-webkit-scrollbar-track { background: transparent; }
    .modal-scroll::-webkit-scrollbar-thumb { background: rgba(1,60,88,0.14); border-radius: 999px; }
</style>
@endpush

@section('content')
@php
    $reviewStatusLabels = ['in_review' => 'قيد المراجعة', 'approved' => 'تمت الموافقة', 'changes_requested' => 'طُلب تعديل', 'released' => 'مُعاد للطابور'];
    $reviewStatusColors = [
        'in_review'         => ['bg' => 'rgba(14,106,150,0.14)', 'fg' => '#0E6A96'],
        'approved'          => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55'],
        'changes_requested' => ['bg' => 'rgba(255,138,101,0.18)', 'fg' => '#C2591A'],
        'released'          => ['bg' => 'rgba(1,60,88,0.1)', 'fg' => 'rgba(1,60,88,0.55)'],
    ];
    $tabs = [null => 'الكل', 'in_review' => 'قيد المراجعة', 'approved' => 'تمت الموافقة', 'changes_requested' => 'طُلب تعديل'];
@endphp
<div x-data="{ changesModalOpen: false, changesReviewId: null, changesMessage: '' }" class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

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

    <div style="margin-bottom:18px;">
        <a href="{{ route('admin.content-review.pending-queue') }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            العودة لطابور المراجعة
        </a>
    </div>

    {{-- ============ HERO ============ --}}
    <div class="cr-hero" style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.2);">
        <div style="position:absolute; width:380px; height:380px; right:-110px; top:-150px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.22) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>
        <div style="position:relative; display:flex; align-items:center; gap:16px;">
            <div style="display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
            </div>
            <div>
                <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);"></p>
                <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">مراجعاتي الحالية</h1>
            </div>
        </div>
    </div>

    {{-- ============ STATUS TABS ============ --}}
    <div style="display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap;">
        @foreach ($tabs as $value => $label)
            <a href="{{ route('admin.content-review.my-sessions', $value ? ['status' => $value] : []) }}"
               class="cr-tab"
               style="padding:9px 18px; border-radius:10px; font-size:12.5px; font-weight:700; text-decoration:none;
                      {{ $currentStatus === $value ? 'background:#00537A; color:#fff;' : 'background:#EFFAFD; color:#00537A; border:1.5px solid rgba(0,83,122,0.14);' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- ============ SESSIONS LIST ============ --}}
    <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:10px; box-shadow:0 10px 26px rgba(0,83,122,0.06); display:flex; flex-direction:column; gap:8px;">
        @forelse ($sessions as $review)
            @php
                $reviewable = $review->reviewable;
                $isLesson = $reviewable instanceof \App\Models\Lesson;
                $rsVal = $review->status instanceof \BackedEnum ? $review->status->value : $review->status;
                $rc = $reviewStatusColors[$rsVal] ?? $reviewStatusColors['in_review'];

                if ($isLesson) {
                    $contextLabel = $reviewable->course->name_ar ?? '—';
                    $teacher = $reviewable->course->teacher ?? null;
                    $typeLabel = 'درس';
                } else {
                    $testableTypeVal = $reviewable->testable_type;
                    $testable = $reviewable->testable;
                    $teacher = $testableTypeVal === 'course' ? ($testable->teacher ?? null) : ($testable->course->teacher ?? null);
                    $contextLabel = match ($testableTypeVal) {
                        'course' => 'اختبار كورس: ' . ($testable->name_ar ?? '—'),
                        'lesson' => 'اختبار درس: ' . ($testable->title_ar ?? '—'),
                        default  => $testableTypeVal,
                    };
                    $typeLabel = 'اختبار';
                }
                $teacherName = $teacher ? (trim(($teacher->first_name ?? '').' '.($teacher->last_name ?? '')) ?: $teacher->email) : null;
                $historyRoute = $isLesson
                    ? route('admin.content-review.lessons.history', $reviewable->id)
                    : route('admin.content-review.tests.history', $reviewable->id);
                $contentRoute = $isLesson
                    ? route('lessons.show', $reviewable->id)
                    : route('test.show', $reviewable->id);
            @endphp
            <div class="cr-row" style="display:flex; align-items:center; gap:18px; padding:18px 22px; border-radius:14px; background:rgba(0,83,122,0.03);">
                <span style="display:inline-flex; padding:5px 11px; border-radius:999px; background:{{ $rc['bg'] }}; color:{{ $rc['fg'] }}; font-size:10.5px; font-weight:700; flex-shrink:0;">{{ $reviewStatusLabels[$rsVal] ?? $rsVal }}</span>

                <div style="flex:1; min-width:0;">
                    <div style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap;">
                        <span style="font-size:10.5px; font-weight:700; color:rgba(1,60,88,0.45);">{{ $typeLabel }}</span>
                        <span style="font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; color:#013C58;">{{ $reviewable->title_en }}</span>
                        <span style="font-size:12px; color:rgba(1,60,88,0.65); font-weight:500;">{{ $reviewable->title_ar }}</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px; margin-top:5px; flex-wrap:wrap;">
                        <span style="font-size:11.5px; color:#00537A; font-weight:600;">{{ $contextLabel }}</span>
                        @if ($teacherName)
                            <span style="font-size:11.5px; color:rgba(1,60,88,0.7); font-weight:600;">{{ $teacherName }}</span>
                        @endif
                    </div>
                    @if ($rsVal === 'changes_requested' && $review->notes->isNotEmpty())
                        <div style="margin-top:8px; background:rgba(255,138,101,0.1); border:1px solid rgba(255,138,101,0.25); border-radius:10px; padding:9px 12px;">
                            <p style="margin:0; font-size:12px; color:#8A3A1A; line-height:1.6;">{{ $review->notes->first()->message }}</p>
                        </div>
                    @endif
                </div>

                <div style="display:flex; gap:8px; flex-shrink:0;">
                    <a href="{{ $contentRoute }}" title="عرض المحتوى" class="cr-action-btn" style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; background:rgba(255,211,91,0.28); color:#8A5A00; text-decoration:none;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </a>

                    <a href="{{ $historyRoute }}" title="السجل" class="cr-action-btn" style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; background:rgba(0,83,122,0.07); color:#00537A; text-decoration:none;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
                    </a>

                    @if ($rsVal === 'in_review')
                        <form action="{{ route('admin.content-review.reviews.approve', $review->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="cr-action-btn" title="موافقة" style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; border:none; background:rgba(76,175,120,0.16); color:#2E7D55; cursor:pointer;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                            </button>
                        </form>

                        <button type="button" title="طلب تعديل" class="cr-action-btn" @click="changesModalOpen = true; changesReviewId = {{ $review->id }}; changesMessage = ''"
                                style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; border:none; background:rgba(255,138,101,0.18); color:#C2591A; cursor:pointer;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                        </button>

                        <form action="{{ route('admin.content-review.reviews.release', $review->id) }}" method="POST">
                            @csrf
                            <button type="submit" title="التخلي عن المراجعة" class="cr-action-btn" style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; border:none; background:rgba(1,60,88,0.07); color:rgba(1,60,88,0.6); cursor:pointer;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="M16 17l5-5-5-5"></path><path d="M21 12H9"></path></svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:14px; padding:64px 20px;">
                <div style="display:flex; align-items:center; justify-content:center; width:64px; height:64px; border-radius:18px; background:rgba(168,232,249,0.25); color:#0E6A96;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                </div>
                <div style="text-align:center;">
                    <p style="margin:0; font-size:14.5px; font-weight:700; color:#013C58;">لايوجد مراجعات حاليا</p>
                    <p style="margin:5px 0 0; font-size:12.5px; color:rgba(1,60,88,0.45);">استلمي محتوى من طابور المراجعة لتبدأ</p>
                </div>
            </div>
        @endforelse
    </div>

    @if ($sessions instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div style="margin-top:22px;">{{ $sessions->appends(request()->query())->links('vendor.pagination.lessons') }}</div>
    @endif

    {{-- ============ REQUEST CHANGES MODAL ============ --}}
    <div x-show="changesModalOpen" x-cloak style="position:fixed; inset:0; z-index:60; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px);" @click="changesModalOpen = false">
        <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
            <div @click.stop style="width:100%; max-width:480px; background:#EFFAFD; border-radius:22px; padding:28px;" dir="rtl">
                <h3 style="margin:0 0 6px; font-family:'Poppins',sans-serif; font-weight:800; font-size:16px; color:#013C58;">طلب تعديل</h3>
                <p style="margin:0 0 16px; font-size:12.5px; color:rgba(1,60,88,0.55);">اكتب  ملاحظاتك للمعلّم عن التعديلات المطلوبة (٥ أحرف على الأقل).</p>
                <form :action="'{{ url('admin/content-review/reviews') }}/' + changesReviewId + '/request-changes'" method="POST">
                    @csrf
                    <div style="border:1.5px solid rgba(0,83,122,0.14); border-radius:11px; background:#FBFEFF; margin-bottom:16px;">
                        <textarea name="message" x-model="changesMessage" rows="4" required minlength="5" placeholder="مثال: الفيديو غير واضح، الرجاء إعادة رفعه بجودة أفضل." style="width:100%; background:transparent; border:none; outline:none; padding:12px 14px; font-size:13px; color:#013C58; font-family:'Tajawal',sans-serif; resize:vertical;"></textarea>
                    </div>
                    <div style="display:flex; gap:10px; justify-content:flex-end;">
                        <button type="button" @click="changesModalOpen = false" style="padding:11px 20px; border-radius:10px; border:1.5px solid rgba(0,83,122,0.16); background:transparent; color:#00537A; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">إلغاء</button>
                        <button type="submit" style="padding:11px 24px; border-radius:10px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">إرسال طلب التعديل</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
