@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-heading">
    <div>
        <h1>Dashboard</h1>
        <p>Welcome back, {{ auth()->user()->name }}.</p>
    </div>
    @canMutate
    <a class="btn btn-primary btn-sm" href="{{ route('certificate.createForm') }}">
        <i class="fa-solid fa-plus me-1"></i> Add Certificate
    </a>
    @endcanMutate
</div>

@if(($myAssignments['total'] ?? 0) > 0)
    <div class="assignment-banner">
        <div>
            <strong>You have {{ $myAssignments['total'] }} certificate{{ $myAssignments['total'] === 1 ? '' : 's' }} waiting on you.</strong>
            <span>{{ $myAssignments['review'] }} for review · {{ $myAssignments['approval'] }} for approval</span>
        </div>
        <a class="btn btn-sm btn-primary" href="{{ route('pendingCertificates', ['assignment' => 'mine']) }}">
            Open my assignments
        </a>
    </div>
@endif

<div class="stats-grid">
    <x-admin.stat-card label="Total Certificates" :value="$stats['total']" icon="fa-file-circle-check" color="blue" meta="Active records" />
    <x-admin.stat-card label="Approved Certificates" :value="$stats['approved']" icon="fa-check" color="green" :meta="$percentages['Approved'].'% of total'" />
    <x-admin.stat-card
        label="Pending review (all)"
        :value="$stats['pending_review']"
        icon="fa-clock"
        color="orange"
        meta="Organization-wide"
        :href="route('pendingCertificates')"
    />
    <x-admin.stat-card
        label="Pending approval (all)"
        :value="$stats['pending_approval']"
        icon="fa-pen"
        color="purple"
        meta="Organization-wide"
        :href="route('pendingCertificates')"
    />
    <x-admin.stat-card
        label="Pending my review"
        :value="$myAssignments['review']"
        icon="fa-clock"
        color="orange"
        meta="Assigned to you"
        :href="route('pendingCertificates', ['assignment' => 'review'])"
    />
    <x-admin.stat-card
        label="Pending my approval"
        :value="$myAssignments['approval']"
        icon="fa-pen"
        color="purple"
        meta="Assigned to you"
        :href="route('pendingCertificates', ['assignment' => 'approval'])"
    />
    <x-admin.stat-card label="Expired Certificates" :value="$stats['expired']" icon="fa-circle-xmark" color="red" :meta="$percentages['Expired'].'% of total'" />
    <x-admin.stat-card label="Active Trainers" :value="$stats['active_trainers']" icon="fa-users" color="cyan" meta="Available trainers" />
</div>

<div class="dashboard-grid">
    <section class="admin-card">
        <div class="admin-card-header">
            <h2>Certificates by Status</h2>
        </div>
        <div class="admin-card-body chart-wrap">
            <canvas id="statusChart"></canvas>
        </div>
    </section>

    <section class="admin-card">
        <div class="admin-card-header">
            <h2>Certificates Issued Over Time</h2>
            <span class="small text-muted">Last 12 months</span>
        </div>
        <div class="admin-card-body chart-wrap">
            <canvas id="monthlyChart"></canvas>
        </div>
    </section>
</div>

<div class="dashboard-bottom">
    <section class="admin-card">
        <div class="admin-card-header">
            <h2>Expiring Soon</h2>
            <span class="small text-muted">Next 30 days</span>
        </div>
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>Certificate</th>
                        <th>Participant</th>
                        <th>Expiry</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expiringSoon as $certificate)
                        <tr>
                            <td><a href="{{ route('certificate.view', $certificate->id) }}">{{ $certificate->certificate_number }}</a></td>
                            <td>{{ $certificate->participant_name }}</td>
                            <td>{{ $certificate->expiry_date ? \Carbon\Carbon::parse($certificate->expiry_date)->format('d M Y') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">No certificates expiring in the next 30 days.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="admin-card">
        <div class="admin-card-header">
            <h2>Recent Certificates</h2>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('certificates.index') }}">View all</a>
        </div>
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>Certificate Number</th>
                        <th>Participant</th>
                        <th>Status</th>
                        <th>Issue Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCertificates as $certificate)
                        <tr>
                            <td><a href="{{ route('certificate.view', $certificate->id) }}">{{ $certificate->certificate_number }}</a></td>
                            <td>{{ $certificate->participant_name }}</td>
                            <td><x-admin.status-badge :status="$certificate->status" /></td>
                            <td>{{ $certificate->issue_date ? \Carbon\Carbon::parse($certificate->issue_date)->format('d M Y') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No certificates available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="admin-card">
        <div class="admin-card-header">
            <h2>Recent Activities</h2>
            @superAdmin
            <a class="btn btn-outline-primary btn-sm" href="{{ route('activity-log.index') }}">View all</a>
            @endsuperAdmin
        </div>
        <div class="admin-card-body">
            <ul class="activity-list">
                @forelse($recentActivities as $activity)
                    @php
                        $sourceApp = $activity->properties['source_app'] ?? null;
                        $apps = config('cvs.apps', []);
                        $sourceLabel = $sourceApp ? ($apps[$sourceApp] ?? $sourceApp) : null;
                    @endphp
                    <li class="activity-item">
                        <span class="activity-dot"><i class="fa-solid fa-clock-rotate-left"></i></span>
                        <div class="activity-text">
                            <p>{{ $activity->description }}</p>
                            <time>{{ $activity->created_at->diffForHumans() }}@if($sourceLabel) · {{ $sourceLabel }}@endif</time>
                        </div>
                    </li>
                @empty
                    <li class="text-center text-muted py-4">Activity will appear here as actions are recorded.</li>
                @endforelse
            </ul>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: @json($statusChart['labels']),
            datasets: [{
                data: @json($statusChart['values']),
                backgroundColor: ['#19aa71', '#ff9800', '#7449bd', '#e83b4f'],
                borderWidth: 0
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '66%',
            plugins: { legend: { position: 'right', labels: { boxWidth: 10, font: { size: 10 } } } }
        }
    });

    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: @json($monthlyChart['labels']),
            datasets: [{
                label: 'Certificates',
                data: @json($monthlyChart['values']),
                borderColor: '#1976d2',
                backgroundColor: 'rgba(25, 118, 210, .10)',
                fill: true,
                tension: .3,
                pointRadius: 3
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } }
        }
    });
});
</script>
@endpush
