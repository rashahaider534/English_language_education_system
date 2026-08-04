<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\scoring\SubmitAnswerRequest;
use App\Http\Resources\Test\StudentTestResource;
use App\Models\Question;
use App\Models\Test;
use App\Models\UserAttempt;
use App\Services\AttemptService;
use App\Services\Test\TestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAttemptController extends Controller
{
    public AttemptService $attemptService;
    public  TestService $testService;

    public function __construct(AttemptService $attemptService,TestService $testService)
    {
        $this->attemptService = $attemptService;
        $this->testService = $testService;
    }
    public function startAndShow(Test $test)
    {
        $attempt = $this->attemptService->startAttempt($test);
        $test = $this->testService->show($test);

        return response()->json([
            'attempt_id' => $attempt->id,
            'test' => new StudentTestResource($test),
        ]);
    }

    public function submitAnswer(SubmitAnswerRequest $request, UserAttempt $attempt, Question $question): JsonResponse
    {
        $this->authorize('update', $attempt);

        return response()->json([
            $this->attemptService->submitAnswer(
                $attempt,
                $question,
                $request->validated('answer')
            )
        ]);
    }

    public function finish(UserAttempt $attempt): JsonResponse
    {
        $this->authorize('update', $attempt);

        $attempt = $this->attemptService->finishAttempt($attempt);

        return response()->json([
            'attempt_id' => $attempt->id,
            'score'      => $attempt->score,
            'passed'     => $attempt->score >= $attempt->test->passing_score,
        ]);
    }

    public function leave(UserAttempt $attempt): JsonResponse
    {
        $this->authorize('update', $attempt);

        $this->attemptService->leaveAttempt($attempt);

        return response()->json([
            'left' => true,
        ]);
    }

    public function review(UserAttempt $attempt): JsonResponse
    {
        $this->authorize('view', $attempt);

        $review = $this->attemptService->review($attempt);

        return response()->json($review);
    }
}
