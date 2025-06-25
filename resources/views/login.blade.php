<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>TÜV Austria BIC CVS | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <style>
        body {
            background-image: url("images/tuv-login-background1.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            height: 100vh;
            font-size: 14px;
        }
        .login-container {
            max-width: 400px;
            margin: auto;
            padding-top: 10%;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .btn {
            font-weight: 600;
            border-radius: 6px;
        }
        .form-control {
            font-size: 14px;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card p-4">
            <h5 class="text-center mb-3">Certificate Verification System (CVS)<br> Login</h5>

            @if(session('error'))
                <div class="alert alert-danger text-center">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('certificate.login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Enter email" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Log in</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
