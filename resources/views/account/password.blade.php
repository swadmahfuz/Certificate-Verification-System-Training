@extends('layouts.admin')

@section('title', 'Set Password')

@section('content')
<div class="page-heading">
    <div>
        <h1>Set Your Password</h1>
        <p>Choose a new password to continue.</p>
    </div>
</div>

@if(session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
@endif

<section class="admin-card">
    <div class="admin-card-body">
        <form method="POST" action="{{ route('account.password.update') }}">
            @csrf
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label" for="password">New password</label>
                    <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
    </div>
</section>
@endsection
