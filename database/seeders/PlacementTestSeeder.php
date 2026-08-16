<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Models\PlacementTest;
use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlacementTestSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $creator = User::findOrFail(3);

            // Published placement test — uses questions 145 through 149
            $this->createPlacementTest(
                creator: $creator,
                titleEn: 'General English Placement Test',
                titleAr: 'اختبار تحديد المستوى العام',
                status: ContentStatus::PUBLISHED,
                questionIds: range(145, 149),
            );

            // Archived placement test — uses questions 150 through 154
            $this->createPlacementTest(
                creator: $creator,
                titleEn: 'General English Placement Test (Old)',
                titleAr: 'اختبار تحديد المستوى العام (قديم)',
                status: ContentStatus::ARCHIVED,
                questionIds: range(150, 154),
            );
        });
    }

    private function createPlacementTest(
        User $creator,
        string $titleEn,
        string $titleAr,
        ContentStatus $status,
        array $questionIds,
    ): void {
        $placementTest = PlacementTest::create([
            'created_by' => $creator->id,
        ]);

        $test = Test::create([
            'testable_type' => 'placement_test',
            'testable_id' => $placementTest->id,
            'passing_score' => 0,
            'title_en' => $titleEn,
            'title_ar' => $titleAr,
            'status' => $status,
        ]);

        $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id');

        $syncData = [];
        foreach ($questionIds as $order => $id) {
            if (!$questions->has($id)) {
                $this->command->warn("Question id {$id} not found — skipping.");
                continue;
            }
            $syncData[$id] = ['order' => $order + 1];
        }

        $test->questions()->sync($syncData);
    }
}
