<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class PermissionManagementController extends Controller
{
    public function index(): View
    {
        $admins = User::role('admin', 'web')->orderBy('first_name')->get();
        $teachers = User::role('teacher', 'api')->orderBy('first_name')->get();
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();

        $permissionLabels = [
            'manage_levels' => 'إدارة المستويات',
            'manage_courses' => 'إدارة الكورسات',
            'manage_level_tests' => 'إدارة اختبارات تحديد المستوى',
            'manage_placement_tests' => 'إدارة اختبارات القبول',
            'manage_placement_questions' => 'إدارة بنك أسئلة تحديد المستوى',
            'manage_podcasts' => 'إدارة البودكاست والتوبكس',
            'publish_levels' => 'نشر المستويات',
        ];

        return view('admin.permissions.index', compact('admins', 'teachers', 'permissions', 'permissionLabels'));
    }

    public function update(Request $request): RedirectResponse
    {
        // بانتظار الربط بالباك-إند: التغييرات هون تصميم واجهة فقط، ما بتنحفظ فعليًا لهلق.
        return redirect()->route('admin.permissions.index')
            ->with('info', 'تعديل الصلاحيات ميزة قيد التطوير — الجدول أعلاه بيعرض الصلاحيات الحقيقية الحالية، بس الحفظ لسا مو مفعّل.');
    }
}
