@extends('dashboard.layouts.app')

@section('content')
    <section class="dashboard-card dashboard-panel">
        <div class="dashboard-panel__header">
            <div>
                <p class="dashboard-panel__eyebrow">Primary Table</p>
                <h3>Courses Overview</h3>
            </div>
        </div>
        <div class="table-wrap">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Level</th>
                        <th>Students</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courses as $course)
                        <tr>
                            <td>{{ $course['course'] }}</td>
                            <td>{{ $course['level'] }}</td>
                            <td>{{ $course['students'] }}</td>
                            <td><span class="dashboard-badge {{ strtolower($course['status']) === 'live' ? 'dashboard-badge--success' : (strtolower($course['status']) === 'draft' ? 'dashboard-badge--warning' : 'dashboard-badge--default') }}">{{ $course['status'] }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="dashboard-card dashboard-panel">
        <div class="dashboard-panel__header">
            <div>
                <p class="dashboard-panel__eyebrow">Compact Table</p>
                <h3>Recent Payments</h3>
            </div>
        </div>
        <div class="table-wrap">
            <table class="dashboard-table dashboard-table--compact">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Student</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>{{ $payment['invoice'] }}</td>
                            <td>{{ $payment['student'] }}</td>
                            <td>{{ $payment['amount'] }}</td>
                            <td>{{ $payment['method'] }}</td>
                            <td><span class="dashboard-badge {{ strtolower($payment['status']) === 'paid' ? 'dashboard-badge--success' : (strtolower($payment['status']) === 'pending' ? 'dashboard-badge--warning' : 'dashboard-badge--danger') }}">{{ $payment['status'] }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
