@extends('layouts.adminLayout.admin-design')
@section('title', 'Edit Role')
@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Edit Role</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active">Edit Role</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>
        @endif

        <form method="POST" action="{{ route('admin.roles.update', ['id' => $role->id]) }}">
            @csrf
            <section class="card admin-settings-card">
                <div class="card-header"><h3>Role Information</h3><p>Update this custom backend role.</p></div>
                <div class="card-body">
                    <label class="form-label" for="name">Role Name <span class="text-danger">*</span></label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                        type="text" value="{{ old('name', $role->name) }}" maxlength="255" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </section>

            @include('admin.roles._permission-matrix', ['selectedPermissions' => $selectedPermissions])

            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.roles.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Update Role
                </button>
            </div>
        </form>
    </div>
@endsection
