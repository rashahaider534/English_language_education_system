<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Lesson\LessonResource;
use App\Http\Resources\Test\TeacherTestResource;
use App\Models\Lesson;
use App\Models\Test;
use App\Services\ContentReview\ContentReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentReviewController extends Controller
{
    public function __construct(
        private ContentReviewService $contentReviewService
    ) {}

    public function submitLesson(Lesson $lesson): JsonResponse
    {
        $this->authorize('update',$lesson);

        $result = $this->contentReviewService->submitLesson($lesson);

        return response()->json([
            'lesson' => new LessonResource($result['lesson']),
            'test' => new TeacherTestResource($result['test']),
        ]);
    }

    public function resubmitLesson(Lesson $lesson): JsonResponse
    {
        $this->authorize('update',$lesson);

        $result = $this->contentReviewService->resubmitLesson($lesson);

        return response()->json([
            'lesson' => new LessonResource($result['lesson']),
            'review' => $result['review'],
        ]);
    }

    public function submitTest(Test $test): JsonResponse
    {
        $this->authorize('update',$test);

        $result = $this->contentReviewService->submitTestForReview($test);

        return response()->json([
            'test' => new TeacherTestResource($result['test']),
        ]);
    }

    public function resubmitTest(Test $test): JsonResponse
    {
        $this->authorize('update',$test);

        $result = $this->contentReviewService->resubmitTestAfterChanges($test);

        return response()->json([
            'test' => new TeacherTestResource($result['test']),
            'review' => $result['review'],
        ]);
    }

    public function lessonReviewHistory(Lesson $lesson): JsonResponse
    {
        $this->authorize('update',$lesson);

        $history = $this->contentReviewService->reviewHistory($lesson);

        return response()->json(['history' => $history]);
    }

    public function testReviewHistory(Test $test): JsonResponse
    {
        $this->authorize('update',$test);

        $history = $this->contentReviewService->reviewHistory($test);

        return response()->json(['history' => $history]);
    }


}
