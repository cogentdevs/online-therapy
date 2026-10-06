@extends('layouts.adminLayout.admin-design')
@section('title', 'Edit Payment Account')
@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Edit Payment Account</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.payment-account.index') }}">Payment Accounts</a></li>
                    <li class="breadcrumb-item active">Edit Payment Account</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>
        @endif

        <form method="POST" action="{{ route('admin.payment-account.update', ['id' => $paymentAccount->id]) }}">
            @csrf
            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Payment Account Information</h3>
                    <p>Update account details and status.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label class="form-label" for="bank_name">Bank Name <span class="text-danger">*</span></label>
                            <input class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" type="text" value="{{ old('bank_name', $paymentAccount->bank_name) }}" maxlength="255">
                            @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="account_title">Account Title <span class="text-danger">*</span></label>
                            <input class="form-control @error('account_title') is-invalid @enderror" id="account_title" name="account_title" type="text" value="{{ old('account_title', $paymentAccount->account_title) }}" maxlength="255">
                            @error('account_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="iban">IBAN</label>
                            <input class="form-control @error('iban') is-invalid @enderror" id="iban" name="iban" type="text" value="{{ old('iban', $paymentAccount->iban) }}" maxlength="100" dir="ltr" autocomplete="off">
                            @error('iban')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="account_no">Account No. <span class="text-danger">*</span></label>
                            <input class="form-control @error('account_no') is-invalid @enderror" id="account_no" name="account_no" type="text" value="{{ old('account_no', $paymentAccount->account_no) }}" maxlength="100" dir="ltr" autocomplete="off">
                            @error('account_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="branch_code">Branch Code</label>
                            <input class="form-control @error('branch_code') is-invalid @enderror" id="branch_code" name="branch_code" type="text" value="{{ old('branch_code', $paymentAccount->branch_code) }}" maxlength="100" dir="ltr" autocomplete="off">
                            @error('branch_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <span class="form-label d-block">Status <span class="text-danger">*</span></span>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" id="payment_account_active" name="is_active" type="radio" value="1" @checked((string) old('is_active', (int) $paymentAccount->is_active) === '1')>
                                    <label class="form-check-label" for="payment_account_active">Active</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" id="payment_account_inactive" name="is_active" type="radio" value="0" @checked((string) old('is_active', (int) $paymentAccount->is_active) === '0')>
                                    <label class="form-check-label" for="payment_account_inactive">Inactive</label>
                                </div>
                            </div>
                            @error('is_active')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </section>
            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.payment-account.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Update Payment Account</button>
            </div>
        </form>
    </div>
@endsection
