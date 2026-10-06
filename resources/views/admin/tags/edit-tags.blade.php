@extends('layouts.adminLayout.admin-design')
@section('title', 'Edit Tag')
@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Edit Tag</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tags.index') }}">Tags</a></li>
                    <li class="breadcrumb-item active">Edit Tag</li>
                </ol>
            </nav>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>
        @endif
        <form method="POST" action="{{ route('admin.tags.update', ['id' => $tag->id]) }}">@csrf
            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Tag Information</h3>
                    <p>Update tag information and status.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6"><label class="form-label" for="language">Language <span
                                    class="text-danger">*</span></label><select
                                class="form-select select2 @error('language') is-invalid @enderror" id="language"
                                name="language" required>
                                <option value="">Select Language</option>
                                @foreach ($languages as $language)
                                    <option value="{{ $language->code }}" @selected(old('language', $tag->language) === $language->code)>
                                        {{ $language->name ?? $language->code }}</option>
                                @endforeach
                            </select>
                            @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-lg-6"><label class="form-label" for="name">Name</label><input
                                class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                type="text" value="{{ old('name', $tag->name) }}" maxlength="255">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12"><span class="form-label d-block">Status <span
                                    class="text-danger">*</span></span>
                            <div class="d-flex gap-4">
                                <div class="form-check"><input class="form-check-input" id="tag_active" name="isActive"
                                        type="radio" value="1" @checked((string) old('isActive', (int) $tag->isActive) === '1')><label
                                        class="form-check-label" for="tag_active">Active</label></div>
                                <div class="form-check"><input class="form-check-input" id="tag_deactive" name="isActive"
                                        type="radio" value="0" @checked((string) old('isActive', (int) $tag->isActive) === '0')><label
                                        class="form-check-label" for="tag_deactive">Deactive</label></div>
                            </div>
                            @error('isActive')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>
            <div class="d-flex justify-content-end gap-2"><a class="btn btn-outline-secondary"
                    href="{{ route('admin.tags.index') }}">Cancel</a><button class="btn btn-primary admin-primary-button"
                    type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Update Tag</button></div>
        </form>
    </div>
@endsection
