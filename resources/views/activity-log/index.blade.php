@extends('layouts.admin')

@section('title', 'Activity Log')

@section('content')
<div class="page-heading">
    <div>
        <h1>Activity Log</h1>
        <p>Review important changes made throughout the Training CVS.</p>
    </div>
</div>

<section class="admin-card">
    <div class="admin-card-header">
        <h2>Recorded Activities</h2>
        <form class="toolbar" method="GET">
            <input class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Search activity">
            <select class="form-select form-select-sm" name="event">
                <option value="">All events</option>
                @foreach($eventTypes as $eventType)
                    <option value="{{ $eventType }}" {{ request('event') === $eventType ? 'selected' : '' }}>{{ $eventType }}</option>
                @endforeach
            </select>
            <select class="form-select form-select-sm" name="source_app">
                <option value="">All apps</option>
                @foreach($apps as $appKey => $appLabel)
                    <option value="{{ $appKey }}" {{ request('source_app') === $appKey ? 'selected' : '' }}>{{ $appLabel }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary btn-sm" type="submit">Filter</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Source</th>
                    <th>Event</th>
                    <th>Description</th>
                    <th>Subject</th>
                    <th>IP Address</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $activity)
                    @php
                        $sourceApp = $activity->properties['source_app'] ?? null;
                        $sourceLabel = $sourceApp ? ($apps[$sourceApp] ?? $sourceApp) : 'N/A';
                        $subjectLink = ($activity->subject_type === 'certificate' && $activity->subject_id)
                            ? route('certificate.view', $activity->subject_id)
                            : null;
                    @endphp
                    <tr>
                        <td>{{ $activity->created_at->format('d M Y, h:i A') }}</td>
                        <td>{{ $activity->causer_name ?: 'System' }}</td>
                        <td><span class="badge bg-light text-dark">{{ $sourceLabel }}</span></td>
                        <td><span class="status-pill status-secondary">{{ $activity->event }}</span></td>
                        <td>{{ $activity->description }}</td>
                        <td>
                            @if($subjectLink)
                                <a href="{{ $subjectLink }}" target="_blank" rel="noopener noreferrer">#{{ $activity->subject_id }}</a>
                            @else
                                {{ $activity->subject_id ?: '—' }}
                            @endif
                        </td>
                        <td>{{ $activity->ip_address ?: 'N/A' }}</td>
                        <td>
                            @if(!empty($activity->properties))
                                <button class="btn btn-sm btn-outline-secondary" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#activity-props-{{ $activity->id }}">
                                    Details
                                </button>
                            @endif
                        </td>
                    </tr>
                    @if(!empty($activity->properties))
                        <tr class="collapse" id="activity-props-{{ $activity->id }}">
                            <td colspan="8">
                                <pre class="small mb-0">{{ json_encode($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No activity has been recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">{{ $activities->links() }}</div>
</section>
@endsection
