<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\Question\CreateQuestionRequest;
use App\Http\Requests\Api\Question\UpdateQuestionRequest;
use App\Models\Question;
use App\Services\QuestionService;
use Illuminate\Http\Request;
use App\Policies\QuestionPolicy;

class QuestionController extends Controller
{
    public QuestionService $questionService;
    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }
    public function index()
    {
        return $this->questionService->index();
    }

    public function show(Request $request,Question $question)
    {
        $this->authorize('view', $question);
        return $this->questionService->show($question);
    }

    public function store(CreateQuestionRequest $request)
    {

        $data = $request->validated();
       // dd($request->validated());
        return $this->questionService->store($data);
    }

    public function checkStatus(Question $question)
    {
        return response()->json([
            $this->questionService->checkStatus($question)
        ]);
    }

    public function updateQuestion(UpdateQuestionRequest $request,Question $question)
    {
        $this->authorize('update', $question);
        $data = $request->validated();
        return  response()->json(
            $this->questionService->updateQuestion($question,$data)
        );
    }

    public function deleteQuestion(Question $question)
    {
        $this->authorize('delete', $question);
        return response()->json(
            $this->questionService->deleteQuestion($question)
        );
    }
}
