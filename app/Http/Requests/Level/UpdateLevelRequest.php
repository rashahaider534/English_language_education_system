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
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
                Rule::unique('levels', 'name_en')->ignore($level->id),
            ],
            'name_ar' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[\x{0600}-\x{06FF}\s0-9\-_]+$/u',
                Rule::unique('levels', 'name_ar')->ignore($level->id),
            ],

            'order' => ['sometimes', 'integer', 'min:0', Rule::unique('levels', 'order')->ignore($level->id),],

            'minimum_score' => [
                'sometimes',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $max = $this->input('maximum_score', null);

                    if ($max !== null && $value >= $max) {
                        $fail('Minimum score must be less than maximum score.');
                    }
                }
            ],

            'maximum_score' => [
                'sometimes',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $min = $this->input('minimum_score', null);

                    if ($min !== null && $value <= $min) {
                        $fail('Maximum score must be greater than minimum score.');
                    }
                }
            ],

            'price' => ['sometimes', 'numeric', 'min:0'],

            'estimated_duration' => ['sometimes', 'integer', 'min:1'],
        ];
    }

}
