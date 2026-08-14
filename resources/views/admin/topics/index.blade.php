@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes topicsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .tp-stat, .tp-card { animation: topicsFadeUp 0.45s ease both; }
    .tp-card:nth-child(2) { animation-delay: 0.04s; }
    .tp-card:nth-child(3) { animation-delay: 0.08s; }
    .tp-card:nth-child(4) { animation-delay: 0.12s; }

    .tp-card { transition: transform 0.25s cubic-bezier(0.16,1,0.3,1), box-shadow 0.25s ease; }
    .tp-card:hover { transform: translateY(-5px); box-shadow: 0 28px 56px rgba(0,83,122,0.16); }

    .tp-action-btn { transition: transform 0.15s ease, background 0.15s ease; text-decoration:none; }
    .tp-action-btn:not(:disabled):hover { transform: translateY(-1px); }

    .tp-tooltip-wrap { position:relative; display:inline-flex; }
    .tp-tooltip-box {
        position:absolute; top:calc(100% + 8px); left:50%; transform:translateX(-50%);
        background:#fff; color:#0B2436; font-size:11px; font-weight:600; white-space:nowrap;
        padding:7px 12px; border-radius:9px; border:1px solid rgba(0,83,122,0.14);
        box-shadow:0 10px 22px rgba(1,60,88,0.16); opacity:0; pointer-events:none;
        transition:opacity 0.15s ease; z-index:5;
    }
    .tp-tooltip-wrap:hover .tp-tooltip-box { opacity:1; }
</style>
@endpush

@section('content')
@php
    use App\Enums\TopicStatus;
    $totalCount = $topics->count();
    $publishedCount = $topics->where('status', TopicStatus::PUBLISHED)->count();
    $pendingCount = $topics->where('status', TopicStatus::PENDING)->count();
@endphp
<div
    x-data="{ deleteModalOpen: false, deleteTarget: null, publishModalOpen: false, publishTarget: null }"
    class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl"
>

    @if (session('success'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(168,232,249,0.18); color:#00537A; border:1px solid rgba(0,83,122,0.14); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('topic'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(255,138,101,0.14); color:#C2591A; border:1px solid rgba(255,138,101,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
            {{ $errors->first('topic') }}
        </div>
    @endif

    {{-- ============ HERO ============ --}}
    <div style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:32px 34px 26px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(168,232,249,0.25) 0%, rgba(168,232,249,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:20px;">
            <div>
                <p style="margin:0; font-size:12px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.85);">المحتوى الصوتي</p>
                <h1 style="margin:8px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:27px; color:#fff;">التوبك</h1>
                <p style="margin:8px 0 0; font-size:13.5px; color:rgba(168,232,249,0.75); max-width:440px; line-height:1.6;">إدارة مواضيع البودكاست التعليمي ونشرها للطلاب</p>
            </div>
            <a href="{{ route('topics.create') }}"
               style="display:flex; align-items:center; gap:8px; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; border:none; border-radius:13px; padding:13px 22px; font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; cursor:pointer; box-shadow:0 12px 26px rgba(0,0,0,0.18); transition:transform 0.15s, box-shadow 0.15s; text-decoration:none;"
               onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 16px 32px rgba(0,0,0,0.24)';"
               onmouseout="this.style.transform=''; this.style.boxShadow='0 12px 26px rgba(0,0,0,0.18)';">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"></path></svg>
                إضافة توبك جديد
            </a>
        </div>

        <div style="position:relative; display:flex; gap:14px; margin-top:26px; flex-wrap:wrap;">
            @php
                $statCard = 'display:flex; align-items:center; gap:13px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.14); backdrop-filter:blur(6px); border-radius:16px; padding:14px 18px; flex:1; min-width:170px;';
                $iconWrapBase = 'display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.12); flex-shrink:0;';
            @endphp
            <div class="tp-stat" style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#FFD35B;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M3 10h18"></path></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(168,232,249,0.75);">إجمالي التوبك</p>
                    <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $totalCount }}</p>
                </div>
            </div>
            <div class="tp-stat" style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#A8E8F9;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(168,232,249,0.75);">منشورة</p>
                    <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $publishedCount }}</p>
                </div>
            </div>
            <div class="tp-stat" style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#FFD35B;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(168,232,249,0.75);">قيد الانتظار</p>
                    <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $pendingCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ TOPICS GRID ============ --}}
    @php
        $statusLabels = ['pending' => 'قيد الانتظار', 'published' => 'منشور'];
        $statusColors = [
            'pending'   => ['bg' => 'rgba(255,211,91,0.92)', 'fg' => '#013C58', 'dot' => '#946200'],
            'published' => ['bg' => 'rgba(168,232,249,0.95)', 'fg' => '#013C58', 'dot' => '#00537A'],
        ];
    @endphp

    @if ($topics->isEmpty())
        <div style="background:#EFFAFD; border:1.5px dashed rgba(0,83,122,0.2); border-radius:22px; padding:60px 20px; text-align:center;">
            <div style="width:58px; height:58px; border-radius:16px; background:rgba(0,83,122,0.06); color:rgba(1,60,88,0.35); display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="3"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-4 4-3-3-4 4"></path></svg>
            </div>
            <p style="margin:0; color:rgba(1,60,88,0.5); font-weight:600; font-size:14px;">   لايوجد توبك     </p>
        </div>
    @else
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(270px, 1fr)); gap:18px;">
            @foreach ($topics as $topic)
                @php
                    $statusValue = $topic->status->value;
                    $sc = $statusColors[$statusValue] ?? $statusColors['pending'];
                    $isPublished = $topic->status === TopicStatus::PUBLISHED;
                    $imageUrl = $topic->getFirstMediaUrl('topic_image');
                    $creatorName = $topic->creator ? (trim(($topic->creator->first_name ?? '').' '.($topic->creator->last_name ?? '')) ?: $topic->creator->email) : '—';
                @endphp
                <div class="tp-card" style="position:relative; background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; overflow:hidden; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
                    <div style="position:absolute; top:0; left:0; right:0; height:3px; background:{{ $sc['dot'] }}; z-index:1;"></div>
                    <div style="position:relative; width:100%; height:138px; background:linear-gradient(135deg,#013C58,#0E6A96,#146B93); overflow:hidden;">
                        @if ($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $topic->name_en }}" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; color:rgba(255,255,255,0.3);">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3Z"></path><path d="M6 11v1a6 6 0 0 0 12 0v-1"></path><path d="M12 18v3M9 21h6"></path></svg>
                            </div>
                        @endif
                        <div style="position:absolute; inset:0; background:linear-gradient(180deg, rgba(1,42,63,0) 45%, rgba(1,42,63,0.35) 100%); pointer-events:none;"></div>
                        <span style="position:absolute; top:10px; right:10px; display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:999px; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }}; font-size:10.5px; font-weight:700; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                            <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:{{ $sc['dot'] }};"></span>{{ $statusLabels[$statusValue] ?? $statusValue }}
                        </span>
                        <div style="position:absolute; bottom:10px; left:10px; display:flex; align-items:center; gap:5px; background:rgba(1,42,63,0.6); backdrop-filter:blur(4px); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:11px; padding:3px 10px; border-radius:999px; border:1px solid rgba(255,255,255,0.14);">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3Z"></path><path d="M6 11v1a6 6 0 0 0 12 0v-1"></path></svg>
                            {{ $topic->podcasts_count }}
                        </div>
                    </div>

                    <div style="padding:17px 18px 18px;">
                        <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:15px; color:#013C58;">{{ $topic->name_en }}</div>
                        <div style="font-size:12.5px; color:rgba(1,60,88,0.5); margin-top:3px;">{{ $topic->name_ar }}</div>

                        <div style="display:flex; align-items:center; gap:4px; margin-top:12px; font-size:10.5px; color:rgba(1,60,88,0.4);">
                            <svg width="10.5" height="10.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                            أنشأه {{ $creatorName }}
                        </div>

                        <div style="display:flex; gap:8px; margin-top:14px; padding-top:14px; border-top:1px solid rgba(0,83,122,0.06); flex-wrap:wrap;">
                            <a href="{{ route('podcasts.index', $topic) }}" title="عرض البودكاست" class="tp-action-btn"
                               style="display:flex; align-items:center; justify-content:center; width:37px; height:37px; border-radius:10px; background:rgba(168,232,249,0.18); color:#00537A; flex-shrink:0;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3Z"></path><path d="M6 11v1a6 6 0 0 0 12 0v-1"></path><path d="M12 18v3M9 21h6"></path></svg>
                            </a>
                            <a href="{{ route('topics.edit', $topic) }}" title="تعديل" class="tp-action-btn"
                               style="display:flex; align-items:center; justify-content:center; width:37px; height:37px; border-radius:10px; background:rgba(0,83,122,0.07); color:#00537A; flex-shrink:0;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                            </a>

                            @if (!$isPublished)
                                <form id="publish-topic-form-{{ $topic->id }}" action="{{ route('topics.publish', $topic) }}" method="POST" style="flex-shrink:0; display:none;">
                                    @csrf
                                </form>
                                <button type="button" title="نشر" class="tp-action-btn"
                                    @click="publishModalOpen = true; publishTarget = { id: {{ $topic->id }}, name: {{ \Illuminate\Support\Js::from($topic->name_ar) }} }"
                                    style="display:flex; align-items:center; justify-content:center; width:37px; height:37px; border-radius:10px; border:none; background:rgba(76,175,120,0.14); color:#2E7D55; cursor:pointer;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
                                </button>
                            @endif

                            <form id="delete-topic-form-{{ $topic->id }}" action="{{ route('topics.delete', $topic) }}" method="POST" style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            <span class="{{ $isPublished ? 'tp-tooltip-wrap' : '' }}" style="flex-shrink:0; margin-inline-start:auto;">
                                <button type="button" title="{{ $isPublished ? '' : 'حذف' }}" class="tp-action-btn"
                                    @if($isPublished) disabled @else @click="deleteModalOpen = true; deleteTarget = { id: {{ $topic->id }}, name: {{ \Illuminate\Support\Js::from($topic->name_ar) }} }" @endif
                                    style="display:flex; align-items:center; justify-content:center; width:37px; height:37px; border-radius:10px; border:none; background:rgba(229,72,77,0.1); color:#C2591A; cursor:{{ $isPublished ? 'not-allowed' : 'pointer' }}; opacity:{{ $isPublished ? 0.4 : 1 }};">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><path d="m19 6-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path></svg>
                                </button>
                                @if ($isPublished)
                                    <span class="tp-tooltip-box">لا يمكن حذف توبك منشور</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ============ PUBLISH CONFIRM MODAL ============ --}}
    <div x-show="publishModalOpen" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="position:fixed; inset:0; z-index:50; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px); overflow-y:auto;"
         @click="publishModalOpen = false">
      <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
        <div @click.stop
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             style="width:100%; max-width:400px; background:#EFFAFD; border-radius:22px; padding:30px 26px; box-shadow:0 44px 100px rgba(1,42,63,0.4); text-align:center;">
            <div style="width:58px; height:58px; border-radius:16px; background:rgba(76,175,120,0.14); color:#2E7D55; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
            </div>
            <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:17px; color:#013C58;">نشر توبك "<span x-text="publishTarget?.name"></span>"؟</h3>
            <p style="margin:10px 0 0; font-size:13px; color:rgba(1,60,88,0.6); line-height:1.7;">.</p>
            <div style="display:flex; gap:10px; margin-top:22px;">
                <button type="button" @click="publishModalOpen = false" style="flex:1; padding:11px; border-radius:11px; border:1.5px solid rgba(0,83,122,0.12); background:#EFFAFD; color:#013C58; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer;">إلغاء</button>
                <button type="button" @click="document.getElementById('publish-topic-form-' + publishTarget.id).submit()" style="flex:1; padding:11px; border-radius:11px; border:none; background:linear-gradient(90deg,#2E7D55,#4CAF78); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">تأكيد النشر</button>
            </div>
        </div>
      </div>
    </div>

    {{-- ============ DELETE CONFIRM MODAL ============ --}}
    <div x-show="deleteModalOpen" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="position:fixed; inset:0; z-index:50; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px); overflow-y:auto;"
         @click="deleteModalOpen = false">
      <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
        <div @click.stop
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             style="width:100%; max-width:400px; background:#EFFAFD; border-radius:22px; padding:30px 26px; box-shadow:0 44px 100px rgba(1,42,63,0.4); text-align:center;">
            <div style="width:58px; height:58px; border-radius:16px; background:rgba(200,60,60,0.14); color:#B23A3A; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><path d="m19 6-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path></svg>
            </div>
            <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:17px; color:#013C58;">حذف توبك "<span x-text="deleteTarget?.name"></span>" نهائيًا؟</h3>
            <p style="margin:10px 0 0; font-size:13px; color:rgba(1,60,88,0.6); line-height:1.7;"></p>
            <div style="display:flex; gap:10px; margin-top:22px;">
                <button type="button" @click="deleteModalOpen = false" style="flex:1; padding:11px; border-radius:11px; border:1.5px solid rgba(0,83,122,0.12); background:#EFFAFD; color:#013C58; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer;">إلغاء</button>
                <button type="button" @click="document.getElementById('delete-topic-form-' + deleteTarget.id).submit()" style="flex:1; padding:11px; border-radius:11px; border:none; background:linear-gradient(90deg,#C1392B,#E05C4E); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">تأكيد الحذف</button>
            </div>
        </div>
      </div>
    </div>
</div>
@endsection
