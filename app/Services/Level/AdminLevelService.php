<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\Level;

class AdminLevelService
{
    public function create(array $data): Level
    {
        return DB::transaction(function () use ($data) {
            return Level::create([
                'name_en' => $data['name_en'],
                'name_ar' => $data['name_ar'],
                'order' => $data['order'],
                'minimum_score'=> $data['minimum_score'],
                'maximum_score'=> $data['maximum_score'],
                'is_active'=> $data['is_active'] ?? true,
                'price'=> $data['price'],
                'estimated_duration'=> $data['estimated_duration'],
                'created_by' => $data['created_by'],
            ]);
        });
    }
}
