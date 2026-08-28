@extends('layouts.admin')

@section('title', 'Access Denied')

@section('content')
<div class="page-heading">
    <div>
        <h1>No Access</h1>
        <p>You do not have permission to use this application.</p>
    </div>
</div>

<section class="admin-card">
    <div class="admin-card-body">
        <p class="mb-3">Contact a Super Admin to request access to this CVS portal.</p>

        @if(!empty($accessibleApps))
            <p class="mb-2"><strong>Portals you can access:</strong></p>
            <ul>
                @foreach($accessibleApps as $appKey)
                    <li>{{ config('cvs.apps.' . $appKey, $appKey) }}</li>
                @endforeach
            </ul>
        @endif

        <a href="{{ route('certificate.search') }}" class="btn btn-outline-primary mt-3">Back to Public Verification</a>
    </div>
</section>
@endsection
