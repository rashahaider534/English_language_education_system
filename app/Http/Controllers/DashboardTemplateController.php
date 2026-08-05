<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardTemplateController extends Controller
{
    public function index(): View
    {
        return $this->render('dashboard.pages.dashboard', [
            'title' => 'الرئيسية',
            'subtitle' => 'مرحبًا بك في لوحة تحكم الأدمن.',
            'breadcrumbs' => [
                ['label' => 'Dashboard'],
            ],
        ]);
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
