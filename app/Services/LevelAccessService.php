<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAttempt;
use App\Models\Level;
use App\Models\UserLevel;
use Illuminate\Support\Collection;
use App\Models\LevelException;
use App\Enums\LevelExceptionStatus;

class LevelAccessService
{

    public function getRecommendedLevel(User $user): ?Level
    {
        $placementAttempt = UserAttempt::query()
            ->where('user_id', $user->id)
            ->whereHas('test', function ($query) {
                $query->where('testable_type', 'placement_test');
            })
            ->latest()
            ->first();

        if (! $placementAttempt) {
            return null;
        }

        return Level::query()
            ->where('minimum_score', '<=', $placementAttempt->score)
            ->where('maximum_score', '>=', $placementAttempt->score)
            ->first();
    }
    public  function getAllowedOrder(User $user)
    {

        $recommendedOrder = $this
            ->getRecommendedLevel($user)
            ?->order ?? 1;
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
    public  function getAvailableLevels(
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
    public function getLockedLevels(
        int $allowedOrder,
        Collection $userLevelIds,
        array $approvedExceptionLevelIds
    ): Collection {
        return Level::query()
            ->where('status', 'published')
            ->where('order', '>', $allowedOrder)
            ->whereNotIn('id', $userLevelIds)
            ->whereNotIn('id', $approvedExceptionLevelIds)
            ->orderBy('order')
            ->get();
    }
    public function getUserLevelContext(User $user): array
    {
        $userLevels = UserLevel::with('level')
            ->where('user_id', $user->id)
            ->whereHas('level', function ($q) {
                $q->where('status', '!=', 'archived');
            })
            ->get();
        return [
            'userLevels' => $userLevels,
            'userLevelIds' => $userLevels->pluck('level_id'),
            'approvedExceptionLevelIds' => LevelException::query()
                ->where('user_id', $user->id)
                ->where('status', LevelExceptionStatus::APPROVED)
                ->whereNotIn(
                    'requested_level_id',
                    $userLevels->pluck('level_id')
                )
                ->pluck('requested_level_id')
                ->toArray(),
        ];
    }
}
