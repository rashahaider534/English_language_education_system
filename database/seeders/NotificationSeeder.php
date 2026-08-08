<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('notifications')->insert([
            // User 2
            [
                'id' => (string) Str::uuid(),
                'user_id' => 2,
                'title' => 'New Topic Published',
                'body' => 'A new topic has been published.',
                'data' => json_encode(['topic_id' => 1]),
                'type' => 'topic-published',
                'read' => false,
                'read_at' => null,
                'created_at' => now()->subMinutes(10),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'user_id' => 2,
                'title' => 'New Podcast Available',
                'body' => 'A new podcast is now available.',
                'data' => json_encode(['podcast_id' => 1]),
                'type' => 'podcast-created',
                'read' => false,
                'read_at' => null,
                'created_at' => now()->subMinutes(20),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'user_id' => 2,
                'title' => 'Course Opened',
                'body' => 'A new course has been opened for you.',
                'data' => json_encode(['course_id' => 1]),
                'type' => 'course-opened',
                'read' => true,
                'read_at' => now()->subMinutes(5),
                'created_at' => now()->subHours(1),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'user_id' => 2,
                'title' => 'Lesson Available',
                'body' => 'A new lesson is now available.',
                'data' => json_encode(['lesson_id' => 1]),
                'type' => 'lesson-opened',
                'read' => false,
                'read_at' => null,
                'created_at' => now()->subHours(2),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'user_id' => 2,
                'title' => 'Level Exception Approved',
                'body' => 'Your level exception request has been approved.',
                'data' => json_encode(['level_exception_id' => 1]),
                'type' => 'level-exception-approved',
                'read' => true,
                'read_at' => now()->subHours(1),
                'created_at' => now()->subHours(3),
                'updated_at' => now(),
            ],

            // User 6
            [
                'id' => (string) Str::uuid(),
                'user_id' => 6,
                'title' => 'New Topic Published',
                'body' => 'A new topic has been published.',
                'data' => json_encode(['topic_id' => 1]),
                'type' => 'topic-published',
                'read' => false,
                'read_at' => null,
                'created_at' => now()->subMinutes(15),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'user_id' => 6,
                'title' => 'New Podcast Available',
                'body' => 'A new podcast is now available.',
                'data' => json_encode(['podcast_id' => 1]),
                'type' => 'podcast-created',
                'read' => false,
                'read_at' => null,
                'created_at' => now()->subMinutes(30),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'user_id' => 6,
                'title' => 'Course Opened',
                'body' => 'A new course has been opened for you.',
                'data' => json_encode(['course_id' => 1]),
                'type' => 'course-opened',
                'read' => true,
                'read_at' => now()->subMinutes(10),
                'created_at' => now()->subHours(1),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'user_id' => 6,
                'title' => 'Lesson Available',
                'body' => 'A new lesson is now available.',
                'data' => json_encode(['lesson_id' => 1]),
                'type' => 'lesson-opened',
                'read' => false,
                'read_at' => null,
                'created_at' => now()->subHours(2),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'user_id' => 6,
                'title' => 'Level Exception Approved',
                'body' => 'Your level exception request has been approved.',
                'data' => json_encode(['level_exception_id' => 1]),
                'type' => 'level-exception-approved',
                'read' => false,
                'read_at' => null,
                'created_at' => now()->subHours(3),
                'updated_at' => now(),
            ],
        ]);
    }
}
