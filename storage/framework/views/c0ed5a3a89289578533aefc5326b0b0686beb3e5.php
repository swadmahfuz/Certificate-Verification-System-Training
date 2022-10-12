<!DOCTYPE html>
<html>
<head>
    <meta name="robots" content="noindex">
    <title>Bulk Imports/Exports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
   
<div class="container">
    <div class="card bg-light mt-3">
        <div class="card-header">
            Import Export Excel to database
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="file" name="file" class="form-control">
                <br>
                <button class="btn btn-success">Import Data</button>
                <a class="btn btn-warning" href="<?php echo e(route('export')); ?>">Export Data</a>
                <a href="/admin" class="btn btn-primary">Admin Panel</a>
            </form>
        </div>
    </div>
</div>
   
</body>
</html><?php /**PATH D:\xampp\htdocs\certificateverify\Project\resources\views/imports-exports.blade.php ENDPATH**/ ?>