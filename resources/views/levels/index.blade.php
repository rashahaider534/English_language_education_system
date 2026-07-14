@extends('dashboard.layouts.app')

@section('content')
<div
    x-data="{
        search: '',
        archiveModalOpen: false,
        archiveTargetId: null,
        archiveTargetName: '',
        archiveTargetLocked: false,
        openArchive(id, name, locked) {
            this.archiveTargetId = id;
            this.archiveTargetName = name;
            this.archiveTargetLocked = locked;
            this.archiveModalOpen = true;
        },
        createModalOpen: {{ ($errors->any() && old('form_type') === 'create') ? 'true' : 'false' }},
        editModalOpen: {{ ($errors->any() && old('form_type') === 'edit') ? 'true' : 'false' }},
        editTarget: {
            id: {{ (int) (old('editing_level_id') ?? 0) }},
            name_en: @js(old('name_en', '')),
            name_ar: @js(old('name_ar', '')),
            order: @js(old('order', '')),
            minimum_score: @js(old('minimum_score', '')),
            maximum_score: @js(old('maximum_score', '')),
            price: @js(old('price', '')),
            estimated_duration: @js(old('estimated_duration', '')),
            status: @js(old('editing_level_status', '')),
        },
        get editIsPublished() { return this.editTarget.status === 'published'; },
        get editIsLocked() { return ['closed', 'archived'].includes(this.editTarget.status); },
        get editIsCoreLocked() { return this.editIsPublished || this.editIsLocked; },
        openEdit(level) {
            this.editTarget = level;
            this.editModalOpen = true;
        },
    }"
    style="font-family:'Tajawal',sans-serif;"
    dir="rtl"
>
    {{-- success flash --}}
    @if (session('success'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(168,232,249,0.18); color:#00537A; border:1px solid rgba(0,83,122,0.14); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ============ HEADER BANNER ============ --}}
    <div style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:32px 34px 26px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(168,232,249,0.25) 0%, rgba(168,232,249,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:20px;">
            <div>
                <p style="margin:0; font-size:12px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.85);">إدارة المحتوى التعليمي</p>
                <h1 style="margin:8px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:27px; color:#fff;">المستويات</h1>
                <p style="margin:8px 0 0; font-size:13.5px; color:rgba(168,232,249,0.75); max-width:440px; line-height:1.6;">إدارة مستويات التعلّم، ترتيبها، ونطاقات الدرجات الخاصة فيها</p>
            </div>
            <button type="button" @click="createModalOpen = true"
               style="display:flex; align-items:center; gap:8px; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; border:none; border-radius:13px; padding:13px 22px; font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; cursor:pointer; box-shadow:0 12px 26px rgba(0,0,0,0.18); transition:transform 0.15s, box-shadow 0.15s;"
               onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 16px 32px rgba(0,0,0,0.24)';"
               onmouseout="this.style.transform=''; this.style.boxShadow='0 12px 26px rgba(0,0,0,0.18)';">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"></path></svg>
                إضافة مستوى جديد
            </button>
        </div>

        {{-- stat cards --}}
        <div style="position:relative; display:flex; gap:14px; margin-top:26px; flex-wrap:wrap;">
            @php
                $statCard = 'display:flex; align-items:center; gap:13px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.14); backdrop-filter:blur(6px); border-radius:16px; padding:14px 18px; flex:1; min-width:170px;';
                $iconWrapBase = 'display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.12); flex-shrink:0;';
            @endphp
            <div style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#FFD35B;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M3 10h18"></path><path d="M8 2v4M16 2v4"></path></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(168,232,249,0.75);">إجمالي المستويات</p>
                    <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $statistics->all_count ?? 0 }}</p>
                </div>
            </div>
            <div style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#A8E8F9;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(168,232,249,0.75);">منشورة</p>
                    <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $statistics->published ?? 0 }}</p>
                </div>
            </div>
            <div style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:#FFD35B;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(168,232,249,0.75);">قيد الانتظار</p>
                    <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $statistics->pending ?? 0 }}</p>
                </div>
            </div>
            <div style="{{ $statCard }}">
                <div style="{{ $iconWrapBase }} color:rgba(255,255,255,0.55);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l1.5-3h15L21 7"></path><path d="M4.5 7h15v12a1 1 0 0 1-1 1h-13a1 1 0 0 1-1-1V7Z"></path><path d="M9 12h6"></path></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(168,232,249,0.75);">مغلقة / مؤرشفة</p>
                    <p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ ($statistics->closed ?? 0) + ($statistics->archived ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ TABLE CARD ============ --}}
    <div style="background:#fff; border:1px solid rgba(0,83,122,0.07); border-radius:22px; overflow:hidden; box-shadow:0 18px 44px rgba(0,83,122,0.06);">

        <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; padding:18px 22px; border-bottom:1px solid rgba(0,83,122,0.06);">
            @php
                $tabBase = "border:none; background:transparent; padding:8px 14px; border-radius:9px; font-family:'Poppins',sans-serif; font-size:12px; font-weight:600; cursor:pointer; transition:background 0.15s, color 0.15s; white-space:nowrap; text-decoration:none; display:inline-block;";
                $activeStatus = request('status');
            @endphp
            <div style="display:flex; gap:6px; background:rgba(0,83,122,0.05); border-radius:12px; padding:4px; flex-wrap:wrap;">
                <a href="{{ route('levels.index') }}" style="{{ $tabBase }} {{ !$activeStatus ? 'background:#013C58; color:#fff;' : 'color:rgba(1,60,88,0.55);' }}">الكل ({{ $statistics->all_count ?? 0 }})</a>
                <a href="{{ route('levels.index', ['status' => 'pending']) }}" style="{{ $tabBase }} {{ $activeStatus === 'pending' ? 'background:#013C58; color:#fff;' : 'color:rgba(1,60,88,0.55);' }}">قيد الانتظار ({{ $statistics->pending ?? 0 }})</a>
                <a href="{{ route('levels.index', ['status' => 'published']) }}" style="{{ $tabBase }} {{ $activeStatus === 'published' ? 'background:#013C58; color:#fff;' : 'color:rgba(1,60,88,0.55);' }}">منشورة ({{ $statistics->published ?? 0 }})</a>
                <a href="{{ route('levels.index', ['status' => 'closed']) }}" style="{{ $tabBase }} {{ $activeStatus === 'closed' ? 'background:#013C58; color:#fff;' : 'color:rgba(1,60,88,0.55);' }}">مغلقة ({{ $statistics->closed ?? 0 }})</a>
                <a href="{{ route('levels.index', ['status' => 'archived']) }}" style="{{ $tabBase }} {{ $activeStatus === 'archived' ? 'background:#013C58; color:#fff;' : 'color:rgba(1,60,88,0.55);' }}">مؤرشفة ({{ $statistics->archived ?? 0 }})</a>
            </div>
            <div style="display:flex; align-items:center; gap:9px; background:rgba(0,83,122,0.05); border:1.5px solid transparent; border-radius:11px; padding:0 14px; min-width:220px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:rgba(1,60,88,0.4); flex-shrink:0;"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3"></path></svg>
                <input x-model="search" placeholder="ابحثي باسم المستوى..." style="flex:1; background:transparent; border:none; outline:none; padding:10px 4px; font-size:13.5px; color:#013C58; font-family:'Tajawal',sans-serif;">
            </div>
        </div>

        <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
            <thead>
                <tr>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; letter-spacing:0.5px; padding:13px 16px; background:rgba(168,232,249,0.10); width:6%;">#</th>
                    <th style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; letter-spacing:0.5px; padding:13px 16px; background:rgba(168,232,249,0.10); width:25%;">المستوى</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; letter-spacing:0.5px; padding:13px 16px; background:rgba(168,232,249,0.10); width:16%;">السعر</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; letter-spacing:0.5px; padding:13px 16px; background:rgba(168,232,249,0.10); width:18%;">المدة</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; letter-spacing:0.5px; padding:13px 16px; background:rgba(168,232,249,0.10); width:17%;">الحالة</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; letter-spacing:0.5px; padding:13px 16px; background:rgba(168,232,249,0.10); width:18%;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $statusColors = [
                        'pending'   => ['bg' => 'rgba(255,211,91,0.16)', 'fg' => '#946200', 'dot' => '#F5A201'],
                        'published' => ['bg' => 'rgba(168,232,249,0.22)', 'fg' => '#00537A', 'dot' => '#00537A'],
                        'closed'    => ['bg' => 'rgba(1,60,88,0.07)', 'fg' => 'rgba(1,60,88,0.6)', 'dot' => 'rgba(1,60,88,0.5)'],
                        'archived'  => ['bg' => 'rgba(1,60,88,0.05)', 'fg' => 'rgba(1,60,88,0.42)', 'dot' => 'rgba(1,60,88,0.35)'],
                    ];
                    $statusLabels = ['pending' => 'قيد الانتظار', 'published' => 'منشور', 'closed' => 'مغلق', 'archived' => 'مؤرشف'];
                    $avatarPalette = ['#00537A', '#0E6A96', '#146B93', '#1C7BA6', '#F5A201', '#C97F00'];
                @endphp
                @forelse ($levels as $i => $level)
                    @php
                        $sc = $statusColors[$level->status] ?? $statusColors['pending'];
                        $dimmed = in_array($level->status, ['closed', 'archived']);
                        $canArchive = !$dimmed;
                        $hasInProgress = $level->userLevels()->where('status', 'in_progress')->exists();
                        $avatarColor = $avatarPalette[$i % count($avatarPalette)];
                    @endphp
                    <tr
                        x-show="!search || '{{ strtolower($level->name_en) }}'.includes(search.toLowerCase()) || '{{ $level->name_ar }}'.includes(search)"
                        style="transition:background 0.15s;"
                        onmouseover="this.style.background='rgba(168,232,249,0.08)';"
                        onmouseout="this.style.background='';"
                    >
                        <td style="padding:14px 16px; border-bottom:1px solid rgba(0,83,122,0.05); vertical-align:middle; text-align:center;">
                            <div style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:rgba(0,83,122,0.06); color:rgba(1,60,88,0.6); font-family:'Poppins',sans-serif; font-weight:700; font-size:12px;">{{ $level->order }}</div>
                        </td>
                        <td style="padding:14px 16px; border-bottom:1px solid rgba(0,83,122,0.05); vertical-align:middle;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:11px; background:{{ $avatarColor }}; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px; flex-shrink:0; opacity:{{ $dimmed ? 0.45 : 1 }};">{{ strtoupper(substr($level->name_en, 0, 2)) }}</div>
                                <div>
                                    <div style="font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; color:#013C58;">{{ $level->name_en }}</div>
                                    <div style="font-size:12px; color:rgba(1,60,88,0.5); margin-top:2px;">{{ $level->name_ar }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:14px 16px; border-bottom:1px solid rgba(0,83,122,0.05); vertical-align:middle; text-align:center;">
                            <span style="font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#013C58;" dir="ltr">${{ $level->price }}</span>
                        </td>
                        <td style="padding:14px 16px; border-bottom:1px solid rgba(0,83,122,0.05); vertical-align:middle; text-align:center;">
                            <div style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-size:13px; color:rgba(1,60,88,0.6);">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
                                <span dir="ltr">{{ $level->estimated_duration }} أسابيع</span>
                            </div>
                        </td>
                        <td style="padding:14px 16px; border-bottom:1px solid rgba(0,83,122,0.05); vertical-align:middle; text-align:center;">
                            <span style="display:inline-flex; align-items:center; gap:6px; padding:5px 12px; border-radius:999px; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }}; font-size:11.5px; font-weight:700;">
                                <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:{{ $sc['dot'] }};"></span>
                                {{ $statusLabels[$level->status] ?? $level->status }}
                            </span>
                        </td>
                        <td style="padding:14px 16px; border-bottom:1px solid rgba(0,83,122,0.05); vertical-align:middle; text-align:center;">
                            <div style="display:flex; gap:8px; justify-content:center;">
                                <button type="button" title="تعديل"
                                   @click="openEdit({{ Illuminate\Support\Js::from([
                                        'id' => $level->id,
                                        'name_en' => $level->name_en,
                                        'name_ar' => $level->name_ar,
                                        'order' => $level->order,
                                        'minimum_score' => $level->minimum_score,
                                        'maximum_score' => $level->maximum_score,
                                        'price' => $level->price,
                                        'estimated_duration' => $level->estimated_duration,
                                        'status' => $level->status,
                                   ]) }})"
                                   style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; border:none; background:rgba(0,83,122,0.07); color:#00537A; cursor:pointer;">
                                    <svg width="15.5" height="15.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                                </button>
                                <button
                                    type="button"
                                    @click="openArchive({{ $level->id }}, '{{ addslashes($level->name_ar) }}', {{ $hasInProgress ? 'true' : 'false' }})"
                                    @if(!$canArchive) disabled @endif
                                    title="{{ !$canArchive ? 'هالمستوى مغلق أو مؤرشف من قبل' : ($hasInProgress ? 'أرشفة (رح يصير مغلق)' : 'أرشفة') }}"
                                    style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; border:none; background:rgba(245,162,1,0.1); color:#C97F00; cursor:{{ $canArchive ? 'pointer' : 'not-allowed' }}; opacity:{{ $canArchive ? 1 : 0.35 }};"
                                >
                                    <svg width="15.5" height="15.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l1.5-3h15L21 7"></path><path d="M4.5 7h15v12a1 1 0 0 1-1 1h-13a1 1 0 0 1-1-1V7Z"></path><path d="M9 12h6"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:60px 20px; text-align:center; color:rgba(1,60,88,0.45); font-weight:600; font-size:14px;">ما في مستويات بعد</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($levels instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div style="padding:16px 22px; border-top:1px solid rgba(0,83,122,0.06);">
                {{ $levels->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- ============ ARCHIVE CONFIRM MODAL ============ --}}
    <div
        x-show="archiveModalOpen"
        x-cloak
        style="position:fixed; inset:0; z-index:50; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px); overflow-y:auto;"
        @click="archiveModalOpen = false"
    >
      <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
        <div @click.stop style="width:100%; max-width:400px; background:#fff; border-radius:22px; padding:30px 26px; box-shadow:0 44px 100px rgba(1,42,63,0.4); text-align:center;">
            <div style="width:58px; height:58px; border-radius:16px; background:rgba(245,162,1,0.1); color:#C97F00; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l1.5-3h15L21 7"></path><path d="M4.5 7h15v12a1 1 0 0 1-1 1h-13a1 1 0 0 1-1-1V7Z"></path><path d="M9 12h6"></path></svg>
            </div>
            <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:17px; color:#013C58;">أرشفة مستوى "<span x-text="archiveTargetName"></span>"؟</h3>
            <p style="margin:10px 0 0; font-size:13px; color:rgba(1,60,88,0.6); line-height:1.7;">
                <span x-show="archiveTargetLocked">في طلاب عم يدرسو هلق هالمستوى، فبيصير "مغلق" (مش أرشيف كامل) لحتى يخلّصو. ما رح ينقبل طلاب جدد فيه.</span>
                <span x-show="!archiveTargetLocked">هيصير هالمستوى مؤرشف بشكل كامل، ومش رح يظهر للطلاب الجدد. تقدري ترجعيه لاحقاً.</span>
            </p>
            <div style="display:flex; gap:10px; margin-top:22px;">
                <button type="button" @click="archiveModalOpen = false" style="flex:1; padding:11px; border-radius:11px; border:1.5px solid rgba(0,83,122,0.12); background:#fff; color:#013C58; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer;">إلغاء</button>
                <form :action="'/levels/' + archiveTargetId + '/archive'" method="POST" style="flex:1;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" style="width:100%; padding:11px; border-radius:11px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">تأكيد</button>
                </form>
            </div>
        </div>
      </div>
    </div>

    {{-- ============ CREATE LEVEL MODAL ============ --}}
    <div
        x-show="createModalOpen"
        x-cloak
        style="position:fixed; inset:0; z-index:50; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px); overflow-y:auto;"
        @click="createModalOpen = false"
    >
      <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
        <div @click.stop style="width:100%; max-width:640px; background:#fff; border-radius:22px; padding:28px 26px; box-shadow:0 44px 100px rgba(1,42,63,0.4); font-family:'Tajawal',sans-serif;" dir="rtl">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
                <div>
                    <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:19px; color:#013C58;">إضافة مستوى جديد</h3>
                    <p style="margin:4px 0 0; font-size:13px; color:rgba(1,60,88,0.55);">عبّي التفاصيل لإنشاء مستوى تعلّم جديد</p>
                </div>
                <button type="button" @click="createModalOpen = false" style="width:32px; height:32px; border-radius:9px; border:none; background:rgba(0,83,122,0.07); color:#00537A; cursor:pointer; flex-shrink:0;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin:auto;"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
                </button>
            </div>

            @if ($errors->any() && old('form_type') === 'create')
                <div style="display:flex; align-items:flex-start; gap:9px; margin-bottom:18px; padding:13px 16px; border-radius:12px; background:rgba(148,98,0,0.08); color:#946200; font-size:13px; font-weight:600;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
                    <ul style="margin:0; padding-inline-start:16px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('levels.store') }}">
                @csrf
                <input type="hidden" name="form_type" value="create">

                @php
                    $label = 'display:block; font-size:12px; font-weight:600; color:rgba(1,60,88,0.6); margin-bottom:7px;';
                    $wrap = 'border:1.5px solid rgba(0,83,122,0.1); border-radius:11px; padding:0 4px; background:#F7FBFD;';
                    $input = "width:100%; background:transparent; border:none; outline:none; padding:11px 11px; font-size:13.5px; color:#013C58; font-family:'Tajawal',sans-serif;";
                    $section = 'margin:0 0 12px; font-size:11.5px; font-weight:700; letter-spacing:0.5px; text-transform:uppercase; color:rgba(1,60,88,0.4);';
                @endphp

                <p style="{{ $section }}">الاسم</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label style="{{ $label }}">بالإنكليزي</label>
                        <div style="{{ $wrap }}">
                            <input name="name_en" value="{{ old('form_type') === 'create' ? old('name_en') : '' }}" placeholder="e.g. Beginner A1" style="{{ $input }}">
                        </div>
                    </div>
                    <div>
                        <label style="{{ $label }}">بالعربي</label>
                        <div style="{{ $wrap }}">
                            <input name="name_ar" value="{{ old('form_type') === 'create' ? old('name_ar') : '' }}" placeholder="مثال: مبتدئ A1" style="{{ $input }}">
                        </div>
                    </div>
                </div>

                <p style="{{ $section }} margin-top:22px;">الترتيب ونطاق الدرجات</p>
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px;">
                    <div>
                        <label style="{{ $label }}">الترتيب</label>
                        <div style="{{ $wrap }}">
                            <input type="number" name="order" value="{{ old('form_type') === 'create' ? old('order') : '' }}" placeholder="1" style="{{ $input }}">
                        </div>
                    </div>
                    <div>
                        <label style="{{ $label }}">أدنى علامة</label>
                        <div style="{{ $wrap }}">
                            <input type="number" name="minimum_score" value="{{ old('form_type') === 'create' ? old('minimum_score') : '' }}" placeholder="0" style="{{ $input }}">
                        </div>
                    </div>
                    <div>
                        <label style="{{ $label }}">أعلى علامة</label>
                        <div style="{{ $wrap }}">
                            <input type="number" name="maximum_score" value="{{ old('form_type') === 'create' ? old('maximum_score') : '' }}" placeholder="100" style="{{ $input }}">
                        </div>
                    </div>
                </div>

                <p style="{{ $section }} margin-top:22px;">السعر والمدة</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label style="{{ $label }}">السعر (بالدولار)</label>
                        <div style="{{ $wrap }}">
                            <input type="number" name="price" value="{{ old('form_type') === 'create' ? old('price') : '' }}" placeholder="49" style="{{ $input }}">
                        </div>
                    </div>
                    <div>
                        <label style="{{ $label }}">المدة المتوقعة (أسابيع)</label>
                        <div style="{{ $wrap }}">
                            <input type="number" name="estimated_duration" value="{{ old('form_type') === 'create' ? old('estimated_duration') : '' }}" placeholder="8" style="{{ $input }}">
                        </div>
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:26px; padding-top:20px; border-top:1px solid rgba(0,83,122,0.06);">
                    <button type="button" @click="createModalOpen = false" style="padding:11px 20px; border-radius:11px; border:1.5px solid rgba(0,83,122,0.12); background:#fff; color:#013C58; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer;">إلغاء</button>
                    <button type="submit" style="display:flex; align-items:center; gap:7px; padding:11px 22px; border-radius:11px; border:none; background:#013C58; color:#fff; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer; box-shadow:0 10px 22px rgba(1,60,88,0.2);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                        إضافة المستوى
                    </button>
                </div>
            </form>
        </div>
      </div>
    </div>

    {{-- ============ EDIT LEVEL MODAL ============ --}}
    <div
        x-show="editModalOpen"
        x-cloak
        style="position:fixed; inset:0; z-index:50; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px); overflow-y:auto;"
        @click="editModalOpen = false"
    >
      <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
        <div @click.stop style="width:100%; max-width:640px; background:#fff; border-radius:22px; padding:28px 26px; box-shadow:0 44px 100px rgba(1,42,63,0.4); font-family:'Tajawal',sans-serif;" dir="rtl">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
                <div>
                    <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:19px; color:#013C58;">تعديل المستوى</h3>
                    <p style="margin:4px 0 0; font-size:13px; color:rgba(1,60,88,0.55);">حدّثي تفاصيل هالمستوى</p>
                </div>
                <button type="button" @click="editModalOpen = false" style="width:32px; height:32px; border-radius:9px; border:none; background:rgba(0,83,122,0.07); color:#00537A; cursor:pointer; flex-shrink:0;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin:auto;"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
                </button>
            </div>

            @if ($errors->any() && old('form_type') === 'edit')
                <div style="display:flex; align-items:flex-start; gap:9px; margin-bottom:18px; padding:13px 16px; border-radius:12px; background:rgba(148,98,0,0.08); color:#946200; font-size:13px; font-weight:600;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
                    <ul style="margin:0; padding-inline-start:16px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="display:flex; align-items:center; justify-content:space-between; background:rgba(0,83,122,0.04); border-radius:12px; padding:12px 16px; margin-bottom:18px;">
                <span style="font-size:12.5px; font-weight:600; color:rgba(1,60,88,0.6);">حالة المستوى الحالية</span>
                <span x-text="{
                    pending: 'قيد الانتظار', published: 'منشور', closed: 'مغلق', archived: 'مؤرشف'
                }[editTarget.status] ?? editTarget.status" style="display:inline-flex; align-items:center; gap:6px; padding:5px 12px; border-radius:999px; background:rgba(0,83,122,0.08); color:#00537A; font-size:11.5px; font-weight:700;"></span>
            </div>

            <div x-show="editIsLocked" style="display:flex; align-items:center; gap:9px; background:rgba(1,60,88,0.06); color:#00537A; border-radius:12px; padding:12px 16px; font-size:13px; font-weight:600; margin-bottom:18px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="10" rx="2.5"></rect><path d="M7.5 10.5V7a4.5 4.5 0 0 1 9 0v3.5"></path></svg>
                <span>هالمستوى مغلق أو مؤرشف، فما فيك تعدّلي عليه — بس فيك تشوفي تفاصيله.</span>
            </div>
            <div x-show="!editIsLocked && editIsPublished" style="display:flex; align-items:center; gap:9px; background:rgba(255,211,91,0.14); color:#946200; border-radius:12px; padding:12px 16px; font-size:13px; font-weight:600; margin-bottom:18px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
                <span>هالمستوى منشور حالياً، فبس فيك تعدّلي الاسم والمدة الزمنية.</span>
            </div>

            <form method="POST" :action="'/levels/' + editTarget.id">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_type" value="edit">
                <input type="hidden" name="editing_level_id" :value="editTarget.id">
                <input type="hidden" name="editing_level_status" :value="editTarget.status">

                @php
                    $label = 'display:block; font-size:12px; font-weight:600; color:rgba(1,60,88,0.6); margin-bottom:7px;';
                    $wrapBase = 'border:1.5px solid rgba(0,83,122,0.1); border-radius:11px; padding:0 4px; background:#F7FBFD;';
                    $wrapLocked = 'border:1.5px solid rgba(0,83,122,0.06); border-radius:11px; padding:0 4px; background:rgba(0,83,122,0.04);';
                    $input = "width:100%; background:transparent; border:none; outline:none; padding:11px 11px; font-size:13.5px; color:#013C58; font-family:'Tajawal',sans-serif;";
                    $section = 'margin:0 0 12px; font-size:11.5px; font-weight:700; letter-spacing:0.5px; text-transform:uppercase; color:rgba(1,60,88,0.4);';
                @endphp

                <p style="{{ $section }}">الاسم</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label style="{{ $label }}">بالإنكليزي</label>
                        <div :style="editIsLocked ? '{{ $wrapLocked }}' : '{{ $wrapBase }}'">
                            <input name="name_en" x-model="editTarget.name_en" :disabled="editIsLocked" style="{{ $input }}">
                        </div>
                    </div>
                    <div>
                        <label style="{{ $label }}">بالعربي</label>
                        <div :style="editIsLocked ? '{{ $wrapLocked }}' : '{{ $wrapBase }}'">
                            <input name="name_ar" x-model="editTarget.name_ar" :disabled="editIsLocked" style="{{ $input }}">
                        </div>
                    </div>
                </div>

                <p style="{{ $section }} margin-top:22px;">الترتيب ونطاق الدرجات</p>
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px;">
                    <div>
                        <label style="{{ $label }}">الترتيب</label>
                        <div :style="editIsCoreLocked ? '{{ $wrapLocked }}' : '{{ $wrapBase }}'">
                            <input type="number" name="order" x-model="editTarget.order" :disabled="editIsCoreLocked" style="{{ $input }}">
                        </div>
                    </div>
                    <div>
                        <label style="{{ $label }}">أدنى علامة</label>
                        <div :style="editIsCoreLocked ? '{{ $wrapLocked }}' : '{{ $wrapBase }}'">
                            <input type="number" name="minimum_score" x-model="editTarget.minimum_score" :disabled="editIsCoreLocked" style="{{ $input }}">
                        </div>
                    </div>
                    <div>
                        <label style="{{ $label }}">أعلى علامة</label>
                        <div :style="editIsCoreLocked ? '{{ $wrapLocked }}' : '{{ $wrapBase }}'">
                            <input type="number" name="maximum_score" x-model="editTarget.maximum_score" :disabled="editIsCoreLocked" style="{{ $input }}">
                        </div>
                    </div>
                </div>

                <p style="{{ $section }} margin-top:22px;">السعر والمدة</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label style="{{ $label }}">السعر (بالدولار)</label>
                        <div :style="editIsCoreLocked ? '{{ $wrapLocked }}' : '{{ $wrapBase }}'">
                            <input type="number" name="price" x-model="editTarget.price" :disabled="editIsCoreLocked" style="{{ $input }}">
                        </div>
                    </div>
                    <div>
                        <label style="{{ $label }}">المدة المتوقعة (أسابيع)</label>
                        <div :style="editIsLocked ? '{{ $wrapLocked }}' : '{{ $wrapBase }}'">
                            <input type="number" name="estimated_duration" x-model="editTarget.estimated_duration" :disabled="editIsLocked" style="{{ $input }}">
                        </div>
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:26px; padding-top:20px; border-top:1px solid rgba(0,83,122,0.06);">
                    <button type="button" @click="editModalOpen = false" style="padding:11px 20px; border-radius:11px; border:1.5px solid rgba(0,83,122,0.12); background:#fff; color:#013C58; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer;">إلغاء</button>
                    <button type="submit" x-show="!editIsLocked" style="display:flex; align-items:center; gap:7px; padding:11px 22px; border-radius:11px; border:none; background:#013C58; color:#fff; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer; box-shadow:0 10px 22px rgba(1,60,88,0.2);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
      </div>
    </div>
</div>
@endsection
