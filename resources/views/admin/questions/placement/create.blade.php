@extends('dashboard.layouts.app')

@push('styles')
<style>
    @keyframes lessonsFadeUp { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
    .q-panel { animation: lessonsFadeUp 0.4s ease both; }

    .q-type-card { transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease; cursor:pointer; }
    .q-type-card:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(1,60,88,0.1); }

    .q-field-wrap { border:1.5px solid rgba(0,83,122,0.14); border-radius:11px; background:#FBFEFF; }
    .q-field-wrap input, .q-field-wrap textarea, .q-field-wrap select { width:100%; background:transparent; border:none; outline:none; padding:11px 13px; font-size:13px; color:#013C58; font-family:'Tajawal',sans-serif; }
    .q-field-wrap input:focus, .q-field-wrap textarea:focus, .q-field-wrap select:focus,
    .q-field-wrap input:focus-visible, .q-field-wrap textarea:focus-visible, .q-field-wrap select:focus-visible {
        outline: none !important;
        box-shadow: none !important;
        border: none !important;
    }
    .q-field-wrap:focus-within {
        border-color: rgba(0,83,122,0.14) !important;
    }

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
<div
    x-data="questionForm(@js(old('type', 'MCQ')), @js(old('difficulty', '')), @js(old('score', '')), @js(old('answers')), @js(old('text_question', '')))"
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
        <p style="margin:6px 0 0; font-size:13px; color:rgba(168,232,249,0.8);">      </p>
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
        @include('admin.questions._quick-form-fields')

        <button type="submit" class="q-submit-btn" style="display:inline-flex; align-items:center; gap:8px; padding:13px 30px; border-radius:12px; border:none; background:linear-gradient(90deg,#F5A201,#FFBA42); color:#013C58; font-family:'Poppins',sans-serif; font-weight:700; font-size:14px; cursor:pointer;">
            حفظ السؤال
        </button>
    </form>
</div>

<script>
function questionForm(initialType, initialDifficulty, initialScore, oldAnswers, oldFillText) {
    initialType = initialType || 'MCQ';
    oldAnswers = oldAnswers || [];

    const mcqDefault = [{ text_answer: '', is_correct: false }, { text_answer: '', is_correct: false }];
    const arrangeDefault = [{ text_answer: '' }, { text_answer: '' }];
    const pairDefault = [{ left_text: '', right_text: '' }, { left_text: '', right_text: '' }];

    return {
        type: initialType,
        difficulty: initialDifficulty || '',
        score: initialScore || '',
        types: [
            { value: 'MCQ', label: 'اختيار من متعدد' },
            { value: 'FILL', label: 'ملء فراغ' },
            { value: 'ARRANGE', label: 'ترتيب كلمات' },
            { value: 'PAIR', label: 'توصيل' },
        ],
        mcqAnswers: (initialType === 'MCQ' && oldAnswers.length)
            ? oldAnswers.map(a => ({ text_answer: a.text_answer || '', is_correct: !!Number(a.is_correct) }))
            : mcqDefault,
        fillText: initialType === 'FILL' ? (oldFillText || '') : '',
        fillAnswers: (initialType === 'FILL' && oldAnswers.length)
            ? oldAnswers.map(a => ({ text_answer: a.text_answer || '', blank_order: Number(a.blank_order) || 1 }))
            : [],
        arrangeAnswers: (initialType === 'ARRANGE' && oldAnswers.length)
            ? oldAnswers.map(a => ({ text_answer: a.text_answer || '' }))
            : arrangeDefault,
        pairAnswers: (initialType === 'PAIR' && oldAnswers.length)
            ? oldAnswers.map(a => ({ left_text: a.left_text || '', right_text: a.right_text || '' }))
            : pairDefault,
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
