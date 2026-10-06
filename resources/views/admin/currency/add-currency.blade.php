@extends('layouts.adminLayout.admin-design')
@section('title', 'Add Currency')
@section('content')
    <div class="container-fluid">
        <div class="mb-4"><h2 class="mb-1">Add Currency</h2><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.currency.index') }}">Currencies</a></li><li class="breadcrumb-item active">Add Currency</li></ol></nav></div>
        @if ($errors->any())<div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>@endif
        <form method="POST" action="{{ route('admin.currency.store') }}">@csrf
            <section class="card admin-settings-card"><div class="card-header"><h3>Currency Information</h3><p>Add a reusable project currency.</p></div><div class="card-body"><div class="row g-4">
                <div class="col-lg-5"><label class="form-label" for="name">Currency Name</label><input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name') }}" maxlength="255" placeholder="US Dollar">@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-lg-3"><label class="form-label" for="code">Currency Code</label><input class="form-control @error('code') is-invalid @enderror" id="code" name="code" type="text" value="{{ old('code') }}" maxlength="20" placeholder="USD">@error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-lg-4"><label class="form-label" for="symbol">Currency Symbol</label><input class="form-control @error('symbol') is-invalid @enderror" id="symbol" name="symbol" type="text" value="{{ old('symbol') }}" maxlength="20" placeholder="$">@error('symbol')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            </div></div></section>
            <div class="d-flex justify-content-end gap-2"><a class="btn btn-outline-secondary" href="{{ route('admin.currency.index') }}">Cancel</a><button class="btn btn-primary admin-primary-button" type="submit"><i class="fa-solid fa-plus me-2"></i>Add Currency</button></div>
        </form>
    </div>
@endsection
