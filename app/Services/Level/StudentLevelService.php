<?php

namespace App\Services\Level;

use App\Enums\AttemptStatus;
use App\Models\UserAttempt;
use App\Models\Level;
use App\Models\LevelException;
use App\Models\User;
use App\Models\UserLevel;
use App\Services\AttemptService;
use App\Services\LevelAccessService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class StudentLevelService
{
    public function __construct(
        private LevelAccessService $levelAccessService,
        private AttemptService $attemptService,
    ) {}



    public function getStudentLevels(User $user)
    {
        $allowedOrder = $this->levelAccessService->getAllowedOrder($user);
        $context = $this->levelAccessService->getUserLevelContext($user);

        $currentLevel =  $context['userLevels']
            ->firstWhere('status', 'in_progress')
            ?->level;

        $completedLevels = $context['userLevels']
            ->where('status', 'completed')
            ->pluck('level')
            ->values();

        $availableLevels = $this->levelAccessService->getAvailableLevels(
            $allowedOrder,
            $context['userLevelIds'],
            $context['approvedExceptionLevelIds']
        );

        $lockedLevels = $this->levelAccessService->getLockedLevels(
            $allowedOrder,
            $context['userLevelIds'],
            $context['approvedExceptionLevelIds']
        );


        return [
            'current_level' => $currentLevel,
            'completed_levels' => $completedLevels,
            'available_levels' => $availableLevels,
            'locked_levels' => $lockedLevels,
        ];
    }

    private const RETAKE_COOLDOWN_DAYS = 30;

    public function getStatus(User $user): array
    {
        $lastCompletedAttempt = $this->getLastCompletedAttempt($user);

        if (!$lastCompletedAttempt) {
            return [
                'action' => 'take_placement_test',
                'can_retake_placement' => false,
            ];
        }
        $canRetake = $this->attemptService->isEligibleForPlacementRetake($user);
        return [
            'action' => 'show_levels',
            'can_retake_placement' => $canRetake,
            'retake_available_at' => $this->getRetakeAvailableAt($lastCompletedAttempt, $canRetake),
        ];
    }


    private function getRetakeAvailableAt(UserAttempt $lastCompletedAttempt, bool $canRetake): ?Carbon
    {
        if ($canRetake) {
            return null;
        }

        return $lastCompletedAttempt->completed_at->addDays(self::RETAKE_COOLDOWN_DAYS);
    }

    private function getLastCompletedAttempt(User $user): ?UserAttempt
    {
        return UserAttempt::query()
            ->where('user_id', $user->id)
            ->where('status', AttemptStatus::COMPLETED)
            ->whereHas('test', fn($q) => $q->where('testable_type', 'placement_test'))
            ->latest('completed_at')
            ->first();
    }


}
