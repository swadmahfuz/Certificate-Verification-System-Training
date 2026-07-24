@extends('layouts.admin')

@section('title', 'Edit Signatory')

@push('styles')
<style>
        body {
            background-color: #f8f9fa;
            font-size: 13px;
        }

        .container {
            max-width: 900px;
            padding-top: 60px;
            padding-bottom: 40px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .required-label::after {
            content: " *";
            color: red;
        }

        .signature-preview {
            width: 220px;
            height: 90px;
            object-fit: contain;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 5px;
        }
    </style>
@endpush

@section('content')
<section>
    <div class="container">
        <div class="card">

            <div class="card-header">

                <h6 class="text-end">
                    Logged in User:
                    <b>{{ auth()->user()->name }} ({{ auth()->user()->designation }})</b>
                </h6>

                <h3 class="text-center mb-3">
                    Edit Signatory
                </h3>

                <div class="text-center">
                    <a href="{{ route('signatories.index') }}" class="btn btn-primary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Signatories
                    </a>
                </div>

            </div>

            <div class="card-body">

                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Please correct the following:</strong>

                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

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

        </div>
    </div>
</section>
@endsection
