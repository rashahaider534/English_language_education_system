<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::findByName('admin');
        $superAdmin = Role::findByName('super-admin');

        $admin->givePermissionTo([
            'create level',
            'update level',
            'archive level',
            'view levels',
        ]);
        $superAdmin->givePermissionTo([
            'create level',
            'update level',
            'archive level',
            'view levels',
        ]);
    }
}
