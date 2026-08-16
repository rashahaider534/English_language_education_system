<?php

namespace Database\Seeders;

use App\Enums\QuestionType;
use App\Models\Question;
use Illuminate\Database\Seeder;

class PlacementQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 3;

        $questions = [

            // =====================================================
            // 1 - MCQ - EASY
            // =====================================================
            [
                'type' => QuestionType::MCQ->value,
                'difficulty' => 'EASY',
                'score' => 2,
                'title_question_en' => 'Grammar - Basic Sentence',
                'title_question_ar' => 'القواعد - الجملة الأساسية',
                'text_question' => 'Choose the grammatically correct sentence.',
                'answers' => [
                    ['text_answer' => 'She is a student.', 'is_correct' => true],
                    ['text_answer' => 'She are a student.', 'is_correct' => false],
                    ['text_answer' => 'She am a student.', 'is_correct' => false],
                    ['text_answer' => 'She be a student.', 'is_correct' => false],
                ],
            ],

            // =====================================================
            // 2 - FILL - EASY
            // =====================================================
            [
                'type' => QuestionType::FILL->value,
                'difficulty' => 'EASY',
                'score' => 2,
                'title_question_en' => 'Grammar - The Verb "to be"',
                'title_question_ar' => 'القواعد - الفعل "to be"',
                'text_question' => 'Complete the sentence: They {1} from Syria.',
                'answers' => [
                    ['text_answer' => 'are', 'blank_order' => 1],
                ],
            ],

            // =====================================================
            // 3 - ARRANGE - EASY
            // =====================================================
            [
                'type' => QuestionType::ARRANGE->value,
                'difficulty' => 'EASY',
                'score' => 2,
                'title_question_en' => 'Sentence Structure - Word Order',
                'title_question_ar' => 'تركيب الجملة - ترتيب الكلمات',
                'text_question' => 'Arrange the words below to form a correct sentence.',
                'answers' => [
                    ['text_answer' => 'I', 'order' => 1, 'is_correct' => true],
                    ['text_answer' => 'like', 'order' => 2, 'is_correct' => true],
                    ['text_answer' => 'English', 'order' => 3, 'is_correct' => true],
                    ['text_answer' => 'very', 'order' => null, 'is_correct' => false],
                ],
            ],

            // =====================================================
            // 4 - PAIR - EASY
            // =====================================================
            [
                'type' => QuestionType::PAIR->value,
                'difficulty' => 'EASY',
                'score' => 2,
                'title_question_en' => 'Vocabulary - Basic Words',
                'title_question_ar' => 'المفردات - الكلمات الأساسية',
                'text_question' => 'Match each Arabic word with its correct English translation.',
                'answers' => [
                    ['left_text' => 'كتاب', 'right_text' => 'Book'],
                    ['left_text' => 'مدرسة', 'right_text' => 'School'],
                    ['left_text' => 'معلم', 'right_text' => 'Teacher'],
                ],
            ],

            // =====================================================
            // 5 - MCQ - MEDIUM
            // =====================================================
            [
                'type' => QuestionType::MCQ->value,
                'difficulty' => 'MEDIUM',
                'score' => 4,
                'title_question_en' => 'Grammar - Past Simple Tense',
                'title_question_ar' => 'القواعد - زمن الماضي البسيط',
                'text_question' => 'Choose the sentence that correctly uses the past simple tense.',
                'answers' => [
                    ['text_answer' => 'She went to the market yesterday.', 'is_correct' => true],
                    ['text_answer' => 'She go to the market yesterday.', 'is_correct' => false],
                    ['text_answer' => 'She gone to the market yesterday.', 'is_correct' => false],
                    ['text_answer' => 'She going to the market yesterday.', 'is_correct' => false],
                ],
            ],

            // =====================================================
            // 6 - FILL - MEDIUM
            // =====================================================
            [
                'type' => QuestionType::FILL->value,
                'difficulty' => 'MEDIUM',
                'score' => 4,
                'title_question_en' => 'Grammar - Present Perfect Tense',
                'title_question_ar' => 'القواعد - زمن المضارع التام',
                'text_question' => 'Complete the sentence using the present perfect: She {1} already finished her homework.',
                'answers' => [
                    ['text_answer' => 'has', 'blank_order' => 1],
                ],
            ],

            // =====================================================
            // 7 - ARRANGE - MEDIUM
            // =====================================================
            [
                'type' => QuestionType::ARRANGE->value,
                'difficulty' => 'MEDIUM',
                'score' => 4,
                'title_question_en' => 'Sentence Structure - Question Formation',
                'title_question_ar' => 'تركيب الجملة - صياغة السؤال',
                'text_question' => 'Arrange the words to form a correct present perfect continuous question.',
                'answers' => [
                    ['text_answer' => 'How', 'order' => 1, 'is_correct' => true],
                    ['text_answer' => 'long', 'order' => 2, 'is_correct' => true],
                    ['text_answer' => 'have', 'order' => 3, 'is_correct' => true],
                    ['text_answer' => 'you', 'order' => 4, 'is_correct' => true],
                    ['text_answer' => 'been', 'order' => 5, 'is_correct' => true],
                    ['text_answer' => 'here', 'order' => 6, 'is_correct' => true],
                    ['text_answer' => 'did', 'order' => null, 'is_correct' => false],
                ],
            ],

            // =====================================================
            // 8 - PAIR - MEDIUM
            // =====================================================
            [
                'type' => QuestionType::PAIR->value,
                'difficulty' => 'MEDIUM',
                'score' => 4,
                'title_question_en' => 'Grammar - Tenses Identification',
                'title_question_ar' => 'القواعد - تحديد الأزمنة',
                'text_question' => 'Match each grammar term with its corresponding example sentence.',
                'answers' => [
                    [
                        'left_text' => 'المضارع التام',
                        'right_text' => 'I have finished my work.'
                    ],
                    [
                        'left_text' => 'الماضي البسيط',
                        'right_text' => 'She visited London last year.'
                    ],
                    [
                        'left_text' => 'المستقبل باستخدام going to',
                        'right_text' => 'They are going to travel tomorrow.'
                    ],
                ],
            ],

            // =====================================================
            // 9 - MCQ - HARD
            // =====================================================
            [
                'type' => QuestionType::MCQ->value,
                'difficulty' => 'HARD',
                'score' => 7,
                'title_question_en' => 'Grammar - Second Conditional',
                'title_question_ar' => 'القواعد - الحالة الشرطية الثانية',
                'text_question' => 'Choose the sentence that correctly expresses a second conditional.',
                'answers' => [
                    [
                        'text_answer' => 'If I were rich, I would travel the world.',
                        'is_correct' => true
                    ],
                    [
                        'text_answer' => 'If I am rich, I would travel the world.',
                        'is_correct' => false
                    ],
                    [
                        'text_answer' => 'If I were rich, I will travel the world.',
                        'is_correct' => false
                    ],
                    [
                        'text_answer' => 'If I was rich, I will travel the world.',
                        'is_correct' => false
                    ],
                ],
            ],

            // =====================================================
            // 10 - FILL - HARD
            // =====================================================
            [
                'type' => QuestionType::FILL->value,
                'difficulty' => 'HARD',
                'score' => 7,
                'title_question_en' => 'Grammar - Third Conditional',
                'title_question_ar' => 'القواعد - الحالة الشرطية الثالثة',
                'text_question' => 'Complete the conditional sentence: If she had studied harder, she {1} the exam.',
                'answers' => [
                    ['text_answer' => 'would have passed', 'blank_order' => 1],
                ],
            ],
        ];

        foreach ($questions as $questionData) {
            $answers = $questionData['answers'];

            $question = Question::create([
                'user_id' => $userId,
                'type' => $questionData['type'],
                'score' => $questionData['score'],
                'title_question_en' => $questionData['title_question_en'],
                'title_question_ar' => $questionData['title_question_ar'],
                'text_question' => $questionData['text_question'],
                'difficulty' => $questionData['difficulty'],
                'previous_question_id' => null,
                'is_placement_question' => true,
            ]);

            $relation = $question->getAnswersRelationName();

            $question->{$relation}()->createMany($answers);
        }
    }
}
