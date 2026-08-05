@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .lex-header, .lex-panel { animation: lessonsFadeUp 0.4s ease both; }
    .lex-panel:nth-child(1) { animation-delay: 0.05s; }
    .lex-panel:nth-child(2) { animation-delay: 0.1s; }

    .lex-back-btn { transition: background 0.15s ease, transform 0.15s ease; }
    .lex-back-btn:hover { background: rgba(0,83,122,0.1); transform: translateY(-1px); }

    .lex-file { transition: background 0.15s ease, border-color 0.15s ease, transform 0.15s ease; }
    .lex-file:hover { background: rgba(0,83,122,0.07); border-color: rgba(0,83,122,0.25); transform: translateX(-2px); }

    .lex-level-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .lex-level-card:hover { transform: translateY(-3px); box-shadow: 0 14px 28px rgba(1,60,88,0.12); }

    .lex-action-btn { transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease; }
    .lex-action-btn:not(:disabled):hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(1,60,88,0.16); }
</style>
@endpush

@section('content')
@php
    $statusLabels = ['pending' => 'قيد الانتظار', 'in_review' => 'قيد المراجعة', 'approved' => 'موافق عليه', 'rejected' => 'مرفوض'];
    $statusColors = [
        'pending'   => ['bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00', 'dot' => '#F5A201'],
        'in_review' => ['bg' => 'rgba(14,106,150,0.14)', 'fg' => '#0E6A96', 'dot' => '#0E6A96'],
        'approved'  => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55', 'dot' => '#4CAF78'],
        'rejected'  => ['bg' => 'rgba(255,138,101,0.18)', 'fg' => '#C2591A', 'dot' => '#FF8A65'],
    ];
    $status = $levelException->status->value;
    $sc = $statusColors[$status] ?? $statusColors['pending'];

    $student = $levelException->user;
    $studentName = $student ? (trim(($student->first_name ?? '').' '.($student->last_name ?? '')) ?: $student->email) : '—';
    $studentInitials = $student ? strtoupper(substr($student->first_name ?? $student->email, 0, 1).substr($student->last_name ?? '', 0, 1)) : '?';

    $attachments = $levelException->getMedia('attachments');

    $canStartReview = $status === 'pending';
    $canDecide = $status === 'in_review';
    $isResolved = in_array($status, ['approved', 'rejected']);
    $progressPct = $isResolved ? 100 : ($canDecide ? 50 : 0);

    $executor = $levelException->admin;
    $executorName = $executor ? (trim(($executor->first_name ?? '').' '.($executor->last_name ?? '')) ?: $executor->email) : null;

    $requestedLevel = $levelException->requestedLevel;
    $recommendedLevel = $levelException->recommendedLevel;
@endphp
<div
    x-data="{ notes: '', noteWarning: false }"
    class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8"
    style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl"
>
    @if (session('success'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(168,232,249,0.18); color:#00537A; border:1px solid rgba(0,83,122,0.14); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background:rgba(255,138,101,0.14); color:#C2591A; border:1px solid rgba(255,138,101,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13px; font-weight:600;">
            @foreach ($errors->all() as $error)
                <p style="margin:0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div style="margin-bottom:18px;">
        <a href="{{ route('levelException.index') }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            العودة للقائمة
        </a>
    </div>

    {{-- ============ HERO HEADER ============ --}}
    <div style="border-radius:23px; padding:1.5px; background:linear-gradient(135deg, rgba(255,211,91,0.5), rgba(14,106,150,0.4)); margin-bottom:24px; box-shadow:0 20px 44px rgba(1,60,88,0.16);">
    <section class="lex-header" style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:22px; padding:36px 40px; display:flex; align-items:center; justify-content:space-between; gap:24px; flex-wrap:wrap;">
        <div style="position:absolute; width:260px; height:260px; right:-90px; top:-120px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.26) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>
        <div style="position:absolute; width:220px; height:220px; left:35%; top:-110px; border-radius:50%; background:radial-gradient(circle, rgba(148,148,148,0.2) 0%, rgba(148,148,148,0) 70%); pointer-events:none;"></div>
        <div style="position:absolute; width:320px; height:320px; left:-100px; bottom:-140px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.2) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:center; gap:22px;">
            <div style="position:relative; width:76px; height:76px; border-radius:50%; border:3px solid rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.1); flex-shrink:0;">
                <span style="font-family:'Poppins',sans-serif; font-weight:800; font-size:24px; color:#fff;">{{ $studentInitials }}</span>
            </div>
            <div>
                <h1 style="margin:0 0 6px; font-family:'Poppins',sans-serif; font-weight:800; font-size:26px; color:#fff;">{{ $studentName }}</h1>
                <p style="margin:0; display:flex; align-items:center; gap:6px; font-size:13.5px; color:rgba(168,232,249,0.85);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z" opacity="0"></path><path d="M22 6c0-1.1-.9-2-2-2H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6Z"></path><path d="m22 6-10 7L2 6"></path></svg>
                    {{ $student->email ?? '' }}
                </p>
                <div style="margin-top:12px; display:flex; align-items:center; gap:10px;">
                    <span style="background:rgba(255,255,255,0.1); color:#fff; font-size:11px; font-weight:700; padding:5px 12px; border-radius:999px; border:1px solid rgba(255,255,255,0.18);">رقم الطالب ({{ $student->id ?? '—' }})</span>
                </div>
            </div>
        </div>

        <div style="position:relative; display:flex; align-items:center; gap:14px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.16); border-radius:16px; padding:14px 24px; backdrop-filter:blur(6px);">
            <div style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:{{ str_replace(['0.16', '0.14', '0.18'], '0.32', $sc['bg']) }}; flex-shrink:0;">
                @if ($status === 'approved')
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                @elseif ($status === 'rejected')
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="M6 6l12 12"></path></svg>
                @elseif ($status === 'in_review')
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="10.5" cy="10.5" r="6.5"></circle><path d="m19 19-4-4"></path></svg>
                @else
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8"></circle><path d="M12 8v4l2.5 2.5"></path></svg>
                @endif
            </div>
            <div>
                <p style="margin:0; font-size:10px; font-weight:700; letter-spacing:0.6px; text-transform:uppercase; color:rgba(168,232,249,0.7);">الحالة الحالية</p>
                <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:16px; color:#fff;">{{ $statusLabels[$status] ?? $status }}</p>
            </div>
        </div>
    </section>
    </div>

    <div style="display:grid; grid-template-columns:1fr 340px; gap:24px; align-items:start;">

        {{-- ============ LEFT COLUMN ============ --}}
        <div style="display:flex; flex-direction:column; gap:24px;">

            {{-- Progress Stepper --}}
            <div class="lex-panel" style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.6); border-radius:20px; padding:30px 32px 26px; box-shadow:0 0 18px rgba(255,186,66,0.26), 0 10px 26px rgba(0,83,122,0.06);">
                <h3 style="margin:0 0 30px; font-family:'Poppins',sans-serif; font-weight:800; font-size:16px; color:#013C58;">
                    مسار الطلب
                </h3>
                {{-- Real: derived directly from levelException->status (pending / in_review / approved|rejected) --}}
                <div style="position:relative; display:flex; justify-content:space-between;">
                    <div style="position:absolute; top:19px; right:0; left:0; height:3px; background:rgba(0,83,122,0.1); border-radius:2px;"></div>
                    <div style="position:absolute; top:19px; right:0; width:{{ $progressPct }}%; height:3px; background:linear-gradient(90deg, #0E6A96, #F5A201); border-radius:2px; transition:width 0.4s ease;"></div>

                    <div style="position:relative; z-index:1; display:flex; flex-direction:column; align-items:center; flex:1;">
                        <div style="width:40px; height:40px; border-radius:50%; background:rgba(14,106,150,0.14); border:2px solid #0E6A96; color:#0E6A96; display:flex; align-items:center; justify-content:center;">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                        </div>
                        <span style="margin-top:10px; font-size:12.5px; font-weight:700; color:#013C58;">تم الإرسال</span>
                        <span style="font-size:10.5px; color:rgba(1,60,88,0.4);">{{ $levelException->created_at ? $levelException->created_at->format('Y-m-d') : '—' }}</span>
                    </div>

                    <div style="position:relative; z-index:1; display:flex; flex-direction:column; align-items:center; flex:1;">
                        <div style="width:40px; height:40px; border-radius:50%; background:{{ $progressPct >= 50 ? 'rgba(255,186,66,0.18)' : '#EFFAFD' }}; border:2px solid {{ $progressPct >= 50 ? '#F5A201' : 'rgba(0,83,122,0.2)' }}; color:{{ $progressPct >= 50 ? '#8A5A00' : 'rgba(1,60,88,0.35)' }}; display:flex; align-items:center; justify-content:center; {{ $canDecide ? 'box-shadow:0 0 0 5px rgba(245,162,1,0.14);' : '' }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </div>
                        <span style="margin-top:10px; font-size:12.5px; font-weight:700; color:{{ $progressPct >= 50 ? '#013C58' : 'rgba(1,60,88,0.4)' }};">قيد المراجعة</span>
                        <span style="font-size:10.5px; color:rgba(1,60,88,0.4);">{{ $canDecide ? 'قيد التنفيذ الآن' : ($progressPct >= 50 ? 'تم' : '-- -- --') }}</span>
                    </div>

                    <div style="position:relative; z-index:1; display:flex; flex-direction:column; align-items:center; flex:1;">
                        <div style="width:40px; height:40px; border-radius:50%; background:{{ $isResolved ? $sc['bg'] : '#EFFAFD' }}; border:2px solid {{ $isResolved ? $sc['dot'] : 'rgba(0,83,122,0.2)' }}; color:{{ $isResolved ? $sc['fg'] : 'rgba(1,60,88,0.35)' }}; display:flex; align-items:center; justify-content:center;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"></path><circle cx="12" cy="12" r="9"></circle></svg>
                        </div>
                        <span style="margin-top:10px; font-size:12.5px; font-weight:700; color:{{ $isResolved ? '#013C58' : 'rgba(1,60,88,0.4)' }};">القرار النهائي</span>
                        <span style="font-size:10.5px; color:rgba(1,60,88,0.4);">{{ $isResolved ? $statusLabels[$status] : '-- -- --' }}</span>
                    </div>
                </div>
            </div>

            {{-- Level Comparison --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px;">
                <div class="lex-level-card" style="background:#EFFAFD; border-top:4px solid rgba(0,83,122,0.25); border-radius:16px; padding:26px 20px; text-align:center; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
                    <span style="font-size:13px; font-weight:800; color:#00537A;">المستوى المطلوب</span>
                    <div style="width:64px; height:64px; border-radius:16px; background:rgba(0,83,122,0.07); display:flex; align-items:center; justify-content:center; margin:14px auto;">
                        <span style="font-family:'Poppins',sans-serif; font-weight:800; font-size:22px; color:#00537A;">{{ $requestedLevel->order ?? '—' }}</span>
                    </div>
                    <h4 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:16px; color:#013C58;">{{ $requestedLevel->name_ar ?? '—' }}</h4>
                </div>

                <div class="lex-level-card" style="background:#EFFAFD; border-top:4px solid rgba(255,186,66,0.5); border-radius:16px; padding:26px 20px; text-align:center; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
                    <span style="font-size:13px; font-weight:800; color:#8A5A00;">مستوى بديل مقترح</span>
                    <div style="width:64px; height:64px; border-radius:16px; background:rgba(255,211,91,0.16); display:flex; align-items:center; justify-content:center; margin:14px auto;">
                        <span style="font-family:'Poppins',sans-serif; font-weight:800; font-size:22px; color:#8A5A00;">{{ $recommendedLevel->order ?? '—' }}</span>
                    </div>
                    <h4 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:16px; color:#013C58;">{{ $recommendedLevel->name_ar ?? '—' }}</h4>
                </div>
            </div>

            {{-- Reason --}}
            <div class="lex-panel" style="position:relative; background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.6); border-radius:20px; padding:28px 30px; box-shadow:0 0 18px rgba(255,186,66,0.26), 0 10px 26px rgba(0,83,122,0.06); overflow:hidden;">
                <svg width="70" height="70" viewBox="0 0 24 24" fill="rgba(0,83,122,0.05)" style="position:absolute; top:16px; left:20px;"><path d="M9 7C5.5 8.5 4 11.5 4 15c0 2.2 1.3 3.5 3 3.5s3-1.2 3-3c0-1.5-1-2.5-2.3-2.8C8 11 9 9 11 8Z"></path><path d="M18 7c-3.5 1.5-5 4.5-5 8 0 2.2 1.3 3.5 3 3.5s3-1.2 3-3c0-1.5-1-2.5-2.3-2.8C17 11 18 9 20 8Z"></path></svg>
                <h3 style="position:relative; margin:0 0 18px; font-family:'Poppins',sans-serif; font-weight:800; font-size:16px; color:#013C58;">
                    سبب طلب التغيير
                </h3>
                <div style="position:relative; background:linear-gradient(160deg, rgba(168,232,249,0.18), rgba(255,211,91,0.1)); border-radius:12px; border-inline-start:4px solid #A8E8F9; padding:18px 20px;">
                    <p style="margin:0; font-size:14px; color:#013C58; font-weight:500; line-height:1.85;">"{{ $levelException->reason }}"</p>
                </div>
            </div>
        </div>

        {{-- ============ RIGHT COLUMN ============ --}}
        <div style="display:flex; flex-direction:column; gap:20px;">

            @if ($canStartReview)
                <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:18px; padding:24px 26px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                        <div style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; background:rgba(0,83,122,0.08); color:#00537A; flex-shrink:0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><path d="M12 1v6m0 6v6"></path></svg>
                        </div>
                        <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">بدء المراجعة</h3>
                    </div>
                    <div>
                        <p style="margin:0 0 16px; font-size:12px; color:rgba(1,60,88,0.55); line-height:1.6;">اضغطي الزر لتحويل حالة الطلب إلى قيد المراجعة</p>
                        <form action="{{ route('levelException.review', $levelException) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="lex-action-btn" style="width:100%; display:flex; align-items:center; justify-content:center; gap:8px; padding:13px; border-radius:11px; border:none; background:linear-gradient(135deg,#013C58,#0E6A96); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">
                                بدء المراجعة
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if ($canDecide)
                <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:18px; overflow:hidden; box-shadow:0 10px 26px rgba(0,83,122,0.08);">
                    <div style="background:linear-gradient(135deg,#013C58,#00537A); padding:18px 22px;">
                        <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#fff;">
                            قرار المراجعة
                        </h3>
                    </div>
                    <div style="padding:22px;">
                        <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:8px;">ملاحظات المراجع (10 أحرف على الأقل)</label>
                        <textarea x-model="notes" @input="if (notes.trim().length >= 10) noteWarning = false" placeholder="أدخل ملاحظاتك حول هذا الطلب هنا..." rows="5" style="width:100%; padding:12px; border:1.5px solid rgba(0,83,122,0.14); border-radius:11px; background:rgba(168,232,249,0.1); color:#013C58; font-size:12.5px; font-family:'Tajawal',sans-serif; line-height:1.6; margin-bottom:16px; outline:none; resize:vertical;"></textarea>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                            <form action="{{ route('levelException.reject', $levelException) }}" method="POST" @submit="if (notes.trim().length < 10) { $event.preventDefault(); noteWarning = true; }">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="review_note" :value="notes">
                                <button type="submit" class="lex-action-btn" style="width:100%; display:flex; align-items:center; justify-content:center; gap:6px; padding:12px; border-radius:11px; border:none; background:rgba(229,72,77,0.85); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:12.5px; cursor:pointer; box-shadow:0 4px 10px rgba(229,72,77,0.14);">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="M6 6l12 12"></path></svg>
                                    رفض
                                </button>
                            </form>
                            <form action="{{ route('levelException.approve', $levelException) }}" method="POST" @submit="if (notes.trim().length < 10) { $event.preventDefault(); noteWarning = true; }">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="review_note" :value="notes">
                                <button type="submit" class="lex-action-btn" style="width:100%; display:flex; align-items:center; justify-content:center; gap:6px; padding:12px; border-radius:11px; border:none; background:rgba(48,164,108,0.85); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:12.5px; cursor:pointer; box-shadow:0 4px 10px rgba(48,164,108,0.14);">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                                    موافقة
                                </button>
                            </form>
                        </div>
                        <p x-show="noteWarning" x-cloak x-transition style="margin:12px 0 0; font-size:11.5px; color:#C2591A; font-weight:700; text-align:center;">⚠ يجب إدخال ملاحظة 10 أحرف على الأقل قبل المتابعة</p>
                    </div>
                </div>
            @endif

            @if ($isResolved)
                <div style="background:linear-gradient(160deg, rgba(168,232,249,0.16), rgba(255,211,91,0.08)); border:1.5px solid {{ $status === 'approved' ? 'rgba(76,175,120,0.35)' : 'rgba(255,138,101,0.35)' }}; border-radius:18px; padding:24px 26px;">
                    <h3 style="margin:0 0 4px; font-family:'Poppins',sans-serif; font-weight:800; font-size:16px; color:{{ $status === 'approved' ? '#2E7D55' : '#C2591A' }};">{{ $status === 'approved' ? 'تمت الموافقة' : 'تم الرفض' }}</h3>
                    @if ($levelException->executed_at)
                        <p style="margin:4px 0 16px; font-size:11px; color:rgba(1,60,88,0.5); font-weight:600;">{{ \Carbon\Carbon::parse($levelException->executed_at)->format('Y-m-d H:i') }} @if($executorName) · {{ $executorName }} @endif</p>
                    @endif
                    <div style="padding:14px; background:rgba(1,60,88,0.05); border-radius:12px; border:1px solid rgba(1,60,88,0.1);">
                        <p style="margin:0 0 8px; font-size:11px; font-weight:700; color:rgba(1,60,88,0.5); letter-spacing:0.3px;">ملاحظات الأدمن</p>
                        <p style="margin:0; font-size:13px; color:#013C58; font-weight:500; line-height:1.7;">{{ $levelException->review_note ?? '—' }}</p>
                    </div>
                </div>
            @endif

            {{-- Attachments --}}
            <div class="lex-panel" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:18px; padding:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
                <h3 style="margin:0 0 16px; display:flex; align-items:center; gap:8px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00537A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                    المرفقات ({{ $attachments->count() }})
                </h3>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    @forelse ($attachments as $file)
                        @php $isPdf = str_contains($file->mime_type ?? '', 'pdf'); @endphp
                        <a href="{{ $file->getUrl() }}" target="_blank" class="lex-file" style="display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:12px; background:#FBFEFF; border:1px solid rgba(0,83,122,0.12); text-decoration:none;">
                            <div style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:9px; background:{{ $isPdf ? 'rgba(229,72,77,0.1)' : 'rgba(14,106,150,0.1)' }}; color:{{ $isPdf ? '#E5484D' : '#0E6A96' }}; flex-shrink:0;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path></svg>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <p style="margin:0; font-size:12px; font-weight:700; color:#013C58; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $file->file_name }}</p>
                                <p style="margin:2px 0 0; font-size:10.5px; color:rgba(1,60,88,0.5);">{{ $file->human_readable_size }}</p>
                            </div>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00537A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        </a>
                    @empty
                        <p style="margin:0; font-size:12.5px; color:rgba(1,60,88,0.45); font-weight:600;">ما في ملفات مرفقة</p>
                    @endforelse
                </div>
            </div>

            {{-- Meta --}}
            <div style="background:rgba(0,83,122,0.04); border:1px solid rgba(0,83,122,0.1); border-radius:16px; padding:18px 20px;">
                <div style="display:flex; align-items:center; justify-content:space-between; font-size:14.5px;">
                    <span style="color:rgba(1,60,88,0.6); font-weight:600;">تاريخ الطلب:</span>
                    <span style="color:#013C58; font-weight:700;">{{ $levelException->created_at ? $levelException->created_at->format('Y-m-d، H:i') : '—' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
