@extends('layouts.admin')

@section('title', 'Certificates')

@section('content')
<div class="page-heading">
    <div>
        <h1>Certificates</h1>
        <p>Search, verify, and manage all training certificates.</p>
    </div>
    @canMutate
    <a class="btn btn-primary" href="{{ route('certificate.createForm') }}">
        <i class="fa-solid fa-plus me-1"></i> Add Certificate
    </a>
    @endcanMutate
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
    @canMutate
    <form id="bulk-action-form" method="POST" class="d-flex flex-wrap align-items-center gap-2 p-3 border-bottom">
        @csrf
        <div id="bulk-certificate-ids"></div>
        <span class="text-muted me-2"><strong id="selected-count">0</strong> selected</span>
        <button id="clear-selection" class="btn btn-outline-secondary btn-sm" type="button" disabled>
            <i class="fa-solid fa-xmark me-1"></i> Clear selection
        </button>
        <button class="btn btn-info btn-sm bulk-action-button" type="submit"
            formaction="{{ route('certificates.bulkReviewSelected') }}" data-action="review" disabled>
            <i class="fa-solid fa-thumbs-up me-1"></i> Review
        </button>
        <button class="btn btn-success btn-sm bulk-action-button" type="submit"
            formaction="{{ route('certificates.bulkApproveSelected') }}" data-action="approve" disabled>
            <i class="fa-solid fa-check me-1"></i> Approve
        </button>
        <button class="btn btn-outline-danger btn-sm bulk-action-button" type="submit"
            formaction="{{ route('certificates.bulkPdf') }}" data-action="pdf" disabled>
            <i class="fa-solid fa-file-zipper me-1"></i> Download PDFs
        </button>
        <button class="btn btn-danger btn-sm bulk-action-button" type="submit"
            formaction="{{ route('certificates.bulkDelete') }}" data-action="delete" disabled>
            <i class="fa-solid fa-trash me-1"></i> Delete
        </button>
    </form>
    @endcanMutate
    <div class="table-responsive">
        <table class="table table-hover admin-table search-result">
            <thead>
                <tr>
                    @canMutate
                    <th>
                        <input id="select-all-visible" class="form-check-input" type="checkbox"
                            aria-label="Select all visible certificates">
                    </th>
                    @endcanMutate
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
                        @canMutate
                        <td>
                            <input class="form-check-input certificate-select" type="checkbox"
                                value="{{ $certificate->id }}"
                                aria-label="Select certificate {{ $certificate->certificate_number }}">
                        </td>
                        @endcanMutate
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
                                <a href="{{ route('certificate.view', $certificate->id) }}" target="_blank" rel="noopener noreferrer" title="View"><i class="fa-solid fa-circle-info"></i></a>
                                @canMutate
                                <a href="{{ route('certificate.edit', $certificate->id) }}" target="_blank" rel="noopener noreferrer" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                @if($certificate->status === 'Approved' && $certificate->certificate_type && $certificate->trainer_id)
                                    <a class="danger" href="{{ route('certificate.generatePdf', $certificate->id) }}" title="Generate PDF"><i class="fa-solid fa-file-pdf"></i></a>
                                @endif
                                <form action="{{ route('certificate.delete', $certificate->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="danger" type="submit" title="Delete" data-confirm="Delete this certificate?"><i class="fa-solid fa-trash"></i></button>
                                </form>
                                @endcanMutate
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="text-center text-muted py-4">No certificates found.</td></tr>
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
    var timer;
    var csrfToken = @json(csrf_token());
    var viewBase = @json(url('/view-certificate'));
    var editBase = @json(url('/edit-certificate'));
    var pdfBase = @json(url('/generate-certificate-pdf'));
    var deleteBase = @json(url('/delete-certificate'));
    var selectionStorageKey = 'certificates.selectedIds';
    var pendingBulkAction = null;

    @if(session('bulk_action_completed'))
        sessionStorage.removeItem(selectionStorageKey);
    @endif

    var selectedIds = loadSelectedIds();

    function loadSelectedIds() {
        try {
            return new Set(JSON.parse(sessionStorage.getItem(selectionStorageKey) || '[]').map(String));
        } catch (error) {
            return new Set();
        }
    }

    function persistSelectedIds() {
        sessionStorage.setItem(selectionStorageKey, JSON.stringify(Array.from(selectedIds)));
    }

    function syncSelectionUi() {
        $('.certificate-select').each(function () {
            this.checked = selectedIds.has(String(this.value));
        });

        var visibleCheckboxes = $('.certificate-select');
        var selectedVisible = visibleCheckboxes.filter(':checked').length;
        var selectAll = $('#select-all-visible').get(0);
        if (selectAll) {
            selectAll.checked = visibleCheckboxes.length > 0 && selectedVisible === visibleCheckboxes.length;
            selectAll.indeterminate = selectedVisible > 0 && selectedVisible < visibleCheckboxes.length;
        }

        $('#selected-count').text(selectedIds.size);
        $('.bulk-action-button').prop('disabled', selectedIds.size === 0);
        $('#clear-selection').prop('disabled', selectedIds.size === 0);

        var hiddenInputs = Array.from(selectedIds).map(function (id) {
            return '<input type="hidden" name="certificate_ids[]" value="' + escapeHtml(id) + '">';
        }).join('');
        $('#bulk-certificate-ids').html(hiddenInputs);
    }

    $(document).on('change', '.certificate-select', function () {
        var id = String(this.value);
        if (this.checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
        }
        persistSelectedIds();
        syncSelectionUi();
    });

    $('#select-all-visible').on('change', function () {
        var shouldSelect = this.checked;
        $('.certificate-select').each(function () {
            var id = String(this.value);
            if (shouldSelect) {
                selectedIds.add(id);
            } else {
                selectedIds.delete(id);
            }
        });
        persistSelectedIds();
        syncSelectionUi();
    });

    $('#clear-selection').on('click', function () {
        selectedIds.clear();
        persistSelectedIds();
        syncSelectionUi();
    });

    $('.bulk-action-button').on('click', function () {
        pendingBulkAction = $(this).data('action');
    });

    $('#bulk-action-form').on('submit', function (event) {
        if (selectedIds.size === 0) {
            event.preventDefault();
            return;
        }

        var labels = {
            review: 'mark as reviewed',
            approve: 'approve',
            pdf: 'generate PDFs for',
            delete: 'delete'
        };
        var action = pendingBulkAction || 'process';
        var message = 'Are you sure you want to ' + (labels[action] || action) + ' ' +
            selectedIds.size + ' selected certificate(s)?';

        if (!window.confirm(message)) {
            event.preventDefault();
        }
        pendingBulkAction = null;
    });

    function fetchCertificates(page, userInput) {
        page = page || 1;
        userInput = userInput || '';
        $.ajax({
            url: @json(route('liveSearch')),
            data: { userInput: userInput, page: page },
            dataType: 'json',
            beforeSend: function () {
                $('.search-result tbody').html('<tr><td colspan="11" class="text-center text-muted py-4">Searching...</td></tr>');
            },
            success: function (res) {
                var html = '';
                $.each(res.data.data, function (i, item) {
                    var title = (item.internal_audit_training === true || item.internal_audit_training == 1)
                        ? 'Internal Auditor - ' + item.training_name
                        : item.training_name;
                    var verification = @json(url('/')) + '?search=' + encodeURIComponent(item.certificate_number);
                    var actions = '<div class="table-actions">' +
                        '<a href="' + viewBase + '/' + item.id + '" target="_blank" rel="noopener noreferrer" title="View"><i class="fa-solid fa-circle-info"></i></a>' +
                        '<a href="' + editBase + '/' + item.id + '" target="_blank" rel="noopener noreferrer" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>' +
                        (item.status === 'Approved' && item.certificate_type && item.trainer_id
                            ? '<a class="danger" href="' + pdfBase + '/' + item.id + '" title="Generate PDF"><i class="fa-solid fa-file-pdf"></i></a>'
                            : '') +
                        '<form action="' + deleteBase + '/' + item.id + '" method="POST">' +
                            '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                            '<input type="hidden" name="_method" value="DELETE">' +
                            '<button class="danger" type="submit" title="Delete" data-confirm="Delete this certificate?"><i class="fa-solid fa-trash"></i></button>' +
                        '</form></div>';

                    html += '<tr><td><input class="form-check-input certificate-select" type="checkbox" value="' +
                        item.id + '" aria-label="Select certificate ' + escapeHtml(item.certificate_number) + '"></td>' +
                        '<td>' + (i + 1 + (res.data.current_page - 1) * res.data.per_page) + '</td>' +
                        '<td>' + escapeHtml(item.certificate_number) + '</td>' +
                        '<td>' + escapeHtml(item.participant_name) + '</td>' +
                        '<td>' + escapeHtml(item.company || 'N/A') + '</td>' +
                        '<td>' + escapeHtml(title) + '</td>' +
                        '<td>' + escapeHtml(item.trainer) + '</td>' +
                        '<td>' + formatDate(item.issue_date) + '</td>' +
                        '<td><span class="status-pill">' + escapeHtml(item.status) + '</span></td>' +
                        '<td><img width="38" height="38" src="https://api.qrserver.com/v1/create-qr-code/?size=76x76&data=' + encodeURIComponent(verification) + '"></td>' +
                        '<td>' + actions + '</td></tr>';
                });
                $('.search-result tbody').html(html || '<tr><td colspan="11" class="text-center text-muted py-4">No matching certificates found.</td></tr>');
                $('.search-pagination').html(generatePaginationLinks(res.data));
                syncSelectionUi();
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

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : value).html();
    }
    function formatDate(value) {
        if (!value) return 'N/A';
        var parts = value.split('-');
        return parts.length === 3 ? parts[2] + '-' + parts[1] + '-' + parts[0] : value;
    }

    syncSelectionUi();
    fetchCertificates();
});
</script>
@endpush
