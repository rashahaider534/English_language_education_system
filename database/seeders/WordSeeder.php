<?php

namespace Database\Seeders;

use App\Models\Word;
use Illuminate\Database\Seeder;

class WordSeeder extends Seeder
{
    public function run(): void
    {
        $words = [];

        $lessonWords = [

            // Lesson 1 - Nouns
            1 => [
                ['en' => 'Book', 'ar' => 'كتاب'],
                ['en' => 'Table', 'ar' => 'طاولة'],
                ['en' => 'Teacher', 'ar' => 'معلم'],
                ['en' => 'Student', 'ar' => 'طالب'],
                ['en' => 'House', 'ar' => 'منزل'],
            ],

            // Lesson 2 - Pronouns
            2 => [
                ['en' => 'I', 'ar' => 'أنا'],
                ['en' => 'You', 'ar' => 'أنت'],
                ['en' => 'He', 'ar' => 'هو'],
                ['en' => 'She', 'ar' => 'هي'],
                ['en' => 'They', 'ar' => 'هم'],
            ],

            // Lesson 3 - Present Simple
            3 => [
                ['en' => 'Eat', 'ar' => 'يأكل'],
                ['en' => 'Drink', 'ar' => 'يشرب'],
                ['en' => 'Study', 'ar' => 'يدرس'],
                ['en' => 'Play', 'ar' => 'يلعب'],
                ['en' => 'Work', 'ar' => 'يعمل'],
            ],

        ];

        foreach ($lessonWords as $lessonId => $lessonVocabulary) {

            foreach ($lessonVocabulary as $word) {

                $words[] = [
                    'lesson_id' => $lessonId,
                    'word_en' => $word['en'],
                    'word_ar' => $word['ar'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Word::insert($words);
    }
}
