<?php

namespace Database\Seeders;

use App\Models\Word;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\WordStatus;
use Illuminate\Support\Facades\DB;

class WordSeeder extends Seeder
{
    public function run(): void
    {
        $words = [];

        $lessonWords = [

    // =====================================================
    // A1 - Beginner
    // =====================================================

    // Course 1 - English Foundations

    // 1 - The Alphabet and Sounds
    1 => [
        ['en' => 'B', 'ar' => 'ب'],
        ['en' => 'letter', 'ar' => 'حرف'],
        ['en' => 'sound', 'ar' => 'صوت'],
        ['en' => 'vowel', 'ar' => 'حرف متحرك'],
        ['en' => 'consonant', 'ar' => 'حرف ساكن'],
    ],

    // 2 - Introducing Yourself
    2 => [
        ['en' => 'name', 'ar' => 'اسم'],
        ['en' => 'age', 'ar' => 'عمر'],
        ['en' => 'student', 'ar' => 'طالب'],
        ['en' => 'country', 'ar' => 'بلد'],
        ['en' => 'nationality', 'ar' => 'جنسية'],
    ],

    // 3 - Numbers and Basic Information
    3 => [
        ['en' => 'number', 'ar' => 'رقم'],
        ['en' => 'address', 'ar' => 'عنوان'],
        ['en' => 'phone', 'ar' => 'هاتف'],
        ['en' => 'birthday', 'ar' => 'عيد ميلاد'],
        ['en' => 'email', 'ar' => 'بريد إلكتروني'],
    ],


    // =====================================================
    // Course 2 - Basic Grammar
    // =====================================================

    // 4 - Nouns and Articles
    4 => [
        ['en' => 'book', 'ar' => 'كتاب'],
        ['en' => 'apple', 'ar' => 'تفاحة'],
        ['en' => 'teacher', 'ar' => 'معلم'],
        ['en' => 'student', 'ar' => 'طالب'],
        ['en' => 'school', 'ar' => 'مدرسة'],
    ],

    // 5 - Subject Pronouns
    5 => [
        ['en' => 'I', 'ar' => 'أنا'],
        ['en' => 'you', 'ar' => 'أنت'],
        ['en' => 'he', 'ar' => 'هو'],
        ['en' => 'she', 'ar' => 'هي'],
        ['en' => 'they', 'ar' => 'هم'],
    ],

    // 6 - This, That, These and Those
    6 => [
        ['en' => 'this', 'ar' => 'هذا / هذه'],
        ['en' => 'that', 'ar' => 'ذلك / تلك'],
        ['en' => 'these', 'ar' => 'هؤلاء / هذه'],
        ['en' => 'those', 'ar' => 'أولئك / تلك'],
        ['en' => 'object', 'ar' => 'غرض / شيء'],
    ],


    // =====================================================
    // Course 3 - Everyday Vocabulary
    // =====================================================

    // 7 - Family and Friends
    7 => [
        ['en' => 'mother', 'ar' => 'أم'],
        ['en' => 'father', 'ar' => 'أب'],
        ['en' => 'brother', 'ar' => 'أخ'],
        ['en' => 'sister', 'ar' => 'أخت'],
        ['en' => 'friend', 'ar' => 'صديق'],
    ],

    // 8 - Food and Drinks
    8 => [
        ['en' => 'bread', 'ar' => 'خبز'],
        ['en' => 'rice', 'ar' => 'أرز'],
        ['en' => 'water', 'ar' => 'ماء'],
        ['en' => 'milk', 'ar' => 'حليب'],
        ['en' => 'juice', 'ar' => 'عصير'],
    ],

    // 9 - Places Around Us
    9 => [
        ['en' => 'school', 'ar' => 'مدرسة'],
        ['en' => 'hospital', 'ar' => 'مشفى'],
        ['en' => 'market', 'ar' => 'سوق'],
        ['en' => 'park', 'ar' => 'حديقة'],
        ['en' => 'library', 'ar' => 'مكتبة'],
    ],


    // =====================================================
    // Course 4 - Basic Conversation
    // =====================================================

    // 10 - Greetings and Introductions
    10 => [
        ['en' => 'hello', 'ar' => 'مرحبًا'],
        ['en' => 'welcome', 'ar' => 'أهلًا وسهلًا'],
        ['en' => 'goodbye', 'ar' => 'وداعًا'],
        ['en' => 'morning', 'ar' => 'صباح'],
        ['en' => 'nice', 'ar' => 'سعيد / لطيف'],
    ],

    // 11 - Asking and Answering Questions
    11 => [
        ['en' => 'what', 'ar' => 'ماذا / ما'],
        ['en' => 'where', 'ar' => 'أين'],
        ['en' => 'when', 'ar' => 'متى'],
        ['en' => 'why', 'ar' => 'لماذا'],
        ['en' => 'how', 'ar' => 'كيف'],
    ],

    // 12 - Talking About Yourself
    12 => [
        ['en' => 'live', 'ar' => 'يعيش'],
        ['en' => 'work', 'ar' => 'يعمل'],
        ['en' => 'study', 'ar' => 'يدرس'],
        ['en' => 'hobby', 'ar' => 'هواية'],
        ['en' => 'family', 'ar' => 'عائلة'],
    ],


    // =====================================================
    // A2 - Elementary
    // =====================================================

    // Course 5 - Past and Future Tenses

    // 13 - Past Simple in Everyday Life
    13 => [
        ['en' => 'visited', 'ar' => 'زار'],
        ['en' => 'played', 'ar' => 'لعب'],
        ['en' => 'watched', 'ar' => 'شاهد'],
        ['en' => 'worked', 'ar' => 'عمل'],
        ['en' => 'cooked', 'ar' => 'طبخ'],
    ],

    // 14 - Talking About Future Plans
    14 => [
        ['en' => 'plan', 'ar' => 'خطة'],
        ['en' => 'tomorrow', 'ar' => 'غدًا'],
        ['en' => 'travel', 'ar' => 'يسافر'],
        ['en' => 'visit', 'ar' => 'يزور'],
        ['en' => 'future', 'ar' => 'المستقبل'],
    ],

    // 15 - Past and Future Time Expressions
    15 => [
        ['en' => 'yesterday', 'ar' => 'أمس'],
        ['en' => 'last', 'ar' => 'الماضي / السابق'],
        ['en' => 'ago', 'ar' => 'منذ'],
        ['en' => 'tomorrow', 'ar' => 'غدًا'],
        ['en' => 'next', 'ar' => 'القادم'],
    ],


    // =====================================================
    // Course 6 - Practical Vocabulary
    // =====================================================

    // 16 - Shopping and Money
    16 => [
        ['en' => 'price', 'ar' => 'سعر'],
        ['en' => 'cheap', 'ar' => 'رخيص'],
        ['en' => 'expensive', 'ar' => 'غالي'],
        ['en' => 'cash', 'ar' => 'نقدًا'],
        ['en' => 'customer', 'ar' => 'زبون'],
    ],

    // 17 - Travel and Transportation
    17 => [
        ['en' => 'airport', 'ar' => 'مطار'],
        ['en' => 'ticket', 'ar' => 'تذكرة'],
        ['en' => 'train', 'ar' => 'قطار'],
        ['en' => 'station', 'ar' => 'محطة'],
        ['en' => 'bus', 'ar' => 'حافلة'],
    ],

    // 18 - Health and Daily Activities
    18 => [
        ['en' => 'doctor', 'ar' => 'طبيب'],
        ['en' => 'medicine', 'ar' => 'دواء'],
        ['en' => 'exercise', 'ar' => 'تمرين'],
        ['en' => 'sleep', 'ar' => 'نوم'],
        ['en' => 'healthy', 'ar' => 'صحي'],
    ],


    // =====================================================
    // Course 7 - Listening and Understanding
    // =====================================================

    // 19 - Understanding Short Conversations
    19 => [
        ['en' => 'conversation', 'ar' => 'محادثة'],
        ['en' => 'question', 'ar' => 'سؤال'],
        ['en' => 'answer', 'ar' => 'إجابة'],
        ['en' => 'listen', 'ar' => 'يستمع'],
        ['en' => 'speaker', 'ar' => 'متحدث'],
    ],

    // 20 - Listening for Key Information
    20 => [
        ['en' => 'information', 'ar' => 'معلومات'],
        ['en' => 'detail', 'ar' => 'تفصيل'],
        ['en' => 'important', 'ar' => 'مهم'],
        ['en' => 'understand', 'ar' => 'يفهم'],
        ['en' => 'meaning', 'ar' => 'معنى'],
    ],

    // 21 - Understanding Everyday Speech
    21 => [
        ['en' => 'usually', 'ar' => 'عادةً'],
        ['en' => 'sometimes', 'ar' => 'أحيانًا'],
        ['en' => 'really', 'ar' => 'حقًا'],
        ['en' => 'probably', 'ar' => 'على الأرجح'],
        ['en' => 'actually', 'ar' => 'في الواقع'],
    ],


    // =====================================================
    // Course 8 - Everyday English Conversation
    // =====================================================

    // 22 - Making Plans with Friends
    22 => [
        ['en' => 'meet', 'ar' => 'يلتقي'],
        ['en' => 'tonight', 'ar' => 'الليلة'],
        ['en' => 'together', 'ar' => 'معًا'],
        ['en' => 'available', 'ar' => 'متاح'],
        ['en' => 'weekend', 'ar' => 'عطلة نهاية الأسبوع'],
    ],

    // 23 - Ordering Food at a Restaurant
    23 => [
        ['en' => 'menu', 'ar' => 'قائمة الطعام'],
        ['en' => 'order', 'ar' => 'يطلب'],
        ['en' => 'meal', 'ar' => 'وجبة'],
        ['en' => 'waiter', 'ar' => 'نادل'],
        ['en' => 'bill', 'ar' => 'فاتورة'],
    ],

    // 24 - Asking for Directions
    24 => [
        ['en' => 'left', 'ar' => 'يسار'],
        ['en' => 'right', 'ar' => 'يمين'],
        ['en' => 'straight', 'ar' => 'مباشرة'],
        ['en' => 'corner', 'ar' => 'زاوية / منعطف'],
        ['en' => 'near', 'ar' => 'قريب'],
    ],


    // =====================================================
    // B1 - Intermediate
    // =====================================================

    // Course 9 - Intermediate Grammar

    // 25 - Present Perfect Tense
    25 => [
        ['en' => 'already', 'ar' => 'بالفعل'],
        ['en' => 'yet', 'ar' => 'حتى الآن'],
        ['en' => 'ever', 'ar' => 'سبق أن'],
        ['en' => 'never', 'ar' => 'أبدًا'],
        ['en' => 'recently', 'ar' => 'مؤخرًا'],
    ],

    // 26 - First and Second Conditionals
    26 => [
        ['en' => 'condition', 'ar' => 'شرط'],
        ['en' => 'possible', 'ar' => 'ممكن'],
        ['en' => 'result', 'ar' => 'نتيجة'],
        ['en' => 'unless', 'ar' => 'ما لم'],
        ['en' => 'imagine', 'ar' => 'يتخيل'],
    ],

    // 27 - Passive Voice
    27 => [
        ['en' => 'build', 'ar' => 'يبني'],
        ['en' => 'produce', 'ar' => 'ينتج'],
        ['en' => 'create', 'ar' => 'ينشئ'],
        ['en' => 'discover', 'ar' => 'يكتشف'],
        ['en' => 'invent', 'ar' => 'يخترع'],
    ],


    // =====================================================
    // Course 10 - Intermediate Conversation
    // =====================================================

    // 28 - Expressing Opinions
    28 => [
        ['en' => 'opinion', 'ar' => 'رأي'],
        ['en' => 'believe', 'ar' => 'يعتقد'],
        ['en' => 'think', 'ar' => 'يفكر / يعتقد'],
        ['en' => 'feel', 'ar' => 'يشعر / يرى'],
        ['en' => 'prefer', 'ar' => 'يفضل'],
    ],

    // 29 - Agreeing and Disagreeing
    29 => [
        ['en' => 'agree', 'ar' => 'يوافق'],
        ['en' => 'disagree', 'ar' => 'يختلف'],
        ['en' => 'exactly', 'ar' => 'بالضبط'],
        ['en' => 'however', 'ar' => 'ومع ذلك'],
        ['en' => 'perhaps', 'ar' => 'ربما'],
    ],

    // 30 - Discussing Everyday Topics
    30 => [
        ['en' => 'topic', 'ar' => 'موضوع'],
        ['en' => 'discuss', 'ar' => 'يناقش'],
        ['en' => 'experience', 'ar' => 'تجربة'],
        ['en' => 'interesting', 'ar' => 'مثير للاهتمام'],
        ['en' => 'conversation', 'ar' => 'محادثة'],
    ],


    // =====================================================
    // B2 - Upper Intermediate
    // =====================================================

    // Course 11 - Advanced Grammar

    // 31 - Advanced Verb Tenses
    31 => [
        ['en' => 'tense', 'ar' => 'زمن'],
        ['en' => 'continuous', 'ar' => 'مستمر'],
        ['en' => 'perfect', 'ar' => 'تام'],
        ['en' => 'duration', 'ar' => 'مدة'],
        ['en' => 'sequence', 'ar' => 'تسلسل'],
    ],

    // 32 - Advanced Conditional Sentences
    32 => [
        ['en' => 'unless', 'ar' => 'ما لم'],
        ['en' => 'provided', 'ar' => 'بشرط أن'],
        ['en' => 'otherwise', 'ar' => 'وإلا'],
        ['en' => 'consequence', 'ar' => ' نتيجة'],
        ['en' => 'possibility', 'ar' => 'احتمال'],
    ],

    // 33 - Reported Speech
    33 => [
        ['en' => 'report', 'ar' => 'ينقل / يبلغ'],
        ['en' => 'statement', 'ar' => 'تصريح'],
        ['en' => 'mention', 'ar' => 'يذكر'],
        ['en' => 'explain', 'ar' => 'يشرح'],
        ['en' => 'speaker', 'ar' => 'متحدث'],
    ],


    // =====================================================
    // Course 12 - Advanced Conversation
    // =====================================================

    // 34 - Debating and Giving Arguments
    34 => [
        ['en' => 'argument', 'ar' => 'حجة'],
        ['en' => 'debate', 'ar' => 'مناظرة / نقاش'],
        ['en' => 'evidence', 'ar' => 'دليل'],
        ['en' => 'claim', 'ar' => 'ادعاء'],
        ['en' => 'reason', 'ar' => 'سبب'],
    ],

    // 35 - Discussing Complex Topics
    35 => [
        ['en' => 'complex', 'ar' => 'معقد'],
        ['en' => 'issue', 'ar' => 'قضية / مسألة'],
        ['en' => 'solution', 'ar' => 'حل'],
        ['en' => 'impact', 'ar' => 'تأثير'],
        ['en' => 'challenge', 'ar' => 'تحدٍ'],
    ],

    // 36 - Speaking Fluently and Naturally
    36 => [
        ['en' => 'fluently', 'ar' => 'بطلاقة'],
        ['en' => 'naturally', 'ar' => 'بشكل طبيعي'],
        ['en' => 'expression', 'ar' => 'تعبير'],
        ['en' => 'confident', 'ar' => 'واثق'],
        ['en' => 'pronunciation', 'ar' => 'نطق'],
    ],
];

        foreach ($lessonWords as $lessonId => $lessonVocabulary) {

            foreach ($lessonVocabulary as $word) {

                $words[] = [
                    'lesson_id' => $lessonId,
                    'word_en' => $word['en'],
                    'word_ar' => $word['ar'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Word::insert($words);
        $word4 = Word::find(1);
        $word4
            ->addMedia(database_path('seeders/audios/test2.wav'))
            ->preservingOriginal()
            ->toMediaCollection('audios');

        $word5 = Word::find(2);
        $word5
            ->addMedia(database_path('seeders/audios/test2.wav'))
            ->preservingOriginal()
            ->toMediaCollection('audios');

        $user = User::find(2);
        $rows = [];
        foreach (Word::take(8)->get() as $index => $word) {

            $rows[] = [
                'user_id'   => $user->id,
                'word_id'   => $word->id,
                'status'    => $index < 4
                    ? WordStatus::KNOW->value
                    : WordStatus::LEARNING->value,
                'added_at'  => now()->subDays(rand(1, 10)),
            ];
        }
        DB::table('user_words')->insert($rows);
    }
}
