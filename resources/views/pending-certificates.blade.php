@extends('layouts.admin')

@section('title', 'Pending Certificates')

@section('content')
<div class="page-heading">
    <div>
        <h1>Pending Certificates</h1>
        <p>
            @if(($assignment ?? null) === 'review')
                Showing certificates assigned to you for review.
            @elseif(($assignment ?? null) === 'approval')
                Showing certificates assigned to you for approval.
            @elseif(($assignment ?? null) === 'mine')
                Showing all certificates assigned to you.
            @else
                Review and approve certificates assigned in the workflow.
            @endif
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <form action="{{ route('bulkReview') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-info btn-sm" data-confirm="Mark all certificates assigned to you for review as Reviewed?">
                <i class="fa-solid fa-thumbs-up me-1"></i> Mark My Assigned as Reviewed
            </button>
        </form>
        <form action="{{ route('bulkApprove') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success btn-sm" data-confirm="Mark all certificates assigned to you for approval as Approved?">
                <i class="fa-solid fa-check-double me-1"></i> Mark My Assigned as Approved
            </button>
        </form>
    </div>
</div>

<div class="filter-chips mb-3">
    <a class="filter-chip {{ empty($assignment) ? 'active' : '' }}" href="{{ route('pendingCertificates') }}">All pending</a>
    <a class="filter-chip {{ ($assignment ?? null) === 'mine' ? 'active' : '' }}" href="{{ route('pendingCertificates', ['assignment' => 'mine']) }}">Assigned to me</a>
    <a class="filter-chip {{ ($assignment ?? null) === 'review' ? 'active' : '' }}" href="{{ route('pendingCertificates', ['assignment' => 'review']) }}">My reviews</a>
    <a class="filter-chip {{ ($assignment ?? null) === 'approval' ? 'active' : '' }}" href="{{ route('pendingCertificates', ['assignment' => 'approval']) }}">My approvals</a>
</div>

<section class="admin-card">
    <div class="admin-card-header">
        <h2>Certificates Pending Review/Approval</h2>
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
                    <th>Reviewer</th>
                    <th>Approver</th>
                    <th>Trg Date</th>
                    <th>Issue Date</th>
                    <th>Exp. Date</th>
                    <th>Status</th>
                    <th>QR</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="p-3 border-top search-pagination">{{ $certificates->links() }}</div>
</section>
@endsection

@push('scripts')
<script>
$(function () {
    var currentUserId = {{ Auth::id() ?? 0 }};
    var csrfToken = @json(csrf_token());
    var viewBase = @json(url('/view-certificate'));
    var editBase = @json(url('/edit-certificate'));
    var deleteBase = @json(url('/delete-certificate'));
    var reviewBase = @json(url('/review-certificate'));
    var approveBase = @json(url('/approve-certificate'));
    var verifyBase = @json(url('/'));
    var assignmentFilter = @json($assignment ?? null);

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : value).html();
    }

    function formatDate(date) {
        if (!date) return 'N/A';
        var d = new Date(date);
        if (isNaN(d.getTime())) return 'N/A';
        return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
    }

    function postButton(url, title, iconClass, confirmMsg, danger, method) {
        return '<form action="' + url + '" method="POST" class="d-inline">' +
            '<input type="hidden" name="_token" value="' + csrfToken + '">' +
            (method ? '<input type="hidden" name="_method" value="' + method + '">' : '') +
            '<button type="submit" class="' + (danger ? 'danger' : '') + '" title="' + title + '" data-confirm="' + confirmMsg + '">' +
            '<i class="' + iconClass + '"></i></button></form>';
    }

    function fetchCertificates(page, userInput) {
        page = page || 1;
        userInput = userInput || '';
        $.ajax({
            url: @json(route('liveSearchPending')),
            data: { userInput: userInput, page: page, assignment: assignmentFilter },
            dataType: 'json',
            beforeSend: function () {
                $('.search-result tbody').html('<tr><td colspan="14" class="text-center text-muted py-4">Searching...</td></tr>');
            },
            success: function (res) {
                var html = '';
                $.each(res.data.data, function (i, d) {
                    var canReview = (d.status === 'Pending Review' || d.status === 'Pending') &&
                        Number(d.review_by_id) === Number(currentUserId);
                    var canApprove = (d.status === 'Pending Approval' || d.status === 'Reviewed') &&
                        Number(d.approval_by_id) === Number(currentUserId);
                    var title = (d.internal_audit_training === true || d.internal_audit_training == 1)
                        ? 'Internal Auditor - ' + d.training_name
                        : d.training_name;
                    var verification = verifyBase + '?search=' + encodeURIComponent(d.certificate_number);
                    var actions = '<div class="table-actions">' +
                        '<a href="' + viewBase + '/' + d.id + '" target="_blank" rel="noopener noreferrer" title="View"><i class="fa-solid fa-circle-info"></i></a>' +
                        '<a href="' + editBase + '/' + d.id + '" target="_blank" rel="noopener noreferrer" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>' +
                        postButton(deleteBase + '/' + d.id, 'Delete', 'fa-solid fa-trash', 'Delete this certificate?', true, 'DELETE') +
                        (canReview ? postButton(reviewBase + '/' + d.id, 'Mark as Reviewed', 'fa-solid fa-thumbs-up', 'Mark this certificate as Reviewed?') : '') +
                        (canApprove ? postButton(approveBase + '/' + d.id, 'Mark as Approved', 'fa-solid fa-check', 'Mark this certificate as Approved?') : '') +
                        '</div>';

                    html += '<tr>' +
                        '<td>' + (i + 1 + (res.data.current_page - 1) * res.data.per_page) + '</td>' +
                        '<td>' + escapeHtml(d.certificate_number) + '</td>' +
                        '<td>' + escapeHtml(d.participant_name) + '</td>' +
                        '<td>' + escapeHtml(d.company || 'N/A') + '</td>' +
                        '<td>' + escapeHtml(title) + '</td>' +
                        '<td>' + escapeHtml(d.trainer) + '</td>' +
                        '<td>' + escapeHtml(d.review_by || 'N/A') + '</td>' +
                        '<td>' + escapeHtml(d.approval_by || 'N/A') + '</td>' +
                        '<td>' + formatDate(d.training_date) + '</td>' +
                        '<td>' + formatDate(d.issue_date) + '</td>' +
                        '<td>' + formatDate(d.expiry_date) + '</td>' +
                        '<td><span class="status-pill">' + escapeHtml(d.status) + '</span></td>' +
                        '<td><img width="38" height="38" src="https://api.qrserver.com/v1/create-qr-code/?size=76x76&data=' + encodeURIComponent(verification) + '"></td>' +
                        '<td>' + actions + '</td></tr>';
                });
                $('.search-result tbody').html(html || '<tr><td colspan="14" class="text-center text-muted py-4">No matching certificates found.</td></tr>');
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

    fetchCertificates();
});
</script>
@endpush
