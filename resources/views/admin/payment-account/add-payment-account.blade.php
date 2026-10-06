@extends('layouts.adminLayout.admin-design')
@section('title', 'Add Payment Account')
@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Add Payment Account</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.payment-account.index') }}">Payment Accounts</a></li>
                    <li class="breadcrumb-item active">Add Payment Account</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>
        @endif

        <form method="POST" action="{{ route('admin.payment-account.store') }}">
            @csrf
            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Payment Account Information</h3>
                    <p>Add account details for subscription payments.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label class="form-label" for="bank_name">Bank Name <span class="text-danger">*</span></label>
                            <input class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" type="text" value="{{ old('bank_name') }}" maxlength="255">
                            @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="account_title">Account Title <span class="text-danger">*</span></label>
                            <input class="form-control @error('account_title') is-invalid @enderror" id="account_title" name="account_title" type="text" value="{{ old('account_title') }}" maxlength="255">
                            @error('account_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="iban">IBAN</label>
                            <input class="form-control @error('iban') is-invalid @enderror" id="iban" name="iban" type="text" value="{{ old('iban') }}" maxlength="100" dir="ltr" autocomplete="off">
                            @error('iban')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="account_no">Account No. <span class="text-danger">*</span></label>
                            <input class="form-control @error('account_no') is-invalid @enderror" id="account_no" name="account_no" type="text" value="{{ old('account_no') }}" maxlength="100" dir="ltr" autocomplete="off">
                            @error('account_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="branch_code">Branch Code</label>
                            <input class="form-control @error('branch_code') is-invalid @enderror" id="branch_code" name="branch_code" type="text" value="{{ old('branch_code') }}" maxlength="100" dir="ltr" autocomplete="off">
                            @error('branch_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </section>
            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.payment-account.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit"><i class="fa-solid fa-plus me-2"></i>Add Payment Account</button>
            </div>
        </form>
    </div>
@endsection
