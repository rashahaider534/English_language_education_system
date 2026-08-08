@extends('dashboard.layouts.app')

@push('styles')
<style>
    .pd-field-wrap { border:1.5px solid rgba(0,83,122,0.14); border-radius:11px; background:#FBFEFF; }
    .pd-field-wrap input { width:100%; background:transparent; border:none; outline:none; padding:11px 13px; font-size:13px; color:#013C58; font-family:'Tajawal',sans-serif; }
    .pd-field-wrap input:focus, .pd-field-wrap input:focus-visible { outline:none !important; box-shadow:none !important; border:none !important; }
    .pd-field-wrap:focus-within { border-color: rgba(14,106,150,0.4) !important; }
    .pd-field-wrap.is-locked { border-color: rgba(0,83,122,0.06); background: rgba(0,83,122,0.04); }

    .pd-field-wrap input[type="file"] { padding:7px; font-size:12.5px; color:rgba(1,60,88,0.55); }
    .pd-field-wrap input[type="file"]::file-selector-button {
        margin-inline-end:10px; padding:8px 16px; border:none; border-radius:8px;
        background:linear-gradient(90deg,#0E6A96,#146B93); color:#fff; font-family:'Tajawal',sans-serif;
        font-weight:700; font-size:12.5px; cursor:pointer; transition:transform 0.15s ease, box-shadow 0.15s ease;
    }
    .pd-field-wrap input[type="file"]::file-selector-button:hover {
        transform:translateY(-1px); box-shadow:0 6px 14px rgba(14,106,150,0.28);
    }
    .pd-field-wrap input[type="file"]:disabled::file-selector-button { opacity:0.5; cursor:not-allowed; }

    .pd-submit-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .pd-submit-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(245,162,1,0.28); }
</style>
@endpush

@section('content')
@php
    use App\Enums\TopicStatus;
    $topicPublished = $podcast->topic->status === TopicStatus::PUBLISHED;
    $videoUrl = $podcast->getFirstMediaUrl('podcast_video');
@endphp
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    <div style="margin-bottom:18px;">
        <a href="{{ route('podcasts.index', $podcast->topic) }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            العودة لبودكاست {{ $podcast->topic->name_ar }}
        </a>
    </div>

    <div style="background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:22px; padding:26px 32px; margin-bottom:22px; box-shadow:0 20px 44px rgba(1,60,88,0.2);">
        <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:22px; color:#fff;">تعديل البودكاست</h1>
        <p style="margin:6px 0 0; font-size:13px; color:rgba(168,232,249,0.8);">{{ $podcast->name_ar }} ({{ $podcast->name_en }})</p>
    </div>

    @if ($errors->any())
        <div style="background:rgba(255,138,101,0.14); color:#C2591A; border:1px solid rgba(255,138,101,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13px; font-weight:600;">
            @foreach ($errors->all() as $error)
                <p style="margin:0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if ($topicPublished)
        <div style="display:flex; align-items:center; gap:9px; background:rgba(255,211,91,0.14); color:#946200; border-radius:12px; padding:12px 16px; font-size:13px; font-weight:600; margin-bottom:18px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
            <span>توبك هذا البودكاست منشور، لذلك يمكن تعديل الاسم والنقاط المطلوبة فقط، ولا يمكن تغيير الفيديو.</span>
        </div>
    @endif

    <form action="{{ route('podcasts.update', $podcast) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:26px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
            <h3 style="margin:0 0 18px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">معلومات البودكاست</h3>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">الاسم بالإنكليزي</label>
                    <div class="pd-field-wrap"><input type="text" name="name_en" value="{{ old('name_en', $podcast->name_en) }}" required></div>
                </div>
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">الاسم بالعربي</label>
                    <div class="pd-field-wrap"><input type="text" name="name_ar" value="{{ old('name_ar', $podcast->name_ar) }}" required></div>
                </div>
            </div>

            <div style="margin-bottom:16px; max-width:260px;">
                <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">النقاط المطلوبة لفتح البودكاست</label>
                <div class="pd-field-wrap"><input type="number" name="point_required" min="1" value="{{ old('point_required', $podcast->point_required) }}" required></div>
            </div>

            <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">فيديو البودكاست</label>
            @if ($videoUrl)
                <video src="{{ $videoUrl }}" controls style="width:100%; max-width:360px; border-radius:12px; margin-bottom:10px; background:#000;"></video>
            @endif
            <div class="pd-field-wrap {{ $topicPublished ? 'is-locked' : '' }}">
                <input type="file" name="video" accept="video/mp4,video/quicktime,video/x-msvideo,video/x-matroska" @if($topicPublished) disabled @endif>
            </div>
            @if ($topicPublished)
                <p style="margin:8px 0 0; font-size:11px; color:#946200;">حقل الفيديو معطّل لأن التوبك منشور.</p>
            @else
                <p style="margin:8px 0 0; font-size:11px; color:rgba(1,60,88,0.45);">اختاري فيديو جديد لاستبدال الحالي، أو اتركيه فاضي.</p>
            @endif
        </div>

        <div style="margin-top:22px;">
            <button type="submit" class="pd-submit-btn" style="display:inline-flex; align-items:center; gap:8px; padding:13px 30px; border-radius:12px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; cursor:pointer;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                حفظ التعديلات
            </button>
        </div>
    </form>
</div>
@endsection
