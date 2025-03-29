<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>View Certificate Information</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />     
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        <style>
            .btn {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 10px 15px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: bold;
                transition: all 0.3s ease;
                white-space: nowrap;
            }
            .btn i {
                font-size: 16px;
            }
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            }
            .btn-container {
                display: flex;
                justify-content: center;
                gap: 10px;
                flex-wrap: wrap;
            }
        </style>
    </head>
    <body background="../images/tuv-login-background1.jpg">
        <section style="padding-top: 60px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="card">
                            <div class="card-header" ><center><h3>TÜV Austria BIC CVS - Detailed Certificate Information</h3></center>
                                <center>
                                    <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;">
                                        <a href="../dashboard" class="btn btn-primary d-flex align-items-center">
                                            <i class="fa-solid fa-arrow-left me-1"></i> Go back to Dashboard
                                        </a>
                                        <a href="../add-certificate" class="btn btn-success d-flex align-items-center">
                                            <i class="fa-solid fa-plus me-1"></i> Add New Certificate
                                        </a>
                                        <a href="../edit-certificate/{{ $certificate->id }}" class="btn btn-warning d-flex align-items-center">
                                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit Certificate
                                        </a>  
                                        @if(Auth::check() && (Auth::user()->id == $certificate->review_by_id || Auth::user()->name == $certificate->review_by) && $certificate->status == 'Pending Review')
                                            <a href="{{ route('certificate.review', $certificate->id) }}" class="btn btn-info d-flex align-items-center">
                                                <i class="fa-solid fa-thumbs-up me-1"></i> Mark as Reviewed
                                            </a>
                                        @endif
                                        @if(Auth::check() && (Auth::user()->id == $certificate->approval_by_id || Auth::user()->name == $certificate->approval_by) && $certificate->status == 'Pending Approval')
                                            <a href="{{ route('certificate.approve', $certificate->id) }}" class="btn btn-success d-flex align-items-center">
                                                <i class="fa-solid fa-check me-1"></i> Mark as Approved
                                            </a>
                                        @endif
                                        <a href="../delete-certificate/{{ $certificate->id }}" class="btn btn-danger d-flex align-items-center">
                                            <i class="fa-solid fa-trash me-1"></i> Delete Certificate
                                        </a>
                                    </div>
                                </center> 
                            </div>
                            <div class="card-body">
                                <table class="table table-striped" style="width:60%; margin-left:20%; margin-right:20%; border: 1px solid black;">

                                    <tr>
                                        <th>Certificate Number</th>
                                        <td>{{ $certificate->certificate_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Certificate Validity</th>
                                        <td>
                                            @if (empty($certificate->expiry_date) || \Carbon\Carbon::now() <= \Carbon\Carbon::parse($certificate->expiry_date))
                                                <span style="color: green;">Certificate Valid! ✅</span>
                                            @else
                                                <span style="color: red;">Certificate Expired! ⚠️</span>
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
                                        <th>Training</th>
                                        <td>{{ $certificate->training_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Training Location</th>
                                        <td>{{ $certificate->location }}</td>
                                    </tr>
                                    <tr>
                                        <th>Trainer</th>
                                        <td>{{ $certificate->trainer }}</td>
                                    </tr>
                                    <tr>
                                        <th>Training Start Date</th>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->training_date)->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Training End Date</th>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->training_end)->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Issue Date</th>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->issue_date)->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Valid Till</th>
                                        @if (!empty($certificate->expiry_date))
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $certificate->expiry_date)->format('d M Y') }}</td>
                                        @else
                                            <td>No Expiry Date</td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <th>Review by</th>
                                        <td>{{ $certificate->review_by }}</td>
                                    </tr>
                                    <tr>
                                        <th>Approval by</th>
                                        <td>{{ $certificate->approval_by }}</td>
                                    </tr>
                                    <tr>
                                        <th>QR Code</th>
                                        @php
                                            $url = url('');  ///capture server url
                                            $verification_url = $url.'?search='.$certificate->certificate_number;   ///concat server url with verification link and certificate number
                                        @endphp
                                        {{-- The line below uses generate qr code image --}}
                                        <td><img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $verification_url }}"/></td>
                                    </tr>
                                    <tr>
                                        <th>Created by</th>
                                        <td>{{ $certificate->created_by }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created on</th>
                                        <td>{{ $certificate->created_at->format('d M Y \a\t H:i:s') }}</td>
                                    <tr>
                                        <th>Last Updated by</th>
                                        <td>{{ $certificate->updated_by }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated on</th>
                                        <td>
                                            <!-- Ensures it will only show when a user updates a certificate after uploading -->
                                            @if ($certificate->updated_by)
                                                {{ $certificate->updated_at->format('d M Y \a\t H:i:s') }} 
                                            @else
                                                
                                            @endif
                                        </td>
                                    </tr>   
                                    <tr>
                                        <th>Deleted by</th>
                                        <td>{{ $certificate->deleted_by }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="card-footer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    </body>
    @include('layouts.footer')  <!-- Including the footer Blade file -->
</html>
