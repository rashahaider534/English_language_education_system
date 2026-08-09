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

    .q-type-card { transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease; cursor:pointer; }
    .q-type-card:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(1,60,88,0.1); }
    .q-field-wrap { border:1.5px solid rgba(0,83,122,0.14); border-radius:11px; background:#FBFEFF; }
    .q-field-wrap input, .q-field-wrap textarea, .q-field-wrap select { width:100%; background:transparent; border:none; outline:none; padding:11px 13px; font-size:13px; color:#013C58; font-family:'Tajawal',sans-serif; }
    .q-field-wrap input[type="file"] { padding:7px; font-size:12.5px; color:rgba(1,60,88,0.55); }
    .q-field-wrap input[type="file"]::file-selector-button {
        margin-inline-end:10px; padding:8px 16px; border:none; border-radius:8px;
        background:linear-gradient(90deg,#0E6A96,#146B93); color:#fff; font-family:'Tajawal',sans-serif;
        font-weight:700; font-size:12.5px; cursor:pointer;
    }
    .q-answer-row { transition: background 0.15s ease; }
    .q-add-btn { transition: transform 0.15s ease, background 0.15s ease; }
    .q-add-btn:hover { transform: translateY(-1px); background: rgba(0,83,122,0.12); }
    .q-submit-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .q-submit-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(245,162,1,0.28); }
    .q-submit-btn:disabled { opacity:0.5; cursor:not-allowed; }

    .modal-scroll::-webkit-scrollbar { width: 8px; }
    .modal-scroll::-webkit-scrollbar-track { background: transparent; }
    .modal-scroll::-webkit-scrollbar-thumb { background: rgba(1,60,88,0.14); border-radius: 999px; }
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
            <span>هذا الاختبار منشور حاليًا. الحفظ رح ينشئ نسخة جديدة منه (draft) بدل تعديل النسخة المنشورة مباشرة.</span>
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
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                    <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">بنك أسئلة تحديد المستوى</h3>
                    <button type="button" @click="openQuickAdd()" style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; border:none; background:rgba(0,83,122,0.08); color:#00537A; font-family:'Poppins',sans-serif; font-weight:700; font-size:11.5px; cursor:pointer;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"></path></svg>
                        سؤال جديد
                    </button>
                </div>

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
                            <button type="button" @click="openPreview(q.id)" title="معاينة السؤال" class="t-mini-btn" style="display:flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:9px; border:none; background:rgba(14,106,150,0.1); color:#0E6A96; flex-shrink:0; cursor:pointer;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
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
                            <button type="button" @click="openPreview(s.id)" title="معاينة السؤال" style="display:flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; border:none; background:rgba(14,106,150,0.1); color:#0E6A96; flex-shrink:0; cursor:pointer;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                            <div style="display:flex; flex-direction:column; gap:2px;">
                                <button type="button" class="t-arrow-btn" @click="moveUp(i)" :disabled="i === 0">▲</button>
                                <button type="button" class="t-arrow-btn" @click="moveDown(i)" :disabled="i === selected.length - 1">▼</button>
                            </div>
                            <button type="button" @click="selected.splice(i,1)" style="width:28px; height:28px; border-radius:8px; border:none; background:rgba(229,72,77,0.1); color:#C2591A; cursor:pointer; flex-shrink:0;">×</button>
                        </div>
                    </template>
                    <p x-show="selected.length === 0" x-cloak style="text-align:center; color:rgba(1,60,88,0.4); font-size:12.5px; padding:24px 0;">لم يتم اضافة اي سؤال</p>
                </div>
            </div>
        </div>

        <div style="margin-top:22px;">
            <p x-show="selected.length > 0 && selected.length < 2" x-cloak style="margin:0 0 12px; font-size:12px; color:#C2591A; font-weight:700;">يجب   إجابتين (سؤالين) على الأقل.</p>
            <button type="submit" class="t-submit-btn" :disabled="selected.length < 2">
                {{ $isPublished ? 'حفظ كنسخة جديدة' : 'حفظ التعديلات' }}
            </button>
        </div>
    </form>

    {{-- ============ PREVIEW MODAL ============ --}}
    <div x-show="previewModalOpen" x-cloak class="modal-scroll" style="position:fixed; inset:0; z-index:60; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px); overflow-y:auto;" @click="previewModalOpen = false">
        <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
            <div @click.stop style="width:100%; max-width:560px; max-height:85vh; overflow-y:auto; background:#EFFAFD; border-radius:22px; padding:28px; box-shadow:0 44px 100px rgba(1,42,63,0.4);" dir="rtl">
                <template x-if="previewLoading">
                    <p style="text-align:center; color:rgba(1,60,88,0.5); padding:30px 0;">جاري التحميل...</p>
                </template>
                <template x-if="!previewLoading && previewData">
                    <div>
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                            <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:16px; color:#013C58;" x-text="previewData.title_ar"></h3>
                            <button type="button" @click="previewModalOpen = false" style="width:28px; height:28px; border-radius:50%; border:none; background:rgba(0,83,122,0.06); color:rgba(1,60,88,0.6); cursor:pointer;">×</button>
                        </div>
                        <p style="margin:0 0 16px; font-size:13px; color:rgba(1,60,88,0.6);" x-text="previewData.title_en"></p>
                        <div style="display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap;">
                            <span style="padding:5px 12px; border-radius:999px; background:rgba(14,106,150,0.12); color:#0E6A96; font-size:11.5px; font-weight:700;" x-text="previewData.type"></span>
                            <span style="padding:5px 12px; border-radius:999px; background:rgba(255,186,66,0.16); color:#8A5A00; font-size:11.5px; font-weight:700;" x-text="previewData.difficulty"></span>
                            <span style="padding:5px 12px; border-radius:999px; background:rgba(76,175,120,0.16); color:#2E7D55; font-size:11.5px; font-weight:700;" x-text="previewData.score + ' نقطة'"></span>
                        </div>

                        <template x-if="previewData.image_url">
                            <img :src="previewData.image_url" style="max-width:100%; border-radius:12px; margin-bottom:16px;">
                        </template>
                        <template x-if="previewData.audio_url">
                            <audio :src="previewData.audio_url" controls style="width:100%; margin-bottom:16px;"></audio>
                        </template>
                        <template x-if="previewData.text_question">
                            <p style="background:#FBFEFF; border:1.5px solid rgba(0,83,122,0.14); border-radius:12px; padding:14px; margin-bottom:16px; font-size:13.5px; color:#013C58;" x-text="previewData.text_question"></p>
                        </template>

                        <div style="display:flex; flex-direction:column; gap:8px;">
                            <template x-for="ans in previewData.answers" :key="ans.id">
                                <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 14px; border-radius:11px; background:#FBFEFF; border:1.5px solid rgba(0,83,122,0.1);">
                                    <span style="font-size:13px; color:#013C58;" x-text="ans.text_answer || (ans.left_text ? (ans.left_text + ' → ' + ans.right_text) : '')"></span>
                                    <span x-show="ans.is_correct" style="font-size:11px; font-weight:700; color:#2E7D55;">✓ صحيحة</span>
                                    <span x-show="ans.blank_order" style="font-size:11px; font-weight:700; color:#0E6A96;" x-text="'فراغ {'+ans.blank_order+'}'"></span>
                                    <span x-show="ans.order" style="font-size:11px; font-weight:700; color:#00537A;" x-text="'ترتيب '+ans.order"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ============ QUICK ADD QUESTION MODAL ============ --}}
    <div x-show="quickAddModalOpen" x-cloak class="modal-scroll" style="position:fixed; inset:0; z-index:60; background:rgba(1,42,63,0.5); backdrop-filter:blur(4px); overflow-y:auto;" @click="quickAddModalOpen = false">
        <div style="min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px;">
            <div @click.stop style="width:100%; max-width:640px; max-height:88vh; overflow-y:auto; background:#EFFAFD; border-radius:22px; padding:28px;" dir="rtl">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
                    <h3 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:16px; color:#013C58;">سؤال جديد</h3>
                    <button type="button" @click="quickAddModalOpen = false" style="width:28px; height:28px; border-radius:50%; border:none; background:rgba(0,83,122,0.06); color:rgba(1,60,88,0.6); cursor:pointer;">×</button>
                </div>

                <p x-show="quickAddError" x-cloak x-text="quickAddError" style="background:rgba(255,138,101,0.14); color:#C2591A; border-radius:12px; padding:12px 16px; margin-bottom:16px; font-size:12.5px; font-weight:600;"></p>

                <form @submit.prevent="submitQuickQuestion($event)">
                    @csrf
                    @include('admin.questions._quick-form-fields')
                    <button type="submit" class="q-submit-btn" :disabled="quickAddSaving" style="display:inline-flex; align-items:center; gap:8px; padding:12px 26px; border-radius:12px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:13.5px;">
                        <span x-text="quickAddSaving ? 'جاري الحفظ...' : 'حفظ وإضافة للاختبار'"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function placementTestForm(pool, initSelected) {
    const mcqDefault = [{ text_answer: '', is_correct: false }, { text_answer: '', is_correct: false }];
    const arrangeDefault = [{ text_answer: '' }, { text_answer: '' }];
    const pairDefault = [{ left_text: '', right_text: '' }, { left_text: '', right_text: '' }];

    return {
        pool: pool,
        selected: initSelected,
        search: '',
        filterType: '',
        filterDifficulty: '',

        previewModalOpen: false,
        previewLoading: false,
        previewData: null,

        // ---- quick-add-question modal state (mirrors questionForm() on the full create page) ----
        quickAddModalOpen: false,
        quickAddSaving: false,
        quickAddError: null,
        type: 'MCQ',
        difficulty: '',
        score: '',
        types: [
            { value: 'MCQ', label: 'اختيار من متعدد' },
            { value: 'FILL', label: 'ملء فراغ' },
            { value: 'ARRANGE', label: 'ترتيب كلمات' },
            { value: 'PAIR', label: 'توصيل' },
        ],
        mcqAnswers: [...mcqDefault.map(a => ({...a}))],
        fillText: '',
        fillAnswers: [],
        arrangeAnswers: [...arrangeDefault.map(a => ({...a}))],
        pairAnswers: [...pairDefault.map(a => ({...a}))],
        get scoreMin() {
            return this.difficulty === 'EASY' ? 1 : (this.difficulty === 'MEDIUM' ? 3 : (this.difficulty === 'HARD' ? 6 : 1));
        },
        get scoreMax() {
            return this.difficulty === 'EASY' ? 2 : (this.difficulty === 'MEDIUM' ? 5 : (this.difficulty === 'HARD' ? 10 : 10));
        },
        get scoreHint() {
            return this.difficulty ? '(' + this.scoreMin + '-' + this.scoreMax + ')' : '';
        },
        addBlank() {
            const nextOrder = this.fillAnswers.length + 1;
            this.fillText += ' {' + nextOrder + '}';
            this.fillAnswers.push({ text_answer: '', blank_order: nextOrder });
        },
        resetQuickAddForm() {
            this.type = 'MCQ';
            this.difficulty = '';
            this.score = '';
            this.mcqAnswers = mcqDefault.map(a => ({...a}));
            this.fillText = '';
            this.fillAnswers = [];
            this.arrangeAnswers = arrangeDefault.map(a => ({...a}));
            this.pairAnswers = pairDefault.map(a => ({...a}));
        },

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

        openPreview(id) {
            this.previewModalOpen = true;
            this.previewLoading = true;
            this.previewData = null;
            fetch('/questions/' + id, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => { this.previewData = data; this.previewLoading = false; })
                .catch(() => { this.previewLoading = false; });
        },

        openQuickAdd() {
            this.resetQuickAddForm();
            this.quickAddError = null;
            this.quickAddModalOpen = true;
        },

        submitQuickQuestion(e) {
            this.quickAddSaving = true;
            this.quickAddError = null;
            const formData = new FormData(e.target);
            fetch('{{ route('questions.store') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || formData.get('_token'),
                },
                body: formData,
            })
            .then(async r => {
                if (!r.ok) {
                    const err = await r.json().catch(() => null);
                    const firstError = err?.errors ? Object.values(err.errors)[0][0] : 'يوجد خطا الرجاء التاكد من الحقول ';
                    throw new Error(firstError);
                }
                return r.json();
            })
            .then(newQuestion => {
                this.pool.push(newQuestion);
                this.selected.push(newQuestion);
                this.quickAddSaving = false;
                this.quickAddModalOpen = false;
            })
            .catch(err => {
                this.quickAddSaving = false;
                this.quickAddError = err.message;
            });
        },
    };
}
</script>
@endsection
