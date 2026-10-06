@extends('layouts.frontLayout.front-design')

@section('title', $author->frontend_visibility['name'] && filled($author->name) ? $author->name : 'مضمون نگار')
@section('meta_description', 'مضمون نگار کا تعارف اور مضامین')

@section('content')
    @php($visibility = $author->frontend_visibility)
    @php($visibleAuthorName = $visibility['name'] && filled($author->name) ? $author->name : 'مضمون نگار')

    <div class="front-author-detail">
        <div class="container-fluid front-author-detail__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('front.mazmoon-nigaar') }}">مضمون نگار</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $visibleAuthorName }}</span>
            </nav>

            <article class="front-author-profile">
                <div class="front-author-profile__content" dir="ltr">
                    <span class="front-author-profile__label front-ui">مضمون نگار</span>
                    @if ($visibility['name'] && filled($author->name))
                        <h1>{{ $author->name }}</h1>
                    @endif
                    @if ($visibility['qualification'] && filled($author->qualification))
                        <p class="front-author-profile__qualification">{{ $author->qualification }}</p>
                    @endif
                    @if ($visibility['speciality'] && filled($author->speciality))
                        <p class="front-author-profile__speciality">{{ $author->speciality }}</p>
                    @endif
                    @if ($visibility['experience_detail'] && filled($author->experience_detail))
                        <p class="front-author-profile__detail">{{ $author->experience_detail }}</p>
                    @endif

                    @if (
                        ($visibility['experience_years'] && filled($author->experience_years)) ||
                            ($visibility['contact_number'] && filled($author->contact_number)) ||
                            ($visibility['email'] && filled($author->email)))
                        <div class="front-author-profile__contacts front-ui">
                            @if ($visibility['experience_years'] && filled($author->experience_years))
                                <span><i class="fa-regular fa-calendar"
                                        aria-hidden="true"></i><b>{{ $author->experience_years }}
                                        سال</b><small>تجربہ</small></span>
                            @endif
                            @if ($visibility['contact_number'] && filled($author->contact_number))
                                <span><i class="fa-solid fa-phone" aria-hidden="true"></i><b
                                        dir="ltr">{{ $author->contact_number }}</b><small>رابطہ نمبر</small></span>
                            @endif
                            @if ($visibility['email'] && filled($author->email))
                                <span><i class="fa-regular fa-envelope" aria-hidden="true"></i><b
                                        dir="ltr">{{ $author->email }}</b><small>ای میل</small></span>
                            @endif
                        </div>
                    @endif
                </div>
                @if ($visibility['picture'])
                    <div class="front-author-profile__image">
                        <img src="{{ asset($author->picture_available ? $author->picture : 'images/backend-images/author/author.png') }}"
                            alt="{{ $visibleAuthorName }}">
                    </div>
                @endif
            </article>

            <div class="row g-4 front-author-detail__main">
                <aside class="col-12 col-lg-3 order-2 order-lg-2 front-author-detail-sidebar">
                    @if ($otherAuthors->isNotEmpty())
                        <section class="front-author-detail-sidebar__section">
                            <h2>مضمون نگار</h2>
                            <div class="front-author-detail-sidebar__authors">
                                @foreach ($otherAuthors as $otherAuthor)
                                    @php($otherVisibility = $otherAuthor->frontend_visibility)
                                    <a class="front-author-detail-sidebar__author" href="{{ $otherAuthor->frontend_url }}">
                                        @if ($otherVisibility['picture'])
                                            <img src="{{ asset($otherAuthor->picture_available ? $otherAuthor->picture : 'images/backend-images/author/author.png') }}"
                                                alt="{{ $otherVisibility['name'] ? $otherAuthor->name : 'مضمون نگار' }}">
                                        @endif
                                        @if ($otherVisibility['name'] && filled($otherAuthor->name))
                                            <span>{{ $otherAuthor->name }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                            <a class="front-author-detail-sidebar__all front-ui"
                                href="{{ route('front.mazmoon-nigaar') }}">تمام مضمون نگار دیکھیں</a>
                        </section>
                    @endif

                    @if ($categories->isNotEmpty())
                        <section class="front-author-detail-sidebar__section">
                            <h2>موضوعات</h2>
                            <div class="front-author-detail-sidebar__filters">
                                @foreach ($categories as $category)
                                    <a class="front-author-detail-sidebar__filter {{ $selectedCategory === $category->id ? 'active' : '' }}"
                                        href="{{ $author->frontend_url . '?' . http_build_query(array_filter(['category' => $category->id, 'year' => $selectedYear, 'q' => $search], fn($value) => filled($value))) }}">
                                        <span>{{ $category->name }}</span>
                                        <b>{{ $category->author_articles_count }}</b>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($years->isNotEmpty())
                        <section class="front-author-detail-sidebar__section">
                            <h2>سال</h2>
                            <div class="front-author-detail-sidebar__filters">
                                @foreach ($years as $year)
                                    <a class="front-author-detail-sidebar__filter {{ $selectedYear === (int) $year->publication_year ? 'active' : '' }}"
                                        href="{{ $author->frontend_url . '?' . http_build_query(array_filter(['category' => $selectedCategory, 'year' => (int) $year->publication_year, 'q' => $search], fn($value) => filled($value))) }}">
                                        <span>{{ $year->publication_year }}</span>
                                        <b>{{ $year->article_count }}</b>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </aside>

                <main class="col-12 col-lg-9 order-1 order-lg-1 front-author-articles">
                    <header class="front-author-articles__header">
                        <div>
                            <h2>{{ $visibleAuthorName }} کے مضامین</h2>
                            {{-- <span aria-hidden="true"></span> --}}
                        </div>
                        <p class="front-ui">کل مضامین ({{ $articles->total() }})</p>
                    </header>

                    <form class="front-author-articles__search front-ui" method="GET"
                        action="{{ $author->frontend_url }}">
                        @if ($selectedCategory)
                            <input name="category" type="hidden" value="{{ $selectedCategory }}">
                        @endif
                        @if ($selectedYear)
                            <input name="year" type="hidden" value="{{ $selectedYear }}">
                        @endif
                        <label class="visually-hidden" for="author-article-search">مضمون تلاش کریں</label>
                        <div class="input-group">
                            <input class="form-control" id="author-article-search" name="q" type="search"
                                value="{{ $search }}" placeholder="مضمون تلاش کریں...">
                            <button type="submit">تلاش کریں</button>
                        </div>
                        @if ($search !== '' || $selectedCategory || $selectedYear)
                            <a class="front-author-articles__reset" href="{{ $author->frontend_url }}">فلٹر ختم کریں</a>
                        @endif
                    </form>

                    @if ($articles->isNotEmpty())
                        <div class="row g-4 front-author-articles__grid">
                            @foreach ($articles as $article)
                                <div class="col-12 col-md-6 col-xl-4">
                                    <article class="front-author-article-card">
                                        <a class="front-author-article-card__image" href="{{ $article->frontend_url }}">
                                            @if ($article->image_available)
                                                <img src="{{ asset($article->image) }}" alt="{{ $article->title }}">
                                            @else
                                                <i class="fa-regular fa-image" aria-hidden="true"></i>
                                            @endif
                                        </a>
                                        <div class="front-author-article-card__body">
                                            <div class="front-author-article-card__meta front-ui">
                                                @if ($article->categories->isNotEmpty())
                                                    <span>{{ $article->categories->first()->name }}</span>
                                                @endif
                                                <time
                                                    datetime="{{ $article->publish_date->toDateString() }}">{{ $article->publish_date->format('M Y d') }}</time>
                                            </div>
                                            <h3><a href="{{ $article->frontend_url }}">{{ $article->title }}</a></h3>
                                            @if (filled($article->short_description))
                                                <p>{{ $article->short_description }}</p>
                                            @endif
                                            <a class="front-author-article-card__action front-ui"
                                                href="{{ $article->frontend_url }}">مزید پڑھیں <i
                                                    class="fa-solid fa-arrow-left" aria-hidden="true"></i></a>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>

                        @if ($articles->hasPages())
                            <div class="front-author-articles__pagination front-ui">{{ $articles->links('pagination::bootstrap-5') }}</div>
                        @endif
                    @else
                        <div class="front-author-articles__empty">
                            {{ $search !== '' || $selectedCategory || $selectedYear ? 'آپ کی تلاش یا منتخب فلٹر کے مطابق کوئی مضمون نہیں ملا۔' : 'اس مضمون نگار کے کوئی مضامین دستیاب نہیں۔' }}
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </div>
@endsection
