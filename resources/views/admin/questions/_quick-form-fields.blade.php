{{-- Reusable question form fields. Must be rendered inside an element whose
     Alpine scope exposes: type, types, difficulty, score, scoreMin, scoreMax,
     scoreHint, mcqAnswers, fillText, fillAnswers, arrangeAnswers, pairAnswers,
     addBlank(). Used by admin.questions.placement.create's own questionForm(),
     and by the quick-add-question modal state in the placement test
     create/edit pages. --}}

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
            <div class="q-field-wrap"><input type="text" name="title_question_ar" placeholder=":  ماهو الاسم؟" required></div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
        <div>
            <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">مستوى الصعوبة</label>
            <div class="q-field-wrap">
                <select name="difficulty" x-model="difficulty" @change="score = ''" required>
                    <option value="" style="color:#013C58;">اختاري...</option>
                    <option value="EASY" style="color:#2E7D55;">● سهل (1-2 نقطة)</option>
                    <option value="MEDIUM" style="color:#8A5A00;">● متوسط (3-5 نقاط)</option>
                    <option value="HARD" style="color:#C2591A;">● صعب (6-10 نقاط)</option>
                </select>
            </div>
        </div>
        <div>
            <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">
                النقاط
                <span x-show="difficulty" style="color:rgba(1,60,88,0.4); font-weight:600;" x-text="scoreHint"></span>
            </label>
            <div class="q-field-wrap"><input type="number" name="score" x-model.number="score" :min="scoreMin" :max="scoreMax" required></div>
        </div>
    </div>

    <div x-data="{ imageFileName: '', audioFileName: '' }" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div>
            <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">صورة (اختياري)</label>
            <div class="q-field-wrap" style="display:flex; align-items:center; gap:4px; padding-left:6px;">
                <input type="file" name="image" x-ref="imageInput" accept=".jpg,.jpeg,.png" style="flex:1;"
                    @change="imageFileName = $event.target.files[0]?.name || ''; if (imageFileName) { audioFileName = ''; $refs.audioInput.value = ''; }">
                <button type="button" x-show="imageFileName" x-cloak @click="imageFileName = ''; $refs.imageInput.value = ''" title="إزالة الصورة"
                    style="display:flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:7px; border:none; background:rgba(229,72,77,0.12); color:#C2591A; font-size:13px; font-weight:700; cursor:pointer; flex-shrink:0;">×</button>
            </div>
        </div>
        <div>
            <label style="display:block; font-size:11.5px; font-weight:700; color:rgba(1,60,88,0.55); margin-bottom:6px;">صوت (اختياري)</label>
            <div class="q-field-wrap" style="display:flex; align-items:center; gap:4px; padding-left:6px;">
                <input type="file" name="audio" x-ref="audioInput" accept=".mp3,.wav,.ogg" style="flex:1;"
                    @change="audioFileName = $event.target.files[0]?.name || ''; if (audioFileName) { imageFileName = ''; $refs.imageInput.value = ''; }">
                <button type="button" x-show="audioFileName" x-cloak @click="audioFileName = ''; $refs.audioInput.value = ''" title="إزالة الصوت"
                    style="display:flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:7px; border:none; background:rgba(229,72,77,0.12); color:#C2591A; font-size:13px; font-weight:700; cursor:pointer; flex-shrink:0;">×</button>
            </div>
        </div>
    </div>
    <p style="margin:8px 0 0; font-size:11px; color:rgba(1,60,88,0.45);">صورة أو صوت، وليس الاثنين  معا .</p>
</div>

{{-- ============ MCQ ============ --}}
<div class="q-panel" x-show="type === 'MCQ'" x-cloak style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.35); border-radius:20px; padding:26px; margin-bottom:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
    <h3 style="margin:0 0 6px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">إجابات اختيار من متعدد</h3>
    <p style="margin:0 0 16px; font-size:11.5px; color:rgba(1,60,88,0.5);">إجابة واحدة بس صح، وعلى الأقل إجابتين.</p>
    <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:14px;">
        <template x-for="(a, i) in mcqAnswers" :key="i">
            <div class="q-answer-row" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:11px; background:rgba(0,83,122,0.03);">
                <input type="radio" :name="'mcq_correct'" @change="mcqAnswers.forEach((x,j)=> x.is_correct = (j===i))" :checked="a.is_correct" style="width:18px; height:18px; accent-color:#0E6A96;">
                <div class="q-field-wrap" style="flex:1;"><input type="text" x-model="a.text_answer" :name="type === 'MCQ' ? ('answers['+i+'][text_answer]') : null" placeholder="نص الإجابة" :required="type === 'MCQ'"></div>
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
    <p style="margin:0 0 16px; font-size:11.5px; color:rgba(1,60,88,0.5);">اكتب نص السؤال، وضع  المؤشر مكان الفراغ واكبس "إضافة فراغ" —  سوف يتم وضع  الرقم تلقائي.</p>
    <div class="q-field-wrap" style="margin-bottom:10px;">
        <textarea x-model="fillText" name="text_question" rows="3" placeholder="e.g. there {1} a cat over {2}" :required="type === 'FILL'"></textarea>
    </div>
    <button type="button" class="q-add-btn" @click="addBlank()" style="display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:10px; background:rgba(255,211,91,0.16); color:#8A5A00; border:none; font-family:'Poppins',sans-serif; font-weight:600; font-size:12px; cursor:pointer; margin-bottom:18px;">+ إضافة فراغ</button>

    <div style="display:flex; flex-direction:column; gap:10px;">
        <template x-for="(a, i) in fillAnswers" :key="i">
            <div class="q-answer-row" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:11px; background:rgba(0,83,122,0.03);">
                <span style="display:flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; background:rgba(0,83,122,0.08); color:#00537A; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px; flex-shrink:0;" x-text="'{'+a.blank_order+'}'"></span>
                <div class="q-field-wrap" style="flex:1;"><input type="text" x-model="a.text_answer" :name="type === 'FILL' ? ('answers['+i+'][text_answer]') : null" placeholder="الكلمة الصحيحة لهالفراغ" :required="type === 'FILL'"></div>
                <input type="hidden" :name="type === 'FILL' ? ('answers['+i+'][blank_order]') : null" :value="a.blank_order">
            </div>
        </template>
    </div>
</div>

{{-- ============ ARRANGE ============ --}}
<div class="q-panel" x-show="type === 'ARRANGE'" x-cloak style="background:#EFFAFD; border:1.5px solid rgba(14,106,150,0.35); border-radius:20px; padding:26px; margin-bottom:22px; box-shadow:0 10px 26px rgba(0,83,122,0.06);">
    <h3 style="margin:0 0 6px; font-family:'Poppins',sans-serif; font-weight:800; font-size:14px; color:#013C58;">ترتيب الكلمات الصحيح</h3>
    <p style="margin:0 0 16px; font-size:11.5px; color:rgba(1,60,88,0.5);">قم بترتيب الكلمات بالشكل الصحيح من الاعلى للاسفل </p>
    <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:16px;">
        <template x-for="(a, i) in arrangeAnswers" :key="i">
            <div class="q-answer-row" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:11px; background:rgba(0,83,122,0.03);">
                <span style="display:flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; background:#00537A; color:#fff; font-family:'Poppins',sans-serif; font-weight:700; font-size:12px; flex-shrink:0;" x-text="i+1"></span>
                <div class="q-field-wrap" style="flex:1;"><input type="text" x-model="a.text_answer" :name="type === 'ARRANGE' ? ('answers['+i+'][text_answer]') : null" placeholder="كلمة" :required="type === 'ARRANGE'"></div>
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
                <div class="q-field-wrap" style="flex:1;"><input type="text" x-model="a.left_text" :name="type === 'PAIR' ? ('answers['+i+'][left_text]') : null" placeholder="كلمة بالعربي" :required="type === 'PAIR'"></div>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(1,60,88,0.35)" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg>
                <div class="q-field-wrap" style="flex:1;"><input type="text" x-model="a.right_text" :name="type === 'PAIR' ? ('answers['+i+'][right_text]') : null" placeholder="Word in English" :required="type === 'PAIR'"></div>
                <button type="button" @click="pairAnswers.splice(i,1)" style="width:30px; height:30px; border-radius:8px; border:none; background:rgba(229,72,77,0.1); color:#C2591A; cursor:pointer;">×</button>
            </div>
        </template>
    </div>
    <button type="button" class="q-add-btn" @click="pairAnswers.push({left_text:'', right_text:''})" style="display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:10px; background:rgba(0,83,122,0.07); color:#00537A; border:none; font-family:'Poppins',sans-serif; font-weight:600; font-size:12px; cursor:pointer;">+ إضافة زوج</button>
</div>
