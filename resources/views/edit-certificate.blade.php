<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TÜV Austria BIC CVS | Edit Certificate Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
</head>
<body background="../images/tuv-login-background1.jpg">

<section class="pt-5">
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h3>Edit Certificate Information</h3>
                <div class="d-flex justify-content-center gap-2 flex-wrap mt-3">
                    <a href="../dashboard" class="btn btn-primary"><i class="fa-solid fa-arrow-left me-1"></i> Go back to Dashboard</a>
                    <a href="../delete-certificate/{{ $certificate->id }}" class="btn btn-danger"><i class="fa-solid fa-trash me-1"></i> Delete Certificate</a>
                </div>
                <p class="text-end mt-2 mb-0" style="font-style: italic;">* Required fields</p>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('certificate.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $certificate->id }}">

                    @foreach([
                        'certificate_number' => 'Certificate Number',
                        'participant_name' => 'Participant Name',
                        'passport_nid' => 'NID/Passport Number',
                        'driving_license' => 'Driving License',
                        'company' => 'Company',
                        'training_name' => 'Training Name',
                        'location' => 'Training Location',
                        'trainer' => 'Trainer Name',
                        'training_date' => 'Training Start Date',
                        'training_end' => 'Training End Date',
                        'issue_date' => 'Issue Date',
                        'expiry_date' => 'Expiry Date'
                    ] as $field => $label)
                        <div class="mb-3">
                            <label for="{{ $field }}">{{ $label }}@if(in_array($field, ['certificate_number', 'participant_name', 'passport_nid', 'training_name', 'location', 'trainer', 'training_date', 'training_end', 'issue_date'])) * @endif</label>
                            @error($field)
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <input type="{{ in_array($field, ['training_date', 'training_end', 'issue_date', 'expiry_date']) ? 'date' : 'text' }}"
                                   name="{{ $field }}" class="form-control"
                                   value="{{ old($field, $certificate->$field) }}"
                                   placeholder="Enter {{ $label }}">
                        </div>
                    @endforeach

                    <div class="mb-3">
                        <label for="review_by">Review by</label>
                        <select name="review_by" class="form-control">
                            <option value="">Select Reviewer</option>
                            @foreach($users as $user)
                                <option value="{{ $user->name }}" {{ $user->name == $certificate->review_by ? 'selected' : '' }}>
                                    {{ $user->name }} | {{ $user->designation }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="approval_by">Approval by</label>
                        <select name="approval_by" class="form-control">
                            <option value="">Select Approver</option>
                            @foreach($users as $user)
                                <option value="{{ $user->name }}" {{ $user->name == $certificate->approval_by ? 'selected' : '' }}>
                                    {{ $user->name }} | {{ $user->designation }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success"><i class="fa-solid fa-check me-1"></i> Update Certificate</button>
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
