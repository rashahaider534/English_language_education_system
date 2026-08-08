<?php

namespace Database\Seeders;

use App\Enums\ReviewStatus;
use App\Models\ContentReview;
use App\Models\Lesson;
use App\Models\Test;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContentReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testReview = ContentReview::create([
            'reviewable_type' => 'test',
            'reviewable_id' => 2,
            'reviewer_id' => 3,
            'status' => ReviewStatus::CHANGES_REQUESTED,
            'claimed_at' => now()->subDays(2),
            'completed_at' => now()->subDay(), ]);
        $testReview->notes()->create([
            'reviewable_type' => 'test',
            'reviewable_id' => 2,
            'admin_id' => 3,
            'message' => 'Please review the questions and correct the issues found in this test.',
            'is_system_generated' => false, ]);

        $lessonReview = ContentReview::create([ 'reviewable_type' => 'lesson',
            'reviewable_id' => 1,
            'reviewer_id' => 3,
            'status' => ReviewStatus::CHANGES_REQUESTED,
            'claimed_at' => now()->subDays(3),
            'completed_at' => now()->subDays(2), ]);
        $lessonReview->notes()->create([
            'reviewable_type' => 'lesson',
            'reviewable_id' => 1,
            'admin_id' => 3,
            'message' => 'Please update the lesson content and review the uploaded video.',
            'is_system_generated' => false, ]); }



}
