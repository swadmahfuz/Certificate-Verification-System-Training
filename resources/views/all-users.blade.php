<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>TÜV Austria BIC CVS | All Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <style>
        body {
            background-color: #f8f9fa;
            font-size: 13px;
        }
        .container {
            max-width: 98%;
            padding-top: 60px;
        }
        .table th {
            background-color: #f1f1f1;
            text-align: center;
            vertical-align: middle;
        }
        .table td, .table th {
            font-size: 12px;
            vertical-align: middle;
        }
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn i {
            font-size: 14px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h3 {
            text-align: center;
        }
    </style>
</head>
<body background="images/tuv-login-background1.jpg">
<section>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h6 class="text-end">Logged in User: <b>{{ auth()->user()->name }} ({{ auth()->user()->designation }})</b></h6>
                <h3 class="mb-3">TÜV Austria BIC - Training Certificate Verification System (CVS)</h3>
                <table class="mx-auto mb-3" style="width: 85%;">
                    <tr>
                        <td><a href="dashboard" class="btn btn-primary"><i class="fa-solid fa-arrow-left me-1"></i> Dashboard</a></td>
                        <td><a href="add-certificate" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i> Add Certificate</a></td>
                        <td><a href="pending-certificates" class="btn btn-info d-flex align-items-center"><i class="fa-solid fa-clock me-1"></i> Pending Certificates</a></td>
                        <td><a href="imports-exports" class="btn btn-warning"><i class="fa-solid fa-file-import me-1"></i> Import/Export</a></td>
                        <td><a href="logout" class="btn btn-danger"><i class="fa-solid fa-right-from-bracket me-1"></i> Log Out</a></td>
                    </tr>
                </table>
            </div>
            <div class="card-body table-responsive">
                <h5 class="text-center mb-3">All Registered Users & Certificate Summary</h5>
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Total Created</th>
                            <th>Total Reviewed</th>
                            <th>Total Approved</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->department ?? 'N/A' }}</td>
                                <td>{{ $user->designation ?? 'N/A' }}</td>
                                <td>{{ $user->certificates_created_count }}</td>
                                <td>{{ $user->certificates_reviewed_count }}</td>
                                <td>{{ $user->certificates_approved_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@include('layouts.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
