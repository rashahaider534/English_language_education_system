<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Level;
use App\Models\LevelException;
use App\Enums\LevelExceptionStatus;

class LevelExceptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::role('student')->get();

        $levels = Level::orderBy('id')->take(3)->get();

       // $admins = User::role(['admin', 'super-admin'])->get();

        if ($students->count() < 10) {
            return;
        }

        if ($levels->count() < 3) {
            return;
        }

        foreach ($students->take(10) as $index => $student) {

            $requestedLevel = $levels[$index % 3];

            $recommendedLevel = $levels[($index + 1) % 3];

            $status = match ($index) {
                0, 1, 2, 3 => LevelExceptionStatus::PENDING,
                4, 5, 6 => LevelExceptionStatus::APPROVED,
                default => LevelExceptionStatus::REJECTED,
            };

            $exception = [
                'user_id' => $student->id,
                'requested_level_id' => $requestedLevel->id,
                'recommended_level_id' => $recommendedLevel->id,
                'status' => $status->value,
                'reason' => 'The student believes that the requested level is more suitable for their current English proficiency.',
                'review_note' => null,
                'executed_by' => null,
                'executed_at' => null,
            ];

            if ($status === LevelExceptionStatus::APPROVED) {


                $exception['review_note'] = 'The request was reviewed and approved based on the student profile and assessment results.';
                $exception['executed_by'] = 1;
                $exception['executed_at'] = now()->subDays(rand(1, 10));
            }

            if ($status === LevelExceptionStatus::REJECTED) {
              //  $admin = $admins->random();

                $exception['review_note'] = 'The requested level is not suitable based on the student assessment results.';
                $exception['executed_by'] = 1;
                $exception['executed_at'] = now()->subDays(rand(1, 10));
            }

            LevelException::create($exception);
        }
    }
}
