<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TÜV Austria BIC CVS - Certificate Verification System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <style>
        body {
            background-color: #f8f9fa;
            font-size: 13px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding-top: 40px;
        }
        .form-control {
            font-size: 14px;
            padding: 10px;
        }
        .btn {
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 15px;
        }
        h1, h3, h4 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mb-4">
            <img src="images/TUV Austria Logo.png" alt="TUV Logo" width="250">
            <h1>Verify Training Certificate</h1>
            <p>Enter the Certificate Number and click the "Verify" button.</p>
        </div>

        <form id="s-form" method="get" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" id="search" class="form-control" placeholder="Ex: TUV/CERT/2022/0911/001" required>
                <button class="btn btn-primary" type="submit">VERIFY</button>
            </div>
        </form>

        @isset($certificates)
            <div>
                @if($certificates->count() < 1)
                    <div class="alert alert-warning text-center">
                        ⚠️ No records of the certificate number you entered can be found in our database. ⚠️<br>
                        Please contact us for further inquiry or clarification. <br>
                        Tel: +88 02 8836403 ; Email: info@tuvat.com.bd 
                    </div>
                @endif

                @foreach ($certificates as $certificate)
                    <div class="mb-4">
                        @if ($certificate->status == 'Deleted')
                            <h3 class="text-danger">This certificate has been deleted and is no longer valid. ❌</h3>
                        @elseif (empty($certificate->expiry_date) || ! \Carbon\Carbon::parse($certificate->expiry_date)->isPast())
                            <h3 class="text-success">Certificate Authentic and Valid! ✅</h3>
                            <h6><center>Please verify the details below:</center></h6>
                        @else
                            <h3 class="text-warning">Certificate Authentic but Expired! ⚠️</h3>
                        @endif

                        <table class="table table-bordered mt-3">
                            <tr><td><strong>Certificate Number</strong></td><td>{{ $certificate->certificate_number }}</td></tr>
                            <tr><td><strong>Participant Name</strong></td><td>{{ $certificate->participant_name }}</td></tr>
                            <tr><td><strong>Company</strong></td><td>{{ $certificate->company }}</td></tr>
                            <tr><td><strong>Training</strong></td><td>{{ $certificate->training_name }}</td></tr>
                            <tr><td><strong>Training Location</strong></td><td>{{ $certificate->location }}</td></tr>
                            <tr><td><strong>Trainer</strong></td><td>{{ $certificate->trainer }}</td></tr>
                            <tr><td><strong>Training Start Date</strong></td><td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->training_date)->format('d M Y') }}</td></tr>
                            <tr><td><strong>Training End Date</strong></td><td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->training_end)->format('d M Y') }}</td></tr>
                            <tr><td><strong>Issue Date</strong></td><td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->issue_date)->format('d M Y') }}</td></tr>
                            <tr>
                                <td><strong>Expiry Date</strong></td>
                                <td>
                                    @if (!empty($certificate->expiry_date))
                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->expiry_date)->format('d M Y') }}
                                    @else
                                        No Expiry Date
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                @endforeach
            </div>
        @endisset

        @include('layouts.footer')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
