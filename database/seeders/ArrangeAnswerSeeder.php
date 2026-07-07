<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArrangeAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // سنقوم بتعريف الكلمات:
        // 'text' => الكلمة
        // 'is_correct' => هل هي جزء من الحل؟
        $questionsData = [
            3 => [
                ['text' => 'Learning', 'is_correct' => true],
                ['text' => 'is', 'is_correct' => true],
                ['text' => 'a', 'is_correct' => true],
                ['text' => 'journey', 'is_correct' => true],
                ['text' => 'car', 'is_correct' => false], // تشتيت
                ['text' => 'book', 'is_correct' => false], // تشتيت
            ],
            4 => [
                ['text' => 'Software', 'is_correct' => true],
                ['text' => 'engineering', 'is_correct' => true],
                ['text' => 'is', 'is_correct' => true],
                ['text' => 'creative', 'is_correct' => true],
                ['text' => 'music', 'is_correct' => false],
            ],
            10 => [
                ['text' => 'Coding', 'is_correct' => true],
                ['text' => 'solves', 'is_correct' => true],
                ['text' => 'complex', 'is_correct' => true],
                ['text' => 'problems', 'is_correct' => true],
                ['text' => 'fast', 'is_correct' => false], // تشتيت
                ['text' => 'logic', 'is_correct' => false], // تشتيت
            ],
            12 => [
                ['text' => 'Laravel', 'is_correct' => true],
                ['text' => 'makes', 'is_correct' => true],
                ['text' => 'backend', 'is_correct' => true],
                ['text' => 'easy', 'is_correct' => true],
                ['text' => 'hard', 'is_correct' => false], // تشتيت
                ['text' => 'PHP', 'is_correct' => false], // تشتيت
            ],
            14 => [
                ['text' => 'Practice', 'is_correct' => true],
                ['text' => 'makes', 'is_correct' => true],
                ['text' => 'your', 'is_correct' => true],
                ['text' => 'code', 'is_correct' => true],
                ['text' => 'perfect', 'is_correct' => true],
                ['text' => 'work', 'is_correct' => false], // تشتيت
                ['text' => 'slow', 'is_correct' => false], // تشتيت
            ],
            16 => [
                ['text' => 'Database', 'is_correct' => true],
                ['text' => 'design', 'is_correct' => true],
                ['text' => 'is', 'is_correct' => true],
                ['text' => 'crucial', 'is_correct' => true],
                ['text' => 'important', 'is_correct' => false], // تشتيت (معنى قريب للتمويه)
                ['text' => 'files', 'is_correct' => false], // تشتيت
            ],
        ];

        foreach ($questionsData as $questionId => $items) {
            $orderCounter = 1;

            // نقوم بخلط المصفوفة عشوائياً حتى لا تظهر كلمات التشتيت دائماً في النهاية
            shuffle($items);

            foreach ($items as $item) {
                DB::table('arrange_answers')->insert([
                    'question_id'  => $questionId,
                    'text_answer'  => $item['text'],
                    'is_correct'   => $item['is_correct'],
                    // إذا كانت صحيحة نعطيها ترتيب، إذا كانت خاطئة نضع null
                    'order'        => $item['is_correct'] ? $orderCounter++ : null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
    }
}
