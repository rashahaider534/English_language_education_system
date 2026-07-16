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
            $creator = User::findOrFail(2);

            // Published / active placement test — uses questions 1 through 9
            $this->createPlacementTest(
                creator: $creator,
                titleEn: 'General English Placement Test',
                titleAr: 'اختبار تحديد المستوى العام',
                isActive: true,
                status: ContentStatus::PUBLISHED,
                questionIds: range(1, 9),
            );

            // Archived / inactive placement test — uses questions 10 through 18
            $this->createPlacementTest(
                creator: $creator,
                titleEn: 'General English Placement Test (Old)',
                titleAr: 'اختبار تحديد المستوى العام (قديم)',
                isActive: false,
                status: ContentStatus::ARCHIVED,
                questionIds: range(10, 18),
            );
        });
    }

    private function createPlacementTest(
        User $creator,
        string $titleEn,
        string $titleAr,
        bool $isActive,
        ContentStatus $status,
        array $questionIds,
    ): void {
        $placementTest = PlacementTest::create([
            'title_en' => $titleEn,
            'title_ar' => $titleAr,
            'is_active' => $isActive,
            'created_by' => $creator->id,
        ]);

        $test = Test::create([
            'testable_type' => 'placement_test', // adjust to match your Relation::morphMap() alias
            'testable_id' => $placementTest->id,
            'passing_score' => 60,
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
