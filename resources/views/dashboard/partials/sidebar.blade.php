@php
    $dashboardNav = [
        ['label' => 'لوحة التحكم', 'route' => 'dashboard', 'icon' => 'home'],
        ['label' => 'محتوى تعليمي', 'route' => 'levels.index', 'icon' => 'levels'],
        ['label' => 'الدروس قيد الانتظار', 'route' => 'lessons.pending', 'icon' => 'pending-lessons'],
        ['label' => 'طلبات الاستثناء', 'route' => 'levelException.index', 'icon' => 'level-exceptions', 'superAdminOnly' => true],
        ['label' => 'بنك أسئلة تحديد المستوى', 'route' => 'questions.placement.index', 'icon' => 'question-bank', 'requiredPermission' => 'manage_placement_questions'],
        ['label' => 'اختبارات تحديد المستوى', 'route' => 'tests.placement.placement.index', 'icon' => 'placement-tests', 'requiredPermission' => 'manage_placement_tests'],
        ['label' => 'بودكاست ', 'route' => 'topics.index', 'icon' => 'podcasts', 'requiredPermission' => 'manage_podcasts'],
        [
            'label' => 'إدارة ومتابعة',
            'icon' => 'management',
            'children' => [
                ['label' => 'أساتذة', 'route' => 'admin.teachers.index', 'icon' => 'teachers'],
                ['label' => 'طلاب', 'route' => 'admin.students.index', 'icon' => 'students'],
                ['label' => 'أدمنز', 'route' => 'admin.admins.index', 'icon' => 'teachers', 'superAdminOnly' => true],
            ],
        ],
        ['label' => 'صندوق الشكاوي', 'route' => 'admin.complaints.index', 'icon' => 'complaints'],
        ['label' => 'الخصومات والعروض', 'route' => 'admin.offers.index', 'icon' => 'offers', 'superAdminOnly' => true],
        ['label' => 'المدفوعات', 'route' => 'admin.payments.index', 'icon' => 'payments', 'superAdminOnly' => true],
        ['label' => 'الرقابة وإدارة الأعمال', 'route' => 'admin.audit.index', 'icon' => 'audit', 'superAdminOnly' => true],
        ['label' => 'الصلاحيات', 'route' => 'admin.permissions.index', 'icon' => 'permissions', 'superAdminOnly' => true],
    ];
@endphp

<aside class="dashboard-sidebar" data-dashboard-sidebar>
    <div class="dashboard-sidebar__brand">
        @php
            $sidebarUser = auth()->user();
            $sidebarUserName = $sidebarUser ? (trim(($sidebarUser->first_name ?? '').' '.($sidebarUser->last_name ?? '')) ?: $sidebarUser->email) : 'Admin';
            $sidebarUserInitial = $sidebarUser ? strtoupper(substr($sidebarUser->first_name ?? $sidebarUser->email, 0, 1)) : 'A';
        @endphp
        <div class="dashboard-sidebar__logo">{{ $sidebarUserInitial }}</div>
        <div class="dashboard-sidebar__brand-text">
            <p class="dashboard-sidebar__eyebrow">Admin Workspace</p>
            <h1 class="dashboard-sidebar__title">{{ $sidebarUserName }}</h1>
        </div>
    </div>

    <div class="dashboard-sidebar__controls">
        <button type="button" class="dashboard-icon-button dashboard-topbar-accent" data-sidebar-toggle aria-label="Toggle sidebar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <path d="M4 7h16M4 12h16M4 17h16" />
            </svg>
        </button>

        @php
            $dashboardCurrentLocale = app()->getLocale();
            $dashboardNextLocale = $dashboardCurrentLocale === 'ar' ? 'en' : 'ar';
            $dashboardLangLabel = $dashboardCurrentLocale === 'ar' ? 'EN' : 'عربي';
        @endphp
        <a href="{{ url('/language/'.$dashboardNextLocale) }}" class="dashboard-icon-button dashboard-topbar-accent dashboard-lang-toggle-compact" title="تبديل اللغة">
            {{ $dashboardLangLabel }}
        </a>

        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <button type="button" class="dashboard-icon-button dashboard-topbar-accent dashboard-notification-button" aria-label="Notifications" @click="open = !open" :aria-expanded="open.toString()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5" />
                    <path d="M9.5 17a2.5 2.5 0 0 0 5 0" />
                </svg>
            </button>

            <div class="dashboard-notification-panel" x-show="open" x-transition x-cloak>
                <div class="dashboard-notification-panel__header">
                    <span class="dashboard-notification-panel__title">الإشعارات</span>
                    <span class="dashboard-badge dashboard-badge--info">تجريبي</span>
                </div>
                <div class="dashboard-notification-panel__list">
                    <div class="dashboard-notification-item">
                        <span class="dashboard-notification-item__dot" style="background:#F5A201;"></span>
                        <div>
                            <p style="margin:0; font-size:13px; font-weight:600; color:#013C58;">طلب استثناء جديد بانتظار المراجعة</p>
                            <p style="margin:2px 0 0; font-size:11px; color:rgba(0,83,122,0.5);">منذ 5 دقائق</p>
                        </div>
                    </div>
                    <div class="dashboard-notification-item">
                        <span class="dashboard-notification-item__dot" style="background:#0E6A96;"></span>
                        <div>
                            <p style="margin:0; font-size:13px; font-weight:600; color:#013C58;">درس جديد قيد الانتظار للمراجعة</p>
                            <p style="margin:2px 0 0; font-size:11px; color:rgba(0,83,122,0.5);">منذ ساعة</p>
                        </div>
                    </div>
                    <div class="dashboard-notification-item">
                        <span class="dashboard-notification-item__dot" style="background:#4CAF78;"></span>
                        <div>
                            <p style="margin:0; font-size:13px; font-weight:600; color:#013C58;">تم اعتماد سؤال جديد ببنك الأسئلة</p>
                            <p style="margin:2px 0 0; font-size:11px; color:rgba(0,83,122,0.5);">أمس</p>
                        </div>
                    </div>
                </div>
                <div class="dashboard-notification-panel__footer">نظام الإشعارات قيد التطوير — هذه بيانات توضيحية مؤقتة</div>
            </div>
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
                    || ($item['route'] === 'tests.placement.placement.index' && request()->routeIs('tests.placement.*'))
                    || ($item['route'] === 'topics.index' && (request()->routeIs('topics.*') || request()->routeIs('podcasts.*')));
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
