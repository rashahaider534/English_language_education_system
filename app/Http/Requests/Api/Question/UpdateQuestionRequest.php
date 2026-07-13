<?php

namespace App\Http\Requests\Api\Question;

use App\Enums\QuestionType;
use App\Rules\ValidArrangeOrder;
use App\Rules\ValidCorrectAnswers;
use App\Rules\ValidFillQuestion;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestionRequest extends FormRequest
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
        $question = $this->route('question');
        $type = $question->type;

        return [
                'title_question_en' => 'sometimes|string|regex:/^[a-zA-Z0-9\s.,!?;:()\'"-]+$/',
                'title_question_ar' => 'sometimes|string|regex:/^[\p{Arabic}0-9\s،؟؛:()«»"\'\-.!,]+$/u',

                'text_question' => [
                    'nullable',
                    'string',
                    Rule::requiredIf($type === QuestionType::FILL->value),

                    Rule::when(
                        $type === QuestionType::FILL->value,
                        [new ValidFillQuestion($this->input('answers', []))]
                    ),
                ],
                'difficulty' => ['sometimes', Rule::in(['EASY','MEDIUM','HARD'])],

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

                'audio' => 'nullable|file|mimes:mp3,wav,ogg|max:5120|prohibits:image',
                'image' => 'nullable|file|mimes:jpeg,jpg,png|max:5120|prohibits:audio',

                'answers' => 'sometimes|array',
                Rule::when(
                    in_array($type, [
                        QuestionType::MCQ->value,
                        QuestionType::ARRANGE->value,
                        QuestionType::PAIR->value,
                    ]),
                    ['min:2']
                ),

                Rule::when(
                    $type === QuestionType::ARRANGE->value,
                    [
                        new ValidArrangeOrder(
                            $this->input('answers', [])
                        )
                    ]
                ),
            Rule::when(
                in_array($type, [
                    QuestionType::MCQ->value,
                    QuestionType::ARRANGE->value,
                ]),
                [
                    new ValidCorrectAnswers(
                        $type,
                        $this->input('answers', [])
                    )
                ]
            ),

            ] + $this->answerRules($type);
    }
    private function answerRules($type): array
    {
        return match ($type) {

            'MCQ' => [
                'answers.*.text_answer' => 'required|string|distinct',
                'answers.*.is_correct' => 'required|boolean',
            ],

            'FILL' => [
                'answers.*.text_answer' => 'required|string',
                'answers.*.blank_order' => 'required|integer|distinct|min:1',
            ],

            'ARRANGE' => [
                'answers.*.text_answer' => 'required|string',
                'answers.*.order' =>  [
                    'integer',
                    'distinct',
                    'required_if:answers.*.is_correct,true',
                    'prohibited_if:answers.*.is_correct,false',
                ],
                'answers.*.is_correct' => 'required|boolean',
            ],

            'PAIR' => [
                'answers.*.left_text' => 'required|string|regex:/^[\p{Arabic}0-9\s،؟؛:()«»"\'\-.!,]+$/u|distinct',
                'answers.*.right_text' => 'required|string|regex:/^[a-zA-Z0-9\s.,!?;:()\'"-]+$/|distinct',
            ],
        };
    }
}
