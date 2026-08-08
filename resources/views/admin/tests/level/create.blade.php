@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .t-panel { animation: lessonsFadeUp 0.4s ease both; }

    .t-field-wrap { border:1.5px solid rgba(0,83,122,0.14); border-radius:11px; background:#FBFEFF; }
    .t-field-wrap input, .t-field-wrap select { width:100%; background:transparent; border:none; outline:none; padding:11px 13px; font-size:13px; color:#013C58; font-family:'Tajawal',sans-serif; }
    .t-field-wrap input:focus, .t-field-wrap select:focus,
    .t-field-wrap input:focus-visible, .t-field-wrap select:focus-visible {
        outline: none !important;
        box-shadow: none !important;
        border: none !important;
    }
    .t-field-wrap:focus-within {
        border-color: rgba(0,83,122,0.14) !important;
    }

    .t-diff-card { transition: border-color 0.15s ease, box-shadow 0.15s ease; }

    .t-diff-input:focus, .t-diff-input:focus-visible {
        outline: none !important;
        box-shadow: none !important;
        border: 1.5px solid rgba(0,83,122,0.14) !important;
    }

    .t-submit-btn {
        display:inline-flex; align-items:center; gap:8px; padding:13px 30px; border-radius:12px; border:none;
        background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:14px;
        transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
    }
    .t-submit-btn:disabled { opacity:0.45; cursor:not-allowed; }
    .t-submit-btn:not(:disabled) { cursor:pointer; }
    .t-submit-btn:not(:disabled):hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(245,162,1,0.28); }
</style>
@endpush

@section('content')
@php
    $difficultyLabels = ['EASY' => 'سهل', 'MEDIUM' => 'متوسط', 'HARD' => 'صعب'];
    $difficultyColors = [
        'EASY'   => ['bg' => 'rgba(76,175,120,0.16)', 'fg' => '#2E7D55'],
        'MEDIUM' => ['bg' => 'rgba(255,186,66,0.16)', 'fg' => '#8A5A00'],
        'HARD'   => ['bg' => 'rgba(255,138,101,0.18)', 'fg' => '#C2591A'],
    ];
    $available = [
        'EASY' => (int) ($availableByDifficulty['EASY'] ?? 0),
        'MEDIUM' => (int) ($availableByDifficulty['MEDIUM'] ?? 0),
        'HARD' => (int) ($availableByDifficulty['HARD'] ?? 0),
    ];
@endphp
<div
    x-data="levelTestForm(@js($available))"
    class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8"
    style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl"
>
    <div style="margin-bottom:18px;">
        <a href="{{ route('levels.index') }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            العودة إلى المستويات
        </a>
    </div>

    @if ($errors->any())
        <div style="background:rgba(255,138,101,0.14); color:#C2591A; border:1px solid rgba(255,138,101,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13px; font-weight:600;">
            @foreach ($errors->all() as $error)
                <p style="margin:0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('tests.level.levelTest.generate', $level) }}" method="POST">
        @csrf

        {{-- ============ TITLE + PASSING SCORE ============ --}}
        <div class="t-panel" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:26px; margin-bottom:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
            <h3 style="margin:0 0 18px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">معلومات الاختبار</h3>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">عنوان الاختبار (إنكليزي)</label>
                    <div class="t-field-wrap"><input type="text" name="title_en" value="{{ old('title_en') }}" placeholder="e.g. Level 1 Final Test" autocomplete="off" required></div>
                </div>
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">عنوان الاختبار (عربي)</label>
                    <div class="t-field-wrap"><input type="text" name="title_ar" value="{{ old('title_ar') }}" placeholder="مثال: اختبار نهاية المستوى الأول" autocomplete="off" required></div>
                </div>
            </div>
            <div style="max-width:260px;">
                <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">درجة النجاح (10-100)</label>
                <div class="t-field-wrap"><input type="number" name="passing_score" min="10" max="100" value="{{ old('passing_score', 60) }}" required></div>
            </div>
        </div>

        {{-- ============ DIFFICULTY COUNTS ============ --}}
        <div class="t-panel" style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.35); border-radius:20px; padding:26px; margin-bottom:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
            <h3 style="margin:0 0 6px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">توزيع الأسئلة حسب الصعوبة</h3>
            <p style="margin:0 0 18px; font-size:11.5px; color:rgba(1,60,88,0.5);"> .</p>

            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:16px;">
                @foreach (['EASY', 'MEDIUM', 'HARD'] as $diff)
                    @php $dc = $difficultyColors[$diff]; @endphp
                    <div class="t-diff-card" style="border:1.5px solid {{ $available[$diff] > 0 ? 'rgba(0,83,122,0.14)' : 'rgba(229,72,77,0.25)' }}; border-radius:14px; padding:18px; background:#FBFEFF;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                            <span style="display:inline-flex; padding:4px 10px; border-radius:999px; background:{{ $dc['bg'] }}; color:{{ $dc['fg'] }}; font-size:11px; font-weight:700;">{{ $difficultyLabels[$diff] }}</span>
                            <span style="font-size:11px; font-weight:700; color:{{ $available[$diff] > 0 ? 'rgba(1,60,88,0.5)' : '#C2591A' }};">متوفر: {{ $available[$diff] }}</span>
                        </div>
                        <input type="number" name="difficulties[{{ strtolower($diff) }}]" x-model.number="counts.{{ strtolower($diff) }}" min="0" :max="available.{{ $diff }}" value="{{ old('difficulties.'.strtolower($diff), 0) }}" class="t-diff-input" style="width:100%; padding:10px 12px; border:1.5px solid rgba(0,83,122,0.14); border-radius:10px; background:#fff; color:#013C58; font-size:14px; font-weight:700; text-align:center; outline:none;" required>
                        <p x-show="counts.{{ strtolower($diff) }} > available.{{ $diff }}" x-cloak style="margin:8px 0 0; font-size:10.5px; color:#C2591A; font-weight:700;">أكتر من العدد المتوفر ({{ $available[$diff] }})</p>
                    </div>
                @endforeach
            </div>

            <p x-show="totalCount === 0" x-cloak style="margin:16px 0 0; font-size:12px; color:#C2591A; font-weight:700;"> .</p>
        </div>

        <button type="submit" class="t-submit-btn" :disabled="totalCount === 0">
            إنشاء الاختبار
        </button>
    </form>
</div>

<script>
function levelTestForm(available) {
    return {
        available: available,
        counts: { easy: 0, medium: 0, hard: 0 },
        get totalCount() {
            return (parseInt(this.counts.easy) || 0) + (parseInt(this.counts.medium) || 0) + (parseInt(this.counts.hard) || 0);
        },
    };
}
</script>
@endsection
