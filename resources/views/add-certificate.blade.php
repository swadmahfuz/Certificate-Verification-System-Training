<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Add Certificate</title>
</head>
    <body background="images/tuv-login-background1.jpg">
        <section style="padding-top: 60px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card">
                            <div class="card-header">Add Certificate Data <a href="dashboard" class="btn btn-success">Back</a></div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('certificate.create') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="certificate_number">Certificate Number</label>
                                        <input type="text" name="certificate_number" class="form-control" placeholder="Enter Certificate Number">
                                        <label for="participant_name">Participant Name</label>
                                        <input type="text" name="participant_name" class="form-control" placeholder="Enter Participant Name">
                                        <label for="passport_nid">NID/Passport Number</label>
                                        <input type="text" name="passport_nid" class="form-control" placeholder="Enter NID/Passport Number">
                                        <label for="driving_license">Driving License</label>
                                        <input type="text" name="driving_license" class="form-control" placeholder="Enter Driving License Number (if available)">
                                        <label for="company">Company</label>
                                        <input type="text" name="company" class="form-control" placeholder="Enter Company Name">
                                        <label for="training_name">Training Name</label>
                                        <input type="text" name="training_name" class="form-control" placeholder="Enter Training Name">
                                        <label for="trainer">Trainer Name</label>
                                        <input type="text" name="trainer" class="form-control" placeholder="Enter Trainer Name">
                                        <label for="training_date">Training Date</label>
                                        <input type="text" name="training_date" class="form-control" placeholder="Enter Training Date">
                                        <label for="issue_date">Issue Date</label>
                                        <input type="text" name="issue_date" class="form-control" placeholder="Enter Certificate Issue Date">
                                        <label for="expiry_date">Expiry Date</label>
                                        <input type="text" name="expiry_date" class="form-control" placeholder="Enter Certificate Expiry Date">
                                    </div>
                                        <button type="submit" class="btn btn-success">Add Details</button>
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
