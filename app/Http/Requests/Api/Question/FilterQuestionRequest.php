<?php

namespace App\Http\Requests\Api\Question;

use App\Enums\QuestionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['nullable', Rule::enum(QuestionType::class)],
            'difficulty' => ['nullable', Rule::in(['EASY', 'MEDIUM', 'HARD'])],

            'min_score' => ['nullable', 'integer', 'min:0'],
            'max_score' => [
                'nullable',
                'integer',
                'min:0',
                'gte:min_score',
            ],

            'search' => ['nullable', 'string', 'max:255'],

            'course_id' => [
                'nullable',
                'integer',
                'required_with:only_eligible',
                Rule::exists('courses', 'id'),
            ],
            'only_eligible' => ['nullable', 'boolean'],

            'sort' => ['nullable', Rule::in(['asc', 'desc'])],

        ];
    }

    public function messages(): array
    {
        return [
            'max_score.gte' => 'The max_score must be greater than or equal to min_score.',
            'course_id.required_with' => 'course_id is required when only_eligible is provided.',
        ];
    }
}
