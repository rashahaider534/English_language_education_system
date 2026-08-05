<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('id', 3)->first();
        $superAdmin = Role::findByName('super-admin', 'web');
        $studentRole=Role::findByName('student','api');
        $permissions = Permission::where('guard_name', 'web')
            ->whereIn('name', [
                'manage_levels',
                'manage_courses',
                'manage_level_tests',
                'manage_placement_tests',
                'manage_placement_questions',
                'publish_levels',
            ])
            ->get();

        $admin->givePermissionTo($permissions);
    }
}
