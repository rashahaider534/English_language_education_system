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


    public function getAllLevels()
    {
        $page = request('page', 1);
        return Cache::tags(['levels'])->remember("levels.page.$page", 3600, function () {
            return Level::with('creator')
                ->orderBy('order', 'asc')
                ->paginate(10);
        });
    }
    public function create(array $data): Level
    {
        return DB::transaction(function () use ($data) {
            return Level::create([
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
        });
    }
    public function update(Level $level, array $data)
    {
        if ($level->created_by !== auth()->id()) {
            throw ValidationException::withMessages([
                'level' => 'You are not allowed to edit this level.',
            ]);
        }
        if ($level->status == 'published') {
            throw ValidationException::withMessages([
                'level' => 'Published levels cannot be modified.',
            ]);
        }
        $level->update($data);
        return $level;
    }
    public function archive(Level $level)
    {
        if ($level->created_by !== auth()->id()) {
            throw ValidationException::withMessages([
                'level' => 'You are not allowed to archive this level.',
            ]);
        }
        if (in_array($level->status, ['closed', 'archived'])) {
            throw ValidationException::withMessages([
                'level' => 'Level is already inactive.',
            ]);
        }

        $level->update([
            'status' => $level->userLevels()->exists()
                ? 'closed'
                : 'archived',
        ]);

        return $level;
    }
    public function clearCache()
    {
        Cache::tags(['levels'])->flush();
    }
}
