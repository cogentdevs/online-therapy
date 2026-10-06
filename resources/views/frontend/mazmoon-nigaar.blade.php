@extends('layouts.frontLayout.front-design')

@section('title', 'Authors')
@section('meta_description', 'Digital Magazine authors')

@section('content')
    <div class="front-authors-page">
        <div class="container-fluid front-authors-page__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">مضمون نگار</span>
            </nav>

            <header class="front-authors-page__heading">
                <h1>مضمون نگار</h1>
                <span aria-hidden="true"></span>
            </header>

            <form class="front-authors-search front-ui" method="GET" action="{{ route('front.mazmoon-nigaar') }}">
                <label class="visually-hidden" for="author-search">مضمون نگار تلاش کریں</label>
                <div class="input-group">
                    <input class="form-control" id="author-search" name="q" type="search" value="{{ $search }}"
                        placeholder="نام، مہارت یا قابلیت سے تلاش کریں...">
                    <button type="submit" aria-label="تلاش کریں"><i class="fa-solid fa-magnifying-glass"
                            aria-hidden="true"></i></button>
                </div>
            </form>

            @if ($authors->isNotEmpty())
                <div class="row g-4 front-authors-grid">
                    @foreach ($authors as $author)
                        @php($visibility = $author->frontend_visibility)
                        <div class="col-12 col-md-6 col-xl-3">
                            <article class="front-author-card">
                                @if ($visibility['picture'])
                                    <div class="front-author-card__media">
                                        <a href="{{ $author->frontend_url }}" tabindex="-1" aria-hidden="true">
                                            <img class="front-author-card__image"
                                                src="{{ asset($author->picture_available ? $author->picture : 'images/backend-images/author/author.png') }}"
                                                alt="{{ $visibility['name'] ? $author->name : 'مضمون نگار' }}">
                                        </a>
                                    </div>
                                @endif

                                <div class="front-author-card__content">
                                    @if ($visibility['name'] && filled($author->name))
                                        <h2 class="front-author-card__name">
                                            <a href="{{ $author->frontend_url }}">{{ $author->name }}</a>
                                        </h2>
                                    @endif

                                    {{-- @if ($visibility['speciality'] && filled($author->speciality))
                                        <p class="front-author-card__meta">{{ $author->speciality }}</p>
                                    @endif --}}

                                    @if ($visibility['qualification'] && filled($author->qualification))
                                        <p class="front-author-card__meta">{{ $author->qualification }}</p>
                                    @endif

                                    @if ($visibility['experience_detail'] && filled($author->experience_detail))
                                        <p class="front-author-card__detail">{{ $author->experience_detail }}</p>
                                    @endif

                                    <a class="front-author-card__action front-ui" href="{{ $author->frontend_url }}">
                                        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                                        <span>پروفائل دیکھیں</span>
                                    </a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                @if ($authors->hasPages())
                    <div class="front-authors-pagination front-ui">
                        {{ $authors->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @else
                <div class="front-authors-empty">
                    {{ $search !== '' ? 'آپ کی تلاش کے مطابق کوئی مضمون نگار نہیں ملا۔' : 'کوئی مضمون نگار دستیاب نہیں۔' }}
                </div>
            @endif
        </div>
    </div>
@endsection
