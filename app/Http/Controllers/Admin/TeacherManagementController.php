<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\Management\TeacherManagementService;

class TeacherManagementController extends Controller
{
    public function __construct(protected TeacherManagementService $teacherService) {}
    
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $teachers = $this->teacherService->getTeachers($search, $status);
        $counts = $this->teacherService->getTeacherCounts();
        return view('admin.teachers.index', ['teachers' => $teachers, 'search' => $search, 'status' => $status, 'totalCount' => $counts['totalCount'], 'activeCount' => $counts['activeCount'], 'inactiveCount' => $counts['inactiveCount'],]);
    }

    public function courses(User $teacher): View
    {
        $data = $this->teacherService->courses($teacher);
        return view('admin.teachers.lessons', ['teacher'=>$data['teacher'],'courses'=>$data['courses']]);
    }

    public function toggleActive(User $teacher): RedirectResponse
    {
        // بانتظار الربط بالباك-إند: عمود is_active غير مفعّل بأي منطق فعلي (لا بتسجيل الدخول ولا بغيره) حاليًا.
        return redirect()->route('admin.teachers.index')
            ->with('info', 'تفعيل/تعطيل حساب الأستاذ ميزة قيد التطوير، وسيتم ربطها بالباك-إند لاحقًا.');
    }
}
