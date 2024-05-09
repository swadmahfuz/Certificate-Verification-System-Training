<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Edit Certificate Details</title>
</head>
    <body background="../images/tuv-login-background1.jpg">
        <section style="padding-top: 60px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-7 offset-md-3">
                        <div class="card">
                            <div class="card-header" ><center><h3>Edit Certificate Information</h3></center>
                                <center>
                                    <a href="../dashboard" class="btn btn-primary">Go back to Dashboard</a> 
                                    <a href="../delete-certificate/{{ $certificate->id }}" class="btn btn-danger">Delete Certificate</a>
                                    <h6 style="text-align: right">* Required fields</h6>
                                </center> 
                            </div>
                            <div class="card-body">
                                @if (Session::has('Edited details successflly'))
                                    <div class="alert alert-success" role="alert">
                                        {{ Session::get('Details_Edited') }}
                                    </div>
                                @endif
                                <form action="{{ route('certificate.update') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <input type="hidden" name="id" value="{{ $certificate->id }}">
                                        
                                        <label for="certificate_number">Certificate Number*</label>
                                        @error('certificate_number')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="text" name="certificate_number" class="form-control" placeholder="Enter Certificate Number" value="{{ $certificate->certificate_number }}">
                                        <br>

                                        <label for="participant_name">Participant Name*</label>
                                        @error('participant_name')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="text" name="participant_name" class="form-control" placeholder="Enter Participant Name" value="{{ $certificate->participant_name }}">
                                        <br>

                                        <label for="passport_nid">NID/Passport Number*</label>
                                        @error('passport_nid')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="text" name="passport_nid" class="form-control" placeholder="Enter NID/Passport Number" value="{{ $certificate->passport_nid }}">
                                        <br>

                                        <label for="driving_license">Driving License</label>
                                        <input type="text" name="driving_license" class="form-control" placeholder="Enter Driving License Number (if available)" value="{{ $certificate->driving_license }}">
                                        <br>

                                        <label for="company">Company</label>
                                        <input type="text" name="company" class="form-control" placeholder="Enter Company Name" value="{{ $certificate->company }}">
                                        <br>

                                        <label for="training_name">Training Name*</label>
                                        @error('training_name')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="text" name="training_name" class="form-control" placeholder="Enter Training Name" value="{{ $certificate->training_name }}">
                                        <br>
                                        
                                        <label for="location">Training Location*</label>
                                        @error('location')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="text" name="location" class="form-control" placeholder="Enter Training Location" value="{{ $certificate->location }}">
                                        <br>


                                        <label for="trainer">Trainer Name*</label>
                                        @error('trainer')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="text" name="trainer" class="form-control" placeholder="Enter Trainer Name" value="{{ $certificate->trainer }}">
                                        <br>

                                        <label for="training_date">Training Start Date*</label>
                                        @error('training_date')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="date" name="training_date" class="form-control" placeholder="Enter Training Date" value="{{ $certificate->training_date }}">
                                        <br>

                                        <label for="training_end">Training End Date*</label>
                                        @error('training_end')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="date" name="training_end" class="form-control" placeholder="Enter Training End Date" value="{{ $certificate->training_end }}">
                                        <br>

                                        <label for="issue_date">Issue Date*</label>
                                        @error('issue_date')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="date" name="issue_date" class="form-control" placeholder="Enter Certificate Issue Date" value="{{ $certificate->issue_date }}">
                                        <br>

                                        <label for="expiry_date">Expiry Date*</label>
                                        @error('expiry_date')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="date" name="expiry_date" class="form-control" placeholder="Enter Certificate Expiry Date" value="{{ $certificate->expiry_date }}">
                                        <br>
                                        <center><button type="submit" class="btn btn-success">Update Certificate</button></center>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    </body>
</html>
