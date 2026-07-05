<?php

namespace App\Services;
 use App\Enums\ContentStatus;
 use App\Models\Question;
 use App\Models\Test;
 use Illuminate\Support\Facades\DB;
 use Illuminate\Validation\ValidationException;


 class TestService
 {
     public function publishTest(Test $test)
     {
         $test->update(['status' => ContentStatus::PUBLISHED]);

         foreach ($test->questions as $question) {
             if ($question->previous_question_id) {
                 $this->checkIfQuestionCanBeArchived($question->previous_question_id);
             }
         }
     }

     private function checkIfQuestionCanBeArchived($questionId)
     {
         $oldQuestion = Question::find($questionId);

         $isStillInPublishedTest = $oldQuestion->tests()
             ->where('status', ContentStatus::PUBLISHED)
             ->exists();

         if (!$isStillInPublishedTest) {
             $oldQuestion->delete();
         }
     }

     public function submitForReview(Test $test)
     {
         return DB::transaction(function () use ($test) {

             $test = Test::where('id', $test->id)->lockForUpdate()->first();

             if (!in_array($test->status, [ContentStatus::DRAFT, ContentStatus::CHANGES_REQUESTED])) {
                 throw ValidationException::withMessages([
                     'error' => 'This test cannot be submitted for review from its current status.',
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
                     'error' => 'This test uses outdated question version(s). Please update them to the latest version before submitting.',
                     'outdated_question_ids' => $outdatedQuestions->pluck('id'),
                 ]);
             }

             $test->update(['status' => ContentStatus::PENDING]);

             return ['status' => 'submitted', 'test' => $test];
         });
     }
 }
