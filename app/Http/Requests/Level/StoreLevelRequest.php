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
        return false;
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
                Rule::unique('levels', 'name_en'),
            ],

            'name_ar' => [
                'required',
                'string',
                'max:255',
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
                'Minimum score must be less than maximum score.'
            );
        }
    });
}
}
