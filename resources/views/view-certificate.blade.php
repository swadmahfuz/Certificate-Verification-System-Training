@extends('layouts.admin')

@section('title', 'Certificate Details')

@push('styles')
<style>
        .certificate-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
    </style>
@endpush

@section('content')
<div class="page-heading">
    <div>
        <h1>Certificate Details</h1>
        <p>{{ $certificate->certificate_number }} — {{ $certificate->participant_name }}</p>
    </div>
    <div class="certificate-actions">
        <a href="{{ route('certificates.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>

        @if($certificate->status !== 'Deleted')
            @canMutate
            <a href="{{ route('certificate.edit', $certificate->id) }}" class="btn btn-warning btn-sm">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit
            </a>
            @endcanMutate

            @if($certificate->certificate_pdf)
                <a href="{{ route('certificate.downloadPdf', $certificate->id) }}" target="_blank" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-file-pdf me-1"></i> Download PDF
                </a>
            @endif

            @canMutate
            @if(Auth::user()->id == $certificate->review_by_id && $certificate->status == 'Pending Review')
                <form action="{{ route('certificate.review', $certificate->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-info btn-sm" data-confirm="Mark this certificate as Reviewed?">
                        <i class="fa-solid fa-thumbs-up me-1"></i> Mark as Reviewed
                    </button>
                </form>
            @endif

            @if(Auth::user()->id == $certificate->approval_by_id && $certificate->status == 'Pending Approval')
                <form action="{{ route('certificate.approve', $certificate->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm" data-confirm="Mark this certificate as Approved?">
                        <i class="fa-solid fa-check me-1"></i> Mark as Approved
                    </button>
                </form>
            @endif

            <form action="{{ route('certificate.delete', $certificate->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete this certificate?">
                    <i class="fa-solid fa-trash me-1"></i> Delete
                </button>
            </form>
            @endcanMutate
        @endif
    </div>
</div>

<section class="admin-card">
    <div class="admin-card-header"><h2>Record Summary</h2></div>
    <div class="admin-card-body">
                @canMutate
                @if(
                    Auth::user()->id == $certificate->created_by_id ||
                    Auth::user()->id == $certificate->review_by_id ||
                    Auth::user()->id == $certificate->approval_by_id
                )
                    <form
                        action="{{ route('certificate.uploadPdf', $certificate->id) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="mb-3"
                    >
                        @csrf
                        <div class="input-group" style="max-width: 600px;">
                            <input type="file" name="certificate_pdf" class="form-control" accept="application/pdf" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fa-solid fa-upload me-1"></i>
                                {{ $certificate->certificate_pdf ? 'Re-upload Certificate' : 'Upload Certificate' }}
                            </button>
                        </div>
                    </form>
                @endif
                @endcanMutate

                @if($certificate->certificate_pdf)
                    <div class="mb-3 text-muted small">
                        Last uploaded by <strong>{{ $certificate->pdf_uploaded_by }}</strong>
                        on {{ \Carbon\Carbon::parse($certificate->pdf_uploaded_at)->format('d M Y \a\t H:i') }}
                    </div>
                @endif

                <div class="table-responsive">

                <table class="table table-striped table-bordered w-100">
                    <tbody>

                        <tr>
                            <th>Certificate Number</th>
                            <td>{{ $certificate->certificate_number }}</td>
                        </tr>

                        <tr>
                            <th>Certificate Type</th>
                            <td>
                                {{ $certificate->certificate_type ?: 'Not Specified' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Certificate Validity</th>

                            <td>
                                @if($certificate->status === 'Deleted')

                                    <span class="text-danger">
                                        This certificate has been deleted ❌
                                    </span>

                                @elseif($certificate->status === 'Pending Review')

                                    <span class="text-warning">
                                        Certificate Pending Review ⚠️
                                    </span>

                                @elseif($certificate->status === 'Pending Approval')

                                    <span class="text-warning">
                                        Certificate Pending Approval ⚠️
                                    </span>

                                @elseif(
                                    empty($certificate->expiry_date) ||
                                    \Carbon\Carbon::now() <=
                                    \Carbon\Carbon::parse($certificate->expiry_date)
                                )

                                    <span class="text-success">
                                        Certificate Valid! ✅
                                    </span>

                                @else

                                    <span class="text-danger">
                                        Certificate Expired! ⚠️
                                    </span>

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

                        <tr>
                            <th>Name</th>
                            <td>{{ $certificate->participant_name }}</td>
                        </tr>

                        <tr>
                            <th>Passport/NID</th>
                            <td>{{ $certificate->passport_nid }}</td>
                        </tr>

                        <tr>
                            <th>Driving License</th>
                            <td>{{ $certificate->driving_license }}</td>
                        </tr>

                        <tr>
                            <th>Company</th>
                            <td>{{ $certificate->company }}</td>
                        </tr>

                        <tr>
                            <th>Training Title</th>
                            <td>
                                @if($certificate->internal_audit_training)
                                    Internal Auditor - {{ $certificate->training_name }}
                                @else
                                    {{ $certificate->training_name }}
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Training Mode</th>
                            <td>
                                @if($certificate->online_training)
                                    Online
                                @else
                                    Physical
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Training Category</th>
                            <td>
                                @if($certificate->is_refresher)
                                    Refresher Training
                                @else
                                    Initial Training
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Training Sessions</th>
                            <td>
                                @if($certificate->has_practical)
                                    Theory &amp; Practical
                                @else
                                    Theory Only
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Training Location</th>
                            <td>
                                @if($certificate->online_training)
                                    Not Applicable
                                @else
                                    {{ $certificate->location }}
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Trainer</th>

                            <td>
                                <strong>
                                    {{ $certificate->trainer ?: 'Not Specified' }}
                                </strong>

                                @if($certificate->trainer_designation)
                                    <br>
                                    {{ $certificate->trainer_designation }}
                                @endif

                                @if($certificate->trainer_email)
                                    <br>

                                    <span class="text-muted">
                                        {{ $certificate->trainer_email }}
                                    </span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Signatory</th>

                            <td>
                                @if($certificate->signatory_name)

                                    <strong>
                                        {{ $certificate->signatory_name }}
                                    </strong>

                                    @if($certificate->signatory_designation)
                                        <br>
                                        {{ $certificate->signatory_designation }}
                                    @endif

                                    @if($certificate->signatory_department)
                                        <br>
                                        {{ $certificate->signatory_department }}
                                    @endif

                                    @if($certificate->signatory_email)
                                        <br>

                                        <span class="text-muted">
                                            {{ $certificate->signatory_email }}
                                        </span>
                                    @endif

                                @else

                                    <span class="text-muted">
                                        No Additional Signatory
                                    </span>

                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Training Start Date</th>

                            <td>
                                {{ \Carbon\Carbon::parse($certificate->training_date)->format('d M Y') }}
                            </td>
                        </tr>

                        <tr>
                            <th>Training End Date</th>

                            <td>
                                {{ \Carbon\Carbon::parse($certificate->training_end)->format('d M Y') }}
                            </td>
                        </tr>

                        <tr>
                            <th>Issue Date</th>

                            <td>
                                {{ \Carbon\Carbon::parse($certificate->issue_date)->format('d M Y') }}
                            </td>
                        </tr>

                        <tr>
                            <th>Valid Till</th>

                            <td>
                                @if(!empty($certificate->expiry_date))

                                    {{ \Carbon\Carbon::parse($certificate->expiry_date)->format('d M Y') }}

                                @else

                                    No Expiry Date

                                @endif
                            </td>
                        </tr>

                        {{--
                            Determine whether this certificate contains all
                            information needed for dynamic PDF generation.

                            Legacy certificates normally do not contain:
                            - Certificate type
                            - Trainer signature snapshot
                            - Optional signatory signature snapshot
                        --}}
                        @php
                            $trainerSignatureExists =
                                !empty($certificate->trainer_signature_path) &&
                                \Illuminate\Support\Facades\Storage::exists(
                                    $certificate->trainer_signature_path
                                );

                            $signatorySignatureExists =
                                empty($certificate->signatory_name) ||
                                (
                                    !empty($certificate->signatory_signature_path) &&
                                    \Illuminate\Support\Facades\Storage::exists(
                                        $certificate->signatory_signature_path
                                    )
                                );

                            $hasPdfGenerationData =
                                !empty($certificate->certificate_type) &&
                                !empty($certificate->trainer) &&
                                $trainerSignatureExists &&
                                $signatorySignatureExists;
                        @endphp

                        {{-- Hide the entire row for legacy certificates. --}}
                        @if($hasPdfGenerationData)
                            <tr>
                                <th>Generate Certificate PDF</th>

                                <td>
                                    @if($certificate->status == 'Approved')

                                        <a
                                            href="{{ route('certificate.generatePdf', $certificate->id) }}"
                                            class="btn btn-success d-inline-flex"
                                            style="width: auto;"
                                        >
                                            <i class="fa-solid fa-file-pdf me-1"></i>
                                            Generate & Download Certificate PDF
                                        </a>

                                        <br>

                                        <small class="text-muted">
                                            The PDF will be generated on demand and will not be stored in the system.
                                        </small>

                                    @else

                                        <span class="text-warning">
                                            Certificate PDF can be generated after approval.
                                        </span>

                                    @endif
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <th>Certificate PDF File</th>

                            <td>
                                @if($certificate->certificate_pdf)

                                    <a
                                        href="{{ route('certificate.downloadPdf', $certificate->id) }}"
                                        target="_blank"
                                    >
                                        <strong>
                                            {{ $certificate->certificate_pdf }}
                                        </strong>
                                    </a>

                                @else

                                    <span class="text-danger">
                                        No certificate PDF uploaded yet ❌
                                    </span>

                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>PDF Uploaded by</th>

                            <td>
                                @if($certificate->certificate_pdf)

                                    {{ $certificate->pdf_uploaded_by }}

                                @else

                                    <span class="text-danger">
                                        No certificate PDF uploaded yet ❌
                                    </span>

                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Certificate Uploaded at</th>

                            <td>
                                @if($certificate->certificate_pdf)

                                    {{ \Carbon\Carbon::parse($certificate->pdf_uploaded_at)->format('d M Y \a\t H:i:s') }}

                                @else

                                    <span class="text-danger">
                                        No certificate PDF uploaded yet ❌
                                    </span>

                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Review by</th>
                            <td>{{ $certificate->review_by }}</td>
                        </tr>

                        <tr>
                            <th>Reviewed on</th>

                            <td>
                                @if($certificate->review_by)

                                    {{ $certificate->reviewed_at
                                        ? $certificate->reviewed_at->format('d M Y \a\t H:i:s')
                                        : 'Not yet reviewed'
                                    }}

                                @else

                                    Not yet reviewed

                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Approval by</th>
                            <td>{{ $certificate->approval_by }}</td>
                        </tr>

                        <tr>
                            <th>Approved on</th>

                            <td>
                                @if($certificate->approval_by)

                                    {{ $certificate->approved_at
                                        ? $certificate->approved_at->format('d M Y \a\t H:i:s')
                                        : 'Not yet approved'
                                    }}

                                @else

                                    Not yet approved

                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>QR Code</th>

                            <td>
                                @php
                                    $verification_url = route(
                                        'certificate.search',
                                        [
                                            'search' =>
                                                $certificate->certificate_number
                                        ]
                                    );
                                @endphp

                                <img
                                    src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&format=png&data={{ urlencode($verification_url) }}"
                                    alt="Certificate Verification QR Code"
                                >
                            </td>
                        </tr>

                        <tr>
                            <th>Created by</th>
                            <td>{{ $certificate->created_by }}</td>
                        </tr>

                        <tr>
                            <th>Created on</th>

                            <td>
                                {{ $certificate->created_at->format('d M Y \a\t H:i:s') }}
                            </td>
                        </tr>

                        <tr>
                            <th>Last Updated by</th>
                            <td>{{ $certificate->updated_by }}</td>
                        </tr>

                        <tr>
                            <th>Last Updated on</th>

                            <td>
                                @if($certificate->updated_by)

                                    {{ $certificate->updated_at->format('d M Y \a\t H:i:s') }}

                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Deleted by</th>

                            <td>
                                @if($certificate->status === 'Deleted')

                                    {{ $certificate->deleted_by }}

                                @else

                                    N/A

                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Deleted on</th>

                            <td>
                                @if($certificate->deleted_by)

                                    {{ $certificate->deleted_at
                                        ? $certificate->deleted_at->format('d M Y \a\t H:i:s')
                                        : 'N/A'
                                    }}

                                @else

                                    N/A

                                @endif
                            </td>
                        </tr>

                    </tbody>
                </table>
                </div>

                {{-- Toggleable Inline PDF Viewer --}}
                @if($certificate->certificate_pdf)
                    @php
                        $collapseId = 'pdfViewerCollapse-' . $certificate->id;
                        $toggleId = 'togglePdfHeaderBtn-' . $certificate->id;
                    @endphp

                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <button
                                id="{{ $toggleId }}"
                                class="btn btn-link header-toggle d-flex align-items-center"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#{{ $collapseId }}"
                                aria-expanded="false"
                                aria-controls="{{ $collapseId }}"
                            >
                                <i class="fa-solid fa-chevron-right me-2 chev"></i>
                                <span>Certificate PDF Preview</span>
                            </button>
                            <small class="text-muted">
                                If it doesn’t load,
                                <a href="{{ route('certificate.downloadPdf', $certificate->id) }}" target="_blank">download</a>.
                            </small>
                        </div>
                        <div class="collapse" id="{{ $collapseId }}">
                            <div class="card-body p-0" style="height: 75vh;">
                                <iframe
                                    data-viewer-src="{{ route('certificate.viewPdf', $certificate->id) }}"
                                    title="Certificate PDF"
                                    style="width: 100%; height: 100%; border: 0;"
                                    allow="fullscreen"
                                    loading="lazy"
                                ></iframe>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning mt-4">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        No certificate PDF uploaded yet.
                    </div>
                @endif
    </div>
</section>
@endsection

@if($certificate->certificate_pdf)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const collapseEl = document.getElementById('pdfViewerCollapse-{{ $certificate->id }}');
    const btn = document.getElementById('togglePdfHeaderBtn-{{ $certificate->id }}');

    if (!collapseEl || !btn) {
        return;
    }

    const iframe = collapseEl.querySelector('iframe');

    collapseEl.addEventListener('show.bs.collapse', function () {
        if (!iframe.getAttribute('src')) {
            iframe.setAttribute('src', iframe.dataset.viewerSrc);
        }

        btn.setAttribute('aria-expanded', 'true');
    });

    collapseEl.addEventListener('hide.bs.collapse', function () {
        btn.setAttribute('aria-expanded', 'false');
    });
});
</script>
@endpush
@endif
