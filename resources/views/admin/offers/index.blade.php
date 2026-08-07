@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes offersFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .ofr-hero, .ofr-stat, .ofr-coupon { animation: offersFadeUp 0.4s ease both; }
    .ofr-coupon:nth-child(2) { animation-delay: 0.05s; }
    .ofr-coupon:nth-child(3) { animation-delay: 0.1s; }

    .ofr-coupon { transition: transform 0.2s ease, box-shadow 0.2s ease; position:relative; }
    .ofr-coupon:hover { transform: translateY(-4px); box-shadow: 0 22px 44px rgba(1,60,88,0.14); }

    .ofr-coupon-tear { border-inline-start: 2px dashed rgba(255,255,255,0.55); position:relative; }
    .ofr-coupon-tear::before, .ofr-coupon-tear::after {
        content:''; position:absolute; inset-inline-start:-11px; width:22px; height:22px; border-radius:50%; background:#DFF2F9; z-index:2;
    }
    .ofr-coupon-tear::before { top:-11px; }
    .ofr-coupon-tear::after { bottom:-11px; }

    .ofr-create-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .ofr-create-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(245,162,1,0.28); }

    .ofc-field-wrap { border:1.5px solid rgba(14,106,150,0.2); border-radius:12px; background:#FBFEFF; transition:border-color 0.15s ease, box-shadow 0.15s ease; }
    .ofc-field-wrap:focus-within { border-color:#0E6A96; box-shadow:0 0 0 3px rgba(168,232,249,0.35); }
    .ofc-field-wrap input { width:100%; background:transparent; border:none; outline:none; padding:12px 14px; font-size:13px; color:#013C58; font-family:'Tajawal',sans-serif; }

    .ofc-select-btn { display:flex; align-items:center; justify-content:space-between; width:100%; padding:12px 14px; background:transparent; border:none; cursor:pointer; font-size:13px; font-family:'Tajawal',sans-serif; color:#013C58; text-align:right; }
    .ofc-select-panel { position:absolute; z-index:20; top:calc(100% + 6px); right:0; left:0; max-height:260px; overflow-y:auto; background:#fff; border:1.5px solid rgba(14,106,150,0.25); border-radius:14px; box-shadow:0 20px 44px rgba(1,60,88,0.16); padding:8px; }
    .ofc-select-panel::-webkit-scrollbar { width:7px; }
    .ofc-select-panel::-webkit-scrollbar-track { background:transparent; }
    .ofc-select-panel::-webkit-scrollbar-thumb { background:rgba(0,83,122,0.18); border-radius:999px; }
    .ofc-select-option { display:block; width:100%; text-align:right; padding:10px 12px; border:none; background:transparent; border-radius:9px; font-size:12.5px; font-family:'Tajawal',sans-serif; color:#013C58; cursor:pointer; transition:background 0.12s ease; }
    .ofc-select-option:hover { background:rgba(168,232,249,0.25); }
    .ofc-select-option.is-active { background:rgba(245,162,1,0.14); color:#8A5A00; font-weight:700; }

    .ofc-submit-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .ofc-submit-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(245,162,1,0.28); }

    /* ---- custom calendar (native date-picker popup can't be recolored, so we build our own) ---- */
    .ofc-cal-panel { position:absolute; z-index:30; bottom:calc(100% + 6px); right:0; width:290px; background:#fff; border:1.5px solid rgba(14,106,150,0.25); border-radius:16px; box-shadow:0 -14px 40px rgba(1,60,88,0.18); padding:16px; }
    .ofc-cal-nav-btn { display:flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; border:none; background:rgba(168,232,249,0.3); color:#0E6A96; cursor:pointer; transition:background 0.12s ease; }
    .ofc-cal-nav-btn:hover { background:rgba(168,232,249,0.55); }
    .ofc-cal-weekday { text-align:center; font-size:10.5px; font-weight:700; color:#0E6A96; padding-bottom:6px; }
    .ofc-cal-day { display:flex; align-items:center; justify-content:center; width:32px; height:32px; margin:2px auto; border-radius:10px; border:none; background:transparent; font-family:'Poppins',sans-serif; font-weight:600; font-size:12.5px; color:#013C58; cursor:pointer; transition:background 0.12s ease, color 0.12s ease; }
    .ofc-cal-day:hover:not(:disabled) { background:rgba(168,232,249,0.35); }
    .ofc-cal-day:disabled { color:rgba(1,60,88,0.22); cursor:default; }
    .ofc-cal-day.is-today { box-shadow:inset 0 0 0 1.5px #F5A201; }
    .ofc-cal-day.is-selected { background:linear-gradient(135deg,#0E6A96,#146B93); color:#fff; box-shadow:0 4px 10px rgba(14,106,150,0.35); }
    .ofc-cal-footer-btn { font-size:11.5px; font-weight:700; color:#8A5A00; background:none; border:none; cursor:pointer; }
    .ofc-cal-footer-btn:hover { text-decoration:underline; }
</style>
@endpush

@section('content')
<div x-data="{ createOpen: {{ $errors->any() ? 'true' : 'false' }} }" class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl">

    @if (session('info'))
        <div style="display:flex; align-items:center; gap:10px; background:rgba(168,232,249,0.18); color:#00537A; border:1px solid rgba(0,83,122,0.14); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13.5px; font-weight:600;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path></svg>
            {{ session('info') }}
        </div>
    @endif

    {{-- ============ HERO ============ --}}
    <div class="ofr-hero" style="position:relative; overflow:hidden; background:linear-gradient(135deg,#013C58 0%, #00537A 55%, #C2591A 150%); border-radius:26px; padding:26px 32px 24px; margin-bottom:22px; box-shadow:0 24px 55px rgba(1,60,88,0.22);">
        <div style="position:absolute; width:420px; height:420px; right:-120px; top:-160px; border-radius:50%; background:radial-gradient(circle, rgba(255,211,91,0.25) 0%, rgba(255,211,91,0) 70%); pointer-events:none;"></div>

        <div style="position:relative; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:22px;">
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="display:flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:16px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.18); color:#FFD35B; flex-shrink:0;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m20.59 13.41-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z"></path><circle cx="7.5" cy="7.5" r="1.5"></circle></svg>
                </div>
                <div>
                    <p style="margin:0; font-size:11.5px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; color:rgba(255,211,91,0.9);">SPECIAL OFFERS</p>
                    <h1 style="margin:6px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:23px; color:#fff;">الخصومات والعروض</h1>
                </div>
            </div>
            <button type="button" @click="createOpen = !createOpen" class="ofr-create-btn" style="display:inline-flex; align-items:center; gap:8px; padding:12px 22px; border-radius:12px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" :style="createOpen ? 'transform:rotate(45deg); transition:transform .2s ease;' : 'transition:transform .2s ease;'"><path d="M12 5v14M5 12h14"></path></svg>
                <span x-text="createOpen ? 'إلغاء' : 'إنشاء عرض جديد'"></span>
            </button>
        </div>

        <div style="position:relative; display:flex; gap:14px; flex-wrap:wrap;">
            <div class="ofr-stat" style="display:flex; align-items:center; gap:13px; background:rgba(76,175,120,0.16); border:1px solid rgba(76,175,120,0.3); border-radius:16px; padding:14px 18px; flex:1; min-width:170px;">
                <div style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.14); color:#7EE0B2; flex-shrink:0;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="M22 4 12 14.01l-3-3"></path></svg></div>
                <div><p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,255,255,0.75);">عروض فعالة حاليًا</p><p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $activeOffers->count() }}</p></div>
            </div>
            <div class="ofr-stat" style="display:flex; align-items:center; gap:13px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.14); border-radius:16px; padding:14px 18px; flex:1; min-width:170px;">
                <div style="display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,0.14); color:#A8E8F9; flex-shrink:0;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg></div>
                <div><p style="margin:0; font-size:11.5px; font-weight:600; color:rgba(255,255,255,0.75);">عروض قديمة/منتهية</p><p style="margin:2px 0 0; font-family:'Poppins',sans-serif; font-weight:800; font-size:20px; color:#fff;">{{ $expiredOffers->count() }}</p></div>
            </div>
        </div>
    </div>

    {{-- ============ INLINE CREATE FORM ============ --}}
    <div x-show="createOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="margin-bottom:26px;">
        <div style="background:#EFFAFD; border:1.5px solid rgba(245,162,1,0.3); border-radius:20px; padding:26px; box-shadow:0 18px 44px rgba(0,83,122,0.08);">
            <h3 style="margin:0 0 18px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14.5px; color:#013C58;">إنشاء عرض / خصم جديد</h3>

            <form method="POST" action="{{ route('admin.offers.store') }}" x-data="{ courseOpen:false, courseId:'', courseLabel:'اختاري الكورس...' }">
                @csrf
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                    <div>
                        <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">الكورس</label>
                        <div @click.outside="courseOpen = false" style="position:relative;">
                            <div class="ofc-field-wrap">
                                <button type="button" class="ofc-select-btn" @click="courseOpen = !courseOpen">
                                    <span x-text="courseLabel" :style="courseId ? 'color:#013C58; font-weight:600;' : 'color:rgba(1,60,88,0.4);'"></span>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0E6A96" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" :style="courseOpen ? 'transform:rotate(180deg); transition:transform .15s ease;' : 'transition:transform .15s ease;'"><path d="m6 9 6 6 6-6"></path></svg>
                                </button>
                            </div>
                            <input type="hidden" name="course_id" :value="courseId" required>
                            <div x-show="courseOpen" x-cloak x-transition class="ofc-select-panel">
                                @foreach ($courses as $course)
                                    <button type="button" class="ofc-select-option" :class="courseId === '{{ $course->id }}' ? 'is-active' : ''"
                                        @click="courseId = '{{ $course->id }}'; courseLabel = {{ \Illuminate\Support\Js::from($course->name_ar.' ('.$course->name_en.') — '.($course->level->name_ar ?? '')) }}; courseOpen = false;">
                                        {{ $course->name_ar }} ({{ $course->name_en }}) — {{ $course->level->name_ar ?? '' }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div>
                        <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">نسبة الخصم %</label>
                        <div class="ofc-field-wrap"><input type="number" name="discount_percent" min="1" max="90" placeholder="مثال: 25" required></div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:22px;">
                    <div x-data="ofcMiniDatePicker()" @click.outside="open = false" style="position:relative;">
                        <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">تاريخ البداية</label>
                        <div class="ofc-field-wrap">
                            <button type="button" class="ofc-select-btn" @click="open = !open">
                                <span x-text="displayLabel" :style="selected ? 'color:#013C58; font-weight:600;' : 'color:rgba(1,60,88,0.4);'"></span>
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0E6A96" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M3 10h18"></path><path d="M8 2v4M16 2v4"></path></svg>
                            </button>
                        </div>
                        <input type="hidden" name="starts_at" :value="selected" required>
                        <div x-show="open" x-cloak x-transition class="ofc-cal-panel">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                                <button type="button" class="ofc-cal-nav-btn" @click="prevMonth()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg></button>
                                <span style="font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; color:#013C58;" x-text="monthNames[viewMonth] + ' ' + viewYear"></span>
                                <button type="button" class="ofc-cal-nav-btn" @click="nextMonth()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg></button>
                            </div>
                            <div style="display:grid; grid-template-columns:repeat(7,1fr);">
                                <template x-for="wd in weekDays" :key="wd"><div class="ofc-cal-weekday" x-text="wd"></div></template>
                            </div>
                            <div style="display:grid; grid-template-columns:repeat(7,1fr);">
                                <template x-for="(cell, i) in grid" :key="i">
                                    <button type="button" class="ofc-cal-day" :class="{ 'is-today': isToday(cell.dateStr), 'is-selected': cell.dateStr === selected }" :disabled="cell.muted" @click="pick(cell.dateStr)" x-text="cell.day"></button>
                                </template>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-top:12px; padding-top:10px; border-top:1px solid rgba(0,83,122,0.08);">
                                <button type="button" class="ofc-cal-footer-btn" @click="clear()">محو</button>
                                <button type="button" class="ofc-cal-footer-btn" @click="goToday()">اليوم</button>
                            </div>
                        </div>
                    </div>
                    <div x-data="ofcMiniDatePicker()" @click.outside="open = false" style="position:relative;">
                        <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">تاريخ النهاية</label>
                        <div class="ofc-field-wrap">
                            <button type="button" class="ofc-select-btn" @click="open = !open">
                                <span x-text="displayLabel" :style="selected ? 'color:#013C58; font-weight:600;' : 'color:rgba(1,60,88,0.4);'"></span>
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0E6A96" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M3 10h18"></path><path d="M8 2v4M16 2v4"></path></svg>
                            </button>
                        </div>
                        <input type="hidden" name="ends_at" :value="selected" required>
                        <div x-show="open" x-cloak x-transition class="ofc-cal-panel">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                                <button type="button" class="ofc-cal-nav-btn" @click="prevMonth()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg></button>
                                <span style="font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; color:#013C58;" x-text="monthNames[viewMonth] + ' ' + viewYear"></span>
                                <button type="button" class="ofc-cal-nav-btn" @click="nextMonth()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg></button>
                            </div>
                            <div style="display:grid; grid-template-columns:repeat(7,1fr);">
                                <template x-for="wd in weekDays" :key="wd"><div class="ofc-cal-weekday" x-text="wd"></div></template>
                            </div>
                            <div style="display:grid; grid-template-columns:repeat(7,1fr);">
                                <template x-for="(cell, i) in grid" :key="i">
                                    <button type="button" class="ofc-cal-day" :class="{ 'is-today': isToday(cell.dateStr), 'is-selected': cell.dateStr === selected }" :disabled="cell.muted" @click="pick(cell.dateStr)" x-text="cell.day"></button>
                                </template>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-top:12px; padding-top:10px; border-top:1px solid rgba(0,83,122,0.08);">
                                <button type="button" class="ofc-cal-footer-btn" @click="clear()">محو</button>
                                <button type="button" class="ofc-cal-footer-btn" @click="goToday()">اليوم</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px; padding-top:18px; border-top:1px solid rgba(0,83,122,0.08);">
                    <button type="button" @click="createOpen = false" style="display:inline-flex; align-items:center; padding:12px 20px; border-radius:12px; border:none; background:rgba(0,83,122,0.08); color:#00537A; font-family:'Poppins',sans-serif; font-weight:600; font-size:13px; cursor:pointer;">إلغاء</button>
                    <button type="submit" class="ofc-submit-btn" style="display:inline-flex; align-items:center; gap:7px; padding:12px 26px; border-radius:12px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:13px; cursor:pointer;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
                        حفظ العرض
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============ ACTIVE OFFERS ============ --}}
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:14px;">
        <span style="width:8px; height:8px; border-radius:50%; background:#4CAF78;"></span>
        <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:15px; color:#013C58;">عروض فعالة</h3>
    </div>
    @if ($activeOffers->isEmpty())
        <div style="background:#EFFAFD; border:1.5px dashed rgba(0,83,122,0.2); border-radius:18px; padding:30px; text-align:center; color:rgba(1,60,88,0.45); font-weight:600; font-size:13px; margin-bottom:26px;">ما في عروض فعالة حاليًا</div>
    @else
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:18px; margin-bottom:26px;">
            @foreach ($activeOffers as $offer)
                <div class="ofr-coupon" style="display:flex; background:#EFFAFD; border:1.5px solid rgba(76,175,120,0.3); border-radius:18px; overflow:hidden; box-shadow:0 12px 28px rgba(0,83,122,0.07);">
                    <div style="flex:1; padding:18px 20px;">
                        <span style="display:inline-flex; padding:4px 11px; border-radius:999px; background:rgba(76,175,120,0.16); color:#2E7D55; font-size:10.5px; font-weight:700; margin-bottom:10px;">فعال الآن</span>
                        <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:700; font-size:14.5px; color:#013C58; line-height:1.4;">{{ $offer['course']->name_ar }}</p>
                        <p style="margin:4px 0 0; font-size:11.5px; color:rgba(1,60,88,0.5);">{{ $offer['course']->name_en }}</p>
                        <div style="display:flex; align-items:center; gap:6px; margin-top:12px; font-size:11px; color:rgba(1,60,88,0.55); font-weight:600;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M3 10h18"></path></svg>
                            {{ $offer['starts_at']->format('Y-m-d') }} → {{ $offer['ends_at']->format('Y-m-d') }}
                        </div>
                    </div>
                    <div class="ofr-coupon-tear" style="width:100px; flex-shrink:0; background:linear-gradient(160deg,#F5A201,#C2591A); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:2px;">
                        <span style="font-family:'Poppins',sans-serif; font-weight:800; font-size:26px; color:#fff;" dir="ltr">%{{ $offer['discount'] }}</span>
                        <span style="font-size:10px; font-weight:700; color:rgba(255,255,255,0.85);">خصم</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ============ EXPIRED OFFERS ============ --}}
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:14px;">
        <span style="width:8px; height:8px; border-radius:50%; background:rgba(1,60,88,0.3);"></span>
        <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:15px; color:#013C58;">عروض قديمة</h3>
    </div>
    @if ($expiredOffers->isEmpty())
        <div style="background:#EFFAFD; border:1.5px dashed rgba(0,83,122,0.2); border-radius:18px; padding:30px; text-align:center; color:rgba(1,60,88,0.45); font-weight:600; font-size:13px;">ما في عروض قديمة</div>
    @else
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:18px;">
            @foreach ($expiredOffers as $offer)
                <div class="ofr-coupon" style="display:flex; background:rgba(239,250,253,0.5); border:1.5px solid rgba(0,83,122,0.12); border-radius:18px; overflow:hidden; opacity:0.75;">
                    <div style="flex:1; padding:18px 20px;">
                        <span style="display:inline-flex; padding:4px 11px; border-radius:999px; background:rgba(1,60,88,0.08); color:rgba(1,60,88,0.5); font-size:10.5px; font-weight:700; margin-bottom:10px;">منتهي</span>
                        <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:700; font-size:14.5px; color:rgba(1,60,88,0.7); line-height:1.4;">{{ $offer['course']->name_ar }}</p>
                        <p style="margin:4px 0 0; font-size:11.5px; color:rgba(1,60,88,0.4);">{{ $offer['course']->name_en }}</p>
                        <div style="display:flex; align-items:center; gap:6px; margin-top:12px; font-size:11px; color:rgba(1,60,88,0.4); font-weight:600;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="3"></rect><path d="M3 10h18"></path></svg>
                            {{ $offer['starts_at']->format('Y-m-d') }} → {{ $offer['ends_at']->format('Y-m-d') }}
                        </div>
                    </div>
                    <div class="ofr-coupon-tear" style="width:100px; flex-shrink:0; background:rgba(1,60,88,0.35); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:2px;">
                        <span style="font-family:'Poppins',sans-serif; font-weight:800; font-size:26px; color:#fff;" dir="ltr">%{{ $offer['discount'] }}</span>
                        <span style="font-size:10px; font-weight:700; color:rgba(255,255,255,0.75);">خصم</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <p style="margin:20px 2px 0; font-size:12px; color:rgba(1,60,88,0.45); font-weight:600;">

    </p>
</div>

<script>
    function ofcMiniDatePicker() {
        const today = new Date();
        return {
            open: false,
            selected: null,
            viewYear: today.getFullYear(),
            viewMonth: today.getMonth(),
            weekDays: ['ح', 'ن', 'ث', 'ر', 'خ', 'ج', 'س'],
            monthNames: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
            get displayLabel() {
                if (!this.selected) return 'اختاري التاريخ...';
                const [y, m, d] = this.selected.split('-').map(Number);
                return d + ' ' + this.monthNames[m - 1] + ' ' + y;
            },
            get grid() {
                const firstOfMonth = new Date(this.viewYear, this.viewMonth, 1);
                const startOffset = firstOfMonth.getDay();
                const daysInMonth = new Date(this.viewYear, this.viewMonth + 1, 0).getDate();
                const daysInPrevMonth = new Date(this.viewYear, this.viewMonth, 0).getDate();
                const cells = [];
                for (let i = startOffset - 1; i >= 0; i--) {
                    cells.push({ day: daysInPrevMonth - i, muted: true, dateStr: null });
                }
                for (let d = 1; d <= daysInMonth; d++) {
                    const mm = String(this.viewMonth + 1).padStart(2, '0');
                    const dd = String(d).padStart(2, '0');
                    cells.push({ day: d, muted: false, dateStr: `${this.viewYear}-${mm}-${dd}` });
                }
                let nextDay = 1;
                while (cells.length % 7 !== 0) {
                    cells.push({ day: nextDay++, muted: true, dateStr: null });
                }
                return cells;
            },
            prevMonth() { this.viewMonth--; if (this.viewMonth < 0) { this.viewMonth = 11; this.viewYear--; } },
            nextMonth() { this.viewMonth++; if (this.viewMonth > 11) { this.viewMonth = 0; this.viewYear++; } },
            pick(dateStr) { if (!dateStr) return; this.selected = dateStr; this.open = false; },
            goToday() {
                const t = new Date();
                this.viewYear = t.getFullYear(); this.viewMonth = t.getMonth();
                const mm = String(this.viewMonth + 1).padStart(2, '0');
                const dd = String(t.getDate()).padStart(2, '0');
                this.selected = `${this.viewYear}-${mm}-${dd}`;
                this.open = false;
            },
            clear() { this.selected = null; },
            isToday(dateStr) {
                if (!dateStr) return false;
                const t = new Date();
                const mm = String(t.getMonth() + 1).padStart(2, '0');
                const dd = String(t.getDate()).padStart(2, '0');
                return dateStr === `${t.getFullYear()}-${mm}-${dd}`;
            },
        };
    }
</script>
@endsection
