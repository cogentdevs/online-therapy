@extends('layouts.adminLayout.admin-design')

@section('title', 'Edit Banner')

@section('content')
    @php($selectedType = old('type', $banner->type))
    @php($selectedPosition = old('position', $banner->position))

    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Edit Banner</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.banner.index') }}">Banners</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Banner</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please correct the highlighted fields.</strong>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.banner.update', ['id' => $banner->id]) }}"
            enctype="multipart/form-data">
            @csrf

            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Banner Information</h3>
                    <p>Update this banner's language, placement, images, and status.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <label class="form-label" for="language">Language <span class="text-danger">*</span></label>
                            <select class="form-select select2 @error('language') is-invalid @enderror" id="language"
                                name="language" required>
                                <option value="">Select Language</option>
                                @foreach ($languages as $language)
                                    <option value="{{ $language->code }}" @selected(old('language', $banner->language) === $language->code)>
                                        {{ $language->name ?? $language->code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label" for="type">Type <span class="text-danger">*</span></label>
                            <select class="form-select select2 @error('type') is-invalid @enderror" id="type"
                                name="type" data-banner-type required>
                                <option value="">Select Type</option>
                                <option value="full" @selected($selectedType === 'full')>Full</option>
                                <option value="side-by-side" @selected($selectedType === 'side-by-side')>Side by side</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label" for="position">Position <span class="text-danger">*</span></label>
                            <select class="form-select select2 @error('position') is-invalid @enderror" id="position"
                                name="position" data-banner-position required>
                                <option value="">Select Position</option>
                                {{-- <option value="right-top" @selected(old('position', $banner->position) === 'right-top')>Right Top</option> --}}
                                {{-- <option value="left" @selected(old('position', $banner->position) === 'left')>Left</option> --}}
                                <option value="center" @selected(old('position', $banner->position) === 'center')>Center</option>
                                {{-- <option value="bottom-full" @selected(old('position', $banner->position) === 'bottom-full')>Bottom Full</option> --}}
                                <option value="category-detail-top-full" @selected($selectedPosition === 'category-detail-top-full')>Category Detail - Top
                                    Full</option>
                            </select>
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label" for="image">Image 1</label>
                            @if ($banner->image)
                                <div class="admin-image-preview mb-3">
                                    <img src="{{ asset($banner->image) }}" alt="Current banner image 1">
                                </div>
                            @endif
                            <input class="form-control @error('image') is-invalid @enderror" id="image" type="file"
                                name="image" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp">
                            <div class="form-text" data-banner-image-one-help
                                data-default-help="For Side by side banners, recommended size: 500 × 350 px. Leave blank to retain the current image. Maximum 5 MB."
                                data-category-detail-help="Recommended size: 1150 × 220 px. Leave blank to retain the current image. Maximum 5 MB.">
                                {{ $selectedType === 'full' && $selectedPosition === 'category-detail-top-full' ? 'Recommended size: 1150 × 220 px. Leave blank to retain the current image. Maximum 5 MB.' : 'For Side by side banners, recommended size: 500 × 350 px. Leave blank to retain the current image. Maximum 5 MB.' }}
                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-6 {{ $selectedType === 'side-by-side' ? '' : 'd-none' }}" data-banner-image-two>
                            <label class="form-label" for="image_2">Image 2</label>
                            @if ($banner->image_2)
                                <div class="admin-image-preview mb-3">
                                    <img src="{{ asset($banner->image_2) }}" alt="Current banner image 2">
                                </div>
                            @endif
                            <input class="form-control @error('image_2') is-invalid @enderror" id="image_2" type="file"
                                name="image_2" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp"
                                data-required-when-side-by-side="{{ $banner->image_2 ? 'false' : 'true' }}">
                            <div class="form-text">Recommended size: 500 × 350 px. Required when Side by side has no
                                existing second image.</div>
                            @error('image_2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <span class="form-label d-block">Status <span class="text-danger">*</span></span>
                            <div class="d-flex flex-wrap gap-4">
                                <div class="form-check">
                                    <input class="form-check-input @error('isActive') is-invalid @enderror"
                                        id="status_active" name="isActive" type="radio" value="1"
                                        @checked((string) old('isActive', (int) $banner->isActive) === '1')>
                                    <label class="form-check-label" for="status_active">Active</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input @error('isActive') is-invalid @enderror"
                                        id="status_deactive" name="isActive" type="radio" value="0"
                                        @checked((string) old('isActive', (int) $banner->isActive) === '0')>
                                    <label class="form-check-label" for="status_deactive">Deactive</label>
                                </div>
                            </div>
                            @error('isActive')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.banner.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit">
                    <i class="fa-solid fa-floppy-disk me-2" aria-hidden="true"></i>Update Banner
                </button>
            </div>
        </form>
    </div>
@endsection
