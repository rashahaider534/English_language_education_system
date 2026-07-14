@extends('dashboard.layouts.app')

@section('content')
    <div class="dashboard-grid dashboard-grid--stats">
        @foreach ($reportCards as $card)
            <article class="dashboard-card stat-card">
                <div class="stat-card__label">{{ $card['label'] }}</div>
                <div class="stat-card__value">{{ $card['value'] }}</div>
                <div class="stat-card__hint">{{ $card['detail'] }}</div>
            </article>
        @endforeach
    </div>

    <section class="dashboard-card dashboard-panel">
        <div class="dashboard-toolbar">
            <div>
                <h3>Report Filters</h3>
                <p class="dashboard-panel__text">Presentation-only controls for the dashboard template.</p>
            </div>
            <div class="dashboard-toolbar__actions">
                <button type="button" class="dashboard-button dashboard-button--ghost">Export PDF</button>
                <button type="button" class="dashboard-button">Export CSV</button>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-field">
                <label>Report Type</label>
                <select>
                    <option>Engagement</option>
                    <option>Revenue</option>
                    <option>Progress</option>
                </select>
            </div>
            <div class="form-field">
                <label>Date Range</label>
                <input type="text" value="Last 30 days" />
            </div>
            <div class="form-field">
                <label>Department</label>
                <select>
                    <option>All Departments</option>
                    <option>Academic</option>
                    <option>Finance</option>
                </select>
            </div>
        </div>
    </section>

    <section class="dashboard-card dashboard-panel">
        <div class="dashboard-panel__header">
            <div>
                <p class="dashboard-panel__eyebrow">Generated Reports</p>
                <h3>Available Exports</h3>
            </div>
        </div>
        <div class="table-wrap">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Report</th>
                        <th>Owner</th>
                        <th>Updated</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $report)
                        <tr>
                            <td>{{ $report['name'] }}</td>
                            <td>{{ $report['owner'] }}</td>
                            <td>{{ $report['updated'] }}</td>
                            <td><span class="dashboard-badge {{ strtolower($report['status']) === 'ready' ? 'dashboard-badge--success' : (strtolower($report['status']) === 'processing' ? 'dashboard-badge--warning' : 'dashboard-badge--default') }}">{{ $report['status'] }}</span></td>
                            <td class="text-right"><button type="button" class="dashboard-button dashboard-button--ghost">Open</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
