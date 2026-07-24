@extends('layouts.admin')

@section('title', 'Edit Signatory')

@push('styles')
<style>
.required-label::after { content: " *"; color: red; }
.signature-preview { width: 220px; height: 90px; object-fit: contain; background: #fff; border: 1px solid #dee2e6; border-radius: 5px; padding: 5px; }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div>
        <h1>Edit Signatory</h1>
        <p>Update signatory profile or signature image.</p>
    </div>
    <a class="btn btn-outline-primary btn-sm" href="{{ route('signatories.index') }}">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Signatories
    </a>
</div>

<section class="admin-card">
    <div class="admin-card-header"><h2>Details</h2></div>
    <div class="admin-card-body">
<form method="POST" action="{{ route('signatories.update', $signatory->id) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label required-label">
                            Signatory Name
                        </label>

                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $signatory->name) }}" maxlength="255" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label required-label">
                            Email Address
                        </label>

                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $signatory->email) }}" maxlength="255" required>

                        <small class="text-muted">
                            This email is used to identify the signatory during bulk certificate import.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="designation" class="form-label required-label">
                            Designation
                        </label>

                        <input type="text" id="designation" name="designation" class="form-control" value="{{ old('designation', $signatory->designation) }}" maxlength="255" required>
                    </div>

                    <div class="mb-3">
                        <label for="department" class="form-label">
                            Department
                        </label>

                        <input type="text" id="department" name="department" class="form-control" value="{{ old('department', $signatory->department) }}" maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Current Signature
                        </label>

                        <div>
                            @if($signatory->signature_path)
                                <a href="{{ route('signatories.signature', $signatory->id) }}" target="_blank">
                                    <img
                                        src="{{ route('signatories.signature', $signatory->id) }}"
                                        alt="{{ $signatory->name }} Signature"
                                        class="signature-preview"
                                    >
                                </a>
                            @else
                                <p class="text-muted mb-0">
                                    No signature is currently available.
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="signature" class="form-label">
                            Replace Signature Image
                        </label>

                        <input type="file" id="signature" name="signature" class="form-control" accept=".png,.jpg,.jpeg,.webp">

                        <small class="text-muted">
                            Leave this empty to retain the current signature. Maximum file size: 2 MB.
                        </small>
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" {{ old('is_active', $signatory->is_active) ? 'checked' : '' }}>

                        <label for="is_active" class="form-check-label">
                            Active Signatory
                        </label>
                    </div>

                    <div class="text-center">

                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Update Signatory
                        </button>

                        <a href="{{ route('signatories.index') }}" class="btn btn-secondary ms-2">
                            Cancel
                        </a>

                    </div>

                </form>
    </div>
</section>
@endsection