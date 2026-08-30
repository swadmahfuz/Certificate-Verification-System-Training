@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="page-heading">
    <div>
        <h1>User Management</h1>
        <p>Manage staff accounts and per-app access levels.</p>
    </div>
    @superAdmin
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-user-plus me-1"></i> Add User
    </a>
    @endsuperAdmin
</div>

<section class="admin-card">
    <div class="admin-card-header">
        <h2>All Users</h2>
        <form class="toolbar" method="GET">
            <input class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Search name or email">
            <button class="btn btn-primary btn-sm" type="submit">Search</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Onboarding</th>
                    <th>Permissions</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->departmentRelation?->name ?? 'N/A' }}</td>
                        <td>
                            @if($user->hasVerifiedEmail())
                                <span class="badge bg-success">Verified</span>
                            @else
                                <span class="badge bg-warning text-dark">Unverified</span>
                            @endif
                            @if($user->password_must_change)
                                <span class="badge bg-info text-dark">Password setup</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_super_admin)
                                <span class="badge bg-dark">All apps</span>
                            @else
                                @forelse($user->appPermissions as $permission)
                                    <span class="badge bg-light text-dark">{{ $apps[$permission->app_key] ?? $permission->app_key }}: {{ $permission->access_level }}</span>
                                @empty
                                    <span class="text-muted">None</span>
                                @endforelse
                            @endif
                        </td>
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
                        <td class="text-nowrap">
                            @superAdmin
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            @if(!$user->hasVerifiedEmail())
                                <form action="{{ route('admin.users.resend-verification', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Resend verify</button>
                                </form>
                            @endif
                            <form action="{{ route('admin.users.send-password-reset', $user) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning">Reset pwd</button>
                            </form>
                            @else
                            <span class="text-muted small">View only</span>
                            @endsuperAdmin
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">{{ $users->links() }}</div>
</section>
@endsection
