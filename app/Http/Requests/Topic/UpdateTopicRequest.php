<?php

namespace App\Http\Requests\Topic;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTopicRequest extends FormRequest
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
        $topic = $this->route('topic');
        return [
            'name_en' => [
                'sometimes',
                'filled',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
                Rule::unique('topics', 'name_en')->ignore($topic->id),
            ],
            'name_ar' => [
                'sometimes',
                'filled',
                'string',
                'max:255',
                'regex:/^[\x{0600}-\x{06FF}\s0-9\-_]+$/u',
                Rule::unique('topics', 'name_ar')->ignore($topic->id),
            ],
            'image' => [
                'nullable',
                'image',
                'max:2048'
            ],
        ];
    }
}
