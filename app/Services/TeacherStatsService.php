<?php

namespace App\Services;

use App\Enums\AttemptStatus;
use App\Enums\ContentStatus;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Test;
use Illuminate\Support\Facades\DB;

class TeacherStatsService
{
    private const TESTABLE_LESSON = 'lesson';
    private const TESTABLE_COURSE = 'course';

    public function courseStats(Course $course): array
    {
        $testIds = $this->publishedTestIdsForCourse($course);

        return [
            'avg_first_attempt_pass_rate' => $this->avgAcrossTests($testIds, fn (Test $t) => $this->firstAttemptPassRate($t)),
            'avg_abandonment_rate' => $this->avgAcrossTests($testIds, fn (Test $t) => $this->abandonmentRate($t)),
            'lessons_funnel' => $this->lessonsFunnel($course),
        ];
    }

    public function testStats(Test $test): array
    {
        return [
            'first_attempt_pass_rate' => $this->firstAttemptPassRate($test),
            'avg_attempts_to_pass' => $this->avgAttemptsToPass($test),
            'abandonment_rate' => $this->abandonmentRate($test),
            'currently_struggling_rate' => $this->currentlyStrugglingRate($test),
            'score_distribution' => $this->scoreDistribution($test),
            'questions' => $this->questionsAnalysis($test),
        ];
    }



    private function publishedTestIdsForCourse(Course $course): array
    {
        $lessonIds = $course->lessons()->where('status', ContentStatus::PUBLISHED)->pluck('id');

        return Test::where('status', ContentStatus::PUBLISHED)
            ->where(function ($q) use ($course, $lessonIds) {
                $q->where(fn ($q2) => $q2->where('testable_type', self::TESTABLE_COURSE)->where('testable_id', $course->id))
                    ->orWhere(fn ($q2) => $q2->where('testable_type', self::TESTABLE_LESSON)->whereIn('testable_id', $lessonIds));
            })
            ->pluck('id')
            ->all();
    }

    private function avgAcrossTests(array $testIds, callable $metric): float
    {
        if (empty($testIds)) {
            return 0;
        }

        $values = Test::whereIn('id', $testIds)->get()->map($metric);

        return round($values->avg(), 1);
    }

    private function firstCompletedAttemptsPerUser(int $testId)
    {
        return DB::table('user_attempts')
            ->where('test_id', $testId)
            ->where('status', AttemptStatus::COMPLETED->value)
            ->orderBy('started_at')
            ->get()
            ->unique('user_id');
    }

    private function firstAttemptPassRate(Test $test): float
    {
        $firstAttempts = $this->firstCompletedAttemptsPerUser($test->id);

        if ($firstAttempts->isEmpty()) {
            return 0;
        }

        $passed = $firstAttempts->where('score', '>=', $test->passing_score)->count();

        return round($passed / $firstAttempts->count() * 100, 1);
    }

    private function avgAttemptsToPass(Test $test): float
    {
        $attemptsByUser = DB::table('user_attempts')
            ->where('test_id', $test->id)
            ->where('status', AttemptStatus::COMPLETED->value)
            ->orderBy('started_at')
            ->get()
            ->groupBy('user_id');

        $attemptsUntilPass = [];

        foreach ($attemptsByUser as $attempts) {
            $count = 0;
            foreach ($attempts as $attempt) {
                $count++;
                if ($attempt->score >= $test->passing_score) {
                    $attemptsUntilPass[] = $count;
                    break;
                }
            }
        }

        return $attemptsUntilPass ? round(array_sum($attemptsUntilPass) / count($attemptsUntilPass), 1) : 0;
    }

    private function abandonmentRate(Test $test): float
    {
        $total = DB::table('user_attempts')->where('test_id', $test->id)->count();

        if (! $total) {
            return 0;
        }

        $abandoned = DB::table('user_attempts')
            ->where('test_id', $test->id)
            ->where('status', AttemptStatus::ABANDONED->value)
            ->count();

        return round($abandoned / $total * 100, 1);
    }

    private function currentlyStrugglingRate(Test $test): float
    {
        $allUsers = DB::table('user_attempts')->where('test_id', $test->id)->distinct()->pluck('user_id');

        if ($allUsers->isEmpty()) {
            return 0;
        }

        $passedUsers = DB::table('user_attempts')
            ->where('test_id', $test->id)
            ->where('status', AttemptStatus::COMPLETED->value)
            ->where('score', '>=', $test->passing_score)
            ->distinct()
            ->pluck('user_id');

        return round($allUsers->diff($passedUsers)->count() / $allUsers->count() * 100, 1);
    }

    private function scoreDistribution(Test $test): array
    {
        $bestScores = DB::table('user_attempts')
            ->where('test_id', $test->id)
            ->where('status', AttemptStatus::COMPLETED->value)
            ->selectRaw('MAX(score) as best_score')
            ->groupBy('user_id')
            ->pluck('best_score');

        $buckets = ['0-49' => 0, '50-69' => 0, '70-89' => 0, '90-100' => 0];

        foreach ($bestScores as $score) {
            $buckets[match (true) {
                $score < 50 => '0-49',
                $score < 70 => '50-69',
                $score < 90 => '70-89',
                default => '90-100',
            }]++;
        }

        return collect($buckets)->map(fn ($count, $range) => compact('range', 'count'))->values()->all();
    }

    private function questionsAnalysis(Test $test): array
    {
        return $test->questions
        ->map(function ($question) use ($test) {
            $stats = DB::table('user_attempt_answers')
                ->join('user_attempts', 'user_attempts.id', '=', 'user_attempt_answers.attempt_id')
                ->where('user_attempts.test_id', $test->id)
                ->where('user_attempt_answers.question_id', $question->id)
                ->selectRaw('AVG(user_attempt_answers.score) as avg_score, COUNT(*) as attempts_count')
                ->first();

            $ratio = $question->score > 0 && $stats->avg_score !== null
                ? round($stats->avg_score / $question->score * 100, 1)
                : null;

            return [
                'question_id' => $question->id,
                'title' => $question->title_question_ar,
                'difficulty' => $question->difficulty,
                'avg_score_ratio' => $ratio,
                'error_rate' => $ratio !== null ? round(100 - $ratio, 1) : null,
                'attempts_count' => $stats->attempts_count,
                'flag' => $this->flagQuestion($question->difficulty, $ratio),
            ];
        })
            ->sortByDesc('error_rate')
            ->values()
            ->all();
    }

    private function flagQuestion(string $difficulty, ?float $ratio): ?string
    {
        if ($ratio === null) {
            return null;
        }

        return match ($difficulty) {
            'EASY'   => $ratio < 60 ? 'unexpected_high_error' : null,
            'MEDIUM' => match (true) {
                $ratio < 40  => 'unexpected_high_error',
                $ratio > 95  => 'unexpectedly_easy',
                default      => null,
            },
            'HARD'   => $ratio > 90 ? 'unexpectedly_easy' : null,
            default  => null,
        };
    }
    private function lessonsFunnel(Course $course): array
    {
        return $course->lessons()
            ->where('status', ContentStatus::PUBLISHED)
            ->orderBy('order')
            ->get()
            ->map(function (Lesson $lesson) {
                $reached = DB::table('user_lessons')->where('lesson_id', $lesson->id)->distinct('user_id')->count('user_id');

                $testId = Test::where('testable_type', self::TESTABLE_LESSON)->where('testable_id', $lesson->id)->value('id');

                $attemptedTest = $testId
                    ? DB::table('user_attempts')->where('test_id', $testId)->distinct('user_id')->count('user_id')
                    : 0;

                return [
                    'lesson_id' => $lesson->id,
                    'order' => $lesson->order,
                    'title' => $lesson->title_ar,
                    'reached' => $reached,
                    'attempted_test' => $attemptedTest,
                ];
            })
            ->all();
    }

//    private function stuckStudents(Course $course, array $testIds): array
//    {
//        $lessonStuck = DB::table('user_lessons')
//            ->join('lessons', 'lessons.id', '=', 'user_lessons.lesson_id')
//            ->where('lessons.course_id', $course->id)
//            ->where('user_lessons.status', 'in_progress')
//            ->where('user_lessons.started_at', '<=', now()->subDays($this->staleDays))
//            ->select('user_lessons.user_id', 'lessons.title_ar as location', DB::raw("'lesson_stuck' as reason"))
//            ->get();
//
//        $repeatedAbandon = DB::table('user_attempts')
//            ->whereIn('test_id', $testIds)
//            ->where('status', AttemptStatus::ABANDONED->value)
//            ->select('user_id', 'test_id', DB::raw('COUNT(*) as abandoned_count'))
//            ->groupBy('user_id', 'test_id')
//            ->having('abandoned_count', '>=', 2)
//            ->get()
//            ->filter(function ($row) {
//                return ! DB::table('user_attempts')
//                    ->join('tests', 'tests.id', '=', 'user_attempts.test_id')
//                    ->where('user_attempts.user_id', $row->user_id)
//                    ->where('user_attempts.test_id', $row->test_id)
//                    ->where('user_attempts.status', AttemptStatus::COMPLETED->value)
//                    ->whereColumn('user_attempts.score', '>=', 'tests.passing_score')
//                    ->exists();
//            });
//
//        $testsById = Test::whereIn('id', $repeatedAbandon->pluck('test_id'))->pluck('title_ar', 'id');
//
//        $repeatedAbandon = $repeatedAbandon->map(fn ($row) => (object) [
//            'user_id' => $row->user_id,
//            'location' => $testsById[$row->test_id] ?? null,
//            'reason' => 'test_abandonment',
//        ]);
//
//        return $lessonStuck->merge($repeatedAbandon)
//            ->unique('user_id')
//            ->values()
//            ->all();
//    }
}
