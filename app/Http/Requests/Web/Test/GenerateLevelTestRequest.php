<?php

namespace App\Http\Requests\Web\Test;

use Illuminate\Foundation\Http\FormRequest;

class GenerateLevelTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by middleware/Policy at the controller level, per our earlier discussion
    }

    public function rules(): array
    {
        return [
            'testable_id' => ['required', 'integer', 'exists:levels,id'],
            'title_en' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s.,!?;:()\'"-]+$/'],
            'title_ar' => ['required', 'string', 'max:255', 'regex:/^[\p{Arabic}0-9\s،؟؛:()«»"\'\-.!,]+$/u'],
            'passing_score' => ['required', 'numeric', 'min:10', 'max:100'],
            'difficulties' => ['required', 'array'],
            'difficulties.easy' => ['required', 'integer', 'min:0'],
            'difficulties.medium' => ['required', 'integer', 'min:0'],
            'difficulties.hard' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'difficulties.easy.required' => 'You must specify a count for easy questions (use 0 if none needed).',
            'difficulties.medium.required' => 'You must specify a count for medium questions (use 0 if none needed).',
            'difficulties.hard.required' => 'You must specify a count for hard questions (use 0 if none needed).',
        ];
    }
}
