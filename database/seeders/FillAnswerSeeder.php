<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FillAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fillQuestionIds = [8, 11, 18, 19];

        foreach ($fillQuestionIds as $questionId) {
            // لنفترض أن كل سؤال من هؤلاء لديه فراغين (يمكنك زيادة العدد أو جعله عشوائياً)
            for ($blank = 1; $blank <= 2; $blank++) {

                DB::table('fill_answers')->insert([
                    'question_id' => $questionId,
                    'text_answer' => "الإجابة الصحيحة للفراغ رقم $blank في السؤال $questionId",
                    'blank_order' => $blank,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }
}
