<?php

namespace App\Services;

use App\Enums\ContentStatus;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\StudentProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProgressService
{
    public function recordActivity(int $userId): array
    {
        return DB::transaction(function () use ($userId) {
            $profile = StudentProfile::where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            $today = Carbon::today();
            $lastActive = $profile->last_activate_date
                ? Carbon::parse($profile->last_activate_date)
                : null;

            if ($lastActive && $lastActive->isSameDay($today)) {
                return [
                    'streak' => $profile->streak,
                    'increased' => false,
                    'is_new_day' => false,
                ];
            }

            if ($lastActive && $lastActive->isSameDay($today->copy()->subDay())) {
                $profile->increment('streak');
            } else {
                $profile->streak = 1;
            }

            $profile->last_activate_date = $today;
            $profile->save();

            return [
                'streak' => $profile->streak,
                'increased' => true,
                'is_new_day' => true,
            ];
        });
    }

    public function getWeeklyLessonActivity(int $userId): array
    {
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::SUNDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SATURDAY);

        $user = User::findOrFail($userId);

        $completions = $user->lessons()
            ->wherePivot('status', 'completed')
            ->wherePivotBetween('completed_at', [$startOfWeek, $endOfWeek])
            ->pluck('user_lessons.completed_at');

        $days = [];
        $cursor = $startOfWeek->copy();
        for ($i = 0; $i < 7; $i++) {
            $days[$cursor->toDateString()] = 0;
            $cursor->addDay();
        }

        foreach ($completions as $completedAt) {
            $date = Carbon::parse($completedAt)->toDateString();
            if (isset($days[$date])) {
                $days[$date]++;
            }
        }

        return $days;
    }

    public function getCourseProgress(User $user, Course $course): float
    {
        $totalLessons = $course->lessons()
            ->where('lessons.status', ContentStatus::PUBLISHED)
            ->count();

        if ($totalLessons === 0) {
            return 0;
        }

        $completedLessons = $course->lessons()
            ->where('lessons.status', ContentStatus::PUBLISHED)
            ->join('user_lessons', 'user_lessons.lesson_id', '=', 'lessons.id')
            ->where('user_lessons.user_id', $user->id)
            ->where('user_lessons.status', 'completed')
            ->count();


        return $this->calculatePercentage($completedLessons, $totalLessons);
    }

    public function getLevelProgress(User $user, Level $level): float
    {
        $totalLessons = $level->lessons()
            ->where('lessons.status', ContentStatus::PUBLISHED)
            ->count();

        if ($totalLessons === 0) {
            return 0;
        }

        $completedLessons = $level->lessons()
            ->where('lessons.status', ContentStatus::PUBLISHED )
            ->join('user_lessons', 'user_lessons.lesson_id', '=', 'lessons.id')
            ->where('user_lessons.user_id', $user->id)
            ->where('user_lessons.status', 'completed')
            ->count();

        return $this->calculatePercentage($completedLessons, $totalLessons);
    }

    private function calculatePercentage(int $completed, int $total): float
    {
        return round(($completed / $total) * 100);
    }
}

