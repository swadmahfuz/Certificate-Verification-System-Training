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
                                    <a href="../delete-certificate/<?php echo e($certificate->id); ?>" class="btn btn-danger">Delete Certificate</a>
                                    <h6 style="text-align: right">* Required fields</h6>
                                </center> 
                            </div>
                            <div class="card-body">
                                <?php if(Session::has('Edited details successflly')): ?>
                                    <div class="alert alert-success" role="alert">
                                        <?php echo e(Session::get('Details_Edited')); ?>

                                    </div>
                                <?php endif; ?>
                                <form action="<?php echo e(route('certificate.update')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <div class="form-group">
                                        <input type="hidden" name="id" value="<?php echo e($certificate->id); ?>">
                                        
                                        <label for="certificate_number">Certificate Number*</label>
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
                                        <input type="text" name="certificate_number" class="form-control" placeholder="Enter Certificate Number" value="<?php echo e($certificate->certificate_number); ?>">
                                        <br>

                                        <label for="participant_name">Participant Name*</label>
                                        <?php $__errorArgs = ['participant_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger"><?php echo e($message); ?></span> <br> 
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        <input type="text" name="participant_name" class="form-control" placeholder="Enter Participant Name" value="<?php echo e($certificate->participant_name); ?>">
                                        <br>

                                        <label for="passport_nid">NID/Passport Number*</label>
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
                                        <input type="text" name="passport_nid" class="form-control" placeholder="Enter NID/Passport Number" value="<?php echo e($certificate->passport_nid); ?>">
                                        <br>

                                        <label for="driving_license">Driving License</label>
                                        <input type="text" name="driving_license" class="form-control" placeholder="Enter Driving License Number (if available)" value="<?php echo e($certificate->driving_license); ?>">
                                        <br>

                                        <label for="company">Company</label>
                                        <input type="text" name="company" class="form-control" placeholder="Enter Company Name" value="<?php echo e($certificate->company); ?>">
                                        <br>

                                        <label for="training_name">Training Name*</label>
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
                                        <input type="text" name="training_name" class="form-control" placeholder="Enter Training Name" value="<?php echo e($certificate->training_name); ?>">
                                        <br>
                                        
                                        <label for="location">Training Location*</label>
                                        <?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger"><?php echo e($message); ?></span> <br> 
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        <input type="text" name="location" class="form-control" placeholder="Enter Training Location" value="<?php echo e($certificate->location); ?>">
                                        <br>


                                        <label for="trainer">Trainer Name*</label>
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
                                        <input type="text" name="trainer" class="form-control" placeholder="Enter Trainer Name" value="<?php echo e($certificate->trainer); ?>">
                                        <br>

                                        <label for="training_date">Training Date*</label>
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
                                        <input type="date" name="training_date" class="form-control" placeholder="Enter Training Date" value="<?php echo e($certificate->training_date); ?>">
                                        <br>

                                        <label for="issue_date">Issue Date*</label>
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
                                        <input type="date" name="issue_date" class="form-control" placeholder="Enter Certificate Issue Date" value="<?php echo e($certificate->issue_date); ?>">
                                        <br>

                                        <label for="expiry_date">Expiry Date*</label>
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
                                        <input type="date" name="expiry_date" class="form-control" placeholder="Enter Certificate Expiry Date" value="<?php echo e($certificate->expiry_date); ?>">
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
<?php /**PATH D:\xampp\htdocs\verify-cert\resources\views/edit-certificate.blade.php ENDPATH**/ ?>