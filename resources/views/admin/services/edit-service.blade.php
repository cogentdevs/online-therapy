@extends('layouts.adminLayout.admin-design')

@section('title', 'Edit Service')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Edit Service</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Services</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Service</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>
        @endif

        <form method="POST" action="{{ route('admin.services.update', ['id' => $service->id]) }}" enctype="multipart/form-data">
            @csrf
            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Service Information</h3>
                    <p>Update the selected Service.</p>
                </div>
                <div class="card-body">@include('admin.services._form-fields')</div>
            </section>
            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.services.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit"><i class="fa-solid fa-floppy-disk me-2" aria-hidden="true"></i>Update Service</button>
            </div>
        </form>
    </div>
@endsection
