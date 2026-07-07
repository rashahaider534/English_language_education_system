<header class="dashboard-topbar">
    <div class="dashboard-topbar__left">
        <button type="button" class="dashboard-icon-button" data-sidebar-toggle aria-label="Toggle sidebar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <path d="M4 7h16M4 12h16M4 17h16" />
            </svg>
        </button>

        <div>
            <p class="dashboard-topbar__eyebrow">Dashboard Template</p>
            <h2 class="dashboard-topbar__title">{{ $title ?? 'Dashboard' }}</h2>
        </div>
    </div>

    <div class="dashboard-topbar__right">
        <label class="dashboard-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <path d="m21 21-4.35-4.35" />
                <circle cx="11" cy="11" r="6" />
            </svg>
            <input type="search" placeholder="Search anything..." aria-label="Search dashboard" />
        </label>

        <button type="button" class="dashboard-icon-button dashboard-notification-button" aria-label="Notifications">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5" />
                <path d="M9.5 17a2.5 2.5 0 0 0 5 0" />
            </svg>
            <span class="dashboard-notification-badge">6</span>
        </button>

        <div class="dashboard-user-menu" data-user-menu>
            <button type="button" class="dashboard-user-menu__trigger" data-user-menu-trigger aria-expanded="false">
                <span class="dashboard-user-menu__avatar">{{ strtoupper(substr($dashboardUser['name'], 0, 1)) }}</span>
                <span class="dashboard-user-menu__meta">
                    <strong>{{ $dashboardUser['name'] }}</strong>
                    <small>{{ $dashboardUser['role'] }}</small>
                </span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <path d="m6 9 6 6 6-6" />
                </svg>
            </button>

            <div class="dashboard-user-menu__dropdown" data-user-menu-dropdown>
                <a href="{{ route('dashboard.profile') }}">View Profile</a>
                <a href="{{ route('dashboard.settings') }}">Account Settings</a>
                <a href="{{ route('profile.edit') }}">Laravel Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </a>
                </form>
            </div>
        </div>
    </div>
</header>
