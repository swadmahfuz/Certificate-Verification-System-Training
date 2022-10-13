<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <title>TÜV Austria BIC - Certificate Verification</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <style>
            .login-form {
                width: 400px;
                margin: 200px auto;
            }
            .login-form form {
                margin-bottom: 15px;
                background: #f7f7f7;
                box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
                padding: 30px;
            }
            .login-form h2 {
                margin: 0 0 15px;
            }
            .form-control, .btn {
                min-height: 38px;
                border-radius: 2px;
            }
            .btn {        
                font-size: 15px;
                font-weight: bold;
            }
        </style>
    </head>
    <body background="images/tuv-login-background1.jpg">
        <div class="login-form">
            <form action="<?php echo e(route('certificate.login')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <h2 class="text-center">Certificate Verification <br> Login</h2>     
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Username" name="username">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Password" name="password">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Log in</button>
                </div>    
            </form>
        </div>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\verify-cert\resources\views//login.blade.php ENDPATH**/ ?>