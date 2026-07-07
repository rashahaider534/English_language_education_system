<?php

namespace App\Services\Level;

use App\Http\Requests\Level\LevelRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Level;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Validation\ValidationException;

class AdminLevelService
{
    public function getLevels(?string $status = null)
    {
        $page = request('page', 1);
        return Cache::tags(['levels'])
            ->remember(
                "levels.$status.page.$page",
                3600,
                function () use ($status) {

                    $query = Level::with('creator')
                        ->orderBy('order', 'asc');

                    if ($status) {
                        $query->where('status', $status);
                    }

                    return $query->paginate(10);
                }
            );
    }

    public function getStatisticsLevel()
    {
        return Cache::tags(['levels'])
            ->remember(
                "levels.statistics",
                3600,
                function () {
                    return Level::selectRaw("
                    COUNT(*) as all_count,
                    SUM(status = 'pending') as pending,
                    SUM(status = 'closed') as closed,
                    SUM(status = 'published') as published,
                    SUM(status = 'archived') as archived")->first();
                }
            );
    }

    public function create(array $data): Level
    {
        return DB::transaction(function () use ($data) {
            $level = Level::create([
                'name_en' => $data['name_en'],
                'name_ar' => $data['name_ar'],
                'order' => $data['order'],
                'minimum_score' => $data['minimum_score'],
                'maximum_score' => $data['maximum_score'],
                'status' => $data['status'] ?? 'pending',
                'price' => $data['price'],
                'estimated_duration' => $data['estimated_duration'],
                'created_by' => $data['created_by'],
            ]);
            Cache::tags(['levels'])->flush();
            return $level;
        });
    }
    public function update(Level $level, array $data)
    {
         $user = auth()->user();
        if (
            !$user->hasRole('super-admin')
            && $level->created_by !== $user->id
        ){
            throw ValidationException::withMessages([
                'level' => 'You are not allowed to edit this level.',
            ]);
        }
        if (in_array($level->status, ['closed', 'archived'])) {
            throw ValidationException::withMessages([
                'level' => 'inactive levels cannot be modified.',
            ]);
        }
        $level->update($data);
        Cache::tags(['levels'])->flush();
        return $level;
    }
    public function archive(Level $level)
    {
        $user = auth()->user();
        if (
            !$user->hasRole('super-admin')
            && $level->created_by !== $user->id
        ) {
            throw ValidationException::withMessages([
                'level' => 'You are not allowed to archive this level.',
            ]);
        }

        if (in_array($level->status, ['closed', 'archived'])) {
            throw ValidationException::withMessages([
                'level' => 'Archived or closed levels cannot be archived again',
            ]);
        }

        $hasInProgressStudents = $level->userLevels()
            ->where('status', 'in_progress')
            ->exists();

        $level->update([
            'status' => $hasInProgressStudents
                ? 'closed'
                : 'archived',
        ]);
        Cache::tags(['levels'])->flush();
        return $level;
    }
}
