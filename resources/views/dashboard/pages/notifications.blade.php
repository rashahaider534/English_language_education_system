@extends('dashboard.layouts.app')

@section('content')
    <div class="dashboard-grid dashboard-grid--split">
        <section class="dashboard-card dashboard-panel">
            <div class="dashboard-panel__header">
                <div>
                    <p class="dashboard-panel__eyebrow">Alerts</p>
                    <h3>Status Banners</h3>
                </div>
            </div>
            <div class="dashboard-stack">
                <div class="alert alert--success">System backup completed successfully.</div>
                <div class="alert alert--warning">Three reports are waiting for export confirmation.</div>
                <div class="alert alert--danger">Payment gateway latency exceeded the configured threshold.</div>
                <div class="alert alert--info">New release notes are ready for review before deployment.</div>
            </div>
        </section>

        <section class="dashboard-card dashboard-panel">
            <div class="dashboard-panel__header">
                <div>
                    <p class="dashboard-panel__eyebrow">Notification Feed</p>
                    <h3>Recent Messages</h3>
                </div>
            </div>
            <div class="activity-feed">
                @foreach ($feed as $item)
                    <article class="activity-item">
                        <span class="activity-item__dot activity-item__dot--{{ $item['type'] }}"></span>
                        <div>
                            <h4>{{ $item['title'] }}</h4>
                            <p>{{ $item['message'] }}</p>
                            <small>{{ $item['time'] }}</small>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
@endsection
