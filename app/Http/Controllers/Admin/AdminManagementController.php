<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use App\Services\Management\AdminManagementService;

class AdminManagementController extends Controller
{
    public function __construct(private AdminManagementService $adminmanagemntservice) {}

    public function index(Request $request): View
    {

        $search = $request->query('search');
        $status = $request->query('status');
        $admins = $this->adminmanagemntservice->getAdmins($search, $status);
        $counts = $this->adminmanagemntservice->getAdminCounts();
        $permissions = $this->adminmanagemntservice->getPermissions();
        return view(
            'admin.admins.index',
            [
                'admins' => $admins,
                'search' => $search,
                'status' => $status,
                'totalCount' => $counts['totalCount'],
                'activeCount' => $counts['activeCount'],
                'inactiveCount' => $counts['inactiveCount'],
                'permissions' => $permissions,
            ]
        );

    }

    public function toggleActive(User $admin): RedirectResponse
    {
        // بانتظار الربط بالباك-إند: عمود is_active غير مفعّل بأي منطق فعلي حاليًا.
        return redirect()->route('admin.admins.index')
            ->with('info', 'تفعيل/تعطيل حساب الأدمن ميزة قيد التطوير، وسيتم ربطها بالباك-إند لاحقًا.');
    }
}
