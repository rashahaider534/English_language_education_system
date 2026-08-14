<?php

namespace App\Services\Dashboard;

use App\Enums\PaymentStatus;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\Payment;
use App\Models\User;

class DashboardService
{
    public function superAdminData(): array
    {
        $totalStudents = User::role('student', 'api')->count();

        $totalTeachers = User::role('teacher', 'api')->count();
        $totalCertificates = Certificate::count();
        $totalAdmins = User::role('admin', 'web')->count();
        $totalPublishedLevels = Level::where('status', 'published')->count();

        $revenueMonthly = Payment::where('status', PaymentStatus::PAID)
            ->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)
            ->sum('amount');
        $revenueYearly = Payment::where('status', PaymentStatus::PAID)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $studentsByLevel = Level::withCount('users')
            ->orderBy('order')
            ->get()
            ->map(fn ($level) => [
                'label' => $level->name_ar,
                'count' => $level->users_count,
            ]);

        $bestSellingLevel = Payment::where('status', PaymentStatus::PAID)
            ->selectRaw('level_id, COUNT(*) as sales_count, SUM(amount) as total_amount')
            ->groupBy('level_id')
            ->orderByDesc('sales_count')
            ->with('level')
            ->first();

        $recentActivity = collect()
            ->concat(User::role('student', 'api')->latest()->take(2)->get()->map(fn ($u) => [
                'label' => 'تم تسجيل طالب جديد',
                'detail' => trim($u->first_name.' '.$u->last_name) ?: $u->email,
                'at' => $u->created_at,
                'dot' => '#E5484D',
            ]))
            ->concat(User::role('teacher', 'api')->latest()->take(2)->get()->map(fn ($u) => [
                'label' => 'تم تسجيل أستاذ جديد',
                'detail' => trim($u->first_name.' '.$u->last_name) ?: $u->email,
                'at' => $u->created_at,
                'dot' => '#0E6A96',
            ]))
            ->concat(Lesson::where('status', 'published')->latest('updated_at')->take(2)->get()->map(fn ($l) => [
                'label' => 'تحديث محتوى تعليمي',
                'detail' => $l->title_ar,
                'at' => $l->updated_at,
                'dot' => '#1E8A57',
            ]))
            ->sortByDesc('at')
            ->take(5)
            ->values();

        return [
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'totalCertificates' => $totalCertificates,
            'totalAdmins' => $totalAdmins,
            'totalPublishedLevels' => $totalPublishedLevels,
            'revenueMonthly' => $revenueMonthly,
            'revenueYearly' => $revenueYearly,
            'studentsByLevel' => $studentsByLevel,
            'bestSellingLevel' => $bestSellingLevel,
            'recentActivity' => $recentActivity,
        ];
    }

    public function adminData(): array
    {
        $lessonsDraft = Lesson::where('status', 'draft')->count();
        $lessonsPending = Lesson::where('status', 'pending')->count();
        $lessonsReviewed = Lesson::whereNotIn('status', ['pending', 'draft'])->count();
        $lessonsTotal = $lessonsPending + $lessonsReviewed;
        $lessonsReviewedPct = $lessonsTotal > 0 ? round(($lessonsReviewed / $lessonsTotal) * 100) : 0;

        $coursesPublished = Course::where('status', 'published')->count();
        $coursesPending = Course::where('status', 'pending')->count();
        $coursesClosed = Course::whereIn('status', ['closed', 'archived'])->count();

        $totalStudents = User::role('student', 'api')->count();
        $newStudentsThisMonth = User::role('student', 'api')->where('created_at', '>=', now()->startOfMonth())->count();

        $topStudents = User::role('student', 'api')
            ->with('studentProfile')
            ->get()
            ->sortByDesc(fn ($u) => $u->studentProfile->points ?? 0)
            ->take(10)
            ->values();

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

        $trendDays = collect(range(6, 0))->map(function ($i) {
            $day = now()->subDays($i)->endOfDay();

            return [
                'label' => now()->subDays($i)->translatedFormat('j M'),
                'published' => Lesson::where('status', 'published')->where('created_at', '<=', $day)->count(),
                'pending' => Lesson::where('status', 'pending')->where('created_at', '<=', $day)->count(),
                'reviewed' => Lesson::whereNotIn('status', ['pending', 'draft'])->where('created_at', '<=', $day)->count(),
            ];
        });

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

        return [
            'lessonsDraft' => $lessonsDraft,
            'lessonsPending' => $lessonsPending,
            'lessonsReviewed' => $lessonsReviewed,
            'lessonsReviewedPct' => $lessonsReviewedPct,
            'coursesPublished' => $coursesPublished,
            'coursesPending' => $coursesPending,
            'coursesClosed' => $coursesClosed,
            'topStudents' => $topStudents,
            'totalStudents' => $totalStudents,
            'newStudentsThisMonth' => $newStudentsThisMonth,
            'studentsGrowthPct' => $studentsGrowthPct,
            'coursesPublishedGrowthPct' => $coursesPublishedGrowthPct,
            'lessonsPendingGrowthPct' => $lessonsPendingGrowthPct,
            'trendDays' => $trendDays,
            'pendingLessonsList' => $pendingLessonsList,
            'recentActivity' => $recentActivity,
        ];
    }
}
