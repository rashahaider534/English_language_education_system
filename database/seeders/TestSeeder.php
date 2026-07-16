<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. تعريف مصفوفة الاختبارات
        $tests = [
            ['title_ar' => 'اختبار الكورس الأول', 'title_en' => 'Course 1 Final', 'type' => 'course', 'id' => 1, 'status' => ContentStatus::PUBLISHED->value],
            ['title_ar' => 'اختبار الدرس الثاني', 'title_en' => 'Lesson 2 Quiz', 'type' => 'lesson', 'id' => 2, 'status' => ContentStatus::PENDING->value],
            ['title_ar' => 'اختبار المستوى المتقدم', 'title_en' => 'Advanced Level Test', 'type' => 'level', 'id' => 3, 'status' => ContentStatus::ARCHIVED->value],
        ];

        foreach ($tests as $test) {
            DB::table('tests')->insert([
                'id' => $test['id'],
                'testable_id' => 1, // معرف عشوائي للمورف
                'testable_type' => $test['type'],
                'passing_score' => 50,
                'title_en' => $test['title_en'],
                'title_ar' => $test['title_ar'],
                'status' => $test['status'],
                'created_at' => now(),
            ]);
        }

        // 2. ربط الأسئلة بالاختبارات (جدول test_questions)
        // سنربط الأسئلة من 1 إلى 18، ونترك 19 و 20 فارغين (أسئلة حرة)
        $testQuestions = [];

        // توزيع 18 سؤال على 3 اختبارات
        for ($i = 1; $i <= 18; $i++) {
            $testQuestions[] = [
                'test_id'     => ($i % 3) + 1, // توزيع دوري على الاختبارات الثلاثة
                'question_id' => $i,
                'order'       => $i,
                'created_at'  => now(),
            ];
        }

        DB::table('test_questions')->insert($testQuestions);
    }
}
