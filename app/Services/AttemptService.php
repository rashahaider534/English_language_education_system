<?php

namespace App\Services;

use App\Enums\AttemptStatus;
use App\Enums\QuestionType;
use App\Models\Question;
use App\Models\StudentProfile;
use App\Models\Test;
use App\Models\User;
use App\Models\UserAttempt;
use App\Models\UserAttemptAnswer;
use App\Services\Course\StudentCourseService;
use App\Services\Lesson\StudentLessonService;
use App\Services\Scoring\QuestionScorerFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttemptService
{
    public StudentCourseService $studentCourseService;
    public StudentLessonService $studentLessonService;
    public QuestionScorerFactory $scorerFactory;
    public function __construct(StudentCourseService $studentCourseService, StudentLessonService $studentLessonService, QuestionScorerFactory $scorerFactory)
    {
        $this->studentCourseService = $studentCourseService;
        $this->studentLessonService = $studentLessonService;
        $this->scorerFactory = $scorerFactory;
    }

    public function submitAnswer(UserAttempt $attempt, Question $question, array $answerJson)
    {
        if ($attempt->status !== AttemptStatus::IN_PROGRESS) {
            throw ValidationException::withMessages([
                'error' => 'This Attempt is no longer available.',
            ]);
        }
        $alreadyAnswered = $attempt->answers()
            ->where('question_id', $question->id)
            ->exists();

        if ($alreadyAnswered) {
            throw ValidationException::withMessages([
                'error' => 'This question has already been answered.',
            ]);
        }
        $this->assertPreviousQuestionsAnswered($attempt, $question);

        $scorer = $this->scorerFactory->make($question->type);
        $score = $scorer->score($question, $answerJson);

        $userAnswer =  UserAttemptAnswer::create([
            'attempt_id'  => $attempt->id,
            'question_id' => $question->id,
            'answer_json' => $answerJson,
            'score'       => $score,
        ]);

        return [
            'answer'     => $userAnswer,
            'score'      => $score,
            'max_score'  => $question->score,
            'is_correct' => $score === $question->score,
        ];
    }

    private function assertPreviousQuestionsAnswered(UserAttempt $attempt, Question $question): void
    {
        $currentOrder = $attempt->test->questions()
            ->where('questions.id', $question->id)
            ->value('test_questions.order');

        if ($currentOrder === null) {
            throw ValidationException::withMessages([
                'error' => 'This question does not belong to this test.',
            ]);
        }

        $requiredQuestionIds = $attempt->test->questions()
            ->wherePivot('order', '<', $currentOrder)
            ->pluck('questions.id');

        $answeredQuestionIds = $attempt->answers()->pluck('question_id');

        $missing = $requiredQuestionIds->diff($answeredQuestionIds);

        if ($missing->isNotEmpty()) {
            throw ValidationException::withMessages([
                'error' => 'You must answer previous questions first.',
            ]);
        }
    }
    public function startAttempt(Test $test): UserAttempt
    {
        return DB::transaction(function () use ($test) {
            $user = auth()->user();
            if ($test->testable_type === 'placement_test') {
                if (!$this->isEligibleForPlacementRetake($user)) {
                    throw ValidationException::withMessages([
                        'error' => 'you have already taken the placement test',
                    ]);
                }
            }
            UserAttempt::where('user_id', $user->id)
                ->where('test_id', $test->id)
                ->where('status', AttemptStatus::IN_PROGRESS)
                ->update([
                    'status'       => AttemptStatus::ABANDONED,
                    'completed_at' => now(),
                ]);

            return UserAttempt::create([
                'user_id'    => $user->id,
                'test_id'    => $test->id,
                'started_at' => now(),
                'score'      => 0,
            ]);
        });
    }

    public function isEligibleForPlacementRetake(User $user): bool
    {
        if ($user->levels()->exists()) {
            return false;
        }

        $lastAttempt = UserAttempt::where('user_id', $user->id)
            ->whereHas('test', fn($q) => $q->where('testable_type', 'placement_test'))
            ->where('status', AttemptStatus::COMPLETED)
            ->latest('completed_at')
            ->first();

        if (!$lastAttempt) {
            return true;
        }
        return $lastAttempt->completed_at->diffInDays(now()) >= 30;
    }

    public function finishAttempt(UserAttempt $attempt): UserAttempt
    {
        return DB::transaction(function () use ($attempt) {
            if ($attempt->status !== AttemptStatus::IN_PROGRESS) {
                throw ValidationException::withMessages([
                    'error' => 'This attempt is no longer active.',
                ]);
            }

            $totalMaxScore = $attempt->test->questions()->sum('score');
            $totalStudentScore = $attempt->answers()->sum('score');

            $percentage = $totalMaxScore > 0
                ? (int) round(($totalStudentScore / $totalMaxScore) * 100)
                : 0;
            $attempt->update([
                'status'       => AttemptStatus::COMPLETED,
                'completed_at' => now(),
                'score'        => $percentage,
            ]);

            $test = $attempt->test;
            $passed = $percentage >= $test->passing_score;

            if ($passed) {
                    match ($test->testable_type) {
                        'lesson' => $this->handleLessonPass($test, $attempt->user),

                        'course' => $this->handleCoursePass(
                            $test,
                            $attempt->user
                        ),
                    default  => null,
                };
            }
            return $attempt->fresh();
        });
    }

    private function handleLessonPass($test, $user): void
    {
        $lesson = $test->testable;

        $user->lessons()->updateExistingPivot(
            $lesson->id,
            [
                'status' => 'completed',
                'completed_at' => now(),
            ]
        );
        StudentProfile::where('user_id', $user->id)
            ->increment('points', $lesson->xp_points);

        $this->studentLessonService->openNextLesson($lesson->course, $user);
    }
    private function handleCoursePass($test, $user): void
    {
        $course = $test->testable;

        $user->StudentCourses()->updateExistingPivot(
            $course->id,
            [
                'status' => 'completed',
                'completed_at' => now(),
            ]
        );

        $this->studentCourseService->openNextCourse(
            $course->level,
            $user
        );
    }

    public function leaveAttempt(UserAttempt $attempt): UserAttempt
    {
        if ($attempt->status !== AttemptStatus::IN_PROGRESS) {
            throw ValidationException::withMessages([
                'error' => 'This attempt is already finished.',
            ]);
        }

        $attempt->update([
            'status' => AttemptStatus::ABANDONED,
            'completed_at' => now(),
        ]);

        return $attempt->fresh();
    }

    public function review(UserAttempt $attempt): array
    {
        if ($attempt->status !== AttemptStatus::COMPLETED) {
            throw ValidationException::withMessages([
                'error' => 'This attempt is not completed yet.',
            ]);
        }

        $test = $attempt->test;
        $passed = $attempt->score >= $test->passing_score;

        if (!$passed) {
            throw ValidationException::withMessages([
                'error' => 'Detailed review is only available for passed attempts.',
            ]);
        }

        $wrongAnswers = $attempt->answers()
            ->with('question.mcqAnswers', 'question.fillAnswers', 'question.arrangeAnswers', 'question.pairAnswers')
            ->get()
            ->filter(fn($userAnswer) => $userAnswer->score < $userAnswer->question->score)
            ->map(function ($userAnswer) {
                $question = $userAnswer->question;

                return [
                    'question_id'      => $question->id,
                    'question_text'    => $question->title_question_en,
                    'type'             => $question->type,
                    'submitted_answer' => $userAnswer->answer_json,
                    'correct_answer'   => $this->getCorrectAnswer($question),
                    'score'            => $userAnswer->score,
                    'max_score'        => $question->score,
                ];
            })
            ->values();

        return [
            'attempt_id'   => $attempt->id,
            'total_score'  => $attempt->score,
            'wrong_answers' => $wrongAnswers,
        ];
    }

    private function getCorrectAnswer(Question $question): array
    {
        return match ($question->type) {
            QuestionType::MCQ => [
                'selected_answer_id' => $question->mcqAnswers
                    ->firstWhere('is_correct', true)
                    ?->id,
            ],

            QuestionType::FILL => [
                'answers' => $question->fillAnswers
                    ->groupBy('blank_order')
                    ->map(fn($group) => $group->pluck('text_answer')->all())
                    ->all(),
            ],

            QuestionType::ARRANGE => [
                'ordered_ids' => $question->arrangeAnswers
                    ->where('is_correct', true)
                    ->sortBy('order')
                    ->pluck('id')
                    ->values()
                    ->all(),
            ],

            QuestionType::PAIR => [
                'pairs' => $question->pairAnswers
                    ->pluck('id', 'id')
                    ->all(),
            ],
        };
    }
}
