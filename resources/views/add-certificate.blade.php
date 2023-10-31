<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Add Certificate</title>
</head>
    <body background="images/tuv-login-background1.jpg">
        <section style="padding-top: 60px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-7 offset-md-3">
                        <div class="card">
                            <div class="card-header" >
                                <center><h3>Add Certificate Information</h3></center>
                                <center>
                                    <a href="./dashboard" class="btn btn-primary">Go back to Dashboard</a> 
                                    <h6 style="text-align: right">* Required fields</h6>
                                </center> 
                            </div>
                            <div class="card-body">
                                
                                <form class="col s12" method="POST" action="{{ route('certificate.create') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="certificate_number">Certificate Number*</label>
                                        @error('certificate_number')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="text" name="certificate_number" class="form-control" placeholder="Enter Certificate Number" value="TUVAT/CERT/{{ $currentYear }}/{{ $currentMonthDay }}/">
                                        <br>
                                        
                                        <label for="participant_name">Participant Name*</label>
                                        @error('participant_name')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="text" name="participant_name" class="form-control" placeholder="Enter Participant Name" value="{{ old('participant_name') }}">
                                        <br>

                                        <label for="passport_nid">NID/Passport Number*</label>
                                        @error('passport_nid')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="text" name="passport_nid" class="form-control" placeholder="Enter NID/Passport Number" value="{{ old('passport_nid') }}">
                                        <br>

                                        <label for="driving_license">Driving License</label>
                                        <input type="text" name="driving_license" class="form-control" placeholder="Enter Driving License Number (if available)" value="{{ old('driving_license') }}">
                                        <br>

                                        <label for="company">Company</label>
                                        <input type="text" name="company" class="form-control" placeholder="Enter Company Name" value="{{ old('company') }}">
                                        <br>

                                        <label for="training_name">Training Name*</label>
                                        @error('training_name')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="text" name="training_name" class="form-control" placeholder="Enter Training Name" value="{{ old('training_name') }}">
                                        <br>

                                        <label for="location">Training Location*</label>
                                        @error('location')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="text" name="location" class="form-control" placeholder="Enter Training Location" value="TUV Austria - BD Office, Dhaka, Bangladesh.">
                                        <br>

                                        <label for="trainer">Trainer Name*</label>
                                        @error('trainer')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="text" name="trainer" class="form-control" placeholder="Enter Trainer Name" value="{{ old('trainer') }}">
                                        <br>

                                        <label for="training_date">Training Date*</label>
                                        @error('training_date')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="date" name="training_date" class="form-control" placeholder="Enter Training Date" value="{{ old('training_date') }}">
                                        <br>

                                        <label for="issue_date">Issue Date*</label>
                                        @error('issue_date')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="date" name="issue_date" class="form-control" placeholder="Enter Certificate Issue Date" value="{{ old('issue_date') }}">
                                        <br>

                                        <label for="expiry_date">Expiry Date*</label>
                                        @error('expiry_date')
                                            <span class="text-danger">{{$message}}</span> <br> 
                                        @enderror
                                        <input type="date" name="expiry_date" class="form-control" placeholder="Enter Certificate Expiry Date" value="{{ old('expiry_date') }}">
                                        <br>
                                        
                                        <center><button type="submit" class="btn btn-success">Add Details</button></center>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </body>
</html>
