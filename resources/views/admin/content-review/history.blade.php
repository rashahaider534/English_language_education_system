@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .cr-hero, .cr-item { animation: lessonsFadeUp 0.4s ease both; }
</style>
@endpush

@section('content')
@php
    $reviewStatusLabels = ['in_review' => 'قيد المراجعة', 'approved' => 'تمت الموافقة', 'changes_requested' => 'طُلب تعديل', 'released' => 'مُعاد للطابور'];
    $reviewStatusColors = [
        'in_review'         => ['bg' => 'rgba(14,106,150,0.14)', 'fg' => '#0E6A96', 'dot' => '#0E6A96'],
        'approved'          => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55', 'dot' => '#4CAF78'],
        'changes_requested' => ['bg' => 'rgba(255,138,101,0.18)', 'fg' => '#C2591A', 'dot' => '#FF8A65'],
        'released'          => ['bg' => 'rgba(1,60,88,0.1)', 'fg' => 'rgba(1,60,88,0.55)', 'dot' => 'rgba(1,60,88,0.4)'],
    ];
@endphp
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    <div style="margin-bottom:18px;">
        <a href="{{ url()->previous() }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            رجوع
        </a>
    </div>

    <div class="cr-hero" style="background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:22px; padding:26px 32px; margin-bottom:26px; box-shadow:0 20px 44px rgba(1,60,88,0.2);">
        <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">سجل التدقيق</p>
        <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:21px; color:#fff;">{{ $title }}</h1>
    </div>

    <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:28px;">
        @forelse ($history as $entry)
            @php
                $isSession = $entry['type'] === 'review_session';
                $item = $entry['data'];
            @endphp
            <div class="cr-item" style="display:flex; gap:16px; {{ !$loop->last ? 'padding-bottom:22px; margin-bottom:22px; border-bottom:1px dashed rgba(0,83,122,0.14);' : '' }}">
                <div style="flex-shrink:0; display:flex; flex-direction:column; align-items:center;">
                    @if ($isSession)
                        @php $rsVal = $item->status instanceof \BackedEnum ? $item->status->value : $item->status; $rc = $reviewStatusColors[$rsVal] ?? $reviewStatusColors['in_review']; @endphp
                        <span style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; background:{{ $rc['bg'] }}; color:{{ $rc['fg'] }};">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="10" cy="7" r="4"></circle></svg>
                        </span>
                    @else
                        <span style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; background:rgba(255,211,91,0.22); color:#8A5A00;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"></path><circle cx="12" cy="12" r="9"></circle></svg>
                        </span>
                    @endif
                </div>

                <div style="flex:1; min-width:0;">
                    @if ($isSession)
                        @php $reviewerName = $item->reviewer ? trim(($item->reviewer->first_name ?? '').' '.($item->reviewer->last_name ?? '')) : 'غير معروف'; @endphp
                        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                            <span style="display:inline-flex; padding:4px 11px; border-radius:999px; background:{{ $rc['bg'] }}; color:{{ $rc['fg'] }}; font-size:11px; font-weight:700;">{{ $reviewStatusLabels[$rsVal] ?? $rsVal }}</span>
                            <span style="font-size:13px; font-weight:700; color:#013C58;">{{ $reviewerName }}</span>
                        </div>
                        <p style="margin:6px 0 0; font-size:11.5px; color:rgba(1,60,88,0.5);">
                            استلم بتاريخ {{ $item->claimed_at?->format('Y-m-d H:i') }}
                            @if ($item->completed_at) — انتهى بتاريخ {{ $item->completed_at->format('Y-m-d H:i') }} @endif
                        </p>
                        @foreach ($item->notes as $note)
                            <div style="margin-top:10px; background:#FBFEFF; border:1.5px solid rgba(0,83,122,0.1); border-radius:11px; padding:12px 14px;">
                                <p style="margin:0; font-size:12.5px; color:#013C58; line-height:1.7;">{{ $note->message }}</p>
                            </div>
                        @endforeach
                    @else
                        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                            <span style="font-size:13px; font-weight:700; color:#013C58;">{{ $item->is_system_generated ? 'ملاحظة تلقائية من النظام' : 'ملاحظة' }}</span>
                        </div>
                        <p style="margin:6px 0 0; font-size:11.5px; color:rgba(1,60,88,0.5);">{{ $item->created_at?->format('Y-m-d H:i') }}</p>
                        <div style="margin-top:10px; background:#FBFEFF; border:1.5px solid rgba(0,83,122,0.1); border-radius:11px; padding:12px 14px;">
                            <p style="margin:0; font-size:12.5px; color:#013C58; line-height:1.7;">{{ $item->message }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p style="text-align:center; color:rgba(1,60,88,0.45); font-weight:600; font-size:13px; padding:40px 0;">لايوجد  سجل تدقيق لهذا المحتوى </p>
        @endforelse
    </div>
</div>
@endsection
