<?php

namespace App\Http\Requests\Word;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWordRequest extends FormRequest
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
            'word_en' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
                Rule::unique('words', 'word_en'),
            ],

            'word_ar' => [
                'required',
                'string',
                'regex:/^[\x{0600}-\x{06FF}\s0-9\-_]+$/u',
                Rule::unique('words', 'word_ar'),
            ],
            'audio' => [
                'required',
                'file',
                //'mimes:mp3,wav,ogg,m4a',
                'max:5120',
            ],

        ];
    }
}
