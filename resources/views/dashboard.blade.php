<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <!-- Include jQuery for AJAX functionality (Required for live search)-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        <style>
            .container {
                max-width: 75%;
            }
            .table-container {
                overflow-x: auto;
            }
            .table-striped tbody td, .table-striped thead th {
                vertical-align: middle; /* Centers the content vertically in table cells */
            }
            .table-striped thead th {
                text-align: left; /* Centers the text horizontally in table headers */
                position: sticky;
                top: 0; /* Keeps the header at the top */
                background-color: rgb(243, 243, 243); /* Non-transparent background */
                border-right: 1px solid #dee2e6; /* Adds a border to the right of each header cell */
            }
            .table-striped thead th:last-child {
                border-right: none; /* Removes the border for the last header cell */
            }
        </style>
    </head>
    <body background="images/tuv-login-background1.jpg">
        <section style="padding-top: 60px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="card">
                            <div class="card-header" style="padding-top: 20px; padding-bottom: 20px;">
                                <center><h3>TÜV Austria BIC Training Certificate DB</h3></center>
                                <h6 style="text-align: right; margin-bottom: 10px;">Logged in User: <b>{{ auth()->user()->name }} ({{ auth()->user()->designation }})</b></h6>
                                <table style="width:90%; margin-left:5%; margin-right:10%;">
                                    <tr>
                                        <td ><a href="add-certificate" class="btn btn-success">Add New Certificate</a></td>
                                        {{-- <form action="{{ route('certificate.adminSearch') }}">
                                            <td style="width: 40%"><input type="text" name="search" class="form-control" placeholder="Search Certificates"></td>
                                            <td ><button type="submit"  class="btn btn-success">Search</button></td>
                                        </form> --}}
                                        <td> <input type="text" class="form-control my-1 search-input" placeholder="Search Certificates"/> </td>
                                        <td ><a href="dashboard" class="btn btn-primary">Refresh</a></td>
                                        <td ><a href="imports-exports" class="btn btn-warning">Import/Export</a></td>
                                        <td ><a href="logout" class="btn btn-danger">Log Out</a></td>
                                    </tr>
                                </table>

                            </div>
                            <div class="card-body">
                                @if (Session::has('post-deleted'))
                                    <div class="alert-success" role="alert">
                                        {{ Session::get('post_deleted') }}
                                    </div>
                                @endif
                                <table class="table table-striped search-result">
                                    <thead>
                                        <tr>
                                            <th>Sl.</th>
                                            <th>Certificate ID</th>
                                            <th>Name</th>
                                            {{-- <th>Passport/NID</th>
                                            <th>DL</th> --}}
                                            <th>Company</th>
                                            <th>Training</th>
                                            <th>Trainer</th>
                                            <th>Trg Date</th>
                                            <th>Issue Date</th>
                                            <th>Exp. Date</th>
                                            <th>QR Code</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Get current page number and continue sl. in next page -->
                                        @php
                                            $currentPage = $certificates->currentPage();
                                            $perPage = $certificates->perPage();
                                            $offset = ($currentPage - 1) * $perPage;
                                        @endphp
                                        @foreach ($certificates as $certificate)
                                            <tr>
                                                <td>{{ $loop->iteration + $offset }}.</td> <!-- continue sl. from previous page -->
                                                <td>{{ $certificate->certificate_number }}</td>
                                                <td>{{ $certificate->participant_name }}</td>
                                                {{-- <td>{{ $certificate->passport_nid }}</td>
                                                <td>{{ $certificate->driving_license }}</td> --}}
                                                <td>{{ $certificate->company }}</td>
                                                <td>{{ $certificate->training_name }}</td>
                                                <td>{{ $certificate->trainer }}</td>
                                                <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->training_date)->format('d-m-Y') }}</td> 
                                                <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->issue_date)->format('d-m-Y') }}</td>
                                                <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->expiry_date)->format('d-m-Y') }}</td>
                                                @php
                                                    $url = url('');  ///capture server url
                                                    $verification_url = $url.'?search='.$certificate->certificate_number;   ///concat server url with verification link and certificate number
                                                @endphp
                                                {{-- The code below uses goqr.me api to generate qr code image --}}
                                                <td> <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $verification_url }}"/> </td>
                                                <td>
                                                    {{-- Action buttons --}}
                                                    
                                                    {{-- using goqr.me api to generate qr code image link--}}
                                                    {{-- <a href="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $verification_url }}" target="_blank" style="margin-bottom: 5px"><i class="fa-solid fa-qrcode" title="Generate QR Code"></i></a> --}} {{-- Not required anymore as QR Code is displayed in the table --}}

                                                    <a href="view-certificate/{{ $certificate->id }}" style="margin-bottom: 5px" target="_blank"><i class="fa-solid fa-circle-info" title="View Certificate Details"></i></a>

                                                    <a href="edit-certificate/{{ $certificate->id }}" style="margin-bottom: 5px" target="_blank"><i class="fa-solid fa-pen-to-square" title="Edit Certificate Information"></i></a>

                                                    <a href="delete-certificate/{{ $certificate->id }}" style="margin-bottom: 5px"><i class="fa-solid fa-trash" title="Delete Certificate"></i></a>
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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                function fetchCertificates(page = 1, userInput = '') {
                    $.ajax({
                        url: "{{ url('live-search') }}",
                        data: {
                            userInput: userInput,
                            page: page
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            $(".search-result tbody").html('<tr><td colspan="11">Please wait while your query is being searched...</td></tr>');
                        },
                        success: function(res) {
                            var _html = '';
                            $.each(res.data.data, function(index, data) {
                                var verification_url = "{{ url('') }}" + "?search=" + data.certificate_number;
                                _html += '<tr>';
                                _html += '<td>' + (index + 1 + (res.data.current_page - 1) * res.data.per_page) + '.</td>'; // Assuming index + 1 as serial number
                                _html += '<td>' + data.certificate_number + '</td>';
                                _html += '<td>' + data.participant_name + '</td>';
                                _html += '<td>' + data.company + '</td>';
                                _html += '<td>' + data.training_name + '</td>';
                                _html += '<td>' + data.trainer + '</td>';
                                _html += '<td>' + formatDate(data.training_date) + '</td>';
                                _html += '<td>' + formatDate(data.issue_date) + '</td>';
                                _html += '<td>' + formatDate(data.expiry_date) + '</td>';
                                _html += '<td><img src="' + generateQRCode(verification_url) + '"/></td>';
                                _html += '<td>';
                                _html += '<a href="view-certificate/' + data.id + '" style="margin-bottom: 5px" target="_blank"><i class="fa-solid fa-circle-info" title="View Certificate Details"></i></a> ';
                                _html += '<a href="edit-certificate/' + data.id + '" style="margin-bottom: 5px" target="_blank"><i class="fa-solid fa-pen-to-square" title="Edit Certificate Information"></i></a> ';
                                _html += '<a href="delete-certificate/' + data.id + '" style="margin-bottom: 5px"><i class="fa-solid fa-trash" title="Delete Certificate"></i></a> ';
                                _html += '</td>';
                                _html += '</tr>';
                            });
                            $(".search-result tbody").html(_html); // Populate the tbody with new rows
        
                            // Update pagination links
                            $('.pagination').remove();
                            $('.card-body').append(generatePaginationLinks(res.data));
                        }
                    });
                }
        
                function formatDate(date) {
                    var d = new Date(date);
                    var day = ('0' + d.getDate()).slice(-2);
                    var month = ('0' + (d.getMonth() + 1)).slice(-2);
                    var year = d.getFullYear();
                    return day + '-' + month + '-' + year;
                }
        
                function generateQRCode(url) {
                    return 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + encodeURIComponent(url);
                }
        
                function generatePaginationLinks(data) {
                    var paginationLinks = '<nav class="pagination-container"><ul class="pagination">';
                    if (data.current_page > 1) {
                        paginationLinks += '<li class="page-item"><a class="page-link" href="#" data-page="' + (data.current_page - 1) + '">&laquo;</a></li>';
                    }
                    for (var i = 1; i <= data.last_page; i++) {
                        paginationLinks += '<li class="page-item' + (i === data.current_page ? ' active' : '') + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
                    }
                    if (data.current_page < data.last_page) {
                        paginationLinks += '<li class="page-item"><a class="page-link" href="#" data-page="' + (data.current_page + 1) + '">&raquo;</a></li>';
                    }
                    paginationLinks += '</ul></nav>';
                    return paginationLinks;
                }
        
                $(".search-input").on('keyup', function() {
                    var userInput = $(this).val();
                    fetchCertificates(1, userInput);
                });
        
                $(document).on('click', '.pagination a', function(e) {
                    e.preventDefault();
                    var page = $(this).attr('data-page');
                    var userInput = $('.search-input').val();
                    fetchCertificates(page, userInput);
                });
        
                // Initial fetch for the first page without any search input
                fetchCertificates();
            });
        </script>
    
    </body>
    <footer> @include('layouts.footer')  <!-- Including the footer Blade file --> </footer>
</html>
 