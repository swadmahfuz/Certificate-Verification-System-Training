<!DOCTYPE html>
<html>
<head>
    <meta name="robots" content="noindex">
    <title>Bulk Imports/Exports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body background="images/tuv-login-background1.jpg">
   
<div class="container">
    <div class="card bg-light mt-3">
        <div class="card-header" style="padding-top: 20px; padding-bottom: 20px;">
            <center>
                <h3 style="padding-bottom: 5px">Import/Export Certificate Data</h3>
                <a href="dashboard" class="btn btn-primary">Dashboard</a>
                <a class="btn btn-warning" href="<?php echo e(route('export')); ?>">Export Database</a>
                <a href="./downloads/Data Import Template.xlsx" class="btn btn-success" download="Import Template.xlsx">Download Data Import Template</a>
                <a href="logout" class="btn btn-danger">Log Out</a>
            </center>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="file" name="file" class="form-control"> 
                <h6 style="text-align: right; font-style:italic; margin-top:5px">Please upload excel sheet as per import tembplate given above.</h6>
                <center><button class="btn btn-success" style="margin-top: 10px">Import Data</button></center>
                <br>
            </form>
        </div>
    </div>
</div>
   
</body>
</html><?php /**PATH D:\xampp\htdocs\verify-cert\resources\views/imports-exports.blade.php ENDPATH**/ ?>