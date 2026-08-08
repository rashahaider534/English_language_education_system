<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class AdminManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $admins = User::role('admin', 'web')
            ->with('permissions')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('is_active', $status === 'active');
            })
            ->orderBy('first_name')
            ->paginate(10)
            ->withQueryString();

        $totalCount = User::role('admin', 'web')->count();
        $activeCount = User::role('admin', 'web')->where('is_active', true)->count();
        $inactiveCount = $totalCount - $activeCount;

        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();

        return view('admin.admins.index', compact('admins', 'search', 'status', 'totalCount', 'activeCount', 'inactiveCount', 'permissions'));
    }

    public function create(): View
    {
        return view('admin.admins.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // بانتظار الربط بالباك-إند: ما في منطق حقيقي لإنشاء حساب أدمن لهلق (نموذج تصميم فقط).
        return redirect()->route('admin.admins.index')
            ->with('info', 'إضافة أدمن جديد ميزة قيد التطوير، وسيتم ربطها بالباك-إند لاحقًا.');
    }

    public function toggleActive(User $admin): RedirectResponse
    {
        // بانتظار الربط بالباك-إند: عمود is_active غير مفعّل بأي منطق فعلي حاليًا.
        return redirect()->route('admin.admins.index')
            ->with('info', 'تفعيل/تعطيل حساب الأدمن ميزة قيد التطوير، وسيتم ربطها بالباك-إند لاحقًا.');
    }
}
