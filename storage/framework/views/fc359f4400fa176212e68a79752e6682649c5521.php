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
    <body>
        <section style="padding-top: 60px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card">
                            <div class="card-header">Add Certificate Data <a href="/admin" class="btn btn-success">Back</a></div>
                            <div class="card-body">
                                <form method="POST" action="<?php echo e(route('certificate.create')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <div class="form-group">
                                        <label for="certificateID">Certificate ID</label>
                                        <input type="text" name="certid" class="form-control" placeholder="Enter Certificate ID">
                                        <label for="Name">Student Name</label>
                                        <input type="text" name="name" class="form-control" placeholder="Enter Student Name">
                                        <label for="St_id">Student ID</label>
                                        <input type="text" name="st_id" class="form-control" placeholder="Enter Student ID">
                                        <label for="Course_code">Course Code</label>
                                        <input type="text" name="course_code" class="form-control" placeholder="Enter Course Code">
                                        <label for="Course_result">Final Result</label>
                                        <input type="text" name="course_result" class="form-control" placeholder="Enter Final Result" value="PASS">
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
<?php /**PATH D:\xampp\htdocs\verify-cert\Project\resources\views/add-certificate.blade.php ENDPATH**/ ?>