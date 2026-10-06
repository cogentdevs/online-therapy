@extends('layouts.adminLayout.admin-design')

@section('title', 'Add Category')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Add Category</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Category</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>
        @endif

        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
            @csrf
            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Category Information and SEO</h3>
                    <p>Add one or more Categories with linked Meta Tags using one language.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4 mb-4">
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
                    </div>

                    @php($categoryEntries = old('entries', [['name' => '']]))
                    @php($nextCategoryIndex = empty($categoryEntries) ? 0 : max(array_map('intval', array_keys($categoryEntries))) + 1)
                    <div data-repeatable-list data-next-index="{{ $nextCategoryIndex }}">
                        <h4 class="h6 mb-3">Category Entries</h4>
                        <div class="vstack gap-3" data-repeatable-rows>
                            @foreach ($categoryEntries as $index => $entry)
                                <div class="border rounded p-3" data-repeatable-row>
                                    <div class="row g-3 align-items-end">
                                        <div class="col-lg-5">
                                            <label class="form-label" for="category_name_{{ $index }}">Category
                                                Name</label>
                                            <input
                                                class="form-control @error('entries.' . $index . '.name') is-invalid @enderror"
                                                id="category_name_{{ $index }}"
                                                name="entries[{{ $index }}][name]" type="text"
                                                value="{{ $entry['name'] ?? '' }}" maxlength="255">
                                            @error('entries.' . $index . '.name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-5">
                                            <label class="form-label" for="category_image_{{ $index }}">Category
                                                Image</label>
                                            <input
                                                class="form-control @error('entries.' . $index . '.image') is-invalid @enderror"
                                                id="category_image_{{ $index }}"
                                                name="entries[{{ $index }}][image]" type="file"
                                                accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp">
                                            @error('entries.' . $index . '.image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-2">
                                            @if ($loop->first)
                                                <button class="btn btn-success" type="button" data-repeatable-add
                                                    aria-label="Add Category row"><i class="fa-solid fa-plus"></i></button>
                                            @else
                                                <button class="btn btn-danger" type="button" data-repeatable-remove
                                                    aria-label="Remove Category row"><i
                                                        class="fa-solid fa-minus"></i></button>
                                            @endif
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label" for="category_keywords_{{ $index }}">Meta
                                                Keywords</label>
                                            <textarea class="form-control @error('entries.' . $index . '.keywords') is-invalid @enderror"
                                                id="category_keywords_{{ $index }}" name="entries[{{ $index }}][keywords]" rows="2">{{ $entry['keywords'] ?? '' }}</textarea>
                                            @error('entries.' . $index . '.keywords')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label" for="category_description_{{ $index }}">Meta
                                                Description</label>
                                            <textarea class="form-control @error('entries.' . $index . '.meta_description') is-invalid @enderror"
                                                id="category_description_{{ $index }}" name="entries[{{ $index }}][meta_description]"
                                                rows="2">{{ $entry['meta_description'] ?? '' }}</textarea>
                                            @error('entries.' . $index . '.meta_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-text mt-2">Images are optional. PNG, JPG, JPEG, or WEBP; maximum 5 MB each.</div>
                        @error('entries')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <template data-repeatable-template>
                            <div class="border rounded p-3" data-repeatable-row>
                                <div class="row g-3 align-items-end">
                                    <div class="col-lg-5"><label class="form-label"
                                            for="category_name___INDEX__">Category Name</label><input class="form-control"
                                            id="category_name___INDEX__" name="entries[__INDEX__][name]" type="text"
                                            maxlength="255"></div>
                                    <div class="col-lg-5"><label class="form-label"
                                            for="category_image___INDEX__">Category Image</label><input
                                            class="form-control" id="category_image___INDEX__"
                                            name="entries[__INDEX__][image]" type="file"
                                            accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp"></div>
                                    <div class="col-lg-2"><button class="btn btn-danger" type="button"
                                            data-repeatable-remove aria-label="Remove Category row"><i
                                                class="fa-solid fa-minus"></i></button></div>
                                    <div class="col-lg-6"><label class="form-label"
                                            for="category_keywords___INDEX__">Meta Keywords</label>
                                        <textarea class="form-control" id="category_keywords___INDEX__" name="entries[__INDEX__][keywords]" rows="2"></textarea>
                                    </div>
                                    <div class="col-lg-6"><label class="form-label"
                                            for="category_description___INDEX__">Meta Description</label>
                                        <textarea class="form-control" id="category_description___INDEX__" name="entries[__INDEX__][meta_description]"
                                            rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </section>

            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.categories.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit">
                    <i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Add Categories
                </button>
            </div>
        </form>
    </div>
@endsection
