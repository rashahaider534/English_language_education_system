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
@php
    $canReply = auth()->user()->hasRole('super-admin', 'web');
@endphp
<div
    x-data="{ replyModalOpen: false, replyTargetId: null, replyTargetName: '' }"
    class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl"
>

    @if (session('success'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(168,232,249,0.18); color:#00537A; border:1px solid rgba(0,83,122,0.14); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(255,138,101,0.14); color:#C2591A; border:1px solid rgba(255,138,101,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->has('error'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(255,138,101,0.14); color:#C2591A; border:1px solid rgba(255,138,101,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
            {{ $errors->first('error') }}
        </div>
    @endif

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
                <p style="margin:8px 0 0; font-size:12.5px; color:rgba(255,255,255,0.7);">إجمالي الرسائل: {{ $unreadCount + $readCount }}</p>
            </div>
        </div>
    </div>

    {{-- ============ FILTER TABS ============ --}}
    @php
        $tabBase = 'display:inline-flex; align-items:center; gap:7px; padding:10px 18px; border-radius:11px; font-size:12.5px; font-weight:700; text-decoration:none; transition:background 0.15s ease;';
    @endphp
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:18px;">
        <a href="{{ route('admin.complaints.index') }}" style="{{ $tabBase }} {{ !$status ? 'background:#013C58; color:#fff;' : 'background:#EFFAFD; color:rgba(1,60,88,0.6);' }}">الكل ({{ $unreadCount + $readCount }})</a>
        <a href="{{ route('admin.complaints.index', ['status' => 'unread']) }}" style="{{ $tabBase }} {{ $status === 'unread' ? 'background:#013C58; color:#fff;' : 'background:#EFFAFD; color:rgba(1,60,88,0.6);' }}">غير مقروءة ({{ $unreadCount }})</a>
        <a href="{{ route('admin.complaints.index', ['status' => 'read']) }}" style="{{ $tabBase }} {{ $status === 'read' ? 'background:#013C58; color:#fff;' : 'background:#EFFAFD; color:rgba(1,60,88,0.6);' }}">مقروءة ({{ $readCount }})</a>
    </div>

    {{-- ============ COMPLAINTS LIST ============ --}}
    <div style="display:flex; flex-direction:column; gap:14px;">
        @forelse ($complaints as $complaint)
            @php
                $sender = $complaint->user;
                $senderName = $sender ? (trim(($sender->first_name ?? '').' '.($sender->last_name ?? '')) ?: $sender->email) : 'مستخدم محذوف';
                $initials = $sender ? strtoupper(substr($sender->first_name ?? $sender->email, 0, 1).substr($sender->last_name ?? '', 0, 1)) : '?';
                $isRead = (bool) $complaint->read;
            @endphp
            <div class="cmp-card" style="position:relative; overflow:hidden; background:{{ $isRead ? '#F7FAFB' : '#FFF9F6' }}; border:1.5px solid {{ $isRead ? 'rgba(0,83,122,0.12)' : 'rgba(255,138,101,0.32)' }}; border-radius:18px; padding:18px 22px 18px 26px; display:flex; gap:16px; align-items:flex-start; box-shadow:0 10px 24px rgba(229,72,77,0.06);">
                <div style="position:absolute; top:0; bottom:0; left:0; width:5px; background:{{ $isRead ? 'rgba(0,83,122,0.2)' : 'linear-gradient(180deg,#FF8A65,#C2591A)' }};"></div>

                <div style="position:relative; display:flex; align-items:center; justify-content:center; width:42px; height:42px; border-radius:13px; background:{{ $isRead ? 'rgba(0,83,122,0.08)' : 'rgba(255,138,101,0.16)' }}; color:{{ $isRead ? '#00537A' : '#C2591A' }}; flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; margin-bottom:9px;">
                        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                            @if ($isRead)
                                <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:999px; background:rgba(76,175,120,0.16); color:#2E7D55; font-size:10.5px; font-weight:800; letter-spacing:0.3px;">تم الرد</span>
                            @else
                                <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:999px; background:rgba(255,138,101,0.16); color:#C2591A; font-size:10.5px; font-weight:800; letter-spacing:0.3px;">شكوى</span>
                            @endif
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

                    @if ($canReply && $sender && !$isRead)
                        <div style="margin-top:12px; display:flex; justify-content:flex-end;">
                            <button type="button"
                                @click="replyModalOpen = true; replyTargetId = {{ $complaint->id }}; replyTargetName = {{ Illuminate\Support\Js::from($senderName) }}"
                                style="display:inline-flex; align-items:center; gap:7px; padding:9px 18px; border-radius:10px; border:none; background:linear-gradient(90deg,#0E6A96,#146B93); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:12.5px; cursor:pointer;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15.5a2 2 0 0 1-2 2H8l-5 4V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v9.5Z"></path></svg>
                                رد على الشكوى
                            </button>
                        </div>
                    @endif
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

    {{-- ============ REPLY MODAL ============ --}}
    @if ($canReply)
        <div x-show="replyModalOpen" x-cloak
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             style="position:fixed; inset:0; z-index:50; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px); overflow-y:auto;"
             @click="replyModalOpen = false">
            <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
                <div @click.stop x-data="{ replyText: '' }"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     style="position:relative; width:100%; max-width:460px; background:#fff; border-radius:22px; padding:28px 26px 26px; box-shadow:0 30px 70px rgba(1,60,88,0.3); overflow:hidden;">
                    <div style="position:absolute; width:220px; height:220px; right:-80px; top:-100px; border-radius:50%; background:radial-gradient(circle, rgba(14,106,150,0.08) 0%, rgba(14,106,150,0) 70%); pointer-events:none;"></div>

                    <div style="position:relative; text-align:center; margin-bottom:18px;">
                        <div style="width:52px; height:52px; border-radius:16px; background:rgba(14,106,150,0.1); color:#0E6A96; display:flex; align-items:center; justify-content:center; margin:0 auto 14px;">
                            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15.5a2 2 0 0 1-2 2H8l-5 4V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v9.5Z"></path></svg>
                        </div>
                        <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:16px; color:#013C58;">الرد على شكوى "<span x-text="replyTargetName"></span>"</h3>
                        <p style="margin:8px 0 0; font-size:12.5px; color:rgba(1,60,88,0.55); line-height:1.6;">هذا الرد سيُرسَل كإيميل مباشرة لصندوق بريد الطالب.</p>
                    </div>

                    <form :action="'/complaints/' + replyTargetId + '/reply'" method="POST" style="position:relative;">
                        @csrf
                        <textarea name="reply_text" x-model="replyText" rows="5" minlength="10" maxlength="5000" required
                            placeholder="اكتب نص الرد هنا... (١٠ أحرف على الأقل)"
                            style="width:100%; border:1.5px solid rgba(0,83,122,0.16); border-radius:12px; padding:12px 14px; font-size:13px; font-family:'Tajawal',sans-serif; color:#013C58; resize:vertical; outline:none; transition:border-color 0.15s ease;"
                            onfocus="this.style.borderColor='#0E6A96'" onblur="this.style.borderColor='rgba(0,83,122,0.16)'"></textarea>
                        <p style="margin:6px 0 0; font-size:11px; color:rgba(1,60,88,0.4); text-align:left;" x-text="replyText.length + ' / 5000'"></p>
                        <div style="display:flex; gap:10px; margin-top:16px;">
                            <button type="button" @click="replyModalOpen = false"
                                style="flex:1; padding:11px; border-radius:11px; border:1.5px solid rgba(0,83,122,0.12); background:#EFFAFD; color:#013C58; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer;">إلغاء</button>
                            <button type="submit" :disabled="replyText.length < 10"
                                :style="'flex:1; padding:11px; border-radius:11px; border:none; background:linear-gradient(90deg,#0E6A96,#146B93); color:#fff; font-family:Poppins,sans-serif; font-weight:700; font-size:13px; ' + (replyText.length < 10 ? 'opacity:0.45; cursor:not-allowed;' : 'cursor:pointer;')">إرسال الرد</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
