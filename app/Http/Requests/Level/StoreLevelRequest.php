<?php

namespace App\Http\Requests\Level;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLevelRequest extends FormRequest
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
        return [
            'name_en' => [
                'required',
                'string',
                'max:255',
                 'regex:/^[a-zA-Z0-9\s\-_]+$/',
                Rule::unique('levels', 'name_en'),
            ],

            'name_ar' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\x{0600}-\x{06FF}\s0-9\-_]+$/u',
                Rule::unique('levels', 'name_ar'),
            ],

            'order' =>
            [
                'required',
                'integer',
                'min:1',
                Rule::unique('levels', 'order'),
            ],

            'minimum_score' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],

            'maximum_score' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],

            'price' => 'required|numeric|min:0',

            'estimated_duration' => 'required|integer|min:1',
        ];
    }
    public function withValidator($validator)
{
    $validator->after(function ($validator) {
        if ($this->minimum_score >= $this->maximum_score) {
            $validator->errors()->add(
                'minimum_score',
                'أدنى علامة يجب أن تكون أقل من أعلى علامة.'
            );
        }
    });
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
            'name_en.required' => 'الاسم بالإنكليزي مطلوب.',
            'name_en.regex' => 'الاسم بالإنكليزي يجب أن يحتوي أحرف إنكليزية وأرقام فقط.',
            'name_en.unique' => 'هذا الاسم بالإنكليزي مستخدم مسبقًا لمستوى آخر.',
            'name_ar.required' => 'الاسم بالعربي مطلوب.',
            'name_ar.regex' => 'الاسم بالعربي يجب أن يحتوي أحرف عربية فقط.',
            'name_ar.unique' => 'هذا الاسم بالعربي مستخدم مسبقًا لمستوى آخر.',
            'order.required' => 'الترتيب مطلوب.',
            'order.integer' => 'الترتيب يجب أن يكون رقمًا صحيحًا.',
            'order.min' => 'الترتيب يجب أن يكون 1 على الأقل.',
            'order.unique' => 'هذا الترتيب مستخدم مسبقًا من مستوى آخر، الرجاء اختيار رقم ترتيب مختلف.',
            'minimum_score.required' => 'أدنى علامة مطلوبة.',
            'maximum_score.required' => 'أعلى علامة مطلوبة.',
            'price.required' => 'السعر مطلوب.',
            'estimated_duration.required' => 'المدة المتوقعة مطلوبة.',
        ];
    }
}
