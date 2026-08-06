@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .q-panel { animation: lessonsFadeUp 0.4s ease both; }

    .q-field-wrap { border:1.5px solid rgba(0,83,122,0.14); border-radius:11px; background:#FBFEFF; transition:border-color 0.15s ease; }
    .q-field-wrap:focus-within { border-color:#0E6A96; }
    .q-field-wrap input, .q-field-wrap textarea, .q-field-wrap select { width:100%; background:transparent; border:none; outline:none; padding:11px 13px; font-size:13px; color:#013C58; font-family:'Tajawal',sans-serif; }
    .q-field-wrap input[type="file"] { padding:7px; font-size:12.5px; color:rgba(1,60,88,0.55); }
    .q-field-wrap input[type="file"]::file-selector-button {
        margin-inline-end:10px; padding:8px 16px; border:none; border-radius:8px;
        background:linear-gradient(90deg,#0E6A96,#146B93); color:#fff; font-family:'Tajawal',sans-serif;
        font-weight:700; font-size:12.5px; cursor:pointer; transition:transform 0.15s ease, box-shadow 0.15s ease;
    }
    .q-field-wrap input[type="file"]::file-selector-button:hover {
        transform:translateY(-1px); box-shadow:0 6px 14px rgba(14,106,150,0.28);
    }

    .q-answer-row { transition: background 0.15s ease; }
    .q-answer-row:hover { background: rgba(168,232,249,0.08); }

    .q-add-btn { transition: transform 0.15s ease, background 0.15s ease; }
    .q-add-btn:hover { transform: translateY(-1px); background: rgba(0,83,122,0.12); }

    .q-submit-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .q-submit-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(245,162,1,0.28); }
</style>
@endpush

@section('content')
@php
    $questionType = $question->type instanceof \BackedEnum ? $question->type->value : $question->type;
    $typeLabels = ['MCQ' => 'اختيار من متعدد', 'FILL' => 'ملء فراغ', 'ARRANGE' => 'ترتيب كلمات', 'PAIR' => 'توصيل'];
    $relation = $question->getAnswersRelationName();
    $existingAnswers = $question->{$relation};

    $isPublished = $question->isUsedInPublishedTests();
    $isArchived = $question->isUsedInArchivedTests();
    $isClosed = $question->isUsedInClosedTests();
    $willVersion = $isPublished || $isArchived || $isClosed;

    $initMcq = $questionType === 'MCQ'
        ? $existingAnswers->map(fn($a) => ['text_answer' => $a->text_answer, 'is_correct' => (bool) $a->is_correct])->values()
        : collect();
    $initFill = $questionType === 'FILL'
        ? $existingAnswers->sortBy('blank_order')->map(fn($a) => ['text_answer' => $a->text_answer, 'blank_order' => $a->blank_order])->values()
        : collect();
    $initArrange = $questionType === 'ARRANGE'
        ? $existingAnswers->sortBy('order')->map(fn($a) => ['text_answer' => $a->text_answer])->values()
        : collect();
    $initPair = $questionType === 'PAIR'
        ? $existingAnswers->map(fn($a) => ['left_text' => $a->left_text, 'right_text' => $a->right_text])->values()
        : collect();

    $imageUrl = $question->getFirstMediaUrl('image');
    $audioUrl = $question->getFirstMediaUrl('audio');
@endphp
<div
    x-data="questionEditForm(@js($questionType), @js($initMcq), @js($initFill), @js($initArrange), @js($initPair), @js($question->text_question ?? ''), @js($question->difficulty))"
    class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8"
    style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl"
>
    <div style="margin-bottom:18px;">
        <a href="{{ route('questions.show', $question) }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            العودة لعرض السؤال
        </a>
    </div>

    <div style="background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:22px; padding:26px 32px; margin-bottom:22px; box-shadow:0 20px 44px rgba(1,60,88,0.2);">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
            <span style="display:inline-flex; padding:5px 12px; border-radius:999px; background:rgba(255,255,255,0.12); color:#FFD35B; font-size:11px; font-weight:700; border:1px solid rgba(255,255,255,0.18);">#{{ $question->id }}</span>
            <span style="display:inline-flex; padding:5px 12px; border-radius:999px; background:rgba(255,255,255,0.12); color:#A8E8F9; font-size:11px; font-weight:700; border:1px solid rgba(255,255,255,0.18);">{{ $typeLabels[$questionType] ?? $questionType }}</span>
        </div>
        <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:22px; color:#fff;">تعديل السؤال</h1>
        <p style="margin:6px 0 0; font-size:13px; color:rgba(168,232,249,0.8);">نوع السؤال ثابت وما بينتغير بعد الإنشاء.</p>
    </div>

    @if ($willVersion)
        <div style="display:flex; align-items:flex-start; gap:10px; background:rgba(255,186,66,0.16); color:#8A5A00; border:1px solid rgba(255,186,66,0.4); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13px; font-weight:600; line-height:1.7;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            <span>هالسؤال مرتبط بامتحان منشور/مؤرشف/مغلق. الحفظ رح ينشئ نسخة جديدة من السؤال تلقائيًا (مش تعديل مباشر)، وأي اختبارات مرتبطة بالنسخة القديمة وقابلة للربط رح تتحدث للنسخة الجديدة تلقائيًا.</span>
        </div>
    @endif

    @if ($errors->any())
        <div style="background:rgba(255,138,101,0.14); color:#C2591A; border:1px solid rgba(255,138,101,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13px; font-weight:600;">
            @foreach ($errors->all() as $error)
                <p style="margin:0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('questions.update', $question) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- ============ COMMON FIELDS ============ --}}
        <div class="q-panel" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:26px; margin-bottom:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
            <h3 style="margin:0 0 18px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">معلومات السؤال</h3>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">نص السؤال (إنكليزي)</label>
                    <div class="q-field-wrap"><input type="text" name="title_question_en" value="{{ old('title_question_en', $question->title_question_en) }}" required></div>
                </div>
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">نص السؤال (عربي)</label>
                    <div class="q-field-wrap"><input type="text" name="title_question_ar" value="{{ old('title_question_ar', $question->title_question_ar) }}" required></div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">مستوى الصعوبة</label>
                    <div class="q-field-wrap">
                        <select name="difficulty" x-model="difficulty" required>
                            <option value="EASY">سهل (1-2 نقطة)</option>
                            <option value="MEDIUM">متوسط (3-5 نقاط)</option>
                            <option value="HARD">صعب (6-10 نقاط)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">
                        النقاط
                        <span style="color:rgba(1,60,88,0.4); font-weight:600;" x-text="scoreHint"></span>
                    </label>
                    <div class="q-field-wrap"><input type="number" name="score" :min="scoreMin" :max="scoreMax" value="{{ old('score', $question->score) }}" required></div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">صورة (اختياري)</label>
                    @if ($imageUrl)
                        <img src="{{ $imageUrl }}" alt="" style="max-width:100%; max-height:120px; border-radius:10px; margin-bottom:8px; border:1px solid rgba(0,83,122,0.12);">
                    @endif
                    <div class="q-field-wrap"><input type="file" name="image" accept=".jpg,.jpeg,.png"></div>
                    <p style="margin:6px 0 0; font-size:10.5px; color:rgba(1,60,88,0.4);">اتركيه فاضي للاحتفاظ بالصورة الحالية.</p>
                </div>
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">صوت (اختياري)</label>
                    @if ($audioUrl)
                        <audio controls src="{{ $audioUrl }}" style="width:100%; margin-bottom:8px;"></audio>
                    @endif
                    <div class="q-field-wrap"><input type="file" name="audio" accept=".mp3,.wav,.ogg"></div>
                    <p style="margin:6px 0 0; font-size:10.5px; color:rgba(1,60,88,0.4);">اتركيه فاضي للاحتفاظ بالصوت الحالي.</p>
                </div>
            </div>
        </div>

        {{-- ============ MCQ ============ --}}
        @if ($questionType === 'MCQ')
        <div class="q-panel" style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.35); border-radius:20px; padding:26px; margin-bottom:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
            <h3 style="margin:0 0 6px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">إجابات اختيار من متعدد</h3>
            <p style="margin:0 0 16px; font-size:11.5px; color:rgba(1,60,88,0.5);">إجابة واحدة بس صح، وعلى الأقل إجابتين.</p>
            <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:14px;">
                <template x-for="(a, i) in mcqAnswers" :key="i">
                    <div class="q-answer-row" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:11px; background:rgba(0,83,122,0.03);">
                        <input type="radio" name="mcq_correct" @change="mcqAnswers.forEach((x,j)=> x.is_correct = (j===i))" :checked="a.is_correct" style="width:18px; height:18px; accent-color:#0E6A96;">
                        <div class="q-field-wrap" style="flex:1;"><input type="text" x-model="a.text_answer" :name="'answers['+i+'][text_answer]'" placeholder="نص الإجابة" required></div>
                        <input type="hidden" :name="'answers['+i+'][is_correct]'" :value="a.is_correct ? 1 : 0">
                        <button type="button" @click="mcqAnswers.splice(i,1)" style="width:30px; height:30px; border-radius:8px; border:none; background:rgba(229,72,77,0.1); color:#C2591A; cursor:pointer;">×</button>
                    </div>
                </template>
            </div>
            <button type="button" class="q-add-btn" @click="mcqAnswers.push({text_answer:'', is_correct:false})" style="display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:10px; background:rgba(0,83,122,0.07); color:#00537A; border:none; font-family:'Poppins',sans-serif; font-weight:600; font-size:12px; cursor:pointer;">+ إضافة إجابة</button>
        </div>
        @endif

        {{-- ============ FILL ============ --}}
        @if ($questionType === 'FILL')
        <div class="q-panel" style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.35); border-radius:20px; padding:26px; margin-bottom:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
            <h3 style="margin:0 0 6px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">نص السؤال والفراغات</h3>
            <p style="margin:0 0 16px; font-size:11.5px; color:rgba(1,60,88,0.5);">اكتب  نص السؤال و  اضغط على "إضافة فراغ" —    .</p>
            <div class="q-field-wrap" style="margin-bottom:10px;">
                <textarea x-model="fillText" name="text_question" rows="3" required></textarea>
            </div>
            <button type="button" class="q-add-btn" @click="addBlank()" style="display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:10px; background:rgba(255,211,91,0.16); color:#8A5A00; border:none; font-family:'Poppins',sans-serif; font-weight:600; font-size:12px; cursor:pointer; margin-bottom:18px;">+ إضافة فراغ</button>

            <div style="display:flex; flex-direction:column; gap:10px;">
                <template x-for="(a, i) in fillAnswers" :key="i">
                    <div class="q-answer-row" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:11px; background:rgba(0,83,122,0.03);">
                        <span style="display:flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; background:rgba(0,83,122,0.08); color:#00537A; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px; flex-shrink:0;" x-text="'{'+a.blank_order+'}'"></span>
                        <div class="q-field-wrap" style="flex:1;"><input type="text" x-model="a.text_answer" :name="'answers['+i+'][text_answer]'" placeholder="الكلمة الصحيحة لهالفراغ" required></div>
                        <input type="hidden" :name="'answers['+i+'][blank_order]'" :value="a.blank_order">
                    </div>
                </template>
            </div>
        </div>
        @endif

        {{-- ============ ARRANGE ============ --}}
        @if ($questionType === 'ARRANGE')
        <div class="q-panel" style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.35); border-radius:20px; padding:26px; margin-bottom:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
            <h3 style="margin:0 0 6px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">ترتيب الكلمات الصحيح</h3>
            <p style="margin:0 0 16px; font-size:11.5px; color:rgba(1,60,88,0.5);">قم بترتيب الكلمات بالشكل الصحيح من الاعلى للاسفل </p>
            <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:16px;">
                <template x-for="(a, i) in arrangeAnswers" :key="i">
                    <div class="q-answer-row" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:11px; background:rgba(0,83,122,0.03);">
                        <span style="display:flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; background:#00537A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px; flex-shrink:0;" x-text="i+1"></span>
                        <div class="q-field-wrap" style="flex:1;"><input type="text" x-model="a.text_answer" :name="'answers['+i+'][text_answer]'" placeholder="كلمة" required></div>
                        <input type="hidden" :name="'answers['+i+'][is_correct]'" value="1">
                        <input type="hidden" :name="'answers['+i+'][order]'" :value="i+1">
                        <button type="button" @click="arrangeAnswers.splice(i,1)" style="width:30px; height:30px; border-radius:8px; border:none; background:rgba(229,72,77,0.1); color:#C2591A; cursor:pointer;">×</button>
                    </div>
                </template>
            </div>
            <button type="button" class="q-add-btn" @click="arrangeAnswers.push({text_answer:''})" style="display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:10px; background:rgba(0,83,122,0.07); color:#00537A; border:none; font-family:'Poppins',sans-serif; font-weight:600; font-size:12px; cursor:pointer;">+ إضافة كلمة</button>
        </div>
        @endif

        {{-- ============ PAIR ============ --}}
        @if ($questionType === 'PAIR')
        <div class="q-panel" style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.35); border-radius:20px; padding:26px; margin-bottom:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
            <h3 style="margin:0 0 6px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">أزواج التوصيل</h3>
            <p style="margin:0 0 16px; font-size:11.5px; color:rgba(1,60,88,0.5);">كل صف: كلمة عربي وترجمتها الإنكليزية.</p>
            <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:14px;">
                <template x-for="(a, i) in pairAnswers" :key="i">
                    <div class="q-answer-row" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:11px; background:rgba(0,83,122,0.03);">
                        <div class="q-field-wrap" style="flex:1;"><input type="text" x-model="a.left_text" :name="'answers['+i+'][left_text]'" placeholder="كلمة بالعربي" required></div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(1,60,88,0.35)" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg>
                        <div class="q-field-wrap" style="flex:1;"><input type="text" x-model="a.right_text" :name="'answers['+i+'][right_text]'" placeholder="Word in English" required></div>
                        <button type="button" @click="pairAnswers.splice(i,1)" style="width:30px; height:30px; border-radius:8px; border:none; background:rgba(229,72,77,0.1); color:#C2591A; cursor:pointer;">×</button>
                    </div>
                </template>
            </div>
            <button type="button" class="q-add-btn" @click="pairAnswers.push({left_text:'', right_text:''})" style="display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:10px; background:rgba(0,83,122,0.07); color:#00537A; border:none; font-family:'Poppins',sans-serif; font-weight:600; font-size:12px; cursor:pointer;">+ إضافة زوج</button>
        </div>
        @endif

        <button type="submit" class="q-submit-btn" style="display:inline-flex; align-items:center; gap:8px; padding:13px 30px; border-radius:12px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; cursor:pointer;">
            {{ $willVersion ? 'حفظ كنسخة جديدة' : 'حفظ التعديلات' }}
        </button>
    </form>
</div>

<script>
function questionEditForm(type, initMcq, initFill, initArrange, initPair, initFillText, initDifficulty) {
    return {
        type: type,
        difficulty: initDifficulty,
        mcqAnswers: initMcq.length ? initMcq : [{ text_answer: '', is_correct: false }, { text_answer: '', is_correct: false }],
        fillText: initFillText,
        fillAnswers: initFill,
        arrangeAnswers: initArrange.length ? initArrange : [{ text_answer: '' }, { text_answer: '' }],
        pairAnswers: initPair.length ? initPair : [{ left_text: '', right_text: '' }, { left_text: '', right_text: '' }],
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
    };
}
</script>
@endsection
