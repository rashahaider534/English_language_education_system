<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidFillQuestion implements ValidationRule
{
    public function __construct(
        private array $answers
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // استخراج جميع الـ Placeholders بالشكل {1}
        preg_match_all('/\{(\d+)\}/', $value, $matches);

        $placeholders = array_map('intval', $matches[1]);

        /*
        |--------------------------------------------------------------------------
        | 1. يجب وجود Placeholder واحد على الأقل
        |--------------------------------------------------------------------------
        */

        if (empty($placeholders)) {
            $fail('The question must contain at least one placeholder like {1}.');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 2. منع أي شيء داخل {} ليس رقماً
        |--------------------------------------------------------------------------
        */

        preg_match_all('/\{([^}]*)\}/', $value, $allBrackets);

        foreach ($allBrackets[1] as $content) {

            if (!preg_match('/^\d+$/', $content)) {

                $fail('Only placeholders in the form {number} are allowed.');

                return;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3. منع التكرار
        |--------------------------------------------------------------------------
        */

        if (count($placeholders) !== count(array_unique($placeholders))) {

            $fail('Placeholder numbers must be unique.');

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 4. يجب أن تكون متسلسلة
        |--------------------------------------------------------------------------
        */

        sort($placeholders);

        $expected = range(1, count($placeholders));

        if ($placeholders !== $expected) {

            $fail('Placeholder numbers must be sequential starting from {1}.');

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 5. عدد الفراغات = عدد الإجابات
        |--------------------------------------------------------------------------
        */

        if (count($placeholders) !== count($this->answers)) {

            $fail('The number of placeholders must equal the number of answers.');

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 6. blank_order يجب أن يطابق الأرقام الموجودة
        |--------------------------------------------------------------------------
        */

        $answerOrders = collect($this->answers)
            ->pluck('blank_order')
            ->map(fn ($v) => (int) $v)
            ->sort()
            ->values()
            ->all();

        $placeholderSet = collect($placeholders)->sort()->values();
        $answerSet = collect($answerOrders)->sort()->values();

        /*
        |--------------------------------------------------------------------------
        | 1. ناقص placeholders
        |--------------------------------------------------------------------------
        */
        $missingInAnswers = $placeholderSet->diff($answerSet);

        if ($missingInAnswers->isNotEmpty()) {
            $fail('Missing blank_order(s): ' . $missingInAnswers->implode(', '));
        }

        /*
        |--------------------------------------------------------------------------
        | 2. زائد أو غير موجود بالنص
        |--------------------------------------------------------------------------
        */
        $extraInAnswers = $answerSet->diff($placeholderSet);

        if ($extraInAnswers->isNotEmpty()) {
            $fail('Invalid blank_order(s) not in question: ' . $extraInAnswers->implode(', '));
        }
            return;
        }
}
