<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserLevel;

class UserLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User 2
        UserLevel::create([
            'user_id' => 2,
            'level_id' => 1,
            'status' => 'completed',
            'enrolled_at' => now()->subDays(60),
            'completed_at' => now()->subDays(30),
        ]);

        UserLevel::create([
            'user_id' => 2,
            'level_id' => 2,
            'status' => 'completed',
            'enrolled_at' => now()->subDays(30),
            'completed_at' => now(),
        ]);

        // User 9
        UserLevel::create([
            'user_id' => 9,
            'level_id' => 1,
            'status' => 'completed',
            'enrolled_at' => now()->subDays(60),
            'completed_at' => now()->subDays(30),
        ]);

        UserLevel::create([
            'user_id' => 9,
            'level_id' => 2,
            'status' => 'in_progress',
            'enrolled_at' => now()->subDays(30),
            'completed_at' => null,
        ]);

        // User 10
        UserLevel::create([
            'user_id' => 10,
            'level_id' => 1,
            'status' => 'completed',
            'enrolled_at' => now()->subDays(90),
            'completed_at' => now()->subDays(60),
        ]);

        UserLevel::create([
            'user_id' => 10,
            'level_id' => 2,
            'status' => 'completed',
            'enrolled_at' => now()->subDays(60),
            'completed_at' => now()->subDays(30),
        ]);

        UserLevel::create([
            'user_id' => 10,
            'level_id' => 3,
            'status' => 'in_progress',
            'enrolled_at' => now()->subDays(30),
            'completed_at' => null,
        ]);

        // User 11
        UserLevel::create([
            'user_id' => 11,
            'level_id' => 1,
            'status' => 'in_progress',
            'enrolled_at' => now()->subDays(30),
            'completed_at' => null,
        ]);

        // User 12
        UserLevel::create([
            'user_id' => 12,
            'level_id' => 1,
            'status' => 'completed',
            'enrolled_at' => now()->subDays(60),
            'completed_at' => now()->subDays(30),
        ]);

        UserLevel::create([
            'user_id' => 12,
            'level_id' => 2,
            'status' => 'in_progress',
            'enrolled_at' => now()->subDays(30),
            'completed_at' => null,
        ]);

        // User 13
        UserLevel::create([
            'user_id' => 13,
            'level_id' => 1,
            'status' => 'in_progress',
            'enrolled_at' => now()->subDays(30),
            'completed_at' => null,
        ]);

        // User 14
        UserLevel::create([
            'user_id' => 14,
            'level_id' => 1,
            'status' => 'completed',
            'enrolled_at' => now()->subDays(60),
            'completed_at' => now()->subDays(30),
        ]);

        UserLevel::create([
            'user_id' => 14,
            'level_id' => 2,
            'status' => 'in_progress',
            'enrolled_at' => now()->subDays(30),
            'completed_at' => null,
        ]);

        // User 15
        UserLevel::create([
            'user_id' => 15,
            'level_id' => 1,
            'status' => 'completed',
            'enrolled_at' => now()->subDays(120),
            'completed_at' => now()->subDays(90),
        ]);

        UserLevel::create([
            'user_id' => 15,
            'level_id' => 2,
            'status' => 'completed',
            'enrolled_at' => now()->subDays(90),
            'completed_at' => now()->subDays(60),
        ]);

        UserLevel::create([
            'user_id' => 15,
            'level_id' => 3,
            'status' => 'completed',
            'enrolled_at' => now()->subDays(60),
            'completed_at' => now()->subDays(30),
        ]);

        UserLevel::create([
            'user_id' => 15,
            'level_id' => 4,
            'status' => 'in_progress',
            'enrolled_at' => now()->subDays(30),
            'completed_at' => null,
        ]);
    }
}
