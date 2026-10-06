@extends('layouts.adminLayout.admin-design')
@section('title', 'Edit Category')
@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Edit Category</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                    <li class="breadcrumb-item active">Edit Category</li>
                </ol>
            </nav>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>
        @endif
        <form method="POST" action="{{ route('admin.categories.update', ['id' => $category->id]) }}"
            enctype="multipart/form-data">@csrf
            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Category Information</h3>
                    <p>Update category information, image, and status.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6"><label class="form-label" for="language">Language <span
                                    class="text-danger">*</span></label><select
                                class="form-select select2 @error('language') is-invalid @enderror" id="language"
                                name="language" required>
                                <option value="">Select Language</option>
                                @foreach ($languages as $language)
                                    <option value="{{ $language->code }}" @selected(old('language', $category->language) === $language->code)>
                                        {{ $language->name ?? $language->code }}</option>
                                @endforeach
                            </select>
                            @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6"><label class="form-label" for="name">Name</label><input
                                class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                type="text" value="{{ old('name', $category->name) }}" maxlength="255">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6"><label class="form-label" for="image">Image</label>
                            @if ($category->image)
                                <div class="mb-2"><img class="admin-current-image" src="{{ asset($category->image) }}"
                                        alt="Current category image"></div>
                            @endif
                            <input class="form-control @error('image') is-invalid @enderror" id="image" name="image"
                                type="file" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp">
                            <div class="form-text">Leave empty to retain the current image. Maximum 5 MB.</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12"><span class="form-label d-block">Status <span
                                    class="text-danger">*</span></span>
                            <div class="d-flex gap-4">
                                <div class="form-check"><input class="form-check-input" id="category_active" name="isActive"
                                        type="radio" value="1" @checked((string) old('isActive', (int) $category->isActive) === '1')><label
                                        class="form-check-label" for="category_active">Active</label></div>
                                <div class="form-check"><input class="form-check-input" id="category_deactive"
                                        name="isActive" type="radio" value="0" @checked((string) old('isActive', (int) $category->isActive) === '0')><label
                                        class="form-check-label" for="category_deactive">Deactive</label></div>
                            </div>
                            @error('isActive')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>
            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>SEO / Meta Tags</h3>
                    <p>The Category name is used as the Meta Tag title.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label class="form-label" for="keywords">Meta Keywords</label>
                            <textarea class="form-control @error('keywords') is-invalid @enderror" id="keywords" name="keywords"
                                rows="3">{{ old('keywords', $metaTag?->keywords) }}</textarea>
                            @error('keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="meta_description">Meta Description</label>
                            <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description"
                                name="meta_description" rows="3">{{ old('meta_description', $metaTag?->description) }}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="alert alert-light border mb-0">
                                <div><strong>Generated Slug:</strong>
                                    {{ $generatedSlug ?? 'Not available' }}
                                </div>
                                <div><strong>Canonical:</strong>
                                    {{ $metaTag?->canonical_url ? 'Custom override configured' : 'Auto-generated from the current domain and Category route' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <h4 class="h6 mb-1">Advanced URL Override</h4>
                            <p class="text-muted small mb-3">Leave empty to use the automatic canonical URL.</p>
                            <label class="form-label" for="canonical_url">Custom Canonical URL</label>
                            <input class="form-control @error('canonical_url') is-invalid @enderror" id="canonical_url"
                                name="canonical_url" type="url"
                                value="{{ old('canonical_url', $metaTag?->canonical_url) }}" maxlength="2048">
                            @error('canonical_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>
            <div class="d-flex justify-content-end gap-2"><a class="btn btn-outline-secondary"
                    href="{{ route('admin.categories.index') }}">Cancel</a><button
                    class="btn btn-primary admin-primary-button" type="submit"><i
                        class="fa-solid fa-floppy-disk me-2"></i>Update Category</button></div>
        </form>
    </div>
@endsection
