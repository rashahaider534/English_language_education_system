<?php

namespace App\Http\Requests\Api\Question;

use App\Enums\QuestionType;
use App\Rules\SequentialOrder;
use App\Rules\ValidCorrectAnswers;
use App\Rules\ValidFillQuestion;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages()
    {
        return [
            'answers.*.left_text.regex' => 'the left text should only be in arabic',
            'answers.*.right_text.regex' => 'the right text should only be in english',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(QuestionType::class)],
            'title_question_en' => 'required|string|regex:/^[a-zA-Z0-9\s.,!?;:()\'"-]+$/',
            'title_question_ar' => 'required|string|regex:/^[\p{Arabic}0-9\s،؟؛:()«»"\'\-.!,]+$/u',
            'text_question' => [
                'nullable',
                'string',
                'required_if:type,FILL',

                Rule::when(
                    $this->input('type') === QuestionType::FILL->value,
                    [new ValidFillQuestion($this->input('answers', []))]
                ),
            ],
            'difficulty'=> ['required', 'string' ,Rule::in(['EASY','MEDIUM','HARD'])],
            'audio' => 'nullable|file|mimes:mp3,wav,ogg|max:5120|prohibits:image',
            'image' => 'nullable|file|mimes:jpeg,jpg,png|max:5120|prohibits:audio',
            'score' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $difficulty = $this->input('difficulty');

                    if ($difficulty === 'easy' && ($value < 1 || $value > 2)) {
                        $fail('An easy question should have only 1 or 2 points.');
                    }

                    if ($difficulty === 'medium' && ($value < 3 || $value > 5)) {
                        $fail('A medium question should have only between 3 and 5 points.');
                    }

                    if ($difficulty === 'hard' && ($value < 6 || $value > 10)) {
                        $fail('A hard question should have between 6 and 10 points.');
                    }
                },
            ],
            'answers' => [
                'required',
                'array',
                Rule::when(
                    in_array($this->input('type'), [
                        QuestionType::MCQ->value,
                        QuestionType::ARRANGE->value,
                        QuestionType::PAIR->value,
                    ]),
                    ['min:2']
                ),
                Rule::when(
                    $this->input('type') === QuestionType::ARRANGE->value,
                    [new SequentialOrder($this->input('answers', []))]
                ),
                Rule::when(
                    in_array($this->input('type'), [
                        QuestionType::MCQ->value,
                        QuestionType::ARRANGE->value,
                    ]),
                    [
                        new ValidCorrectAnswers(
                            $this->input('type'),
                            $this->input('answers', [])
                        )
                    ]
                ),
            ],

            'answers.*.text_answer' => [
                'string','filled',

                Rule::requiredIf(
                    in_array($this->input('type'), ['MCQ', 'FILL', 'ARRANGE'])
                ),

                Rule::when(
                    $this->input('type') === QuestionType::MCQ->value,
                    ['distinct']
                ),
            ],
            'answers.*.is_correct' => 'required_if:type,MCQ|required_if:type,ARRANGE|boolean',
            'answers.*.order' => [
                'integer',
                'distinct',
                'min:1',
                Rule::when(
                    $this->input('type') === QuestionType::ARRANGE->value,
                    ['required_if:answers.*.is_correct,true', 'prohibited_if:answers.*.is_correct,false']
                ),
                Rule::when(
                    $this->input('type') !== QuestionType::ARRANGE->value,
                    ['prohibited']
                ),
            ],

            'answers.*.blank_order' => 'required_if:type,FILL|integer|distinct|min:1',


            'answers.*.left_text' => 'required_if:type,PAIR|string|regex:/^[\p{Arabic}0-9\s،؟؛:()«»"\'\-.!,]+$/u',
            'answers.*.right_text' => 'required_if:type,PAIR|string|regex:/^[a-zA-Z0-9\s.,!?;:()\'"-]+$/|distinct',



        ];
    }
}
