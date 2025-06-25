<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TÜV Austria BIC CVS | Pending Certificates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <style>
        .container { max-width: 99%; }
        .table-container { overflow-x: auto; }
        .table-striped tbody td, .table-striped thead th {
            vertical-align: middle;
        }
        .table-striped thead th {
            text-align: left;
            position: sticky;
            top: 0;
            background-color: rgb(243, 243, 243);
            border-right: 1px solid #dee2e6;
        }
        .table-striped thead th:last-child { border-right: none; }
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn i { font-size: 16px; }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table-striped { font-size: 11px; }
    </style>
</head>
<body background="images/tuv-login-background1.jpg">
<section style="padding-top: 60px;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="text-end">Logged in User: <b>{{ auth()->user()->name }} ({{ auth()->user()->designation }})</b></h6>
                        <h3 class="text-center mb-3">TÜV Austria BIC - Training Certificate Verification System (CVS)</h3>
                        <table class="mx-auto mb-3" style="width: 80%;">
                            <tr>
                                <td><a href="add-certificate" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i> Add New Certificate</a></td>
                                <td><a href="dashboard" class="btn btn-primary"><i class="fa-solid fa-arrow-left me-1"></i> Dashboard</a></td>
                                <td><a href="imports-exports" class="btn btn-warning"><i class="fa-solid fa-file-import me-1"></i> Import/Export</a></td>
                                <td><a href="logout" class="btn btn-danger"><i class="fa-solid fa-right-from-bracket me-1"></i> Log Out</a></td>
                            </tr>
                        </table>
                        <table class="mx-auto mb-2" style="width: 35%;">
                            <tr><td><input type="text" class="form-control search-input" placeholder="Search Certificates"/></td></tr>
                        </table>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped search-result">
                            <thead>
                                <tr><th colspan="12" class="text-center fs-5 fw-bold">Certificates Pending Review/Approval</th></tr>
                                <tr>
                                    <th>Sl.</th>
                                    <th>Certificate ID</th>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Training</th>
                                    <th>Trainer</th>
                                    <th>Trg Date</th>
                                    <th>Issue Date</th>
                                    <th>Exp. Date</th>
                                    <th>Status</th>
                                    <th>QR Code</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="card-footer">{{ $certificates->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        function fetchCertificates(page = 1, userInput = '') {
            $.ajax({
                url: "{{ url('live-search-pending') }}",
                data: { userInput, page },
                dataType: 'json',
                beforeSend: function() {
                    $(".search-result tbody").html('<tr><td colspan="12">Searching...</td></tr>');
                },
                success: function(res) {
                    let html = '';
                    $.each(res.data.data, function(i, d) {
                        let url = "{{ url('') }}" + "?search=" + d.certificate_number;
                        html += '<tr>' +
                                '<td>' + (i + 1 + (res.data.current_page - 1) * res.data.per_page) + '.</td>' +
                                '<td>' + d.certificate_number + '</td>' +
                                '<td>' + d.participant_name + '</td>' +
                                '<td>' + d.company + '</td>' +
                                '<td>' + d.training_name + '</td>' +
                                '<td>' + d.trainer + '</td>' +
                                '<td>' + formatDate(d.training_date) + '</td>' +
                                '<td>' + formatDate(d.issue_date) + '</td>' +
                                '<td>' + (d.expiry_date ? formatDate(d.expiry_date) : 'N/A') + '</td>' +
                                '<td>' + d.status + '</td>' +
                                '<td><img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=' + encodeURIComponent(url) + '"/></td>' +
                                '<td>' +
                                    '<a href="view-certificate/' + d.id + '" target="_blank"><i class="fa-solid fa-circle-info"></i></a> ' +
                                    '<a href="edit-certificate/' + d.id + '" target="_blank"><i class="fa-solid fa-pen-to-square"></i></a> ' +
                                    '<a href="delete-certificate/' + d.id + '"><i class="fa-solid fa-trash"></i></a> ' +
                                    (d.status === 'Pending Review' ? '<a href="review-certificate/' + d.id + '"><i class="fa-solid fa-thumbs-up"></i></a> ' : '') +
                                    (d.status === 'Pending Approval' ? '<a href="approve-certificate/' + d.id + '"><i class="fa-solid fa-check"></i></a>' : '') +
                                '</td>' +
                            '</tr>';
                    });
                    $(".search-result tbody").html(html || '<tr><td colspan="12">No matching certificates found.</td></tr>');
                    $('.pagination').remove();
                    $('.card-body').append(generatePaginationLinks(res.data));
                }
            });
        }

        function formatDate(date) {
            const d = new Date(date);
            return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
        }

        function generatePaginationLinks(data) {
            let links = '<nav><ul class="pagination">';
            if (data.current_page > 1) {
                links += '<li class="page-item"><a class="page-link" href="#" data-page="' + (data.current_page - 1) + '">&laquo;</a></li>';
            }
            for (let i = 1; i <= data.last_page; i++) {
                links += '<li class="page-item' + (i === data.current_page ? ' active' : '') + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
            }
            if (data.current_page < data.last_page) {
                links += '<li class="page-item"><a class="page-link" href="#" data-page="' + (data.current_page + 1) + '">&raquo;</a></li>';
            }
            return links + '</ul></nav>';
        }

        $('.search-input').on('keyup', function() {
            fetchCertificates(1, $(this).val());
        });

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            fetchCertificates($(this).data('page'), $('.search-input').val());
        });

        fetchCertificates();
    });
</script>
</body>
<footer>@include('layouts.footer')</footer>
</html>