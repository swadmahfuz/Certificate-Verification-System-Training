@extends('layouts.admin')

@section('title', 'Deleted Certificates')

@section('content')
<div class="page-heading">
    <div>
        <h1>Deleted Certificates</h1>
        <p>Soft-deleted certificates kept for audit and reference.</p>
    </div>
</div>

<section class="admin-card">
    <div class="admin-card-header">
        <h2>Deleted Certificates</h2>
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
                    <th>Trg Date</th>
                    <th>Issue Date</th>
                    <th>Exp. Date</th>
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
                        <td>
                            @if($certificate->internal_audit_training)
                                Internal Auditor - {{ $certificate->training_name }}
                            @else
                                {{ $certificate->training_name }}
                            @endif
                        </td>
                        <td>{{ $certificate->trainer }}</td>
                        <td>{{ $certificate->training_date ? \Carbon\Carbon::parse($certificate->training_date)->format('d-m-Y') : 'N/A' }}</td>
                        <td>{{ $certificate->issue_date ? \Carbon\Carbon::parse($certificate->issue_date)->format('d-m-Y') : 'N/A' }}</td>
                        <td>{{ $certificate->expiry_date ? \Carbon\Carbon::parse($certificate->expiry_date)->format('d-m-Y') : 'N/A' }}</td>
                        <td><x-admin.status-badge :status="$certificate->status" /></td>
                        <td>
                            <img width="38" height="38" alt="QR code" src="https://api.qrserver.com/v1/create-qr-code/?size=76x76&amp;data={{ urlencode(url('/').'?search='.$certificate->certificate_number) }}">
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('certificate.view', $certificate->id) }}" target="_blank" rel="noopener noreferrer" title="View"><i class="fa-solid fa-circle-info"></i></a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="text-center text-muted py-4">No deleted certificates found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top search-pagination">{{ $certificates->links() }}</div>
</section>
@endsection

@push('scripts')
<script>
$(function () {
    var viewBase = @json(url('/view-certificate'));
    var verifyBase = @json(url('/'));

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : value).html();
    }

    function formatDate(date) {
        if (!date) return 'N/A';
        var d = new Date(date);
        if (isNaN(d.getTime())) return 'N/A';
        return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
    }

    function fetchCertificates(page, userInput) {
        page = page || 1;
        userInput = userInput || '';
        $.ajax({
            url: @json(route('liveSearchDeleted')),
            data: { userInput: userInput, page: page },
            dataType: 'json',
            beforeSend: function () {
                $('.search-result tbody').html('<tr><td colspan="12" class="text-center text-muted py-4">Searching...</td></tr>');
            },
            success: function (res) {
                var html = '';
                $.each(res.data.data, function (index, data) {
                    var title = (data.internal_audit_training === true || data.internal_audit_training == 1)
                        ? 'Internal Auditor - ' + data.training_name
                        : data.training_name;
                    var verification = verifyBase + '?search=' + encodeURIComponent(data.certificate_number);
                    html += '<tr>' +
                        '<td>' + (index + 1 + (res.data.current_page - 1) * res.data.per_page) + '</td>' +
                        '<td>' + escapeHtml(data.certificate_number) + '</td>' +
                        '<td>' + escapeHtml(data.participant_name) + '</td>' +
                        '<td>' + escapeHtml(data.company || 'N/A') + '</td>' +
                        '<td>' + escapeHtml(title) + '</td>' +
                        '<td>' + escapeHtml(data.trainer) + '</td>' +
                        '<td>' + formatDate(data.training_date) + '</td>' +
                        '<td>' + formatDate(data.issue_date) + '</td>' +
                        '<td>' + formatDate(data.expiry_date) + '</td>' +
                        '<td><span class="status-pill">' + escapeHtml(data.status) + '</span></td>' +
                        '<td><img width="38" height="38" src="https://api.qrserver.com/v1/create-qr-code/?size=76x76&data=' + encodeURIComponent(verification) + '"></td>' +
                        '<td><div class="table-actions"><a href="' + viewBase + '/' + data.id + '" target="_blank" rel="noopener noreferrer" title="View"><i class="fa-solid fa-circle-info"></i></a></div></td>' +
                        '</tr>';
                });
                $('.search-result tbody').html(html || '<tr><td colspan="12" class="text-center text-muted py-4">No matching certificates found.</td></tr>');
                $('.search-pagination').html(generatePaginationLinks(res.data));
            }
        });
    }

    function generatePaginationLinks(data) {
        var links = '<nav><ul class="pagination mb-0">';
        if (data.current_page > 1) {
            links += '<li class="page-item"><a class="page-link" href="#" data-page="' + (data.current_page - 1) + '">&laquo;</a></li>';
        }
        for (var i = 1; i <= data.last_page; i++) {
            links += '<li class="page-item' + (i === data.current_page ? ' active' : '') + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
        }
        if (data.current_page < data.last_page) {
            links += '<li class="page-item"><a class="page-link" href="#" data-page="' + (data.current_page + 1) + '">&raquo;</a></li>';
        }
        return links + '</ul></nav>';
    }

    var timer;
    $('.search-input').on('input', function () {
        var query = this.value;
        clearTimeout(timer);
        timer = setTimeout(function () {
            fetchCertificates(1, query);
        }, 250);
    });

    $(document).on('click', '.search-pagination .page-link', function (e) {
        e.preventDefault();
        fetchCertificates($(this).data('page'), $('.search-input').val());
    });
});
</script>
@endpush
