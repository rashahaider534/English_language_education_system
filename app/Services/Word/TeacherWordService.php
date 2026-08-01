<?php

namespace App\Services\Word;

use App\Models\Lesson;
use App\Models\User;
use App\Models\Word;
use App\Enums\ContentStatus;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
class TeacherWordService
{
    public function create(Lesson $lesson, array $data)
    {
         return DB::transaction(function () use ($lesson, $data) {
        if (!in_array($lesson->status, [
            ContentStatus::DRAFT->value,
            ContentStatus::PENDING->value,
            ContentStatus::CHANGES_REQUESTED->value
        ])) {
            throw ValidationException::withMessages([
                'word' => 'You cannot add word to this lesson.',
            ]);
        }
        $word = Word::create(
            [
                'lesson_id' => $lesson->id,
                'word_en' => $data['word_en'],
                'word_ar' => $data['word_ar']
            ]
        );
        if (isset($data['audio'])) {
                $word
                    ->addMedia($data['audio'])
                    ->toMediaCollection('audios');
            }
        Cache::tags(['words', 'lesson_'.$lesson->id])->flush();
        return $word->load('media');
         });
    }

    public function update(Word $word, array $data)
    {
        return DB::transaction(function () use ($word, $data) {
        if (!in_array($word->lesson->status, [
            ContentStatus::DRAFT->value,
            ContentStatus::PENDING->value,
            ContentStatus::CHANGES_REQUESTED->value
        ])) {
            throw ValidationException::withMessages([
                'word' => 'You cannot update word .',
            ]);
        }

        if (isset($data['audio'])) {
                $word
                    ->addMedia($data['audio'])
                    ->toMediaCollection('audios');
            }
        $word->update(
            [
                'word_en' => $data['word_en'],
                'word_ar' => $data['word_ar']
            ]
        );
        Cache::tags(['words', 'lesson_'.$word->lesson->id])->flush();
        return $word->fresh();
        });
    }

    public function delete(Word $word)
    {
        if (!in_array($word->lesson->status, [
            ContentStatus::DRAFT->value,
            ContentStatus::PENDING->value,
            ContentStatus::CHANGES_REQUESTED->value
        ])) {
            throw ValidationException::withMessages([
                'word' => 'You cannot update word .',
            ]);
        }
        $word->delete();
        Cache::tags(['words', 'lesson_'.$word->lesson->id])->flush();
        return ['word deleted  successfully'];
    }
}
