<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\User;
use App\Models\Lesson;
class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::role('student')->get();
        $lessons = Lesson::all();

        foreach ($lessons as $lesson) {

            // إنشاء 5 تعليقات لكل درس
            foreach (range(1, 5) as $index) {

                Comment::create([
                    'user_id' => $users->random()->id,
                    'lesson_id' => $lesson->id,
                    'comment' => fake()->sentence(10),
                    'created_at' => now(),
                ]);
            }}}
}
