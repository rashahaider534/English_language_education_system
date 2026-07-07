<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidArrangeOrder implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function __construct(
        private array $answers
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $orders = collect($this->answers)
            ->pluck('order')
            ->map(fn($v) => (int)$v)
            ->sort()
            ->values()
            ->all();

        $expected = range(1, count($orders));

        if ($orders !== $expected) {
            $fail('The order values must be sequential starting from 1.');
        }
    }
}
