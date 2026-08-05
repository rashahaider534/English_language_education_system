@extends('dashboard.layouts.app')

@section('content')
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    <div style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:32px 32px 30px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.2) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>
        <div style="position:absolute; width:300px; height:300px; left:-80px; bottom:-140px; border-radius:50%; background:radial-gradient(circle, rgba(168,232,249,0.2) 0%, rgba(168,232,249,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:center; gap:16px;">
            <div style="display:flex; align-items:center; justify-content:center; width:56px; height:56px; border-radius:18px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 2 8l10 5 10-5-10-5Z" /><path d="M2 13l10 5 10-5" /><path d="M2 18l10 5 10-5" /></svg>
            </div>
            <div>
                <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">لوحة تحكم الأدمن</p>
                <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:24px; color:#fff;">مرحبًا بك، {{ $dashboardUser['name'] }}</h1>
                <p style="margin:8px 0 0; font-size:13.5px; color:rgba(255,255,255,0.75); max-width:520px;">من هون بتقدري تتابعي المحتوى التعليمي، الطلبات قيد الانتظار، الأساتذة والطلاب، وصندوق الشكاوي.</p>
            </div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:18px; margin-top:22px;">
        @php
            $quickLinks = [
                ['label' => 'محتوى تعليمي', 'desc' => 'المستويات والكورسات والدروس', 'route' => 'levels.index', 'icon' => 'levels'],
                ['label' => 'الدروس قيد الانتظار', 'desc' => 'دروس بانتظار المراجعة', 'route' => 'lessons.pending', 'icon' => 'pending-lessons'],
                ['label' => 'أساتذة', 'desc' => 'إدارة ومتابعة الأساتذة', 'route' => 'admin.teachers.index', 'icon' => 'teachers'],
                ['label' => 'طلاب', 'desc' => 'إدارة ومتابعة الطلاب', 'route' => 'admin.students.index', 'icon' => 'students'],
                ['label' => 'صندوق الشكاوي', 'desc' => 'رسائل ملاحظات المستخدمين', 'route' => 'admin.complaints.index', 'icon' => 'complaints'],
            ];
        @endphp
        @foreach ($quickLinks as $link)
            <a href="{{ route($link['route']) }}" style="display:flex; align-items:center; gap:14px; background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.14); border-radius:18px; padding:18px; text-decoration:none; transition:transform .15s ease, box-shadow .15s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 14px 28px rgba(0,83,122,0.12)';" onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
                <div style="display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:14px; background:rgba(0,83,122,0.08); color:#00537A; flex-shrink:0;">
                    @include('dashboard.partials.icons.'.$link['icon'])
                </div>
                <div>
                    <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#013C58;">{{ $link['label'] }}</p>
                    <p style="margin:3px 0 0; font-size:12px; color:rgba(1,60,88,0.55);">{{ $link['desc'] }}</p>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
