@extends('layouts.admin')

@section('title', 'Add User')

@section('content')
<div class="page-heading">
    <div>
        <h1>Add User</h1>
        <p>Create a new staff account. The user will receive a verification email, then a password setup link after they verify.</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Back to Users</a>
</div>

<section class="admin-card">
    <div class="admin-card-body">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label" for="name">Full name</label>
                    <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="department_id">Department</label>
                    <select id="department_id" name="department_id" class="form-select" required>
                        <option value="">Select department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="designation">Designation</label>
                    <input id="designation" type="text" name="designation" class="form-control" value="{{ old('designation') }}" required>
                </div>
            </div>

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
                            <tr>
                                <td>{{ $appLabel }}</td>
                                <td class="text-center">
                                    <input type="radio" name="permissions[{{ $appKey }}]" value="" {{ (old('permissions.' . $appKey) === null || old('permissions.' . $appKey) === '') ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input type="radio" name="permissions[{{ $appKey }}]" value="view" {{ old('permissions.' . $appKey) === 'view' ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input type="radio" name="permissions[{{ $appKey }}]" value="full" {{ old('permissions.' . $appKey) === 'full' ? 'checked' : '' }}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary">Create User</button>
        </form>
    </div>
</section>
@endsection
