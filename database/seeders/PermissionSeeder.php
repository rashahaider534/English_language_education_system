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
        Permission::create(['name' => 'create level', 'guard_name' => 'web']);
        Permission::create(['name' => 'update level', 'guard_name' => 'web']);
        Permission::create(['name' => 'archive level', 'guard_name' => 'web']);
        Permission::create(['name' => 'view levels', 'guard_name' => 'web']);
    }
}
