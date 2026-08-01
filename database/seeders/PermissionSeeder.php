<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'manage_levels', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage_courses', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage_level_tests', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage_placement_tests', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage_placement_questions', 'guard_name' => 'web']);
        Permission::create(['name' => 'publish_levels', 'guard_name' => 'web']);
        Permission::create(['name' => 'create comments', 'guard_name' => 'api']);
    }
}
