@extends('layouts.admin')

@section('title', 'Add Signatory')

@push('styles')
<style>
.required-label::after { content: " *"; color: red; }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div>
        <h1>Add Signatory</h1>
        <p>Register a signatory and upload their signature image.</p>
    </div>
    <a class="btn btn-outline-primary btn-sm" href="{{ route('signatories.index') }}">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Signatories
    </a>
</div>

<section class="admin-card">
    <div class="admin-card-header"><h2>Details</h2></div>
    <div class="admin-card-body">
<form method="POST" action="{{ route('signatories.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label required-label">
                            Signatory Name
                        </label>

                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" maxlength="255" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label required-label">
                            Email Address
                        </label>

                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" maxlength="255" required>

                        <small class="text-muted">
                            This email will be used to identify the signatory during bulk certificate import.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="designation" class="form-label required-label">
                            Designation
                        </label>

                        <input type="text" id="designation" name="designation" class="form-control" value="{{ old('designation') }}" maxlength="255" required>
                    </div>

                    <div class="mb-3">
                        <label for="department" class="form-label">
                            Department
                        </label>

                        <input type="text" id="department" name="department" class="form-control" value="{{ old('department') }}" maxlength="255">

                        <small class="text-muted">
                            Example: Business Assurance & Training
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="signature" class="form-label required-label">
                            Signature Image
                        </label>

                        <input type="file" id="signature" name="signature" class="form-control" accept=".png,.jpg,.jpeg,.webp" required>

                        <small class="text-muted">
                            Recommended: transparent PNG with a clean signature. Maximum file size: 2 MB.
                        </small>
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" {{ old('is_active', 1) ? 'checked' : '' }}>

                        <label for="is_active" class="form-check-label">
                            Active Signatory
                        </label>
                    </div>

                    <div class="text-center">

                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save Signatory
                        </button>

                        <a href="{{ route('signatories.index') }}" class="btn btn-secondary ms-2">
                            Cancel
                        </a>

                    </div>

                </form>
    </div>
</section>
@endsection