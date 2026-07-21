<?php

namespace App\Http\Requests\Web\Test;

use App\Rules\SequentialOrder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePlacementTestRequest extends FormRequest
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
            'title_en' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s.,!?;:()\'"-]+$/'],
            'title_ar' => ['required', 'string', 'max:255', 'regex:/^[\p{Arabic}0-9\s،؟؛:()«»"\'\-.!,]+$/u'],
            'questions' => ['required', 'array', 'min:2', new SequentialOrder($this->input('questions', []))],
            'questions.*.id' => [
                'required', 'integer', 'distinct',
                Rule::exists('questions', 'id')->where('is_placement_question', true),
            ],


             ];
    }
}
