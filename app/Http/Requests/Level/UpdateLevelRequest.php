<?php

namespace App\Http\Requests\Level;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLevelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $level = $this->route('level');
        return [
            'name_en' => [
                'sometimes',
                'filled',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
                Rule::unique('levels', 'name_en')->ignore($level->id),
            ],
            'name_ar' => [
                'sometimes',
                'filled',
                'string',
                'max:255',
                'regex:/^[\x{0600}-\x{06FF}\s0-9\-_]+$/u',
                Rule::unique('levels', 'name_ar')->ignore($level->id),
            ],

            'order' => ['sometimes','filled', 'integer', 'min:0', Rule::unique('levels', 'order')->ignore($level->id),],

            'minimum_score' => [
                'sometimes',
                'filled',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $max = $this->input('maximum_score', null);

                    if ($max !== null && $value >= $max) {
                        $fail('أدنى علامة يجب أن تكون أقل من أعلى علامة.');
                    }
                }
            ],

            'maximum_score' => [
                'sometimes',
                'filled',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $min = $this->input('minimum_score', null);

                    if ($min !== null && $value <= $min) {
                        $fail('أعلى علامة يجب أن تكون أكبر من أدنى علامة.');
                    }
                }
            ],

            'price' => ['sometimes', 'filled','numeric', 'min:0'],

            'estimated_duration' => ['sometimes','filled', 'integer', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name_en' => 'الاسم بالإنكليزي',
            'name_ar' => 'الاسم بالعربي',
            'order' => 'الترتيب',
            'minimum_score' => 'أدنى علامة',
            'maximum_score' => 'أعلى علامة',
            'price' => 'السعر',
            'estimated_duration' => 'المدة المتوقعة',
        ];
    }

    public function messages(): array
    {
        return [
            'name_en.regex' => 'الاسم بالإنكليزي يجب أن يحتوي أحرف إنكليزية وأرقام فقط.',
            'name_en.unique' => 'هذا الاسم بالإنكليزي مستخدم مسبقًا لمستوى آخر.',
            'name_ar.regex' => 'الاسم بالعربي يجب أن يحتوي أحرف عربية فقط.',
            'name_ar.unique' => 'هذا الاسم بالعربي مستخدم مسبقًا لمستوى آخر.',
            'order.integer' => 'الترتيب يجب أن يكون رقمًا صحيحًا.',
            'order.unique' => 'هذا الترتيب مستخدم مسبقًا من مستوى آخر، الرجاء اختيار رقم ترتيب مختلف.',
        ];
    }
}
