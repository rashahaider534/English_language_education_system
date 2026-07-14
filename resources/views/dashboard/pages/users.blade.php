@extends('dashboard.layouts.app')

@section('content')
    <section class="dashboard-card dashboard-panel">
        <div class="dashboard-toolbar">
            <div>
                <h3>Users Directory</h3>
                <p class="dashboard-panel__text">Static demo table intended for dashboard preview only.</p>
            </div>
            <div class="dashboard-toolbar__actions">
                <label class="dashboard-search dashboard-search--compact">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                        <path d="m21 21-4.35-4.35" />
                        <circle cx="11" cy="11" r="6" />
                    </svg>
                    <input type="search" placeholder="Search users" />
                </label>
                <button type="button" class="dashboard-button">Add User</button>
            </div>
        </div>

        <div class="table-wrap">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user['name'] }}</td>
                            <td>{{ $user['email'] }}</td>
                            <td>{{ $user['role'] }}</td>
                            <td>{{ $user['joined'] }}</td>
                            <td>
                                <span class="dashboard-badge {{ strtolower($user['status']) === 'active' ? 'dashboard-badge--success' : (strtolower($user['status']) === 'pending' ? 'dashboard-badge--warning' : 'dashboard-badge--danger') }}">
                                    {{ $user['status'] }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <button type="button" class="dashboard-button dashboard-button--ghost">View</button>
                                    <button type="button" class="dashboard-button dashboard-button--ghost">Edit</button>
                                    <button type="button" class="dashboard-button dashboard-button--danger">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
