<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\AssignPermissionRequest;
use App\Http\Requests\Permission\RevokePermissionRequset;
use App\Models\User;
use App\Services\PermissionService;
use App\Http\Requests\Permission\StoreAccountRequset;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(
        private PermissionService $roleservice
    ) {}

    public function index()
    {
        $data = $this->roleservice->index(auth()->user());

        return view('admin.permission.index', [
            'admins' => $data['admins'],
            'teachers' => $data['teachers'],
        ]);
    }

    public function getAdmin(User $user)
    {
        $admin = $this->roleservice->getAdmin($user);
        return view('admin.permissions.show',compact('admin'));
    }

    public function createadmin()
    {
        return view('admin.admin.create');
    }

    public function createteacher()
    {
        return view('admin.teachers.create');
    }

    public function storeAdmin(StoreAccountRequset $request)
    {
        $this->roleservice->storeAdmin(auth()->user(), $request->validated());
        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Admin created successfully');
    }

    public function storeTeacher(StoreAccountRequset $request)
    {
        $this->roleservice->storeTeacher(auth()->user(), $request->validated());

        return redirect()
            ->route('admin.teachers.index')
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
        return view('admin.permissions.assign', compact('permissions', 'user'));
    }

    public function assignPermissions(User $user, AssignPermissionRequest $request)
    {
        $this->roleservice->assignPermissions($user, $request->validated('permissions'));
        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permissions assigned successfully');
    }

    public function revokePermissions(User $user, RevokePermissionRequset $request)
    {
        $this->roleservice->revokePermissions($user, $request->validated('permissions'));
        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permissions revoked successfully');
    }
}
