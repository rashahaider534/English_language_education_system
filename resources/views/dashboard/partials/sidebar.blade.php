@php
    $dashboardNav = [
        ['label' => 'لوحة التحكم', 'route' => 'dashboard', 'icon' => 'home'],
        ['label' => 'محتوى تعليمي', 'route' => 'levels.index', 'icon' => 'levels'],
        ['label' => 'محتوى قيد المراجعة', 'route' => 'admin.content-review.pending-queue', 'icon' => 'pending-lessons', 'hideForSuperAdmin' => true],
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
        ['label' => 'المدفوعات', 'route' => 'admin.payments.index', 'icon' => 'payments', 'superAdminOnly' => true],
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

        @php
            $sidebarNotifications = $sidebarUser ? $sidebarUser->notifications()->orderBy('read')->orderByDesc('created_at')->take(8)->get() : collect();
            $sidebarUnreadCount = $sidebarUser ? $sidebarUser->notifications()->where('read', false)->count() : 0;
        @endphp
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <button type="button" class="dashboard-icon-button dashboard-topbar-accent dashboard-notification-button" aria-label="Notifications" @click="open = !open" :aria-expanded="open.toString()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5" />
                    <path d="M9.5 17a2.5 2.5 0 0 0 5 0" />
                </svg>
                @if ($sidebarUnreadCount > 0)
                    <span class="dashboard-notification-badge">{{ $sidebarUnreadCount > 9 ? '9+' : $sidebarUnreadCount }}</span>
                @endif
            </button>

            <div class="dashboard-notification-panel" x-show="open" x-transition x-cloak>
                <div class="dashboard-notification-panel__header">
                    <span class="dashboard-notification-panel__title">الإشعارات</span>
                    @if ($sidebarUnreadCount > 0)
                        <form action="{{ route('notifications.readAll') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" style="border:none; background:none; padding:0; font-size:11px; font-weight:700; color:#0E6A96; cursor:pointer;">تحديد الكل كمقروء</button>
                        </form>
                    @endif
                </div>
                <div class="dashboard-notification-panel__list">
                    @forelse ($sidebarNotifications as $notification)
                        @if ($notification->read)
                            <div class="dashboard-notification-item">
                                <span class="dashboard-notification-item__dot" style="background:rgba(0,83,122,0.2);"></span>
                                <div>
                                    <p style="margin:0; font-size:13px; font-weight:600; color:rgba(1,60,88,0.6);">{{ $notification->title }}</p>
                                    <p style="margin:2px 0 0; font-size:11px; color:rgba(0,83,122,0.45);">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @else
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="dashboard-notification-item" style="width:100%; text-align:right; border:none; background:none; cursor:pointer;">
                                    <span class="dashboard-notification-item__dot" style="background:#F5A201;"></span>
                                    <div>
                                        <p style="margin:0; font-size:13px; font-weight:700; color:#013C58;">{{ $notification->title }}</p>
                                        <p style="margin:2px 0 0; font-size:11px; color:rgba(0,83,122,0.5);">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                </button>
                            </form>
                        @endif
                    @empty
                        <div class="dashboard-notification-item">
                            <p style="margin:0; font-size:13px; color:rgba(1,60,88,0.45);">لايوجد اشعارات للان </p>
                        </div>
                    @endforelse
                </div>
                @if ($sidebarNotifications->isNotEmpty())
                    <a href="{{ route('notifications.index') }}" class="dashboard-notification-panel__footer" style="display:block; text-decoration:none;">عرض جميع الإشعارات</a>
                @endif
            </div>
        </div>
    </div>

    <nav class="dashboard-sidebar__nav">
        <p class="dashboard-sidebar__section">Navigation</p>

        @foreach ($dashboardNav as $item)
            @continue(($item['superAdminOnly'] ?? false) && !auth()->user()->hasRole('super-admin', 'web'))
            @continue(($item['hideForSuperAdmin'] ?? false) && auth()->user()->hasRole('super-admin', 'web'))
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
                    || ($item['route'] === 'topics.index' && (request()->routeIs('topics.*') || request()->routeIs('podcasts.*')))
                    || ($item['route'] === 'admin.content-review.pending-queue' && request()->routeIs('admin.content-review.*'));
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
