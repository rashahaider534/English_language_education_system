<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;
use App\Mail\AdminCreatedMail;
use App\Mail\TeacherCreatedMail;
use Spatie\Permission\Models\Permission;

class RoleService
{
    public function storeAdmin(User $user, array $data)
    {
        if (!$user->hasRole('super-admin')) {
            throw ValidationException::withMessages([
                'error' => 'You are not allowed to add Admin.',
            ]);
        }
        if (User::where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => ['Email already exists']
            ]);
        }
        $plainPassword = $data['password'];
        $admin = User::create(
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        Mail::to($admin->email)->send(new TeacherCreatedMail($admin, $plainPassword));
        return $admin;
    }

    public function storeTeacher(User $user, array $data)
    {
        if (!$user->hasRole('super-admin')) {
            throw ValidationException::withMessages([
                'error' => 'You are not allowed to add Admin.',
            ]);
        }
        if (User::where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => ['Email already exists']
            ]);
        }
        $plainPassword = $data['password'];
        $teacher = User::create(
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
            ]
        );
        $teacher->assignRole(Role::findByName('teacher', 'api'));
        Mail::to($teacher->email)->send(new TeacherCreatedMail($teacher, $plainPassword));
        return $teacher;
    }

    public function delete(User $user)
    {
        if (!auth()->user->hasRole('super-admin')) {
            throw ValidationException::withMessages([
                'error' => 'You are not allowed to add Admin.',
            ]);
        }
        $user->delete();
        return ['account unactive successfully'];
    }

    public function permissions()
    {
        return Permission::where('guard_name', 'web')->get();
    }

    public function assignPermissions(User $admin, array $permissions)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            throw ValidationException::withMessages([
                'error' => 'Not allowed.'
            ]);
        }
        
        if (!$admin->hasRole('admin')) {
            throw ValidationException::withMessages([
                'error' => 'this not admin.'
            ]);
        }

        $admin->syncPermissions($permissions);

        return $admin;
    }
}
