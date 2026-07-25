<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
class UserLessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('user_lessons')->insert([
            [
                'user_id' => 2,
                'lesson_id' => 4,
                'status' => 'completed',
            ],
            [
                'user_id' => 2,
                'lesson_id' => 5,
                'status' => 'in_progress',
            ],



        ]);
    }
}
