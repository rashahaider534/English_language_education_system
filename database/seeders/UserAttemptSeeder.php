<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserAttempt;
class UserAttemptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         UserAttempt::create([
            'test_id' => 4,
            'user_id' => 2,
            'score' => 51,
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
}
