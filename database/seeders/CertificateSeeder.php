<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Certificate;
use App\Models\UserLevel;

class CertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userLevels = UserLevel::with(['user', 'level'])
            ->where('status', 'completed')
            ->get();

        foreach ($userLevels as $userLevel) {

            Certificate::create([
                'user_level_id' => $userLevel->id,
                'certificate_number' => 'CERT-' . strtoupper(uniqid()),
                'student_name' => $userLevel->user->first_name . ' ' . $userLevel->user->last_name,
                'level_name' => $userLevel->level->name_en,
                'issued_at' => $userLevel->completed_at ?? now(),
            ]);
        }
    }
}
