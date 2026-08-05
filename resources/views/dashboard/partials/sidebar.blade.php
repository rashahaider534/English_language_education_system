@php
    $dashboardNav = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home'],
        ['label' => 'محتوى تعليمي', 'route' => 'levels.index', 'icon' => 'levels'],
        ['label' => 'الدروس قيد الانتظار', 'route' => 'lessons.pending', 'icon' => 'pending-lessons'],
        ['label' => 'طلبات الاستثناء', 'route' => 'levelException.index', 'icon' => 'level-exceptions', 'superAdminOnly' => true],
        ['label' => 'بنك أسئلة تحديد المستوى', 'route' => 'questions.placement.index', 'icon' => 'question-bank', 'requiredPermission' => 'manage_placement_questions'],
        ['label' => 'اختبارات تحديد المستوى', 'route' => 'tests.placement.placement.index', 'icon' => 'placement-tests', 'requiredPermission' => 'manage_placement_tests'],
        [
            'label' => 'إدارة ومتابعة',
            'icon' => 'management',
            'children' => [
                ['label' => 'أساتذة', 'route' => 'admin.teachers.index', 'icon' => 'teachers'],
                ['label' => 'طلاب', 'route' => 'admin.students.index', 'icon' => 'students'],
            ],
        ],
        ['label' => 'صندوق الشكاوي', 'route' => 'admin.complaints.index', 'icon' => 'complaints'],
    ];
@endphp

<aside class="dashboard-sidebar" data-dashboard-sidebar>
    <div class="dashboard-sidebar__brand">
        <div class="dashboard-sidebar__logo">EL</div>
        <div class="dashboard-sidebar__brand-text">
            <p class="dashboard-sidebar__eyebrow">Admin Workspace</p>
            <h1 class="dashboard-sidebar__title">{{ config('app.name', 'English System') }}</h1>
        </div>
    </div>

    <nav class="dashboard-sidebar__nav">
        <p class="dashboard-sidebar__section">Navigation</p>

        @foreach ($dashboardNav as $item)
            @continue(($item['superAdminOnly'] ?? false) && !auth()->user()->hasRole('super-admin', 'web'))
            @continue(($item['requiredPermission'] ?? null) && !auth()->user()->can($item['requiredPermission'], 'web'))

            @if (!empty($item['children']))
                @php
                    $children = collect($item['children'])->filter(function ($child) {
                        return (!($child['superAdminOnly'] ?? false) || auth()->user()->hasRole('super-admin', 'web'))
                            && (!($child['requiredPermission'] ?? null) || auth()->user()->can($child['requiredPermission'], 'web'));
                    })->values();
                    $groupActive = $children->contains(fn ($child) => request()->routeIs($child['route']) || request()->routeIs($child['route'].'.*'));
                @endphp
                @if ($children->isNotEmpty())
                    <div x-data="{ open: {{ $groupActive ? 'true' : 'false' }} }" class="dashboard-nav-group">
                        <button type="button"
                            class="dashboard-nav-link dashboard-nav-group__trigger {{ $groupActive ? 'is-active' : '' }}"
                            @click="open = !open; document.querySelector('[data-dashboard-shell]')?.classList.remove('is-sidebar-collapsed'); try { localStorage.setItem('dashboardSidebarCollapsed', '0'); } catch (e) {}"
                            :aria-expanded="open.toString()"
                            title="{{ $item['label'] }}">
                            <span class="dashboard-nav-link__icon">
                                @include('dashboard.partials.icons.'.$item['icon'])
                            </span>
                            <span class="dashboard-nav-link__label">{{ $item['label'] }}</span>
                            <span class="dashboard-nav-group__chevron" :class="{ 'is-open': open }">
                                @include('dashboard.partials.icons.chevron-down')
                            </span>
                        </button>

                        <div class="dashboard-nav-group__panel" x-show="open" x-transition x-cloak>
                            @foreach ($children as $child)
                                @php $childActive = request()->routeIs($child['route']) || request()->routeIs($child['route'].'.*'); @endphp
                                <a href="{{ route($child['route']) }}" class="dashboard-nav-link dashboard-nav-link--child {{ $childActive ? 'is-active' : '' }}" title="{{ $child['label'] }}">
                                    <span class="dashboard-nav-link__icon">
                                        @include('dashboard.partials.icons.'.$child['icon'])
                                    </span>
                                    <span class="dashboard-nav-link__label">{{ $child['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
                @continue
            @endif

            @php
                $active = request()->routeIs($item['route'])
                    || ($item['route'] === 'levels.index' && (request()->routeIs('levels.*') || request()->routeIs('courses.*')))
                    || ($item['route'] === 'tests.placement.placement.index' && request()->routeIs('tests.placement.*'));
            @endphp
            <a href="{{ route($item['route']) }}" class="dashboard-nav-link {{ $active ? 'is-active' : '' }}" title="{{ $item['label'] }}">
                <span class="dashboard-nav-link__icon">
                    @include('dashboard.partials.icons.'.$item['icon'])
                </span>
                <span class="dashboard-nav-link__label">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="dashboard-sidebar__footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dashboard-sidebar__logout" title="تسجيل الخروج">
                <span class="dashboard-nav-link__icon">
                    @include('dashboard.partials.icons.logout')
                </span>
                <span class="dashboard-sidebar__logout-label">تسجيل الخروج</span>
            </button>
        </form>
    </div>
</aside>

<div class="dashboard-backdrop" data-dashboard-backdrop></div>
