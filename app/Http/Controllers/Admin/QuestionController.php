<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Question\CreateQuestionRequest;
use App\Http\Requests\Api\Question\UpdateQuestionRequest;
use App\Models\Question;
use App\Services\QuestionService;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public QuestionService $questionService;

    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }
    public function store(CreateQuestionRequest $request)
    {
        $data = $request->validated();
        $question = $this->questionService->store($data);
        //return view
    }

    public function update(UpdateQuestionRequest $request , Question $question)
    {
        $this->authorize('update', $question);
        $data = $request->validated();
        $result = $this->questionService->updateQuestion($question, $data);

      //  return view
    }

    public function deleteQuestion(Question $question)
    {
        $this->authorize('delete', $question);

        $this->questionService->deleteQuestion($question);
        //return view

    }
}
