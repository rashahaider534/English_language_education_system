<?php

namespace App\Services\Scoring;

use App\Models\Question;

class FillScorer implements QuestionScorer
{
    public function score(Question $question, array $submittedAnswer): int
    {
        $submittedBlanks = $submittedAnswer['answers'] ?? [];

        if (empty($submittedBlanks)) {
            return 0;
        }

        $correctAnswersByBlank = $question->fillAnswers
            ->groupBy('blank_order');

        $totalBlanks = $correctAnswersByBlank->count();

        if ($totalBlanks === 0) {
            return 0;
        }

        $correctCount = 0;

        foreach ($correctAnswersByBlank as $blankOrder => $acceptedAnswers) {
            $studentAnswer = $submittedBlanks[$blankOrder] ?? null;

            if ($studentAnswer === null) {
                continue;
            }

            $normalizedStudentAnswer = $this->normalize($studentAnswer);

            $isCorrect = $acceptedAnswers->contains(
                fn($accepted) => $this->normalize($accepted->text_answer) === $normalizedStudentAnswer
            );

            if ($isCorrect) {
                $correctCount++;
            }
        }

        return (int) round(($correctCount / $totalBlanks) * $question->score);
    }

    private function normalize(string $text): string
    {
        return trim(mb_strtolower($text));
    }
}
