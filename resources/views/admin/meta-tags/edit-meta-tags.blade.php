@extends('layouts.adminLayout.admin-design')

@section('title', 'Edit Meta Tags')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Edit Meta Tags</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.meta-tags.index') }}">SEO / Meta Tags</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Meta Tags</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>
        @endif

        <form method="POST" action="{{ route('admin.meta-tags.update', ['id' => $metaTag->id]) }}">
            @csrf
            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>SEO Information</h3>
                    <p>The record identity is protected while its SEO values remain editable.</p>
                </div>
                <div class="card-body">
                    @if ($metaTag->table_name !== null)
                        <div class="alert alert-info">
                            <strong>Module:</strong> {{ $moduleLabel }}
                            <span class="mx-2">|</span>
                            <strong>Record ID:</strong> {{ $metaTag->table_id }}
                        </div>
                    @endif
                    <div class="row g-4">
                        {{-- Fixed English is retained as record identity; no language control is shown. --}}
                        @if (false)
                        <div class="d-none">
                            <label class="form-label">Language</label>
                            <input class="form-control" type="text"
                                value="{{ $languageName ?? ($metaTag->language ?? '—') }}" readonly>
                        </div>
                        @endif
                        <div class="col-12">
                            <label class="form-label">Title / Page</label>
                            <input class="form-control" type="text" value="{{ $metaTag->title }}" readonly>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="keywords">Keywords</label>
                            <textarea class="form-control @error('keywords') is-invalid @enderror" id="keywords" name="keywords" rows="3">{{ old('keywords', $metaTag->keywords) }}</textarea>
                            @error('keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="3">{{ old('description', $metaTag->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="alert alert-light border mb-0">
                                <div><strong>Generated Slug:</strong> {{ $generatedSlug ?? 'Not available' }}</div>
                                <div><strong>Canonical:</strong>
                                    {{ $metaTag->canonical_url ? 'Custom override configured' : 'Auto-generated from the current domain and route' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <h4 class="h6 mb-1">Advanced URL Override</h4>
                            <p class="text-muted small mb-3">Leave empty to use the automatic canonical URL.</p>
                            <label class="form-label" for="canonical_url">Custom Canonical URL</label>
                            <input class="form-control @error('canonical_url') is-invalid @enderror" id="canonical_url"
                                name="canonical_url" type="url"
                                value="{{ old('canonical_url', $metaTag->canonical_url) }}" maxlength="2048">
                            @error('canonical_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>
            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.meta-tags.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit">
                    <i class="fa-solid fa-floppy-disk me-2" aria-hidden="true"></i>Update Meta Tags
                </button>
            </div>
        </form>
    </div>
@endsection
