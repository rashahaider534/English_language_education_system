<?php

namespace App\Http\Requests\Course;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
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
        $course = $this->route('course');
        return [
            'name_en' => [
                'sometimes',
                'filled',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
                Rule::unique('courses', 'name_en')->ignore($course->id),
            ],
            'name_ar' => [
                'sometimes',
                'filled',
                'string',
                'max:255',
                'regex:/^[\x{0600}-\x{06FF}\s0-9\-_]+$/u',
                Rule::unique('courses', 'name_ar')->ignore($course->id),
            ],

            'order' => [
                'sometimes',
                'filled',
                'integer',
                'min:0',
                Rule::unique('courses', 'order')
                    ->where('level_id', $course->level_id)
                    ->ignore($course->id),
            ],

            'estimated_duration' => ['sometimes', 'filled', 'integer', 'min:1'],
            'image' => 'sometimes|filled|image|mimes:jpg,jpeg,png,webp|max:2048',
            'teacher_id' => [
                'sometimes',
                'filled',
                'exists:users,id',
            ],
        ];
    }
}
