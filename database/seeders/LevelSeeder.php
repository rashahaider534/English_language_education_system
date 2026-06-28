<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Level;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        Level::insert([
            [
                'id' => 1,
                'name_en' => 'A1',
                'name_ar' => 'مبتدئ',
                'order' => 1,
                'minimum_score' => 0,
                'maximum_score' => 24,
                'price' => 50,
                'estimated_duration' => 30,
                'created_by' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name_en' => 'A2',
                'name_ar' => 'مبتدئ متقدم',
                'order' => 2,
                'minimum_score' => 25,
                'maximum_score' => 49,
                'price' => 60,
                'estimated_duration' => 35,
                'created_by' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name_en' => 'B1',
                'name_ar' => 'متوسط',
                'order' => 3,
                'minimum_score' => 50,
                'maximum_score' => 74,
                'price' => 70,
                'estimated_duration' => 40,
                'created_by' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name_en' => 'B2',
                'name_ar' => 'متوسط متقدم',
                'order' => 4,
                'minimum_score' => 75,
                'maximum_score' => 100,
                'price' => 80,
                'estimated_duration' => 45,
                'created_by' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
