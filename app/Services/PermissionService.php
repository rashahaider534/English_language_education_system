<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;
use App\Mail\AdminCreatedMail;
use App\Mail\AccountCreatedMail;
use App\Models\TeacherProfile;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function getAdmin(User $user)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            throw ValidationException::withMessages([
                'error' => 'You are not allowed to view Admin.',
            ]);
        }

        if (!$user->hasRole('admin')) {
            throw ValidationException::withMessages([
                'error' => 'This user is not an admin.',
            ]);
        }


        return  $user->load('permissions');
    }

    public function storeAdmin(User $user, array $data)
    {
        if (!$user->hasRole('super-admin')) {
            throw ValidationException::withMessages([
                'error' => 'You are not allowed to add Admin.',
            ]);
        }
        if (User::where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => ['هذا البريد الإلكتروني مستخدم مسبقًا']
            ]);
        }
        $plainPassword = $data['password'];
        $admin = User::create(
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'email_verified_at' => null,
            ]
        );
        $admin->assignRole(Role::findByName('admin', 'web'));

        Mail::to($admin->email)->send(new AccountCreatedMail($admin, $plainPassword));
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
                'email' => ['هذا البريد الإلكتروني مستخدم مسبقًا']
            ]);
        }
        $plainPassword = $data['password'];
        $teacher = User::create(
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'email_verified_at' => null,
            ]
        );
        TeacherProfile::create([
        'user_id'=>$teacher->id,
        'bio'=>null,
        ]
        );
        $teacher->assignRole(Role::findByName('teacher', 'api'));
        Mail::to($teacher->email)->send(new AccountCreatedMail($teacher, $plainPassword));
        return $teacher;
    }

    public function delete(User $user)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            throw ValidationException::withMessages([
                'error' => 'You are not allowed to add Admin.',
            ]);
        }

        $user->delete();
        return ['account unactive successfully'];
    }

    public function permissions()
    {
        if (!auth()->user()->hasRole('super-admin')) {
            throw ValidationException::withMessages([
                'error' => 'Not allowed.'
            ]);
        }
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

        $permissionModels = Permission::where('guard_name', 'web')
            ->whereIn('name', $permissions)
            ->get();

        $admin->syncPermissions($permissionModels);

        return $admin;
    }

    public function revokePermissions(User $admin, array $permissions)
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

        $permissionModels = Permission::where('guard_name', 'web')
            ->whereIn('name', $permissions)
            ->get();

        $admin->revokePermissionTo($permissionModels);

        return $admin;
    }
}
