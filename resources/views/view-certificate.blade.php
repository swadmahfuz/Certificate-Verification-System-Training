<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TÜV Austria BIC CVS | View Certificate Information</title>
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
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .card-header {
            background-color: #f4f4f4;
            padding: 20px;
        }
    </style>
</head>
<body background="../images/tuv-login-background1.jpg">

<section class="pt-5">
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h3>TÜV Austria BIC CVS - Detailed Certificate Information</h3>
                <div class="btn-container mt-3">
                    <a href="../dashboard" class="btn btn-primary"><i class="fa-solid fa-arrow-left me-1"></i> Go back to Dashboard</a>
                    @if($certificate->status !== 'Deleted')
                        <a href="../add-certificate" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i> Add New Certificate</a>
                        <a href="../edit-certificate/{{ $certificate->id }}" class="btn btn-warning"><i class="fa-solid fa-pen-to-square me-1"></i> Edit Certificate</a>
                        @if($certificate->certificate_pdf)
                            <a href="{{ route('certificate.downloadPdf', $certificate->id) }}" target="_blank" class="btn btn-secondary">
                                <i class="fa-solid fa-file-pdf me-1"></i> Download Certificate PDF
                            </a>
                        @endif
                        @if(Auth::check() && (Auth::user()->id == $certificate->review_by_id || Auth::user()->name == $certificate->review_by) && $certificate->status == 'Pending Review')
                            <a href="{{ route('certificate.review', $certificate->id) }}" class="btn btn-info"><i class="fa-solid fa-thumbs-up me-1"></i> Mark as Reviewed</a>
                        @endif
                        @if(Auth::check() && (Auth::user()->id == $certificate->approval_by_id || Auth::user()->name == $certificate->approval_by) && $certificate->status == 'Pending Approval')
                            <a href="{{ route('certificate.approve', $certificate->id) }}" class="btn btn-success"><i class="fa-solid fa-check me-1"></i> Mark as Approved</a>
                        @endif
                        <a href="../delete-certificate/{{ $certificate->id }}" class="btn btn-danger"><i class="fa-solid fa-trash me-1"></i> Delete Certificate</a>
                    @endif
                </div>

                {{-- PDF Upload Form --}}
                @if(Auth::check() &&
                    (
                        Auth::user()->id == $certificate->created_by_id || Auth::user()->name == $certificate->created_by ||
                        Auth::user()->id == $certificate->review_by_id || Auth::user()->name == $certificate->review_by ||
                        Auth::user()->id == $certificate->approval_by_id || Auth::user()->name == $certificate->approval_by
                    )
                )
                    <form action="{{ route('certificate.uploadPdf', $certificate->id) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <div class="input-group justify-content-center" style="max-width: 600px; margin: 0 auto;">
                            <input type="file" name="certificate_pdf" class="form-control" accept="application/pdf" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fa-solid fa-upload me-1"></i>
                                {{ $certificate->certificate_pdf ? 'Re-upload Certificate' : 'Upload Certificate' }}
                            </button>
                        </div>
                    </form>
                @endif

                @if($certificate->certificate_pdf)
                    <div class="mt-2 text-muted small">
                        Last Uploaded by: <strong>{{ $certificate->pdf_uploaded_by }}</strong> on {{ \Carbon\Carbon::parse($certificate->pdf_uploaded_at)->format('d M Y \a\t H:i') }}
                    </div>
                @endif
            </div>

            <div class="card-body table-responsive">
                <table class="table table-striped table-bordered w-100">
                    <tbody>
                        <tr><th>Certificate Number</th><td>{{ $certificate->certificate_number }}</td></tr>
                        <tr>
                            <th>Certificate Validity</th>
                            <td>
                                @if ($certificate->status === 'Deleted')
                                    <span class="text-danger">This certificate has been deleted ❌</span>
                                @elseif ($certificate->status === 'Pending Review')
                                    <span class="text-warning">Certificate Pending Review ⚠️</span>
                                @elseif ($certificate->status === 'Pending Approval')
                                    <span class="text-warning">Certificate Pending Approval ⚠️</span>
                                @elseif (empty($certificate->expiry_date) || \Carbon\Carbon::now() <= \Carbon\Carbon::parse($certificate->expiry_date))
                                    <span class="text-success">Certificate Valid! ✅</span>
                                @else
                                    <span class="text-danger">Certificate Expired! ⚠️</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Approval Status</th>
                            <td>
                                @if($certificate->status == 'Pending')
                                    {{ $certificate->status }} Review ⚠️
                                @elseif($certificate->status == 'Reviewed')
                                    {{ $certificate->status }}. Pending Approval ⚠️
                                @elseif($certificate->status == 'Approved')
                                    {{ $certificate->status }} ✅
                                @else
                                    {{ $certificate->status }}
                                @endif
                            </td>
                        </tr>
                        <tr><th>Name</th><td>{{ $certificate->participant_name }}</td></tr>
                        <tr><th>Passport/NID</th><td>{{ $certificate->passport_nid }}</td></tr>
                        <tr><th>Driving License</th><td>{{ $certificate->driving_license }}</td></tr>
                        <tr><th>Company</th><td>{{ $certificate->company }}</td></tr>
                        <tr><th>Training</th><td>{{ $certificate->training_name }}</td></tr>
                        <tr><th>Training Location</th><td>{{ $certificate->location }}</td></tr>
                        <tr><th>Trainer</th><td>{{ $certificate->trainer }}</td></tr>
                        <tr><th>Training Start Date</th><td>{{ \Carbon\Carbon::parse($certificate->training_date)->format('d M Y') }}</td></tr>
                        <tr><th>Training End Date</th><td>{{ \Carbon\Carbon::parse($certificate->training_end)->format('d M Y') }}</td></tr>
                        <tr><th>Issue Date</th><td>{{ \Carbon\Carbon::parse($certificate->issue_date)->format('d M Y') }}</td></tr>
                        <tr>
                            <th>Valid Till</th>
                            <td>
                                @if (!empty($certificate->expiry_date))
                                    {{ \Carbon\Carbon::parse($certificate->expiry_date)->format('d M Y') }}
                                @else
                                    No Expiry Date
                                @endif
                            </td>
                        </tr>
                        <tr><th>Certificate PDF File</th>
                            <td>
                                @if($certificate->certificate_pdf)
                                <a href="{{ route('certificate.downloadPdf', $certificate->id) }}" target="_blank">
                                    <strong>{{ $certificate->certificate_pdf }}</strong><br>
                                </a>
                                @else
                                    <span class="text-danger">No certificate PDF uploaded yet ❌</span>
                                @endif
                            </td>
                        </tr>
                        <tr><th>PDF Uploaded by</th>
                            <td>
                                @if($certificate->certificate_pdf)
                                    {{ $certificate->pdf_uploaded_by }}
                                @else
                                    <span class="text-danger">No certificate PDF uploaded yet ❌</span>
                                @endif
                            </td>
                        </tr>
                        <tr><th>Certificate Uploaded at</th>
                            <td>
                                @if($certificate->certificate_pdf)
                                    {{ \Carbon\Carbon::parse($certificate->pdf_uploaded_at)->format('d M Y \a\t H:i:s') }}
                                @else
                                    <span class="text-danger">No certificate PDF uploaded yet ❌</span>
                                @endif
                            </td>
                        </tr>
                        <tr><th>Review by</th><td>{{ $certificate->review_by }}</td></tr>
                        <tr><th>Approval by</th><td>{{ $certificate->approval_by }}</td></tr>
                        <tr>
                            <th>QR Code</th>
                            <td>
                                @php
                                    $url = url('');
                                    $verification_url = $url . '?search=' . $certificate->certificate_number;
                                @endphp
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $verification_url }}" />
                            </td>
                        </tr>
                        <tr><th>Created by</th><td>{{ $certificate->created_by }}</td></tr>
                        <tr><th>Created on</th><td>{{ $certificate->created_at->format('d M Y \a\t H:i:s') }}</td></tr>
                        <tr><th>Last Updated by</th><td>{{ $certificate->updated_by }}</td></tr>
                        <tr>
                            <th>Last Updated on</th>
                            <td>
                                @if ($certificate->updated_by)
                                    {{ $certificate->updated_at->format('d M Y \a\t H:i:s') }}
                                @endif
                            </td>
                        </tr>
                        <tr><th>Deleted by</th><td>{{ $certificate->deleted_by }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@include('layouts.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
