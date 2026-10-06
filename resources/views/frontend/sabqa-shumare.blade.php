@extends('layouts.frontLayout.front-design')

@section('title', 'سابقہ شمارے')
@section('meta_description', 'ڈیجیٹل میگزین کے سابقہ شمارے')

@section('content')
    @php
        $filterUrl = static function (string $name, int $value): string {
            $filters = request()->only(['author', 'category', 'year', 'tag', 'search']);
            $filters[$name] = $value;

            return route('sabqa-shumare', array_filter($filters));
        };
    @endphp

    <div class="front-sabqa-shumare">
        <div class="container-fluid front-sabqa-shumare__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span><span aria-current="page">سابقہ شمارے</span>
            </nav>

            <div class="row justify-content-center front-sabqa-search">
                <div class="col-12 col-lg-10 col-xl-8">
                    <form class="front-sabqa-search__form front-ui" method="GET" action="{{ route('sabqa-shumare') }}">
                        @foreach (request()->only(['author', 'category', 'year', 'tag']) as $filterName => $filterValue)
                            @if (filled($filterValue))
                                <input type="hidden" name="{{ $filterName }}" value="{{ $filterValue }}">
                            @endif
                        @endforeach
                        <label class="visually-hidden" for="sabqa-search">شمارہ تلاش کریں</label>
                        <input id="sabqa-search" name="search" type="search" value="{{ $search }}"
                            maxlength="100" placeholder="عنوان، مضمون نگار یا تفصیل تلاش کریں...">
                        <button type="submit">تلاش کریں</button>
                        @if ($search !== '')
                            <a class="front-sabqa-search__clear"
                                href="{{ route('sabqa-shumare', array_filter(request()->only(['author', 'category', 'year', 'tag']))) }}">تلاش ختم کریں</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="row g-4 align-items-start front-sabqa-layout">
                <aside class="col-lg-3 order-2 order-lg-1">
                    <div class="front-sabqa-sidebar">
                        @if ($authors->isNotEmpty())
                            <section class="front-taza-sidebar-card" aria-labelledby="sabqa-authors-heading">
                                <h2 id="sabqa-authors-heading">مضمون نگار</h2>
                                <div class="front-sabqa-authors">
                                    @foreach ($authors as $author)
                                        <a class="front-sabqa-author {{ $selectedAuthor === $author->id ? 'active' : '' }}"
                                            href="{{ $filterUrl('author', $author->id) }}">
                                            @if ($author->picture)
                                                <img src="{{ asset($author->picture) }}" alt="{{ $author->name }}">
                                            @endif
                                            <span>{{ $author->name }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </section>
                        @endif

                        @if ($categories->isNotEmpty())
                            <section class="front-taza-sidebar-card" aria-labelledby="sabqa-categories-heading">
                                <h2 id="sabqa-categories-heading">موضوعات</h2>
                                <ul class="front-sabqa-filter-list">
                                    @foreach ($categories as $category)
                                        <li><a class="{{ $selectedCategory === $category->id ? 'active' : '' }}"
                                                href="{{ $filterUrl('category', $category->id) }}"><span>{{ $category->name }}</span></a></li>
                                    @endforeach
                                </ul>
                            </section>
                        @endif

                        @if ($years->isNotEmpty())
                            <section class="front-taza-sidebar-card" aria-labelledby="sabqa-years-heading">
                                <h2 id="sabqa-years-heading">سال</h2>
                                <ul class="front-sabqa-filter-list front-ui">
                                    @foreach ($years as $year)
                                        <li><a class="{{ $selectedYear === $year ? 'active' : '' }}"
                                                href="{{ $filterUrl('year', $year) }}">{{ $year }}</a></li>
                                    @endforeach
                                </ul>
                            </section>
                        @endif

                        @if ($tags->isNotEmpty())
                            <section class="front-taza-sidebar-card" aria-labelledby="sabqa-tags-heading">
                                <h2 id="sabqa-tags-heading">ٹیگز</h2>
                                <div class="front-sabqa-tags">
                                    @foreach ($tags as $tag)
                                        <a class="front-ui {{ $selectedTag === $tag->id ? 'active' : '' }}"
                                                href="{{ $filterUrl('tag', $tag->id) }}">{{ $tag->name }}</a>
                                    @endforeach
                                </div>
                            </section>
                        @endif
                    </div>
                </aside>

                <main class="col-lg-9 order-1 order-lg-2" aria-labelledby="sabqa-shumare-heading">
                    <h1 class="front-taza-section-title" id="sabqa-shumare-heading">سابقہ شمارے</h1>

                    @if ($magazines->isNotEmpty())
                        <div class="row g-3">
                            @foreach ($magazines as $magazine)
                                @php($magazineDestination = $magazine->search_result_url ?? $magazine->frontend_url)
                                <div class="col-12 col-xl-6">
                                    <article class="front-sabqa-card">
                                        @if ($magazine->cover_image)
                                            <a class="front-sabqa-card__cover" href="{{ $magazineDestination }}">
                                                <img src="{{ asset($magazine->cover_image) }}"
                                                    alt="{{ $magazine->title }}" width="300" height="475">
                                            </a>
                                        @endif
                                        <div class="front-sabqa-card__content">
                                            @if ($magazine->title)
                                                <h2><a href="{{ $magazineDestination }}">{{ $magazine->title }}</a></h2>
                                            @endif
                                            @if ($magazine->publish_date)
                                                <time class="front-taza-meta front-ui"
                                                    datetime="{{ $magazine->publish_date->toDateString() }}">{{ $magazine->publish_date->format('d M Y') }}</time>
                                            @endif
                                            @if ($magazine->issue_number)
                                                <p class="front-sabqa-card__issue front-ui">شمارہ نمبر: {{ $magazine->issue_number }}</p>
                                            @endif
                                            @if ($magazine->authors->isNotEmpty())
                                                <p class="front-sabqa-card__authors"><strong>مضمون نگار:</strong>
                                                    {{ $magazine->authors->pluck('name')->filter()->implode(', ') }}</p>
                                            @endif
                                            @if ($magazine->show_visit_counter)
                                                <span class="d-inline-flex align-items-center gap-1 text-muted small"
                                                    data-magazine-visit-count="{{ $magazine->site_visits_count ?? 0 }}">
                                                    <i class="fa-regular fa-eye" aria-hidden="true"></i>
                                                    {{ number_format($magazine->site_visits_count ?? 0) }}
                                                </span>
                                            @endif
                                            @if ($magazine->description)
                                                <p class="front-sabqa-card__description">{{ $magazine->description }}</p>
                                            @endif
                                            <a class="front-taza-button front-taza-button--small front-ui"
                                                href="{{ $magazineDestination }}">آن لائن پڑھیں</a>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>

                        @if ($magazines->hasPages())
                            <nav class="front-sabqa-pagination front-ui" aria-label="صفحات">
                                {{ $magazines->links('pagination::bootstrap-5') }}
                            </nav>
                        @endif
                    @else
                        <div class="front-sabqa-empty">
                            <p class="mb-0">کوئی شمارہ دستیاب نہیں۔</p>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </div>
@endsection
