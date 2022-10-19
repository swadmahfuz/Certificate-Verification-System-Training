<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Edit Certificate Details</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    </head>
    <body background="../images/tuv-login-background1.jpg">
        <section style="padding-top: 60px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card">
                            <div class="card-header">
                                Edit Certificate Details
                                <a href="../dashboard" class="btn btn-success">Back</a>
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
                                        <label for="certificate_number">Certificate Number</label>
                                        <input type="text" name="certificate_number" class="form-control" value="{{ $certificate->certificate_number }}">
                                        <label for="participant_name">Participant Name</label>
                                        <input type="text" name="participant_name" class="form-control" value="{{ $certificate->participant_name }}">
                                        <label for="passport_nid">NID/Passport Number</label>
                                        <input type="text" name="passport_nid" class="form-control" value="{{ $certificate->passport_nid }}">
                                        <label for="driving_license">Driving License</label>
                                        <input type="text" name="driving_license" class="form-control" value="{{ $certificate->driving_license }}">
                                        <label for="company">Company</label>
                                        <input type="text" name="company" class="form-control" value="{{ $certificate->company }}">
                                        <label for="training_name">Training Name</label>
                                        <input type="text" name="training_name" class="form-control" value="{{ $certificate->training_name }}">
                                        <label for="trainer">Trainer Name</label>
                                        <input type="text" name="trainer" class="form-control" value="{{ $certificate->trainer }}">
                                        <label for="training_date">Training Date</label>
                                        <input type="text" name="training_date" class="form-control" value="{{ $certificate->training_date }}">
                                        <label for="issue_date">Issue Date</label>
                                        <input type="text" name="issue_date" class="form-control" value="{{ $certificate->issue_date }}">
                                        <label for="expiry_date">Expiry Date</label>
                                        <input type="text" name="expiry_date" class="form-control" value="{{ $certificate->expiry_date }}">
                                    <button type="submit" class="btn btn-success">Update Certificate</button>
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
