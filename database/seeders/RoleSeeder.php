<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate([ 'name' => 'student', 'guard_name' => 'api', ]);
        Role::firstOrCreate([ 'name' => 'teacher', 'guard_name' => 'api', ]);
        Role::firstOrCreate([ 'name' => 'admin', 'guard_name' => 'web', ]);
        Role::firstOrCreate([ 'name' => 'super-admin', 'guard_name' => 'web', ]); }

}
