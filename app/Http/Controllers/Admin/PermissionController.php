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
        $admin = $this->roleservice->storeAdmin(auth()->user(), $request->validated());

        if ($request->filled('permissions')) {
            $this->roleservice->assignPermissions($admin, $request->input('permissions'));
        }

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'تم إنشاء حساب الأدمن بنجاح');
    }

    public function storeTeacher(StoreAccountRequset $request)
    {
        $this->roleservice->storeTeacher(auth()->user(), $request->validated());

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'تم إنشاء حساب الأستاذ بنجاح');
    }

    public function destroy(User $user)
    {
        $this->roleservice->delete($user);
        return redirect()
            ->back()
            ->with('success', 'تم تعطيل الحساب بنجاح');
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
            ->route('admin.permission.show', $user->id)
            ->with('success', 'تم حفظ صلاحيات الأدمن بنجاح');
    }

    public function revokePermissions(User $user, RevokePermissionRequset $request)
    {
        $this->roleservice->revokePermissions($user, $request->validated('permissions'));
        return redirect()
            ->route('admin.permission.show', $user->id)
            ->with('success', 'تم سحب الصلاحية بنجاح');
    }
}
