<?php

namespace App\Services\Scoring;

use App\Enums\QuestionType;
use InvalidArgumentException;

class QuestionScorerFactory
{
    public function make(QuestionType $type): QuestionScorer
    {
        return match ($type) {
            QuestionType::MCQ     => new McqScorer(),
            QuestionType::FILL    => new FillScorer(),
            QuestionType::ARRANGE => new ArrangeScorer(),
            QuestionType::PAIR    => new PairScorer(),
        };
    }
}
