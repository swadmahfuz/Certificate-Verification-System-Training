@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="page-heading">
    <div>
        <h1>Users</h1>
        <p>Registered staff and their certificate activity summary.</p>
    </div>
</div>

<section class="admin-card">
    <div class="admin-card-header">
        <h2>All Registered Users</h2>
    </div>
    <div class="table-responsive">
        <table class="table table-hover admin-table">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Total Created</th>
                    <th>Total Reviewed</th>
                    <th>Total Approved</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->departmentRelation?->name ?? 'N/A' }}</td>
                        <td>{{ $user->designation ?? 'N/A' }}</td>
                        <td>{{ $user->certificates_created_count }}</td>
                        <td>{{ $user->certificates_reviewed_count }}</td>
                        <td>{{ $user->certificates_approved_count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
