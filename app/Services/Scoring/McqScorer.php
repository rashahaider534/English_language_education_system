<?php

namespace App\Services\Scoring;
use App\Models\Question;
class McqScorer implements QuestionScorer
{
    public function score(Question $question, array $submittedAnswer): int
    {
        $selectedAnswerId = $submittedAnswer['selected_answer_id'] ?? null;

        if (!$selectedAnswerId) {
            return 0;
        }

        $isCorrect = $question->mcqAnswers()
            ->where('id', $selectedAnswerId)
            ->where('is_correct', true)
            ->exists();

        return $isCorrect ? $question->score : 0;
    }
}
