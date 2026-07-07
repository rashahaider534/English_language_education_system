@extends('dashboard.layouts.app')

@section('content')
    <div class="dashboard-grid dashboard-grid--cards">
        <article class="dashboard-card feature-card">
            <span class="dashboard-badge dashboard-badge--success">Performance</span>
            <h3>Growth Snapshot</h3>
            <p>Monthly engagement is trending upward with stronger completion on structured learning paths.</p>
            <div class="feature-card__footer">
                <strong>+24%</strong>
                <span>vs previous period</span>
            </div>
        </article>

        <article class="dashboard-card feature-card">
            <span class="dashboard-badge dashboard-badge--warning">Attention</span>
            <h3>Review Queue</h3>
            <p>Eight new course submissions are waiting for publishing review and quality checks.</p>
            <div class="feature-card__footer">
                <strong>8 items</strong>
                <span>Needs moderation</span>
            </div>
        </article>

        <article class="dashboard-card feature-card">
            <span class="dashboard-badge dashboard-badge--info">Scheduling</span>
            <h3>Upcoming Sessions</h3>
            <p>Live speaking sessions remain evenly distributed across afternoon and evening timeslots.</p>
            <div class="feature-card__footer">
                <strong>26 today</strong>
                <span>Across 4 instructors</span>
            </div>
        </article>
    </div>

    <section class="dashboard-card dashboard-panel">
        <div class="dashboard-panel__header">
            <div>
                <p class="dashboard-panel__eyebrow">Examples</p>
                <h3>Card Variations</h3>
            </div>
        </div>
        <div class="dashboard-grid dashboard-grid--split">
            <div class="sub-card">
                <h4>Announcement</h4>
                <p>The new placement test flow is ready for internal QA and curriculum review.</p>
                <button type="button" class="dashboard-button dashboard-button--ghost">Read More</button>
            </div>
            <div class="sub-card">
                <h4>Operational Checklist</h4>
                <div class="dashboard-stack">
                    <label><input type="checkbox" checked> Validate lesson imports</label>
                    <label><input type="checkbox" checked> Review weekly metrics</label>
                    <label><input type="checkbox"> Approve pending teachers</label>
                </div>
            </div>
        </div>
    </section>
@endsection
