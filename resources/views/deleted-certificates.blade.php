<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>TÜV Austria BIC CVS | Deleted Certificates</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        <style>
            .container {
                max-width: 99%;
            }
            .table-container {
                overflow-x: auto;
            }
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
            .table-striped thead th:last-child {
                border-right: none;
            }
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
            .btn i {
                font-size: 16px;
            }
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            }
            .table-striped {
                font-size: 11px;
            }
        </style>
    </head>
    <body background="images/tuv-login-background1.jpg">
        <section style="padding-top: 60px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header" style="padding-top: 20px; padding-bottom: 0px;">
                                <h6 style="text-align: right; margin-bottom: 10px;">Logged in User: <b>{{ auth()->user()->name }} ({{ auth()->user()->designation }})</b></h6>
                                <center><h3 style="margin-bottom: 20px;">TÜV Austria BIC - Training Certificate Verification System (CVS)</h3></center>
                                <table style="width:80%; margin: auto;">
                                    <tr>
                                        <td><a href="add-certificate" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i> Add New Certificate</a></td>
                                        <td><a href="dashboard" class="btn btn-primary"><i class="fa-solid fa-arrows-rotate me-1"></i> Return to Dashboard</a></td>
                                        <td><a href="imports-exports" class="btn btn-warning"><i class="fa-solid fa-file-import me-1"></i> Import/Export Data</a></td>
                                        <td><a href="logout" class="btn btn-danger"><i class="fa-solid fa-right-from-bracket me-1"></i> Log Out</a></td>
                                    </tr>
                                </table>
                                <table style="width:35%; margin: auto;">
                                    <tr>
                                        <td><input type="text" class="form-control my-1 search-input" placeholder="Search Certificates"/></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="card-body">
                                @if (Session::has('post-deleted'))
                                    <div class="alert alert-success">{{ Session::get('post_deleted') }}</div>
                                @endif
                                <table class="table table-striped search-result">
                                    <thead>
                                        <th colspan="12" style="text-align: center; font-weight: bold; font-size: 1.5em;">Deleted Certificates</th>
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
                                    <tbody>
                                        @php
                                            $currentPage = $certificates->currentPage();
                                            $perPage = $certificates->perPage();
                                            $offset = ($currentPage - 1) * $perPage;
                                        @endphp
                                        @foreach ($certificates as $certificate)
                                            <tr>
                                                <td>{{ $loop->iteration + $offset }}.</td>
                                                <td>{{ $certificate->certificate_number }}</td>
                                                <td>{{ $certificate->participant_name }}</td>
                                                <td>{{ $certificate->company }}</td>
                                                <td>{{ $certificate->training_name }}</td>
                                                <td>{{ $certificate->trainer }}</td>
                                                <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->training_date)->format('d-m-Y') }}</td>
                                                <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->issue_date)->format('d-m-Y') }}</td>
                                                <td>
                                                    @if ($certificate->expiry_date)
                                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->expiry_date)->format('d-m-Y') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $certificate->status }}</td>
                                                @php
                                                    $url = url('');
                                                    $verification_url = $url.'?search='.$certificate->certificate_number;
                                                @endphp
                                                <td><img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $verification_url }}"/></td>
                                                <td>
                                                    <a href="view-certificate/{{ $certificate->id }}" target="_blank"><i class="fa-solid fa-circle-info" title="View"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                {{ $certificates->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script>
            $(document).ready(function() {
                function fetchCertificates(page = 1, userInput = '') {
                    $.ajax({
                        url: "{{ url('live-search-deleted') }}",
                        data: { userInput: userInput, page: page },
                        dataType: 'json',
                        beforeSend: function() {
                            $(".search-result tbody").html('<tr><td colspan="12">Searching...</td></tr>');
                        },
                        success: function(res) {
                            let _html = '';
                            $.each(res.data.data, function(index, data) {
                                const verification_url = "{{ url('') }}" + "?search=" + data.certificate_number;
                                _html += '<tr>';
                                _html += '<td>' + (index + 1 + (res.data.current_page - 1) * res.data.per_page) + '.</td>';
                                _html += '<td>' + data.certificate_number + '</td>';
                                _html += '<td>' + data.participant_name + '</td>';
                                _html += '<td>' + data.company + '</td>';
                                _html += '<td>' + data.training_name + '</td>';
                                _html += '<td>' + data.trainer + '</td>';
                                _html += '<td>' + formatDate(data.training_date) + '</td>';
                                _html += '<td>' + formatDate(data.issue_date) + '</td>';
                                _html += '<td>' + formatDate(data.expiry_date) + '</td>';
                                _html += '<td>' + data.status + '</td>';
                                _html += '<td><img src="' + generateQRCode(verification_url) + '"/></td>';
                                _html += '<td><a href="view-certificate/' + data.id + '" target="_blank"><i class="fa-solid fa-circle-info" title="View"></i></a></td>';
                                _html += '</tr>';
                            });
                            if (_html === '') {
                                _html = '<tr><td colspan="12" class="text-center">No matching certificates found.</td></tr>';
                            }
                            $(".search-result tbody").html(_html);
                            $('.pagination').remove();
                            $('.card-body').append(generatePaginationLinks(res.data));
                        }
                    });
                }

                function formatDate(date) {
                    if (!date) return 'N/A';
                    const d = new Date(date);
                    return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
                }

                function generateQRCode(url) {
                    return 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=' + encodeURIComponent(url);
                }

                function generatePaginationLinks(data) {
                    let paginationLinks = '<nav class="pagination-container"><ul class="pagination">';
                    if (data.current_page > 1) {
                        paginationLinks += '<li class="page-item"><a class="page-link" href="#" data-page="' + (data.current_page - 1) + '">&laquo;</a></li>';
                    }
                    for (let i = 1; i <= data.last_page; i++) {
                        paginationLinks += '<li class="page-item' + (i === data.current_page ? ' active' : '') + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
                    }
                    if (data.current_page < data.last_page) {
                        paginationLinks += '<li class="page-item"><a class="page-link" href="#" data-page="' + (data.current_page + 1) + '">&raquo;</a></li>';
                    }
                    paginationLinks += '</ul></nav>';
                    return paginationLinks;
                }

                $(".search-input").on('keyup', function() {
                    const userInput = $(this).val();
                    fetchCertificates(1, userInput);
                });

                $(document).on('click', '.pagination a', function(e) {
                    e.preventDefault();
                    const page = $(this).attr('data-page');
                    const userInput = $('.search-input').val();
                    fetchCertificates(page, userInput);
                });

                fetchCertificates();
            });
        </script>
    </body>
    <footer>@include('layouts.footer')</footer>
</html>
