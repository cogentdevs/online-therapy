@extends('layouts.frontLayout.front-design')

@section('title', $category->name)
@section('meta_description', $category->name . ' کے شمارے اور مضامین')

@section('content')
    <div class="front-category-detail">
        <div class="container-fluid front-category-detail__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-current="page">{{ $category->name }}</span>
            </nav>

            <div class="row g-4 align-items-start front-category-detail__layout">
                <aside class="col-lg-3 order-2 order-lg-1">
                    <section class="front-category-sidebar" aria-labelledby="category-sidebar-heading">
                        <h2 id="category-sidebar-heading"><i class="fa-solid fa-table-cells-large" aria-hidden="true"></i> تمام موضوعات</h2>
                        <div class="front-category-sidebar__list">
                            @foreach ($categories as $sidebarCategory)
                                <a class="front-category-sidebar__item {{ $sidebarCategory->id === $category->id ? 'active' : '' }}"
                                    href="{{ $sidebarCategory->frontend_url }}"
                                    @if ($sidebarCategory->id === $category->id) aria-current="page" @endif>
                                    <span class="front-category-sidebar__icon">
                                        @if ($sidebarCategory->image_available)
                                            <img src="{{ asset($sidebarCategory->image) }}" alt="">
                                        @else
                                            <i class="fa-solid fa-folder-open" aria-hidden="true"></i>
                                        @endif
                                    </span>
                                    <span>{{ $sidebarCategory->name }}</span>
                                    <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                                </a>
                            @endforeach
                        </div>
                    </section>
                </aside>

                <main class="col-lg-9 order-1 order-lg-2">
                    <header class="front-category-header {{ $categoryBanner?->image_available ? 'front-category-header--with-banner' : '' }}">
                        @if ($categoryBanner?->image_available)
                            <img class="front-category-header__banner" src="{{ asset($categoryBanner->image) }}" alt="">
                        @endif
                        <span class="front-category-header__icon">
                            @if ($category->image_available)
                                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}">
                            @else
                                <i class="fa-solid fa-folder-open" aria-hidden="true"></i>
                            @endif
                        </span>
                        <h1>{{ $category->name }}</h1>
                        <span class="front-category-header__line" aria-hidden="true"></span>
                    </header>

                    <nav class="front-category-tabs front-ui" aria-label="موضوع کا مواد">
                        {{-- Temporarily hidden - feature retained for future use.
                        <a class="{{ $tab === 'magazines' ? 'active' : '' }}"
                            href="{{ route('mozu-detail', ['id' => $category->id, 'mozuName' => request()->route('mozuName'), 'tab' => 'magazines']) }}">
                            <i class="fa-solid fa-book-open" aria-hidden="true"></i> شمارے
                        </a>
                        --}}
                        <a class="{{ $tab === 'articles' ? 'active' : '' }}"
                            href="{{ route('mozu-detail', ['id' => $category->id, 'mozuName' => request()->route('mozuName'), 'tab' => 'articles']) }}">
                            <i class="fa-regular fa-newspaper" aria-hidden="true"></i> مضامین
                        </a>
                    </nav>

                    <div class="front-category-search front-ui">
                        <form method="GET" action="{{ $category->frontend_url }}">
                            <input type="hidden" name="tab" value="{{ $tab }}">
                            <label class="visually-hidden" for="category-search">عنوان تلاش کریں</label>
                            <input id="category-search" name="q" type="search" maxlength="100" value="{{ $search }}"
                                placeholder="{{ $tab === 'magazines' ? 'شمارہ تلاش کریں...' : 'مضمون تلاش کریں...' }}">
                            <button type="submit">تلاش کریں</button>
                            @if ($search !== '')
                                <a href="{{ route('mozu-detail', ['id' => $category->id, 'mozuName' => request()->route('mozuName'), 'tab' => $tab]) }}">تلاش ختم کریں</a>
                            @endif
                        </form>
                    </div>

                    @forelse ($groupedContent as $year => $items)
                        <section class="front-category-year" aria-labelledby="category-year-{{ $year }}">
                            <h2 id="category-year-{{ $year }}">{{ $year }}</h2>

                            @if ($tab === 'magazines')
                                <div class="row g-4 front-category-magazines">
                                    @foreach ($items as $magazine)
                                        <div class="col-12 col-md-6 col-xl-3">
                                            <article class="front-category-magazine-card">
                                                <a class="front-category-magazine-card__cover" href="{{ $magazine->frontend_url }}">
                                                    <img src="{{ $magazine->cover_image ? asset($magazine->cover_image) : asset('images/frontend-images/magazine/latest-cover.svg') }}"
                                                        alt="{{ $magazine->title }}">
                                                </a>
                                                <div class="front-category-magazine-card__body">
                                                    <h3><a href="{{ $magazine->frontend_url }}">{{ $magazine->title }}</a></h3>
                                                    @if ($magazine->issue_number)
                                                        <span class="front-category-magazine-card__issue">شمارہ نمبر: {{ $magazine->issue_number }}</span>
                                                    @endif
                                                    <time class="front-meta" datetime="{{ $magazine->publish_date->toDateString() }}">{{ $magazine->publish_date->format('M Y d') }}</time>
                                                    <a class="front-taza-button front-ui" href="{{ $magazine->frontend_url }}">آن لائن پڑھیں</a>
                                                </div>
                                            </article>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="row g-4 front-category-articles">
                                    @foreach ($items as $article)
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <article class="front-category-article-card">
                                                <a class="front-category-article-card__image" href="{{ $article->frontend_url }}">
                                                    @if ($article->image_available)
                                                        <img src="{{ asset($article->image) }}" alt="{{ $article->title }}">
                                                    @else
                                                        <i class="fa-regular fa-image" aria-hidden="true"></i>
                                                    @endif
                                                </a>
                                                <div class="front-category-article-card__body">
                                                    <h3><a href="{{ $article->frontend_url }}">{{ $article->title }}</a></h3>
                                                    <time class="front-meta" datetime="{{ $article->publish_date->toDateString() }}">{{ $article->publish_date->format('M Y d') }}</time>
                                                    @if ($article->short_description)
                                                        <p>{{ $article->short_description }}</p>
                                                    @endif
                                                    <a class="front-taza-button front-ui" href="{{ $article->frontend_url }}">مزید پڑھیں</a>
                                                </div>
                                            </article>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </section>
                    @empty
                        <div class="front-category-empty">
                            @if ($search !== '')
                                آپ کی تلاش کے مطابق کوئی نتیجہ نہیں ملا۔
                            @elseif ($tab === 'magazines')
                                اس موضوع میں کوئی شمارہ دستیاب نہیں۔
                            @else
                                اس موضوع میں کوئی مضمون دستیاب نہیں۔
                            @endif
                        </div>
                    @endforelse

                    @if ($years->hasPages())
                        <div class="front-category-pagination front-ui">{{ $years->links('pagination::bootstrap-5') }}</div>
                    @endif
                </main>
            </div>
        </div>
    </div>
@endsection
