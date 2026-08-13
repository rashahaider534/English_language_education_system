<?php

namespace App\Services\Management;

use App\Models\Level;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StudentManagementService
{
    public function getStudents(?string $search = null, ?int $levelId = null): LengthAwarePaginator
    {
        $students = User::role('student')
            ->with([
                'studentProfile',
                'userLevels' => function ($query) {
                    $query->latest('enrolled_at')
                        ->with('level');
                }
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($levelId, function ($query) use ($levelId) {
                $query->whereHas('userLevels', function ($q) use ($levelId) {
                    $q->where('level_id', $levelId);
                });
            })
            ->orderBy('first_name')
            ->paginate(10)
            ->withQueryString();

        foreach ($students as $student) {
            $student->current_level = optional(
                $student->userLevels->first()
            )->level;
        }

        return $students;
    }

    public function getLevels()
    {
        return Level::withCount('users')
            ->orderBy('order')
            ->get();
    }

    public function getTotalStudents(): int
    {
        return User::role('student')->count();
    }

    public function getLevelDistribution($levels)
    {
        return $levels->map(function ($level) {
            return [
                'label' => $level->name_ar ?? $level->name_en,
                'count' => $level->users_count,
            ];
        });
    }

    public function getLevelDistributionMax($levelDistribution): int
    {
        return max(
            1,
            $levelDistribution->max('count') ?? 1
        );
    }

    public function getIndexData(
        ?string $search = null,
        ?int $levelId = null
    ): array {
        $students = $this->getStudents($search, $levelId);

        $levels = $this->getLevels();

        $totalCount = $this->getTotalStudents();

        $levelDistribution = $this->getLevelDistribution($levels);

        $levelDistributionMax = $this->getLevelDistributionMax(
            $levelDistribution
        );

        return [
            'students' => $students,
            'levels' => $levels,
            'totalCount' => $totalCount,
            'levelDistribution' => $levelDistribution,
            'levelDistributionMax' => $levelDistributionMax,
        ];
    }
}
