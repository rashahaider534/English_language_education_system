<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Test;
use App\Services\TeacherStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherStatsController extends Controller
{
    public function __construct(private TeacherStatsService $statsService)
    {
    }
    public function courseStats(Course $course): JsonResponse
    {
       $this->authorize('update', $course);

        return response()->json($this->statsService->courseStats($course));
    }

    public function testStats(Test $test): JsonResponse
    {
        $course = $this->resolveCourseForTest($test);
        $this->authorize('update', $course);

        return response()->json($this->statsService->testStats($test));
    }

    private function resolveCourseForTest(Test $test): ?Course
    {
        return match ($test->testable_type) {
            'course' => $test->testable,
            'lesson' => $test->testable?->course,
            default  => null,
        };
    }
}
