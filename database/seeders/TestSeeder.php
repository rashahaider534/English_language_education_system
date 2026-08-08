<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. تعريف الاختبارات
        $tests = [
            [
                'title_ar' => 'اختبار الكورس الأول',
                'title_en' => 'Course 1 Final',
                'type' => 'course',
                'id' => 1,
                'status' => ContentStatus::PUBLISHED->value,
            ],
            [
                'title_ar' => 'اختبار الدرس الثاني',
                'title_en' => 'Lesson 2 Quiz',
                'type' => 'lesson',
                'id' => 2,
                'status' => ContentStatus::PENDING->value,
            ],
            [
                'title_ar' => 'اختبار المستوى المتقدم',
                'title_en' => 'Advanced Level Test',
                'type' => 'level',
                'id' => 3,
                'status' => ContentStatus::ARCHIVED->value,
            ],
        ];

        foreach ($tests as $test) {
            DB::table('tests')->insert([
                'id' => $test['id'],
                'testable_id' => 1,
                'testable_type' => $test['type'],
                'passing_score' => 50,
                'title_en' => $test['title_en'],
                'title_ar' => $test['title_ar'],
                'status' => $test['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. إضافة أول 4 أسئلة إلى جميع الاختبارات
        $testQuestions = [];

        foreach ($tests as $test) {
            for ($questionId = 1; $questionId <= 4; $questionId++) {
                $testQuestions[] = [
                    'test_id' => $test['id'],
                    'question_id' => $questionId,
                    'order' => $questionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('test_questions')->insert($testQuestions);
    }
}
