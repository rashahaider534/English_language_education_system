<?php

namespace App\Services\ContentReview;

use App\Enums\ContentStatus;
use App\Enums\ReviewStatus;
use App\Models\ContentReview;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\Question;
use App\Models\Test;
use App\Services\Test\AdminTestService;
use App\Services\Test\TestService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminContentReviewService
{
    public AdminTestService $adminTestService;
    public TestService $testService;

    public function __construct(AdminTestService $adminTestService, TestService $testService)
    {
        $this->adminTestService = $adminTestService;
        $this->testService = $testService;
    }

//    public function reviewQueueCourseTests()
//    {
//        return Test::where('testable_type', 'course')
//            ->where('status', ContentStatus::PENDING->value)
//            ->with(['testable:id,name_ar,name_en,teacher_id', 'testable.teacher:id,first_name,last_name,email'])
//            ->paginate(10);
//    }
    public function pendingContentQueue()
    {
        $pendingLessonIds = Lesson::where('status', ContentStatus::PENDING)->pluck('id');

        $lessons = Lesson::query()
            ->select(['id', 'title_ar', 'title_en', 'course_id', 'order', 'status', 'created_at'])
            ->selectRaw("'lesson' as queue_type")
            ->with([
                'course:id,name_ar,name_en,teacher_id',
                'course.teacher:id,first_name,last_name,email',
            ])
            ->where('status', ContentStatus::PENDING)
            ->get();

        $tests = Test::query()
            ->select(['id', 'title_ar', 'title_en', 'testable_type', 'testable_id', 'status', 'created_at'])
            ->selectRaw("'test' as queue_type")
            ->where('status', ContentStatus::PENDING)
            ->where(function ($q) use ($pendingLessonIds) {
                $q->where('testable_type', 'course')
                    ->orWhere(function ($q2) use ($pendingLessonIds) {
                        $q2->where('testable_type', 'lesson')
                            ->whereNotIn('testable_id', $pendingLessonIds);
                    });
            })
            ->with(['testable' => function ($morphTo) {
                $morphTo->morphWith([
                    Course::class => ['teacher:id,first_name,last_name,email'],
                    Lesson::class => ['course.teacher:id,first_name,last_name,email'],
                ]);
            }])
            ->get();

        $merged = $lessons->concat($tests)->sortBy('created_at')->values();

        $page = (int) request('page', 1);
        $perPage = 10;

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $merged->forPage($page, $perPage),
            $merged->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function myReviewSessions(?string $status = null)
    {
        $latestReviewIds = ContentReview::where('reviewer_id', auth()->id())
            ->where('status', '!=', ReviewStatus::RELEASED)
            ->selectRaw('MAX(id) as id')
            ->groupBy('reviewable_type', 'reviewable_id')
            ->pluck('id');

        return ContentReview::whereIn('id', $latestReviewIds)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->with([
                'reviewable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Lesson::class => ['course:id,name_ar,name_en,teacher_id', 'course.teacher:id,first_name,last_name'],
                        Test::class   => ['testable'],
                    ]);
                },
                'notes' => fn ($q) => $q->latest(),
            ])
            ->latest('claimed_at')
            ->paginate(10);
    }
    public function claimLesson(Lesson $lesson): array
    {
        return DB::transaction(function () use ($lesson) {
            $lesson = Lesson::lockForUpdate()->find($lesson->id);
            $test = $lesson->activeTest()->lockForUpdate()->first();

            if (!$test) {
                throw ValidationException::withMessages([
                    'error' => 'This lesson has no associated test — data inconsistency.',
                ]);
            }

            if ($lesson->status !== ContentStatus::PENDING || $test->status !== ContentStatus::PENDING) {
                throw ValidationException::withMessages([
                    'error' => 'This lesson is no longer available in the queue. It may have already been claimed.',
                ]);
            }

            $reviewerId = auth()->id();
            $now = now();

            $lessonReview = $lesson->reviews()->create([
                'reviewer_id' => $reviewerId,
                'status' => ReviewStatus::IN_REVIEW,
                'claimed_at' => $now,
            ]);

            $testReview = $test->reviews()->create([
                'reviewer_id' => $reviewerId,
                'status' => ReviewStatus::IN_REVIEW,
                'claimed_at' => $now,
            ]);

            $lesson->update(['status' => ContentStatus::IN_REVIEW]);
            $test->update(['status' => ContentStatus::IN_REVIEW]);

            return ['lesson_review' => $lessonReview, 'test_review' => $testReview];
        });
    }

    public function claimTest(Test $test): ContentReview
    {
        return DB::transaction(function () use ($test) {
            $test = Test::lockForUpdate()->find($test->id);

            if ($test->status !== ContentStatus::PENDING) {
                throw ValidationException::withMessages([
                    'error' => 'This test is no longer available in the queue.',
                ]);
            }

            $review = $test->reviews()->create([
                'reviewer_id' => auth()->id(),
                'status' => ReviewStatus::IN_REVIEW,
                'claimed_at' => now(),
            ]);

            $test->update(['status' => ContentStatus::IN_REVIEW]);

            return $review;
        });
    }

    //هاد تبع اختبار المستوى وتحديد المستوى
    public function approveDirectly(Test $test): Test
    {
        return DB::transaction(function () use ($test) {
            $test = Test::where('id', $test->id)->lockForUpdate()->first();

            if ($test->status !== ContentStatus::DRAFT) {
                throw ValidationException::withMessages([
                    'error' => 'This test cannot be approved from its current status.',
                ]);
            }

            $existingApproved = Test::where('testable_type', $test->testable_type)
                ->where('testable_id', $test->testable_id)
                ->where('status', ContentStatus::APPROVED->value)
                ->where('id', '!=', $test->id)
                ->lockForUpdate()
                ->exists();

            if ($existingApproved) {
                throw ValidationException::withMessages([
                    'error' => 'There is already an approved test you cant approve this test unless you handle the approved test.',
                ]);
            }

            $questions = $test->questions()->withTrashed()->with('nextVersion')->get();

            $deletedQuestions = $questions->filter(fn ($q) => $q->trashed());
            if ($deletedQuestions->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'error' => 'This test references question(s) that no longer exist. Please replace them.',
                    'deleted_question_ids' => $deletedQuestions->pluck('id'),
                ]);
            }

            $outdatedQuestions = $questions->filter(fn ($q) => $q->nextVersion !== null);
            if ($outdatedQuestions->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'error' => 'This test uses outdated question version(s). Please update them to the latest version before approving.',
                    'outdated_question_ids' => $outdatedQuestions->pluck('id'),
                ]);
            }

            if ($test->testable_type === 'level') {
                $this->adminTestService->checkQuestionsAvailableForLevel($test->testable_id, $questions->pluck('id'));
            } elseif ($test->testable_type === 'placement_test') {
                $this->adminTestService->checkQuestionsAreValidPlacementQuestions($questions->pluck('id'));
            }

            $test->update(['status' => ContentStatus::APPROVED]);

            return $test;
        });
    }

    public function approve(ContentReview $review): void
    {
        DB::transaction(function () use ($review) {
            $review = ContentReview::lockForUpdate()->find($review->id);

            $this->assertActiveReviewer($review);

            $reviewable = $review->reviewable;

            if ($reviewable instanceof Test && !$this->testService->isTestStillEligible($reviewable)) {
                throw ValidationException::withMessages([
                    'error' => 'This test contains a question that is no longer eligible. Please return it for changes.',
                ]);
            }

            $review->update(['status' => ReviewStatus::APPROVED, 'completed_at' => now()]);
            $reviewable->update(['status' => ContentStatus::APPROVED]);
        });
    }

    public function requestChanges(ContentReview $review, string $message): void
    {
        DB::transaction(function () use ($review, $message) {
            $review = ContentReview::lockForUpdate()->find($review->id);
            $this->assertActiveReviewer($review);

            $review->update(['status' => ReviewStatus::CHANGES_REQUESTED, 'completed_at' => now()]);
            $review->reviewable->update(['status' => ContentStatus::CHANGES_REQUESTED]);

            $review->notes()->create([
                'admin_id' => auth()->id(),
                'message' => $message,
                'is_system_generated' => false,
                'reviewable_type' => $review->reviewable_type,
                'reviewable_id' => $review->reviewable_id,
            ]);
        });
    }

    private function assertActiveReviewer(ContentReview $review): void
    {
        if ($review->status !== ReviewStatus::IN_REVIEW) {
            throw ValidationException::withMessages([
                'error' => 'This review is no longer awaiting your decision — it may have changed. Please refresh.',
            ]);
        }

        if ($review->reviewer_id !== auth()->id()) {
            throw ValidationException::withMessages([
                'error' => 'You are not the reviewer assigned to this task.',
            ]);
        }
    }
    public function release(ContentReview $review): void
    {
        DB::transaction(function () use ($review) {
            $review = ContentReview::lockForUpdate()->find($review->id);

            if ($review->status !== ReviewStatus::IN_REVIEW) {
                throw ValidationException::withMessages([
                    'error' => 'This review is not currently open.',
                ]);
            }

            if ($review->reviewer_id !== auth()->id()) {
                throw ValidationException::withMessages([
                    'error' => 'You are not the reviewer assigned to this task.',
                ]);
            }

            $sibling = $this->getSiblingReview($review);
           // dd($sibling);
            if ($sibling) {
                if (in_array($sibling->status, [ReviewStatus::APPROVED, ReviewStatus::CHANGES_REQUESTED])) {
                    throw ValidationException::withMessages([
                        'error' => 'You have already decided on the related item. Please complete your review of this one as well before releasing.',
                    ]);
                }

                if ($sibling->status === ReviewStatus::IN_REVIEW) {
                    $sibling->update(['status' => ReviewStatus::RELEASED, 'completed_at' => now()]);
                    $sibling->reviewable->update(['status' => ContentStatus::PENDING]);
                }
            }

            $review->update(['status' => ReviewStatus::RELEASED, 'completed_at' => now()]);
            $review->reviewable->update(['status' => ContentStatus::PENDING]);
        });
    }

    private function getSiblingReview(ContentReview $review): ?ContentReview
    {
        $reviewable = $review->reviewable;

        if ($reviewable instanceof Lesson) {
            $sibling = $reviewable->activeTest;
        } elseif ($reviewable instanceof Test && $reviewable->testable_type === 'lesson') {
            $sibling = $reviewable->testable;
        } else {
            return null;
        }

        $activeReviewCycleStatuses = [
            ContentStatus::IN_REVIEW,
            ContentStatus::CHANGES_REQUESTED,
            ContentStatus::APPROVED,
        ];

        if (!$sibling || !in_array($sibling->status, $activeReviewCycleStatuses)) {
            return null;
        }

        return $sibling->reviews()->latest('claimed_at')->first();
    }

    public function publishTest(Test $test): void
    {
        DB::transaction(function () use ($test) {
            $test = Test::where('id', $test->id)->lockForUpdate()->first();

            if ($test->status !== ContentStatus::APPROVED) {
                throw ValidationException::withMessages([
                    'error' => 'This test cannot be published from its current status.',
                ]);
            }

            if ($test->previous_test_id) {
                $parentPublished = match ($test->testable_type) {
                    'lesson' => Lesson::find($test->testable_id)?->status === ContentStatus::PUBLISHED,
                    'course' => Course::find($test->testable_id)?->status === 'published',
                    'level'  => Level::find($test->testable_id)?->status === 'published',
                    'placement_test' => true,
                    default => false,
                };

                if (!$parentPublished) {
                    throw ValidationException::withMessages([
                        'error' => 'Cannot publish a new test version before its parent content is published.',
                    ]);
                }
            }

            $test->update(['status' => ContentStatus::PUBLISHED]);

            $previousTestId = $test->previous_test_id;

            if ($test->testable_type === 'placement_test' && !$previousTestId) {
                $previousTestId = Test::where('testable_type', 'placement_test')
                    ->where('status', ContentStatus::PUBLISHED)
                    ->where('id', '!=', $test->id)
                    ->value('id');
            }

            if ($previousTestId) {
                $oldQuestionIds = Test::find($previousTestId)->questions()->pluck('questions.id')->all();
                $newQuestionIds = $test->questions()->pluck('questions.id')->all();
                $removedQuestionIds = array_values(array_diff($oldQuestionIds, $newQuestionIds));

                $this->archivePreviousVersion($previousTestId);

                if ($test->testable_type === 'lesson' && !empty($removedQuestionIds)) {
                    $this->testService->revalidateDependentTests($removedQuestionIds, true);
                }
            }

            foreach ($test->questions as $question) {
                if ($question->previous_question_id) {
                    $this->checkIfQuestionCanBeArchived($question->previous_question_id);
                }
            }
        });
    }
    private function archivePreviousVersion($previousTestId): void
    {
        Test::where('id', $previousTestId)->update(['status' => ContentStatus::ARCHIVED]);
    }

    private function checkIfQuestionCanBeArchived($questionId):void
    {
        $oldQuestion = Question::find($questionId);

        $isStillInPublishedTest = $oldQuestion->tests()
            ->whereIn('status', [ContentStatus::PUBLISHED , ContentStatus::CLOSED])
            ->exists();

        if (!$isStillInPublishedTest) {
            $oldQuestion->delete();
        }
    }

    public function publishLevel(Level $level): void
    {
        DB::transaction(function () use ($level) {
            $level = Level::where('id', $level->id)->lockForUpdate()->first();

            $courses = Course::where('level_id', $level->id)->get();
            $lessons = Lesson::whereIn('course_id', $courses->pluck('id'))->get();

            $incompleteLessons = $lessons->where('status', '!=', ContentStatus::APPROVED);
            if ($incompleteLessons->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'error' => 'Not all lessons are approved yet.',
                    'lesson_ids' => $incompleteLessons->pluck('id'),
                ]);
            }

            $lessonTests = Test::where('testable_type', 'lesson')
                ->whereIn('testable_id', $lessons->pluck('id'))
                ->where('status', ContentStatus::APPROVED)
                ->get();

            $courseTests = Test::where('testable_type', 'course')
                ->whereIn('testable_id', $courses->pluck('id'))
                ->where('status', ContentStatus::APPROVED)
                ->get();

            $levelTest = Test::where('testable_type', 'level')
                ->where('testable_id', $level->id)
                ->where('status', ContentStatus::APPROVED)
                ->first();

            $lessonsWithApprovedTest = $lessonTests->pluck('testable_id')->unique();
            $lessonsMissingApprovedTest = $lessons->pluck('id')->diff($lessonsWithApprovedTest);

            if ($lessonsMissingApprovedTest->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'error' => 'Some lessons do not have an approved test.',
                    'lesson_ids' => $lessonsMissingApprovedTest->values(),
                ]);
            }

            $coursesWithApprovedTest = $courseTests->pluck('testable_id')->unique();
            $coursesMissingApprovedTest = $courses->pluck('id')->diff($coursesWithApprovedTest);

            if ($coursesMissingApprovedTest->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'error' => 'Some courses do not have an approved test.',
                    'course_ids' => $coursesMissingApprovedTest->values(),
                ]);
            }

            if (!$levelTest) {
                throw ValidationException::withMessages([
                    'error' => 'This level does not have an approved test.',
                ]);
            }

            $allTests = $lessonTests->concat($courseTests)->push($levelTest);

            foreach ($lessons as $lesson) {
                $lesson->update(['status' => ContentStatus::PUBLISHED]);
            }
            foreach ($courses as $course) {
                $course->update(['status' => 'published']);
            }
            foreach ($allTests as $test) {
                $this->publishTest($test);
            }

            $level->update(['status' => 'published']);
        });
    }

    public function revertApprovedLesson(Lesson $lesson, string $message): ContentReview
    {
        return DB::transaction(function () use ($lesson, $message) {
            $lesson = Lesson::where('id', $lesson->id)->lockForUpdate()->first();

            if ($lesson->status !== ContentStatus::APPROVED) {
                throw ValidationException::withMessages([
                    'error' => 'This lesson is not currently approved.',
                ]);
            }

            $review = $lesson->reviews()->create([
                'reviewer_id' => auth()->id(),
                'status' => 'changes_requested',
                'claimed_at' => now(),
                'completed_at' => now(),
            ]);

            $review->notes()->create([
                'admin_id' => auth()->id(),
                'message' => $message,
                'is_system_generated' => false,
                'reviewable_type' => 'lesson',
                'reviewable_id' => $lesson->id,
            ]);

            $lesson->update(['status' => ContentStatus::CHANGES_REQUESTED]);

            return $review;
        });
    }
}
