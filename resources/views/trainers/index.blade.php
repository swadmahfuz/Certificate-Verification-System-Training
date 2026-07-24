@extends('layouts.admin')

@section('title', 'Trainers')

@push('styles')
<style>
.signature-image {
    width: 140px;
    height: 55px;
    object-fit: contain;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 3px;
}
</style>
@endpush

@section('content')
<div class="page-heading">
    <div>
        <h1>Trainers</h1>
        <p>Manage trainer profiles and signature images for certificates.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('trainers.create') }}">
        <i class="fa-solid fa-user-plus me-1"></i> Add Trainer
    </a>
</div>

<section class="admin-card">
    <div class="admin-card-header">
        <h2>Trainer Management</h2>
    </div>
    <div class="table-responsive">
        <table class="table table-hover admin-table">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Trainer Name</th>
                    <th>Email</th>
                    <th>Designation</th>
                    <th>Signature</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php $offset = ($trainers->currentPage() - 1) * $trainers->perPage(); @endphp
                @forelse($trainers as $trainer)
                    <tr>
                        <td>{{ $loop->iteration + $offset }}</td>
                        <td><strong>{{ $trainer->name }}</strong></td>
                        <td>{{ $trainer->email }}</td>
                        <td>{{ $trainer->designation }}</td>
                        <td>
                            @if($trainer->signature_path)
                                <a href="{{ route('trainers.signature', $trainer->id) }}" target="_blank" title="View full signature">
                                    <img src="{{ route('trainers.signature', $trainer->id) }}" alt="{{ $trainer->name }} Signature" class="signature-image">
                                </a>
                            @else
                                <span class="text-muted">No signature uploaded</span>
                            @endif
                        </td>
                        <td>
                            @if($trainer->is_active)
                                <span class="status-pill status-success">Active</span>
                            @else
                                <span class="status-pill status-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('trainers.edit', $trainer->id) }}" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                <form method="POST" action="{{ route('trainers.toggleStatus', $trainer->id) }}">
                                    @csrf
                                    <button type="submit" class="{{ $trainer->is_active ? 'danger' : '' }}" title="{{ $trainer->is_active ? 'Deactivate' : 'Activate' }}" data-confirm="Change this trainer status?">
                                        <i class="fa-solid {{ $trainer->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No trainers have been added yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($trainers->hasPages())
        <div class="p-3 border-top">{{ $trainers->links() }}</div>
    @endif
</section>
@endsection
