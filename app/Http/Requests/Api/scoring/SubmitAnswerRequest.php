<?php

namespace App\Http\Requests\Api\scoring;

use App\Enums\QuestionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $question = $this->route('question');

        return match ($question->type) {
            QuestionType::MCQ=> [
                'answer' => ['required', 'array'],
                'answer.selected_answer_id' => [
                    'required',
                    'integer',
                    Rule::exists('mcq_answers', 'id')
                        ->where('question_id', $question->id),
                ],
            ],
            QuestionType::FILL => [
                'answer' => ['required', 'array'],
                'answer.answers' => ['required', 'array'],
                'answer.answers.*' => ['required', 'string'],
            ],
            QuestionType::ARRANGE=> [
                'answer' => ['required', 'array'],
                'answer.ordered_ids' => ['required', 'array'],
                'answer.ordered_ids.*' => ['required', 'integer',
                    Rule::exists('arrange_answers', 'id')->where('question_id', $question->id),],
            ],
            QuestionType::PAIR=> [
                'answer' => ['required', 'array'],
                'answer.pairs' => ['required', 'array'],
                'answer.pairs.*' => ['required', 'integer',
                    Rule::exists('pair_answers', 'id')->where('question_id', $question->id)],
            ],
        };
    }
}
/*اشكال الاجابات هيك لازم تكون
MCQ
{
    "answer": {
        "selected_answer_id": 5
    }
}
FILL
{
    "answer": {
        "answers": {
            "1": "hello",
            "2": "world"
        }
    }
}
ARRANGE
{
    "answer": {
        "ordered_ids": [4, 2, 7, 1]
    }
}
PAIR
{
    "answer": {
        "pairs": {
            "1": 1,
            "2": 2,
            "3": 3
        }
    }
}
*/
