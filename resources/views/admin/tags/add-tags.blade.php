@extends('layouts.adminLayout.admin-design')
@section('title', 'Add Tag')
@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Add Tag</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tags.index') }}">Tags</a></li>
                    <li class="breadcrumb-item active">Add Tag</li>
                </ol>
            </nav>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>
        @endif
        <form method="POST" action="{{ route('admin.tags.store') }}">@csrf
            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Tag Information</h3>
                    <p>Add one or more tags using one language.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4 mb-4">
                        <div class="col-lg-6"><label class="form-label" for="language">Language <span
                                    class="text-danger">*</span></label><select
                                class="form-select select2 @error('language') is-invalid @enderror" id="language"
                                name="language" required>
                                <option value="">Select Language</option>
                                @foreach ($languages as $language)
                                    <option value="{{ $language->code }}" @selected(old('language') === $language->code)>
                                        {{ $language->name ?? $language->code }}</option>
                                @endforeach
                            </select>
                            @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @php($tagNames = old('names', ['']))
                    <div data-repeatable-list data-next-index="{{ count($tagNames) }}">
                        <h4 class="h6 mb-3">Tag Entries</h4>
                        <div class="vstack gap-3" data-repeatable-rows>
                            @foreach ($tagNames as $index => $tagName)
                                <div class="row g-2 align-items-end" data-repeatable-row>
                                    <div class="col"><label class="form-label"
                                            for="tag_name_{{ $index }}">Name</label><input
                                            class="form-control @error('names.' . $index) is-invalid @enderror"
                                            id="tag_name_{{ $index }}" name="names[]" type="text"
                                            value="{{ $tagName }}" maxlength="255">
                                        @error('names.' . $index)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-auto">
                                        @if ($loop->first)
                                            <button class="btn btn-success" type="button" data-repeatable-add
                                                aria-label="Add Tag row"><i
                                                class="fa-solid fa-plus"></i></button>@else<button
                                                class="btn btn-danger" type="button" data-repeatable-remove
                                                aria-label="Remove Tag row"><i class="fa-solid fa-minus"></i></button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('names')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <template data-repeatable-template>
                            <div class="row g-2 align-items-end" data-repeatable-row>
                                <div class="col"><label class="form-label" for="tag_name___INDEX__">Name</label><input
                                        class="form-control" id="tag_name___INDEX__" name="names[]" type="text"
                                        maxlength="255"></div>
                                <div class="col-auto"><button class="btn btn-danger" type="button" data-repeatable-remove
                                        aria-label="Remove Tag row"><i class="fa-solid fa-minus"></i></button></div>
                            </div>
                        </template>
                    </div>
                </div>
            </section>
            <div class="d-flex justify-content-end gap-2"><a class="btn btn-outline-secondary"
                    href="{{ route('admin.tags.index') }}">Cancel</a><button class="btn btn-primary admin-primary-button"
                    type="submit"><i class="fa-solid fa-plus me-2"></i>Add Tags</button></div>
        </form>
    </div>
@endsection
