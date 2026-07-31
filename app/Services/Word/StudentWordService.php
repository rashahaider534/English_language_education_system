<?php

namespace App\Services\Word;

use App\Enums\WordStatus;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Word;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class StudentWordService
{

    public function getLessonWords(Lesson $lesson)
    {
        return Cache::tags(['words', 'lesson_' . $lesson->id])
            ->remember(
                "lesson.{$lesson->id}.words",
                3600,
                fn() => $lesson->words()
                    ->get()
            );
    }

    public function saveWordStatus(User $user, Word $word, WordStatus $status)
    {
        $canAccess = $user->lessons()
            ->where('lesson_id', $word->lesson_id)
            ->wherePivotIn('status', [
                'in_progress',
                'completed',
            ])
            ->exists();

        if (! $canAccess) {
            throw ValidationException::withMessages([
                'word' => 'You cannot add this word to your word bank.',
            ]);
        }

        $exists = $user->words()
            ->where('word_id', $word->id)
            ->exists();

        if ($exists) {
            $user->words()->updateExistingPivot(
                $word->id,
                [
                    'status' => $status,
                ]
            );
        } else {
            $user->words()->attach(
                $word->id,
                [
                    'status' => $status,
                    'added_at' => now(),
                ]
            );
        }

        return $user->words()
            ->where('word_id', $word->id)
            ->with('lesson')
            ->first();
    }

    public function getKnowWords(User $user)
    {
        return $user->words()
            ->wherePivot('status', WordStatus::KNOW)
            ->with('lesson')
            ->paginate(20);
    }

    public function getWordsByStatus(User $user, WordStatus $status)
    {
        return $user->words()
            ->wherePivot('status', $status->value)
            ->with('lesson')
            ->paginate(20);
    }
}
