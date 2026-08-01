<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Question\CreateQuestionRequest;
use App\Http\Requests\Api\Question\FilterQuestionRequest;
use App\Http\Requests\Api\Question\UpdateQuestionRequest;
use App\Models\Level;
use App\Models\Question;
use App\Services\QuestionService;
use App\Services\Test\AdminTestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public QuestionService $questionService;
    public AdminTestService $adminTestService;

    public function __construct(QuestionService $questionService , AdminTestService $adminTestService)
    {
        $this->questionService = $questionService;
        $this->adminTestService = $adminTestService;
    }

    public function show(Question $question): View
    {
        $questions = $this->questionService->show($question);
        return view('admin.questions.show', compact('questions'));
    }

    public function createPlacementQuestion(): View
    { return view('admin.questions.placement.create'); }
    public function store(CreateQuestionRequest $request): RedirectResponse
    { $data = $request->validated();
        $this->questionService->store($data);
        return redirect() ->route('admin.questions.placement.index') ->with('success', 'Question created successfully.'); }

    public function edit(Question $question): View
    { $this->authorize('update', $question);
        return view('admin.questions.edit', [ 'question' => $question, ]); }
    public function update( UpdateQuestionRequest $request, Question $question ): RedirectResponse
    { $this->authorize('update', $question);
        $data = $request->validated();
        $this->questionService->updateQuestion( $question, $data );
        return redirect() ->route('admin.questions.placement.index') ->with('success', 'Question updated successfully.');}

    public function deleteQuestion( Question $question ): RedirectResponse
    { $this->authorize('delete', $question);
        $this->questionService->deleteQuestion($question);
        return redirect() ->route('admin.questions.placement.index') ->with('success', 'Question deleted successfully.');
    }

    public function indexPlacementQuestions( FilterQuestionRequest $request ): View
    { $questions = $this->adminTestService->filter( $request->validated(), 'placement' );
        return view('admin.questions.placement.index', [ 'questions' => $questions, ]); }

    public function indexLevelTestQuestions( FilterQuestionRequest $request, Level $level ): View
    { $questions = $this->adminTestService->filter( $request->validated(), 'level_test', $level->id );
        return view('admin.questions.level-test.index', [ 'questions' => $questions, 'level' => $level, ]); }
}
