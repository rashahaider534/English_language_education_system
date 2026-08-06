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

    .t-pool-row { transition: background 0.15s ease; }
    .t-pool-row:hover { background: rgba(168,232,249,0.12); }

    .t-selected-row { transition: background 0.15s ease; }
    .t-selected-row:hover { background: rgba(255,211,91,0.08); }

    .t-mini-btn { transition: transform 0.15s ease, background 0.15s ease; }
    .t-mini-btn:hover { transform: translateY(-1px); }

    .t-arrow-btn { width:22px; height:18px; border:none; border-radius:5px; background:rgba(0,83,122,0.08); color:#00537A; cursor:pointer; opacity:1; }
    .t-arrow-btn:disabled { opacity:0.3; cursor:not-allowed; }

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
    $typeLabels = ['MCQ' => 'اختيار من متعدد', 'FILL' => 'ملء فراغ', 'ARRANGE' => 'ترتيب كلمات', 'PAIR' => 'توصيل'];
    $typeColors = ['MCQ' => '#0E6A96', 'FILL' => '#8A5A00', 'ARRANGE' => '#2E7D55', 'PAIR' => '#C2591A'];
    $difficultyLabels = ['EASY' => 'سهل', 'MEDIUM' => 'متوسط', 'HARD' => 'صعب'];
    $difficultyColors = ['EASY' => '#2E7D55', 'MEDIUM' => '#8A5A00', 'HARD' => '#C2591A'];

    $statusVal = $test->status?->value ?? $test->status;
    $isPublished = $statusVal === 'published';

    $selectedInit = $test->questions->sortBy(fn($q) => $q->pivot->order)->values()->map(fn($q) => [
        'id' => $q->id,
        'title_en' => $q->title_question_en,
        'title_ar' => $q->title_question_ar,
        'type' => $q->type instanceof \BackedEnum ? $q->type->value : $q->type,
        'difficulty' => $q->difficulty,
        'score' => $q->score,
    ]);

    $poolForJs = $questions->map(fn($q) => [
        'id' => $q->id,
        'title_en' => $q->title_question_en,
        'title_ar' => $q->title_question_ar,
        'type' => $q->type instanceof \BackedEnum ? $q->type->value : $q->type,
        'difficulty' => $q->difficulty,
        'score' => $q->score,
    ])->values();
@endphp
<div
    x-data="placementTestForm(@js($poolForJs), @js($selectedInit))"
    class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8"
    style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl"
>
    <div style="margin-bottom:18px;">
        <a href="{{ route('tests.placement.placement.show', $test) }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            العودة لعرض الاختبار
        </a>
    </div>

    <div style="background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:22px; padding:26px 32px; margin-bottom:22px; box-shadow:0 20px 44px rgba(1,60,88,0.2);">
        <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:22px; color:#fff;">تعديل اختبار تحديد المستوى</h1>
        <p style="margin:6px 0 0; font-size:13px; color:rgba(168,232,249,0.8);">#{{ $test->id }} — {{ $test->title_en }}</p>
    </div>

    @if ($isPublished)
        <div style="display:flex; align-items:flex-start; gap:10px; background:rgba(255,186,66,0.16); color:#8A5A00; border:1px solid rgba(255,186,66,0.4); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13px; font-weight:600; line-height:1.7;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            <span>هالاختبار منشور حاليًا. الحفظ رح ينشئ نسخة جديدة منه (draft) بدل تعديل النسخة المنشورة مباشرة.</span>
        </div>
    @endif

    @if ($errors->any())
        <div style="background:rgba(255,138,101,0.14); color:#C2591A; border:1px solid rgba(255,138,101,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13px; font-weight:600;">
            @foreach ($errors->all() as $error)
                <p style="margin:0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('tests.update', $test) }}" method="POST" @submit="beforeSubmit">
        @csrf

        {{-- ============ TITLE FIELDS ============ --}}
        <div class="t-panel" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:26px; margin-bottom:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
            <h3 style="margin:0 0 18px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">معلومات الاختبار</h3>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">عنوان الاختبار (إنكليزي)</label>
                    <div class="t-field-wrap"><input type="text" name="title_en" value="{{ old('title_en', $test->title_en) }}" required></div>
                </div>
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">عنوان الاختبار (عربي)</label>
                    <div class="t-field-wrap"><input type="text" name="title_ar" value="{{ old('title_ar', $test->title_ar) }}" required></div>
                </div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:22px; align-items:start;">
            {{-- ============ QUESTION POOL ============ --}}
            <div class="t-panel" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:24px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
                <h3 style="margin:0 0 14px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">بنك أسئلة تحديد المستوى</h3>

                <div style="display:flex; gap:8px; margin-bottom:14px;">
                    <div class="t-field-wrap" style="flex:1;"><input type="text" x-model="search" placeholder="بحث بنص السؤال..."></div>
                    <div class="t-field-wrap" style="width:120px;">
                        <select x-model="filterType">
                            <option value="" style="color:#013C58;">كل الأنواع</option>
                            @foreach ($typeLabels as $val => $label)
                                <option value="{{ $val }}" style="color:{{ $typeColors[$val] }};">● {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="t-field-wrap" style="width:110px;">
                        <select x-model="filterDifficulty">
                            <option value="" style="color:#013C58;">كل الصعوبات</option>
                            @foreach ($difficultyLabels as $val => $label)
                                <option value="{{ $val }}" style="color:{{ $difficultyColors[$val] }};">● {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="max-height:460px; overflow-y:auto; display:flex; flex-direction:column; gap:8px;">
                    <template x-for="q in filteredPool" :key="q.id">
                        <div class="t-pool-row" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:11px; background:rgba(0,83,122,0.03);">
                            <div style="flex:1; min-width:0;">
                                <p style="margin:0; font-size:12.5px; font-weight:700; color:#013C58; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" x-text="q.title_en"></p>
                                <p style="margin:2px 0 0; font-size:11px; color:rgba(1,60,88,0.5);"><span x-text="q.type"></span> · <span x-text="q.difficulty"></span> · <span x-text="q.score"></span> نقطة</p>
                            </div>
                            <button type="button" class="t-mini-btn" @click="addQuestion(q)" style="display:flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:9px; border:none; background:rgba(0,83,122,0.08); color:#00537A; cursor:pointer; flex-shrink:0;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"></path></svg>
                            </button>
                        </div>
                    </template>
                    <p x-show="filteredPool.length === 0" x-cloak style="text-align:center; color:rgba(1,60,88,0.4); font-size:12.5px; padding:20px 0;">لا يوجد أسئلة مطابقة</p>
                </div>
            </div>

            {{-- ============ SELECTED / ORDERED ============ --}}
            <div class="t-panel" style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.35); border-radius:20px; padding:24px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
                <h3 style="margin:0 0 6px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">أسئلة الاختبار المختارة</h3>
                <p style="margin:0 0 14px; font-size:11.5px; color:rgba(1,60,88,0.5);">
                    عدد الأسئلة: <span x-text="selected.length" style="font-weight:700;"></span> (يجب  إجابتين على الأقل). الترتيب عبر  الاسهم.
                </p>

                <div style="display:flex; flex-direction:column; gap:8px;">
                    <template x-for="(s, i) in selected" :key="s.id">
                        <div class="t-selected-row" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:11px; background:rgba(255,211,91,0.08);">
                            <span style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:#00537A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:11.5px; flex-shrink:0;" x-text="i+1"></span>
                            <div style="flex:1; min-width:0;">
                                <p style="margin:0; font-size:12.5px; font-weight:700; color:#013C58; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" x-text="s.title_en"></p>
                                <p style="margin:2px 0 0; font-size:11px; color:rgba(1,60,88,0.5);"><span x-text="s.type"></span> · <span x-text="s.difficulty"></span></p>
                            </div>
                            <input type="hidden" :name="'questions['+i+'][id]'" :value="s.id">
                            <input type="hidden" :name="'questions['+i+'][order]'" :value="i+1">
                            <div style="display:flex; flex-direction:column; gap:2px;">
                                <button type="button" class="t-arrow-btn" @click="moveUp(i)" :disabled="i === 0">▲</button>
                                <button type="button" class="t-arrow-btn" @click="moveDown(i)" :disabled="i === selected.length - 1">▼</button>
                            </div>
                            <button type="button" @click="selected.splice(i,1)" style="width:28px; height:28px; border-radius:8px; border:none; background:rgba(229,72,77,0.1); color:#C2591A; cursor:pointer; flex-shrink:0;">×</button>
                        </div>
                    </template>
                    <p x-show="selected.length === 0" x-cloak style="text-align:center; color:rgba(1,60,88,0.4); font-size:12.5px; padding:24px 0;">لسا ما ضفتي أي سؤال</p>
                </div>
            </div>
        </div>

        <div style="margin-top:22px;">
            <p x-show="selected.length > 0 && selected.length < 2" x-cloak style="margin:0 0 12px; font-size:12px; color:#C2591A; font-weight:700;">لازم تختاري إجابتين (سؤالين) على الأقل.</p>
            <button type="submit" class="t-submit-btn" :disabled="selected.length < 2">
                {{ $isPublished ? 'حفظ كنسخة جديدة' : 'حفظ التعديلات' }}
            </button>
        </div>
    </form>
</div>

<script>
function placementTestForm(pool, initSelected) {
    return {
        pool: pool,
        selected: initSelected,
        search: '',
        filterType: '',
        filterDifficulty: '',
        get filteredPool() {
            const selectedIds = this.selected.map(s => s.id);
            return this.pool.filter(q => {
                if (selectedIds.includes(q.id)) return false;
                if (this.filterType && q.type !== this.filterType) return false;
                if (this.filterDifficulty && q.difficulty !== this.filterDifficulty) return false;
                if (this.search && !q.title_en.toLowerCase().includes(this.search.toLowerCase()) && !q.title_ar.includes(this.search)) return false;
                return true;
            });
        },
        addQuestion(q) {
            this.selected.push(q);
        },
        moveUp(i) {
            if (i === 0) return;
            [this.selected[i - 1], this.selected[i]] = [this.selected[i], this.selected[i - 1]];
        },
        moveDown(i) {
            if (i === this.selected.length - 1) return;
            [this.selected[i + 1], this.selected[i]] = [this.selected[i], this.selected[i + 1]];
        },
        beforeSubmit(e) {
            if (this.selected.length < 2) {
                e.preventDefault();
            }
        },
    };
}
</script>
@endsection
