<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Lesson\LessonResource;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\Lesson\AdminLessonService;

class LessonController extends Controller
{
    public function __construct(
        private AdminLessonService $service
    ) {}
    public function index(Course $course, ?string $status = null)
    {
        $statistics = $this->service->getStatisticsLessons($course);
        $lessons = $this->service->getLessonsCourse(
            $course,
            $status
        );

        return view('admin.lessons.index', compact(
            'statistics',
            'lessons',
            'course'
        ));
    }
    public function show(Lesson $lesson)
    {
        $lesson = $this->service->show($lesson);
        return view('lessons.show', compact('lesson'));
    }
    public function archive(Lesson $lesson)
    {
        $lesson = $this->service->archive($lesson);
        return redirect()
            ->back()
            ->with('success', 'Lesson archived successfully.');
    }
}
