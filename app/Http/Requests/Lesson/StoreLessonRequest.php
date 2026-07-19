<?php

namespace App\Http\Requests\Lesson;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLessonRequest extends FormRequest
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
            'title_en' => [
                'required',
                'string',
                'max:255',
                'filled',
                 'regex:/^[a-zA-Z0-9\s\-_]+$/',
                Rule::unique('lessons', 'title_en'),
            ],

            'title_ar' => [
                'required',
                'string',
                'filled',
                'max:255',
                'regex:/^[\x{0600}-\x{06FF}\s0-9\-_]+$/u',
                Rule::unique('lessons', 'title_ar'),
            ],

            'course_id' => [
                
                'exists:courses,id',
            ],

            'order' => [
                'required',
                'filled',
                'integer',
                'min:1',
                Rule::unique('lessons', 'order')->where('course_id', $this->course_id),
            ],

            'xp_points' => [
                'required',
                'filled',
                'integer',
                'min:1',
            ],

            'video' => [
                'required',
                'filled',
                'file',
                'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska',

            ],

        ];
    }
}
