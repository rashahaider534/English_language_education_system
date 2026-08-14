<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Level;
use App\Services\ProgressService;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function __construct(private ProgressService $progressService)
    {}
    public function courseProgress(Request $request, Course $course)
    {
        return response()->json([
            'progress' => $this->progressService->getCourseProgress($request->user(), $course),
        ]);
    }

    public function levelProgress(Request $request, Level $level)
    {
        return response()->json([
            'progress' => $this->progressService->getLevelProgress($request->user(), $level),
        ]);
    }
}
