@extends('layouts.adminLayout.admin-design')
@section('title', 'Add Magazine')
@section('content')
    @php
        $selectedProviders = array_map('intval', old('provider_ids', $storageProviders->where('is_default', true)->modelKeys()));
        $selectedCategories = array_map('intval', old('category_ids', []));
        $selectedAuthors = array_map('intval', old('author_ids', []));
        $selectedRelatedMagazines = array_map('intval', old('related_magazine_ids', []));
    @endphp
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Add Magazine</h2>
            <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.magazine.index') }}">Magazines</a></li>
                <li class="breadcrumb-item active">Add Magazine</li>
            </ol></nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>
        @endif
        @if ($storageProviders->isEmpty())
            <div class="alert alert-danger" role="alert">
                No active storage provider is available. Activate and seed a provider before uploading a Magazine PDF.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.magazine.store') }}" enctype="multipart/form-data">
            @csrf
            <section class="card admin-settings-card">
                <div class="card-header"><h3>Magazine Information</h3><p>Create the editorial record as a draft.</p></div>
                <div class="card-body"><div class="row g-4">
                    <div class="col-lg-4">
                        <label class="form-label" for="language">Language <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('language') is-invalid @enderror" id="language" name="language" data-magazine-language required>
                            <option value="">Select Language</option>
                            @foreach ($languages as $language)
                                <option value="{{ $language->code }}" @selected(old('language') === $language->code)>{{ $language->name ?? $language->code }}</option>
                            @endforeach
                        </select>
                        @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-5">
                        <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" type="text" value="{{ old('title') }}" maxlength="255" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label" for="issue_number">Issue Number</label>
                        <input class="form-control @error('issue_number') is-invalid @enderror" id="issue_number" name="issue_number" type="text" value="{{ old('issue_number') }}" maxlength="255">
                        @error('issue_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="publish_date">Publish Date</label>
                        <input class="form-control @error('publish_date') is-invalid @enderror" id="publish_date" name="publish_date" type="date" value="{{ old('publish_date') }}">
                        @error('publish_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="cover_image">Cover Image</label>
                        <input class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" type="file" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp">
                        <div class="form-text">Recommended size: 370 × 475 px. PNG, JPG, JPEG, or WEBP. Maximum 5 MB.</div>
                        @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="category_ids">Categories</label>
                        <select class="form-select select2 @error('category_ids.*') is-invalid @enderror" id="category_ids" name="category_ids[]" data-magazine-language-options multiple>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" data-language="{{ $category->language }}" @selected(in_array($category->id, $selectedCategories, true))>{{ $category->name ?? 'Untitled' }} ({{ $category->language }})</option>
                            @endforeach
                        </select>
                        @error('category_ids.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="author_ids">Authors</label>
                        <select class="form-select select2 @error('author_ids.*') is-invalid @enderror" id="author_ids" name="author_ids[]" multiple>
                            @foreach ($authors as $author)
                                <option value="{{ $author->id }}" @selected(in_array($author->id, $selectedAuthors, true))>{{ $author->name ?? 'Untitled' }}</option>
                            @endforeach
                        </select>
                        @error('author_ids.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="related_magazine_ids">Related Magazines</label>
                        <select class="form-select select2 @error('related_magazine_ids.*') is-invalid @enderror" id="related_magazine_ids" name="related_magazine_ids[]" data-magazine-language-options multiple>
                            @foreach ($relatedMagazines as $relatedMagazine)
                                <option value="{{ $relatedMagazine->id }}" data-language="{{ $relatedMagazine->language }}" @selected(in_array($relatedMagazine->id, $selectedRelatedMagazines, true))>{{ $relatedMagazine->title ?? 'Untitled' }}@if ($relatedMagazine->issue_number) — {{ $relatedMagazine->issue_number }}@endif ({{ $relatedMagazine->language }})</option>
                            @endforeach
                        </select>
                        <div class="form-text">Optional. Available Magazines follow the selected language.</div>
                        @error('related_magazine_ids')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        @error('related_magazine_ids.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-6"><span class="form-label d-block">Free Access</span><div class="d-flex gap-4">
                        <div class="form-check"><input class="form-check-input" id="free_yes" name="isFree" type="radio" value="1" data-free-access-toggle @checked((string) old('isFree', '0') === '1')><label class="form-check-label" for="free_yes">Free</label></div>
                        <div class="form-check"><input class="form-check-input" id="free_no" name="isFree" type="radio" value="0" data-free-access-toggle @checked((string) old('isFree', '0') === '0')><label class="form-check-label" for="free_no">Paid</label></div>
                    </div>@error('isFree')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                    <div class="col-lg-6 {{ (string) old('isFree', '0') === '1' ? '' : 'd-none' }}" data-free-until-field><label class="form-label" for="free_until">Free Until</label><input class="form-control @error('free_until') is-invalid @enderror" id="free_until" name="free_until" type="date" value="{{ old('free_until') }}" min="{{ now()->toDateString() }}" data-free-until-input @disabled((string) old('isFree', '0') !== '1')><div class="form-text">Optional. Leave empty for permanently free access.</div>@error('free_until')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-lg-6"><span class="form-label d-block">Featured</span><div class="d-flex gap-4">
                        <div class="form-check"><input class="form-check-input" id="featured_yes" name="isFeatured" type="radio" value="1" @checked((string) old('isFeatured', '0') === '1')><label class="form-check-label" for="featured_yes">Yes</label></div>
                        <div class="form-check"><input class="form-check-input" id="featured_no" name="isFeatured" type="radio" value="0" @checked((string) old('isFeatured', '0') === '0')><label class="form-check-label" for="featured_no">No</label></div>
                    </div>@error('isFeatured')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                    <div class="col-lg-6"><span class="form-label d-block">Show Visit Counter</span><div class="d-flex gap-4">
                        <div class="form-check"><input class="form-check-input" id="show_visit_counter_yes" name="show_visit_counter" type="radio" value="1" @checked((string) old('show_visit_counter', '0') === '1')><label class="form-check-label" for="show_visit_counter_yes">Yes</label></div>
                        <div class="form-check"><input class="form-check-input" id="show_visit_counter_no" name="show_visit_counter" type="radio" value="0" @checked((string) old('show_visit_counter', '0') === '0')><label class="form-check-label" for="show_visit_counter_no">No</label></div>
                    </div>@error('show_visit_counter')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                    <div class="col-lg-6"><span class="form-label d-block">Allow PDF Download</span><div class="d-flex gap-4">
                        <div class="form-check"><input class="form-check-input" id="download_no" name="is_downloadable" type="radio" value="0" @checked((string) old('is_downloadable', '0') === '0')><label class="form-check-label" for="download_no">No — View/Read Only</label></div>
                        <div class="form-check"><input class="form-check-input" id="download_yes" name="is_downloadable" type="radio" value="1" @checked((string) old('is_downloadable', '0') === '1')><label class="form-check-label" for="download_yes">Yes — Download Allowed</label></div>
                    </div>@error('is_downloadable')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                </div></div>
            </section>

            <section class="card admin-settings-card">
                <div class="card-header"><h3>Magazine PDF Storage</h3><p>The PDF remains private and is stored through the configured media providers.</p></div>
                <div class="card-body"><div class="row g-4">
                    <div class="col-lg-6">
                        <label class="form-label" for="pdf">Magazine PDF <span class="text-danger">*</span></label>
                        <input class="form-control @error('pdf') is-invalid @enderror" id="pdf" name="pdf" type="file" accept=".pdf,application/pdf" required>
                        <div class="form-text">PDF only. Maximum 200 MB.</div>
                        @error('pdf')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-6">
                        <span class="form-label d-block">Store PDF On <span class="text-danger">*</span></span>
                        @forelse ($storageProviders as $provider)
                            <div class="form-check mb-2">
                                <input class="form-check-input" id="provider_{{ $provider->id }}" name="provider_ids[]" type="checkbox" value="{{ $provider->id }}" @checked(in_array($provider->id, $selectedProviders, true))>
                                <label class="form-check-label" for="provider_{{ $provider->id }}">{{ $provider->name ?? $provider->slug }} @if ($provider->is_default)<span class="badge text-bg-secondary ms-1">Default</span>@endif</label>
                            </div>
                        @empty
                            <p class="text-danger mb-0">No active storage providers are configured.</p>
                        @endforelse
                        @error('provider_ids')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        @error('provider_ids.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div></div>
            </section>

            <section class="card admin-settings-card">
                <div class="card-header"><h3>SEO Settings</h3><p>Title and slug are synchronized automatically.</p></div>
                <div class="card-body"><div class="row g-4">
                    <div class="col-lg-6"><label class="form-label" for="keywords">Meta Keywords</label><textarea class="form-control @error('keywords') is-invalid @enderror" id="keywords" name="keywords" rows="3">{{ old('keywords') }}</textarea>@error('keywords')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-lg-6"><label class="form-label" for="meta_description">Meta Description</label><textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description" name="meta_description" rows="3">{{ old('meta_description') }}</textarea>@error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div></div>
            </section>

            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.magazine.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit" @disabled($storageProviders->isEmpty())><i class="fa-solid fa-plus me-2"></i>Add Magazine</button>
            </div>
        </form>
    </div>
@endsection
