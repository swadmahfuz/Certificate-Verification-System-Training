@extends('layouts.admin')

@section('title', 'Departments')

@section('content')
<div class="page-heading">
    <div>
        <h1>Departments</h1>
        <p>Manage departments available during registration and user creation.</p>
    </div>
</div>

<section class="admin-card mb-4">
    <div class="admin-card-header"><h2>Add Department</h2></div>
    <div class="admin-card-body">
        <form method="POST" action="{{ route('admin.departments.store') }}" class="row g-3">
            @csrf
            <div class="col-md-8">
                <label class="form-label" for="department-name">Department name</label>
                <input id="department-name" type="text" name="name" class="form-control" required maxlength="255">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Add Department</button>
            </div>
        </form>
    </div>
</section>

<section class="admin-card">
    <div class="admin-card-header"><h2>All Departments</h2></div>
    <div class="table-responsive">
        <table class="table table-hover admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $department)
                    <tr>
                        <td>
                            <form method="POST" action="{{ route('admin.departments.update', $department) }}" class="d-flex gap-2">
                                @csrf
                                <input type="text" name="name" value="{{ $department->name }}" class="form-control form-control-sm" required maxlength="255">
                                <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                            </form>
                        </td>
                        <td>
                            @if($department->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.departments.toggle', $department) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    {{ $department->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">No departments configured yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
