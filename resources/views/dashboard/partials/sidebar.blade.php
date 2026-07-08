@php
    $dashboardNav = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home'],
        ['label' => 'مستويات', 'route' => 'levels.index', 'icon' => 'levels'],
        ['label' => 'Users', 'route' => 'dashboard.users', 'icon' => 'users'],
        ['label' => 'Roles & Permissions', 'route' => 'dashboard.roles', 'icon' => 'shield'],
        ['label' => 'Reports', 'route' => 'dashboard.reports', 'icon' => 'reports'],
        ['label' => 'Tables', 'route' => 'dashboard.tables', 'icon' => 'table'],
        ['label' => 'Forms', 'route' => 'dashboard.forms', 'icon' => 'forms'],
        ['label' => 'Cards', 'route' => 'dashboard.cards', 'icon' => 'cards'],
        ['label' => 'Charts', 'route' => 'dashboard.charts', 'icon' => 'charts'],
        ['label' => 'Notifications', 'route' => 'dashboard.notifications', 'icon' => 'bell'],
        ['label' => 'Profile', 'route' => 'dashboard.profile', 'icon' => 'profile'],
        ['label' => 'Settings', 'route' => 'dashboard.settings', 'icon' => 'settings'],
        ['label' => 'Blank Page', 'route' => 'dashboard.blank', 'icon' => 'blank'],
    ];
@endphp

<aside class="dashboard-sidebar" data-dashboard-sidebar>
    <div class="dashboard-sidebar__brand">
        <div class="dashboard-sidebar__logo">EL</div>
        <div>
            <p class="dashboard-sidebar__eyebrow">Admin Workspace</p>
            <h1 class="dashboard-sidebar__title">{{ config('app.name', 'English System') }}</h1>
        </div>
    </div>

    <nav class="dashboard-sidebar__nav">
        <p class="dashboard-sidebar__section">Navigation</p>

        @foreach ($dashboardNav as $item)
            @php
                $active = request()->routeIs($item['route'])
                    || ($item['route'] === 'levels.index' && request()->routeIs('levels.*'));
            @endphp
            <a href="{{ route($item['route']) }}" class="dashboard-nav-link {{ $active ? 'is-active' : '' }}">
                <span class="dashboard-nav-link__icon">
                    @include('dashboard.partials.icons.'.$item['icon'])
                </span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="dashboard-sidebar__panel">
        <p class="dashboard-sidebar__section">System Status</p>
        <div class="dashboard-sidebar__metric">
            <span>Uptime</span>
            <strong>99.94%</strong>
        </div>
        <div class="dashboard-progress">
            <span style="width: 81%"></span>
        </div>
        <p class="dashboard-sidebar__help">Demo dashboard template ready for extension inside Laravel Blade.</p>
    </div>
</aside>

<div class="dashboard-backdrop" data-dashboard-backdrop></div>
