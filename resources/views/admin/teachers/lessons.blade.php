@extends('dashboard.layouts.app')

@section('content')
@php
    $statusLabels = [
        'draft' => 'مسودة',
        'pending' => 'قيد الانتظار',
        'in_review' => 'قيد المراجعة',
        'changes_requested' => 'مطلوب تعديل',
        'approved' => 'معتمد',
        'published' => 'منشور',
        'archived' => 'مؤرشف',
        'closed' => 'مغلق',
    ];
    $statusColors = [
        'draft' => ['bg' => 'rgba(1,60,88,0.08)', 'fg' => 'rgba(1,60,88,0.6)'],
        'pending' => ['bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00'],
        'in_review' => ['bg' => 'rgba(14,106,150,0.14)', 'fg' => '#0E6A96'],
        'changes_requested' => ['bg' => 'rgba(255,138,101,0.18)', 'fg' => '#C2591A'],
        'approved' => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55'],
        'published' => ['bg' => 'rgba(76,175,120,0.24)', 'fg' => '#1E6B45'],
        'archived' => ['bg' => 'rgba(1,60,88,0.08)', 'fg' => 'rgba(1,60,88,0.55)'],
        'closed' => ['bg' => 'rgba(1,60,88,0.1)', 'fg' => 'rgba(1,60,88,0.6)'],
    ];
    $teacherName = trim($teacher->first_name.' '.$teacher->last_name) ?: $teacher->email;
@endphp
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    <div style="display:flex; align-items:center; gap:14px; margin-bottom:22px; flex-wrap:wrap;">
        <a href="{{ route('admin.teachers.index') }}" style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); color:#00537A; text-decoration:none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"></path></svg>
        </a>
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:14px; background:#00537A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:14px;">{{ strtoupper(substr($teacher->first_name ?? $teacher->email, 0, 1).substr($teacher->last_name ?? '', 0, 1)) }}</div>
            <div>
                <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:rgba(1,60,88,0.5);">دروس الأستاذ</p>
                <h1 style="margin:4px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#013C58;">{{ $teacherName }}</h1>
            </div>
        </div>
    </div>

    <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; overflow:hidden; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
            <thead>
                <tr>
                    <th style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 16px; background:rgba(168,232,249,0.22); width:38%;">عنوان الدرس</th>
                    <th style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:28%;">الكورس</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:18%;">الحالة</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:16%;">تاريخ الإنشاء</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lessons as $lesson)
                    @php
                        $lessonStatusVal = $lesson->status instanceof \BackedEnum ? $lesson->status->value : $lesson->status;
                        $sc = $statusColors[$lessonStatusVal] ?? $statusColors['draft'];
                    @endphp
                    <tr>
                        <td style="padding:14px 16px; border-bottom:1px solid rgba(0,83,122,0.05); font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; color:#00537A; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $lesson->title_ar ?? $lesson->title_en }}</td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); font-size:12.5px; color:rgba(1,60,88,0.65); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $lesson->course->name_ar ?? $lesson->course->name_en ?? '—' }}</td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                            <span style="display:inline-flex; padding:5px 11px; border-radius:999px; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }}; font-size:11px; font-weight:700;">{{ $statusLabels[$lessonStatusVal] ?? $lessonStatusVal }}</span>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center; font-size:12.5px; color:rgba(1,60,88,0.65);">{{ $lesson->created_at?->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding:60px 20px; text-align:center; color:rgba(1,60,88,0.45); font-weight:600; font-size:14px;">لايوجد دروس مرتبطة بهذا الاستاذ </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($lessons instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div style="padding:16px 22px; border-top:1px solid rgba(0,83,122,0.06);">
                {{ $lessons->links('vendor.pagination.lessons') }}
            </div>
        @endif
    </div>
</div>
@endsection
