<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserLevel;
class UserLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
              UserLevel::create([
            'user_id' => 2,
            'level_id' => 1,
            'status' => 'in_progress',
            'enrolled_at' => now(),
            'completed_at' => null,
        ]);

        UserLevel::create([
            'user_id' => 2,
            'level_id' => 2,
            'status' => 'completed',
            'enrolled_at' => now()->subDays(30),
            'completed_at' => now(),
        ]);
    }
}
