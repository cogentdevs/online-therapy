@extends('layouts.adminLayout.admin-design')

@section('title', 'Home Article')

@section('content')
    @php
        $selectedLatestArticleIds = array_map('intval', old('latest_article_ids', $latestArticleIds));
        $selectedEditorialCenterArticleIds = array_map(
            'intval',
            old('editorial_center_article_ids', $editorialCenterArticleIds),
        );
        $selectedEditorialFeaturedArticleId = old('editorial_featured_article_id', $editorialFeaturedArticleId);
    @endphp

    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">Home Article</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Home Article</li>
                </ol>
            </nav>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>
        @endif

        <form method="POST" action="{{ route('admin.home-article.update') }}">
            @csrf
            @method('PUT')

            <section class="card admin-settings-card">
                <div class="card-header">
                    <h3>Homepage Article Placement</h3>
                    <p>Choose the Urdu Articles displayed in each homepage Article area.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label" for="latest_article_ids">Show On Latest</label>
                            <select class="form-select select2 @error('latest_article_ids') is-invalid @enderror"
                                id="latest_article_ids" name="latest_article_ids[]" multiple>
                                @foreach ($articles as $article)
                                    <option value="{{ $article->id }}" @selected(in_array($article->id, $selectedLatestArticleIds, true))>
                                        {{ $article->title ?: 'Untitled Article' }}@if ($article->publish_date)
                                            — {{ $article->publish_date->format('d M Y') }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Select up to 3 Articles.</div>
                            @error('latest_article_ids')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('latest_article_ids.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="editorial_center_article_ids">Editorial Center</label>
                            <select class="form-select select2 @error('editorial_center_article_ids') is-invalid @enderror"
                                id="editorial_center_article_ids" name="editorial_center_article_ids[]" multiple>
                                @foreach ($articles as $article)
                                    <option value="{{ $article->id }}" @selected(in_array($article->id, $selectedEditorialCenterArticleIds, true))>
                                        {{ $article->title ?: 'Untitled Article' }}@if ($article->publish_date)
                                            — {{ $article->publish_date->format('d M Y') }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Select up to 3 Articles.</div>
                            @error('editorial_center_article_ids')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('editorial_center_article_ids.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="editorial_featured_article_id">Editorial Featured</label>
                            <select class="form-select select2 @error('editorial_featured_article_id') is-invalid @enderror"
                                id="editorial_featured_article_id" name="editorial_featured_article_id">
                                <option value="">Select Article</option>
                                @foreach ($articles as $article)
                                    <option value="{{ $article->id }}" @selected((string) $selectedEditorialFeaturedArticleId === (string) $article->id)>
                                        {{ $article->title ?: 'Untitled Article' }}@if ($article->publish_date)
                                            — {{ $article->publish_date->format('d M Y') }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('editorial_featured_article_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <div class="d-flex justify-content-end">
                <button class="btn btn-primary admin-primary-button" type="submit">
                    <i class="fa-solid fa-floppy-disk me-2" aria-hidden="true"></i>Update Settings
                </button>
            </div>
        </form>
    </div>
@endsection
