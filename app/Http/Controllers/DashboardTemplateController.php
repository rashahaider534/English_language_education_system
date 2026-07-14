<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardTemplateController extends Controller
{
    public function index(): View
    {
        return $this->render('dashboard.pages.dashboard', [
            'title' => 'Dashboard Overview',
            'subtitle' => 'Track platform activity, engagement trends, and quick actions from one place.',
            'breadcrumbs' => [
                ['label' => 'Dashboard'],
            ],
            'stats' => [
                ['label' => 'Active Students', 'value' => '12,480', 'change' => '+8.2%', 'trend' => 'up'],
                ['label' => 'Course Completion', 'value' => '84.6%', 'change' => '+3.4%', 'trend' => 'up'],
                ['label' => 'Live Sessions', 'value' => '126', 'change' => '+12 today', 'trend' => 'up'],
                ['label' => 'Open Support Cases', 'value' => '18', 'change' => '-5.1%', 'trend' => 'down'],
            ],
            'activity' => [
                ['title' => 'Placement exam published', 'meta' => '2 minutes ago', 'type' => 'success'],
                ['title' => '18 students reached Intermediate B1', 'meta' => '18 minutes ago', 'type' => 'info'],
                ['title' => 'Payment reconciliation waiting review', 'meta' => '45 minutes ago', 'type' => 'warning'],
                ['title' => 'Content update synced to mobile app', 'meta' => '1 hour ago', 'type' => 'default'],
            ],
            'recentUsers' => [
                ['name' => 'Maya Thompson', 'email' => 'maya@example.com', 'role' => 'Student', 'status' => 'Active'],
                ['name' => 'James Collins', 'email' => 'james@example.com', 'role' => 'Teacher', 'status' => 'Pending'],
                ['name' => 'Sara Nelson', 'email' => 'sara@example.com', 'role' => 'Parent', 'status' => 'Active'],
                ['name' => 'Omar Faris', 'email' => 'omar@example.com', 'role' => 'Admin', 'status' => 'Suspended'],
            ],
            'quickActions' => [
                ['title' => 'Create Course', 'description' => 'Prepare a new learning path for the next intake.'],
                ['title' => 'Review Reports', 'description' => 'Check weekly growth and retention metrics.'],
                ['title' => 'Broadcast Notice', 'description' => 'Send a platform-wide notification to all users.'],
            ],
            'chart' => [
                ['label' => 'Mon', 'value' => 54],
                ['label' => 'Tue', 'value' => 72],
                ['label' => 'Wed', 'value' => 68],
                ['label' => 'Thu', 'value' => 88],
                ['label' => 'Fri', 'value' => 81],
                ['label' => 'Sat', 'value' => 60],
                ['label' => 'Sun', 'value' => 76],
            ],
        ]);
    }

    public function users(): View
    {
        return $this->render('dashboard.pages.users', [
            'title' => 'Users Management',
            'subtitle' => 'Demo listing for learners, teachers, and administrators.',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Users'],
            ],
            'users' => [
                ['name' => 'Maya Thompson', 'email' => 'maya@example.com', 'role' => 'Student', 'joined' => '2026-06-28', 'status' => 'Active'],
                ['name' => 'James Collins', 'email' => 'james@example.com', 'role' => 'Teacher', 'joined' => '2026-06-27', 'status' => 'Pending'],
                ['name' => 'Sara Nelson', 'email' => 'sara@example.com', 'role' => 'Parent', 'joined' => '2026-06-26', 'status' => 'Active'],
                ['name' => 'Omar Faris', 'email' => 'omar@example.com', 'role' => 'Admin', 'joined' => '2026-06-25', 'status' => 'Suspended'],
                ['name' => 'Lina Carter', 'email' => 'lina@example.com', 'role' => 'Student', 'joined' => '2026-06-21', 'status' => 'Active'],
            ],
        ]);
    }

    public function roles(): View
    {
        return $this->render('dashboard.pages.roles', [
            'title' => 'Roles & Permissions',
            'subtitle' => 'Reference roles, responsibilities, and access scope for the dashboard.',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Roles & Permissions'],
            ],
            'roles' => [
                ['name' => 'Administrator', 'users' => 4, 'description' => 'Full access to platform settings, content, and reporting.'],
                ['name' => 'Teacher', 'users' => 18, 'description' => 'Manage classes, assignments, and learner progress.'],
                ['name' => 'Student', 'users' => 1234, 'description' => 'Access courses, assessments, and certificates.'],
                ['name' => 'Support', 'users' => 6, 'description' => 'Handle incidents, moderation, and communication workflows.'],
            ],
            'permissions' => [
                'Manage learners and instructors',
                'Publish lessons and course modules',
                'Review payments and export reports',
                'Configure notifications and system preferences',
                'Moderate comments and support cases',
                'Audit platform activity logs',
            ],
            'matrix' => [
                ['permission' => 'User Management', 'admin' => true, 'teacher' => false, 'student' => false, 'support' => true],
                ['permission' => 'Course Publishing', 'admin' => true, 'teacher' => true, 'student' => false, 'support' => false],
                ['permission' => 'Assessment Review', 'admin' => true, 'teacher' => true, 'student' => false, 'support' => false],
                ['permission' => 'Billing Export', 'admin' => true, 'teacher' => false, 'student' => false, 'support' => true],
            ],
        ]);
    }

    public function reports(): View
    {
        return $this->render('dashboard.pages.reports', [
            'title' => 'Reports Center',
            'subtitle' => 'Demo reporting workspace with quick filters and export actions.',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Reports'],
            ],
            'reportCards' => [
                ['label' => 'New Enrollments', 'value' => '438', 'detail' => 'This month'],
                ['label' => 'Average Score', 'value' => '91%', 'detail' => 'Across placement tests'],
                ['label' => 'Renewal Rate', 'value' => '76%', 'detail' => 'Monthly subscriptions'],
                ['label' => 'Completion Growth', 'value' => '+14%', 'detail' => 'Compared with last month'],
            ],
            'reports' => [
                ['name' => 'Weekly Engagement Summary', 'owner' => 'Operations', 'updated' => '2026-07-04', 'status' => 'Ready'],
                ['name' => 'Course Performance Review', 'owner' => 'Academic Team', 'updated' => '2026-07-03', 'status' => 'Processing'],
                ['name' => 'Revenue Snapshot', 'owner' => 'Finance', 'updated' => '2026-07-02', 'status' => 'Ready'],
                ['name' => 'Teacher Productivity Log', 'owner' => 'HR', 'updated' => '2026-07-01', 'status' => 'Archived'],
            ],
        ]);
    }

    public function tables(): View
    {
        return $this->render('dashboard.pages.tables', [
            'title' => 'Tables Showcase',
            'subtitle' => 'Different table styles ready for list-heavy admin workflows.',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Tables'],
            ],
            'courses' => [
                ['course' => 'Business English', 'level' => 'B2', 'students' => 120, 'status' => 'Live'],
                ['course' => 'IELTS Intensive', 'level' => 'C1', 'students' => 84, 'status' => 'Draft'],
                ['course' => 'Grammar Foundations', 'level' => 'A2', 'students' => 201, 'status' => 'Live'],
                ['course' => 'Conversation Lab', 'level' => 'B1', 'students' => 96, 'status' => 'Archived'],
            ],
            'payments' => [
                ['invoice' => '#INV-1023', 'student' => 'Maya Thompson', 'amount' => '$120', 'method' => 'Card', 'status' => 'Paid'],
                ['invoice' => '#INV-1024', 'student' => 'Lina Carter', 'amount' => '$95', 'method' => 'PayPal', 'status' => 'Pending'],
                ['invoice' => '#INV-1025', 'student' => 'Noah Stone', 'amount' => '$180', 'method' => 'Bank Transfer', 'status' => 'Refunded'],
            ],
        ]);
    }

    public function forms(): View
    {
        return $this->render('dashboard.pages.forms', [
            'title' => 'Forms Showcase',
            'subtitle' => 'Reusable admin form patterns for CRUD screens and settings pages.',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Forms'],
            ],
        ]);
    }

    public function cards(): View
    {
        return $this->render('dashboard.pages.cards', [
            'title' => 'Cards Library',
            'subtitle' => 'Summary cards, metric blocks, and contextual content panels.',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Cards'],
            ],
        ]);
    }

    public function charts(): View
    {
        return $this->render('dashboard.pages.charts', [
            'title' => 'Charts Preview',
            'subtitle' => 'Simple visual placeholders built without extra chart packages.',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Charts'],
            ],
            'engagementBars' => [
                ['label' => 'Jan', 'value' => 48],
                ['label' => 'Feb', 'value' => 62],
                ['label' => 'Mar', 'value' => 58],
                ['label' => 'Apr', 'value' => 80],
                ['label' => 'May', 'value' => 74],
                ['label' => 'Jun', 'value' => 92],
            ],
            'distribution' => [
                ['label' => 'Students', 'value' => 62, 'color' => 'bg-sky-500'],
                ['label' => 'Teachers', 'value' => 18, 'color' => 'bg-emerald-500'],
                ['label' => 'Parents', 'value' => 12, 'color' => 'bg-amber-500'],
                ['label' => 'Admins', 'value' => 8, 'color' => 'bg-violet-500'],
            ],
        ]);
    }

    public function notifications(): View
    {
        return $this->render('dashboard.pages.notifications', [
            'title' => 'Notifications Center',
            'subtitle' => 'Demo alerts, banners, toast styles, and activity messages.',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Notifications'],
            ],
            'feed' => [
                ['title' => 'Weekly report is ready', 'message' => 'Your engagement summary has been generated for review.', 'time' => '5 minutes ago', 'type' => 'info'],
                ['title' => 'New instructor approved', 'message' => 'A teacher account has completed verification and is now active.', 'time' => '22 minutes ago', 'type' => 'success'],
                ['title' => 'Payment reconciliation delayed', 'message' => 'Two transactions require manual confirmation.', 'time' => '1 hour ago', 'type' => 'warning'],
                ['title' => 'Maintenance scheduled', 'message' => 'A low-impact deployment window is planned for tonight.', 'time' => '3 hours ago', 'type' => 'default'],
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

    public function blank(): View
    {
        return $this->render('dashboard.pages.blank', [
            'title' => 'Blank Page',
            'subtitle' => 'An empty scaffold for future dashboard features.',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Blank Page'],
            ],
        ]);
    }

    private function render(string $view, array $data = []): View
    {
        return view($view, $data + [
            'dashboardUser' => [
                'name' => optional(auth()->user())->name ?: 'Admin User',
                'email' => optional(auth()->user())->email ?: 'admin@example.com',
                'role' => 'Platform Administrator',
            ],
        ]);
    }
}
