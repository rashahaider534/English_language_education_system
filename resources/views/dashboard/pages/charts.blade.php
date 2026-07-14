@extends('dashboard.layouts.app')

@section('content')
    <div class="dashboard-grid dashboard-grid--split">
        <section class="dashboard-card dashboard-panel">
            <div class="dashboard-panel__header">
                <div>
                    <p class="dashboard-panel__eyebrow">Bar Chart</p>
                    <h3>Monthly Engagement</h3>
                </div>
            </div>
            <div class="bars-chart">
                @foreach ($engagementBars as $item)
                    <div class="bars-chart__item">
                        <span class="bars-chart__bar" style="height: {{ $item['value'] }}%"></span>
                        <small>{{ $item['label'] }}</small>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="dashboard-card dashboard-panel">
            <div class="dashboard-panel__header">
                <div>
                    <p class="dashboard-panel__eyebrow">Distribution</p>
                    <h3>User Segments</h3>
                </div>
            </div>
            <div class="donut-chart">
                <div class="donut-chart__visual"></div>
                <div class="dashboard-stack">
                    @foreach ($distribution as $item)
                        <div class="chart-legend">
                            <span class="chart-legend__swatch {{ $item['color'] }}"></span>
                            <span>{{ $item['label'] }}</span>
                            <strong>{{ $item['value'] }}%</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection
