<?php

namespace App\Services\Scoring;

use App\Models\Question;

class PairScorer implements QuestionScorer
{
    public function score(Question $question, array $submittedAnswer): int
    {
        $submittedPairs = $submittedAnswer['pairs'] ?? [];

        if (empty($submittedPairs)) {
            return 0;
        }

        $totalPairs = $question->pairAnswers()->count();

        if ($totalPairs === 0) {
            return 0;
        }

        $correctCount = 0;

        foreach ($submittedPairs as $leftId => $rightId) {
            if ((int) $leftId === (int) $rightId) {
                $correctCount++;
            }
        }

        return (int) round(($correctCount / $totalPairs) * $question->score);
    }
}
