
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin Area</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    </head>
    <body>
        <section style="padding-top: 60px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="card">
                            <div class="card-header" ><center><h3>TÜV Austria BIC Certificate DB</h3></center>
                                <table>
                                    <tr>
                                        <td style="width: 16%"><a href="/add-certificate" class="btn btn-success">Add New Certificate</a></td>
                                        <form action="<?php echo e(route('certificate.adminSearch')); ?>">
                                        <td style="width: 50%"><input type="text" name="search" class="form-control" placeholder="Search Certificate ID or Student Name"></td>
                                        <td style="width: 8%"><button type="submit"  class="btn btn-success">Search</button></td>
                                        </form>
                                        <td style="width: 8%"><a href="/admin" class="btn btn-primary">Refresh</a></td>
                                        <td style="width: 10%"><a href="/imports-exports" class="btn btn-warning">Bulk Im/Ex</a></td>
                                        <td style="width: 8%"><a href="/logout" class="btn btn-danger">Log Out</a></td>
                                    </tr>

                                </table>

                            </div>
                            <div class="card-body">
                                <?php if(Session::has('post-deleted')): ?>
                                    <div class="alert-success" role="alert">
                                        <?php echo e(Session::get('post_deleted')); ?>

                                    </div>
                                <?php endif; ?>
                                <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Certificate ID</th>
                                        <th>Name</th>
                                        <th>Student ID</th>
                                        <th>Course Code</th>
                                        <th>Final Result</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $certificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certificate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($certificate->certificate_id); ?></td>
                                            <td><?php echo e($certificate->st_name); ?></td>
                                            <td><?php echo e($certificate->st_id); ?></td>
                                            <td><?php echo e($certificate->course_code); ?></td>
                                            <td><?php echo e($certificate->course_result); ?></td>
                                            <td>
                                                <a href="/delete-certificate/<?php echo e($certificate->id); ?>" class="btn btn-danger">Delete</a>
                                                <a href="/edit-certificate/<?php echo e($certificate->id); ?>" class="btn btn-success">Edit</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            </div>
                            <div class="card-footer">
                                <?php echo e($certificates->links()); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    </body>
</html>
<?php /**PATH D:\xampp\htdocs\certificateverify\Project\resources\views/certificates.blade.php ENDPATH**/ ?>