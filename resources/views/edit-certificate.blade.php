<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>TÜV Austria BIC CVS | Edit Certificate Details</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <style>
        body {
            font-size: 13px;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .btn i {
            font-size: 14px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #f4f4f4;
            padding: 20px;
        }

        label {
            font-weight: 600;
        }

        .training-options {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px 18px;
        }

        .training-options .form-check-label {
            font-weight: 500;
        }
    </style>
</head>

<body background="../images/tuv-login-background1.jpg">

<section class="pt-5">
    <div class="container">
        <div class="card">

            <div class="card-header text-center">
                <h3>Edit Certificate Information</h3>

                <div class="d-flex justify-content-center gap-2 flex-wrap mt-3">
                    <a href="../dashboard" class="btn btn-primary">
                        <i class="fa-solid fa-arrow-left me-1"></i>
                        Go back to Dashboard
                    </a>

                    <a href="../delete-certificate/{{ $certificate->id }}" class="btn btn-danger">
                        <i class="fa-solid fa-trash me-1"></i>
                        Delete Certificate
                    </a>
                </div>

                <p class="text-end mt-2 mb-0" style="font-style: italic;">
                    * Required fields
                </p>
            </div>

            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('certificate.update') }}" method="POST">
                    @csrf

                    <input
                        type="hidden"
                        name="id"
                        value="{{ $certificate->id }}"
                    >

                    @foreach([
                        'certificate_number' => 'Certificate Number',
                        'participant_name' => 'Participant Name',
                        'passport_nid' => 'NID/Passport Number',
                        'driving_license' => 'Driving License',
                        'company' => 'Company',
                        'training_name' => 'Training Name',
                        'location' => 'Training Location',
                        'training_date' => 'Training Start Date',
                        'training_end' => 'Training End Date',
                        'issue_date' => 'Issue Date',
                        'expiry_date' => 'Expiry Date'
                    ] as $field => $label)

                        <div class="mb-3">
                            <label for="{{ $field }}">
                                {{ $label }}

                                @if(in_array($field, [
                                    'certificate_number',
                                    'participant_name',
                                    'passport_nid',
                                    'training_name',
                                    'location',
                                    'training_date',
                                    'training_end',
                                    'issue_date'
                                ]))
                                    *
                                @endif
                            </label>

                            @error($field)
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                            @enderror

                            <input
                                type="{{ in_array($field, [
                                    'training_date',
                                    'training_end',
                                    'issue_date',
                                    'expiry_date'
                                ]) ? 'date' : 'text' }}"
                                name="{{ $field }}"
                                id="{{ $field }}"
                                class="form-control"
                                value="{{ old($field, $certificate->$field) }}"
                                placeholder="Enter {{ $label }}"
                            >
                        </div>

                        @if($field == 'certificate_number')
                            <div class="mb-3">
                                <label for="certificate_type">
                                    Certificate Type *
                                </label>

                                @error('certificate_type')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <select
                                    name="certificate_type"
                                    id="certificate_type"
                                    class="form-control"
                                    required
                                >
                                    <option value="">
                                        Select Certificate Type
                                    </option>

                                    <option
                                        value="Certificate"
                                        {{ old('certificate_type', $certificate->certificate_type) == 'Certificate' ? 'selected' : '' }}
                                    >
                                        Certificate
                                    </option>

                                    <option
                                        value="Certificate of Achievement"
                                        {{ old('certificate_type', $certificate->certificate_type) == 'Certificate of Achievement' ? 'selected' : '' }}
                                    >
                                        Certificate of Achievement
                                    </option>

                                    <option
                                        value="Certificate of Competency"
                                        {{ old('certificate_type', $certificate->certificate_type) == 'Certificate of Competency' ? 'selected' : '' }}
                                    >
                                        Certificate of Competency
                                    </option>

                                    <option
                                        value="Certificate of Attendance"
                                        {{ old('certificate_type', $certificate->certificate_type) == 'Certificate of Attendance' ? 'selected' : '' }}
                                    >
                                        Certificate of Attendance
                                    </option>
                                </select>
                            </div>
                        @endif

                        @if($field == 'training_name')
                            <div class="mb-3">
                                <label class="mb-2">
                                    Training Classification
                                </label>

                                @error('has_practical')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror

                                @error('is_refresher')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror

                                @error('internal_audit_training')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror

                                @error('online_training')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div class="training-options">

                                    <div class="form-check mb-2">
                                        <input
                                            type="hidden"
                                            name="has_practical"
                                            value="0"
                                        >

                                        <input
                                            type="checkbox"
                                            name="has_practical"
                                            id="has_practical"
                                            class="form-check-input"
                                            value="1"
                                            {{ old('has_practical', $certificate->has_practical) ? 'checked' : '' }}
                                        >

                                        <label
                                            for="has_practical"
                                            class="form-check-label"
                                        >
                                            Includes Practical Sessions
                                        </label>

                                        <div class="text-muted small">
                                            Select this when practical sessions were included in the training program.
                                        </div>
                                    </div>

                                    <div class="form-check">
                                        <input
                                            type="hidden"
                                            name="is_refresher"
                                            value="0"
                                        >

                                        <input
                                            type="checkbox"
                                            name="is_refresher"
                                            id="is_refresher"
                                            class="form-check-input"
                                            value="1"
                                            {{ old('is_refresher', $certificate->is_refresher) ? 'checked' : '' }}
                                        >

                                        <label
                                            for="is_refresher"
                                            class="form-check-label"
                                        >
                                            Refresher Training
                                        </label>

                                        <div class="text-muted small">
                                            Select this when the program is a refresher training.
                                        </div>
                                    </div>

                                    <div class="form-check mt-2">
                                        <input
                                            type="hidden"
                                            name="internal_audit_training"
                                            value="0"
                                        >

                                        <input
                                            type="checkbox"
                                            name="internal_audit_training"
                                            id="internal_audit_training"
                                            class="form-check-input"
                                            value="1"
                                            {{ old('internal_audit_training', $certificate->internal_audit_training) ? 'checked' : '' }}
                                        >

                                        <label
                                            for="internal_audit_training"
                                            class="form-check-label"
                                        >
                                            Internal Auditor Training
                                        </label>

                                        <div class="text-muted small">
                                            Select this when the certificate is for an Internal Auditor training program.
                                        </div>
                                    </div>

                                    <div class="form-check mt-2">
                                        <input
                                            type="hidden"
                                            name="online_training"
                                            value="0"
                                        >

                                        <input
                                            type="checkbox"
                                            name="online_training"
                                            id="online_training"
                                            class="form-check-input"
                                            value="1"
                                            {{ old('online_training', $certificate->online_training) ? 'checked' : '' }}
                                        >

                                        <label
                                            for="online_training"
                                            class="form-check-label"
                                        >
                                            Online Training
                                        </label>

                                        <div class="text-muted small">
                                            Select this when the training program was conducted online.
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endif

                        @if($field == 'location')
                            <div class="mb-3">
                                <label for="trainer_id">
                                    Trainer *
                                </label>

                                @error('trainer_id')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <select
                                    name="trainer_id"
                                    id="trainer_id"
                                    class="form-control"
                                    required
                                >
                                    <option value="">
                                        Select Trainer
                                    </option>

                                    @foreach($trainers as $trainer)
                                        <option
                                            value="{{ $trainer->id }}"
                                            {{ old('trainer_id', $certificate->trainer_id) == $trainer->id ? 'selected' : '' }}
                                        >
                                            {{ $trainer->name }} — {{ $trainer->designation }}

                                            @if(!$trainer->is_active)
                                                (Inactive)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>

                                <small class="text-muted">
                                    The selected trainer’s name, email, designation and signature will be recorded automatically.
                                </small>
                            </div>

                            <div class="mb-3">
                                <label for="signatory_id">
                                    Signatory
                                </label>

                                @error('signatory_id')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <select
                                    name="signatory_id"
                                    id="signatory_id"
                                    class="form-control"
                                >
                                    <option value="">
                                        No Additional Signatory
                                    </option>

                                    @foreach($signatories as $signatory)
                                        <option
                                            value="{{ $signatory->id }}"
                                            {{ old('signatory_id', $certificate->signatory_id) == $signatory->id ? 'selected' : '' }}
                                        >
                                            {{ $signatory->name }} — {{ $signatory->designation }}

                                            @if($signatory->department)
                                                | {{ $signatory->department }}
                                            @endif

                                            @if(!$signatory->is_active)
                                                (Inactive)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>

                                <small class="text-muted">
                                    Optional. Select “No Additional Signatory” when the certificate should contain only the trainer’s signature.
                                </small>
                            </div>
                        @endif

                    @endforeach

                    <div class="mb-3">
                        <label for="review_by">
                            Review by
                        </label>

                        <select
                            name="review_by"
                            id="review_by"
                            class="form-control"
                        >
                            <option value="">
                                Select Reviewer
                            </option>

                            @foreach($users as $user)
                                <option
                                    value="{{ $user->name }}"
                                    {{ old('review_by', $certificate->review_by) == $user->name ? 'selected' : '' }}
                                >
                                    {{ $user->name }} | {{ $user->designation }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="approval_by">
                            Approval by
                        </label>

                        <select
                            name="approval_by"
                            id="approval_by"
                            class="form-control"
                        >
                            <option value="">
                                Select Approver
                            </option>

                            @foreach($users as $user)
                                <option
                                    value="{{ $user->name }}"
                                    {{ old('approval_by', $certificate->approval_by) == $user->name ? 'selected' : '' }}
                                >
                                    {{ $user->name }} | {{ $user->designation }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-check me-1"></i>
                            Update Certificate
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>

@include('layouts.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>