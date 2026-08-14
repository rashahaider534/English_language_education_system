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
        return $lesson->words()
                    ->with('media')
                    ->get();

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
            ->first()->load('media');
    }

    public function getKnowWords(User $user)
    {
        return $user->words()
            ->wherePivot('status', WordStatus::KNOW)
            ->with(['lesson','media'])
            ->paginate(20);
    }

    public function getWordsByStatus(User $user, WordStatus $status)
    {
        return $user->words()
            ->wherePivot('status', $status->value)
           ->with(['lesson','media'])
            ->paginate(20)->load('media');
    }

    public function checkAnswer(User $user, Word $word, array $data): array
    {
        $answerId = (int) $data['answer_id'];

        $exists = $user->words()
            ->where('word_id', $word->id)
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'word' => 'This word does not belong to your word bank.',
            ]);
        }

        $isCorrect = $word->id === $answerId;

        return [
            'correct' => $isCorrect,
            'message' => $isCorrect
                ? 'Answer is correct'
                : 'Answer is incorrect',
            'correct_answer_id' => $isCorrect ? null : $word->id,
        ];
    }

    public function quizWords(User $user)
    {
        $quizWords = $user->words()
            ->with('media')
            ->orderByPivot('added_at', 'desc')
            ->take(10)
            ->get();

        return $quizWords->map(function ($word) {
            return [
                'Quiz' => $this->buildQuestion($word),
            ];
        });
    }

    private function buildQuestion(Word $word): array
    {
        $options = $this->options($word);

        $englishToArabic = (bool) random_int(0, 1);

        $question = $englishToArabic
            ? $word->word_en
            : $word->word_ar;

        $optionField = $englishToArabic
            ? 'word_ar'
            : 'word_en';

        return [
            'word_id' => $word->id,
            'question' => $question,
            'audio' => $word->getFirstMediaUrl('audios'),
            'options' => $options->map(function ($option) use ($optionField) {
                return [
                    'id' => $option->id,
                    'text' => $option->{$optionField},
                ];
            })->values(),
        ];
    }

    private function options(Word $word)
    {
        $options = Word::query()
            ->select('id', 'word_en', 'word_ar')
            ->whereKeyNot($word->id)
            ->whereHas('lesson', function ($query) use ($word) {
                $query->where('course_id', $word->lesson->course_id);
            })
            ->inRandomOrder()
            ->limit(3)
            ->get();

        $options->push(
            Word::query()
                ->select('id', 'word_en', 'word_ar')
                ->findOrFail($word->id)
        );

        return $options
            ->shuffle()
            ->values();
    }
}
