<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class UserCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('user_courses')->insert([
            [
                'user_id' => 2,
                'course_id' => 1,
                'status' => 'completed',
                'started_at' => now(),
                'completed_at' => null,
            ],
            [
                'user_id' => 2,
                'course_id' => 2,
                'status' => 'in_progress',
                'started_at' => now()->subDays(7),
                'completed_at' => now(),
            ],
               [
                'user_id' => 2,
                'course_id' => 4,
                'status' => 'in_progress',
                'started_at' => now(),
                'completed_at' => null,
            ],
            [
                'user_id' => 2,
                'course_id' => 6,
                'status' => 'completed',
                'started_at' => now()->subDays(7),
                'completed_at' => now(),
            ],
               [
                'user_id' => 2,
                'course_id' => 7,
                'status' => 'in_progress',
                'started_at' => now(),
                'completed_at' => null,
            ],
         
        ]);
    }
}
