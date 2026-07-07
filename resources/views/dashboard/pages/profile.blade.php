@extends('dashboard.layouts.app')

@section('content')
    <div class="dashboard-grid dashboard-grid--split">
        <section class="dashboard-card profile-card">
            <div class="profile-card__avatar">{{ strtoupper(substr($dashboardUser['name'], 0, 1)) }}</div>
            <h3>{{ $dashboardUser['name'] }}</h3>
            <p>{{ $dashboardUser['role'] }}</p>
            <span>{{ $dashboardUser['email'] }}</span>

            <div class="dashboard-summary-list">
                <div><span>Courses Managed</span><strong>14</strong></div>
                <div><span>Last Login</span><strong>Today</strong></div>
                <div><span>Security Score</span><strong>92%</strong></div>
            </div>
        </section>

        <section class="dashboard-card dashboard-panel">
            <div class="dashboard-panel__header">
                <div>
                    <p class="dashboard-panel__eyebrow">Profile Form</p>
                    <h3>Edit Profile</h3>
                </div>
            </div>
            <form class="dashboard-form">
                <div class="form-grid">
                    <div class="form-field">
                        <label>Display Name</label>
                        <input type="text" value="{{ $dashboardUser['name'] }}" />
                    </div>
                    <div class="form-field">
                        <label>Email</label>
                        <input type="email" value="{{ $dashboardUser['email'] }}" />
                    </div>
                    <div class="form-field form-field--full">
                        <label>About</label>
                        <textarea rows="4">Platform administrator focused on learning operations and reporting.</textarea>
                    </div>
                </div>
            </form>
        </section>
    </div>

    <section class="dashboard-card dashboard-panel">
        <div class="dashboard-panel__header">
            <div>
                <p class="dashboard-panel__eyebrow">Security</p>
                <h3>Security Settings</h3>
            </div>
        </div>
        <div class="dashboard-grid dashboard-grid--split">
            <div class="sub-card">
                <h4>Two-Factor Authentication</h4>
                <p>Enable a second factor for additional account protection.</p>
                <label class="toggle">
                    <input type="checkbox" checked>
                    <span></span>
                </label>
            </div>
            <div class="sub-card">
                <h4>Session Monitoring</h4>
                <p>Review current device sessions and revoke stale access.</p>
                <button type="button" class="dashboard-button dashboard-button--ghost">Manage Sessions</button>
            </div>
        </div>
    </section>
@endsection
