@extends('layouts.adminLayout.admin-design')

@section('title', 'Add FAQ')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Add FAQ</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.faq.index') }}">FAQs</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add FAQ</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please correct the highlighted fields.</strong>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.faq.store') }}">
            @csrf

            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>FAQ Information</h3>
                    <p>Add a frequently asked question and its answer.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4" data-faq-category-form>
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
                            @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label" for="faq_category_id">FAQ Category <span class="text-danger">*</span></label>
                            <select class="form-select select2 @error('faq_category_id') is-invalid @enderror" id="faq_category_id" name="faq_category_id" data-faq-category-select required>
                                <option value="">Select FAQ Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" data-language="{{ $category->language }}" @selected((string) old('faq_category_id') === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text d-none" data-faq-category-empty>Please create an active FAQ Category for this language first.</div>
                            @error('faq_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="question">Question <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('question') is-invalid @enderror" id="question" name="question"
                                rows="3" required>{{ old('question') }}</textarea>
                            @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="answer">Answer <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('answer') is-invalid @enderror" id="answer" name="answer"
                                rows="5" maxlength="1000" required>{{ old('answer') }}</textarea>
                            <div class="form-text">Maximum 1000 characters.</div>
                            @error('answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </section>

            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.faq.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit">
                    <i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Add FAQ
                </button>
            </div>
        </form>
    </div>
@endsection
