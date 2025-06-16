<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Certificate</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
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
                <h3>TÜV Austria BIC CVS - Add New Certificate</h3>
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
                        <label for="trainer">Trainer Name *</label>
                        @error('trainer') <div class="text-danger">{{ $message }}</div> @enderror
                        <input type="text" name="trainer" class="form-control" value="{{ old('trainer') }}">
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
