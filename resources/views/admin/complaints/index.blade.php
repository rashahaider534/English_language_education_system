@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes complaintsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .cmp-hero, .cmp-card { animation: complaintsFadeUp 0.4s ease both; }
    .cmp-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .cmp-card:hover { transform: translateY(-3px); box-shadow: 0 18px 36px rgba(0,83,122,0.1); }
</style>
@endpush

@section('content')
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    {{-- ============ HERO ============ --}}
    <div class="cmp-hero" style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.2) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:center; gap:16px;">
            <div style="display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15.5a2 2 0 0 1-2 2H8l-5 4V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v9.5Z" /><path d="M12 7.2v4M12 14.2h.01" /></svg>
            </div>
            <div>
                <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">ملاحظات المستخدمين</p>
                <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">صندوق الشكاوي</h1>
                <p style="margin:8px 0 0; font-size:12.5px; color:rgba(255,255,255,0.7);">إجمالي الرسائل: {{ $complaints->total() }}</p>
            </div>
        </div>
    </div>

    {{-- ============ COMPLAINTS LIST ============ --}}
    <div style="display:flex; flex-direction:column; gap:14px;">
        @forelse ($complaints as $complaint)
            @php
                $sender = $complaint->user;
                $senderName = $sender ? (trim(($sender->first_name ?? '').' '.($sender->last_name ?? '')) ?: $sender->email) : 'مستخدم محذوف';
                $initials = $sender ? strtoupper(substr($sender->first_name ?? $sender->email, 0, 1).substr($sender->last_name ?? '', 0, 1)) : '?';
            @endphp
            <div class="cmp-card" style="position:relative; overflow:hidden; background:#FFF9F6; border:1.5px solid rgba(255,138,101,0.32); border-radius:18px; padding:18px 22px 18px 26px; display:flex; gap:16px; align-items:flex-start; box-shadow:0 10px 24px rgba(229,72,77,0.06);">
                <div style="position:absolute; top:0; bottom:0; left:0; width:5px; background:linear-gradient(180deg,#FF8A65,#C2591A);"></div>

                <div style="position:relative; display:flex; align-items:center; justify-content:center; width:42px; height:42px; border-radius:13px; background:rgba(255,138,101,0.16); color:#C2591A; flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; margin-bottom:9px;">
                        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                            <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:999px; background:rgba(255,138,101,0.16); color:#C2591A; font-size:10.5px; font-weight:800; letter-spacing:0.3px;">شكوى</span>
                            <p style="margin:0; display:flex; align-items:center; gap:7px; font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#013C58;">
                                <span style="display:flex; align-items:center; justify-content:center; width:20px; height:20px; border-radius:7px; background:#C2591A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:9.5px; flex-shrink:0;">{{ $initials }}</span>
                                {{ $senderName }}
                            </p>
                        </div>
                        <span style="display:inline-flex; align-items:center; gap:4px; font-size:11px; color:rgba(1,60,88,0.5); font-weight:600;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
                            {{ $complaint->created_at?->diffForHumans() }}
                        </span>
                    </div>
                    <p style="margin:0; font-size:13.5px; color:rgba(1,60,88,0.75); line-height:1.7; background:rgba(255,255,255,0.6); border-radius:10px; padding:10px 12px;">{{ $complaint->text }}</p>
                </div>
            </div>
        @empty
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:14px; padding:64px 20px; background:#EFFAFD; border:1.5px dashed rgba(0,83,122,0.2); border-radius:22px;">
                <div style="display:flex; align-items:center; justify-content:center; width:64px; height:64px; border-radius:18px; background:rgba(168,232,249,0.25); color:#0E6A96;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15.5a2 2 0 0 1-2 2H8l-5 4V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v9.5Z" /></svg>
                </div>
                <p style="margin:0; font-size:14.5px; font-weight:700; color:#013C58;"> لايوجد رسائل بصندوق الشكاوي حاليًا</p>
            </div>
        @endforelse
    </div>

    @if ($complaints instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div style="margin-top:22px;">{{ $complaints->links('vendor.pagination.lessons') }}</div>
    @endif
</div>
@endsection
