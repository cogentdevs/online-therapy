@extends('layouts.adminLayout.admin-design')

@section('title', 'Change Password')

@section('content')
    <div class="container-fluid">
        <div class="admin-page-intro">
            <div>
                <span class="admin-page-eyebrow">Account Security</span>
                <h2>Change Password</h2>
                <p>Update the password used to access your Super Admin account.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif

        <div class="card admin-form-card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.change-password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input
                            id="current_password"
                            type="password"
                            name="current_password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            autocomplete="current-password"
                            required
                        >
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            autocomplete="new-password"
                            required
                        >
                        <div class="form-text">Use at least 8 characters and choose a password different from your current password.</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary admin-primary-button">Update Password</button>
                </form>
            </div>
        </div>
    </div>
@endsection
