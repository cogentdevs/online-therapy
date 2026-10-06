@extends('layouts.adminLayout.admin-design')

@section('title', 'Add Slider')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Add Slider</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.slider.index') }}">Sliders</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Slider</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please correct the highlighted fields.</strong>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.slider.store') }}" enctype="multipart/form-data">
            @csrf

            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Slider Information</h3>
                    <p>Add the content and destination used by this slider.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label class="form-label" for="language">Language <span class="text-danger">*</span></label>
                            <select class="form-select select2 @error('language') is-invalid @enderror" id="language"
                                name="language" required>
                                <option value="">Select Language</option>
                                @foreach ($languages as $language)
                                    <option value="{{ $language->code }}" @selected(old('language') === $language->code)>
                                        {{ $language->name ?? $language->code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label" for="content_position">Content Position</label>
                            <select class="form-select select2 @error('content_position') is-invalid @enderror"
                                id="content_position" name="content_position">
                                <option value="">Select Content Position</option>
                                <option value="top" @selected(old('content_position') === 'top')>Top</option>
                                <option value="center" @selected(old('content_position') === 'center')>Center</option>
                                <option value="bottom" @selected(old('content_position') === 'bottom')>Bottom</option>
                            </select>
                            @error('content_position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label" for="top_heading">Top Heading</label>
                            <input class="form-control @error('top_heading') is-invalid @enderror" id="top_heading"
                                name="top_heading" type="text" value="{{ old('top_heading') }}" maxlength="255">
                            @error('top_heading')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-6">
                            <label class="form-label" for="main_heading">Main Heading</label>
                            <input class="form-control @error('main_heading') is-invalid @enderror" id="main_heading"
                                name="main_heading" type="text" value="{{ old('main_heading') }}" maxlength="255">
                            @error('main_heading')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-6">
                            <label class="form-label" for="bottom_text">Bottom Text</label>
                            <input class="form-control @error('bottom_text') is-invalid @enderror" id="bottom_text"
                                type="text" name="bottom_text" value="{{ old('bottom_text') }}">
                            @error('bottom_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label" for="button_label">Button Label</label>
                            <input class="form-control @error('button_label') is-invalid @enderror" id="button_label"
                                name="button_label" type="text" value="{{ old('button_label') }}" maxlength="255">
                            @error('button_label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label" for="button_url">Button URL</label>
                            <input class="form-control @error('button_url') is-invalid @enderror" id="button_url"
                                name="button_url" type="text" value="{{ old('button_url') }}"
                                placeholder="https://example.com">
                            @error('button_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-6">
                            <label class="form-label" for="image">Slider Image <span class="text-danger">*</span></label>
                            <input class="form-control @error('image') is-invalid @enderror" id="image" name="image"
                                type="file" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp" required>
                            <div class="form-text">Recommended size: 1020 × 590 px. Use a high-quality landscape PNG,
                                JPG, JPEG, or WEBP image. Maximum size: 5 MB.</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.slider.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit">
                    <i class="fa-solid fa-floppy-disk me-2" aria-hidden="true"></i>Add Slider
                </button>
            </div>
        </form>
    </div>
@endsection
