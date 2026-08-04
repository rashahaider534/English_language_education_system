<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $teachers = User::role('teacher')
            ->with('teacherProfile')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('is_active', $status === 'active');
            })
            ->orderBy('first_name')
            ->paginate(10)
            ->withQueryString();

        foreach ($teachers as $teacher) {
            $teacher->published_lessons_count = Lesson::whereHas('course', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })->where('status', 'published')->count();
        }

        $totalCount = User::role('teacher')->count();
        $activeCount = User::role('teacher')->where('is_active', true)->count();
        $inactiveCount = $totalCount - $activeCount;

        return view('admin.teachers.index', compact('teachers', 'search', 'status', 'totalCount', 'activeCount', 'inactiveCount'));
    }

    public function create(): View
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // بانتظار الربط بالباك-إند: ما في منطق حقيقي لإنشاء حساب أستاذ لهلق (نموذج تصميم فقط).
        return redirect()->route('admin.teachers.index')
            ->with('info', 'إضافة أستاذ جديد ميزة قيد التطوير، وسيتم ربطها بالباك-إند لاحقًا.');
    }

    public function lessons(User $teacher): View
    {
        abort_unless($teacher->hasRole('teacher'), 404);

        $lessons = Lesson::whereHas('course', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->with('course')->latest()->paginate(15);

        $teacher->load('teacherProfile');

        return view('admin.teachers.lessons', compact('teacher', 'lessons'));
    }

    public function toggleActive(User $teacher): RedirectResponse
    {
        // بانتظار الربط بالباك-إند: عمود is_active غير مفعّل بأي منطق فعلي (لا بتسجيل الدخول ولا بغيره) حاليًا.
        return redirect()->route('admin.teachers.index')
            ->with('info', 'تفعيل/تعطيل حساب الأستاذ ميزة قيد التطوير، وسيتم ربطها بالباك-إند لاحقًا.');
    }
}
