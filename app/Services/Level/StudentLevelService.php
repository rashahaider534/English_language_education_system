<?php

namespace App\Services\Level;

use App\Models\UserAttempt;
use App\Models\Level;
use App\Models\LevelException;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class StudentLevelService
{
    private function getAllowedOrder(User $user)
    {
        $placementAttempt = UserAttempt::query()
            ->where('user_id', $user->id)
            ->whereHas('test', function ($query) {
                $query->where('testable_type', 'placement_test');
            })
            ->latest()
            ->first();
        $recommendedOrder = 1;
        if ($placementAttempt) {
            $score = $placementAttempt->score;
            $recommendedLevel = Level::where('minimum_score', '<=', $score)
                ->where('maximum_score', '>=', $score)
                ->first();
            if ($recommendedLevel) {
                $recommendedOrder = $recommendedLevel->order;
            }
        }
        $lastCompletedLevelOrder = UserLevel::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('level')
            ->get()
            ->max(fn($userLevel) => $userLevel->level->order) ?? 0;

        $allowedOrder = max(
            $recommendedOrder,
            $lastCompletedLevelOrder + 1
        );

        return $allowedOrder;
    }
    private function getAvailableLevels(
        int $allowedOrder,
        Collection $userLevelIds,
        array $approvedExceptionLevelIds
    ): Collection {
        return Level::query()
            ->where('status', 'published')
            ->where(function ($query) use ($allowedOrder, $approvedExceptionLevelIds) {
                $query->where('order', '<=', $allowedOrder)
                    ->orWhereIn('id', $approvedExceptionLevelIds);
            })
            ->whereNotIn('id', $userLevelIds)
            ->orderBy('order')
            ->get();
    }
    public function getStudentLevels(User $user)
    {
        $allowedOrder = $this->getAllowedOrder($user);
        $userLevels = UserLevel::with('level')
            ->where('user_id', $user->id)
            ->whereHas('level', function ($q) {
                $q->where('status', '!=', 'archived');
            })
            ->get();

        $currentLevel = $userLevels
            ->firstWhere('status', 'in_progress')
            ?->level;

        $completedLevels = $userLevels
            ->where('status', 'completed')
            ->pluck('level')
            ->values();


        $userLevelIds = $userLevels->pluck('level_id');

        $approvedExceptionLevelIds = LevelException::query()
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereNotIn('requested_level_id', $userLevelIds)
            ->pluck('requested_level_id')
            ->toArray();

        $availableLevels = $this->getAvailableLevels(
            $allowedOrder,
            $userLevelIds,
            $approvedExceptionLevelIds
        );

        $lockedLevels = Level::query()
            ->where('status', 'published')
            ->where('order', '>', $allowedOrder)
            ->whereNotIn('id', $userLevelIds)
            ->whereNotIn('id', $approvedExceptionLevelIds)
            ->orderBy('order')
            ->get();

        return [
            'current_level' => $currentLevel,
            'completed_levels' => $completedLevels,
            'available_levels' => $availableLevels,
            'locked_levels' => $lockedLevels,
        ];
    }

    public function getPurchasableLevels(User $user)
    {
        $userLevels = UserLevel::where('user_id', $user->id)->get();
        $allowedOrder = $this->getAllowedOrder($user);
        $userLevelIds = $userLevels->pluck('level_id');
        $approvedExceptionLevelIds = LevelException::query()
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereNotIn('requested_level_id', $userLevelIds)
            ->pluck('requested_level_id')
            ->toArray();

        return $this->getAvailableLevels(
            $allowedOrder,
            $userLevelIds,
            $approvedExceptionLevelIds
        );
    }
}
