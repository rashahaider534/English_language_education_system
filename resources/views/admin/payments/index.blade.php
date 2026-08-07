@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes paymentsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .pay-hero, .pay-stat, .pay-row { animation: paymentsFadeUp 0.4s ease both; }
    .pay-stat { transition: transform 0.2s ease; }
    .pay-stat:hover { transform: translateY(-3px); }
    .pay-row:hover { background: rgba(168,232,249,0.1); }
    .pay-filter-pill { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .pay-filter-pill:hover { transform: translateY(-1px); }
</style>
@endpush

@section('content')
@php
    $statusLabels = ['pending' => 'قيد الانتظار', 'paid' => 'ناجحة', 'failed' => 'فاشلة'];
    $statusColors = [
        'pending' => ['bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00'],
        'paid'    => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55'],
        'failed'  => ['bg' => 'rgba(255,138,101,0.18)', 'fg' => '#C2591A'],
    ];
@endphp
<div class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    {{-- ============ HERO ============ --}}
    <div class="pay-hero" style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.2) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>
        <svg width="160" height="160" viewBox="0 0 24 24" fill="none" stroke="#A8E8F9" stroke-width="1" style="position:absolute; left:-30px; bottom:-45px; opacity:0.08; pointer-events:none;"><rect x="2" y="5" width="20" height="14" rx="3"></rect><path d="M2 10h20"></path></svg>

        <div style="position:relative; display:flex; align-items:center; gap:16px; margin-bottom:22px;">
            <div style="display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="3"></rect><path d="M2 10h20"></path></svg>
            </div>
            <div>
                <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(168,232,249,0.8);">المدفوعات</p>
                <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">سجل مدفوعات الطلاب</h1>
            </div>
        </div>

        <div style="position:relative; display:flex; gap:14px; flex-wrap:wrap;">
            <div class="pay-stat" style="display:flex; align-items:center; gap:13px; background:linear-gradient(135deg, rgba(255,211,91,0.24), rgba(255,211,91,0.08)); border:1px solid rgba(255,211,91,0.38); border-radius:16px; padding:14px 18px; flex:1; min-width:160px;">
                <div style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.2); color:#FFD35B; flex-shrink:0;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></div>
                <div><p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,255,255,0.8);">إجمالي الإيرادات</p><p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:19px; color:#fff;" dir="ltr">${{ number_format($totalRevenue, 2) }}</p></div>
            </div>
            <div class="pay-stat" style="display:flex; align-items:center; gap:13px; background:linear-gradient(135deg, rgba(126,224,178,0.24), rgba(126,224,178,0.08)); border:1px solid rgba(126,224,178,0.38); border-radius:16px; padding:14px 18px; flex:1; min-width:160px;">
                <div style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.2); color:#7EE0B2; flex-shrink:0;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg></div>
                <div><p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,255,255,0.8);">عمليات ناجحة</p><p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $paidCount }}</p></div>
            </div>
            <div class="pay-stat" style="display:flex; align-items:center; gap:13px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.14); border-radius:16px; padding:14px 18px; flex:1; min-width:160px;">
                <div style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.14); color:#A8E8F9; flex-shrink:0;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg></div>
                <div><p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,255,255,0.8);">قيد الانتظار</p><p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $pendingCount }}</p></div>
            </div>
            <div class="pay-stat" style="display:flex; align-items:center; gap:13px; background:linear-gradient(135deg, rgba(255,138,101,0.22), rgba(255,138,101,0.06)); border:1px solid rgba(255,138,101,0.35); border-radius:16px; padding:14px 18px; flex:1; min-width:160px;">
                <div style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.2); color:#FF8A65; flex-shrink:0;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="m15 9-6 6M9 9l6 6"></path></svg></div>
                <div><p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,255,255,0.8);">فاشلة</p><p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $failedCount }}</p></div>
            </div>
        </div>
    </div>

    {{-- ============ FILTER ============ --}}
    <div style="display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap;">
        <a href="{{ route('admin.payments.index') }}" class="pay-filter-pill" style="padding:9px 18px; border-radius:999px; font-size:12.5px; font-weight:700; text-decoration:none; background:{{ !$status ? '#013C58' : '#EFFAFD' }}; color:{{ !$status ? '#fff' : '#013C58' }}; border:1.5px solid rgba(0,83,122,0.16); box-shadow:{{ !$status ? '0 6px 14px rgba(1,60,88,0.22)' : 'none' }};">الكل</a>
        @foreach (['paid' => 'ناجحة', 'pending' => 'قيد الانتظار', 'failed' => 'فاشلة'] as $key => $label)
            @php $activeColor = $statusColors[$key]; @endphp
            <a href="{{ route('admin.payments.index', ['status' => $key]) }}" class="pay-filter-pill" style="padding:9px 18px; border-radius:999px; font-size:12.5px; font-weight:700; text-decoration:none; background:{{ $status === $key ? '#013C58' : '#EFFAFD' }}; color:{{ $status === $key ? '#fff' : '#013C58' }}; border:1.5px solid rgba(0,83,122,0.16); box-shadow:{{ $status === $key ? '0 6px 14px rgba(1,60,88,0.22)' : 'none' }};">{{ $label }}</a>
        @endforeach
    </div>

    {{-- ============ PAYMENTS TABLE ============ --}}
    <div style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:22px; overflow:hidden; box-shadow:0 18px 44px rgba(0,83,122,0.06);">
        <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
            <thead>
                <tr>
                    <th style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 16px; background:rgba(168,232,249,0.22); width:26%;">الطالب</th>
                    <th style="text-align:right; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:20%;">المستوى</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:14%;">المبلغ</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:14%;">الحالة</th>
                    <th style="text-align:center; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.45); text-transform:uppercase; padding:13px 12px; background:rgba(168,232,249,0.22); width:16%;">التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    @php
                        $payerName = $payment->user ? (trim(($payment->user->first_name ?? '').' '.($payment->user->last_name ?? '')) ?: $payment->user->email) : '—';
                        $initials = $payment->user ? strtoupper(substr($payment->user->first_name ?? $payment->user->email, 0, 1)) : '؟';
                        $statusVal = $payment->status instanceof \BackedEnum ? $payment->status->value : $payment->status;
                        $sc = $statusColors[$statusVal] ?? $statusColors['pending'];
                    @endphp
                    <tr class="pay-row">
                        <td style="padding:14px 16px; border-bottom:1px solid rgba(0,83,122,0.05);">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:10px; background:#00537A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px; flex-shrink:0;">{{ $initials }}</div>
                                <span style="font-size:13px; font-weight:700; color:#013C58; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $payerName }}</span>
                            </div>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); font-size:12.5px; color:rgba(1,60,88,0.65);">{{ $payment->level->name_ar ?? '—' }}</td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; color:#013C58;" dir="ltr">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center;">
                            <span style="display:inline-flex; align-items:center; gap:5px; padding:5px 11px; border-radius:999px; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }}; font-size:11px; font-weight:700;">
                                <span style="width:6px; height:6px; border-radius:50%; background:{{ $sc['fg'] }};"></span>
                                {{ $statusLabels[$statusVal] ?? $statusVal }}
                            </span>
                        </td>
                        <td style="padding:14px 12px; border-bottom:1px solid rgba(0,83,122,0.05); text-align:center; font-size:12px; color:rgba(1,60,88,0.55);">{{ $payment->paid_at?->format('Y-m-d') ?? $payment->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:0;">
                            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px; padding:60px 20px;">
                                <div style="display:flex; align-items:center; justify-content:center; width:56px; height:56px; border-radius:16px; background:rgba(0,83,122,0.06); color:rgba(1,60,88,0.3);">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="3"></rect><path d="M2 10h20"></path></svg>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($payments instanceof \Illuminate\Pagination\LengthAwarePaginator && $payments->hasPages())
            <div style="padding:16px 22px; border-top:1px solid rgba(0,83,122,0.06);">
                {{ $payments->links('vendor.pagination.lessons') }}
            </div>
        @endif
    </div>
</div>
@endsection
