@extends('layouts.admin')

@section('title', 'Certificates')

@section('content')
<div class="page-heading">
    <div>
        <h1>Certificates</h1>
        <p>Search, verify, and manage all training certificates.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('certificate.createForm') }}">
        <i class="fa-solid fa-plus me-1"></i> Add Certificate
    </a>
</div>

<section class="admin-card">
    <div class="admin-card-header">
        <h2>All Certificates</h2>
        <div class="toolbar">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input class="form-control search-input" type="search" placeholder="Search certificates">
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover admin-table search-result">
            <thead>
                <tr>
                    <th>Sl.</th>
                    <th>Certificate ID</th>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Training Title</th>
                    <th>Trainer</th>
                    <th>Issue Date</th>
                    <th>Status</th>
                    <th>QR</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php $offset = ($certificates->currentPage() - 1) * $certificates->perPage(); @endphp
                @forelse($certificates as $certificate)
                    <tr>
                        <td>{{ $loop->iteration + $offset }}</td>
                        <td>{{ $certificate->certificate_number }}</td>
                        <td>{{ $certificate->participant_name }}</td>
                        <td>{{ $certificate->company ?: 'N/A' }}</td>
                        <td>{{ $certificate->internal_audit_training ? 'Internal Auditor - ' : '' }}{{ $certificate->training_name }}</td>
                        <td>{{ $certificate->trainer }}</td>
                        <td>{{ $certificate->issue_date ? \Carbon\Carbon::parse($certificate->issue_date)->format('d-m-Y') : 'N/A' }}</td>
                        <td><x-admin.status-badge :status="$certificate->status" /></td>
                        <td>
                            <img width="38" height="38" alt="QR code" src="https://api.qrserver.com/v1/create-qr-code/?size=76x76&amp;data={{ urlencode(url('/').'?search='.$certificate->certificate_number) }}">
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('certificate.view', $certificate->id) }}" title="View"><i class="fa-solid fa-circle-info"></i></a>
                                <a href="{{ route('certificate.edit', $certificate->id) }}" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                @if($certificate->status === 'Approved' && $certificate->certificate_type && $certificate->trainer_id)
                                    <a class="danger" href="{{ route('certificate.generatePdf', $certificate->id) }}" title="Generate PDF"><i class="fa-solid fa-file-pdf"></i></a>
                                @endif
                                <form action="{{ route('certificate.delete', $certificate->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="danger" type="submit" title="Delete" data-confirm="Delete this certificate?"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">No certificates found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">{{ $certificates->links() }}</div>
</section>
@endsection

@push('scripts')
<script>
$(function () {
    var timer;
    $('.search-input').on('input', function () {
        var query = this.value;
        clearTimeout(timer);
        timer = setTimeout(function () {
            $.get(@json(route('liveSearch')), { userInput: query }, function (response) {
                var rows = response.data.data.map(function (item, index) {
                    var title = (item.internal_audit_training ? 'Internal Auditor - ' : '') + item.training_name;
                    var verification = @json(url('/')) + '?search=' + encodeURIComponent(item.certificate_number);
                    return '<tr><td>' + (index + 1) + '</td>' +
                        '<td>' + escapeHtml(item.certificate_number) + '</td>' +
                        '<td>' + escapeHtml(item.participant_name) + '</td>' +
                        '<td>' + escapeHtml(item.company || 'N/A') + '</td>' +
                        '<td>' + escapeHtml(title) + '</td>' +
                        '<td>' + escapeHtml(item.trainer) + '</td>' +
                        '<td>' + formatDate(item.issue_date) + '</td>' +
                        '<td><span class="status-pill">' + escapeHtml(item.status) + '</span></td>' +
                        '<td><img width="38" height="38" src="https://api.qrserver.com/v1/create-qr-code/?size=76x76&data=' + encodeURIComponent(verification) + '"></td>' +
                        '<td><div class="table-actions"><a href="' + @json(url('/view-certificate')) + '/' + item.id + '"><i class="fa-solid fa-circle-info"></i></a>' +
                        '<a href="' + @json(url('/edit-certificate')) + '/' + item.id + '"><i class="fa-solid fa-pen-to-square"></i></a></div></td></tr>';
                }).join('');
                $('.search-result tbody').html(rows || '<tr><td colspan="10" class="text-center py-4">No matching certificates.</td></tr>');
            });
        }, 250);
    });

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : value).html();
    }
    function formatDate(value) {
        if (!value) return 'N/A';
        var parts = value.split('-');
        return parts.length === 3 ? parts[2] + '-' + parts[1] + '-' + parts[0] : value;
    }
});
</script>
@endpush
