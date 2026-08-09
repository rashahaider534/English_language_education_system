@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .t-hero, .t-stat, .t-row { animation: lessonsFadeUp 0.4s ease both; }

    .t-row { transition: background 0.15s ease; }
    .t-row:hover { background: rgba(168,232,249,0.1); }

    .t-icon-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .t-icon-btn:hover { transform: translateY(-2px) scale(1.05); box-shadow: 0 8px 16px rgba(1,60,88,0.16); }

    .t-create-btn, .t-secondary-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .t-create-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(245,162,1,0.28); }
    .t-secondary-btn:hover { transform: translateY(-2px); }
</style>
@endpush

@section('content')
@php
    $statusLabels = [
        'draft' => 'مسودة', 'pending' => 'قيد الإرسال', 'in_review' => 'قيد المراجعة',
        'changes_requested' => 'مطلوب تعديل', 'approved' => 'موافق عليه', 'published' => 'منشور',
        'archived' => 'مؤرشف', 'closed' => 'مغلق',
    ];
    $statusColors = [
        'draft'              => ['bg' => 'rgba(0,83,122,0.1)', 'fg' => '#00537A'],
        'pending'            => ['bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00'],
        'in_review'          => ['bg' => 'rgba(14,106,150,0.14)', 'fg' => '#0E6A96'],
        'changes_requested'  => ['bg' => 'rgba(255,138,101,0.18)', 'fg' => '#C2591A'],
        'approved'           => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55'],
        'published'          => ['bg' => 'rgba(76,175,120,0.22)', 'fg' => '#1E5C3B'],
        'archived'           => ['bg' => 'rgba(1,60,88,0.1)', 'fg' => 'rgba(1,60,88,0.55)'],
        'closed'             => ['bg' => 'rgba(229,72,77,0.14)', 'fg' => '#C2323A'],
    ];
    $totalCount = $tests->total();
@endphp
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    @if (session('success'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(168,232,249,0.18); color:#00537A; border:1px solid rgba(0,83,122,0.14); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div style="display:flex; align-items:center; gap:10px; background:rgba(255,138,101,0.14); color:#C2591A; border:1px solid rgba(255,138,101,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            {{ $errors->first() }}
        </div>
    @endif

    <div style="margin-bottom:18px;">
        <a href="{{ route('levels.index') }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            العودة للمستويات
        </a>
    </div>

    {{-- ============ HERO ============ --}}
    <div class="t-hero" style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.2) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:22px;">
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M8 2v4M16 2v4M3 10h18"></path></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">اختبارات المستوى</p>
                    <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">اختبارات مستوى: {{ $level->name_ar }}</h1>
                </div>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="{{ route('tests.level.levelTest.create', $level) }}" class="t-create-btn" style="display:inline-flex; align-items:center; gap:8px; padding:12px 22px; border-radius:12px; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; text-decoration:none; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"></path></svg>
                    اختبار جديد
                </a>
            </div>
        </div>

        <div style="position:relative; display:flex; gap:14px; flex-wrap:wrap;">
            <div class="t-stat" style="display:flex; align-items:center; gap:13px; background:rgba(255,211,91,0.08); border:1px solid rgba(255,211,91,0.22); border-radius:16px; padding:14px 18px; flex:1; min-width:150px;">
                <div style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.14); color:#FFD35B; flex-shrink:0;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M3 10h18"></path></svg></div>
                <div><p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,236,176,0.85);">إجمالي الاختبارات</p><p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $totalCount }}</p></div>
            </div>
        </div>
    </div>

    {{-- ============ TESTS TABLE ============ --}}
    <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; overflow:hidden; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
            <thead>
                <tr>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:8%;">#</th>
                    <th style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:38%;">عنوان الاختبار</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:16%;">الحالة</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:18%;">تاريخ الإنشاء</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:20%;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tests as $test)
                    @php
                        $statusVal = $test->status?->value ?? $test->status;
                        $sc = $statusColors[$statusVal] ?? $statusColors['draft'];
                    @endphp
                    <tr class="t-row">
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                            <div style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:rgba(0,83,122,0.06); color:rgba(1,60,88,0.6); font-family:'Poppins',sans-serif; font-weight:700; font-size:12px;">{{ $test->id }}</div>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05);">
                            <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#00537A; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $test->title_en }}</div>
                            <div style="font-size:12px; color:#0E6A96; opacity:0.75; margin-top:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $test->title_ar }}</div>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                            <span style="display:inline-flex; padding:5px 11px; border-radius:999px; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }}; font-size:11px; font-weight:700;">{{ $statusLabels[$statusVal] ?? $statusVal }}</span>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                            <span style="font-size:12.5px; color:rgba(1,60,88,0.6); font-weight:600;">{{ $test->created_at?->format('Y-m-d') }}</span>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                            <div style="display:flex; gap:8px; justify-content:center;">
                                <a href="{{ route('tests.level.levelTest.show', ['level' => $level, 'test' => $test]) }}" title="عرض" class="t-icon-btn" style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; background:rgba(168,232,249,0.22); color:#00537A; text-decoration:none;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </a>
                                @unless (in_array($statusVal, ['archived', 'closed']))
                                    <a href="{{ route('tests.level.levelTest.edit', ['level' => $level, 'test' => $test]) }}" title="تعديل" class="t-icon-btn" style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; background:rgba(255,211,91,0.22); color:#8A5A00; text-decoration:none;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                                    </a>
                                @endunless
                                @if ($statusVal === 'draft' && auth()->user()->can('publish_levels'))
                                    <form action="{{ route('admin.content-review.tests.approve-directly', $test) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="موافقة مباشرة" class="t-icon-btn" style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; border:none; background:rgba(76,175,120,0.16); color:#2E7D55; cursor:pointer;">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:60px 20px; text-align:center; color:rgba(1,60,88,0.45); font-weight:600; font-size:14px;">لا يوجد اختبارلهذا المستوى
</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($tests instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div style="padding:16px 22px; border-top:1px solid rgba(0,83,122,0.06);">
                {{ $tests->links('vendor.pagination.lessons') }}
            </div>
        @endif
    </div>
</div>
@endsection
