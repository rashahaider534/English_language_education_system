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
      | 5. عدد الفراغات الفريدة = عدد blank_order الفريدة بالإجابات
      |    (بدل عدد صفوف الإجابات الخام، عشان نسمح أكتر من إجابة لنفس الفراغ)
      |--------------------------------------------------------------------------
      */

        $answerOrders = collect($this->answers)
            ->pluck('blank_order')
            ->map(fn ($v) => (int) $v);

        $uniqueAnswerOrders = $answerOrders->unique()->values();

        if (count($placeholders) !== $uniqueAnswerOrders->count()) {
            $fail('The number of unique blanks must equal the number of unique blank_order values in answers.');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 6. blank_order يجب أن يطابق الأرقام الموجودة بالنص (بدون تكرار بالمقارنة)
        |--------------------------------------------------------------------------
        */

        $placeholderSet = collect($placeholders)->unique()->sort()->values();
        $answerSet = $uniqueAnswerOrders->sort()->values();

        // ناقص placeholders (فراغ موجود بالنص، ما إله أي إجابة)
        $missingInAnswers = $placeholderSet->diff($answerSet);

        if ($missingInAnswers->isNotEmpty()) {
            $fail('Missing blank_order(s): ' . $missingInAnswers->implode(', '));
            return;
        }

        // زائد أو غير موجود بالنص (إجابة لفراغ مش موجود بالنص أصلاً)
        $extraInAnswers = $answerSet->diff($placeholderSet);

        if ($extraInAnswers->isNotEmpty()) {
            $fail('Invalid blank_order(s) not in question: ' . $extraInAnswers->implode(', '));
            return;
        }
    }
}
