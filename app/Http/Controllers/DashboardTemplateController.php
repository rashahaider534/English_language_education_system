<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Payment;
use App\Models\User;
use Illuminate\View\View;

class DashboardTemplateController extends Controller
{
    public function index(): View
    {
        if (auth()->user()?->hasRole('super-admin')) {
            return $this->superAdminIndex();
        }

        $lessonsDraft = Lesson::where('status', 'draft')->count();
        $lessonsPending = Lesson::where('status', 'pending')->count();
        $lessonsReviewed = Lesson::whereNotIn('status', ['pending', 'draft'])->count();
        $lessonsTotal = $lessonsPending + $lessonsReviewed;
        $lessonsReviewedPct = $lessonsTotal > 0 ? round(($lessonsReviewed / $lessonsTotal) * 100) : 0;
        $lessonsTotalAll = Lesson::count();
        $lessonsPublished = Lesson::where('status', 'published')->count();
        $lessonsArchived = Lesson::whereIn('status', ['archived', 'closed'])->count();

        $coursesPublished = Course::where('status', 'published')->count();
        $coursesPending = Course::where('status', 'pending')->count();
        $coursesClosed = Course::whereIn('status', ['closed', 'archived'])->count();

        $totalStudents = User::role('student', 'api')->count();
        $newStudentsThisMonth = User::role('student', 'api')->where('created_at', '>=', now()->startOfMonth())->count();

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

        $topStudents = User::role('student', 'api')
            ->with('studentProfile')
            ->get()
            ->sortByDesc(fn ($u) => $u->studentProfile->points ?? 0)
            ->take(10)
            ->values();

        // ============ month-over-month growth % for the top stat cards ============
        $studentsBeforeThisMonth = User::role('student', 'api')->where('created_at', '<', now()->startOfMonth())->count();
        $studentsGrowthPct = $studentsBeforeThisMonth > 0
            ? round((($totalStudents - $studentsBeforeThisMonth) / $studentsBeforeThisMonth) * 100)
            : ($totalStudents > 0 ? 100 : 0);

        $coursesPublishedBeforeThisMonth = Course::where('status', 'published')->where('updated_at', '<', now()->startOfMonth())->count();
        $coursesPublishedGrowthPct = $coursesPublishedBeforeThisMonth > 0
            ? round((($coursesPublished - $coursesPublishedBeforeThisMonth) / $coursesPublishedBeforeThisMonth) * 100)
            : ($coursesPublished > 0 ? 100 : 0);

        $lessonsPendingBeforeThisMonth = Lesson::where('status', 'pending')->where('updated_at', '<', now()->startOfMonth())->count();
        $lessonsPendingGrowthPct = $lessonsPendingBeforeThisMonth > 0
            ? round((($lessonsPending - $lessonsPendingBeforeThisMonth) / $lessonsPendingBeforeThisMonth) * 100)
            : ($lessonsPending > 0 ? 100 : 0);

        // ============ 7-day trend line chart: cumulative lesson counts by current status, bucketed by created_at ============
        $trendDays = collect(range(6, 0))->map(function ($i) {
            $day = now()->subDays($i)->endOfDay();

            return [
                'label' => now()->subDays($i)->translatedFormat('j M'),
                'published' => Lesson::where('status', 'published')->where('created_at', '<=', $day)->count(),
                'pending' => Lesson::where('status', 'pending')->where('created_at', '<=', $day)->count(),
                'reviewed' => Lesson::whereNotIn('status', ['pending', 'draft'])->where('created_at', '<=', $day)->count(),
            ];
        });

        // ============ real lessons-per-teacher bar chart ============
        $lessonsByTeacher = Lesson::with('course.teacher')
            ->get()
            ->groupBy(fn ($lesson) => $lesson->course?->teacher_id)
            ->filter(fn ($group, $teacherId) => $teacherId !== null)
            ->map(fn ($group) => [
                'teacher' => $group->first()->course->teacher,
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->take(4)
            ->values();

        // ============ pending lessons list with a real time-based urgency heuristic ============
        $pendingLessonsList = Lesson::where('status', 'pending')
            ->with('course.level')
            ->latest('updated_at')
            ->take(4)
            ->get()
            ->map(function ($lesson) {
                $daysPending = $lesson->updated_at->diffInDays(now());
                $urgency = $daysPending >= 5 ? 'high' : ($daysPending >= 2 ? 'medium' : 'low');

                return [
                    'lesson' => $lesson,
                    'days_pending' => $daysPending,
                    'urgency' => $urgency,
                ];
            });

        // ============ recent activity feed built from real timestamps across a few models ============
        $recentActivity = collect()
            ->concat(Lesson::where('status', 'published')->latest('updated_at')->take(3)->get()->map(fn ($l) => [
                'type' => 'lesson_published', 'label' => 'تم نشر درس جديد', 'detail' => $l->title_ar, 'at' => $l->updated_at,
            ]))
            ->concat(Course::latest('created_at')->take(2)->get()->map(fn ($c) => [
                'type' => 'course_created', 'label' => 'تم إنشاء كورس جديد', 'detail' => $c->name_ar, 'at' => $c->created_at,
            ]))
            ->concat(Lesson::where('status', 'pending')->latest('updated_at')->take(2)->get()->map(fn ($l) => [
                'type' => 'lesson_pending', 'label' => 'درس بانتظار المراجعة', 'detail' => $l->title_ar, 'at' => $l->updated_at,
            ]))
            ->sortByDesc('at')
            ->take(5)
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
            'lessonsTotalAll' => $lessonsTotalAll,
            'lessonsPublished' => $lessonsPublished,
            'lessonsArchived' => $lessonsArchived,
            'coursesPublished' => $coursesPublished,
            'coursesPending' => $coursesPending,
            'coursesClosed' => $coursesClosed,
            'coursesByTeacher' => $coursesByTeacher,
            'topStudents' => $topStudents,
            'totalStudents' => $totalStudents,
            'newStudentsThisMonth' => $newStudentsThisMonth,
            'studentsGrowthPct' => $studentsGrowthPct,
            'coursesPublishedGrowthPct' => $coursesPublishedGrowthPct,
            'lessonsPendingGrowthPct' => $lessonsPendingGrowthPct,
            'trendDays' => $trendDays,
            'lessonsByTeacher' => $lessonsByTeacher,
            'pendingLessonsList' => $pendingLessonsList,
            'recentActivity' => $recentActivity,
        ]);
    }

    private function superAdminIndex(): View
    {
        $totalStudents = User::role('student')->count();
        $studentsBeforeThisMonth = User::role('student')->where('created_at', '<', now()->startOfMonth())->count();
        $studentGrowthPct = $studentsBeforeThisMonth > 0
            ? round((($totalStudents - $studentsBeforeThisMonth) / $studentsBeforeThisMonth) * 100, 1)
            : ($totalStudents > 0 ? 100 : 0);

        $totalTeachers = User::role('teacher')->count();
        $totalCertificates = Certificate::count();

        $revenueMonthly = Payment::where('status', PaymentStatus::PAID)
            ->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)
            ->sum('amount');
        $revenueYearly = Payment::where('status', PaymentStatus::PAID)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $arabicMonths = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        $monthlySignups = collect(range(5, 0))->map(function ($i) use ($arabicMonths) {
            $date = now()->subMonths($i);

            return [
                'label' => $arabicMonths[$date->month - 1],
                'count' => User::role('student')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        });

        $bestSellingLevel = Payment::where('status', PaymentStatus::PAID)
            ->selectRaw('level_id, COUNT(*) as sales_count, SUM(amount) as total_amount')
            ->groupBy('level_id')
            ->orderByDesc('sales_count')
            ->with('level')
            ->first();

        return $this->render('dashboard.pages.super-admin-dashboard', [
            'title' => 'الرئيسية',
            'subtitle' => 'مرحبًا بك في لوحة تحكم السوبر أدمن.',
            'breadcrumbs' => [
                ['label' => 'Dashboard'],
            ],
            'totalStudents' => $totalStudents,
            'studentGrowthPct' => $studentGrowthPct,
            'totalTeachers' => $totalTeachers,
            'totalCertificates' => $totalCertificates,
            'totalUsers' => User::count(),
            'revenueMonthly' => $revenueMonthly,
            'revenueYearly' => $revenueYearly,
            'monthlySignups' => $monthlySignups,
            'bestSellingLevel' => $bestSellingLevel,
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
