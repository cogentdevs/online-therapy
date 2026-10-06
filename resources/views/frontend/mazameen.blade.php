@extends('layouts.frontLayout.front-design')

@section('title', 'Articles')
@section('meta_description', 'Digital Magazine articles')

@section('content')
    @php
        $filterUrl = static function (string $name, int $value): string {
            $filters = request()->only(['author', 'category', 'year', 'tag', 'search']);
            $filters[$name] = $value;

            return route('mazameen', array_filter($filters));
        };
    @endphp

    <div class="front-sabqa-shumare front-mazameen">
        <div class="container-fluid front-sabqa-shumare__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span><span aria-current="page">مضامین</span>
            </nav>

            <div class="row justify-content-center front-sabqa-search">
                <div class="col-12 col-lg-10 col-xl-8">
                    <form class="front-sabqa-search__form front-ui" method="GET" action="{{ route('mazameen') }}">
                        @foreach (request()->only(['author', 'category', 'year', 'tag']) as $filterName => $filterValue)
                            @if (filled($filterValue))
                                <input type="hidden" name="{{ $filterName }}" value="{{ $filterValue }}">
                            @endif
                        @endforeach
                        <label class="visually-hidden" for="mazameen-search">مضمون تلاش کریں</label>
                        <input id="mazameen-search" name="search" type="search" value="{{ $search }}" maxlength="100" placeholder="عنوان، مضمون نگار یا تفصیل تلاش کریں...">
                        <button type="submit">تلاش کریں</button>
                        @if ($search !== '')
                            <a class="front-sabqa-search__clear" href="{{ route('mazameen', array_filter(request()->only(['author', 'category', 'year', 'tag']))) }}">تلاش ختم کریں</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="row g-4 align-items-start front-sabqa-layout">
                <aside class="col-lg-3 order-2 order-lg-1">
                    <div class="front-sabqa-sidebar">
                        @if ($authors->isNotEmpty())
                            <section class="front-taza-sidebar-card"><h2>مضمون نگار</h2><div class="front-sabqa-authors">
                                @foreach ($authors as $author)
                                    <a class="front-sabqa-author {{ $selectedAuthor === $author->id ? 'active' : '' }}" href="{{ $filterUrl('author', $author->id) }}">
                                        @if ($author->picture)<img src="{{ asset($author->picture) }}" alt="{{ $author->name }}">@endif
                                        <span>{{ $author->name }}</span>
                                    </a>
                                @endforeach
                            </div></section>
                        @endif
                        @if ($categories->isNotEmpty())
                            <section class="front-taza-sidebar-card"><h2>موضوعات</h2><ul class="front-sabqa-filter-list">
                                @foreach ($categories as $category)<li><a class="{{ $selectedCategory === $category->id ? 'active' : '' }}" href="{{ $filterUrl('category', $category->id) }}">{{ $category->name }}</a></li>@endforeach
                            </ul></section>
                        @endif
                        @if ($years->isNotEmpty())
                            <section class="front-taza-sidebar-card"><h2>سال</h2><ul class="front-sabqa-filter-list front-ui">
                                @foreach ($years as $year)<li><a class="{{ $selectedYear === $year ? 'active' : '' }}" href="{{ $filterUrl('year', $year) }}">{{ $year }}</a></li>@endforeach
                            </ul></section>
                        @endif
                        @if ($tags->isNotEmpty())
                            <section class="front-taza-sidebar-card"><h2>ٹیگز</h2><div class="front-sabqa-tags">
                                @foreach ($tags as $tag)<a class="front-ui {{ $selectedTag === $tag->id ? 'active' : '' }}" href="{{ $filterUrl('tag', $tag->id) }}">{{ $tag->name }}</a>@endforeach
                            </div></section>
                        @endif
                    </div>
                </aside>

                <main class="col-lg-9 order-1 order-lg-2" aria-labelledby="mazameen-heading">
                    <h1 class="front-taza-section-title" id="mazameen-heading">مضامین</h1>
                    @forelse ($articles as $article)
                        @php($articleDestination = $article->search_result_url ?? $article->frontend_url)
                        <article class="front-mazameen-card mb-3">
                            <a class="front-mazameen-card__image" href="{{ $articleDestination }}"><img src="{{ asset($article->image_available ? $article->image : 'images/backend-images/articles/placeholder.png') }}" alt="{{ $article->title }}" width="420" height="240"></a>
                            <div class="front-mazameen-card__content">
                                @if ($article->categories->isNotEmpty())<span class="front-mazameen-card__category">{{ $article->categories->first()->name }}</span>@endif
                                <h2><a href="{{ $articleDestination }}">{{ $article->title }}</a></h2>
                                <div class="front-mazameen-card__meta front-ui">
                                    @if ($article->publish_date)<time datetime="{{ $article->publish_date->toDateString() }}">{{ $article->publish_date->format('d M Y') }}</time>@endif
                                    @if ($article->issue_number)<span>شمارہ نمبر: {{ $article->issue_number }}</span>@endif
                                    @if ($article->authors->isNotEmpty())<span>مضمون نگار: {{ $article->authors->pluck('name')->filter()->implode(', ') }}</span>@endif
                                    @if ($article->show_visit_counter)
                                        <span class="d-inline-flex align-items-center gap-1 text-muted small"
                                            data-article-visit-count="{{ $article->site_visits_count ?? 0 }}">
                                            <i class="fa-regular fa-eye" aria-hidden="true"></i>
                                            {{ number_format($article->site_visits_count ?? 0) }}
                                        </span>
                                    @endif
                                </div>
                                @if ($article->short_description)<p class="front-mazameen-card__description">{{ $article->short_description }}</p>@endif
                                <a class="front-taza-button front-taza-button--small front-ui" href="{{ $articleDestination }}">مکمل پڑھیں</a>
                            </div>
                        </article>
                    @empty
                        <div class="front-sabqa-empty"><p class="mb-0">کوئی مضمون دستیاب نہیں۔</p></div>
                    @endforelse

                    @if ($articles->hasPages())
                        <nav class="front-sabqa-pagination front-ui" aria-label="صفحات">{{ $articles->links('pagination::bootstrap-5') }}</nav>
                    @endif
                </main>
            </div>
        </div>
    </div>
@endsection
