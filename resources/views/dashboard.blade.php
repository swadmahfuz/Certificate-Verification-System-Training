<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>TÜV Austria BIC CVS | Admin Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <!-- Include jQuery for AJAX functionality (Required for live search)-->
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        <style>
            .container { max-width: 99%; }
            .table-container { overflow-x: auto; }
            .table-striped tbody td, .table-striped thead th { vertical-align: middle; } /* Vertically centers the text in table cells */
            .table-striped thead th {
                text-align: left; /* Centers the text horizontally in table headers */
                position: sticky;
                top: 0; /* Keeps the header at the top */
                background-color: rgb(243, 243, 243); /* Non-transparent background */
                border-right: 1px solid #dee2e6; /* Adds a border to the right of each header cell */
            }
            .table-striped thead th:last-child { border-right: none; } /* Removes the right border from the last header cell */
            .table-striped { font-size: 11px; } /* Sets the font size for the table */
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
        </style>
    </head>
    <body background="images/tuv-login-background1.jpg">
        <section style="padding-top: 60px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="card">
                            <div class="card-header" style="padding-top: 20px; padding-bottom: 0px;">
                                <h6 style="text-align: right; margin-bottom: 10px;">Logged in User: <b>{{ auth()->user()->name }} ({{ auth()->user()->designation }})</b></h6>
                                <center><h3 style="margin-bottom: 20px;">TÜV Austria BIC - Training Certificate Verification System (CVS)</h3></center>
                                @php
                                    $currentDomain = request()->getHost();   // e.g., "training.example.com"
                                    $baseDomain = preg_replace('/^[^.]+\./', '', $currentDomain); // e.g., "example.com"
                                @endphp
                                <!-- The above code is used to capture the current subdomain and base domain, but will only work on cPanel not Local Host (XAMPP) -->
                                 <!-- The two buttons below will also only work if app is hosted on cPanel -->
                                <table style="width:80%; margin: auto;">
                                    <tr>
                                        <td>
                                            <a href="https://training.{{ $baseDomain }}/dashboard" class="btn btn-dark d-flex align-items-center" target="_blank">
                                                <i class="fa-solid fa-graduation-cap me-1"></i> Training CVS Portal
                                            </a>
                                        </td>
                                        <td>
                                            <a href="https://inspection.{{ $baseDomain }}/dashboard" class="btn btn-dark d-flex align-items-center" target="_blank">
                                                <i class="fa-solid fa-magnifying-glass me-1"></i> Inspection CVS Portal
                                            </a>
                                        </td>
                                        <td>
                                            <a href="https://calibration.{{ $baseDomain }}/dashboard" class="btn btn-dark d-flex align-items-center" target="_blank">
                                                <i class="fa-solid fa-wrench me-1"></i> Calibration CVS Portal
                                            </a>
                                        </td>
                                        <td>
                                            <a href="https://reports.{{ $baseDomain }}/dashboard" class="btn btn-dark d-flex align-items-center" target="_blank">
                                                <i class="fa-solid fa-file-lines me-1"></i> Reports CVS Portal
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                                <table style="width:80%; margin: auto;">
                                    <tr>
                                        <td>
                                            <a href="add-certificate" class="btn btn-success d-flex align-items-center">
                                                <i class="fa-solid fa-plus me-1"></i> Add New Certificate
                                            </a>
                                        </td>
                                        <td>
                                            <a href="dashboard" class="btn btn-primary d-flex align-items-center">
                                                <i class="fa-solid fa-arrows-rotate me-1"></i> Refresh 
                                            </a>
                                        </td>
                                        <td>
                                            <a href="pending-certificates" class="btn btn-info d-flex align-items-center">
                                                <i class="fa-solid fa-clock me-1"></i> Pending Certificates
                                            </a>
                                        </td>
                                        <td>
                                            <a href="imports-exports" class="btn btn-warning d-flex align-items-center">
                                                <i class="fa-solid fa-file-import me-1"></i> Import/Export Data
                                            </a>
                                        </td>
                                        <td>
                                            <a href="all-users" class="btn btn-secondary d-flex align-items-center">
                                                <i class="fa-solid fa-users me-1"></i> View All Users
                                            </a>
                                        <td>
                                            <a href="logout" class="btn btn-danger d-flex align-items-center">
                                                <i class="fa-solid fa-right-from-bracket me-1"></i> Log Out
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                                
                                <table style="width:35%; margin: auto;">
                                    <tr>
                                        <td> <input type="text" class="form-control my-1 search-input" placeholder="Search Certificates"/> </td>
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
                                        <th colspan="12" style="text-align: center; font-weight: bold; font-size: 1.5em;">All Certificates</th>
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
                                                <td>{{ $certificate->company }}</td>
                                                <td>{{ $certificate->training_name }}</td>
                                                <td>{{ $certificate->trainer }}</td>
                                                <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->training_date)->format('d-m-Y') }}</td> 
                                                <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->issue_date)->format('d-m-Y') }}</td>
                                                <td>
                                                    @if ($certificate->expiry_date)     <!-- Checks if expiration date is NULL -->
                                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->expiry_date)->format('d-m-Y') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $certificate->status }}</td>
                                                @php
                                                    $url = url('');  ///capture server url
                                                    $verification_url = $url.'?search='.$certificate->certificate_number;   ///concat server url with verification link and certificate number
                                                @endphp
                                                {{-- The code below uses goqr.me api to generate qr code image --}}
                                                <td> <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $verification_url }}"/> </td>
                                                <td>
                                                    {{-- Action buttons --}}
                                                    <a href="view-certificate/{{ $certificate->id }}" style="margin-bottom: 5px" target="_blank"><i class="fa-solid fa-circle-info" title="View Certificate Details"></i></a>
                                                    <a href="edit-certificate/{{ $certificate->id }}" style="margin-bottom: 5px" target="_blank"><i class="fa-solid fa-pen-to-square" title="Edit Certificate Information"></i></a>
                                                    <a href="delete-certificate/{{ $certificate->id }}" style="margin-bottom: 5px"><i class="fa-solid fa-trash" title="Delete Certificate"></i></a>
                                                    @if(Auth::check() && (Auth::user()->id == $certificate->review_by_id || Auth::user()->name == $certificate->review_by) && $certificate->status == 'Pending Review')
                                                        <a href="{{ route('certificate.review', $certificate->id) }}" style="margin-bottom: 5px">><i class="fa-solid fa-thumbs-up" title="Mark as Reviewed"></i></a>
                                                    @endif
                                                    @if(Auth::check() && (Auth::user()->id == $certificate->approval_by_id || Auth::user()->name == $certificate->approval_by) && $certificate->status == 'Pending Approval')
                                                        <a href="{{ route('certificate.approve', $certificate->id) }}" style="margin-bottom: 5px">><i class="fa-solid fa-check" title="Mark as Approved"></i></a>
                                                    @endif
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
                            $(".search-result tbody").html('<tr><td colspan="11">Searching...</td></tr>');
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
                                _html += '<td>' + data.status + '</td>';
                                _html += '<td><img src="' + generateQRCode(verification_url) + '"/></td>';
                                _html += '<td>';
                                _html += '<a href="view-certificate/' + data.id + '" style="margin-bottom: 5px" target="_blank"><i class="fa-solid fa-circle-info" title="View Certificate Details"></i></a> ';
                                _html += '<a href="edit-certificate/' + data.id + '" style="margin-bottom: 5px" target="_blank"><i class="fa-solid fa-pen-to-square" title="Edit Certificate Information"></i></a> ';
                                _html += '<a href="delete-certificate/' + data.id + '" style="margin-bottom: 5px"><i class="fa-solid fa-trash" title="Delete Certificate"></i></a> ';
                                
                                if ({{ Auth::check() }} && ({{ Auth::user()->id }} == data.review_by_id || "{{ Auth::user()->name }}" == data.review_by) && data.status == 'Pending Review') {
                                    _html += '<a href="' + "{{ url('') }}/review-certificate/" + data.id + '"><i class="fa-solid fa-thumbs-up" title="Mark as Reviewed"></i></a> ';
                                }
                                if ({{ Auth::check() }} && ({{ Auth::user()->id }} == data.approval_by_id || "{{ Auth::user()->name }}" == data.approval_by) && data.status == 'Pending Approval') {
                                    _html += '<a href="' + "{{ url('') }}/approve-certificate/" + data.id + '"><i class="fa-solid fa-check" title="Mark as Approved"></i></a> ';
                                }
                                
                                _html += '</td>';
                                _html += '</tr>';
                            });
                            if (_html === '') {
                                _html = '<tr><td colspan="11" class="text-center">No matching certificates found.</td></tr>';
                            }
                            $(".search-result tbody").html(_html); // Populate the tbody with new rows
        
                            // Update pagination links
                            $('.pagination').remove();
                            $('.card-body').append(generatePaginationLinks(res.data));
                        }
                    });
                }
        
                function formatDate(date) {
                    if (!date) return 'N/A';
                    var d = new Date(date);
                    var day = ('0' + d.getDate()).slice(-2);
                    var month = ('0' + (d.getMonth() + 1)).slice(-2);
                    var year = d.getFullYear();
                    return day + '-' + month + '-' + year;
                }
        
                function generateQRCode(url) {
                    return 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=' + encodeURIComponent(url);
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