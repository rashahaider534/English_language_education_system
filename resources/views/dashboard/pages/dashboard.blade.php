@extends('dashboard.layouts.app')

@section('content')
    <section class="dashboard-page-grid">
        <div class="dashboard-grid dashboard-grid--stats">
            @foreach ($stats as $stat)
                <article class="dashboard-card stat-card">
                    <div class="stat-card__label">{{ $stat['label'] }}</div>
                    <div class="stat-card__value">{{ $stat['value'] }}</div>
                    <div class="stat-card__trend {{ $stat['trend'] === 'up' ? 'is-positive' : 'is-negative' }}">{{ $stat['change'] }}</div>
                </article>
            @endforeach
        </div>

        <div class="dashboard-grid dashboard-grid--main">
            <section class="dashboard-card dashboard-panel">
                <div class="dashboard-panel__header">
                    <div>
                        <p class="dashboard-panel__eyebrow">Overview</p>
                        <h3>Welcome back, {{ $dashboardUser['name'] }}</h3>
                    </div>
                    <span class="dashboard-badge dashboard-badge--info">Live Demo</span>
                </div>
                <p class="dashboard-panel__text">{{ $subtitle }}</p>

                <div class="chart-card">
                    <div class="chart-card__header">
                        <div>
                            <h4>Weekly Engagement</h4>
                            <p>Completed lessons and active sessions</p>
                        </div>
                        <strong>+12.8%</strong>
                    </div>
                    <div class="mini-bars">
                        @foreach ($chart as $item)
                            <div class="mini-bars__item">
                                <span class="mini-bars__bar" style="height: {{ $item['value'] }}%"></span>
                                <small>{{ $item['label'] }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="dashboard-card dashboard-panel">
                <div class="dashboard-panel__header">
                    <div>
                        <p class="dashboard-panel__eyebrow">Actions</p>
                        <h3>Quick Actions</h3>
                    </div>
                </div>
                <div class="dashboard-stack">
                    @foreach ($quickActions as $action)
                        <article class="action-tile">
                            <div>
                                <h4>{{ $action['title'] }}</h4>
                                <p>{{ $action['description'] }}</p>
                            </div>
                            <button type="button" class="dashboard-button dashboard-button--ghost">Open</button>
                        </article>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="dashboard-grid dashboard-grid--split">
            <section class="dashboard-card dashboard-panel">
                <div class="dashboard-panel__header">
                    <div>
                        <p class="dashboard-panel__eyebrow">Timeline</p>
                        <h3>Recent Activity</h3>
                    </div>
                </div>
                <div class="activity-feed">
                    @foreach ($activity as $item)
                        <article class="activity-item">
                            <span class="activity-item__dot activity-item__dot--{{ $item['type'] }}"></span>
                            <div>
                                <h4>{{ $item['title'] }}</h4>
                                <p>{{ $item['meta'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="dashboard-card dashboard-panel">
                <div class="dashboard-panel__header">
                    <div>
                        <p class="dashboard-panel__eyebrow">Highlights</p>
                        <h3>Platform Summary</h3>
                    </div>
                </div>
                <div class="dashboard-summary-list">
                    <div><span>New registrations</span><strong>154</strong></div>
                    <div><span>Courses published</span><strong>12</strong></div>
                    <div><span>Average rating</span><strong>4.8/5</strong></div>
                    <div><span>Pending reviews</span><strong>9</strong></div>
                </div>
            </section>
        </div>

        <section class="dashboard-card dashboard-panel">
            <div class="dashboard-panel__header">
                <div>
                    <p class="dashboard-panel__eyebrow">Users</p>
                    <h3>Recent Users</h3>
                </div>
                <a href="{{ route('dashboard.users') }}" class="dashboard-button dashboard-button--ghost">View All</a>
            </div>

            <div class="table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentUsers as $user)
                            <tr>
                                <td>{{ $user['name'] }}</td>
                                <td>{{ $user['email'] }}</td>
                                <td>{{ $user['role'] }}</td>
                                <td><span class="dashboard-badge {{ strtolower($user['status']) === 'active' ? 'dashboard-badge--success' : (strtolower($user['status']) === 'pending' ? 'dashboard-badge--warning' : 'dashboard-badge--danger') }}">{{ $user['status'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </section>
@endsection
