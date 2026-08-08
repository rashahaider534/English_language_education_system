<?php

namespace App\Http\Requests\Web\Test;

use App\Rules\SequentialOrder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateLevelTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title_en' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s.,!?;:()\'"-]+$/'],
            'title_ar' => ['required', 'string', 'max:255', 'regex:/^[\p{Arabic}0-9\s،؟؛:()«»"\'\-.!,]+$/u'],
            'passing_score' => ['required', 'numeric', 'min:10', 'max:100'],
            'questions' => ['required', 'array', 'min:2', new SequentialOrder($this->input('questions', []))],
            'questions.*.id' => ['required', 'integer', 'distinct', Rule::exists('questions', 'id')],
            'questions.*.order' => ['required', 'integer', 'min:1'],
        ];
    }
}
