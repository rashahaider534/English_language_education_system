<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\Test;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        // 1. تعريف الاختبارات
//        $tests = [
//            [
//                'title_ar' => 'اختبار الكورس الأول',
//                'title_en' => 'Course 1 Final',
//                'type' => 'course',
//                'id' => 1,
//                'status' => ContentStatus::PUBLISHED->value,
//            ],
//            [
//                'title_ar' => 'اختبار الدرس الثاني',
//                'title_en' => 'Lesson 2 Quiz',
//                'type' => 'lesson',
//                'id' => 2,
//                'status' => ContentStatus::PENDING->value,
//            ],
//            [
//                'title_ar' => 'اختبار المستوى المتقدم',
//                'title_en' => 'Advanced Level Test',
//                'type' => 'level',
//                'id' => 3,
//                'status' => ContentStatus::ARCHIVED->value,
//            ],
//        ];
//
//        foreach ($tests as $test) {
//            DB::table('tests')->insert([
//                'id' => $test['id'],
//                'testable_id' => 1,
//                'testable_type' => $test['type'],
//                'passing_score' => 50,
//                'title_en' => $test['title_en'],
//                'title_ar' => $test['title_ar'],
//                'status' => $test['status'],
//                'created_at' => now(),
//                'updated_at' => now(),
//            ]);
//        }
//
//        // 2. إضافة أول 4 أسئلة إلى جميع الاختبارات
//        $testQuestions = [];
//
//        foreach ($tests as $test) {
//            for ($questionId = 1; $questionId <= 4; $questionId++) {
//                $testQuestions[] = [
//                    'test_id' => $test['id'],
//                    'question_id' => $questionId,
//                    'order' => $questionId,
//                    'created_at' => now(),
//                    'updated_at' => now(),
//                ];
//            }
//        }
//
//        DB::table('test_questions')->insert($testQuestions);

        $currentQuestionId = 1;
        $courseQuestionPools = [];

        // =====================================================
        // 1. Lesson Tests (اختبارات الدروس)
        // =====================================================
        $lessons = Lesson::all();
        foreach ($lessons as $lesson) {
            $test = Test::create([
                'testable_type' => 'lesson',
                'testable_id' => $lesson->id,
                'passing_score' => 60,
                'title_en' => 'Test: ' . $lesson->title_en,
                'title_ar' => 'اختبار: ' . $lesson->title_ar,
                'status' => 'pending',
            ]);

            $lessonQuestionIds = [];
            for ($i = 1; $i <= 4; $i++) {
                $lessonQuestionIds[] = $currentQuestionId;

                DB::table('test_questions')->insert([
                    'test_id' => $test->id,
                    'question_id' => $currentQuestionId,
                    'order' => $i,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $currentQuestionId++;
            }

            // تخزين أسئلة الدروس لتجميعها لاحقاً حسب الكورس
            $courseQuestionPools[$lesson->course_id][] = $lessonQuestionIds;
        }

        // تجميع أسئلة الكورسات (كل كورس لديه 3 دروس × 4 أسئلة = 12 سؤالاً في المسبح)
        $flattenedCoursePools = [];
        foreach ($courseQuestionPools as $courseId => $lessonPools) {
            $flattenedCoursePools[$courseId] = array_merge(...$lessonPools);
        }

        // =====================================================
        // 2. Course Tests (اختبارات الكورسات)
        // =====================================================
        $courses = Course::all();
        $levelQuestionPools = [];

        foreach ($courses as $course) {
            $test = Test::create([
                'testable_type' => 'course',
                'testable_id' => $course->id,
                'passing_score' => 70,
                'title_en' => 'Course Test: ' . $course->name_en,
                'title_ar' => 'اختبار الكورس: ' . $course->name_ar,
                'status' => 'pending',
            ]);

            // اختيار أول 4 أسئلة (أو يمكن استخدام array_rand) من مسبح أسئلة دروس الكورس نفسه
            $pool = $flattenedCoursePools[$course->id] ?? [];
            $selectedQuestions = array_slice($pool, 0, 4);

            foreach ($selectedQuestions as $index => $qId) {
                DB::table('test_questions')->insert([
                    'test_id' => $test->id,
                    'question_id' => $qId,
                    'order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // تجميع أسئلة الكورسات لتحديد مسبح أسئلة المستويات لاحقاً
            $levelQuestionPools[$course->level_id][] = $pool;
        }

        // تجميع أسئلة المستويات (تضم جميع أسئلة دروس الكورسات التابعة للمستوى)
        $flattenedLevelPools = [];
        foreach ($levelQuestionPools as $levelId => $pools) {
            $flattenedLevelPools[$levelId] = array_merge(...$pools);
        }

        // =====================================================
        // 3. Level Tests (اختبارات المستويات)
        // =====================================================
        $levels = Level::all();
        foreach ($levels as $level) {
            $test = Test::create([
                'testable_type' => 'level',
                'testable_id' => $level->id,
                'passing_score' => 75,
                'title_en' => 'Level Test: ' . $level->name_en,
                'title_ar' => 'اختبار المستوى: ' . $level->name_ar,
                'status' => 'pending',
            ]);

            // اختيار 4 أسئلة حصراً من مسبح أسئلة دروس كورسات هذا المستوى
            $pool = $flattenedLevelPools[$level->id] ?? [];
            $selectedQuestions = array_slice($pool, 0, 4);

            foreach ($selectedQuestions as $index => $qId) {
                DB::table('test_questions')->insert([
                    'test_id' => $test->id,
                    'question_id' => $qId,
                    'order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

    }
}
