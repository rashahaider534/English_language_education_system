<?php

namespace App\Services\ContentReview;

use App\Enums\ContentStatus;
use App\Models\ContentReview;
use App\Models\Lesson;
use App\Models\Test;
use App\Services\Test\TestService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ContentReviewService
{
    public TestService $testService;

    public function __construct(TestService $testService)
    {
        $this->testService = $testService;
    }

    public function submitLesson(Lesson $lesson): array
    {
        return DB::transaction(function () use ($lesson) {
            $lesson = Lesson::where('id', $lesson->id)->lockForUpdate()->first();

            $draftTests = $lesson->tests()
                ->where('status', ContentStatus::DRAFT)
                ->lockForUpdate()
                ->get();

            if ($draftTests->count() > 1) {
                throw ValidationException::withMessages([
                    'error' => 'There is more than one draft test please pick one and delete the others.',
                    'draft_test_ids' => $draftTests->pluck('id'),
                ]);
            }

            $test = $draftTests->first();

            if (!$test) {
                throw ValidationException::withMessages([
                    'error' => 'This lesson does not have a draft test.',
                ]);
            }

            if ($lesson->status !== ContentStatus::DRAFT) {
                throw ValidationException::withMessages([
                    'error' => 'You cant submit this lesson from ots current status',
                ]);
            }

            $this->validateLessonForSubmission($lesson);
            $this->validateTestForSubmission($test);

            $lesson->update(['status' => ContentStatus::PENDING]);
            $test->update(['status' => ContentStatus::PENDING]);

            return ['lesson' => $lesson, 'test' => $test];
        });
    }

    public function resubmitLesson(Lesson $lesson): array
    {
        return DB::transaction(function () use ($lesson) {
            $lesson = Lesson::where('id', $lesson->id)->lockForUpdate()->first();

            if ($lesson->status !== ContentStatus::CHANGES_REQUESTED) {
                throw ValidationException::withMessages([
                    'error' => 'This lesson is not waiting for resubmission.',
                ]);
            }

            $this->validateLessonForSubmission($lesson);

            $review = $this->resubmit($lesson);

            $lesson->update(['status' => ContentStatus::IN_REVIEW]);

            return ['lesson' => $lesson, 'review' => $review];
        });
    }

    private function validateLessonForSubmission(Lesson $lesson): void
    {
        if (!$lesson->getFirstMedia('videos')) {
            throw ValidationException::withMessages([
                'error' => 'You have to upload a video first.',
            ]);
        }
    }

    public function submitTestForReview(Test $test)
    {
        return DB::transaction(function () use ($test) {
            $test = Test::where('id', $test->id)->lockForUpdate()->first();

            if ($test->status !== ContentStatus::DRAFT) {
                throw ValidationException::withMessages([
                    'error' => 'You cant submit this test from ots current status ',
                ]);
            }
            $this->validateTestForSubmission($test);

            $test->update(['status' => ContentStatus::PENDING]);

            return ['status' => 'submitted', 'test' => $test];
        });
    }

    public function resubmitTestAfterChanges(Test $test)
    {
        return DB::transaction(function () use ($test) {
            $test = Test::where('id', $test->id)->lockForUpdate()->first();

            if ($test->status !== ContentStatus::CHANGES_REQUESTED) {
                throw ValidationException::withMessages([
                    'error' => 'This test is not waiting for resubmission.',
                ]);
            }

            $this->validateTestForSubmission($test);

            $newReview = $this->resubmit($test);

            $test->update(['status' => ContentStatus::IN_REVIEW]);

            return ['status' => 'resubmitted', 'test' => $test, 'review' => $newReview];
        });
    }
    public function resubmit($reviewable): ContentReview
    {
        $lastReview = $reviewable->reviews()
            ->where('status', 'changes_requested')
            ->latest('completed_at')
            ->first();

        if (!$lastReview) {
            throw new \RuntimeException('Cannot resubmit: no prior changes_requested review found.');
        }

        return $reviewable->reviews()->create([
            'reviewer_id' => $lastReview->reviewer_id,
            'status' => 'in_review',
            'claimed_at' => now(),
        ]);
    }
    private function validateTestForSubmission(Test $test): void
    {
        $questions = $test->questions()->withTrashed()->with('nextVersion')->get();

        $deletedQuestions = $questions->filter(fn ($q) => $q->trashed());
        if ($deletedQuestions->isNotEmpty()) {
            throw ValidationException::withMessages([
                'error' => 'This test has one (or more) question that is deleted please review.',
                'deleted_question_ids' => $deletedQuestions->pluck('id'),
            ]);
        }

        $outdatedQuestions = $questions->filter(fn ($q) => $q->nextVersion !== null);
        if ($outdatedQuestions->isNotEmpty()) {
            throw ValidationException::withMessages([
                'error' => 'This test has one (or more) question that is outdated please review.',
                'outdated_question_ids' => $outdatedQuestions->pluck('id'),
            ]);
        }
        if ($test->testable_type === 'course') {
            $this->testService->checkQuestionsAvailableForCourse($test->testable_id, $questions->pluck('id'));
        }

    }

    public function reviewHistory(Model $reviewable)
    {
        $reviews = $reviewable->reviews()
            ->with([
                'reviewer:id,first_name,last_name',
                'notes' => fn ($q) => $q->latest(),
            ])
            ->get()
            ->map(fn ($review) => [
                'type' => 'review_session',
                'timestamp' => $review->completed_at ?? $review->claimed_at,
                'data' => $review,
            ]);

        $systemNotes = $reviewable->reviewNotes()
            ->whereNull('content_review_id')
            ->latest()
            ->get()
            ->map(fn ($note) => [
                'type' => 'system_note',
                'timestamp' => $note->created_at,
                'data' => $note,
            ]);

        return $reviews->concat($systemNotes)
            ->sortBy('timestamp')
            ->values();
    }

}


