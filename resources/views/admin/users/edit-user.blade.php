@extends('layouts.adminLayout.admin-design')
@section('title', 'Edit Admin User')
@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Edit Admin User</h2>
            <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Admin Users</a></li>
                <li class="breadcrumb-item active">Edit User</li>
            </ol></nav>
        </div>

        @if (session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
        @if ($errors->any())<div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>@endif

        <form method="POST" action="{{ route('admin.users.update', ['id' => $managedUser->id]) }}">
            @csrf
            <section class="card admin-settings-card">
                <div class="card-header"><h3>Admin User Information</h3><p>Update account details and its single custom Admin role.</p></div>
                <div class="card-body"><div class="row g-4">
                    <div class="col-lg-6"><label class="form-label" for="name">Name <span class="text-danger">*</span></label><input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name', $managedUser->name) }}" maxlength="255" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-lg-6"><label class="form-label" for="email">Email <span class="text-danger">*</span></label><input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $managedUser->email) }}" maxlength="255" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-lg-6"><label class="form-label" for="phone">Phone</label><input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" type="text" value="{{ old('phone', $managedUser->phone) }}" maxlength="50">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-lg-6"><label class="form-label" for="role_id">Role <span class="text-danger">*</span></label><select class="form-select select2 @error('role_id') is-invalid @enderror" id="role_id" name="role_id" data-role-permission-select required><option value="">Select Admin Role</option>@foreach ($roles as $role)<option value="{{ $role->id }}" @selected((string) old('role_id', $selectedRoleId) === (string) $role->id)>{{ str($role->name)->headline() }}</option>@endforeach</select>@error('role_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                    <div class="col-lg-6"><label class="form-label" for="password">New Password</label><input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password"><div class="form-text">Leave blank to retain the current password.</div>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-lg-6"><label class="form-label" for="password_confirmation">Confirm New Password</label><input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"></div>
                    <div class="col-12"><span class="form-label d-block">Status <span class="text-danger">*</span></span>@php($statusValue = (string) old('is_active', (int) $managedUser->is_active))<div class="d-flex flex-wrap gap-4"><div class="form-check"><input class="form-check-input" id="active" name="is_active" type="radio" value="1" @checked($statusValue === '1')><label class="form-check-label" for="active">Active</label></div><div class="form-check"><input class="form-check-input" id="deactive" name="is_active" type="radio" value="0" @checked($statusValue === '0')><label class="form-check-label" for="deactive">Deactive</label></div></div>@error('is_active')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
                </div></div>
            </section>

            @include('admin.users._role-permission-preview')

            <div class="d-flex justify-content-end gap-2"><a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Cancel</a><button class="btn btn-primary admin-primary-button" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Update Admin User</button></div>
        </form>
    </div>
@endsection
