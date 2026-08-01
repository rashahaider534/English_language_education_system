<?php

namespace App\Services\Word;

use App\Models\Lesson;
use App\Models\User;
use App\Models\Word;
use App\Enums\ContentStatus;
use Illuminate\Validation\ValidationException;

class TeacherWordService
{
    public function create(Lesson $lesson, array $data)
    {
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
        return $word;
    }

    public function update(Word $word, array $data)
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
        $word->update(
            [
                'word_en' => $data['word_en'],
                'word_ar' => $data['word_ar']
            ]
        );
        return $word->fresh();
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
        return ['word deleted  successfully'];
    }
}
