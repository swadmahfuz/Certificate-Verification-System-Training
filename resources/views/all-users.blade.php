<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>TÜV Austria BIC CVS | All Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <style>
        body {
            font-size: 14px;
            background-color: #f8f9fa;
        }
        .container {
            padding-top: 60px;
        }
        .table th {
            background-color: #f1f1f1;
            text-align: center;
        }
        .table td, .table th {
            vertical-align: middle;
            font-size: 13px;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>All Registered Users</h2>
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Designation</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->department ?? 'N/A' }}</td>
                            <td>{{ $user->designation ?? 'N/A' }}</td>
                            </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('layouts.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
