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
        Permission::create(['name' => 'create level']);
        Permission::create(['name' => 'update level']);
        Permission::create(['name' => 'archive level']);
        Permission::create(['name' => 'view levels']);
    }
}
