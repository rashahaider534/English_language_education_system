<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class McqAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mcqQuestionIds = [1, 2, 5, 6, 15, 20];

        foreach ($mcqQuestionIds as $questionId) {
            for ($j = 1; $j <= 3; $j++) {
                DB::table('mcq_answers')->insert([
                    'question_id' => $questionId,
                    'text_answer' => "إجابة رقم $j للسؤال $questionId",
                    'is_correct'  => ($j === 1),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }
}
