<?php

namespace App\Services;

use App\Enums\ContentStatus;
use App\Http\Resources\Question\TeacherQuestionResource;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Test;
use App\Services\Test\TestService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;

class QuestionService
{

    protected TestService $testService;

    public function __construct(TestService $testService)
    {
        $this->testService = $testService;
    }
    public function index()
    {
        $user = auth()->user();
        $activeQuestions = Question::where('user_id', $user->id)
            ->whereDoesntHave('nextVersion')
            ->latest()
            ->paginate(10);

        return response()->json([
            'active_questions' => TeacherQuestionResource::collection($activeQuestions)->response()->getData(true),
              ]);

    }

    public function ArchivedQuestions()
    {
        $user = auth()->user();
        $archivedVersions = Question::where('user_id', $user->id)
            ->hasChildren()
            ->latest()
            ->paginate(10);

        return response()->json([
            'deprecated_questions' => TeacherQuestionResource::collection($archivedVersions)->response()->getData(true),
        ]);

    }

    public function show(Question $question)
    {
        $question->load($question->getAnswersRelationName());

        return new TeacherQuestionResource($question);
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['user_id'] = auth()->id();
            $questionData = Arr::except($data, ['answers']);
            $question = Question::create($questionData);

            if (isset($data['audio'])) {
                $question->addMedia($data['audio'])->toMediaCollection('audio');
            }
            if (isset($data['image'])) {
                $question->addMedia($data['image'])->toMediaCollection('image');
            }
            if ($question->type === 'MCQ') {
                $correctAnswersCount = collect($data['answers'])->where('is_correct', true)->count();
                if ($correctAnswersCount > 1) {
                    throw ValidationException::withMessages(['It cant have more than one correct answer']);
                }
            }
            $relation = $question->getAnswersRelationName();
            //  dd($relation, $data['answers']);
            $question->{$relation}()->createMany($data['answers']);

            $question->load($relation);
            return $question;

        });
    }

    public function checkStatus(Question $question)
    {
        $publishedTests = $question->published_tests;
        $archivedTests = $question->archived_tests;
        $inReviewTests = $question->in_review_tests;
        $approvedTests = $question->approved_tests;
        $closedTests = $question->closed_tests;

        $isPublished = $publishedTests->isNotEmpty();
        $isArchived = $archivedTests->isNotEmpty();
        $isInReview = $inReviewTests->isNotEmpty();
        $isApproved = $approvedTests->isNotEmpty();
        $isClosed = $closedTests->isNotEmpty();

        $status = 'Editable.';
        $message = 'You can edit this question directly.';

        if ($isInReview) {
            $status = 'locked_in_review';
            $message = 'This question belongs to a test currently under review and cannot be edited until the review is completed.';
        } elseif ($isPublished) {
            $status = 'locked';
            $message = 'This question is related to published tests. A new version will be created and you will need to update the related tests.';
        } elseif ($isArchived || $isClosed) {
            $status = 'versioned';
            $message = 'This question is related to archived or closed tests. A new version will be created.';
        }

        if ($isApproved) {
            $message .= ' Note: this question is also used in approved test(s) that will be reverted to changes requested and require re-review after this edit.';
        }

        return [
            'status' => $status,
            'affected_published_tests' => $publishedTests,
            'affected_archived_tests' => $archivedTests,
            'affected_closed_tests' => $closedTests,
            'affected_in_review_tests' => $inReviewTests,
            'affected_approved_tests' => $approvedTests,
            'will_revert_to_pending' => $isApproved,
            'message' => $message,
        ];
    }

    public function updateQuestion(Question $question, array $data)
    {
        return DB::transaction(function () use ($question, $data) {

            $relatedTests = $question->tests()->orderBy('id')->lockForUpdate()->get();

            if ($relatedTests->contains(fn($t) => $t->status === ContentStatus::IN_REVIEW)) {
                throw ValidationException::withMessages([
                    'error' => 'This question belongs to a test currently under review and cannot be edited.'
                ]);
            }

            if ($question->isUsedInPublishedTests() || $question->isUsedInArchivedTests() || $question->isUsedInClosedTests()) {

                $data['type'] = $question->type;
                $data['previous_question_id'] = $question->id;
                $data['user_id'] = auth()->id();
                $newQuestion = Question::create($data);
                $this->syncAnswersAndMedia($question, $newQuestion, $data);

                if (!$question->isUsedInPublishedTests() && !$question->isUsedInClosedTests()) {
                    $question->delete();
                }
                $this->cascadeApprovedTestsToChangesRequested($relatedTests);

                $relinkableTests = Test::whereIn('id', $relatedTests->pluck('id'))
                    ->whereIn('status', [
                        ContentStatus::DRAFT,
                        ContentStatus::PENDING,
                        ContentStatus::CHANGES_REQUESTED,
                    ])
                    ->get();

                foreach ($relinkableTests as $test) {

                    DB::table('test_questions')
                        ->where('test_id', $test->id)
                        ->where('question_id', $question->id)
                        ->update([
                            'question_id' => $newQuestion->id
                        ]);
                }
                return [
                    'status' => 'versioned',
                    'message' => 'a new version has been created.',
                    'question' => new TeacherQuestionResource($newQuestion)
                ];
            }

            $question->update($data);
            //  dd($data['answers']);
            $this->syncAnswersAndMedia($question, $question, $data, $isUpdate = true);
            $this->cascadeApprovedTestsToChangesRequested($relatedTests);
            return [
                'status' => 'updated',
                'message' => 'question updated successfully.',
                'question' => $question
            ];
        });

    }

    private function syncAnswersAndMedia(Question $oldQuestion, Question $newQuestion, array $data, $isUpdate = false)
    {

        $relation = $newQuestion->getAnswersRelationName();
        // dd($relation);
        if (isset($data['answers'])) {
            if ($isUpdate) {

                $newQuestion->{$relation}()->delete();
            }
                $newQuestion->{$relation}()->createMany($data['answers']);

            if (isset($data['image'])) {
                $newQuestion->addMedia($data['image'])->toMediaCollection('image');
            } else {
                if(!$isUpdate)
                {$oldQuestion->getFirstMedia('image')?->copy($newQuestion, 'image');}
                }

            if (isset($data['audio'])) {
                $newQuestion->addMedia($data['audio'])->toMediaCollection('audio');
            } else {
                if(!$isUpdate)
                $oldQuestion->getFirstMedia('audio')?->copy($newQuestion, 'audio');
            }
        }
    }

    public function deleteQuestion(Question $question)
        {
            return DB::transaction(function () use ($question) {

                $relatedTests = $question->tests()->orderBy('id')->lockForUpdate()->get();

                if ($relatedTests->contains(fn($t) => $t->status === ContentStatus::IN_REVIEW)) {
                    throw ValidationException::withMessages([
                        'error' => 'This question belongs to a test currently under review and cannot be deleted.',
                    ]);
                }

                if ($question->isUsedInPublishedTests() || $question->isUsedInClosedTests()) {
                    throw ValidationException::withMessages([
                        'error' => 'You cannot delete this question because it is used in published tests.',
                    ]);
                }
                $this->cascadeApprovedTestsToChangesRequested($relatedTests);

                if ($question->isUsedInArchivedTests()) {
                    return $question->delete();
                }

                return $question->forceDelete();
            });
        }private function cascadeApprovedTestsToChangesRequested($tests): void
        {
            $approvedTestIds = $tests
                ->where('status', ContentStatus::APPROVED)
                ->pluck('id');

            if ($approvedTestIds->isNotEmpty()) {
                Test::whereIn('id', $approvedTestIds)
                    ->update(['status' => ContentStatus::CHANGES_REQUESTED]);
            }
        }
    public function blockingTests(Question $question)
    {
        $tests = $question->tests()
            ->whereIn('status',[ContentStatus::PUBLISHED,ContentStatus::CLOSED] )
            ->get(['tests.id', 'tests.title_en', 'tests.title_ar', 'tests.testable_type', 'tests.testable_id']);

        return response()->json(['blocking_tests' => $tests]);
    }

    public function filter(array $data)
    {
        $query = Question::where('user_id', auth()->id());

        if (!empty($data['type'])) {
            $query->where('type', $data['type']);
        }

        if (!empty($data['difficulty'])) {
            $query->where('difficulty', $data['difficulty']);
        }

        if (isset($data['min_score'])) {
            $query->where('score', '>=', $data['min_score']);
        }

        if (isset($data['max_score'])) {
            $query->where('score', '<=', $data['max_score']);
        }

        if (!empty($data['search'])) {
            $search = $data['search'];

            $query->where(function ($q) use ($search) {
                $q->where('title_question_en', 'like', "%{$search}%")
                    ->orWhere('title_question_ar', 'like', "%{$search}%");
            });
        }

        $courseId = $data['course_id'] ?? null;
        $onlyEligibleProvided = array_key_exists('only_eligible', $data);
        $onlyEligible = filter_var($data['only_eligible'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $eligibleIds = null;

        if ($onlyEligibleProvided) {
            $course = Course::findOrFail($courseId);

            if ($course->teacher_id !== auth()->id()) {
                abort(403);
            }

            $lessonsIds = Lesson::where('course_id', $courseId)->pluck('id');

            $questionIdsOwnedByTeacher = Question::where('user_id', auth()->id())
                ->pluck('id');

            $eligibleIds = $this->testService
                ->eligibleQuestionIdsFromLessonTests($questionIdsOwnedByTeacher, $lessonsIds);

            if ($onlyEligible) {
                $query->whereIn('id', $eligibleIds);
            }
        } elseif ($courseId) {
            $course = Course::findOrFail($courseId);

            if ($course->teacher_id !== auth()->id()) {
                abort(403);
            }

            $lessonsIds = Lesson::where('course_id', $courseId)->pluck('id');

            $query->whereHas('tests', function ($q) use ($lessonsIds) {
                $q->where('testable_type', 'lesson')
                    ->whereIn('testable_id', $lessonsIds);
            });
        }

        $sortDirection = $data['sort'] ?? 'desc';

        $query->orderBy('created_at', $sortDirection);

        $paginated = $query->paginate(10);

        if ($onlyEligibleProvided && !$onlyEligible) {
            $paginated->getCollection()->transform(function ($q) use ($eligibleIds) {
                $q->is_eligible = in_array($q->id, $eligibleIds);

                return $q;
            });
        }

        return $paginated;
    }
}
