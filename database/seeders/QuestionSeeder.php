<?php

namespace Database\Seeders;

use App\Enums\QuestionType;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * لكل درس (id من 1 لـ 36) بنولد 4 أسئلة: MCQ, FILL, ARRANGE, PAIR
     * بحيث تصير جاهزين لما نعمل TestSeeder ونربط كل اختبار بـ 4 أسئلة.
     *
     * التعديلات المطبقة حسب قواعد المشروع:
     * - FILL: الفراغات بصيغة {1}, {2}... جوا text_question (مش ---)،
     *   وبعض الأسئلة فيها أكتر من إجابة مقبولة لنفس blank_order
     *   (مثلاً don't و do not لنفس الفراغ).
     * - ARRANGE: كل سؤال فيه كلمات صحيحة (is_correct = true) مرقمة بشكل
     *   متسلسل من 1 بحقل order، بالإضافة لكلمة تشتيت وحدة على الأقل
     *   (is_correct = false) بدون أي قيمة بحقل order (null)، تماشياً
     *   مع قاعدة "ممنوع يكون عندها order إذا كانت للتشتيت".
     *
     * ملاحظات عامة:
     * - استخدمت $question->getAnswersRelationName() (نفس آلية QuestionService)
     *   بدل ما افترض اسم العلاقة يدوياً.
     * - كل الأسئلة is_placement_question = false و previous_question_id = null
     *   لأنها أسئلة دروس عادية مو أسئلة تصنيف.
     * - user_id للسؤال = teacher_id الخاص بصاحب الكورس يلي فيه الدرس (نفس
     *   المعلمين المستخدمين بـ CourseSeeder).
     * - افترضت إنو قيم enum لـ QuestionType هي MCQ / FILL / ARRANGE / PAIR
     *   (uppercase)، تأكدي منها بمشروعك قبل التشغيل.
     */
    public function run(): void
    {
        $difficultyScore = [
            'EASY'   => 2,
            'MEDIUM' => 4,
            'HARD'   => 7,
        ];

        // lesson_id => teacher_id (نفس المعلمين المستخدمين بـ CourseSeeder لكل كورس)
        // lesson_id => teacher_id (نفس المعلمين الحقيقيين المستخدمين بـ CourseSeeder لكل كورس)
        $lessonTeacher = [];
        foreach (range(1, 3)   as $id) $lessonTeacher[$id] = 6;  // Course 1  - English Foundations
        foreach (range(4, 6)   as $id) $lessonTeacher[$id] = 7;  // Course 2  - Basic Grammar
        foreach (range(7, 9)   as $id) $lessonTeacher[$id] = 8;  // Course 3  - Everyday Vocabulary
        foreach (range(10, 12) as $id) $lessonTeacher[$id] = 6;  // Course 4  - Basic Conversation
        foreach (range(13, 15) as $id) $lessonTeacher[$id] = 6;  // Course 5  - Past and Future Tenses
        foreach (range(16, 18) as $id) $lessonTeacher[$id] = 7;  // Course 6  - Practical Vocabulary
        foreach (range(19, 21) as $id) $lessonTeacher[$id] = 8;  // Course 7  - Listening and Understanding
        foreach (range(22, 24) as $id) $lessonTeacher[$id] = 7;  // Course 8  - Everyday English Conversation
        foreach (range(25, 27) as $id) $lessonTeacher[$id] = 6;  // Course 9  - Intermediate Grammar
        foreach (range(28, 30) as $id) $lessonTeacher[$id] = 7;  // Course 10 - Intermediate Conversation
        foreach (range(31, 33) as $id) $lessonTeacher[$id] = 6;  // Course 11 - Advanced Grammar
        foreach (range(34, 36) as $id) $lessonTeacher[$id] = 8;  // Course 12 - Advanced Conversation

        // مستوى الصعوبة حسب مستوى الدرس (A1 أسهل ... B2 أصعب)
        $lessonDifficulty = [
            1 => 'EASY', 2 => 'EASY', 3 => 'EASY', 4 => 'EASY', 5 => 'EASY', 6 => 'EASY',
            7 => 'EASY', 8 => 'EASY', 9 => 'EASY', 10 => 'EASY', 11 => 'EASY', 12 => 'EASY',
            13 => 'EASY', 14 => 'MEDIUM', 15 => 'EASY', 16 => 'MEDIUM', 17 => 'EASY', 18 => 'MEDIUM',
            19 => 'EASY', 20 => 'MEDIUM', 21 => 'EASY', 22 => 'MEDIUM', 23 => 'EASY', 24 => 'MEDIUM',
            25 => 'MEDIUM', 26 => 'MEDIUM', 27 => 'MEDIUM', 28 => 'MEDIUM', 29 => 'MEDIUM', 30 => 'MEDIUM',
            31 => 'MEDIUM', 32 => 'HARD', 33 => 'MEDIUM', 34 => 'HARD', 35 => 'MEDIUM', 36 => 'HARD',
        ];

        $data = $this->questionsData();

        foreach ($data as $lessonId => $set) {
            $userId     = $lessonTeacher[$lessonId];
            $difficulty = $lessonDifficulty[$lessonId];
            $score      = $difficultyScore[$difficulty];

            $this->createMcq($set['mcq'], $userId, $difficulty, $score);
            $this->createFill($set['fill'], $userId, $difficulty, $score);
            $this->createArrange($set['arrange'], $userId, $difficulty, $score);
            $this->createPair($set['pair'], $userId, $difficulty, $score);
        }
    }

    private function baseAttributes(
        int $userId,
        string $type,
        string $titleEn,
        string $titleAr,
        string $difficulty,
        int $score,
        ?string $textQuestion = null
    ): array {
        return [
            'user_id'                => $userId,
            'type'                   => $type,
            'score'                  => $score,
            'title_question_en'      => $titleEn,
            'title_question_ar'      => $titleAr,
            'text_question'          => $textQuestion,
            'difficulty'             => $difficulty,
            'previous_question_id'   => null,
            'is_placement_question'  => false,
        ];
    }

    private function createMcq(array $q, int $userId, string $difficulty, int $score): void
    {
        $question = Question::create($this->baseAttributes(
            $userId, QuestionType::MCQ->value, $q['en'], $q['ar'], $difficulty, $score
        ));

        $answers = array_map(fn ($opt) => [
            'text_answer' => $opt['en'],
            'is_correct'  => $opt['correct'],
        ], $q['options']);

        $question->{$question->getAnswersRelationName()}()->createMany($answers);
    }

    private function createFill(array $q, int $userId, string $difficulty, int $score): void
    {
        $question = Question::create($this->baseAttributes(
            $userId, QuestionType::FILL->value, $q['en'], $q['ar'], $difficulty, $score, $q['text']
        ));

        // ممكن يكون في أكتر من إجابة مقبولة لنفس blank_order (مثلاً don't / do not)
        $answers = array_map(fn ($a) => [
            'text_answer' => $a['text'],
            'blank_order' => $a['blank_order'],
        ], $q['answers']);

        $question->{$question->getAnswersRelationName()}()->createMany($answers);
    }

    private function createArrange(array $q, int $userId, string $difficulty, int $score): void
    {
        $question = Question::create($this->baseAttributes(
            $userId, QuestionType::ARRANGE->value, $q['en'], $q['ar'], $difficulty, $score
        ));

        $answers = [];
        $order = 1;

        // الكلمات الصحيحة: is_correct = true + order متسلسل إجباري
        foreach ($q['items'] as $item) {
            $answers[] = [
                'text_answer' => $item,
                'order'       => $order++,
                'is_correct'  => true,
            ];
        }

        // كلمات التشتيت: is_correct = false + ممنوع يكون عندها order
        foreach ($q['distractors'] ?? [] as $distractor) {
            $answers[] = [
                'text_answer' => $distractor,
                'order'       => null,
                'is_correct'  => false,
            ];
        }

        $question->{$question->getAnswersRelationName()}()->createMany($answers);
    }

    private function createPair(array $q, int $userId, string $difficulty, int $score): void
    {
        $question = Question::create($this->baseAttributes(
            $userId, QuestionType::PAIR->value, $q['en'], $q['ar'], $difficulty, $score
        ));

        $answers = array_map(fn ($p) => [
            'left_text'  => $p['ar'],
            'right_text' => $p['en'],
        ], $q['pairs']);

        $question->{$question->getAnswersRelationName()}()->createMany($answers);
    }

    /**
     * بيانات الأسئلة الأربعة لكل درس (id الدرس متل ما هو بـ LessonSeeder).
     */
    private function questionsData(): array
    {
        return [

            // =====================================================
            // Course 1 - English Foundations (A1)
            // =====================================================

            1 => [ // The Alphabet and Sounds
                'mcq' => [
                    'en' => "Which letter comes right after the letter 'B' in the English alphabet?",
                    'ar' => "أي حرف يأتي مباشرة بعد حرف بي في الأبجدية الإنجليزية؟",
                    'options' => [
                        ['en' => 'C', 'correct' => true],
                        ['en' => 'A', 'correct' => false],
                        ['en' => 'D', 'correct' => false],
                        ['en' => 'F', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence about the English alphabet.',
                    'ar' => 'أكمل الجملة عن الأبجدية الإنجليزية.',
                    'text' => 'The English alphabet has twenty-six {1}.',
                    'answers' => [
                        ['text' => 'letters', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the first four letters of the alphabet in order.',
                    'ar' => 'رتب أول أربعة حروف من الأبجدية بالترتيب الصحيح.',
                    'items' => ['A', 'B', 'C', 'D'],
                    'distractors' => ['Z'],
                ],
                'pair' => [
                    'en' => 'Match each ordinal position to its letter.',
                    'ar' => 'طابق كل ترتيب مع الحرف المناسب له.',
                    'pairs' => [
                        ['ar' => 'الحرف الأول', 'en' => 'A'],
                        ['ar' => 'الحرف الثاني', 'en' => 'B'],
                        ['ar' => 'الحرف الثالث', 'en' => 'C'],
                    ],
                ],
            ],

            2 => [ // Introducing Yourself
                'mcq' => [
                    'en' => 'Which sentence is the correct way to introduce yourself?',
                    'ar' => 'ما هي الطريقة الصحيحة لتعريف نفسك؟',
                    'options' => [
                        ['en' => 'My name is Sara.', 'correct' => true],
                        ['en' => 'Name my is Sara.', 'correct' => false],
                        ['en' => 'Is my name Sara.', 'correct' => false],
                        ['en' => 'Sara name is my.', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the introduction sentence.',
                    'ar' => 'أكمل جملة التعريف بالنفس.',
                    'text' => 'Hello, my name {1} Ahmed.',
                    'answers' => [
                        ['text' => 'is', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Put the words in the correct order to introduce yourself.',
                    'ar' => 'رتب الكلمات لتكوين جملة صحيحة للتعريف بالنفس.',
                    'items' => ['My', 'name', 'is', 'Omar'],
                    'distractors' => ['you'],
                ],
                'pair' => [
                    'en' => 'Match each question with its correct answer.',
                    'ar' => 'طابق كل سؤال مع إجابته الصحيحة.',
                    'pairs' => [
                        ['ar' => 'ما اسمك؟', 'en' => 'My name is Lina.'],
                        ['ar' => 'من أين أنت؟', 'en' => 'I am from Jordan.'],
                        ['ar' => 'كم عمرك؟', 'en' => 'I am twenty years old.'],
                    ],
                ],
            ],

            3 => [ // Numbers and Basic Information
                'mcq' => [
                    'en' => 'What is the correct spelling of the number 7?',
                    'ar' => 'ما هي الكتابة الصحيحة للرقم 7؟',
                    'options' => [
                        ['en' => 'Seven', 'correct' => true],
                        ['en' => 'Sevin', 'correct' => false],
                        ['en' => 'Seaven', 'correct' => false],
                        ['en' => 'Sevan', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence with the correct number word.',
                    'ar' => 'أكمل الجملة بكلمة الرقم الصحيحة.',
                    'text' => 'I have {1} brothers and one sister.',
                    'answers' => [
                        ['text' => 'two', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange these numbers in order from smallest to largest.',
                    'ar' => 'رتب هذه الأرقام من الأصغر إلى الأكبر.',
                    'items' => ['one', 'three', 'five', 'nine'],
                    'distractors' => ['two'],
                ],
                'pair' => [
                    'en' => 'Match the number with its written word.',
                    'ar' => 'طابق كل رقم مع كلمته المكتوبة.',
                    'pairs' => [
                        ['ar' => 'الرقم أربعة', 'en' => 'Four'],
                        ['ar' => 'الرقم ستة', 'en' => 'Six'],
                        ['ar' => 'الرقم عشرة', 'en' => 'Ten'],
                    ],
                ],
            ],

            // =====================================================
            // Course 2 - Basic Grammar (A1)
            // =====================================================

            4 => [ // Nouns and Articles
                'mcq' => [
                    'en' => "Choose the correct article: '___ apple is on the table.'",
                    'ar' => 'اختر أداة التعريف الصحيحة للجملة.',
                    'options' => [
                        ['en' => 'An', 'correct' => true],
                        ['en' => 'A', 'correct' => false],
                        ['en' => 'The the', 'correct' => false],
                        ['en' => 'Some a', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence with the correct article.',
                    'ar' => 'أكمل الجملة بأداة التعريف الصحيحة.',
                    'text' => 'She has {1} cat and a dog.',
                    'answers' => [
                        ['text' => 'a', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a correct sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة صحيحة.',
                    'items' => ['The', 'book', 'is', 'new'],
                    'distractors' => ['a'],
                ],
                'pair' => [
                    'en' => 'Match each noun with its correct article.',
                    'ar' => 'طابق كل اسم مع أداة التعريف المناسبة له.',
                    'pairs' => [
                        ['ar' => 'تفاحة', 'en' => 'An apple'],
                        ['ar' => 'كتاب', 'en' => 'A book'],
                        ['ar' => 'شمس', 'en' => 'The sun'],
                    ],
                ],
            ],

            5 => [ // Subject Pronouns
                'mcq' => [
                    'en' => "Which pronoun replaces 'Ahmed and Sara' in a sentence?",
                    'ar' => 'أي ضمير يحل محل أحمد وسارة في الجملة؟',
                    'options' => [
                        ['en' => 'They', 'correct' => true],
                        ['en' => 'He', 'correct' => false],
                        ['en' => 'She', 'correct' => false],
                        ['en' => 'It', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence with the correct subject pronoun.',
                    'ar' => 'أكمل الجملة بضمير الفاعل الصحيح.',
                    'text' => '{1} is a teacher.',
                    'answers' => [
                        ['text' => 'She', 'blank_order' => 1],
                        ['text' => 'He', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to make a correct sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة صحيحة.',
                    'items' => ['We', 'are', 'good', 'friends'],
                    'distractors' => ['very'],
                ],
                'pair' => [
                    'en' => 'Match each subject with its correct pronoun.',
                    'ar' => 'طابق كل فاعل مع الضمير الصحيح له.',
                    'pairs' => [
                        ['ar' => 'أنا وأنت', 'en' => 'We'],
                        ['ar' => 'الكتاب', 'en' => 'It'],
                        ['ar' => 'هم الطلاب', 'en' => 'They'],
                    ],
                ],
            ],

            6 => [ // This, That, These and Those
                'mcq' => [
                    'en' => 'Which word is used for a plural object that is far away?',
                    'ar' => 'أي كلمة تُستخدم للإشارة إلى أشياء بعيدة وجمع؟',
                    'options' => [
                        ['en' => 'Those', 'correct' => true],
                        ['en' => 'This', 'correct' => false],
                        ['en' => 'That', 'correct' => false],
                        ['en' => 'These', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence with the correct demonstrative.',
                    'ar' => 'أكمل الجملة بأداة الإشارة الصحيحة.',
                    'text' => '{1} is my pen, it is near me.',
                    'answers' => [
                        ['text' => 'This', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a correct sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة صحيحة.',
                    'items' => ['Those', 'are', 'my', 'shoes'],
                    'distractors' => ['this'],
                ],
                'pair' => [
                    'en' => 'Match each situation with the correct demonstrative word.',
                    'ar' => 'طابق كل حالة مع أداة الإشارة الصحيحة.',
                    'pairs' => [
                        ['ar' => 'شيء قريب مفرد', 'en' => 'This'],
                        ['ar' => 'شيء بعيد مفرد', 'en' => 'That'],
                        ['ar' => 'أشياء قريبة جمع', 'en' => 'These'],
                    ],
                ],
            ],

            // =====================================================
            // Course 3 - Everyday Vocabulary (A1)
            // =====================================================

            7 => [ // Family and Friends
                'mcq' => [
                    'en' => "What do we call our father's brother?",
                    'ar' => 'ماذا نسمي أخا الأب؟',
                    'options' => [
                        ['en' => 'Uncle', 'correct' => true],
                        ['en' => 'Cousin', 'correct' => false],
                        ['en' => 'Nephew', 'correct' => false],
                        ['en' => 'Grandfather', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence about family.',
                    'ar' => 'أكمل الجملة عن العائلة.',
                    'text' => "My mother's sister is my {1}.",
                    'answers' => [
                        ['text' => 'aunt', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to describe a family member.',
                    'ar' => 'رتب الكلمات لوصف أحد أفراد العائلة.',
                    'items' => ['My', 'brother', 'is', 'kind'],
                    'distractors' => ['sister'],
                ],
                'pair' => [
                    'en' => 'Match each family word with its meaning.',
                    'ar' => 'طابق كل كلمة عائلية مع معناها.',
                    'pairs' => [
                        ['ar' => 'الأخت', 'en' => 'Sister'],
                        ['ar' => 'الجد', 'en' => 'Grandfather'],
                        ['ar' => 'الصديق', 'en' => 'Friend'],
                    ],
                ],
            ],

            8 => [ // Food and Drinks
                'mcq' => [
                    'en' => 'Which of these is a drink?',
                    'ar' => 'أي من هذه الكلمات تعتبر مشروباً؟',
                    'options' => [
                        ['en' => 'Juice', 'correct' => true],
                        ['en' => 'Bread', 'correct' => false],
                        ['en' => 'Rice', 'correct' => false],
                        ['en' => 'Cheese', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence about food.',
                    'ar' => 'أكمل الجملة عن الطعام.',
                    'text' => 'I would like a cup of {1}, please.',
                    'answers' => [
                        ['text' => 'tea', 'blank_order' => 1],
                        ['text' => 'coffee', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to make a correct request sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة طلب صحيحة.',
                    'items' => ['Can', 'I', 'have', 'water'],
                    'distractors' => ['juice'],
                ],
                'pair' => [
                    'en' => 'Match each food word with its category.',
                    'ar' => 'طابق كل كلمة طعام مع تصنيفها.',
                    'pairs' => [
                        ['ar' => 'مشروب ساخن', 'en' => 'Coffee'],
                        ['ar' => 'فاكهة', 'en' => 'Apple'],
                        ['ar' => 'خضار', 'en' => 'Carrot'],
                    ],
                ],
            ],

            9 => [ // Places Around Us
                'mcq' => [
                    'en' => 'Where do you go to buy medicine?',
                    'ar' => 'إلى أين تذهب لشراء الدواء؟',
                    'options' => [
                        ['en' => 'Pharmacy', 'correct' => true],
                        ['en' => 'Bakery', 'correct' => false],
                        ['en' => 'Library', 'correct' => false],
                        ['en' => 'Bank', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence about places.',
                    'ar' => 'أكمل الجملة عن الأماكن.',
                    'text' => 'I borrow books from the {1}.',
                    'answers' => [
                        ['text' => 'library', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a correct sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة صحيحة.',
                    'items' => ['The', 'school', 'is', 'near'],
                    'distractors' => ['far'],
                ],
                'pair' => [
                    'en' => 'Match each place with what you do there.',
                    'ar' => 'طابق كل مكان مع النشاط المرتبط به.',
                    'pairs' => [
                        ['ar' => 'مكان الصلاة', 'en' => 'Mosque'],
                        ['ar' => 'مكان التسوق', 'en' => 'Market'],
                        ['ar' => 'مكان العلاج', 'en' => 'Hospital'],
                    ],
                ],
            ],

            // =====================================================
            // Course 4 - Basic Conversation (A1)
            // =====================================================

            10 => [ // Greetings and Introductions
                'mcq' => [
                    'en' => 'Which greeting is used in the morning?',
                    'ar' => 'أي تحية تُستخدم في الصباح؟',
                    'options' => [
                        ['en' => 'Good morning', 'correct' => true],
                        ['en' => 'Good night', 'correct' => false],
                        ['en' => 'Good evening', 'correct' => false],
                        ['en' => 'Goodbye', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the greeting sentence.',
                    'ar' => 'أكمل جملة التحية.',
                    'text' => 'Nice to {1} you.',
                    'answers' => [
                        ['text' => 'meet', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a greeting sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة تحية صحيحة.',
                    'items' => ['How', 'are', 'you', 'today'],
                    'distractors' => ['yesterday'],
                ],
                'pair' => [
                    'en' => 'Match each greeting with the correct situation.',
                    'ar' => 'طابق كل تحية مع الموقف المناسب لها.',
                    'pairs' => [
                        ['ar' => 'عند اللقاء الأول', 'en' => 'Nice to meet you'],
                        ['ar' => 'عند الوداع', 'en' => 'Goodbye'],
                        ['ar' => 'في المساء', 'en' => 'Good evening'],
                    ],
                ],
            ],

            11 => [ // Asking and Answering Questions
                'mcq' => [
                    'en' => 'Which question word asks about a place?',
                    'ar' => 'أي أداة استفهام تُستخدم للسؤال عن المكان؟',
                    'options' => [
                        ['en' => 'Where', 'correct' => true],
                        ['en' => 'When', 'correct' => false],
                        ['en' => 'Who', 'correct' => false],
                        ['en' => 'Why', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the question.',
                    'ar' => 'أكمل السؤال.',
                    'text' => '{1} do you live?',
                    'answers' => [
                        ['text' => 'Where', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a correct question.',
                    'ar' => 'رتب الكلمات لتكوين سؤال صحيح.',
                    'items' => ['What', 'is', 'your', 'name'],
                    'distractors' => ['where'],
                ],
                'pair' => [
                    'en' => 'Match each question word with its use.',
                    'ar' => 'طابق كل أداة استفهام مع استخدامها.',
                    'pairs' => [
                        ['ar' => 'للسؤال عن الوقت', 'en' => 'When'],
                        ['ar' => 'للسؤال عن السبب', 'en' => 'Why'],
                        ['ar' => 'للسؤال عن الشخص', 'en' => 'Who'],
                    ],
                ],
            ],

            12 => [ // Talking About Yourself
                'mcq' => [
                    'en' => 'Which sentence correctly talks about a hobby?',
                    'ar' => 'أي جملة تصف هواية بشكل صحيح؟',
                    'options' => [
                        ['en' => 'I like reading books.', 'correct' => true],
                        ['en' => 'I like reading a books.', 'correct' => false],
                        ['en' => 'I liking read books.', 'correct' => false],
                        ['en' => 'I likes reading books.', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence about yourself.',
                    'ar' => 'أكمل الجملة عن نفسك.',
                    'text' => 'I {1} from Syria.',
                    'answers' => [
                        ['text' => 'am', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to describe yourself.',
                    'ar' => 'رتب الكلمات لوصف نفسك.',
                    'items' => ['I', 'am', 'a', 'student'],
                    'distractors' => ['teacher'],
                ],
                'pair' => [
                    'en' => 'Match each sentence beginning with the correct ending.',
                    'ar' => 'طابق كل بداية جملة مع نهايتها الصحيحة.',
                    'pairs' => [
                        ['ar' => 'أنا أحب', 'en' => 'playing football'],
                        ['ar' => 'أنا أعمل', 'en' => 'as a teacher'],
                        ['ar' => 'أنا أسكن في', 'en' => 'Damascus'],
                    ],
                ],
            ],

            // =====================================================
            // Course 5 - Past and Future Tenses (A2)
            // =====================================================

            13 => [ // Past Simple in Everyday Life
                'mcq' => [
                    'en' => "What is the past simple form of 'go'?",
                    'ar' => 'ما هو الشكل الصحيح للفعل go في الماضي البسيط؟',
                    'options' => [
                        ['en' => 'Went', 'correct' => true],
                        ['en' => 'Goed', 'correct' => false],
                        ['en' => 'Gone', 'correct' => false],
                        ['en' => 'Going', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence with the past simple form.',
                    'ar' => 'أكمل الجملة بصيغة الماضي البسيط الصحيحة.',
                    'text' => 'Yesterday, I {1} to the market.',
                    'answers' => [
                        ['text' => 'went', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a past simple sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة بصيغة الماضي البسيط.',
                    'items' => ['She', 'visited', 'her', 'grandmother'],
                    'distractors' => ['visits'],
                ],
                'pair' => [
                    'en' => 'Match each present verb with its past simple form.',
                    'ar' => 'طابق كل فعل بصيغة المضارع مع صيغته في الماضي.',
                    'pairs' => [
                        ['ar' => 'يذهب', 'en' => 'Went'],
                        ['ar' => 'يأكل', 'en' => 'Ate'],
                        ['ar' => 'يرى', 'en' => 'Saw'],
                    ],
                ],
            ],

            14 => [ // Talking About Future Plans
                'mcq' => [
                    'en' => 'Which sentence correctly expresses a future plan?',
                    'ar' => 'أي جملة تعبر بشكل صحيح عن خطة مستقبلية؟',
                    'options' => [
                        ['en' => 'I am going to visit my uncle next week.', 'correct' => true],
                        ['en' => 'I go to visit my uncle next week.', 'correct' => false],
                        ['en' => 'I visited my uncle next week.', 'correct' => false],
                        ['en' => 'I visiting my uncle next week.', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence about future plans.',
                    'ar' => 'أكمل الجملة عن خطة مستقبلية.',
                    'text' => 'We {1} going to travel next summer.',
                    'answers' => [
                        ['text' => 'are', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a sentence about future plans.',
                    'ar' => 'رتب الكلمات لتكوين جملة عن خطة مستقبلية.',
                    'items' => ['I', 'am', 'going', 'to', 'study'],
                    'distractors' => ['will'],
                ],
                'pair' => [
                    'en' => 'Match each future expression with its correct use.',
                    'ar' => 'طابق كل تعبير مستقبلي مع استخدامه الصحيح.',
                    'pairs' => [
                        ['ar' => 'خطة مؤكدة تقريباً', 'en' => 'Going to'],
                        ['ar' => 'قرار لحظي', 'en' => 'Will'],
                        ['ar' => 'جدول زمني ثابت', 'en' => 'Present continuous'],
                    ],
                ],
            ],

            15 => [ // Past and Future Time Expressions
                'mcq' => [
                    'en' => 'Which word refers to the future?',
                    'ar' => 'أي كلمة تشير إلى المستقبل؟',
                    'options' => [
                        ['en' => 'Tomorrow', 'correct' => true],
                        ['en' => 'Yesterday', 'correct' => false],
                        ['en' => 'Ago', 'correct' => false],
                        ['en' => 'Last week', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence with the correct time expression.',
                    'ar' => 'أكمل الجملة بتعبير الزمن الصحيح.',
                    'text' => 'I saw him two days {1}.',
                    'answers' => [
                        ['text' => 'ago', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a correct sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة صحيحة.',
                    'items' => ['Next', 'year', 'I', 'will', 'graduate'],
                    'distractors' => ['yesterday'],
                ],
                'pair' => [
                    'en' => 'Match each time expression with past or future.',
                    'ar' => 'طابق كل تعبير زمني مع الماضي أو المستقبل.',
                    'pairs' => [
                        ['ar' => 'الأسبوع الماضي', 'en' => 'Last week'],
                        ['ar' => 'في المستقبل', 'en' => 'In the future'],
                        ['ar' => 'منذ ساعة', 'en' => 'An hour ago'],
                    ],
                ],
            ],

            // =====================================================
            // Course 6 - Practical Vocabulary (A2)
            // =====================================================

            16 => [ // Shopping and Money
                'mcq' => [
                    'en' => 'What do you say to ask the price of something?',
                    'ar' => 'ماذا تقول للسؤال عن سعر شيء ما؟',
                    'options' => [
                        ['en' => 'How much is this?', 'correct' => true],
                        ['en' => 'How many is this?', 'correct' => false],
                        ['en' => 'How much are you?', 'correct' => false],
                        ['en' => 'How this much?', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence about shopping.',
                    'ar' => 'أكمل الجملة عن التسوق.',
                    'text' => 'This shirt is too expensive; do you have a {1} one?',
                    'answers' => [
                        ['text' => 'cheaper', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a shopping question.',
                    'ar' => 'رتب الكلمات لتكوين سؤال تسوق صحيح.',
                    'items' => ['Do', 'you', 'accept', 'credit', 'cards'],
                    'distractors' => ['cash'],
                ],
                'pair' => [
                    'en' => 'Match each shopping word with its meaning.',
                    'ar' => 'طابق كل كلمة تسوق مع معناها.',
                    'pairs' => [
                        ['ar' => 'الفاتورة', 'en' => 'Receipt'],
                        ['ar' => 'الخصم', 'en' => 'Discount'],
                        ['ar' => 'الصراف', 'en' => 'Cashier'],
                    ],
                ],
            ],

            17 => [ // Travel and Transportation
                'mcq' => [
                    'en' => 'Which of these is a means of transportation?',
                    'ar' => 'أي من هذه الكلمات تعتبر وسيلة نقل؟',
                    'options' => [
                        ['en' => 'Bus', 'correct' => true],
                        ['en' => 'Chair', 'correct' => false],
                        ['en' => 'Table', 'correct' => false],
                        ['en' => 'Window', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence about travel.',
                    'ar' => 'أكمل الجملة عن السفر.',
                    'text' => 'I usually take the {1} to work.',
                    'answers' => [
                        ['text' => 'train', 'blank_order' => 1],
                        ['text' => 'bus', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a travel sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة عن السفر.',
                    'items' => ['The', 'plane', 'leaves', 'at', 'noon'],
                    'distractors' => ['arrives'],
                ],
                'pair' => [
                    'en' => 'Match each transportation word with its related place.',
                    'ar' => 'طابق كل وسيلة نقل مع مكانها المرتبط بها.',
                    'pairs' => [
                        ['ar' => 'الطائرة', 'en' => 'Airport'],
                        ['ar' => 'القطار', 'en' => 'Station'],
                        ['ar' => 'السيارة', 'en' => 'Garage'],
                    ],
                ],
            ],

            18 => [ // Health and Daily Activities
                'mcq' => [
                    'en' => 'What should you do if you have a headache?',
                    'ar' => 'ماذا يجب أن تفعل إذا كنت تعاني من صداع؟',
                    'options' => [
                        ['en' => 'Take some rest', 'correct' => true],
                        ['en' => 'Run five kilometers', 'correct' => false],
                        ['en' => 'Eat a lot of sugar', 'correct' => false],
                        ['en' => 'Stay awake all night', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence about daily health habits.',
                    'ar' => 'أكمل الجملة عن العادات الصحية اليومية.',
                    'text' => 'You should drink enough {1} every day.',
                    'answers' => [
                        ['text' => 'water', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a daily routine sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة عن روتين يومي.',
                    'items' => ['I', 'wake', 'up', 'early', 'every', 'day'],
                    'distractors' => ['late'],
                ],
                'pair' => [
                    'en' => 'Match each health word with its meaning.',
                    'ar' => 'طابق كل كلمة صحية مع معناها.',
                    'pairs' => [
                        ['ar' => 'صداع', 'en' => 'Headache'],
                        ['ar' => 'حمى', 'en' => 'Fever'],
                        ['ar' => 'دواء', 'en' => 'Medicine'],
                    ],
                ],
            ],

            // =====================================================
            // Course 7 - Listening and Understanding (A2)
            // =====================================================

            19 => [ // Understanding Short Conversations
                'mcq' => [
                    'en' => "In a conversation, what does 'Excuse me' usually mean?",
                    'ar' => 'في المحادثة ماذا تعني عبارة Excuse me عادة؟',
                    'options' => [
                        ['en' => 'A polite way to get attention', 'correct' => true],
                        ['en' => 'A way to say goodbye', 'correct' => false],
                        ['en' => 'A way to order food', 'correct' => false],
                        ['en' => 'A way to ask for money', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the short conversation.',
                    'ar' => 'أكمل المحادثة القصيرة.',
                    'text' => 'A: How are you? B: I am fine, {1} you.',
                    'answers' => [
                        ['text' => 'thank', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to complete the conversation reply.',
                    'ar' => 'رتب الكلمات لإكمال رد المحادثة.',
                    'items' => ['I', 'am', 'doing', 'well'],
                    'distractors' => ['bad'],
                ],
                'pair' => [
                    'en' => 'Match each conversational phrase with its meaning.',
                    'ar' => 'طابق كل عبارة محادثة مع معناها.',
                    'pairs' => [
                        ['ar' => 'عفواً، المعذرة', 'en' => 'Excuse me'],
                        ['ar' => 'لا بأس', 'en' => 'No problem'],
                        ['ar' => 'بالتأكيد', 'en' => 'Sure'],
                    ],
                ],
            ],

            20 => [ // Listening for Key Information
                'mcq' => [
                    'en' => 'When listening for key information, what should you focus on first?',
                    'ar' => 'عند الاستماع للمعلومات الأساسية، على ماذا يجب أن تركز أولاً؟',
                    'options' => [
                        ['en' => 'Names, numbers, and dates', 'correct' => true],
                        ['en' => 'Every single word equally', 'correct' => false],
                        ['en' => 'Background noise', 'correct' => false],
                        ['en' => "The speaker's accent only", 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence about listening skills.',
                    'ar' => 'أكمل الجملة عن مهارات الاستماع.',
                    'text' => 'Listen carefully and try to catch the main {1}.',
                    'answers' => [
                        ['text' => 'idea', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a correct instruction.',
                    'ar' => 'رتب الكلمات لتكوين تعليمة صحيحة.',
                    'items' => ['Listen', 'to', 'the', 'announcement', 'carefully'],
                    'distractors' => ['quickly'],
                ],
                'pair' => [
                    'en' => 'Match each listening tip with its purpose.',
                    'ar' => 'طابق كل نصيحة استماع مع هدفها.',
                    'pairs' => [
                        ['ar' => 'التركيز على الأرقام', 'en' => 'Catch important details'],
                        ['ar' => 'تخمين المعنى', 'en' => 'Understand new words'],
                        ['ar' => 'إعادة الاستماع', 'en' => 'Confirm information'],
                    ],
                ],
            ],

            21 => [ // Understanding Everyday Speech
                'mcq' => [
                    'en' => "What does the phrase 'What's up?' usually mean in casual speech?",
                    'ar' => 'ماذا تعني عبارة What is up عادة في الحديث اليومي؟',
                    'options' => [
                        ['en' => "How are you / what's happening", 'correct' => true],
                        ['en' => 'What is the price', 'correct' => false],
                        ['en' => 'Where are you going', 'correct' => false],
                        ['en' => 'What time is it', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence with the correct negative form.',
                    'ar' => 'أكمل الجملة بصيغة النفي الصحيحة.',
                    'text' => 'I {1} understand that clearly.',
                    'answers' => [
                        ['text' => "don't", 'blank_order' => 1],
                        ['text' => 'do not', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form an everyday sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة يومية صحيحة.',
                    'items' => ['I', "don't", 'understand', 'that'],
                    'distractors' => ['always'],
                ],
                'pair' => [
                    'en' => 'Match each everyday phrase with its meaning.',
                    'ar' => 'طابق كل عبارة يومية مع معناها.',
                    'pairs' => [
                        ['ar' => 'لا بأس، لا مشكلة', 'en' => "It's okay"],
                        ['ar' => 'لست متأكداً', 'en' => "I'm not sure"],
                        ['ar' => 'بكل سرور', 'en' => 'With pleasure'],
                    ],
                ],
            ],

            // =====================================================
            // Course 8 - Everyday English Conversation (A2)
            // =====================================================

            22 => [ // Making Plans with Friends
                'mcq' => [
                    'en' => 'Which sentence is used to suggest an activity?',
                    'ar' => 'أي جملة تُستخدم لاقتراح نشاط ما؟',
                    'options' => [
                        ['en' => 'Shall we go to the cinema tonight?', 'correct' => true],
                        ['en' => 'We go cinema tonight?', 'correct' => false],
                        ['en' => 'Going we cinema tonight?', 'correct' => false],
                        ['en' => 'Cinema tonight we go?', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence about making plans.',
                    'ar' => 'أكمل الجملة عن التخطيط.',
                    'text' => "Let's {1} up at six o'clock.",
                    'answers' => [
                        ['text' => 'meet', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a suggestion.',
                    'ar' => 'رتب الكلمات لتكوين جملة اقتراح.',
                    'items' => ['Why', "don't", 'we', 'go', 'together'],
                    'distractors' => ['alone'],
                ],
                'pair' => [
                    'en' => 'Match each planning phrase with its purpose.',
                    'ar' => 'طابق كل عبارة تخطيط مع الغرض منها.',
                    'pairs' => [
                        ['ar' => 'اقتراح نشاط', 'en' => 'How about going out?'],
                        ['ar' => 'الموافقة على خطة', 'en' => 'Sounds good'],
                        ['ar' => 'رفض بلطف', 'en' => 'Maybe next time'],
                    ],
                ],
            ],

            23 => [ // Ordering Food at a Restaurant
                'mcq' => [
                    'en' => 'What do you say to order food politely?',
                    'ar' => 'ماذا تقول لطلب الطعام بأدب؟',
                    'options' => [
                        ['en' => 'I would like the chicken, please.', 'correct' => true],
                        ['en' => 'Give me chicken now.', 'correct' => false],
                        ['en' => 'Chicken you bring me.', 'correct' => false],
                        ['en' => 'I chicken want.', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the restaurant sentence.',
                    'ar' => 'أكمل جملة المطعم.',
                    'text' => 'Could I have the {1}, please?',
                    'answers' => [
                        ['text' => 'menu', 'blank_order' => 1],
                        ['text' => 'bill', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a restaurant request.',
                    'ar' => 'رتب الكلمات لتكوين طلب في المطعم.',
                    'items' => ['Can', 'I', 'have', 'the', 'bill'],
                    'distractors' => ['menu'],
                ],
                'pair' => [
                    'en' => 'Match each restaurant word with its meaning.',
                    'ar' => 'طابق كل كلمة مطعم مع معناها.',
                    'pairs' => [
                        ['ar' => 'قائمة الطعام', 'en' => 'Menu'],
                        ['ar' => 'النادل', 'en' => 'Waiter'],
                        ['ar' => 'الفاتورة', 'en' => 'Bill'],
                    ],
                ],
            ],

            24 => [ // Asking for Directions
                'mcq' => [
                    'en' => 'Which question is correct for asking directions?',
                    'ar' => 'أي سؤال صحيح للاستفسار عن الاتجاهات؟',
                    'options' => [
                        ['en' => 'Excuse me, how do I get to the station?', 'correct' => true],
                        ['en' => 'Excuse me, station where is going?', 'correct' => false],
                        ['en' => 'Station how I get to?', 'correct' => false],
                        ['en' => 'How station to get I?', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the direction sentence.',
                    'ar' => 'أكمل جملة الاتجاهات.',
                    'text' => 'Turn {1} at the next corner.',
                    'answers' => [
                        ['text' => 'left', 'blank_order' => 1],
                        ['text' => 'right', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a direction sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة اتجاهات صحيحة.',
                    'items' => ['Go', 'straight', 'then', 'turn', 'right'],
                    'distractors' => ['left'],
                ],
                'pair' => [
                    'en' => 'Match each direction word with its meaning.',
                    'ar' => 'طابق كل كلمة اتجاه مع معناها.',
                    'pairs' => [
                        ['ar' => 'يسار', 'en' => 'Left'],
                        ['ar' => 'يمين', 'en' => 'Right'],
                        ['ar' => 'مستقيم', 'en' => 'Straight'],
                    ],
                ],
            ],

            // =====================================================
            // Course 9 - Intermediate Grammar (B1)
            // =====================================================

            25 => [ // Present Perfect Tense
                'mcq' => [
                    'en' => 'Which sentence correctly uses the present perfect tense?',
                    'ar' => 'أي جملة تستخدم زمن المضارع التام بشكل صحيح؟',
                    'options' => [
                        ['en' => 'I have visited Paris three times.', 'correct' => true],
                        ['en' => 'I have visit Paris three times.', 'correct' => false],
                        ['en' => 'I visited Paris three times ago.', 'correct' => false],
                        ['en' => 'I am visited Paris three times.', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence using the present perfect.',
                    'ar' => 'أكمل الجملة باستخدام زمن المضارع التام.',
                    'text' => 'She {1} already finished her homework.',
                    'answers' => [
                        ['text' => 'has', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a present perfect sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة بصيغة المضارع التام.',
                    'items' => ['They', 'have', 'never', 'been', 'abroad'],
                    'distractors' => ['has'],
                ],
                'pair' => [
                    'en' => 'Match each present perfect use with its example.',
                    'ar' => 'طابق كل استخدام للمضارع التام مع مثاله.',
                    'pairs' => [
                        ['ar' => 'خبرة حياتية', 'en' => 'I have traveled to Egypt.'],
                        ['ar' => 'حدث لم ينته بعد', 'en' => 'She has not finished yet.'],
                        ['ar' => 'تغيير حديث', 'en' => 'He has just arrived.'],
                    ],
                ],
            ],

            26 => [ // First and Second Conditionals
                'mcq' => [
                    'en' => 'Which sentence is an example of the first conditional?',
                    'ar' => 'أي جملة تعتبر مثالاً على الشرط الأول؟',
                    'options' => [
                        ['en' => 'If it rains, I will stay home.', 'correct' => true],
                        ['en' => 'If it rained, I would stay home.', 'correct' => false],
                        ['en' => 'If it rains, I would stay home.', 'correct' => false],
                        ['en' => 'If it rain, I will stay home.', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the second conditional sentence.',
                    'ar' => 'أكمل جملة الشرط الثاني.',
                    'text' => 'If I {1} rich, I would travel the world.',
                    'answers' => [
                        ['text' => 'were', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a first conditional sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة شرط أول صحيحة.',
                    'items' => ['If', 'you', 'study', 'you', 'will', 'pass'],
                    'distractors' => ['studied'],
                ],
                'pair' => [
                    'en' => 'Match each conditional type with its use.',
                    'ar' => 'طابق كل نوع شرط مع استخدامه.',
                    'pairs' => [
                        ['ar' => 'احتمال حقيقي في المستقبل', 'en' => 'First conditional'],
                        ['ar' => 'حالة غير حقيقية في الحاضر', 'en' => 'Second conditional'],
                        ['ar' => 'نتيجة مؤكدة', 'en' => 'Zero conditional'],
                    ],
                ],
            ],

            27 => [ // Passive Voice
                'mcq' => [
                    'en' => 'Which sentence is written in the passive voice?',
                    'ar' => 'أي جملة مكتوبة بصيغة المبني للمجهول؟',
                    'options' => [
                        ['en' => 'The letter was written by John.', 'correct' => true],
                        ['en' => 'John wrote the letter.', 'correct' => false],
                        ['en' => 'John is writing the letter.', 'correct' => false],
                        ['en' => 'John will write the letter.', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the passive sentence.',
                    'ar' => 'أكمل الجملة بصيغة المبني للمجهول.',
                    'text' => 'The house {1} built in 1990.',
                    'answers' => [
                        ['text' => 'was', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a passive sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة مبنية للمجهول.',
                    'items' => ['The', 'cake', 'was', 'baked', 'yesterday'],
                    'distractors' => ['bakes'],
                ],
                'pair' => [
                    'en' => 'Match each active sentence with its passive form.',
                    'ar' => 'طابق كل جملة معلومة مع صيغتها المبنية للمجهول.',
                    'pairs' => [
                        ['ar' => 'الشرطة تعتقل اللص', 'en' => 'The thief is arrested.'],
                        ['ar' => 'العمال بنوا الجسر', 'en' => 'The bridge was built.'],
                        ['ar' => 'المعلم يصحح الأوراق', 'en' => 'The papers are corrected.'],
                    ],
                ],
            ],

            // =====================================================
            // Course 10 - Intermediate Conversation (B1)
            // =====================================================

            28 => [ // Expressing Opinions
                'mcq' => [
                    'en' => 'Which phrase is used to express a personal opinion?',
                    'ar' => 'أي عبارة تُستخدم للتعبير عن رأي شخصي؟',
                    'options' => [
                        ['en' => 'In my opinion, this is a good idea.', 'correct' => true],
                        ['en' => 'In my opinion this idea good.', 'correct' => false],
                        ['en' => 'My opinion in, this good idea.', 'correct' => false],
                        ['en' => 'Opinion my this is good idea.', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence expressing an opinion.',
                    'ar' => 'أكمل الجملة التي تعبر عن رأي.',
                    'text' => 'I {1} that studying abroad is a great experience.',
                    'answers' => [
                        ['text' => 'believe', 'blank_order' => 1],
                        ['text' => 'think', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form an opinion sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة رأي صحيحة.',
                    'items' => ['I', 'think', 'this', 'plan', 'is', 'great'],
                    'distractors' => ['thought'],
                ],
                'pair' => [
                    'en' => 'Match each opinion phrase with its meaning.',
                    'ar' => 'طابق كل عبارة رأي مع معناها.',
                    'pairs' => [
                        ['ar' => 'من وجهة نظري', 'en' => 'From my point of view'],
                        ['ar' => 'أعتقد أن', 'en' => 'I believe that'],
                        ['ar' => 'بصراحة', 'en' => 'To be honest'],
                    ],
                ],
            ],

            29 => [ // Agreeing and Disagreeing
                'mcq' => [
                    'en' => 'Which phrase is used to disagree politely?',
                    'ar' => 'أي عبارة تُستخدم للاختلاف بأدب؟',
                    'options' => [
                        ['en' => 'I see your point, but I disagree.', 'correct' => true],
                        ['en' => 'You are completely wrong.', 'correct' => false],
                        ['en' => 'I disagree you are stupid.', 'correct' => false],
                        ['en' => "No, never, that's wrong.", 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence about agreement.',
                    'ar' => 'أكمل الجملة عن الموافقة.',
                    'text' => 'I {1} agree with you on this point.',
                    'answers' => [
                        ['text' => 'totally', 'blank_order' => 1],
                        ['text' => 'completely', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a disagreement sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة اختلاف بالرأي.',
                    'items' => ['I', 'am', 'afraid', 'I', 'disagree'],
                    'distractors' => ['agree'],
                ],
                'pair' => [
                    'en' => 'Match each phrase with agreement or disagreement.',
                    'ar' => 'طابق كل عبارة مع كونها موافقة أو اختلافاً.',
                    'pairs' => [
                        ['ar' => 'أوافقك الرأي تماماً', 'en' => "I couldn't agree more"],
                        ['ar' => 'لا أعتقد ذلك', 'en' => "I don't think so"],
                        ['ar' => 'ربما أنت محق', 'en' => 'You might be right'],
                    ],
                ],
            ],

            30 => [ // Discussing Everyday Topics
                'mcq' => [
                    'en' => 'Which question is suitable for starting a casual discussion?',
                    'ar' => 'أي سؤال مناسب لبدء نقاش عادي؟',
                    'options' => [
                        ['en' => 'What do you think about this news?', 'correct' => true],
                        ['en' => 'You think what about news this?', 'correct' => false],
                        ['en' => 'News this about you think what?', 'correct' => false],
                        ['en' => 'Think you what news about this?', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the discussion sentence.',
                    'ar' => 'أكمل جملة النقاش.',
                    'text' => "Let's {1} about our weekend plans.",
                    'answers' => [
                        ['text' => 'talk', 'blank_order' => 1],
                        ['text' => 'chat', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a discussion sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة نقاش صحيحة.',
                    'items' => ['What', 'do', 'you', 'think', 'about', 'this'],
                    'distractors' => ['did'],
                ],
                'pair' => [
                    'en' => 'Match each discussion phrase with its function.',
                    'ar' => 'طابق كل عبارة نقاش مع وظيفتها.',
                    'pairs' => [
                        ['ar' => 'طلب رأي الآخر', 'en' => 'What do you think?'],
                        ['ar' => 'تغيير الموضوع', 'en' => 'By the way'],
                        ['ar' => 'إنهاء النقاش', 'en' => "Anyway, let's move on"],
                    ],
                ],
            ],

            // =====================================================
            // Course 11 - Advanced Grammar (B2)
            // =====================================================

            31 => [ // Advanced Verb Tenses
                'mcq' => [
                    'en' => 'Which sentence uses the past perfect continuous correctly?',
                    'ar' => 'أي جملة تستخدم زمن الماضي التام المستمر بشكل صحيح؟',
                    'options' => [
                        ['en' => 'She had been studying for two hours before I called.', 'correct' => true],
                        ['en' => 'She had be studying for two hours before I called.', 'correct' => false],
                        ['en' => 'She has been studying for two hours before I called.', 'correct' => false],
                        ['en' => 'She was been studying for two hours before I called.', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence with the correct advanced tense.',
                    'ar' => 'أكمل الجملة بالزمن المتقدم الصحيح.',
                    'text' => 'By next year, I {1} have graduated from university.',
                    'answers' => [
                        ['text' => 'will', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a future perfect sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة بصيغة المستقبل التام.',
                    'items' => ['By', 'noon', 'she', 'will', 'have', 'finished'],
                    'distractors' => ['finish'],
                ],
                'pair' => [
                    'en' => 'Match each tense with its example sentence.',
                    'ar' => 'طابق كل زمن مع الجملة المثال الخاصة به.',
                    'pairs' => [
                        ['ar' => 'الماضي التام المستمر', 'en' => 'I had been working all day.'],
                        ['ar' => 'المستقبل التام', 'en' => 'I will have left by then.'],
                        ['ar' => 'المضارع التام المستمر', 'en' => 'I have been living here for years.'],
                    ],
                ],
            ],

            32 => [ // Advanced Conditional Sentences
                'mcq' => [
                    'en' => 'Which sentence is an example of the third conditional?',
                    'ar' => 'أي جملة تعتبر مثالاً على الشرط الثالث؟',
                    'options' => [
                        ['en' => 'If I had studied harder, I would have passed the exam.', 'correct' => true],
                        ['en' => 'If I study harder, I will pass the exam.', 'correct' => false],
                        ['en' => 'If I studied harder, I would pass the exam.', 'correct' => false],
                        ['en' => 'If I have studied harder, I pass the exam.', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the third conditional sentence.',
                    'ar' => 'أكمل جملة الشرط الثالث.',
                    'text' => 'If she had known about the meeting, she {1} have attended.',
                    'answers' => [
                        ['text' => 'would', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a mixed conditional sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة شرط مختلط صحيحة.',
                    'items' => ['If', 'I', 'had', 'left', 'earlier', 'I', 'would', 'be', 'there', 'now'],
                    'distractors' => ['leave'],
                ],
                'pair' => [
                    'en' => 'Match each conditional sentence with its meaning.',
                    'ar' => 'طابق كل جملة شرطية مع معناها.',
                    'pairs' => [
                        ['ar' => 'ندم على حدث ماضٍ', 'en' => 'I wish I had studied more.'],
                        ['ar' => 'نتيجة افتراضية غير واقعية', 'en' => 'If I had wings, I would fly.'],
                        ['ar' => 'شرط مختلط بين الماضي والحاضر', 'en' => 'If I had saved money, I would be rich now.'],
                    ],
                ],
            ],

            33 => [ // Reported Speech
                'mcq' => [
                    'en' => 'Which sentence correctly reports what someone said?',
                    'ar' => 'أي جملة تنقل كلام شخص آخر بشكل صحيح؟',
                    'options' => [
                        ['en' => 'He said that he was tired.', 'correct' => true],
                        ['en' => 'He said that he is tired.', 'correct' => false],
                        ['en' => 'He said that he tired.', 'correct' => false],
                        ['en' => 'He say that he was tired.', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the reported speech sentence.',
                    'ar' => 'أكمل جملة الكلام المنقول.',
                    'text' => 'She told me that she {1} going to the party.',
                    'answers' => [
                        ['text' => 'was', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a reported speech sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة كلام منقول صحيحة.',
                    'items' => ['He', 'said', 'that', 'he', 'was', 'busy'],
                    'distractors' => ['is'],
                ],
                'pair' => [
                    'en' => 'Match each direct sentence with its reported form.',
                    'ar' => 'طابق كل جملة مباشرة مع صيغتها المنقولة.',
                    'pairs' => [
                        ['ar' => 'أنا سعيد', 'en' => 'He said he was happy.'],
                        ['ar' => 'سأذهب غداً', 'en' => 'She said she would go the next day.'],
                        ['ar' => 'لا أحب القهوة', 'en' => "He said he didn't like coffee."],
                    ],
                ],
            ],

            // =====================================================
            // Course 12 - Advanced Conversation (B2)
            // =====================================================

            34 => [ // Debating and Giving Arguments
                'mcq' => [
                    'en' => 'Which phrase is used to introduce a strong counter-argument?',
                    'ar' => 'أي عبارة تُستخدم لتقديم حجة مضادة قوية؟',
                    'options' => [
                        ['en' => 'On the other hand, one could argue that...', 'correct' => true],
                        ['en' => 'On other hand one could argue that.', 'correct' => false],
                        ['en' => 'Other the hand one could argue.', 'correct' => false],
                        ['en' => 'On the hand other, argue could one.', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the debate sentence.',
                    'ar' => 'أكمل جملة النقاش الجدلي.',
                    'text' => 'There is strong {1} to support this argument.',
                    'answers' => [
                        ['text' => 'evidence', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a persuasive argument sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة إقناعية قوية.',
                    'items' => ['This', 'clearly', 'proves', 'that', 'we', 'are', 'right'],
                    'distractors' => ['wrong'],
                ],
                'pair' => [
                    'en' => 'Match each debate phrase with its function.',
                    'ar' => 'طابق كل عبارة نقاش جدلي مع وظيفتها.',
                    'pairs' => [
                        ['ar' => 'تقديم حجة', 'en' => 'The main reason is that...'],
                        ['ar' => 'دحض حجة', 'en' => 'That is not entirely true because...'],
                        ['ar' => 'الاستنتاج', 'en' => 'In conclusion...'],
                    ],
                ],
            ],

            35 => [ // Discussing Complex Topics
                'mcq' => [
                    'en' => 'Which phrase helps introduce a complex topic clearly?',
                    'ar' => 'أي عبارة تساعد على تقديم موضوع معقد بوضوح؟',
                    'options' => [
                        ['en' => 'Let me break this down into a few points.', 'correct' => true],
                        ['en' => 'Let break this me down few points.', 'correct' => false],
                        ['en' => 'Break this let me into points few.', 'correct' => false],
                        ['en' => 'This let break me down points few.', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence about discussing complex topics.',
                    'ar' => 'أكمل الجملة عن مناقشة المواضيع المعقدة.',
                    'text' => "This issue is quite {1}, so let's take it step by step.",
                    'answers' => [
                        ['text' => 'complicated', 'blank_order' => 1],
                        ['text' => 'complex', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a sentence introducing a complex topic.',
                    'ar' => 'رتب الكلمات لتكوين جملة تقدم موضوعاً معقداً.',
                    'items' => ['There', 'are', 'several', 'factors', 'to', 'consider', 'here'],
                    'distractors' => ['few'],
                ],
                'pair' => [
                    'en' => 'Match each phrase with its purpose in a discussion.',
                    'ar' => 'طابق كل عبارة مع هدفها في النقاش.',
                    'pairs' => [
                        ['ar' => 'تبسيط فكرة', 'en' => 'In simple terms'],
                        ['ar' => 'إضافة تفصيل', 'en' => 'Furthermore'],
                        ['ar' => 'تلخيص', 'en' => 'To sum up'],
                    ],
                ],
            ],

            36 => [ // Speaking Fluently and Naturally
                'mcq' => [
                    'en' => 'Which technique helps you speak more fluently?',
                    'ar' => 'أي أسلوب يساعدك على التحدث بطلاقة أكبر؟',
                    'options' => [
                        ['en' => 'Thinking in English instead of translating', 'correct' => true],
                        ['en' => 'Translating every word from your native language', 'correct' => false],
                        ['en' => 'Speaking as slowly as possible always', 'correct' => false],
                        ['en' => 'Avoiding any conversation practice', 'correct' => false],
                    ],
                ],
                'fill' => [
                    'en' => 'Complete the sentence about fluent speaking.',
                    'ar' => 'أكمل الجملة عن التحدث بطلاقة.',
                    'text' => 'The more you practice, the more {1} you become.',
                    'answers' => [
                        ['text' => 'fluent', 'blank_order' => 1],
                    ],
                ],
                'arrange' => [
                    'en' => 'Arrange the words to form a natural spoken sentence.',
                    'ar' => 'رتب الكلمات لتكوين جملة تحدث طبيعية.',
                    'items' => ['Honestly', 'speaking', 'I', 'think', "it's", 'a', 'great', 'idea'],
                    'distractors' => ['bad'],
                ],
                'pair' => [
                    'en' => 'Match each fluency tip with its benefit.',
                    'ar' => 'طابق كل نصيحة طلاقة مع فائدتها.',
                    'pairs' => [
                        ['ar' => 'استخدام تعابير شائعة', 'en' => 'Sound more natural'],
                        ['ar' => 'عدم الخوف من الأخطاء', 'en' => 'Build confidence'],
                        ['ar' => 'الممارسة اليومية', 'en' => 'Improve fluency over time'],
                    ],
                ],
            ],
        ];
    }
}
