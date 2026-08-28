@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="page-heading">
    <div>
        <h1>Edit User</h1>
        <p>{{ $user->name }} ({{ $user->email }})</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Back to Users</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<section class="admin-card">
    <div class="admin-card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label" for="name">Full name</label>
                    <input id="name" type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="department_id">Department</label>
                    <select id="department_id" name="department_id" class="form-select" required>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="designation">Designation</label>
                    <input id="designation" type="text" name="designation" class="form-control" value="{{ old('designation', $user->designation) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="password">New password</label>
                    <input id="password" type="password" name="password" class="form-control">
                    <div class="form-text">Leave blank to keep the current password.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="password_confirmation">Confirm new password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            <div class="mb-4">
                <input type="hidden" name="is_active" value="0">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Account active (user can log in)</label>
                </div>
            </div>

            @if($user->id === auth()->id() || !$user->is_super_admin)
                <input type="hidden" name="is_super_admin" value="0">
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="is_super_admin" value="1" id="is_super_admin" {{ old('is_super_admin', $user->is_super_admin) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_super_admin">Super Admin (full access to all apps and administration)</label>
                </div>
            @elseif($user->is_super_admin)
                <p class="text-muted mb-4">This user is a Super Admin. Profile can be edited; role and permissions are locked.</p>
            @endif

            @if(!$user->is_super_admin || ($user->id === auth()->id() && !old('is_super_admin', $user->is_super_admin)))
                <h2 class="h5 mb-3">App permissions</h2>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Application</th>
                                <th>No access</th>
                                <th>View only</th>
                                <th>Full access</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apps as $appKey => $appLabel)
                                @php $level = old('permissions.' . $appKey, $current[$appKey] ?? ''); @endphp
                                <tr>
                                    <td>{{ $appLabel }}</td>
                                    <td class="text-center">
                                        <input type="radio" name="permissions[{{ $appKey }}]" value="" {{ $level === '' ? 'checked' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <input type="radio" name="permissions[{{ $appKey }}]" value="view" {{ $level === 'view' ? 'checked' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <input type="radio" name="permissions[{{ $appKey }}]" value="full" {{ $level === 'full' ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.users.send-password-reset', $user) }}" class="mt-3" onsubmit="return confirm('Send a password reset email to {{ $user->email }}?');">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm">Send password reset email</button>
        </form>
    </div>
</section>
@endsection
