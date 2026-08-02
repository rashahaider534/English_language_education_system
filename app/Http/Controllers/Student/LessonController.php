<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\Lesson\StudentLessonResource;
use App\Http\Resources\Lesson\StudentDetailLessonResource;
use App\Http\Resources\CommentResource;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\Lesson\StudentLessonService;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function __construct(
        private StudentLessonService $service
    ) {}
    public function index(Course $course)
    {
        $data = $this->service->getlessons($course, auth()->user());
        return response()->json([
            'progress' => $data['progress'],
            'current_lesson' => $data['current_lesson']
                ? new StudentLessonResource($data['current_lesson'])
                : null,
            'completed_lessons' => StudentLessonResource::collection($data['completed_lessons']),
            'locked_lessons' => StudentLessonResource::collection($data['locked_lessons']),
        ]);
    }

    public function show(Lesson $lesson)
    {
        $data = $this->service->show($lesson, auth()->user());

        return response()->json([
            'lesson' => new StudentDetailLessonResource($data['lesson']),
            'comments' => CommentResource::collection($data['comments']),
        ]);
    }
}
