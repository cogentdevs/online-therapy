@extends('layouts.adminLayout.admin-design')

@section('title', 'Edit Advertisement')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Edit Advertisement</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ads.index') }}">Advertisements</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Advertisement</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please correct the highlighted fields.</strong>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.ads.update', ['id' => $ad->id]) }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Advertisement Information</h3>
                    <p>Update this advertisement's targeting, content, schedule, and status.</p>
                </div>
                <div class="card-body">
                    @include('admin.ads._form')
                </div>
            </section>

            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.ads.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit">
                    <i class="fa-solid fa-floppy-disk me-2" aria-hidden="true"></i>Update Advertisement
                </button>
            </div>
        </form>
    </div>
@endsection
