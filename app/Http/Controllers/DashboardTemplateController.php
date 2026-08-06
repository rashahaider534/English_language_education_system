<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\View\View;

class DashboardTemplateController extends Controller
{
    public function index(): View
    {
        $lessonsDraft = Lesson::where('status', 'draft')->count();
        $lessonsPending = Lesson::where('status', 'pending')->count();
        $lessonsReviewed = Lesson::whereNotIn('status', ['pending', 'draft'])->count();
        $lessonsTotal = $lessonsPending + $lessonsReviewed;
        $lessonsReviewedPct = $lessonsTotal > 0 ? round(($lessonsReviewed / $lessonsTotal) * 100) : 0;

        $coursesPublished = Course::where('status', 'published')->count();
        $coursesPending = Course::where('status', 'pending')->count();
        $coursesClosed = Course::whereIn('status', ['closed', 'archived'])->count();

        $totalStudents = User::role('student')->count();
        $newStudentsThisMonth = User::role('student')->where('created_at', '>=', now()->startOfMonth())->count();

        $coursesByTeacher = Course::where('status', 'pending')
            ->with('teacher')
            ->get()
            ->groupBy('teacher_id')
            ->map(fn ($group) => [
                'teacher' => $group->first()->teacher,
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->values();

        $topStudents = User::role('student')
            ->with('studentProfile')
            ->get()
            ->sortByDesc(fn ($u) => $u->studentProfile->points ?? 0)
            ->take(10)
            ->values();

        return $this->render('dashboard.pages.dashboard', [
            'title' => 'الرئيسية',
            'subtitle' => 'مرحبًا بك في لوحة تحكم الأدمن.',
            'breadcrumbs' => [
                ['label' => 'Dashboard'],
            ],
            'lessonsDraft' => $lessonsDraft,
            'lessonsPending' => $lessonsPending,
            'lessonsReviewed' => $lessonsReviewed,
            'lessonsReviewedPct' => $lessonsReviewedPct,
            'coursesPublished' => $coursesPublished,
            'coursesPending' => $coursesPending,
            'coursesClosed' => $coursesClosed,
            'coursesByTeacher' => $coursesByTeacher,
            'topStudents' => $topStudents,
            'totalStudents' => $totalStudents,
            'newStudentsThisMonth' => $newStudentsThisMonth,
        ]);
    }

    public function profile(): View
    {
        return $this->render('dashboard.pages.profile', [
            'title' => 'Profile Overview',
            'subtitle' => 'Demo profile details, preferences, and account security widgets.',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Profile'],
            ],
        ]);
    }

    public function settings(): View
    {
        return $this->render('dashboard.pages.settings', [
            'title' => 'Platform Settings',
            'subtitle' => 'General preferences, system controls, and notification defaults.',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Settings'],
            ],
        ]);
    }

    private function render(string $view, array $data = []): View
    {
        $user = auth()->user();
        $name = $user ? trim($user->first_name.' '.$user->last_name) : '';

        return view($view, $data + [
            'dashboardUser' => [
                'name' => $name ?: 'Admin User',
                'email' => $user->email ?? 'admin@example.com',
                'role' => 'Platform Administrator',
            ],
        ]);
    }
}
