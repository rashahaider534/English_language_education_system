<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\Question\CreateQuestionRequest;
use App\Http\Requests\Api\Question\FilterQuestionRequest;
use App\Http\Requests\Api\Question\UpdateQuestionRequest;
use App\Http\Resources\Question\TeacherQuestionResource;
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

    public function ArchiveQuestions()
    {
        return $this->questionService->ArchivedQuestions();
    }

    public function show(Request $request,Question $question)
    {
        $this->authorize('view', $question);
        return $this->questionService->show($question);
    }

    public function store(CreateQuestionRequest $request)
    {

        $data = $request->validated();
        return response()->json(
            new TeacherQuestionResource($this->questionService->store($data))
        );
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
        $result = $this->questionService->updateQuestion($question, $data);
        $result['question'] = new TeacherQuestionResource($result['question']);

        return response()->json($result);
    }

    public function deleteQuestion(Question $question)
    {
        $this->authorize('delete', $question);
        return response()->json(
            $this->questionService->deleteQuestion($question)
        );
    }

    public function blockingTests(Question $question)
    {
        return $this->questionService->blockingTests($question);
    }

    public function filter(FilterQuestionRequest $request)
    {
        $data = $request->validated();
        return response()->json(TeacherQuestionResource::collection($this->questionService->filter($data)), 200);
    }
}
