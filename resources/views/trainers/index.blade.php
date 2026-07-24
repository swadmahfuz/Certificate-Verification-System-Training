@extends('layouts.admin')

@section('title', 'Trainers')

@push('styles')
<style>
        body {
            background-color: #f8f9fa;
            font-size: 13px;
        }

        .container {
            max-width: 98%;
            padding-top: 60px;
            padding-bottom: 40px;
        }

        .table th {
            background-color: #f1f1f1;
            text-align: center;
            vertical-align: middle;
        }

        .table td,
        .table th {
            font-size: 12px;
            vertical-align: middle;
        }

        .btn {
            display: inline-flex;
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

        .signature-image {
            width: 140px;
            height: 55px;
            object-fit: contain;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 3px;
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 5px;
        }

        .status-badge {
            min-width: 70px;
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: bold;
        }

        .status-active {
            color: #0f5132;
            background-color: #d1e7dd;
        }

        .status-inactive {
            color: #842029;
            background-color: #f8d7da;
        }
    </style>
@endpush

@section('content')
<section>
    <div class="container">

        <div class="card">

            <div class="card-header">

                <h6 class="text-end">
                    Logged in User:
                    <b>
                        {{ auth()->user()->name }}
                        ({{ auth()->user()->designation }})
                    </b>
                </h6>

                <h3 class="mb-3">
                    TÜV Austria BIC - Training Certificate Verification
                    System (CVS)
                </h3>

                <table class="mx-auto mb-3" style="width: 65%;">
                    <tr>
                        <td class="px-2">
                            <a
                                href="{{ route('dashboard') }}"
                                class="btn btn-primary w-100"
                            >
                                <i class="fa-solid fa-arrow-left me-1"></i>
                                Dashboard
                            </a>
                        </td>

                        <td class="px-2">
                            <a
                                href="{{ route('trainers.create') }}"
                                class="btn btn-success w-100"
                            >
                                <i class="fa-solid fa-user-plus me-1"></i>
                                Add New Trainer
                            </a>
                        </td>

                        <td class="px-2">
                            <a
                                href="{{ route('trainers.index') }}"
                                class="btn btn-info w-100"
                            >
                                <i class="fa-solid fa-arrows-rotate me-1"></i>
                                Refresh
                            </a>
                        </td>

                        <td class="px-2">
                            <a
                                href="{{ url('/logout') }}"
                                class="btn btn-danger w-100"
                            >
                                <i
                                    class="fa-solid fa-right-from-bracket me-1"
                                ></i>
                                Log Out
                            </a>
                        </td>
                    </tr>
                </table>

            </div>

            <div class="card-body table-responsive">

                <h5 class="text-center mb-3">
                    Training CVS Trainer Management
                </h5>

                @if(session('success'))
                    <div
                        class="alert alert-success alert-dismissible fade show"
                        role="alert"
                    >
                        {{ session('success') }}

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close"
                        ></button>
                    </div>
                @endif

                @if(session('error'))
                    <div
                        class="alert alert-danger alert-dismissible fade show"
                        role="alert"
                    >
                        {{ session('error') }}

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close"
                        ></button>
                    </div>
                @endif

                @php
                    $offset =
                        ($trainers->currentPage() - 1)
                        * $trainers->perPage();
                @endphp

                <table
                    class="table table-bordered table-striped text-center"
                >
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Trainer Name</th>
                            <th>Email</th>
                            <th>Designation</th>
                            <th>Signature</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($trainers as $trainer)
                            <tr>
                                <td>
                                    {{ $loop->iteration + $offset }}
                                </td>

                                <td>
                                    <strong>{{ $trainer->name }}</strong>
                                </td>

                                <td>
                                    {{ $trainer->email }}
                                </td>

                                <td>
                                    {{ $trainer->designation }}
                                </td>

                                <td>
                                    @if($trainer->signature_path)
                                        <a
                                            href="{{ route(
                                                'trainers.signature',
                                                $trainer->id
                                            ) }}"
                                            target="_blank"
                                            title="View full signature"
                                        >
                                            <img
                                                src="{{ route(
                                                    'trainers.signature',
                                                    $trainer->id
                                                ) }}"
                                                alt="{{ $trainer->name }}
                                                    Signature"
                                                class="signature-image"
                                            >
                                        </a>
                                    @else
                                        <span class="text-muted">
                                            No signature uploaded
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if($trainer->is_active)
                                        <span
                                            class="
                                                status-badge
                                                status-active
                                            "
                                        >
                                            Active
                                        </span>
                                    @else
                                        <span
                                            class="
                                                status-badge
                                                status-inactive
                                            "
                                        >
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="action-buttons">

                                        <a
                                            href="{{ route(
                                                'trainers.edit',
                                                $trainer->id
                                            ) }}"
                                            class="btn btn-sm btn-warning"
                                        >
                                            <i
                                                class="
                                                    fa-solid
                                                    fa-pen-to-square
                                                    me-1
                                                "
                                            ></i>
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'trainers.toggleStatus',
                                                $trainer->id
                                            ) }}"
                                            onsubmit="
                                                return confirm(
                                                    'Are you sure you want ' +
                                                    'to change this trainer status?'
                                                );
                                            "
                                        >
                                            @csrf

                                            @if($trainer->is_active)
                                                <button
                                                    type="submit"
                                                    class="
                                                        btn
                                                        btn-sm
                                                        btn-danger
                                                    "
                                                >
                                                    <i
                                                        class="
                                                            fa-solid
                                                            fa-user-slash
                                                            me-1
                                                        "
                                                    ></i>
                                                    Deactivate
                                                </button>
                                            @else
                                                <button
                                                    type="submit"
                                                    class="
                                                        btn
                                                        btn-sm
                                                        btn-success
                                                    "
                                                >
                                                    <i
                                                        class="
                                                            fa-solid
                                                            fa-user-check
                                                            me-1
                                                        "
                                                    ></i>
                                                    Activate
                                                </button>
                                            @endif
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    No trainers have been added yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

            @if($trainers->hasPages())
                <div class="card-footer">
                    {{ $trainers->links() }}
                </div>
            @endif

        </div>

    </div>
</section>
@endsection
