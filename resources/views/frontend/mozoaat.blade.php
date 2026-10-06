@extends('layouts.frontLayout.front-design')

@section('title', 'موضوعات')
@section('meta_description', 'ڈیجیٹل میگزین کے تمام موضوعات')

@section('content')
    <div class="front-topics-page">
        <div class="container-fluid front-topics-page__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">موضوعات</span>
            </nav>

            <header class="front-topics-page__heading">
                <h1>موضوعات</h1>
                <span aria-hidden="true"></span>
            </header>

            @if ($categories->isNotEmpty())
                <div class="row g-4 front-topic-grid">
                    @foreach ($categories as $category)
                        <div class="col-12 col-md-6 col-xl-3">
                            <a class="front-topic-list-card" href="{{ $category->frontend_url }}">
                                <span class="front-topic-list-card__icon">
                                    @if ($category->image_available)
                                        <img src="{{ asset($category->image) }}" alt="{{ $category->name }}">
                                    @else
                                        <i class="fa-solid fa-folder-open" aria-hidden="true"></i>
                                    @endif
                                </span>
                                <span class="front-topic-list-card__content">
                                    <strong class="front-topic-list-card__name">{{ $category->name }}</strong>
                                    <small class="front-topic-list-card__count">{{ $category->total_content_count }} مواد</small>
                                </span>
                                <span class="front-topic-list-card__arrow" aria-hidden="true">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>

                @if ($categories->hasPages())
                    <div class="front-topics-pagination front-ui">
                        {{ $categories->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @else
                <div class="front-topics-empty">کوئی موضوع دستیاب نہیں۔</div>
            @endif
        </div>
    </div>
@endsection
