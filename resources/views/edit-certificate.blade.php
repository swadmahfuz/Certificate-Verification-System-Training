<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Edit Certificate Details</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    </head>
    <body>
        <section style="padding-top: 60px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card">
                            <div class="card-header">
                                Edit Certificate Details
                                <a href="admin" class="btn btn-success">Back</a>
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
                                        <label for="certificateID">Certificate ID</label>
                                        <input type="text" name="certid" class="form-control" placeholder="Confirm or Update Certificate ID" value="{{ $certificate->certificate_id }}">
                                        <label for="Name">Student Name</label>
                                        <input type="text" name="name" class="form-control" placeholder="UpdateName" value="{{ $certificate->st_name }}">
                                        <label for="St_id">Student ID</label>
                                        <input type="text" name="st_id" class="form-control" placeholder="Update Student ID" value="{{ $certificate->st_id }}">
                                        <label for="Course_code">Course Code</label>
                                        <input type="text" name="course_code" class="form-control" placeholder="Update Course Code" value="{{ $certificate->course_code }}">
                                        <label for="Course_result">Course Result</label>
                                        <input type="text" name="course_result" class="form-control" placeholder="Update Final Result" value="{{ $certificate->course_result }}">
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
