@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .q-panel { animation: lessonsFadeUp 0.4s ease both; }

    .q-type-card { transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease; cursor:pointer; }
    .q-type-card:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(1,60,88,0.1); }

    .q-field-wrap { border:1.5px solid rgba(0,83,122,0.14); border-radius:11px; background:#FBFEFF; transition:border-color 0.15s ease; }
    .q-field-wrap:focus-within { border-color:#0E6A96; }
    .q-field-wrap input, .q-field-wrap textarea, .q-field-wrap select { width:100%; background:transparent; border:none; outline:none; padding:11px 13px; font-size:13px; color:#013C58; font-family:'Tajawal',sans-serif; }

    .q-answer-row { transition: background 0.15s ease; }
    .q-answer-row:hover { background: rgba(168,232,249,0.08); }

    .q-add-btn { transition: transform 0.15s ease, background 0.15s ease; }
    .q-add-btn:hover { transform: translateY(-1px); background: rgba(0,83,122,0.12); }

    .q-submit-btn { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .q-submit-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(245,162,1,0.28); }
</style>
@endpush

@section('content')
<div
    x-data="questionForm()"
    class="-mx-4 -my-6 px-4 py-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8"
    style="background:#DFF2F9; font-family:'Tajawal',sans-serif; min-height:100vh;" dir="rtl"
>
    <div style="margin-bottom:18px;">
        <a href="{{ route('questions.placement.index') }}" style="display:inline-flex; align-items:center; gap:6px; color:#00537A; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 19-7-7 7-7"></path></svg>
            العودة لبنك الأسئلة
        </a>
    </div>

    <div style="background:linear-gradient(135deg,#013C58 0%, #00537A 60%, #0E6A96 130%); border-radius:22px; padding:26px 32px; margin-bottom:22px; box-shadow:0 20px 44px rgba(1,60,88,0.2);">
        <h1 style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:22px; color:#fff;">سؤال جديد لبنك اختبار تحديد المستوى</h1>
        <p style="margin:6px 0 0; font-size:13px; color:rgba(168,232,249,0.8);">اختاري نوع السؤال أول، وبيتغير شكل الفورم حسب النوع</p>
    </div>

    @if ($errors->any())
        <div style="background:rgba(255,138,101,0.14); color:#C2591A; border:1px solid rgba(255,138,101,0.3); border-radius:14px; padding:14px 18px; margin-bottom:20px; font-size:13px; font-weight:600;">
            @foreach ($errors->all() as $error)
                <p style="margin:0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('questions.store') }}" method="POST" enctype="multipart/form-data" @submit="beforeSubmit">
        @csrf
        <input type="hidden" name="type" :value="type">

        {{-- ============ TYPE PICKER ============ --}}
        <div class="q-panel" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:14px; margin-bottom:22px;">
            <template x-for="t in types" :key="t.value">
                <div class="q-type-card" @click="type = t.value" :style="type === t.value
                    ? 'background:#EFFAFD; border:2px solid #0E6A96; border-radius:16px; padding:18px; text-align:center; box-shadow:0 0 16px rgba(14,106,150,0.2);'
                    : 'background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:16px; padding:18px; text-align:center;'">
                    <p style="margin:0; font-family:'Poppins',sans-serif; font-weight:800; font-size:13.5px; color:#013C58;" x-text="t.label"></p>
                </div>
            </template>
        </div>

        {{-- ============ COMMON FIELDS ============ --}}
        <div class="q-panel" style="background:#EFFAFD; border:1.5px solid rgba(0,83,122,0.16); border-radius:20px; padding:26px; margin-bottom:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
            <h3 style="margin:0 0 18px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">معلومات السؤال</h3>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">نص السؤال (إنكليزي)</label>
                    <div class="q-field-wrap"><input type="text" name="title_question_en" placeholder="e.g. What is a noun?" required></div>
                </div>
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">نص السؤال (عربي)</label>
                    <div class="q-field-wrap"><input type="text" name="title_question_ar" placeholder="مثال: شو هو الاسم؟" required></div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">مستوى الصعوبة</label>
                    <div class="q-field-wrap">
                        <select name="difficulty" x-model="difficulty" required>
                            <option value="">اختاري...</option>
                            <option value="EASY">سهل (1-2 نقطة)</option>
                            <option value="MEDIUM">متوسط (3-5 نقاط)</option>
                            <option value="HARD">صعب (6-10 نقاط)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">
                        النقاط
                        <span x-show="difficulty" style="color:rgba(1,60,88,0.4); font-weight:600;" x-text="scoreHint"></span>
                    </label>
                    <div class="q-field-wrap"><input type="number" name="score" :min="scoreMin" :max="scoreMax" required></div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">صورة (اختياري)</label>
                    <div class="q-field-wrap"><input type="file" name="image" accept=".jpg,.jpeg,.png" @change="if($event.target.files.length) audioCleared = true"></div>
                </div>
                <div>
                    <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">صوت (اختياري)</label>
                    <div class="q-field-wrap"><input type="file" name="audio" accept=".mp3,.wav,.ogg" @change="if($event.target.files.length) imageCleared = true"></div>
                </div>
            </div>
            <p style="margin:8px 0 0; font-size:11px; color:rgba(1,60,88,0.45);">صورة أو صوت، مش الاثنين مع بعض.</p>
        </div>

        {{-- ============ MCQ ============ --}}
        <div class="q-panel" x-show="type === 'MCQ'" x-cloak style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.35); border-radius:20px; padding:26px; margin-bottom:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
            <h3 style="margin:0 0 6px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">إجابات اختيار من متعدد</h3>
            <p style="margin:0 0 16px; font-size:11.5px; color:rgba(1,60,88,0.5);">إجابة واحدة بس صح، وعلى الأقل إجابتين.</p>
            <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:14px;">
                <template x-for="(a, i) in mcqAnswers" :key="i">
                    <div class="q-answer-row" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:11px; background:rgba(0,83,122,0.03);">
                        <input type="radio" :name="'mcq_correct'" @change="mcqAnswers.forEach((x,j)=> x.is_correct = (j===i))" :checked="a.is_correct" style="width:18px; height:18px; accent-color:#0E6A96;">
                        <div class="q-field-wrap" style="flex:1;"><input type="text" x-model="a.text_answer" :name="type === 'MCQ' ? ('answers['+i+'][text_answer]') : null" placeholder="نص الإجابة" required></div>
                        <input type="hidden" :name="type === 'MCQ' ? ('answers['+i+'][is_correct]') : null" :value="a.is_correct ? 1 : 0">
                        <button type="button" @click="mcqAnswers.splice(i,1)" style="width:30px; height:30px; border-radius:8px; border:none; background:rgba(229,72,77,0.1); color:#C2591A; cursor:pointer;">×</button>
                    </div>
                </template>
            </div>
            <button type="button" class="q-add-btn" @click="mcqAnswers.push({text_answer:'', is_correct:false})" style="display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:10px; background:rgba(0,83,122,0.07); color:#00537A; border:none; font-family:'Poppins',sans-serif; font-weight:600; font-size:12px; cursor:pointer;">+ إضافة إجابة</button>
        </div>

        {{-- ============ FILL ============ --}}
        <div class="q-panel" x-show="type === 'FILL'" x-cloak style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.35); border-radius:20px; padding:26px; margin-bottom:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
            <h3 style="margin:0 0 6px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">نص السؤال والفراغات</h3>
            <p style="margin:0 0 16px; font-size:11.5px; color:rgba(1,60,88,0.5);">اكتبي نص السؤال، وحطي المؤشر مكان الفراغ واكبسي "إضافة فراغ" — رح يتحط الرقم تلقائي.</p>
            <div class="q-field-wrap" style="margin-bottom:10px;">
                <textarea x-ref="fillText" x-model="fillText" name="text_question" rows="3" placeholder="e.g. there {1} a cat over {2}" required></textarea>
            </div>
            <button type="button" class="q-add-btn" @click="addBlank()" style="display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:10px; background:rgba(255,211,91,0.16); color:#8A5A00; border:none; font-family:'Poppins',sans-serif; font-weight:600; font-size:12px; cursor:pointer; margin-bottom:18px;">+ إضافة فراغ</button>

            <div style="display:flex; flex-direction:column; gap:10px;">
                <template x-for="(a, i) in fillAnswers" :key="i">
                    <div class="q-answer-row" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:11px; background:rgba(0,83,122,0.03);">
                        <span style="display:flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; background:rgba(0,83,122,0.08); color:#00537A; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px; flex-shrink:0;" x-text="'{'+a.blank_order+'}'"></span>
                        <div class="q-field-wrap" style="flex:1;"><input type="text" x-model="a.text_answer" :name="type === 'FILL' ? ('answers['+i+'][text_answer]') : null" placeholder="الكلمة الصحيحة لهالفراغ" required></div>
                        <input type="hidden" :name="type === 'FILL' ? ('answers['+i+'][blank_order]') : null" :value="a.blank_order">
                    </div>
                </template>
            </div>
        </div>

        {{-- ============ ARRANGE ============ --}}
        <div class="q-panel" x-show="type === 'ARRANGE'" x-cloak style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.35); border-radius:20px; padding:26px; margin-bottom:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
            <h3 style="margin:0 0 6px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">ترتيب الكلمات الصحيح</h3>
            <p style="margin:0 0 16px; font-size:11.5px; color:rgba(1,60,88,0.5);">رتبي الكلمات بالترتيب الصح (من فوق لتحت)، الرقم بينحط تلقائي.</p>
            <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:16px;">
                <template x-for="(a, i) in arrangeAnswers" :key="i">
                    <div class="q-answer-row" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:11px; background:rgba(0,83,122,0.03);">
                        <span style="display:flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; background:#00537A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px; flex-shrink:0;" x-text="i+1"></span>
                        <div class="q-field-wrap" style="flex:1;"><input type="text" x-model="a.text_answer" :name="type === 'ARRANGE' ? ('answers['+i+'][text_answer]') : null" placeholder="كلمة" required></div>
                        <input type="hidden" :name="type === 'ARRANGE' ? ('answers['+i+'][is_correct]') : null" value="1">
                        <input type="hidden" :name="type === 'ARRANGE' ? ('answers['+i+'][order]') : null" :value="i+1">
                        <button type="button" @click="arrangeAnswers.splice(i,1)" style="width:30px; height:30px; border-radius:8px; border:none; background:rgba(229,72,77,0.1); color:#C2591A; cursor:pointer;">×</button>
                    </div>
                </template>
            </div>
            <button type="button" class="q-add-btn" @click="arrangeAnswers.push({text_answer:''})" style="display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:10px; background:rgba(0,83,122,0.07); color:#00537A; border:none; font-family:'Poppins',sans-serif; font-weight:600; font-size:12px; cursor:pointer;">+ إضافة كلمة</button>
        </div>

        {{-- ============ PAIR ============ --}}
        <div class="q-panel" x-show="type === 'PAIR'" x-cloak style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.35); border-radius:20px; padding:26px; margin-bottom:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
            <h3 style="margin:0 0 6px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">أزواج التوصيل</h3>
            <p style="margin:0 0 16px; font-size:11.5px; color:rgba(1,60,88,0.5);">كل صف: كلمة عربي وترجمتها الإنكليزية.</p>
            <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:14px;">
                <template x-for="(a, i) in pairAnswers" :key="i">
                    <div class="q-answer-row" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:11px; background:rgba(0,83,122,0.03);">
                        <div class="q-field-wrap" style="flex:1;"><input type="text" x-model="a.left_text" :name="type === 'PAIR' ? ('answers['+i+'][left_text]') : null" placeholder="كلمة بالعربي" required></div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(1,60,88,0.35)" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg>
                        <div class="q-field-wrap" style="flex:1;"><input type="text" x-model="a.right_text" :name="type === 'PAIR' ? ('answers['+i+'][right_text]') : null" placeholder="Word in English" required></div>
                        <button type="button" @click="pairAnswers.splice(i,1)" style="width:30px; height:30px; border-radius:8px; border:none; background:rgba(229,72,77,0.1); color:#C2591A; cursor:pointer;">×</button>
                    </div>
                </template>
            </div>
            <button type="button" class="q-add-btn" @click="pairAnswers.push({left_text:'', right_text:''})" style="display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:10px; background:rgba(0,83,122,0.07); color:#00537A; border:none; font-family:'Poppins',sans-serif; font-weight:600; font-size:12px; cursor:pointer;">+ إضافة زوج</button>
        </div>

        <button type="submit" class="q-submit-btn" style="display:inline-flex; align-items:center; gap:8px; padding:13px 30px; border-radius:12px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; cursor:pointer;">
            حفظ السؤال
        </button>
    </form>
</div>

<script>
function questionForm() {
    return {
        type: 'MCQ',
        difficulty: '',
        types: [
            { value: 'MCQ', label: 'اختيار من متعدد' },
            { value: 'FILL', label: 'ملء فراغ' },
            { value: 'ARRANGE', label: 'ترتيب كلمات' },
            { value: 'PAIR', label: 'توصيل' },
        ],
        mcqAnswers: [{ text_answer: '', is_correct: false }, { text_answer: '', is_correct: false }],
        fillText: '',
        fillAnswers: [],
        arrangeAnswers: [{ text_answer: '' }, { text_answer: '' }],
        pairAnswers: [{ left_text: '', right_text: '' }, { left_text: '', right_text: '' }],
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
        beforeSubmit() {
            // nothing extra needed; each type-specific block only submits its own named inputs while visible in DOM
        },
    };
}
</script>
@endsection
