@extends('layouts.admin')

@section('title', 'Import / Export')

@section('content')
<div class="page-heading">
    <div>
        <h1>Import / Export</h1>
        <p>Export certificate data or import records from the Excel template.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('export') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-file-export me-1"></i> Export Database
        </a>
        <a href="{{ asset('downloads/TUVAT CVS Training - Data Import Template.xlsx') }}" class="btn btn-info btn-sm">
            <i class="fa-solid fa-download me-1"></i> Blank Excel Template
        </a>
        <a href="{{ asset('downloads/TUVAT CVS Training - Sample Data File.xlsx') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-file-lines me-1"></i> Sample Data
        </a>
    </div>
</div>

@if(session('import_error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('import_error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<section class="admin-card">
    <div class="admin-card-header">
        <h2>Import Certificate Data</h2>
    </div>
    <div class="admin-card-body">
        <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="import-file">Excel file</label>
                <input id="import-file" type="file" name="file" class="form-control" required>
            </div>
            <ul class="small text-muted mb-3">
                <li>Upload an MS Excel sheet matching the import template.</li>
                <li>Required columns include <strong>certificate_type</strong>, <strong>trainer_email</strong>, and <strong>signatory_email</strong>.</li>
                <li>Allowed certificate types: Certificate, Certificate of Achievement, Certificate of Competency, Certificate of Attendance.</li>
                <li>Trainer email must match an active trainer. Signatory email is optional.</li>
                <li>Do not change template formatting. All dates must use <strong>YYYY-MM-DD</strong>.</li>
            </ul>
            <button class="btn btn-success" type="submit">
                <i class="fa-solid fa-file-import me-1"></i> Import Data
            </button>
        </form>
    </div>
</section>
@endsection
