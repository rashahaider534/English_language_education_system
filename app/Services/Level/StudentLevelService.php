<?php

namespace App\Services\Level;

use App\Models\UserAttempt;
use App\Models\Level;
use App\Models\User;
use App\Models\UserLevel;

class StudentLevelService
{
    public function getAllowedOrder(User $user)
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
    public function getStudentLevels()
    {
        $user = auth()->user();
        $allowedOrder = $this->getAllowedOrder($user);
        $levels = Level::where('status', 'published')->orderBy('order', 'asc')->paginate(10);
        foreach ($levels as $level) {
            $level->state = $level->order <= $allowedOrder
                ? 'available'
                : 'locked';
        }   
        return $levels;
    }
}
