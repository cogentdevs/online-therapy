@extends('layouts.adminLayout.admin-design')
@section('title', 'Payment Accounts')
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Payment Accounts</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Payment Accounts</li>
                    </ol>
                </nav>
            </div>
            @can('payment-accounts.create')
                <a class="btn btn-primary admin-primary-button" href="{{ route('admin.payment-account.create') }}">
                    <i class="fa-solid fa-plus me-2"></i>Add Payment Account
                </a>
            @endcan
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
        @endif

        <div class="card admin-settings-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" data-admin-datatable data-admin-serials>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Bank Name</th>
                                <th>Account Title</th>
                                <th>IBAN</th>
                                <th>Account No.</th>
                                <th>Branch Code</th>
                                <th>Status</th>
                                @role('super-admin')
                                    <th>Created By</th>
                                @endrole
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paymentAccounts as $paymentAccount)
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>{{ $paymentAccount->bank_name }}</td>
                                    <td>{{ $paymentAccount->account_title }}</td>
                                    <td dir="ltr">{{ $paymentAccount->iban ?: '—' }}</td>
                                    <td dir="ltr">{{ $paymentAccount->account_no }}</td>
                                    <td dir="ltr">{{ $paymentAccount->branch_code ?: '—' }}</td>
                                    <td>
                                        <span class="badge {{ $paymentAccount->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $paymentAccount->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    @role('super-admin')
                                        <td>@include('admin.partials.created-by', ['record' => $paymentAccount])</td>
                                    @endrole
                                    <td>{{ $paymentAccount->created_at?->format('d M Y, h:i A') ?: '—' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can('payment-accounts.edit')
                                                <a class="btn btn-sm btn-outline-primary"
                                                    href="{{ route('admin.payment-account.edit', ['id' => $paymentAccount->id]) }}"
                                                    aria-label="Edit Payment Account">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                            @endcan
                                            @can('payment-accounts.delete')
                                                <form method="POST" action="{{ route('admin.payment-account.destroy', ['id' => $paymentAccount->id]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" type="submit"
                                                        data-delete-confirm
                                                        data-delete-title="Delete this Payment Account?"
                                                        data-delete-text="This payment account will be permanently deleted."
                                                        aria-label="Delete Payment Account">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
