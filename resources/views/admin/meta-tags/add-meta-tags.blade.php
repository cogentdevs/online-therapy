@extends('layouts.adminLayout.admin-design')

@section('title', 'Add Main Page SEO')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Add Main Page SEO</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.meta-tags.index') }}">SEO / Meta Tags</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Main Page SEO</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>
        @endif

        <form method="POST" action="{{ route('admin.meta-tags.store') }}">
            @csrf
            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Main Page SEO</h3>
                    <p>Core pages are identified by their fixed English page title.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label" for="title">Title / Page <span class="text-danger">*</span></label>
                            <select class="form-select select2 @error('title') is-invalid @enderror" id="title"
                                name="title" data-core-page-select required>
                                <option value="">Select Main Page</option>
                                @foreach ($corePages as $pageTitle => $pageSlug)
                                    <option value="{{ $pageTitle }}" data-page-slug="{{ $pageSlug }}"
                                        @selected(old('title') === $pageTitle)>{{ $pageTitle }}</option>
                                @endforeach
                            </select>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="keywords">Keywords</label>
                            <textarea class="form-control @error('keywords') is-invalid @enderror" id="keywords" name="keywords" rows="3">{{ old('keywords') }}</textarea>
                            @error('keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>
            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.meta-tags.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit">
                    <i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Add Meta Tags
                </button>
            </div>
        </form>
    </div>
@endsection
