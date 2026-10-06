@extends('layouts.adminLayout.admin-design')

@section('title', 'Edit FAQ')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Edit FAQ</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.faq.index') }}">FAQs</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit FAQ</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please correct the highlighted fields.</strong>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.faq.update', ['id' => $faq->id]) }}">
            @csrf

            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>FAQ Information</h3>
                    <p>Update this frequently asked question, answer, and status.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4" data-faq-category-form>
                        <div class="col-lg-6">
                            <label class="form-label" for="language">Language <span class="text-danger">*</span></label>
                            <select class="form-select select2 @error('language') is-invalid @enderror" id="language"
                                name="language" required>
                                <option value="">Select Language</option>
                                @foreach ($languages as $language)
                                    <option value="{{ $language->code }}" @selected(old('language', $faq->language) === $language->code)>
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
                                    <option value="{{ $category->id }}" data-language="{{ $category->language }}" @selected((string) old('faq_category_id', $faq->faq_category_id) === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text d-none" data-faq-category-empty>Please create an active FAQ Category for this language first.</div>
                            @error('faq_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="question">Question <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('question') is-invalid @enderror" id="question" name="question"
                                rows="3" required>{{ old('question', $faq->question) }}</textarea>
                            @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="answer">Answer <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('answer') is-invalid @enderror" id="answer" name="answer"
                                rows="5" maxlength="1000" required>{{ old('answer', $faq->answer) }}</textarea>
                            <div class="form-text">Maximum 1000 characters.</div>
                            @error('answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <span class="form-label d-block">Status <span class="text-danger">*</span></span>
                            <div class="d-flex flex-wrap gap-4">
                                <div class="form-check">
                                    <input class="form-check-input @error('isActive') is-invalid @enderror"
                                        id="status_active" name="isActive" type="radio" value="1"
                                        @checked((string) old('isActive', (int) $faq->isActive) === '1')>
                                    <label class="form-check-label" for="status_active">Active</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input @error('isActive') is-invalid @enderror"
                                        id="status_deactive" name="isActive" type="radio" value="0"
                                        @checked((string) old('isActive', (int) $faq->isActive) === '0')>
                                    <label class="form-check-label" for="status_deactive">Deactive</label>
                                </div>
                            </div>
                            @error('isActive')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </section>

            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.faq.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit">
                    <i class="fa-solid fa-floppy-disk me-2" aria-hidden="true"></i>Update FAQ
                </button>
            </div>
        </form>
    </div>
@endsection
