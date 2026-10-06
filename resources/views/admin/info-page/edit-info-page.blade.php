@extends('layouts.adminLayout.admin-design')

@section('title', 'Edit '.$definition['titles']['en'])

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Edit {{ $definition['titles']['en'] }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{ $definition['titles']['en'] }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if (session('status'))<div class="alert alert-success" role="alert">{{ session('status') }}</div>@endif
        @if ($errors->any())<div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>@endif

        {{-- Temporarily retained language selector markup is intentionally hidden: content language is fixed to English. --}}
        {{-- <section class="card admin-settings-card mb-4">
            <div class="card-header"><h3>Language</h3><p>Load the selected language version of this fixed page.</p></div>
            <div class="card-body">
                <form class="row g-3 align-items-end" method="GET" action="{{ route('admin.info-pages.edit', ['page' => $page]) }}">
                    <div class="col-md-6">
                        <label class="form-label" for="info-page-language">Language</label>
                        <select class="form-select select2" id="info-page-language" name="language" required>
                            @foreach ($languages as $availableLanguage)
                                <option value="{{ $availableLanguage->code }}" @selected($language === $availableLanguage->code)>{{ $availableLanguage->name ?? strtoupper($availableLanguage->code) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto"><button class="btn btn-outline-primary" type="submit"><i class="fa-solid fa-language me-2"></i>Load Language</button></div>
                </form>
            </div>
        </section> --}}

        <form method="POST" action="{{ route('admin.info-pages.update', ['page' => $page]) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="language" value="{{ $language }}">
            <section class="card admin-settings-card">
                <div class="card-header"><h3>Page Content</h3><p>Update only the {{ strtoupper($language) }} content for this page.</p></div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label" for="info-page-title">Title</label>
                            <input class="form-control" id="info-page-title" value="{{ $definition['titles'][$language] }}" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="info-page-description">Description</label>
                            <textarea class="form-control article-editor @error('description') is-invalid @enderror" id="info-page-description" name="description" rows="12">{{ old('description', $infoPage->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </section>
            @can($definition['permission'].'.edit')
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-primary admin-primary-button" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Update {{ $definition['titles']['en'] }}</button>
                </div>
            @endcan
        </form>
    </div>
@endsection
