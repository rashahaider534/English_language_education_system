<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
use Illuminate\View\View;

class DashboardTemplateController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function index(): View
    {
        if (auth()->user()?->hasRole('super-admin')) {
            return $this->superAdminIndex();
        }

        return $this->render('dashboard.pages.dashboard', [
            'title' => 'الرئيسية',
            'subtitle' => 'مرحبًا بك في لوحة تحكم الأدمن.',
            'breadcrumbs' => [
                ['label' => 'Dashboard'],
            ],
        ] + $this->dashboardService->adminData());
    }

    private function superAdminIndex(): View
    {
        return $this->render('dashboard.pages.super-admin-dashboard', [
            'title' => 'الرئيسية',
            'subtitle' => 'مرحبًا بك في لوحة تحكم السوبر أدمن.',
            'breadcrumbs' => [
                ['label' => 'Dashboard'],
            ],
        ] + $this->dashboardService->superAdminData());
    }

    public function profile(): View
    {
        return $this->render('dashboard.pages.profile', [
            'title' => 'Profile Overview',
            'subtitle' => 'Demo profile details, preferences, and account security widgets.',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Profile'],
            ],
        ]);
    }

    public function settings(): View
    {
        return $this->render('dashboard.pages.settings', [
            'title' => 'Platform Settings',
            'subtitle' => 'General preferences, system controls, and notification defaults.',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Settings'],
            ],
        ]);
    }

    private function render(string $view, array $data = []): View
    {
        $user = auth()->user();
        $name = $user ? trim($user->first_name.' '.$user->last_name) : '';

        return view($view, $data + [
            'dashboardUser' => [
                'name' => $name ?: 'Admin User',
                'email' => $user->email ?? 'admin@example.com',
                'role' => 'Platform Administrator',
            ],
        ]);
    }
}
