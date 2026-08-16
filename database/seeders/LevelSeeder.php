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
                'maximum_score' => 16,
                'price' => 50,
                'estimated_duration' => 30,
                'created_by' => 1,
                'status' => 'pending',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 2,
                'name_en' => 'A2',
                'name_ar' => 'مبتدئ متقدم',
                'order' => 2,
                'minimum_score' => 17,
                'maximum_score' => 33,
                'price' => 60,
                'estimated_duration' => 35,
                'created_by' => 1,
                'status' => 'pending',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 3,
                'name_en' => 'B1',
                'name_ar' => 'متوسط',
                'order' => 3,
                'minimum_score' => 34,
                'maximum_score' => 50,
                'price' => 70,
                'estimated_duration' => 40,
                'created_by' => 1,
                'status' => 'pending',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 4,
                'name_en' => 'B2',
                'name_ar' => 'متوسط متقدم',
                'order' => 4,
                'minimum_score' => 51,
                'maximum_score' => 67,
                'price' => 80,
                'estimated_duration' => 45,
                'created_by' => 1,
                'status' => 'pending',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 5,
                'name_en' => 'C1',
                'name_ar' => 'متقدم',
                'order' => 5,
                'minimum_score' => 68,
                'maximum_score' => 83,
                'price' => 90,
                'estimated_duration' => 50,
                'created_by' => 1,
                'status' => 'pending',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 6,
                'name_en' => 'C2',
                'name_ar' => 'متقدم جدًا',
                'order' => 6,
                'minimum_score' => 84,
                'maximum_score' => 100,
                'price' => 100,
                'estimated_duration' => 55,
                'created_by' => 1,
                'status' => 'pending',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
