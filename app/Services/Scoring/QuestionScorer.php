<?php

namespace App\Services\Scoring;

use App\Models\Question;

interface QuestionScorer
{
    public function score(Question $question, array $submittedAnswer): int;
}
