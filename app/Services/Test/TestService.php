<?php

namespace App\Services\Test;
 use App\Enums\ContentStatus;
 use App\Http\Resources\Test\TeacherTestResource;
 use App\Jobs\SendNotificationJob;
 use App\Models\Course;
 use App\Models\Lesson;
 use App\Models\ContentReview;
 use App\Models\PlacementTest;
 use App\Models\Question;
 use App\Models\Test;
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
             case ContentStatus::CHANGES_REQUESTED:
                 SendNotificationJob::dispatch();
//          $this->notify(
//              $dependentTest->owner_id,
//              "A question used in '{$dependentTest->title_en}' was removed from its source lesson test. Please review before submitting."
//          );
                 break;
             case ContentStatus::PENDING:
                 $dependentTest->update([
                     'status' => ContentStatus::DRAFT,
                 ]);
                 //                 $this->notify(
//                     $dependentTest->owner_id,
//                     "Test '{$dependentTest->title_en}' was returned for draft because a question it depends on was removed from a lesson test."
//                 );
                 break;
             case ContentStatus::APPROVED:
                 $dependentTest->update([
                     'status' => ContentStatus::CHANGES_REQUESTED,
                      ]);
//                 $this->notify(
//                     $dependentTest->owner_id,
//                     "Test '{$dependentTest->title_en}' was returned for changes because a question it depends on was removed from a lesson test."
//                 );
                 break;

             case ContentStatus::IN_REVIEW:
                    //تذكري انه اذا كان قيد التدقيق بدي ابعت اشعار للاستاذ و للمدقق انه صار تغيير عالاختبار والمعالجة لح تتم عن طريق تابع العرض للادمن عن طريق العلامة يلي لح ابعتها
//                 $this->notify(
//                     $dependentTest->reviewer_id,
//                     "A question used in '{$dependentTest->title_en}' was removed from its source lesson test. Please review before approving."
//                 );
//                 $this->notify(
//                     $dependentTest->owner_id,
//                     "A question used in '{$dependentTest->title_en}' was removed from its source lesson test."
//                 );
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

//                 $this->notify(
//                     $dependentTest->owner_id,
//                     "A question was removed from '{$dependentTest->title_en}' (published) because its source was deleted from a lesson test. A new draft version was created — please review and resubmit it."
//                 );
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


 }
