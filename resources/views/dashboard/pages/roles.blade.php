@extends('dashboard.layouts.app')

@section('content')
    <div class="dashboard-grid dashboard-grid--stats">
        @foreach ($roles as $role)
            <article class="dashboard-card stat-card">
                <div class="stat-card__label">{{ $role['name'] }}</div>
                <div class="stat-card__value">{{ $role['users'] }}</div>
                <div class="stat-card__hint">{{ $role['description'] }}</div>
            </article>
        @endforeach
    </div>

    <div class="dashboard-grid dashboard-grid--split">
        <section class="dashboard-card dashboard-panel">
            <div class="dashboard-panel__header">
                <div>
                    <p class="dashboard-panel__eyebrow">Permissions</p>
                    <h3>Core Capabilities</h3>
                </div>
            </div>
            <div class="dashboard-stack">
                @foreach ($permissions as $permission)
                    <div class="list-item">
                        <span class="list-item__marker"></span>
                        <span>{{ $permission }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="dashboard-card dashboard-panel">
            <div class="dashboard-panel__header">
                <div>
                    <p class="dashboard-panel__eyebrow">Reference</p>
                    <h3>Roles Summary</h3>
                </div>
            </div>
            <div class="dashboard-summary-list">
                <div><span>Total roles</span><strong>{{ count($roles) }}</strong></div>
                <div><span>Permission groups</span><strong>6</strong></div>
                <div><span>Protected routes</span><strong>24</strong></div>
                <div><span>Audit rules</span><strong>12</strong></div>
            </div>
        </section>
    </div>

    <section class="dashboard-card dashboard-panel">
        <div class="dashboard-panel__header">
            <div>
                <p class="dashboard-panel__eyebrow">Access Matrix</p>
                <h3>Permission Matrix</h3>
            </div>
        </div>
        <div class="table-wrap">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Permission</th>
                        <th>Admin</th>
                        <th>Teacher</th>
                        <th>Student</th>
                        <th>Support</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($matrix as $item)
                        <tr>
                            <td>{{ $item['permission'] }}</td>
                            <td>{{ $item['admin'] ? 'Yes' : 'No' }}</td>
                            <td>{{ $item['teacher'] ? 'Yes' : 'No' }}</td>
                            <td>{{ $item['student'] ? 'Yes' : 'No' }}</td>
                            <td>{{ $item['support'] ? 'Yes' : 'No' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
