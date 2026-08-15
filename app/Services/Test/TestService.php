<?php

namespace App\Services\Test;
 use App\Enums\ContentStatus;
 use App\Enums\ReviewStatus;
 use App\Http\Resources\Test\TeacherTestResource;
 use App\Jobs\SendNotificationJob;
 use App\Models\Course;
 use App\Models\Lesson;
 use App\Models\ContentReview;
 use App\Models\PlacementTest;
 use App\Models\Question;
 use App\Models\Test;
 use App\Models\User;
 use Illuminate\Support\Arr;
 use Illuminate\Support\Facades\DB;
 use Illuminate\Validation\ValidationException;


 class TestService
 {
     private ?AdminTestService $adminTestServiceInstance = null;

     private function adminTestService(): AdminTestService
     {
         return $this->adminTestServiceInstance ??= app(AdminTestService::class);
     }

//     public function index()
//     {
//         $teacherId = auth()->id();
//
//         $courseIds = Course::where('teacher_id', $teacherId)->pluck('id');
//         $lessonIds = Lesson::whereIn('course_id', $courseIds)->pluck('id');
//
//         $tests = Test::where(function ($query) use ($courseIds, $lessonIds) {
//             $query->where('testable_type', 'course')->whereIn('testable_id', $courseIds);
//         })->orWhere(function ($query) use ($lessonIds) {
//             $query->where('testable_type', 'lesson')->whereIn('testable_id', $lessonIds);
//         })->get();
//
//         return $tests;
//
//     }
     public function index()
     {
         $teacherId = auth()->id();

         $courseIds = Course::where('teacher_id', $teacherId)->pluck('id');
         $lessonIds = Lesson::whereIn('course_id', $courseIds)->pluck('id');

         $tests = Test::where(function ($query) use ($courseIds, $lessonIds) {
             $query->where('testable_type', 'course')->whereIn('testable_id', $courseIds);
         })
             ->orWhere(function ($query) use ($lessonIds) {
                 $query->where('testable_type', 'lesson')->whereIn('testable_id', $lessonIds);
             })
             ->with(['testable', 'latestReview.notes'])
             ->get();

         $tests->transform(function ($test) {
             $test->review_notes = $test->status === ContentStatus::CHANGES_REQUESTED
                 ? $test->latestReview?->notes
                 : collect();

             unset($test->latestReview);

             return $test;
         });

         return $tests;
     }

     public function show(Test $test)
     {
         $test->load('questions');
         $test->questions->each(function ($question) {
             $relation = $question->getAnswersRelationName();
             $question->load($relation);
         });
         return $test;
 }

     public function store(array $data)
     {
         return DB::transaction(function () use ($data) {
             return $this->createTest($data);
         });
     }

     public function createTest(array $data)
     {
             $questions_ids = collect($data['questions'])->pluck('id');
             if($data['testable_type'] === 'course')
             {
                 $this->checkQuestionsAvailableForCourse($data['testable_id'], $questions_ids);
             }
             $testData = Arr::except($data, ['questions']);
             $test = Test::create($testData);
             $syncData = collect($data['questions'])
                 ->mapWithKeys(fn ($q) => [$q['id'] => ['order' => $q['order']]])
                 ->all();
             $test->questions()->sync($syncData);
             return $test;
     }

     public function eligibleQuestionIdsFromLessonTests($questionIds, $lessonIds): array
     {
         return Question::whereIn('id', $questionIds)
             ->whereHas('tests', function ($q) use ($lessonIds) {
                 $q->where('testable_type', 'lesson')
                     ->whereIn('testable_id', $lessonIds)
                     ->whereIn('status', [ContentStatus::APPROVED, ContentStatus::PUBLISHED]);
             })
             ->pluck('id')
             ->all();
     }

//     public function checkQuestionsAvailableForCourse($testable_id, $questions_ids, bool $throwOnFailure = true): bool
//     {
//         $lessons_ids = Lesson::where('course_id', $testable_id)->pluck('id');
//
//         $available_questions = Question::whereIn('id', $questions_ids)
//             ->whereHas('tests', function ($q) use ($lessons_ids) {
//                 $q->where('testable_type', 'lesson')
//                     ->whereIn('testable_id', $lessons_ids)
//                     ->whereIn('status', [ContentStatus::APPROVED, ContentStatus::PUBLISHED]);
//             })->count();
//
//         $isValid = $available_questions === $questions_ids->count();
//
//         if (!$isValid && $throwOnFailure) {
//             throw ValidationException::withMessages([
//                 'questions' => 'One or more questions are not eligible for this course test.',
//             ]);
//         }
//         return $isValid;
//     }
     public function checkQuestionsAvailableForCourse($testable_id, $questions_ids, bool $throwOnFailure = true): bool
     {
         $lessons_ids = Lesson::where('course_id', $testable_id)->pluck('id');
         $eligible = $this->eligibleQuestionIdsFromLessonTests($questions_ids, $lessons_ids);

         $totalCount = is_countable($questions_ids) ? count($questions_ids) : $questions_ids->count();
         $isValid = count($eligible) === $totalCount;

         if (!$isValid && $throwOnFailure) {
             throw ValidationException::withMessages([
                 'questions' => 'One or more questions are not eligible for this course test.',
             ]);
         }

         return $isValid;
     }


     public function update(Test $test,array $data)
     {
         return DB::transaction(function () use ($data, $test) {

             $test = Test::where('id', $test->id)->lockForUpdate()->first();
             if($test->status === ContentStatus::IN_REVIEW)
             {
                 throw ValidationException::withMessages([
                     'error' => 'This test currently under review and cannot be edited.'
                 ]);
             }
             if($test->status === ContentStatus::ARCHIVED || $test->status === ContentStatus::CLOSED)
             {
                 throw ValidationException::withMessages([
                     'error' => 'This test is archived and cannot be edited.'
                 ]);
             }

             $oldQuestionIds = $test->questions()->pluck('questions.id')->all();
             $newQuestionIds = collect($data['questions'])->pluck('id')->all();
             $removedQuestionIds = array_diff($oldQuestionIds, $newQuestionIds);

             logger('before revalidate', ['removedQuestionIds' => $removedQuestionIds]);
             if ($test->status === ContentStatus::PUBLISHED)
             {
                 $data['testable_id'] = $test->testable_id;
                 $data['testable_type'] = $test->testable_type;
                 $data['previous_test_id'] = $test->id;
                 $newTest = $this->store($data);
                 $result =   new TeacherTestResource($newTest);
             }else{
                 if ($test->testable_type === 'course')
                 {
                     $this->checkQuestionsAvailableForCourse($test->testable_id, collect($data['questions'])->pluck('id'));
                 }
                 $testData = Arr::except($data, ['questions']);
                 if($test->status === ContentStatus::APPROVED)
                 {
                     $testData['status'] = ContentStatus::CHANGES_REQUESTED;
                     $this->createSystemReview(
                         $test,
                         "Test '{$test->title_en}' was edited by its teacher after being approved, and has been automatically returned to 'changes requested'. Please re-review the updated content."
                     );
                 }
                 if ($test->status === ContentStatus::PENDING) {
                     $testData['status'] = ContentStatus::DRAFT;
                 }
                 $test->update($testData);
                 $syncData = collect($data['questions'])
                     ->mapWithKeys(fn ($q) => [$q['id'] => ['order' => $q['order']]])
                     ->all();
                 $test->questions()->sync($syncData);
                 $result = new TeacherTestResource($test);

                 logger('checking cascade condition', [
                     'testable_type' => $test->testable_type,
                     'is_lesson' => $test->testable_type === 'lesson',
                     'removedQuestionIds_empty' => empty($removedQuestionIds),
                 ]);
                 if ($test->testable_type === 'lesson' && !empty($removedQuestionIds)) {
                     $this->revalidateDependentTests(array_values($removedQuestionIds) , false);
                 }
             }

            return $result;
        });
     }
     public function revalidateDependentTests(array $removedQuestionIds , bool $calledFromPublish): void
     {logger('revalidateDependentTests', ['removedQuestionIds' => $removedQuestionIds]);

         $statuses = [
             ContentStatus::DRAFT,
             ContentStatus::CHANGES_REQUESTED,
             ContentStatus::PENDING,
             ContentStatus::IN_REVIEW,
             ContentStatus::APPROVED,
         ];
         if ($calledFromPublish) {
             $statuses[] = ContentStatus::PUBLISHED;
         }
         $dependentTests = Test::whereIn('testable_type', ['course', 'level'])
             ->whereIn('status', $statuses)
             ->whereHas('questions', fn ($q) => $q->whereIn('questions.id', $removedQuestionIds))
             ->get();

         logger('revalidateDependentTests', ['dependentTests' => $dependentTests]);
         foreach ($dependentTests as $dependentTest) {
             $currentQuestionIds = $dependentTest->questions()->pluck('questions.id');

             $stillEligible = $dependentTest->testable_type === 'course'
                 ? $this->checkQuestionsAvailableForCourse($dependentTest->testable_id, $currentQuestionIds, throwOnFailure: false)
                 : $this->adminTestService()->checkQuestionsAvailableForLevel($dependentTest->testable_id, $currentQuestionIds, throwOnFailure: false);

             logger('cascade eligibility result', [
                 'dependentTest_id' => $dependentTest->id,
                 'currentQuestionIds' => $currentQuestionIds->all(),
                 'stillEligible' => $stillEligible,
             ]);
             if ($stillEligible) {
                 continue;
             }

             $this->handleInvalidatedDependency($dependentTest, $removedQuestionIds);
         }
     }

     private function handleInvalidatedDependency(Test $dependentTest, array $removedQuestionIds): void
     {
         logger('handleInvalidatedDependency', ['removedQuestionIds' => $removedQuestionIds , 'dependentTest' => $dependentTest]);
         switch ($dependentTest->status) {

             case ContentStatus::DRAFT:
                 $this->notifyDependencyOwners(
                     $dependentTest,
                     'Test Content Alert',
                     "One or more questions used in test '{$dependentTest->title_en}' were removed from their source lesson test. Please review before submitting.",
                     $removedQuestionIds
                 );
                 break;
             case ContentStatus::CHANGES_REQUESTED:
                 $message = "An additional question : " .$this->formatQuestionsList($removedQuestionIds)
                     . ") used in test '{$dependentTest->title_en}' was removed from its source lesson test. Please take this into account while making changes.";

                 $this->attachSystemNoteToLatestReview($dependentTest, $message);

                 $this->notifyDependencyOwners($dependentTest, 'Test Content Alert', $message, $removedQuestionIds);
                 break;
             case ContentStatus::PENDING:
                 $dependentTest->update(['status' => ContentStatus::DRAFT]);

                 $this->notifyDependencyOwners(
                     $dependentTest,
                     'Test Returned to Draft',
                     "Test '{$dependentTest->title_en}' was automatically returned to draft because a question it depended on was removed from its source lesson test.",
                     $removedQuestionIds
                 );
                 break;

             case ContentStatus::APPROVED:
                 $dependentTest->update(['status' => ContentStatus::CHANGES_REQUESTED]);

                 $message = "Test '{$dependentTest->title_en}' (previously approved) was automatically returned to 'changes requested' because question (name: "
                     . $this->formatQuestionsList($removedQuestionIds)
                     . ") it depended on was removed from its source lesson test. Please review and update the test.";

                 $this->createSystemReview($dependentTest, $message);

                 $this->notifyDependencyOwners($dependentTest, 'Test Requires Changes', $message, $removedQuestionIds);
                 break;

             case ContentStatus::IN_REVIEW:
                 $message = "A question ". $this->formatQuestionsList($removedQuestionIds)
                     . ") used in test '{$dependentTest->title_en}', which is currently under review, was removed from its source lesson test. Please take this into account.";

                 $this->notifyInReviewParties($dependentTest, 'Test Under Review Affected', $message, $removedQuestionIds);
                 break;
             case ContentStatus::PUBLISHED:
                 logger('published course', ['removedQuestionIds' => $removedQuestionIds]);
                 $remainingQuestions = $dependentTest->questions()
                     ->whereNotIn('questions.id', $removedQuestionIds)
                     ->get()
                     ->values()
                     ->map(fn ($q, $i) => ['id' => $q->id, 'order' => $i + 1])
                     ->all();
                 logger('published course', ['remainingQuestionIds' => $remainingQuestions]);
                 $this->createTest([
                     'testable_type' => $dependentTest->testable_type,
                     'testable_id' => $dependentTest->testable_id,
                     'title_en' => $dependentTest->title_en,
                     'title_ar' => $dependentTest->title_ar,
                     'passing_score' => $dependentTest->passing_score,
                     'previous_test_id' => $dependentTest->id,
                     'questions' => $remainingQuestions,
                 ]);

                 $this->notifyDependencyOwners(
                     $dependentTest,
                     'New Test Version Created',
                     "A question was removed from your published test '{$dependentTest->title_en}' because it was deleted from its source lesson test. A new draft version has been created — please review and resubmit it.",
                     $removedQuestionIds
                 );
                 break;
         }
         }

     public function delete(Test $test)
     {
         return DB::transaction(function () use ($test) {
             $test = Test::where('id', $test->id)->lockForUpdate()->first();
             if ($test->status === ContentStatus::PUBLISHED || $test->status === ContentStatus::ARCHIVED || $test->status === ContentStatus::CLOSED)
             {
                 throw ValidationException::withMessages([
                     'error' => 'This test cannot be deleted.'
                 ]);
             }
             if ($test->status === ContentStatus::IN_REVIEW)
             {
                 throw ValidationException::withMessages([
                     'error' => 'This test is under review and  cannot be deleted.'
                 ]);
             }

             $test->delete();
             return ['message' => 'Test deleted successfully.'];
         });

     }
     public function isTestStillEligible(Test $test): bool
     {
         if (!in_array($test->testable_type, ['course', 'level'])) {
             return true;
         }

         $questionIds = $test->questions()->pluck('questions.id');

         return $test->testable_type === 'course'
             ? $this->checkQuestionsAvailableForCourse($test->testable_id, $questionIds, throwOnFailure: false)
             : $this->adminTestService()->checkQuestionsAvailableForLevel($test->testable_id, $questionIds, throwOnFailure: false);
     }


//     public function approve(Test $test, ContentReview $review)
//     {
//         return DB::transaction(function () use ($test, $review) {
//             $test = Test::where('id', $test->id)->lockForUpdate()->first();
//
//             if ($test->status !== ContentStatus::IN_REVIEW) {
//                 throw ValidationException::withMessages([
//                     'error' => 'This test is no longer awaiting your review — its status has changed. Please refresh.',
//                 ]);
//             }
//
//             if (!$this->isTestStillEligible($test)) {
//                 throw ValidationException::withMessages([
//                     'error' => 'This test cannot be approved — it contains a question that is no longer eligible. Please return it for changes.',
//                 ]);
//             }
//
//             $review->update(['status' => 'completed', 'completed_at' => now()]);
//
//             $test->update(['status' => ContentStatus::APPROVED]);
//         });
//     }

     public function createSystemReview(Test $test, string $message): void
     {
         $lastReview = $test->reviews()->latest('claimed_at')->first();

         $review = $test->reviews()->create([
             'reviewer_id' => $lastReview->reviewer_id,
             'status' => ReviewStatus::CHANGES_REQUESTED,
             'claimed_at' => now(),
             'completed_at' => now(),
         ]);

         $review->notes()->create([
             'reviewable_type' => $test->getMorphClass(),
             'reviewable_id' => $test->id,
             'admin_id' => null,
             'message' => $message,
             'is_system_generated' => true,
         ]);
     }
     private function attachSystemNoteToLatestReview(Test $test, string $message): void
     {
         $latestReview = $test->reviews()->latest('claimed_at')->first();

         $latestReview->notes()->create([
             'reviewable_type' => $test->getMorphClass(),
             'reviewable_id' => $test->id,
             'admin_id' => null,
             'message' => $message,
             'is_system_generated' => true,
         ]);
     }
     public function notifyDependencyOwners(Test $test, string $title, string $body, array $removedQuestionIds): void
     {
         $userIds = $test->testable_type === 'level'
             ? User::permission('manage_level_tests')->pluck('id')->all()
             : array_filter([$test->testable->teacher_id]);

         if (empty($userIds)) {
             return;
         }

         SendNotificationJob::dispatch(
             array_values($userIds),
             $title,
             $body,
             [
                 'test_id' => $test->id,
                 'removed_question_ids' => $removedQuestionIds,
             ],
             'content_dependency_change'
         );
     }
     private function notifyInReviewParties(Test $test, string $title, string $body, array $removedQuestionIds): void
     {
         $reviewerId = $test->reviews()
             ->where('status', ReviewStatus::IN_REVIEW)
             ->latest('claimed_at')
             ->value('reviewer_id');

         $ownerIds = $test->testable_type === 'level'
             ? User::permission('manage_level_tests')->pluck('id')->all()
             : array_filter([$test->testable->teacher_id]);

         $userIds = array_unique(array_filter(array_merge($ownerIds, [$reviewerId])));

         if (empty($userIds)) {
             return;
         }

         SendNotificationJob::dispatch(
             array_values($userIds),
             $title,
             $body,
             [
                 'test_id' => $test->id,
                 'removed_question_ids' => $removedQuestionIds,
             ],
             'content_dependency_change'
         );
     }

     private function formatQuestionsList(array $questionIds): string
     {
         $titles = Question::whereIn('id', $questionIds)
             ->pluck('title_question_en')
             ->all();

         return implode(', ', array_map(fn ($t) => "\"$t\"", $titles));
     }
 }
