@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="page-heading">
    <div>
        <h1>User Management</h1>
        <p>Manage staff accounts and per-app access levels.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-user-plus me-1"></i> Add User
    </a>
</div>

<section class="admin-card">
    <div class="admin-card-header"><h2>All Users</h2></div>
    <div class="table-responsive">
        <table class="table table-hover admin-table">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Reviewed</th>
                    <th>Approved</th>
                    <th>Actions</th>
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
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_super_admin)
                                <span class="badge bg-dark">Super Admin</span>
                            @else
                                <span class="badge bg-light text-dark">Standard</span>
                            @endif
                        </td>
                        <td>{{ $user->certificates_created_count }}</td>
                        <td>{{ $user->certificates_reviewed_count }}</td>
                        <td>{{ $user->certificates_approved_count }}</td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="text-center text-muted py-4">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
