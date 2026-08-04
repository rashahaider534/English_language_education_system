<?php

namespace Database\Seeders;

use App\Models\Word;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\WordStatus;
use Illuminate\Support\Facades\DB;

class WordSeeder extends Seeder
{
    public function run(): void
    {
        $words = [];

        $lessonWords = [

            4 => [
                ['en' => 'Eat', 'ar' => 'يأكل'],
                ['en' => 'Drink', 'ar' => 'يشرب'],
                ['en' => 'run', 'ar' => 'يركض'],
                ['en' => 'see', 'ar' => 'يرى']

            ],

            5 => [
                ['en' => 'I', 'ar' => 'أنا'],
                ['en' => 'You', 'ar' => 'أنت'],
                ['en' => 'He', 'ar' => 'هو'],
                ['en' => 'She', 'ar' => 'هي']

            ],
             1 => [
                ['en' => 'I', 'ar' => 'أنا'],
                ['en' => 'You', 'ar' => 'أنت'],
                ['en' => 'He', 'ar' => 'هو'],
                ['en' => 'She', 'ar' => 'هي']

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
        $word4 = Word::find(1);
        $word4
            ->addMedia(database_path('seeders/audios/test2.wav'))
            ->preservingOriginal()
            ->toMediaCollection('audios');

        $word5 = Word::find(2);
        $word5
            ->addMedia(database_path('seeders/audios/test2.wav'))
            ->preservingOriginal()
            ->toMediaCollection('audios');

        $user = User::find(2);
        $rows = [];
        foreach (Word::take(8)->get() as $index => $word) {

            $rows[] = [
                'user_id'   => $user->id,
                'word_id'   => $word->id,
                'status'    => $index < 4
                    ? WordStatus::KNOW->value
                    : WordStatus::LEARNING->value,
                'added_at'  => now()->subDays(rand(1, 10)),
            ];
        }
        DB::table('user_words')->insert($rows);
    }
}
