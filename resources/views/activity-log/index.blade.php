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
            <button class="btn btn-primary btn-sm" type="submit">Filter</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Event</th>
                    <th>Description</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $activity)
                    <tr>
                        <td>{{ $activity->created_at->format('d M Y, h:i A') }}</td>
                        <td>{{ $activity->causer_name ?: 'System' }}</td>
                        <td><span class="status-pill status-secondary">{{ $activity->event }}</span></td>
                        <td>{{ $activity->description }}</td>
                        <td>{{ $activity->ip_address ?: 'N/A' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No activity has been recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">{{ $activities->links() }}</div>
</section>
@endsection
