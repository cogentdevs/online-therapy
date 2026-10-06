@extends('layouts.frontLayout.front-design')

@section('title', $article->title)
@section('meta_description', $article->short_description)

@section('content')
    <div class="front-mazmoon-detail">
        <div class="container-fluid front-mazmoon-detail__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span><a href="{{ route('mazameen') }}">مضامین</a>
                <span aria-hidden="true">/</span><span aria-current="page">{{ $article->title }}</span>
            </nav>

            <div class="row g-4 align-items-start front-mazmoon-detail__layout">
                <aside class="col-lg-3 order-2 order-lg-1" data-article-sidebar>
                    <div class="front-sabqa-sidebar">
                        @if ($article->authors->isNotEmpty())
                            <section class="front-taza-sidebar-card" aria-labelledby="mazmoon-authors-heading">
                                <h2 id="mazmoon-authors-heading">مضمون نگار</h2>
                                <div class="front-sabqa-authors">
                                    @foreach ($article->authors as $author)
                                        @php
                                            $authorUrl = $author->getKey() && filled($author->name)
                                                ? route('front.mazmoon-nigaar.detail', ['id' => $author->id, 'slug' => $author->name])
                                                : null;
                                        @endphp
                                        @if ($authorUrl)
                                            <a class="front-sabqa-author" href="{{ $authorUrl }}">
                                        @else
                                            <div class="front-sabqa-author">
                                        @endif
                                            @if ($author->picture)<img src="{{ asset($author->picture) }}" alt="{{ $author->name }}">@endif
                                            <span>{{ $author->name }}</span>
                                        @if ($authorUrl)
                                            </a>
                                        @else
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </section>
                        @endif

                        @if ($article->categories->isNotEmpty())
                            <section class="front-taza-sidebar-card" aria-labelledby="mazmoon-categories-heading">
                                <h2 id="mazmoon-categories-heading">موضوعات</h2>
                                <ul class="front-sabqa-filter-list">
                                    @foreach ($article->categories as $category)
                                        <li>
                                            @if ($category->getKey() && filled($category->name))
                                                <a href="{{ route('mozu-detail', ['id' => $category->id, 'mozuName' => $category->name]) }}">
                                                    <span>{{ $category->name }}</span>
                                                </a>
                                            @else
                                                <span>{{ $category->name }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                        @endif

                        @if ($article->tags->isNotEmpty())
                            <section class="front-taza-sidebar-card" aria-labelledby="mazmoon-tags-heading">
                                <h2 id="mazmoon-tags-heading">ٹیگز</h2>
                                <div class="front-sabqa-tags">
                                    @foreach ($article->tags as $tag)
                                        @if ($tag->getKey() && filled($tag->name))
                                            <a class="front-ui"
                                                href="{{ route('mazameen', ['tag' => $tag->id]) }}">{{ $tag->name }}</a>
                                        @else
                                            <span class="front-ui">{{ $tag->name }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </section>
                        @endif

                        @if ($article->relatedArticles->isNotEmpty())
                            <section class="front-taza-sidebar-card" aria-labelledby="mazmoon-related-heading">
                                <h2 id="mazmoon-related-heading">متعلقہ مضامین</h2>
                                <div class="front-mazmoon-related">
                                    @foreach ($article->relatedArticles as $relatedArticle)
                                        <article class="front-mazmoon-related__card">
                                            <a href="{{ $relatedArticle->frontend_url }}"><img src="{{ asset($relatedArticle->image_available ? $relatedArticle->image : 'images/backend-images/articles/placeholder.png') }}" alt="{{ $relatedArticle->title }}" width="82" height="68"></a>
                                            <div class="front-mazmoon-related__content">
                                                <h3><a href="{{ $relatedArticle->frontend_url }}">{{ $relatedArticle->title }}</a></h3>
                                                @if ($relatedArticle->publish_date)<time class="front-ui" datetime="{{ $relatedArticle->publish_date->toDateString() }}">{{ $relatedArticle->publish_date->format('d M Y') }}</time>@endif
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </section>
                        @endif
                    </div>
                </aside>

                <main class="col-lg-9 order-1 order-lg-2" data-article-detail>
                    <article class="front-mazmoon-detail__article">
                        @if ($article->categories->isNotEmpty())<span class="front-mazmoon-detail__category">{{ $article->categories->first()->name }}</span>@endif
                        <h1 class="front-mazmoon-detail__title">{{ $article->title }}</h1>
                        <div class="front-mazmoon-detail__header-row">
                            <div class="front-mazmoon-detail__meta front-ui">
                                @if ($article->authors->isNotEmpty())<span><i class="fa-solid fa-user" aria-hidden="true"></i> {{ $article->authors->pluck('name')->filter()->implode(', ') }}</span>@endif
                                @if ($article->publish_date)<time datetime="{{ $article->publish_date->toDateString() }}"><i class="fa-regular fa-calendar" aria-hidden="true"></i> {{ $article->publish_date->format('d M Y') }}</time>@endif
                                @if ($article->issue_number)<span>شمارہ نمبر: {{ $article->issue_number }}</span>@endif
                                @if ($article->show_visit_counter)
                                    <span class="d-inline-flex align-items-center gap-1 text-muted small"
                                        data-article-visit-count="{{ $article->site_visits_count ?? 0 }}">
                                        <i class="fa-regular fa-eye" aria-hidden="true"></i>
                                        {{ number_format($article->site_visits_count ?? 0) }}
                                    </span>
                                @endif
                            </div>
                            <div class="front-mazmoon-detail__actions front-ui">
                                @auth('web')
                                    <button class="front-article-bookmark{{ $isBookmarked ? ' is-bookmarked' : '' }}" type="button"
                                        aria-pressed="{{ $isBookmarked ? 'true' : 'false' }}"
                                        data-article-bookmark
                                        data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}"
                                        data-store-url="{{ route('front.article-bookmarks.store', $article) }}"
                                        data-destroy-url="{{ route('front.article-bookmarks.destroy', $article) }}">
                                        <i class="{{ $isBookmarked ? 'fa-solid' : 'fa-regular' }} fa-bookmark" aria-hidden="true"></i>
                                        <span data-article-bookmark-label>{{ $isBookmarked ? 'بک مارک سے ہٹائیں' : 'بک مارک کریں' }}</span>
                                    </button>
                                @else
                                    <button class="front-article-bookmark" type="button" data-bs-toggle="modal"
                                        data-bs-target="#subscription-login-modal">
                                        <i class="fa-regular fa-bookmark" aria-hidden="true"></i>
                                        <span>بک مارک کریں</span>
                                    </button>
                                @endauth
                            </div>
                        </div>
                        @if ($article->image_available)<img class="front-mazmoon-detail__hero" src="{{ asset($article->image) }}" alt="{{ $article->title }}">@endif
                        @if ($article->article)<div class="front-mazmoon-detail__body">{!! $article->article !!}</div>@endif
                    </article>
                </main>
            </div>
        </div>
    </div>
@endsection
