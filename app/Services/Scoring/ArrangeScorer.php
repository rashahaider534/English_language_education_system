<?php

namespace App\Services\Scoring;

use App\Models\Question;

class ArrangeScorer implements QuestionScorer
{
    public function score(Question $question, array $submittedAnswer): int
    {
        $submittedOrder = $submittedAnswer['ordered_ids'] ?? [];

        if (empty($submittedOrder)) {
            return 0;
        }

        // بس العناصر الصحيحة (استبعاد المشتتات: is_correct=false أو order=null)
        $correctOrder = $question->arrangeAnswers
            ->where('is_correct', true)
            ->sortBy('order')
            ->pluck('id')
            ->values()
            ->all();

        $totalItems = count($correctOrder);

        if ($totalItems === 0) {
            return 0;
        }

        $correctPositions = 0;

        foreach ($correctOrder as $index => $correctId) {
            if (($submittedOrder[$index] ?? null) == $correctId) {
                $correctPositions++;
            }
        }

        return (int) round(($correctPositions / $totalItems) * $question->score);
    }
}
