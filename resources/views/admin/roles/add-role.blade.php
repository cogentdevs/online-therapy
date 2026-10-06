@extends('layouts.adminLayout.admin-design')
@section('title', 'Add Role')
@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Add Role</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active">Add Role</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>
        @endif

        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <section class="card admin-settings-card">
                <div class="card-header"><h3>Role Information</h3><p>Create a custom backend role.</p></div>
                <div class="card-body">
                    <label class="form-label" for="name">Role Name <span class="text-danger">*</span></label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                        type="text" value="{{ old('name') }}" maxlength="255" placeholder="Content Editor" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </section>

            @include('admin.roles._permission-matrix', ['selectedPermissions' => []])

            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.roles.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit">
                    <i class="fa-solid fa-plus me-2"></i>Add Role
                </button>
            </div>
        </form>
    </div>
@endsection
