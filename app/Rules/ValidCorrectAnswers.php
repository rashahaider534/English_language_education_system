<?php

namespace App\Rules;

use App\Enums\QuestionType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCorrectAnswers implements ValidationRule
{
    public function __construct(
        private string $type,
        private array $answers
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $correctAnswers = collect($this->answers)
            ->where('is_correct', true)
            ->count();

        if ($this->type === QuestionType::MCQ->value) {

            if ($correctAnswers === 0) {
                $fail('MCQ questions must contain one correct answer.');
                return;
            }

            if ($correctAnswers > 1) {
                $fail('MCQ questions cannot contain more than one correct answer.');
                return;
            }
        }

        if ($this->type === QuestionType::ARRANGE->value) {

            if ($correctAnswers < 2) {
                $fail('Arrange questions must contain at least two correct items.');
            }
        }
    }
}
