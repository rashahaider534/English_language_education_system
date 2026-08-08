<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Level;
use App\Models\LessonReview;
use App\Models\User;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(): View
    {
        $admins = User::role('admin', 'web')->get();

        $reviewStats = $admins->map(function ($admin) {
            $reviews = LessonReview::where('assigned_to', $admin->id)->get();

            return [
                'admin' => $admin,
                'total' => $reviews->count(),
                'approved' => $reviews->where('status', 'approved')->count(),
                'rejected' => $reviews->where('status', 'rejected')->count(),
                'in_review' => $reviews->whereIn('status', ['pending', 'in_review', 'request_changes'])->count(),
            ];
        })->sortByDesc('total')->values();

        $totalReviewed = LessonReview::whereIn('status', ['approved', 'rejected'])->count();
        $totalApproved = LessonReview::where('status', 'approved')->count();
        $totalPendingReview = LessonReview::whereIn('status', ['pending', 'in_review', 'request_changes'])->count();

        // لسا ما في عداد مشاهدات حقيقي بالباك-إند لكل مستوى — النسب هون توضيحية فقط لشكل الواجهة.
        $levels = Level::withCount('courses')->orderBy('order')->get();
        $exampleViewRates = [72, 54, 88, 33, 61, 19];
        $levels->each(function ($level, $i) use ($exampleViewRates) {
            $level->example_view_rate = $exampleViewRates[$i % count($exampleViewRates)];
        });

        return view('admin.audit.index', compact('reviewStats', 'totalReviewed', 'totalApproved', 'totalPendingReview', 'levels'));
    }

    public function level(Level $level): View
    {
        $courses = Course::where('level_id', $level->id)->withCount('lessons')->get();

        // لسا ما في نظام تقييم/مراجعات حقيقي للكورسات بالباك-إند — التقييمات هون توضيحية فقط.
        $exampleRatings = [4.6, 3.9, 4.8, 4.2, 3.5];
        $courses->each(function ($course, $i) use ($exampleRatings) {
            $course->example_rating = $exampleRatings[$i % count($exampleRatings)];
        });

        return view('admin.audit.level', compact('level', 'courses'));
    }
}
