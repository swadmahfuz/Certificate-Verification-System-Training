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
                    <div class="col-md-8 offset-md-2">
                        <div class="card">
                            <div class="card-header" style="text-align: center">
                                <h3 >Add Certificate Data</h3> <br> 
                                <a href="dashboard" class="btn btn-success">Back to Dashboard</a>
                            </div>
                            <div class="card-body">
                                
                                <form class="col s12" method="POST" action="<?php echo e(route('certificate.create')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <div class="form-group">
                                        <label for="certificate_number">Certificate Number</label>
                                        <?php $__errorArgs = ['certificate_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger"><?php echo e($message); ?></span> <br> 
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        <input type="text" name="certificate_number" class="form-control" placeholder="Enter Certificate Number" value="<?php echo e(old('certificate_number')); ?>">
                                        <br>
                                        
                                        <label for="participant_name">Participant Name</label>
                                        <input type="text" name="participant_name" class="form-control" placeholder="Enter Participant Name" value="<?php echo e(old('participant_name')); ?>">
                                        <br>

                                        <label for="passport_nid">NID/Passport Number</label>
                                        <?php $__errorArgs = ['passport_nid'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger"><?php echo e($message); ?></span> <br> 
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        <input type="text" name="passport_nid" class="form-control" placeholder="Enter NID/Passport Number" value="<?php echo e(old('passport_nid')); ?>">
                                        <br>

                                        <label for="driving_license">Driving License</label>
                                        <input type="text" name="driving_license" class="form-control" placeholder="Enter Driving License Number (if available)" value="<?php echo e(old('driving_license')); ?>">
                                        <br>

                                        <label for="company">Company</label>
                                        <input type="text" name="company" class="form-control" placeholder="Enter Company Name" value="<?php echo e(old('company')); ?>">
                                        <br>

                                        <label for="training_name">Training Name</label>
                                        <?php $__errorArgs = ['training_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger"><?php echo e($message); ?></span> <br> 
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        <input type="text" name="training_name" class="form-control" placeholder="Enter Training Name" value="<?php echo e(old('training_name')); ?>">
                                        <br>

                                        <label for="trainer">Trainer Name</label>
                                        <?php $__errorArgs = ['trainer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger"><?php echo e($message); ?></span> <br> 
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        <input type="text" name="trainer" class="form-control" placeholder="Enter Trainer Name" value="<?php echo e(old('trainer')); ?>">
                                        <br>

                                        <label for="training_date">Training Date</label>
                                        <?php $__errorArgs = ['training_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger"><?php echo e($message); ?></span> <br> 
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        <input type="date" name="training_date" class="form-control" placeholder="Enter Training Date" value="<?php echo e(old('training_date')); ?>">
                                        <br>

                                        <label for="issue_date">Issue Date</label>
                                        <?php $__errorArgs = ['issue_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger"><?php echo e($message); ?></span> <br> 
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        <input type="date" name="issue_date" class="form-control" placeholder="Enter Certificate Issue Date" value="<?php echo e(old('issue_date')); ?>">
                                        <br>

                                        <label for="expiry_date">Expiry Date</label>
                                        <?php $__errorArgs = ['expiry_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger"><?php echo e($message); ?></span> <br> 
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        <input type="date" name="expiry_date" class="form-control" placeholder="Enter Certificate Expiry Date" value="<?php echo e(old('expiry_date')); ?>">
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
<?php /**PATH D:\xampp\htdocs\verify-cert\resources\views/add-certificate.blade.php ENDPATH**/ ?>