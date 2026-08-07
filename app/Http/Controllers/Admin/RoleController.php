<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignPermissionRequest;
use App\Models\User;
use App\Services\RoleService;
use App\Http\Requests\StoreAdmin_TeacherRequset;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(
        private RoleService $roleservice
    ) {}

    public function create()
    {
        return view('admin.admins.create');
    }
    
    public function createteacher()
    {
        return view('admin.teacher.create');
    }

    public function storeAdmin(StoreAdmin_TeacherRequset $request)
    {
        $admin = $this->roleservice->storeAdmin(auth()->user(), $request->validated());
        return redirect()
            ->route('admins.permissions', $admin->id)
            ->with('success', 'Admin created successfully');
    }

    public function storeTeacher(StoreAdmin_TeacherRequset $request)
    {
        $this->roleservice->storeTeacher(auth()->user(), $request->validated());

        return redirect()
            ->back()
            ->with('success', 'Teacher created successfully');
    }

    public function destroy(User $user)
    {
        $this->roleservice->delete($user);
        return redirect()
            ->back()
            ->with('success', 'User deleted successfully');
    }

    public function choosePermissions(User $user)
    {
        $permissions = $this->roleservice->permissions();

        return view('admin.permissions.assign', [
            'user' => $user,
            'permissions' => $permissions,
        ]);
    }

    public function assignPermissions(User $user, AssignPermissionRequest $request)
    {
        $this->roleservice->assignPermissions($user, $request->validated());
        return redirect()
            ->route('admins.index')
            ->with('success', 'Permissions assigned successfully');
    }
}
