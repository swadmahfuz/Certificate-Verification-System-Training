@extends('layouts.admin')

@section('title', 'Signatories')

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
        <h1>Signatories</h1>
        <p>Manage authorizing signatories and signature images.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('signatories.create') }}">
        <i class="fa-solid fa-user-plus me-1"></i> Add Signatory
    </a>
</div>

<section class="admin-card">
    <div class="admin-card-header">
        <h2>Signatory Management</h2>
    </div>
    <div class="table-responsive">
        <table class="table table-hover admin-table">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Signatory Name</th>
                    <th>Email</th>
                    <th>Designation</th>
                    <th>Department</th>
                    <th>Signature</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php $offset = ($signatories->currentPage() - 1) * $signatories->perPage(); @endphp
                @forelse($signatories as $signatory)
                    <tr>
                        <td>{{ $loop->iteration + $offset }}</td>
                        <td><strong>{{ $signatory->name }}</strong></td>
                        <td>{{ $signatory->email }}</td>
                        <td>{{ $signatory->designation }}</td>
                        <td>{{ $signatory->department ?: 'N/A' }}</td>
                        <td>
                            @if($signatory->signature_path)
                                <a href="{{ route('signatories.signature', $signatory->id) }}" target="_blank">
                                    <img src="{{ route('signatories.signature', $signatory->id) }}" alt="{{ $signatory->name }} Signature" class="signature-image">
                                </a>
                            @else
                                <span class="text-muted">No signature uploaded</span>
                            @endif
                        </td>
                        <td>
                            @if($signatory->is_active)
                                <span class="status-pill status-success">Active</span>
                            @else
                                <span class="status-pill status-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('signatories.edit', $signatory->id) }}" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                <form method="POST" action="{{ route('signatories.toggleStatus', $signatory->id) }}">
                                    @csrf
                                    <button type="submit" class="{{ $signatory->is_active ? 'danger' : '' }}" title="{{ $signatory->is_active ? 'Deactivate' : 'Activate' }}" data-confirm="Change this signatory status?">
                                        <i class="fa-solid {{ $signatory->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No signatories have been added yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($signatories->hasPages())
        <div class="p-3 border-top">{{ $signatories->links() }}</div>
    @endif
</section>
@endsection
