@extends('layouts.admin')

@section('title', 'Edit Trainer')

@push('styles')
<style>
.required-label::after { content: " *"; color: red; }
.signature-preview { width: 220px; height: 90px; object-fit: contain; background: #fff; border: 1px solid #dee2e6; border-radius: 5px; padding: 5px; }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div>
        <h1>Edit Trainer</h1>
        <p>Update trainer profile or signature image.</p>
    </div>
    <a class="btn btn-outline-primary btn-sm" href="{{ route('trainers.index') }}">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Trainers
    </a>
</div>

<section class="admin-card">
    <div class="admin-card-header"><h2>Details</h2></div>
    <div class="admin-card-body">
<form
                    method="POST"
                    action="{{ route('trainers.update', $trainer->id) }}"
                    enctype="multipart/form-data"
                >
                    @csrf

                    <div class="mb-3">
                        <label
                            for="name"
                            class="form-label required-label"
                        >
                            Trainer Name
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $trainer->name) }}"
                            maxlength="255"
                            required
                            autofocus
                        >
                    </div>

                    <div class="mb-3">
                        <label
                            for="email"
                            class="form-label required-label"
                        >
                            Email Address
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', $trainer->email) }}"
                            maxlength="255"
                            required
                        >

                        <small class="text-muted">
                            This email is used to identify the trainer during
                            bulk certificate import.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label
                            for="designation"
                            class="form-label required-label"
                        >
                            Designation
                        </label>

                        <input
                            type="text"
                            id="designation"
                            name="designation"
                            class="form-control"
                            value="{{ old(
                                'designation',
                                $trainer->designation
                            ) }}"
                            maxlength="255"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Current Signature
                        </label>

                        <div>
                            @if($trainer->signature_path)
                                <a
                                    href="{{ route(
                                        'trainers.signature',
                                        $trainer->id
                                    ) }}"
                                    target="_blank"
                                >
                                    <img
                                        src="{{ route(
                                            'trainers.signature',
                                            $trainer->id
                                        ) }}"
                                        alt="{{ $trainer->name }} Signature"
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
                        <label
                            for="signature"
                            class="form-label"
                        >
                            Replace Signature Image
                        </label>

                        <input
                            type="file"
                            id="signature"
                            name="signature"
                            class="form-control"
                            accept=".png,.jpg,.jpeg,.webp"
                        >

                        <small class="text-muted">
                            Leave this empty to retain the current signature.
                            Maximum file size: 2 MB.
                        </small>
                    </div>

                    <div class="form-check mb-4">
                        <input
                            type="checkbox"
                            id="is_active"
                            name="is_active"
                            value="1"
                            class="form-check-input"
                            {{
                                old(
                                    'is_active',
                                    $trainer->is_active
                                )
                                    ? 'checked'
                                    : ''
                            }}
                        >

                        <label
                            for="is_active"
                            class="form-check-label"
                        >
                            Active Trainer
                        </label>
                    </div>

                    <div class="text-center">

                        <button
                            type="submit"
                            class="btn btn-success"
                        >
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            Update Trainer
                        </button>

                        <a
                            href="{{ route('trainers.index') }}"
                            class="btn btn-secondary ms-2"
                        >
                            Cancel
                        </a>

                    </div>

                </form>
    </div>
</section>
@endsection