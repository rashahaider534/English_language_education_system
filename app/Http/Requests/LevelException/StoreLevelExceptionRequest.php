<?php

namespace App\Http\Requests\LevelException;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLevelExceptionRequest extends FormRequest
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
              'reason' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],

            // يمكن رفع أكثر من ملف
            'attachments' => [
                'nullable',
                'array',
                'max:5',
            ],

            'attachments.*' => [
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                //'max:10240',
            ],
        ];
    }
}
