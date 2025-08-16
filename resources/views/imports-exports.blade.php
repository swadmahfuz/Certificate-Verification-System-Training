<!DOCTYPE html>
<html>
<head>
    <meta name="robots" content="noindex">
    <title>Bulk Imports/Exports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-size: 13px;
        }
        .container {
            max-width: 99%;
        }
        .table-container {
            overflow-x: auto;
        }
        .table-striped tbody td, .table-striped thead th {
            vertical-align: middle;
        }
        .table-striped thead th {
            text-align: left;
            position: sticky;
            top: 0;
            background-color: rgb(243, 243, 243);
            border-right: 1px solid #dee2e6;
        }
        .table-striped thead th:last-child {
            border-right: none;
        }
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn i {
            font-size: 14px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body background="images/tuv-login-background1.jpg">

<div class="container">
    <div class="card bg-light mt-3">
        <div class="card-header" style="padding-top: 20px; padding-bottom: 20px;">
            <center>
                <h3 style="padding-bottom: 5px">TÜV Austria BIC CVS | Import/Export Training Certificate Data</h3>
                <table style="width:64%; margin: auto;">
                    <tr>
                        <td>
                            <a href="dashboard" class="btn btn-success d-flex align-items-center">
                                <i class="fa-solid fa-table-columns me-1"></i> Dashboard
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('export') }}" class="btn btn-primary d-flex align-items-center">
                                <i class="fa-solid fa-file-export me-1"></i> Export Database
                            </a>
                        </td>
                        <td>
                            <a href="./downloads/TUVAT CVS Training - Data Import Template.xlsx" class="btn btn-info d-flex align-items-center">
                                <i class="fa-solid fa-download me-1"></i> Download Blank CSV File
                            </a>
                        </td>
                        <td>
                            <a href="./downloads/TUVAT CVS Training - Sample Data File.xlsx" class="btn btn-secondary d-flex align-items-center">
                                <i class="fa-solid fa-file-lines me-1"></i> Download Sample Data
                            </a>
                        </td>
                        <td>
                            <a href="logout" class="btn btn-danger d-flex align-items-center">
                                <i class="fa-solid fa-right-from-bracket me-1"></i> Log Out
                            </a>
                        </td>
                    </tr>
                </table>
            </center>
        </div>
        <div class="card-body">
            <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" class="form-control">
                <h6 style="text-align: right; font-style:italic; margin-top:5px">Please upload MS Excel sheet as per the given import template given above.</h6>
                <h6 style="text-align: right; font-style: italic; font-weight: bold; margin-top: 5px; color: red;">Do not change template formatting.</h6>
                <h6 style="text-align: right; font-style: italic; font-weight: bold; margin-top: 5px; color: red;">All dates in excel sheet must be given in YYYY-MM-DD Format.</h6>
                <h6 style="text-align: right; font-style: italic; font-weight: bold; margin-top: 5px; color: red;">Example: "20 May 2024" → "2024-05-20"</h6>
                <center><button class="btn btn-success" style="margin-top: 10px">Import Data</button></center>
                <br>
            </form>
        </div>
    </div>
</div>
</body>
<footer> @include('layouts.footer') </footer>
</html>
