<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Certificate</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
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
    </style>
</head>
<body background="images/tuv-login-background1.jpg">

<section class="pt-5">
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h3>TÜV Austria BIC CVS | Add New Certificate</h3>
                <div class="mt-3 d-flex justify-content-center">
                    <a href="./dashboard" class="btn btn-primary me-2"><i class="fa-solid fa-arrow-left me-1"></i> Go back to Dashboard</a>
                </div>
                <p class="text-end mt-2 mb-0" style="font-style: italic;">* Required fields</p>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('certificate.create') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="certificate_number">Certificate Number *</label>
                        @error('certificate_number') <div class="text-danger">{{ $message }}</div> @enderror
                        <input type="text" name="certificate_number" class="form-control" value="TUVAT/CERT/{{ $currentYear }}/{{ $currentMonthDay }}/">
                    </div>

                    <div class="mb-3">
                        <label for="certificate_type">Certificate Type *</label>

                        @error('certificate_type')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror

                        <select
                            name="certificate_type"
                            id="certificate_type"
                            class="form-control"
                            required
                        >
                            <option value="">Select Certificate Type</option>

                            <option
                                value="Certificate"
                                {{ old('certificate_type') == 'Certificate' ? 'selected' : '' }}
                            >
                                Certificate
                            </option>

                            <option
                                value="Certificate of Achievement"
                                {{ old('certificate_type') == 'Certificate of Achievement' ? 'selected' : '' }}
                            >
                                Certificate of Achievement
                            </option>

                            <option
                                value="Certificate of Competency"
                                {{ old('certificate_type') == 'Certificate of Competency' ? 'selected' : '' }}
                            >
                                Certificate of Competency
                            </option>

                            <option
                                value="Certificate of Attendance"
                                {{ old('certificate_type') == 'Certificate of Attendance' ? 'selected' : '' }}
                            >
                                Certificate of Attendance
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="participant_name">Participant Name *</label>
                        @error('participant_name') <div class="text-danger">{{ $message }}</div> @enderror
                        <input type="text" name="participant_name" class="form-control" value="{{ old('participant_name') }}">
                    </div>

                    <div class="mb-3">
                        <label for="passport_nid">NID/Passport Number *</label>
                        @error('passport_nid') <div class="text-danger">{{ $message }}</div> @enderror
                        <input type="text" name="passport_nid" class="form-control" value="{{ old('passport_nid') }}">
                    </div>

                    <div class="mb-3">
                        <label for="driving_license">Driving License</label>
                        <input type="text" name="driving_license" class="form-control" value="{{ old('driving_license') }}">
                    </div>

                    <div class="mb-3">
                        <label for="company">Company</label>
                        <input type="text" name="company" class="form-control" value="{{ old('company') }}">
                    </div>

                    <div class="mb-3">
                        <label for="training_name">Training Name *</label>
                        @error('training_name') <div class="text-danger">{{ $message }}</div> @enderror
                        <input type="text" name="training_name" class="form-control" value="{{ old('training_name') }}">
                    </div>

                    <div class="mb-3">
                        <label for="location">Training Location *</label>
                        @error('location') <div class="text-danger">{{ $message }}</div> @enderror
                        <input type="text" name="location" class="form-control" value="TUV Austria - BD Office, Dhaka, Bangladesh.">
                    </div>

                    <div class="mb-3">
                        <label for="trainer_id">Trainer *</label>

                        @error('trainer_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror

                        <select
                            name="trainer_id"
                            id="trainer_id"
                            class="form-control"
                            required
                        >
                            <option value="">Select Trainer</option>

                            @foreach($trainers as $trainer)
                                <option
                                    value="{{ $trainer->id }}"
                                    {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}
                                >
                                    {{ $trainer->name }}
                                    @if($trainer->designation)
                                        — {{ $trainer->designation }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        <small class="text-muted">
                            The selected trainer’s email, designation and signature will be
                            recorded automatically.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="signatory_id">Signatory</label>

                        @error('signatory_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror

                        <select name="signatory_id" id="signatory_id" class="form-control">
                            <option value="">No Additional Signatory</option>

                            @foreach($signatories as $signatory)
                                <option value="{{ $signatory->id }}" {{ old('signatory_id') == $signatory->id ? 'selected' : '' }}>
                                    {{ $signatory->name }} — {{ $signatory->designation }}
                                    @if($signatory->department)
                                        | {{ $signatory->department }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        <small class="text-muted">
                            Optional. Leave this as “No Additional Signatory” when the certificate should contain only the trainer’s signature.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="training_date">Training Start Date *</label>
                        @error('training_date') <div class="text-danger">{{ $message }}</div> @enderror
                        <input type="date" name="training_date" class="form-control" value="{{ old('training_date') }}">
                    </div>

                    <div class="mb-3">
                        <label for="training_end">Training End Date *</label>
                        @error('training_end') <div class="text-danger">{{ $message }}</div> @enderror
                        <input type="date" name="training_end" class="form-control" value="{{ old('training_end') }}">
                    </div>

                    <div class="mb-3">
                        <label for="issue_date">Issue Date *</label>
                        @error('issue_date') <div class="text-danger">{{ $message }}</div> @enderror
                        <input type="date" name="issue_date" class="form-control" value="{{ old('issue_date') }}">
                    </div>

                    <div class="mb-3">
                        <label for="expiry_date">Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}">
                    </div>

                    <div class="mb-3">
                        <label for="review_by">Review by</label>
                        <select name="review_by" class="form-control">
                            <option value="">Select Reviewer</option>
                            @foreach($users as $user)
                                <option value="{{ $user->name }}">{{ $user->name }} | {{ $user->designation }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="approval_by">Approval by</label>
                        <select name="approval_by" class="form-control">
                            <option value="">Select Approver</option>
                            @foreach($users as $user)
                                <option value="{{ $user->name }}">{{ $user->name }} | {{ $user->designation }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success"><i class="fa-solid fa-check me-1"></i> Add Details</button>
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
