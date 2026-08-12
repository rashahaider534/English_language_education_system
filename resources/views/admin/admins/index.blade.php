@extends('dashboard.layouts.app')

@push('styles')
    <style>
        @keyframes adminsFadeUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .adm-hero,
        .adm-stat,
        .adm-filters,
        .adm-row {
            animation: adminsFadeUp 0.4s ease both;
        }

        .adm-stat:nth-child(1) {
            animation-delay: 0.02s;
        }

        .adm-stat:nth-child(2) {
            animation-delay: 0.06s;
        }

        .adm-stat:nth-child(3) {
            animation-delay: 0.1s;
        }

        .adm-row {
            transition: background 0.15s ease;
        }

        .adm-row:hover {
            background: rgba(168, 232, 249, 0.1);
        }

        .adm-icon-btn {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .adm-icon-btn:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 16px rgba(1, 60, 88, 0.16);
        }

        .adm-create-btn {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .adm-create-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(245, 162, 1, 0.28);
        }

        .adm-toggle {
            transition: background 0.2s ease;
        }

        .adm-toggle span {
            transition: transform 0.2s ease;
        }

        .adm-field-wrap {
            border: 1.5px solid rgba(0, 83, 122, 0.14);
            border-radius: 11px;
            background: #FBFEFF;
        }

        .adm-field-wrap input,
        .adm-field-wrap textarea {
            width: 100%;
            background: transparent;
            border: none;
            outline: none;
            padding: 11px 13px;
            font-size: 13px;
            color: #013C58;
            font-family: 'Tajawal', sans-serif;
        }
    </style>
@endpush

@section('content')
    <div x-data="{ createModalOpen: {{ $errors->any() ? 'true' : 'false' }}, deactivateModalOpen: false, deactivateTargetId: null, deactivateTargetName: '' }" class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8"
        style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

        {{-- ============ HERO ============ --}}
        <div class="adm-hero"
            style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
            <div
                style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.2) 0%, rgba(255,211,91,0) 70%); pointer-events:none;">
            </div>

            <div
                style="position:relative; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:22px;">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div
                        style="display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2 3 7v6c0 5 3.8 8.7 9 9 5.2-.3 9-4 9-9V7l-9-5Z"></path>
                        </svg>
                    </div>
                    <div>
                        <p
                            style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">
                            إدارة ومتابعة</p>
                        <h1
                            style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">
                            الأدمنز</h1>
                    </div>
                </div>
                <button type="button" @click="createModalOpen = true" class="adm-create-btn"
                    style="display:inline-flex; align-items:center; gap:8px; padding:12px 22px; border-radius:12px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 5v14M5 12h14"></path>
                    </svg>
                    إضافة أدمن
                </button>
            </div>

            <div style="position:relative; display:flex; gap:14px; flex-wrap:wrap;">
                @php
                    $statCard =
                        'display:flex; align-items:center; gap:13px; background:rgba(255,211,91,0.08); border:1px solid rgba(255,211,91,0.22); border-radius:16px; padding:14px 18px; flex:1; min-width:150px;';
                    $iconWrapBase =
                        'display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.14); flex-shrink:0;';
                @endphp
                <div class="adm-stat" style="{{ $statCard }}">
                    <div style="{{ $iconWrapBase }} color:#FFD35B;"><svg width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="3"></rect>
                            <path d="M3 10h18"></path>
                        </svg></div>
                    <div>
                        <p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,236,176,0.85);">إجمالي الأدمنز
                        </p>
                        <p
                            style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">
                            {{ $totalCount }}</p>
                    </div>
                </div>
                <div class="adm-stat" style="{{ $statCard }}">
                    <div style="{{ $iconWrapBase }} color:#4CAF78;"><svg width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5"></path>
                        </svg></div>
                    <div>
                        <p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,236,176,0.85);">نشط</p>
                        <p
                            style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">
                            {{ $activeCount }}</p>
                    </div>
                </div>
                <div class="adm-stat" style="{{ $statCard }}">
                    <div style="{{ $iconWrapBase }} color:#FF8A65;"><svg width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="m15 9-6 6M9 9l6 6"></path>
                        </svg></div>
                    <div>
                        <p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,236,176,0.85);">معطّل</p>
                        <p
                            style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">
                            {{ $inactiveCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div
                style="display:flex; align-items:center; gap:10px; background:rgba(76,175,120,0.14); color:#2E7D55; border:1px solid rgba(76,175,120,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <path d="M22 4 12 14.01l-3-3"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- ============ FILTERS ============ --}}
        <form method="GET" action="{{ route('admin.admins.index') }}" class="adm-filters"
            style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:22px; background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:16px; padding:14px 18px;">
            <input type="text" name="search" value="{{ $search }}" placeholder="ابحث بالاسم أو الإيميل..."
                style="flex:1; min-width:180px; padding:9px 14px; border:1.5px solid rgba(0,83,122,0.14); border-radius:10px; background:#FBFEFF; color:#013C58; font-size:13px; font-family:'Tajawal',sans-serif; outline:none;">

            <select name="status"
                style="padding:9px 14px; border:1.5px solid rgba(0,83,122,0.14); border-radius:10px; background:#FBFEFF; color:#013C58; font-size:12.5px; font-family:'Tajawal',sans-serif; outline:none;">
                <option value="" style="color:#013C58;">{{ !$status ? '✓' : '○' }} كل الحالات</option>
                <option value="active" @selected($status === 'active') style="color:#2E7D55;">
                    {{ $status === 'active' ? '✓' : '●' }} نشط</option>
                <option value="inactive" @selected($status === 'inactive') style="color:#C2591A;">
                    {{ $status === 'inactive' ? '✓' : '●' }} معطّل</option>
            </select>

            <button type="submit"
                style="display:inline-flex; align-items:center; gap:6px; padding:9px 18px; border-radius:10px; border:none; background:#013C58; color:#fff; font-family:'Poppins',sans-serif; font-weight:600; font-size:12.5px; cursor:pointer;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                فلترة
            </button>
            @if ($search || $status)
                <a href="{{ route('admin.admins.index') }}"
                    style="font-size:12px; color:rgba(1,60,88,0.5); font-weight:600; text-decoration:none;">إلغاء
                    الفلترة</a>
            @endif
        </form>

        {{-- ============ ADMINS TABLE ============ --}}
        <div
            style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; overflow:hidden; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
            <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
                <thead>
                    <tr>
                        <th
                            style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 16px; background:rgba(168,232,249,0.22); width:15%;">
                            الاسم</th>
                        <th
                            style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:16%;">
                            الإيميل</th>
                        <th
                            style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:19%;">
                            الصلاحيات</th>
                        <th
                            style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:10%;">
                            تاريخ الانضمام</th>
                        <th
                            style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:9%;">
                            الراتب</th>
                        <th
                            style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:9%;">
                            الحالة</th>
                        <th
                            style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:22%;">
                            إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admins as $admin)
                        @php
                            $adminName = trim($admin->first_name . ' ' . $admin->last_name) ?: $admin->email;
                            $initials = strtoupper(
                                substr($admin->first_name ?? $admin->email, 0, 1) .
                                    substr($admin->last_name ?? '', 0, 1),
                            );
                        @endphp
                        <tr class="adm-row">
                            <td style="padding:14px 16px; border-bottom:1px solid rgba(0,83,122,0.05);">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div
                                        style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; background:#00537A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px; flex-shrink:0;">
                                        {{ $initials }}</div>
                                    <div
                                        style="font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; color:#013C58; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        {{ $adminName }}</div>
                                </div>
                            </td>
                            <td
                                style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); font-size:12.5px; color:rgba(1,60,88,0.65); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                {{ $admin->email }}</td>
                            <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05);">
                                @forelse ($admin->permissions as $permission)
                                    <span
                                        style="display:inline-flex; margin:2px; padding:4px 10px; border-radius:999px; background:rgba(14,106,150,0.1); color:#0E6A96; font-size:10.5px; font-weight:700; white-space:nowrap;">{{ $permission->name }}</span>
                                @empty
                                    <span style="font-size:11.5px; color:rgba(1,60,88,0.4);">ما في صلاحيات</span>
                                @endforelse
                            </td>
                            <td
                                style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center; font-size:12.5px; color:rgba(1,60,88,0.65);">
                                {{ $admin->created_at?->format('Y-m-d') }}</td>
                            <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                                <span title="عمود الراتب تصميم فقط — بانتظار الربط بالباك-إند"
                                    style="display:inline-flex; align-items:center; gap:4px; font-family:'Poppins',sans-serif; font-weight:700; font-size:12.5px; color:rgba(1,60,88,0.4);">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 8v4l3 2"></path>
                                    </svg>
                                    قيد الانتظار
                                </span>
                            </td>
                            <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                                @if ($admin->deleted_at)
                                    <span
                                        style="display:inline-flex; padding:5px 11px; border-radius:999px; background:rgba(255,138,101,0.18); color:#C2591A; font-size:11px; font-weight:700;">
                                        معطّل
                                    </span>
                                @else
                                    <span
                                        style="display:inline-flex; padding:5px 11px; border-radius:999px; background:rgba(76,175,120,0.16); color:#2E7D55; font-size:11px; font-weight:700;">
                                        نشط
                                    </span>
                                @endif
                            </td>
                            <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                                <div style="display:flex; gap:8px; justify-content:center; align-items:center;">
                                    <a href="{{ route('admin.permission.show', $admin) }}" title="عرض التفاصيل"
                                        class="adm-icon-btn"
                                        style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; background:rgba(168,232,249,0.22); color:#00537A; text-decoration:none;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="1.9" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                    </a>
                                     @unless ($admin->deleted_at)
                                    <a href="{{ route('admin.permissions', $admin) }}" title="تعديل الصلاحيات"
                                        class="adm-icon-btn"
                                        style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; background:rgba(255,211,91,0.16); color:#8A5A00; text-decoration:none;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="1.9" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="m9 12 2 2 4-4" />
                                            <circle cx="12" cy="12" r="10" />
                                        </svg>
                                    </a>
                                   
                                        <button type="button"
                                            @click="deactivateTargetId = {{ $admin->id }}; deactivateTargetName = {{ Illuminate\Support\Js::from($adminName) }}; deactivateModalOpen = true;"
                                            title="تعطيل الحساب"
                                            style="display:flex; align-items:center; justify-content:center; width:33px; height:33px; border-radius:10px; border:none; background:rgba(255,138,101,0.14); color:#C2591A; cursor:pointer;">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.9" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="9" />
                                                <path d="m5.5 5.5 13 13" />
                                            </svg>
                                        </button>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7"
                                style="padding:60px 20px; text-align:center; color:rgba(1,60,88,0.45); font-weight:600; font-size:14px;">
                                لايوجد ادمن بهذا القسم</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($admins instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div style="padding:16px 22px; border-top:1px solid rgba(0,83,122,0.06);">
                    {{ $admins->links('vendor.pagination.lessons') }}
                </div>
            @endif
        </div>

        <p style="margin:16px 2px 0; font-size:12px; color:rgba(1,60,88,0.45); font-weight:600;">
        </p>

        {{-- ============ CREATE MODAL ============ --}}
        <div x-show="createModalOpen" x-cloak
            style="position:fixed; inset:0; z-index:50; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px); overflow-y:auto;"
            @click="createModalOpen = false">
            <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
                <div @click.stop
                    style="position:relative; width:100%; max-width:580px; max-height:88vh; overflow-y:auto; background:#EFFAFD; border-radius:28px; padding:32px 28px 28px; box-shadow:0 50px 110px rgba(1,42,63,0.42); font-family:'Tajawal',sans-serif;"
                    dir="rtl">
                    <button type="button" @click="createModalOpen = false"
                        style="position:absolute; top:16px; left:16px; width:30px; height:30px; border-radius:50%; border:none; background:rgba(0,83,122,0.06); color:rgba(1,60,88,0.6); display:flex; align-items:center; justify-content:center; cursor:pointer;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </button>

                    <div style="text-align:center; margin-bottom:22px;">
                        <h1
                            style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#013C58;">
                            إضافة أدمن جديد</h1>
                        <p style="margin:6px 0 0; font-size:13px; color:rgba(1,60,88,0.5);">سوف يتم حفظ معلومات هذا الحساب
                            وارسال ايميل يتضمن كلمة المرور</p>
                    </div>

                    @if ($errors->any())
                        <div
                            style="display:flex; align-items:flex-start; gap:9px; margin-bottom:18px; padding:13px 16px; border-radius:12px; background:rgba(148,98,0,0.08); color:#946200; font-size:13px; font-weight:600;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                style="flex-shrink:0; margin-top:1px;">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 8v5"></path>
                                <path d="M12 16h.01"></path>
                            </svg>
                            <ul style="margin:0; padding-inline-start:16px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admins.permission.store') }}">
                        @csrf

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                            <div>
                                <label
                                    style="display:block; font-size:12px; font-weight:700; color:#013C58; margin-bottom:6px;">الاسم
                                    الأول</label>
                                <div class="adm-field-wrap"><input type="text" name="first_name"
                                        placeholder="مثال: سارة"></div>
                            </div>
                            <div>
                                <label
                                    style="display:block; font-size:12px; font-weight:700; color:#013C58; margin-bottom:6px;">الاسم
                                    الأخير</label>
                                <div class="adm-field-wrap"><input type="text" name="last_name"
                                        placeholder="مثال: يوسف"></div>
                            </div>
                        </div>

                        <div style="margin-bottom:16px;">
                            <label
                                style="display:block; font-size:12px; font-weight:700; color:#013C58; margin-bottom:6px;">البريد
                                الإلكتروني</label>
                            <div class="adm-field-wrap"><input type="email" name="email"
                                    placeholder="admin@example.com"></div>
                        </div>

                        <div style="margin-bottom:22px;">
                            <label
                                style="display:block; font-size:12px; font-weight:700; color:#013C58; margin-bottom:6px;">كلمة
                                المرور المبدئية</label>
                            <div class="adm-field-wrap"><input type="text" name="password"
                                    placeholder="8 أحرف على الأقل"></div>
                        </div>

                        <div style="margin-bottom:22px;">
                            <label
                                style="display:block; font-size:12px; font-weight:700; color:#013C58; margin-bottom:10px;">الصلاحيات
                                اختياري _يمكن التعديل عليها لاحقا </label>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                @foreach ($permissions as $permission)
                                    @php
                                        $permissionLabel = match ($permission->name) {
                                            'manage_levels' => 'إدارة المستويات',
                                            'manage_courses' => 'إدارة الكورسات',
                                            'manage_level_tests' => 'إدارة اختبارات تحديد المستوى',
                                            'manage_placement_tests' => 'إدارة اختبارات القبول',
                                            'manage_placement_questions' => 'إدارة بنك أسئلة تحديد المستوى',
                                            'manage_podcasts' => 'إدارة البودكاست والتوبكس',
                                            'publish_levels' => 'نشر المستويات',
                                            default => $permission->name,
                                        };
                                    @endphp
                                    <label
                                        style="display:flex; align-items:center; gap:8px; padding:10px 12px; border-radius:11px; border:1.5px solid rgba(0,83,122,0.14); background:#FBFEFF; cursor:pointer; font-size:12.5px; font-weight:600; color:#013C58;">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                            style="width:16px; height:16px; accent-color:#0E6A96;">
                                        {{ $permissionLabel }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div
                            style="display:flex; flex-direction:row-reverse; gap:10px; padding-top:18px; border-top:1px solid rgba(0,83,122,0.06);">
                            <button type="submit"
                                style="display:flex; align-items:center; gap:7px; padding:12px 24px; border-radius:999px; border:none; background:linear-gradient(90deg,#013C58,#00537A); color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px; cursor:pointer; box-shadow:0 14px 28px rgba(1,60,88,0.28);">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"></path>
                                </svg>
                                حفظ
                            </button>
                            <button type="button" @click="createModalOpen = false"
                                style="padding:12px 20px; border:none; background:transparent; color:rgba(1,60,88,0.5); font-family:'Poppins',sans-serif; font-weight:600; font-size:13.5px; cursor:pointer;">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ============ DEACTIVATE CONFIRM MODAL ============ --}}
        <div x-show="deactivateModalOpen" x-cloak
            style="position:fixed; inset:0; z-index:50; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px); overflow-y:auto;"
            @click="deactivateModalOpen = false">
            <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
                <div @click.stop
                    style="width:100%; max-width:400px; background:#EFFAFD; border-radius:22px; padding:30px 26px; box-shadow:0 44px 100px rgba(1,42,63,0.4); text-align:center;">
                    <div
                        style="width:58px; height:58px; border-radius:16px; background:rgba(255,138,101,0.14); color:#C2591A; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9" />
                            <path d="m5.5 5.5 13 13" />
                        </svg>
                    </div>
                    <h3
                        style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:17px; color:#013C58;">
                        تعطيل حساب "<span x-text="deactivateTargetName"></span>"؟</h3>
                    <p style="margin:10px 0 0; font-size:13px; color:rgba(1,60,88,0.6); line-height:1.7;">
                    </p>
                    <div style="display:flex; gap:10px; margin-top:22px;">
                        <button type="button" @click="deactivateModalOpen = false"
                            style="flex:1; padding:11px; border-radius:11px; border:1.5px solid rgba(0,83,122,0.12); background:#EFFAFD; color:#013C58; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer;">إلغاء</button>
                        <form :action="'/permission/' + deactivateTargetId + '/delete'" method="POST" style="flex:1;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                style="width:100%; padding:11px; border-radius:11px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">تأكيد</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
