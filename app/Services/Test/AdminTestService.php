<?php

namespace App\Services\Test;
use App\Enums\ContentStatus;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\PlacementTest;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminTestService
{
    public TestService $testService;
    public function storePlacementTest(array $data): Test
    {
        return DB::transaction(function () use ($data) {
            $placementTest = PlacementTest::create(['created_by' => auth()->id()]);

            $data['testable_type'] = 'placement_test';
            $data['testable_id'] = $placementTest->id;

            return $this->testService->createTest($data);
        });
    }


    public function update(Test $test, array $data)
    {
        return DB::transaction(function () use ($data, $test) {
            $test = Test::where('id', $test->id)->lockForUpdate()->first();

            if ($test->status === ContentStatus::ARCHIVED) {
                throw ValidationException::withMessages([
                    'error' => 'This test is archived and cannot be edited.'
                ]);
            }

            if ($test->testable_type === 'level') {
                $this->checkQuestionsAvailableForLevel($test->testable_id, collect($data['questions'])->pluck('id'));
            } elseif ($test->testable_type === 'placement_test') {
                $this->checkQuestionsAreValidPlacementQuestions(collect($data['questions'])->pluck('id'));
            }

            if ($test->status === ContentStatus::PUBLISHED) {
                $data['testable_id'] = $test->testable_id;
                $data['testable_type'] = $test->testable_type;
                $data['previous_test_id'] = $test->id;
                $newTest = $this->testService->createTest($data);
                return $newTest;
            }

            $testData = Arr::except($data, ['questions']);
            if ($test->status === ContentStatus::APPROVED) {
                $testData['status'] = ContentStatus::CHANGES_REQUESTED;
            }

            $test->update($testData);

            $syncData = collect($data['questions'])
                ->mapWithKeys(fn ($q) => [$q['id'] => ['order' => $q['order']]])
                ->all();
            $test->questions()->sync($syncData);

            return $test;
        });
    }
    public function checkQuestionsAreValidPlacementQuestions($questionIds): bool
    {
        $validCount = Question::whereIn('id', $questionIds)
            ->where('is_placement_question', true)
            ->count();

        $isValid = $validCount === (is_countable($questionIds) ? count($questionIds) : $questionIds->count());

        if (!$isValid) {
            throw ValidationException::withMessages([
                'questions' => 'One or more questions are not valid placement-test questions.',
            ]);
        }

        return $isValid;
    }

    public function checkQuestionsAvailableForLevel($testable_id, $questions_ids, bool $throwOnFailure = true): bool
    {
        $course_ids = \App\Models\Course::where('level_id', $testable_id)->pluck('id');
        $remaining = collect($questions_ids)->all();

        foreach ($course_ids as $courseId) {
            if (empty($remaining)) {
                break;
            }

            $lessonIds = Lesson::where('course_id', $courseId)->pluck('id');
            $eligible = $this->testService->eligibleQuestionIdsFromLessonTests($remaining, $lessonIds);
            $remaining = array_diff($remaining, $eligible);
        }

        $isValid = empty($remaining);
        if (!$isValid && $throwOnFailure) {
            throw ValidationException::withMessages([
                'questions' => 'One or more questions are not eligible for this level test.',
            ]);
        }

        return $isValid;
    }

    public function generateLevelTest(array $data): Test
    {
        return DB::transaction(function () use ($data) {
            $levelId = $data['testable_id'];
            $difficultyCounts = [
                'EASY' => $data['difficulties']['easy'],
                'MEDIUM' => $data['difficulties']['medium'],
                'HARD' => $data['difficulties']['hard'],
            ];

            $courseIds = Course::where('level_id', $levelId)->pluck('id')->all();

            if (empty($courseIds)) {
                throw ValidationException::withMessages(['error' => 'This level has no courses to generate a test from.']);
            }

            $selectedQuestionIds = [];

            foreach ($difficultyCounts as $difficulty => $neededCount) {
                if ($neededCount <= 0) {
                    continue;
                }
                $selectedQuestionIds = array_merge(
                    $selectedQuestionIds,
                    $this->selectQuestionsFairlyAcrossCourses($courseIds, $difficulty, $neededCount)
                );
            }

            $questions = collect($selectedQuestionIds)
                ->unique()
                ->values()
                ->map(fn ($id, $i) => ['id' => $id, 'order' => $i + 1])
                ->all();

            return $this->testService->createTest([
                'testable_type' => 'level',
                'testable_id' => $levelId,
                'title_en' =>$data['$titleEn'],
                'title_ar' => $data['$titleAr'],
                'passing_score' => $data['$passingScore'],
                'questions' => $questions,
            ]);
        });
    }

    private function selectQuestionsFairlyAcrossCourses(array $courseIds, string $difficulty, int $neededCount): array
    {
        $courseIds = collect($courseIds)->shuffle()->values();
        $courseCount = $courseIds->count();
        $base = intdiv($neededCount, $courseCount);
        $remainder = $neededCount % $courseCount;

        $selected = collect();
        $shortfallCourses = [];
        foreach ($courseIds as $i => $courseId) {
            $target = $base + ($i < $remainder ? 1 : 0);
            if ($target === 0) {
                continue;
            }

            $lessonIds = Lesson::where('course_id', $courseId)->pluck('id');

            $available = Question::where('difficulty', $difficulty)
                ->whereHas('tests', function ($q) use ($lessonIds) {
                    $q->where('testable_type', 'lesson')
                        ->whereIn('testable_id', $lessonIds)
                        ->whereIn('status', [ContentStatus::APPROVED, ContentStatus::PUBLISHED]);
                })
                ->inRandomOrder()
                ->limit($target)
                ->pluck('id');

            $selected = $selected->merge($available);

            if ($available->count() < $target) {
                $shortfallCourses[] = ['course_id' => $courseId, 'missing' => $target - $available->count()];
            }
        }

        foreach ($shortfallCourses as $shortfall) {
            $stillNeeded = $shortfall['missing'];

            foreach ($courseIds as $courseId) {
                if ($stillNeeded <= 0) break;

                $lessonIds = Lesson::where('course_id', $courseId)->pluck('id');

                $extra = Question::where('difficulty', $difficulty)
                    ->whereHas('tests', function ($q) use ($lessonIds) {
                        $q->where('testable_type', 'lesson')
                            ->whereIn('testable_id', $lessonIds)
                            ->whereIn('status', [ContentStatus::APPROVED, ContentStatus::PUBLISHED]);
                    })
                    ->whereNotIn('id', $selected)
                    ->inRandomOrder()
                    ->limit($stillNeeded)
                    ->pluck('id');

                $selected = $selected->merge($extra);
                $stillNeeded -= $extra->count();
            }

            if ($stillNeeded > 0) {
                throw ValidationException::withMessages([
                    'error' => "Not enough '{$shortfall}' difficulty questions available across this level's courses to generate the requested test.",
                ]);
            }
        }

        return $selected->all();
    }
}
