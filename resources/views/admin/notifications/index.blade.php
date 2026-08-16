@extends('dashboard.layouts.app')

@section('content')
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    @if (session('success'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(168,232,249,0.18); color:#00537A; border:1px solid rgba(0,83,122,0.14); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div style="display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-bottom:22px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:14px; background:#00537A; color:#fff;">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5" /><path d="M9.5 17a2.5 2.5 0 0 0 5 0" /></svg>
            </div>
            <div>
                <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:rgba(1,60,88,0.5);">لوحة التحكم</p>
                <h1 style="margin:4px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#013C58;">الإشعارات @if($unreadCount > 0) <span style="font-size:13px; font-weight:700; color:#C2591A;">({{ $unreadCount }} غير مقروء)</span> @endif</h1>
            </div>
        </div>
        @if ($unreadCount > 0)
            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" style="display:inline-flex; align-items:center; gap:8px; padding:11px 20px; border-radius:11px; border:none; background:linear-gradient(90deg,#013C58,#00537A); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">
                    تحديد الكل كمقروء
                </button>
            </form>
        @endif
    </div>

    <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; overflow:hidden; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        @forelse ($notifications as $notification)
            <div style="display:flex; align-items:flex-start; gap:14px; padding:18px 22px; border-bottom:1px solid rgba(0,83,122,0.06); {{ $notification->read ? '' : 'background:rgba(255,211,91,0.06);' }}">
                <span style="margin-top:5px; display:inline-block; width:9px; height:9px; border-radius:50%; flex-shrink:0; background:{{ $notification->read ? 'rgba(0,83,122,0.2)' : '#F5A201' }};"></span>
                <x-notification-link :notification="$notification" style="flex:1; min-width:0; text-decoration:none; display:block;">
                    <p style="margin:0; font-size:14px; font-weight:{{ $notification->read ? '600' : '700' }}; color:{{ $notification->read ? 'rgba(1,60,88,0.6)' : '#013C58' }};">{{ $notification->title }}</p>
                    <p style="margin:5px 0 0; font-size:13px; color:rgba(1,60,88,0.55); line-height:1.6;">{{ $notification->body }}</p>
                    <p style="margin:7px 0 0; font-size:11px; color:rgba(0,83,122,0.4);">{{ $notification->created_at->diffForHumans() }}</p>
                </x-notification-link>
                <div style="display:flex; align-items:center; gap:8px; flex-shrink:0;">
                    @unless ($notification->read)
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" title="تحديد كمقروء" style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; border:none; background:rgba(76,175,120,0.16); color:#2E7D55; cursor:pointer;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                            </button>
                        </form>
                    @endunless
                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="حذف" style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; border:none; background:rgba(255,138,101,0.14); color:#C2591A; cursor:pointer;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div style="padding:60px 20px; text-align:center;">
                <p style="margin:0; font-size:14px; color:rgba(1,60,88,0.45); font-weight:600;">لايوجد اشعارات للان </p>
            </div>
        @endforelse
    </div>
</div>
@endsection
