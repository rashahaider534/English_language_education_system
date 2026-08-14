@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes pdFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .pd-card { animation: pdFadeUp 0.45s ease both; transition: transform 0.25s cubic-bezier(0.16,1,0.3,1), box-shadow 0.25s ease; }
    .pd-card:hover { transform: translateY(-5px); box-shadow: 0 28px 56px rgba(0,83,122,0.16); }
    .pd-action-btn { transition: transform 0.15s ease; text-decoration:none; }
    .pd-action-btn:not(:disabled):hover { transform: translateY(-1px); }
</style>
@endpush

@section('content')
@php
    use App\Enums\TopicStatus;
    $isPublished = $topic->status === TopicStatus::PUBLISHED;
@endphp
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    <div style="margin-bottom:18px;">
        <a href="{{ route('topics.index') }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
           العودة للتوبك
        </a>
    </div>

    @if (session('success'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(168,232,249,0.18); color:#00537A; border:1px solid rgba(0,83,122,0.14); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('podcast'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(255,138,101,0.14); color:#C2591A; border:1px solid rgba(255,138,101,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
            {{ $errors->first('podcast') }}
        </div>
    @endif

    {{-- ============ HERO ============ --}}
    <div style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:28px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:380px; height:380px; right:-120px; top:-150px; border-radius:50%; background:radial-gradient(circle, rgba(168,232,249,0.22) 0%, rgba(168,232,249,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:16px;">
            <div>
                <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.85);">بودكاست التوبك</p>
                <h1 style="margin:8px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:24px; color:#fff;">{{ $topic->name_ar }} <span style="color:rgba(168,232,249,0.7); font-size:16px;">({{ $topic->name_en }})</span></h1>
                <span style="display:inline-flex; align-items:center; gap:6px; margin-top:10px; padding:5px 12px; border-radius:999px; background:{{ $isPublished ? 'rgba(168,232,249,0.25)' : 'rgba(255,211,91,0.22)' }}; color:#fff; font-size:11.5px; font-weight:700;">
                    <span style="width:6px; height:6px; border-radius:50%; background:{{ $isPublished ? '#A8E8F9' : '#FFD35B' }};"></span>
                    {{ $isPublished ? 'التوبك منشور' : 'التوبك قيد الانتظار' }}
                </span>
            </div>
            <a href="{{ route('podcasts.create', ['topic_id' => $topic->id]) }}"
               style="display:flex; align-items:center; gap:8px; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; border:none; border-radius:13px; padding:13px 22px; font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; cursor:pointer; box-shadow:0 12px 26px rgba(0,0,0,0.18); text-decoration:none;">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"></path></svg>
                إضافة بودكاست
            </a>
        </div>

    </div>

    @if ($isPublished)
        <div style="display:flex; align-items:center; gap:9px; margin-bottom:20px; background:rgba(255,186,66,0.14); border:1px solid rgba(255,186,66,0.3); border-radius:12px; padding:12px 16px; font-size:12.5px; color:#8A5A00; font-weight:600; line-height:1.6;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
            هذا التوبك منشور، لذلك يمكن تعديل الاسم والنقاط المطلوبة لبودكاساته فقط، ولا يمكن حذف أي بودكاست ضمنه.
        </div>
    @endif

    {{-- ============ PODCASTS GRID ============ --}}
    @if ($podcasts->isEmpty())
        <div style="background:#EFFAFD; border:1.5px dashed rgba(0,83,122,0.2); border-radius:22px; padding:60px 20px; text-align:center;">
            <div style="width:58px; height:58px; border-radius:16px; background:rgba(0,83,122,0.06); color:rgba(1,60,88,0.35); display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3Z"></path><path d="M6 11v1a6 6 0 0 0 12 0v-1"></path></svg>
            </div>
            <p style="margin:0; color:rgba(1,60,88,0.5); font-weight:600; font-size:14px;">لايوجد  بودكاست ضمن هذا التوبك </p>
        </div>
    @else
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(270px, 1fr)); gap:18px; margin-bottom:20px;">
            @foreach ($podcasts as $podcast)
                @php
                    $hasVideo = $podcast->getFirstMediaUrl('podcast_video') !== '';
                    $creatorName = $podcast->creator ? (trim(($podcast->creator->first_name ?? '').' '.($podcast->creator->last_name ?? '')) ?: $podcast->creator->email) : '—';
                @endphp
                <div class="pd-card" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; overflow:hidden; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
                    <div style="position:relative; width:100%; height:100px; background:linear-gradient(135deg,#013C58,#0E6A96,#146B93); display:flex; align-items:center; justify-content:center;">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.85)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M8 5v14l11-7Z"></path></svg>
                        <span style="position:absolute; top:10px; left:10px; display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:999px; background:{{ $hasVideo ? 'rgba(76,175,120,0.92)' : 'rgba(255,255,255,0.85)' }}; color:{{ $hasVideo ? '#fff' : '#946200' }}; font-size:10px; font-weight:700;">
                            {{ $hasVideo ? 'يحتوي على فيديو  ' : 'بدون فيديو' }}
                        </span>
                        <span style="position:absolute; top:10px; right:10px; display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:999px; background:rgba(255,211,91,0.95); color:#013C58; font-size:10.5px; font-weight:800;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 2.9 6 6.6.9-4.8 4.6 1.1 6.5-5.8-3-5.8 3 1.1-6.5-4.8-4.6 6.6-.9L12 2Z"></path></svg>
                            {{ $podcast->point_required }}
                        </span>
                    </div>
                    <div style="padding:16px 18px 18px;">
                        <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:14.5px; color:#013C58;">{{ $podcast->name_en }}</div>
                        <div style="font-size:12.5px; color:rgba(1,60,88,0.5); margin-top:3px;">{{ $podcast->name_ar }}</div>
                        <div style="display:flex; align-items:center; gap:4px; margin-top:10px; font-size:10.5px; color:rgba(1,60,88,0.4);">
                            <svg width="10.5" height="10.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                            أنشأه {{ $creatorName }}
                        </div>

                        <div style="display:flex; gap:8px; margin-top:14px; padding-top:14px; border-top:1px solid rgba(0,83,122,0.06);">
                            <a href="{{ route('podcasts.show', $podcast) }}" title="عرض" class="pd-action-btn"
                               style="display:flex; align-items:center; justify-content:center; width:37px; height:37px; border-radius:10px; background:rgba(168,232,249,0.18); color:#00537A; flex-shrink:0;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </a>
                            <a href="{{ route('podcasts.edit', $podcast) }}" title="تعديل" class="pd-action-btn"
                               style="display:flex; align-items:center; justify-content:center; width:37px; height:37px; border-radius:10px; background:rgba(0,83,122,0.07); color:#00537A; flex-shrink:0;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                            </a>

                            <form action="{{ route('podcasts.delete', $podcast) }}" method="POST" style="flex-shrink:0; margin-inline-start:auto;"
                                  @if(!$isPublished) onsubmit="return confirm('حذف بودكاست &quot;{{ addslashes($podcast->name_ar) }}&quot; نهائيًا؟');" @endif>
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="{{ $isPublished ? 'ما فيك تحذفي بودكاست تحت توبك منشور' : 'حذف' }}" class="pd-action-btn"
                                    @if($isPublished) disabled @endif
                                    style="display:flex; align-items:center; justify-content:center; width:37px; height:37px; border-radius:10px; border:none; background:rgba(229,72,77,0.1); color:#C2591A; cursor:{{ $isPublished ? 'not-allowed' : 'pointer' }}; opacity:{{ $isPublished ? 0.4 : 1 }};">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><path d="m19 6-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div>
            {{ $podcasts->links() }}
        </div>
    @endif
</div>
@endsection
