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
            ContentStatus::DRAFT,
            ContentStatus::PENDING,
            ContentStatus::CHANGES_REQUESTED
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
        return $word->load('media');
         });
    }

    public function update(Word $word, array $data)
    {
        return DB::transaction(function () use ($word, $data) {
        if (!in_array($word->lesson->status, [
            ContentStatus::DRAFT,
            ContentStatus::PENDING,
            ContentStatus::CHANGES_REQUESTED
        ])) {
            throw ValidationException::withMessages([
                'word' => 'You cannot update word .',
            ]);
        }

        if (isset($data['audio'])) {
             $word->clearMediaCollection('audios');
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
        return $word->fresh();
        });
    }

    public function delete(Word $word)
    {
        if (!in_array($word->lesson->status, [
            ContentStatus::DRAFT,
            ContentStatus::PENDING,
            ContentStatus::CHANGES_REQUESTED
        ])) {
            throw ValidationException::withMessages([
                'word' => 'You cannot update word .',
            ]);
        }
        $word->delete();
        return ['word deleted  successfully'];
    }
}
