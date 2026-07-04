<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $questions = [];

        // تحديد الأسئلة ومعرفاتها وأنواعها بشكل ثابت
        $dataMap = [
            ['ids' => [1, 2, 5, 6, 15, 20], 'type' => 'MCQ'],
            ['ids' => [8, 11, 18, 19],      'type' => 'FILL'],
            ['ids' => [3, 4, 10, 12, 14, 16], 'type' => 'ARRANGE'],
            ['ids' => [7, 9, 13, 17],       'type' => 'PAIR'],
        ];

        $difficulties = ['EASY', 'MEDIUM', 'HARD'];
        $userIds = [5, 6, 7];

        // نقوم بإنشاء 20 سؤالاً ونحدد بيانات كل واحد بدقة
        for ($i = 1; $i <= 20; $i++) {

            // البحث عن النوع المناسب لهذا الـ ID
            $type = 'MCQ'; // القيمة الافتراضية
            foreach ($dataMap as $group) {
                if (in_array($i, $group['ids'])) {
                    $type = $group['type'];
                    break;
                }
            }

            $questions[] = [
                'id'                => $i, // تثبيت المعرف
                'user_id'           => $faker->randomElement($userIds),
                'type'              => $type, // النوع المرتبط بالأجوبة
                'score'             => $faker->numberBetween(2, 10),
                'title_question_en' => "Test $type - Question $i",
                'title_question_ar' => "اختبار $type - سؤال $i",
                'text_question'     => "هذا هو نص السؤال رقم $i من نوع $type.",
                'difficulty'        => $faker->randomElement($difficulties),
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        // إدخال البيانات دفعة واحدة
        DB::table('questions')->insert($questions);
    }

}
