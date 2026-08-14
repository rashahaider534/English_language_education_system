@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .pc-panel { animation: lessonsFadeUp 0.4s ease both; }
    .pc-panel:nth-child(1) { animation-delay: 0.02s; }
    .pc-panel:nth-child(2) { animation-delay: 0.06s; }

    .pc-action-btn { transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease; }
    .pc-action-btn:not(:disabled):hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(1,60,88,0.16); }

    .pc-meta-row { transition: background 0.15s ease; }
    .pc-meta-row:hover { background: rgba(0,83,122,0.05); }
</style>
@endpush

@section('content')
@php
    use App\Enums\TopicStatus;
    $isPublished = $podcast->topic->status === TopicStatus::PUBLISHED;
    $videoUrl = $podcast->getFirstMediaUrl('podcast_video');
    $creatorName = $podcast->creator ? (trim(($podcast->creator->first_name ?? '').' '.($podcast->creator->last_name ?? '')) ?: $podcast->creator->email) : '—';
    $accent = '#0E6A96';
    $accentBg = 'rgba(14,106,150,0.14)';
    $statusHero = $isPublished
        ? ['bg' => 'rgba(126,224,178,0.2)', 'fg' => '#7EE0B2']
        : ['bg' => 'rgba(255,211,91,0.18)', 'fg' => '#FFD35B'];
@endphp
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    @if (session('success'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(168,232,249,0.18); color:#00537A; border:1px solid rgba(0,83,122,0.14); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-bottom:18px;">
        <a href="{{ route('podcasts.index', $podcast->topic) }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            العودة لبودكاست {{ $podcast->topic->name_ar }}
        </a>
    </div>

    {{-- ============ HERO ============ --}}
    <div style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:28px 32px 26px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.2) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:flex-start; justify-content:space-between; gap:20px; flex-wrap:wrap;">
            <div style="display:flex; align-items:flex-start; gap:16px; min-width:0;">
                <div style="flex-shrink:0; width:52px; height:52px; border-radius:15px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); display:flex; align-items:center; justify-content:center; color:#A8E8F9;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 5v14l11-7Z"></path></svg>
                </div>
                <div style="min-width:0;">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px; flex-wrap:wrap;">
                        <span style="display:inline-flex; align-items:center; gap:6px; padding:5px 12px; border-radius:999px; background:{{ $statusHero['bg'] }}; color:{{ $statusHero['fg'] }}; font-size:11px; font-weight:700; border:1px solid {{ $statusHero['bg'] }};">
                            <span style="width:6px; height:6px; border-radius:50%; background:{{ $statusHero['fg'] }};"></span>
                            توبك {{ $isPublished ? 'منشور' : 'قيد الانتظار' }}
                        </span>
                        <span style="display:inline-flex; padding:5px 12px; border-radius:999px; background:rgba(255,255,255,0.1); color:#A8E8F9; font-size:11px; font-weight:700; border:1px solid rgba(255,255,255,0.18);">{{ $podcast->topic->name_ar }}</span>
                    </div>
                    <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:21px; color:#fff; line-height:1.5;">{{ $podcast->name_en }}</h1>
                    <p style="margin:6px 0 0; font-size:14px; color:rgba(168,232,249,0.85);">{{ $podcast->name_ar }}</p>
                </div>
            </div>
            <div style="flex-shrink:0; display:flex; align-items:center; gap:14px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.16); border-radius:16px; padding:14px 24px;">
                <div style="text-align:center;">
                    <p style="margin:0; font-size:10px; font-weight:700; letter-spacing:0.6px; text-transform:uppercase; color:rgba(168,232,249,0.7);">النقاط المطلوبة</p>
                    <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:22px; color:#fff;">{{ $podcast->point_required }}</p>
                </div>
            </div>
        </div>

        <div style="position:relative; display:flex; gap:10px; margin-top:22px;">
            <a href="{{ route('podcasts.edit', $podcast) }}" class="pc-action-btn" style="display:inline-flex; align-items:center; gap:8px; padding:11px 20px; border-radius:12px; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; text-decoration:none; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                تعديل البودكاست
            </a>
            <form action="{{ route('podcasts.delete', $podcast) }}" method="POST"
                  @if(!$isPublished) onsubmit="return confirm('حذف بودكاست &quot;{{ addslashes($podcast->name_ar) }}&quot; نهائيًا؟');" @endif>
                @csrf
                @method('DELETE')
                <button type="submit" @disabled($isPublished) title="{{ $isPublished ? 'لايمكن  حذف بودكاست تحت توبك منشور' : 'حذف' }}" class="pc-action-btn" style="display:inline-flex; align-items:center; gap:8px; padding:11px 20px; border-radius:12px; border:1px solid rgba(255,255,255,0.2); background:rgba(229,72,77,0.18); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:{{ $isPublished ? 'not-allowed' : 'pointer' }}; opacity:{{ $isPublished ? 0.45 : 1 }};">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                    حذف
                </button>
            </form>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 320px; gap:24px; align-items:start;">
        {{-- ============ MAIN: VIDEO ============ --}}
        <div class="pc-panel" style="background:#013C58; border-radius:20px; overflow:hidden; box-shadow:0 10px 26px rgba(0,83,122,0.06); border-inline-start:4px solid {{ $accent }};">
            @if ($videoUrl)
                <video src="{{ $videoUrl }}" controls style="width:100%; max-height:460px; display:block; background:#000;"></video>
            @else
                <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; padding:80px 20px; color:rgba(255,255,255,0.5);">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M8 5v14l11-7Z"></path></svg>
                    <p style="margin:0; font-size:13px; font-weight:600;">لايوجد  فيديو مرفوع لهذا البودكاست</p>
                </div>
            @endif
        </div>

        {{-- ============ SIDE: META ============ --}}
        <div style="display:flex; flex-direction:column; gap:20px;">
            <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:18px; padding:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
                <h3 style="margin:0 0 14px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">معلومات البودكاست</h3>
                <div style="display:flex; flex-direction:column; gap:4px;">
                    <div class="pc-meta-row" style="display:flex; align-items:center; justify-content:space-between; gap:10px; padding:9px 6px; border-radius:9px;">
                        <span style="display:flex; align-items:center; gap:7px; font-size:12.5px; color:rgba(1,60,88,0.55); font-weight:600;">
                            <span style="width:24px; height:24px; border-radius:7px; background:{{ $accentBg }}; color:{{ $accent }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3Z"></path><path d="M6 11v1a6 6 0 0 0 12 0v-1"></path></svg>
                            </span>
                            التوبك
                        </span>
                        <a href="{{ route('podcasts.index', $podcast->topic) }}" style="font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; color:#00537A; text-decoration:none;">{{ $podcast->topic->name_ar }}</a>
                    </div>
                    <div class="pc-meta-row" style="display:flex; align-items:center; justify-content:space-between; gap:10px; padding:9px 6px; border-radius:9px;">
                        <span style="display:flex; align-items:center; gap:7px; font-size:12.5px; color:rgba(1,60,88,0.55); font-weight:600;">
                            <span style="width:24px; height:24px; border-radius:7px; background:{{ $accentBg }}; color:{{ $accent }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21a8 8 0 1 0-16 0"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            </span>
                            أنشأه
                        </span>
                        <span style="font-size:13px; color:#013C58; font-weight:700;">{{ $creatorName }}</span>
                    </div>
                    <div class="pc-meta-row" style="display:flex; align-items:center; justify-content:space-between; gap:10px; padding:9px 6px; border-radius:9px;">
                        <span style="display:flex; align-items:center; gap:7px; font-size:12.5px; color:rgba(1,60,88,0.55); font-weight:600;">
                            <span style="width:24px; height:24px; border-radius:7px; background:{{ $accentBg }}; color:{{ $accent }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M3 10h18"></path><path d="M8 2v4M16 2v4"></path></svg>
                            </span>
                            تاريخ الإنشاء
                        </span>
                        <span style="font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; color:#013C58;" dir="ltr">{{ $podcast->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
