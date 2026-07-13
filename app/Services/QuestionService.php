<?php

namespace App\Services;

use App\Enums\ContentStatus;
use App\Http\Resources\Question\TeacherQuestionResource;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuestionService
{

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
            return new teacherQuestionResource($question);

        });
    }

    public function checkStatus(Question $question)
    {
        $publishedTests = $question->published_tests;
        $archivedTests = $question->archived_tests;
        $inReviewTests = $question->in_review_tests;
        $approvedTests = $question->approved_tests;

        $isPublished = $publishedTests->isNotEmpty();
        $isArchived = $archivedTests->isNotEmpty();
        $isInReview = $inReviewTests->isNotEmpty();
        $isApproved = $approvedTests->isNotEmpty();

        $status = 'Editable.';
        $message = 'You can edit this question directly.';

        if ($isInReview) {
            $status = 'locked_in_review';
            $message = 'This question belongs to a test currently under review and cannot be edited until the review is completed.';
        } elseif ($isPublished) {
            $status = 'locked';
            $message = 'This question is related to published tests. A new version will be created and you will need to update the related tests.';
        } elseif ($isArchived) {
            $status = 'versioned';
            $message = 'This question is related to archived tests. A new version will be created.';
        }

        if ($isApproved) {
            $message .= ' Note: this question is also used in approved test(s) that will be reverted to pending and require re-review after this edit.';
        }

        return [
            'status' => $status,
            'affected_published_tests' => $publishedTests,
            'affected_archived_tests' => $archivedTests,
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

            if ($relatedTests->contains(fn($t) => $t->status === ContentStatus::IN_REVIEW->value)) {
                throw ValidationException::withMessages([
                    'error' => 'This question belongs to a test currently under review and cannot be edited.'
                ]);
            }

            if ($question->isUsedInPublishedTests() || $question->isUsedInArchivedTests()) {

                $data['type'] = $question->type;
                $data['previous_question_id'] = $question->id;
                $data['user_id'] = auth()->id();
                $newQuestion = Question::create($data);
                $this->syncAnswersAndMedia($question, $newQuestion, $data);

                if (!$question->isUsedInPublishedTests()) {
                    $question->delete();
                }
                $this->cascadeApprovedTestsToDraft($relatedTests);

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
            $this->cascadeApprovedTestsToDraft($relatedTests);
            return [
                'status' => 'updated',
                'message' => 'question updated successfully.',
                'question' => new TeacherQuestionResource($question)
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

        public
        function deleteQuestion(Question $question)
        {
            return DB::transaction(function () use ($question) {

                $relatedTests = $question->tests()->orderBy('id')->lockForUpdate()->get();

                if ($relatedTests->contains(fn($t) => $t->status === ContentStatus::IN_REVIEW->value)) {
                    throw ValidationException::withMessages([
                        'error' => 'This question belongs to a test currently under review and cannot be deleted.',
                    ]);
                }

                if ($question->isUsedInPublishedTests()) {
                    throw ValidationException::withMessages([
                        'error' => 'You cannot delete this question because it is used in published tests.',
                    ]);
                }
                $this->cascadeApprovedTestsToDraft($relatedTests);

                if ($question->isUsedInArchivedTests()) {
                    return $question->delete();
                }

                return $question->forceDelete();
            });
        }



        private
        function cascadeApprovedTestsToDraft($tests): void
        {
            $approvedTestIds = $tests
                ->where('status', ContentStatus::APPROVED)
                ->pluck('id');

            if ($approvedTestIds->isNotEmpty()) {
                Test::whereIn('id', $approvedTestIds)
                    ->update(['status' => ContentStatus::DRAFT]);
            }
        }

    public function blockingTests(Question $question)
    {
        $tests = $question->tests()
            ->where('status', ContentStatus::PUBLISHED)
            ->get(['tests.id', 'tests.title_en', 'tests.title_ar', 'tests.testable_type', 'tests.testable_id']);

        return response()->json(['blocking_tests' => $tests]);
    }

}
