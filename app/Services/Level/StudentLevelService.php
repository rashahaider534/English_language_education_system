<?php

namespace App\Services\Level;

use App\Models\UserAttempt;
use App\Models\Level;
use App\Models\LevelException;
use App\Models\User;
use App\Models\UserLevel;
use App\Services\LevelAccessService;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class StudentLevelService
{
    public function __construct(
        private LevelAccessService $levelAccessService
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

}
