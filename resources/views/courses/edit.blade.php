@extends('dashboard.layouts.app')

@section('content')
@php
    $statusLabels = ['pending' => 'قيد الانتظار', 'published' => 'منشور', 'closed' => 'مغلق', 'archived' => 'مؤرشف'];
    $statusColors = [
        'pending'   => ['bg' => 'rgba(255,211,91,0.16)', 'fg' => '#946200', 'dot' => '#F5A201'],
        'published' => ['bg' => 'rgba(168,232,249,0.22)', 'fg' => '#00537A', 'dot' => '#00537A'],
        'closed'    => ['bg' => 'rgba(1,60,88,0.07)', 'fg' => 'rgba(1,60,88,0.6)', 'dot' => 'rgba(1,60,88,0.5)'],
        'archived'  => ['bg' => 'rgba(1,60,88,0.05)', 'fg' => 'rgba(1,60,88,0.42)', 'dot' => 'rgba(1,60,88,0.35)'],
    ];
    $sc = $statusColors[$course->status] ?? $statusColors['pending'];

    $isPublished = $course->status === 'published';
    $isLocked = in_array($course->status, ['closed', 'archived']);
    $isCoreLocked = $isPublished || $isLocked; // order/teacher

    $label = 'display:block; font-size:12px; font-weight:600; color:rgba(1,60,88,0.6); margin-bottom:7px;';
    $wrapBase = 'border:1.5px solid rgba(0,83,122,0.1); border-radius:11px; padding:0 4px; background:#F7FBFD;';
    $wrapLocked = 'border:1.5px solid rgba(0,83,122,0.06); border-radius:11px; padding:0 4px; background:rgba(0,83,122,0.04);';
    $input = "width:100%; background:transparent; border:none; outline:none; padding:11px 11px; font-size:13.5px; color:#013C58; font-family:'Tajawal',sans-serif;";
    $section = 'margin:0 0 12px; font-size:11.5px; font-weight:700; letter-spacing:0.5px; text-transform:uppercase; color:rgba(1,60,88,0.4);';
@endphp

<div style="font-family:'Tajawal',sans-serif; max-width:680px; margin:0 auto;" dir="rtl">

    <div style="display:flex; align-items:center; gap:10px; margin-bottom:22px;">
        <a href="{{ route('courses.index', $level) }}" style="display:flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:11px; background:rgba(0,83,122,0.07); color:#00537A; text-decoration:none;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"></path><path d="M11 18l-6-6 6-6"></path></svg>
        </a>
        <div>
            <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:22px; color:#013C58;">تعديل الكورس</h1>
            <p style="margin:4px 0 0; font-size:13px; color:rgba(1,60,88,0.55);">{{ $level->name_ar }} ({{ $level->name_en }})</p>
        </div>
    </div>

    @if ($errors->any())
        <div style="display:flex; align-items:flex-start; gap:9px; margin-bottom:18px; padding:13px 16px; border-radius:12px; background:rgba(148,98,0,0.08); color:#946200; font-size:13px; font-weight:600;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
            <ul style="margin:0; padding-inline-start:16px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('courses.update', $course) }}" enctype="multipart/form-data"
          style="background:#fff; border:1px solid rgba(0,83,122,0.08); border-radius:22px; padding:28px 26px; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        @csrf
        @method('PUT')

        <div style="display:flex; align-items:center; justify-content:space-between; background:rgba(0,83,122,0.04); border-radius:12px; padding:12px 16px; margin-bottom:18px;">
            <span style="font-size:12.5px; font-weight:600; color:rgba(1,60,88,0.6);">حالة الكورس الحالية</span>
            <span style="display:inline-flex; align-items:center; gap:6px; padding:5px 12px; border-radius:999px; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }}; font-size:11.5px; font-weight:700;">
                <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:{{ $sc['dot'] }};"></span>
                {{ $statusLabels[$course->status] ?? $course->status }}
            </span>
        </div>

        @if ($isLocked)
            <div style="display:flex; align-items:center; gap:9px; background:rgba(1,60,88,0.06); color:#00537A; border-radius:12px; padding:12px 16px; font-size:13px; font-weight:600; margin-bottom:18px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="10" rx="2.5"></rect><path d="M7.5 10.5V7a4.5 4.5 0 0 1 9 0v3.5"></path></svg>
                <span>هذا الكورس  مغلق أو مؤرشف، لايمكن التعديل  عليه.</span>
            </div>
        @elseif ($isPublished)
            <div style="display:flex; align-items:center; gap:9px; background:rgba(255,211,91,0.14); color:#946200; border-radius:12px; padding:12px 16px; font-size:13px; font-weight:600; margin-bottom:18px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
                <span>  هذا الكورس منشور يمكن تعديل فقط الاسم,المدة,الصورة</span>
            </div>
        @endif

        <p style="{{ $section }}">الاسم</p>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div>
                <label style="{{ $label }}">بالإنكليزي</label>
                <div style="{{ $wrapBase }}">
                    <input name="name_en" value="{{ old('name_en', $course->name_en) }}" style="{{ $input }}" @if($isLocked) disabled @endif>
                </div>
            </div>
            <div>
                <label style="{{ $label }}">بالعربي</label>
                <div style="{{ $wrapBase }}">
                    <input name="name_ar" value="{{ old('name_ar', $course->name_ar) }}" style="{{ $input }}" @if($isLocked) disabled @endif>
                </div>
            </div>
        </div>

        <p style="{{ $section }} margin-top:22px;">الترتيب والمدة</p>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div>
                <label style="{{ $label }}">الترتيب</label>
                <div style="{{ $isCoreLocked ? $wrapLocked : $wrapBase }}">
                    <input type="number" name="order" value="{{ old('order', $course->order) }}" style="{{ $input }}" @if($isCoreLocked) disabled @endif>
                </div>
            </div>
            <div>
                <label style="{{ $label }}">المدة المتوقعة (أسابيع)</label>
                <div style="{{ $isLocked ? $wrapLocked : $wrapBase }}">
                    <input type="number" name="estimated_duration" value="{{ old('estimated_duration', $course->estimated_duration) }}" style="{{ $input }}" @if($isLocked) disabled @endif>
                </div>
            </div>
        </div>

        <p style="{{ $section }} margin-top:22px;">الأستاذ</p>
        <div style="{{ $isCoreLocked ? $wrapLocked : $wrapBase }}">
            <select name="teacher_id" style="{{ $input }}" @if($isCoreLocked) disabled @endif>
                <option value="">قم باختيار الأستاذ...</option>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}" @selected(old('teacher_id', $course->teacher_id) == $teacher->id)>{{ $teacher->email }}</option>
                @endforeach
            </select>
        </div>

        <p style="{{ $section }} margin-top:22px;">صورة الكورس</p>
        <div style="display:flex; align-items:center; gap:14px; border:1.5px dashed rgba(0,83,122,0.18); border-radius:14px; padding:14px;">
            @if ($course->getFirstMediaUrl('course_image'))
                <img src="{{ $course->getFirstMediaUrl('course_image') }}" style="width:64px; height:64px; border-radius:12px; object-fit:cover; flex-shrink:0;">
            @else
                <div style="width:64px; height:64px; border-radius:12px; background:rgba(0,83,122,0.05); display:flex; align-items:center; justify-content:center; color:rgba(1,60,88,0.3); flex-shrink:0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="3"></rect><circle cx="9" cy="10" r="2"></circle><path d="m21 16-5-5-4 4-3-3-4 4"></path></svg>
                </div>
            @endif
            <div style="flex:1;">
                <input type="file" name="image" accept="image/*" style="font-size:12.5px; color:rgba(1,60,88,0.6); width:100%;" @if($isLocked) disabled @endif>
                <p style="margin:6px 0 0; font-size:11px; color:rgba(1,60,88,0.4);">JPG أو PNG أو WEBP، حتى 2 ميغابايت — قابلة للتعديل حتى لو الكورس منشور</p>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:26px; padding-top:20px; border-top:1px solid rgba(0,83,122,0.06);">
            <a href="{{ route('courses.index', $level) }}" style="padding:11px 20px; border-radius:11px; border:1.5px solid rgba(0,83,122,0.12); background:#fff; color:#013C58; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; text-decoration:none;">إلغاء</a>
            @unless ($isLocked)
                <button type="submit" style="display:flex; align-items:center; gap:7px; padding:11px 22px; border-radius:11px; border:none; background:#013C58; color:#fff; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer; box-shadow:0 10px 22px rgba(1,60,88,0.2);">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                    حفظ التعديلات
                </button>
            @endunless
        </div>
    </form>
</div>
@endsection
