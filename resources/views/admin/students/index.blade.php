@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes studentsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .std-hero, .std-stat, .std-chart, .std-filters { animation: studentsFadeUp 0.4s ease both; }
    .std-stat:nth-child(1) { animation-delay: 0.02s; }
    .std-stat:nth-child(2) { animation-delay: 0.06s; }

    .std-row { transition: background 0.15s ease; }
    .std-row:hover { background: rgba(168,232,249,0.1); }

    .std-bar { transition: height 0.5s cubic-bezier(0.16,1,0.3,1); }
    .std-ban-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .std-ban-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(194,89,26,0.22); }

    .std-select-panel::-webkit-scrollbar { width: 7px; }
    .std-select-panel::-webkit-scrollbar-track { background: transparent; }
    .std-select-panel::-webkit-scrollbar-thumb { background: rgba(0,83,122,0.18); border-radius: 999px; }
    .std-select-panel::-webkit-scrollbar-thumb:hover { background: rgba(0,83,122,0.32); }
    .std-select-panel { scrollbar-width: thin; scrollbar-color: rgba(0,83,122,0.22) transparent; }
    .std-select-option:hover { background: rgba(168,232,249,0.18); }
</style>
@endpush

@section('content')
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    @if (session('info'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(255,186,66,0.16); color:#8A5A00; border:1px solid rgba(245,162,1,0.25); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4M12 8h.01"></path></svg>
            {{ session('info') }}
        </div>
    @endif

    {{-- ============ HERO ============ --}}
    <div class="std-hero" style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.2) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:center; gap:16px; margin-bottom:22px;">
            <div style="display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 7.5 12 3l10 4.5-10 4.5-10-4.5Z" /><path d="M6 10v5c0 1.5 2.7 3 6 3s6-1.5 6-3v-5" /></svg>
            </div>
            <div>
                <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">إدارة ومتابعة</p>
                <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">الطلاب</h1>
            </div>
        </div>

        <div style="position:relative; display:flex; gap:14px; flex-wrap:wrap;">
            @php
                $statCard = 'display:flex; align-items:center; gap:13px; background:rgba(255,211,91,0.08); border:1px solid rgba(255,211,91,0.22); border-radius:16px; padding:14px 18px; flex:1; min-width:150px;';
                $iconWrapBase = 'display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.14); flex-shrink:0;';
            @endphp
            <div class="std-stat" style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#FFD35B;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M3 10h18"></path></svg></div>
                <div><p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,236,176,0.85);">إجمالي الطلاب</p><p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $totalCount }}</p></div>
            </div>
        </div>
    </div>

    {{-- ============ LEVEL DISTRIBUTION ============ --}}
    <div class="std-chart" style="margin-bottom:22px;">
        <h3 style="margin:0 0 14px; font-family:'Poppins',sans-serif; font-weight:700; font-size:15px; color:#013C58;">توزع الطلاب حسب المستوى</h3>
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(160px, 1fr)); gap:14px;">
            @php
                $avatarPalette = ['#00537A', '#0E6A96', '#146B93', '#1C7BA6', '#F5A201', '#C97F00'];
            @endphp
            @forelse ($levelDistribution as $i => $item)
                @php $avatarColor = $avatarPalette[$i % count($avatarPalette)]; @endphp
                <div class="dash-stat" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:18px; padding:18px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
                    <div style="display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:12px; background:{{ $avatarColor }}; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; margin-bottom:12px;">{{ strtoupper(substr($item['label'], 0, 2)) }}</div>
                    <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:24px; color:#013C58;">{{ $item['count'] }}</p>
                    <p style="margin:4px 0 0; font-size:12.5px; color:rgba(1,60,88,0.55); font-weight:600;">{{ $item['label'] }}</p>
                    <div style="margin-top:10px; height:6px; border-radius:999px; background:rgba(0,83,122,0.08); overflow:hidden;">
                        <div class="std-bar" style="height:100%; width:{{ round(($item['count'] / $levelDistributionMax) * 100) }}%; background:{{ $avatarColor }}; border-radius:999px;"></div>
                    </div>
                </div>
            @empty
                <p style="color:rgba(1,60,88,0.45); font-size:13px;">ما في مستويات معرّفة بعد.</p>
            @endforelse
        </div>
    </div>

    {{-- ============ FILTERS ============ --}}
    <form method="GET" action="{{ route('admin.students.index') }}" class="std-filters" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:22px; background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:16px; padding:14px 18px;">
        <input type="text" name="search" value="{{ $search }}" placeholder="ابحث بالاسم أو الإيميل..." style="flex:1; min-width:180px; padding:9px 14px; border:1.5px solid rgba(0,83,122,0.14); border-radius:10px; background:#FBFEFF; color:#013C58; font-size:13px; font-family:'Tajawal',sans-serif; outline:none;">

        @php
            $selectedLevel = $levels->first(fn ($l) => (string) $levelId === (string) $l->id);
        @endphp
        <div x-data="{ open: false }" @click.outside="open = false" style="position:relative;">
            <button type="button" @click="open = !open"
                style="display:flex; align-items:center; gap:8px; padding:9px 14px; border:1.5px solid rgba(0,83,122,0.14); border-radius:10px; background:#FBFEFF; color:#013C58; font-size:12.5px; font-family:'Tajawal',sans-serif; cursor:pointer; min-width:160px; justify-content:space-between;">
                <span>{{ $selectedLevel ? ($selectedLevel->name_ar ?? $selectedLevel->name_en) : 'كل المستويات' }}</span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.5; flex-shrink:0;"><path d="m6 9 6 6 6-6"></path></svg>
            </button>
            <div x-show="open" x-transition x-cloak
                 class="std-select-panel"
                 style="position:absolute; top:calc(100% + 6px); right:0; min-width:180px; max-height:220px; overflow-y:auto; background:#fff; border:1.5px solid rgba(0,83,122,0.14); border-radius:12px; box-shadow:0 16px 36px rgba(1,60,88,0.16); z-index:20; padding:6px;">
                <button type="submit" name="level" value="" class="std-select-option" style="display:block; width:100%; text-align:right; padding:9px 12px; border:none; background:transparent; border-radius:8px; font-size:12.5px; font-family:'Tajawal',sans-serif; color:#013C58; cursor:pointer;">كل المستويات</button>
                @foreach ($levels as $level)
                    <button type="submit" name="level" value="{{ $level->id }}" class="std-select-option" style="display:block; width:100%; text-align:right; padding:9px 12px; border:none; background:transparent; border-radius:8px; font-size:12.5px; font-family:'Tajawal',sans-serif; color:#013C58; cursor:pointer;">{{ $level->name_ar ?? $level->name_en }}</button>
                @endforeach
            </div>
        </div>

        <button type="submit" style="display:inline-flex; align-items:center; gap:6px; padding:9px 18px; border-radius:10px; border:none; background:#013C58; color:#fff; font-family:'Poppins',sans-serif; font-weight:600; font-size:12.5px; cursor:pointer;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3"></path></svg>
            فلترة
        </button>
        @if ($search || $levelId)
            <a href="{{ route('admin.students.index') }}" style="font-size:12px; color:rgba(1,60,88,0.5); font-weight:600; text-decoration:none;">إلغاء الفلترة</a>
        @endif
    </form>

    {{-- ============ STUDENTS TABLE ============ --}}
    <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; overflow:hidden; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
            <thead>
                <tr>
                    <th style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 16px; background:rgba(168,232,249,0.22); width:24%;">الاسم</th>
                    <th style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:22%;">الإيميل</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:16%;">المستوى الحالي</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:14%;">تاريخ الانضمام</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:12%;">الحالة</th>
                    {{-- <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:12%;">إجراءات</th> --}}
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $student)
                    @php
                        $studentName = trim($student->first_name.' '.$student->last_name) ?: $student->email;
                        $initials = strtoupper(substr($student->first_name ?? $student->email, 0, 1).substr($student->last_name ?? '', 0, 1));
                    @endphp
                    <tr class="std-row">
                        <td style="padding:14px 16px; border-bottom:1px solid rgba(0,83,122,0.05);">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; background:#0E6A96; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px; flex-shrink:0;">{{ $initials }}</div>
                                <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#013C58; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $studentName }}</div>
                            </div>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); font-size:12.5px; color:rgba(1,60,88,0.65); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $student->email }}</td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                            @if ($student->current_level)
                                <span style="display:inline-flex; padding:5px 11px; border-radius:999px; background:rgba(0,83,122,0.08); color:#00537A; font-size:11px; font-weight:700;">{{ $student->current_level->name_ar ?? $student->current_level->name_en }}</span>
                            @else
                                <span style="font-size:11.5px; color:rgba(1,60,88,0.4);">—</span>
                            @endif
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center; font-size:12.5px; color:rgba(1,60,88,0.65);">{{ $student->created_at?->format('Y-m-d') }}</td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                            @if ($student->is_active)
                                <span style="display:inline-flex; padding:5px 11px; border-radius:999px; background:rgba(76,175,120,0.16); color:#2E7D55; font-size:11px; font-weight:700;">نشط</span>
                            @else
                                <span style="display:inline-flex; padding:5px 11px; border-radius:999px; background:rgba(255,138,101,0.18); color:#C2591A; font-size:11px; font-weight:700;">محظور</span>
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:60px 20px; text-align:center; color:rgba(1,60,88,0.45); font-weight:600; font-size:14px;">ما في طلاب بهالفلتر</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($students instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div style="padding:16px 22px; border-top:1px solid rgba(0,83,122,0.06);">
                {{ $students->links('vendor.pagination.lessons') }}
            </div>
        @endif
    </div>
</div>
@endsection
