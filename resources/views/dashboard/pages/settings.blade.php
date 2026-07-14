@extends('dashboard.layouts.app')

@section('content')
    <div class="dashboard-grid dashboard-grid--split">
        <section class="dashboard-card dashboard-panel">
            <div class="dashboard-panel__header">
                <div>
                    <p class="dashboard-panel__eyebrow">General</p>
                    <h3>General Settings</h3>
                </div>
            </div>
            <div class="dashboard-stack">
                <div class="setting-row">
                    <div>
                        <h4>Platform Name</h4>
                        <p>Displayed across dashboard headers and notifications.</p>
                    </div>
                    <input type="text" value="{{ config('app.name', 'English System') }}" />
                </div>
                <div class="setting-row">
                    <div>
                        <h4>Default Language</h4>
                        <p>Set the primary locale for new users and emails.</p>
                    </div>
                    <select>
                        <option>English</option>
                        <option>Arabic</option>
                    </select>
                </div>
            </div>
        </section>

        <section class="dashboard-card dashboard-panel">
            <div class="dashboard-panel__header">
                <div>
                    <p class="dashboard-panel__eyebrow">System</p>
                    <h3>System Settings</h3>
                </div>
            </div>
            <div class="dashboard-stack">
                <div class="setting-row">
                    <div>
                        <h4>Maintenance Mode</h4>
                        <p>Temporarily restrict access during planned operations.</p>
                    </div>
                    <label class="toggle">
                        <input type="checkbox">
                        <span></span>
                    </label>
                </div>
                <div class="setting-row">
                    <div>
                        <h4>Auto Backup</h4>
                        <p>Create nightly backups for database and uploaded media.</p>
                    </div>
                    <label class="toggle">
                        <input type="checkbox" checked>
                        <span></span>
                    </label>
                </div>
            </div>
        </section>
    </div>

    <section class="dashboard-card dashboard-panel">
        <div class="dashboard-panel__header">
            <div>
                <p class="dashboard-panel__eyebrow">Notifications</p>
                <h3>Notification Settings</h3>
            </div>
        </div>
        <div class="dashboard-stack">
            <div class="setting-row">
                <div>
                    <h4>Email Reports</h4>
                    <p>Receive daily and weekly operational reports by email.</p>
                </div>
                <label class="toggle">
                    <input type="checkbox" checked>
                    <span></span>
                </label>
            </div>
            <div class="setting-row">
                <div>
                    <h4>Browser Notifications</h4>
                    <p>Show live alerts for system events and user actions.</p>
                </div>
                <label class="toggle">
                    <input type="checkbox" checked>
                    <span></span>
                </label>
            </div>
        </div>
    </section>
@endsection
