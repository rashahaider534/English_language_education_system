<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PairAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pairsData = [
            7  => [
                ['left' => 'برمجة خلفية', 'right' => 'Backend'],
                ['left' => 'قاعدة بيانات', 'right' => 'Database'],
                ['left' => 'إطار عمل', 'right' => 'Framework'],
                ['left' => 'مستودع بيانات', 'right' => 'Repository'],
            ],
            9  => [
                ['left' => 'تفاحة', 'right' => 'Apple'],
                ['left' => 'سيارة', 'right' => 'Car'],
                ['left' => 'كلب', 'right' => 'Dog'],
                ['left' => 'مطرقة', 'right' => 'Hammer'],
            ],
            13 => [
                ['left' => 'دافئ', 'right' => 'Warm'],
                ['left' => 'بارد', 'right' => 'Cold'],
                ['left' => 'كبير', 'right' => 'Big'],
                ['left' => 'صغير', 'right' => 'Small'],
            ],
            17 => [
                ['left' => 'دائرة', 'right' => 'Circle'],
                ['left' => 'مربع', 'right' => 'Square'],
                ['left' => 'مثلث', 'right' => 'Triangle'],
                ['left' => 'خط', 'right' => 'Line'],
            ],
        ];

        foreach ($pairsData as $questionId => $pairs) {
            foreach ($pairs as $pair) {
                DB::table('pair_answers')->insert([
                    'question_id' => $questionId,
                    'left_text'   => $pair['left'],   // عربي
                    'right_text'  => $pair['right'],  // إنجليزي
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }
}
