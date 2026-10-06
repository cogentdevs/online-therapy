@extends('layouts.frontLayout.front-design')

@section('title', 'تازہ شمارہ')
@section('meta_description', 'ڈیجیٹل میگزین کا تازہ شمارہ اور منتخب اردو مضامین')

@section('content')
    @php
        $magazine = $tazaShumara?->magazine;
        // $hasAds = $tazaShumaraAds->isNotEmpty();
        // $mainColumnClass = $hasAds ? 'col-lg-6' : 'col-lg-9';
        $authors = $magazine?->authors?->where('isActive', true) ?? collect();
        $topics =
            $magazine?->categories?->where('isActive', true)->where('language', $tazaShumara?->language) ?? collect();
        $tags = $magazine?->tags?->where('isActive', true)->where('language', $tazaShumara?->language) ?? collect();
        $sidebarCover = $magazine?->cover_image;
        $widthClasses = ['full' => 'col-12', 'half' => 'col-md-6', 'third' => 'col-md-4'];
    @endphp

    <div class="front-taza-shumara">
        <div class="container-fluid front-taza-shumara__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span><span aria-current="page">تازہ شمارہ</span>
            </nav>

            @if ($magazine)
                <div class="row g-4 align-items-start front-taza-layout">
                    {{-- <main class="{{ $mainColumnClass }} order-1 order-lg-2"> --}}
                    <main class="col-lg-6 order-1 order-lg-2">
                        <section class="front-taza-content" aria-labelledby="current-issue-heading">
                            <h1 class="front-taza-section-title" id="current-issue-heading">تازہ شمارہ</h1>
                            <article class="front-taza-magazine">
                                @if ($magazine->publish_date)
                                    <time class="front-taza-magazine__date front-ui"
                                        datetime="{{ $magazine->publish_date->toDateString() }}">{{ $magazine->publish_date->format('d M Y') }}</time>
                                @endif
                                @if ($magazine->issue_number)
                                    <p class="front-taza-meta front-ui">شمارہ نمبر: {{ $magazine->issue_number }}</p>
                                @endif
                                @if ($tazaShumara->cover_image)
                                    <div class="front-taza-magazine__cover">
                                        <img src="{{ asset($tazaShumara->cover_image) }}" alt="Magazine cover">
                                    </div>
                                @endif
                                @if (($tazaShumara->show_title && $magazine->title) || ($tazaShumara->show_short_description && $magazine->description))
                                    <div class="front-taza-magazine__content">
                                        @if ($tazaShumara->show_title && $magazine->title)
                                            <h2>{{ $magazine->title }}</h2>
                                        @endif
                                        @if ($tazaShumara->show_short_description && $magazine->description)
                                            <p>{{ $magazine->description }}</p>
                                        @endif
                                    </div>
                                @endif
                            </article>

                            @if ($articlePlacements->flatten(1)->isNotEmpty())
                                <section class="front-taza-articles" aria-labelledby="issue-articles-heading">
                                    <h2 class="front-taza-section-title" id="issue-articles-heading">شمارے کے مضامین</h2>
                                    @foreach (['top', 'center', 'bottom'] as $position)
                                        @if ($articlePlacements[$position]->isNotEmpty())
                                            <div class="row g-3 {{ $loop->last ? '' : 'mb-4' }}">
                                                @foreach ($articlePlacements[$position] as $placement)
                                                    @php
                                                        $article = $placement->article;
                                                        $categoryName = $article->categories->first()?->name;
                                                        $columnClass =
                                                            $widthClasses[$placement->display_width] ?? 'col-12';
                                                    @endphp
                                                    <div class="{{ $columnClass }}">
                                                        @if ($placement->display_width === 'full')
                                                            <article class="front-taza-article-card">
                                                                <a class="front-taza-article-card__image"
                                                                    href="{{ $article->frontend_url }}"><img
                                                                        src="{{ asset($article->image_available ? $article->image : 'images/backend-images/articles/placeholder.png') }}"
                                                                        alt="{{ $article->title ?: 'Article image' }}"></a>
                                                                <div class="front-taza-article-card__content">
                                                                    @if ($categoryName)
                                                                        <span
                                                                            class="front-taza-badge front-ui">{{ $categoryName }}</span>
                                                                    @endif
                                                                    @if ($article->title)
                                                                        <h3><a
                                                                                href="{{ $article->frontend_url }}">{{ $article->title }}</a>
                                                                        </h3>
                                                                    @endif
                                                                    @if ($article->publish_date)
                                                                        <time class="front-taza-meta front-ui"
                                                                            datetime="{{ $article->publish_date->toDateString() }}">{{ $article->publish_date->format('d M Y') }}</time>
                                                                    @endif
                                                                    @if ($article->short_description)
                                                                        <p>{{ $article->short_description }}</p>
                                                                    @endif
                                                                    <a class="front-taza-button front-taza-button--small front-ui"
                                                                        href="{{ $article->frontend_url }}">مکمل پڑھیں</a>
                                                                </div>
                                                            </article>
                                                        @else
                                                            <article
                                                                class="front-taza-compact-card front-taza-compact-card--{{ $placement->display_width }}">
                                                                <a class="front-taza-compact-card__image"
                                                                    href="{{ $article->frontend_url }}">
                                                                    <img src="{{ asset($article->image_available ? $article->image : 'images/backend-images/articles/placeholder.png') }}"
                                                                        alt="{{ $article->title ?: 'Article image' }}">
                                                                    @if ($categoryName)
                                                                        <span
                                                                            class="front-taza-badge front-ui">{{ $categoryName }}</span>
                                                                    @endif
                                                                </a>
                                                                <div class="front-taza-compact-card__body">
                                                                    @if ($article->title)
                                                                        <h3><a
                                                                                href="{{ $article->frontend_url }}">{{ $article->title }}</a>
                                                                        </h3>
                                                                    @endif
                                                                    @if ($article->publish_date)
                                                                        <time class="front-taza-meta front-ui"
                                                                            datetime="{{ $article->publish_date->toDateString() }}">{{ $article->publish_date->format('d M Y') }}</time>
                                                                    @endif
                                                                </div>
                                                            </article>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endforeach
                                </section>
                            @endif
                        </section>
                    </main>

                    <aside class="col-lg-3 order-2 order-lg-3 front-taza-sidebars" aria-label="شمارے کی معلومات">
                        @if ($authors->isNotEmpty())
                            <section class="front-taza-sidebar-card">
                                <h2>مضمون نگار</h2>
                                <div class="front-taza-authors">
                                    @foreach ($authors as $author)
                                        @php
                                            $authorUrl = $author->getKey() && filled($author->name)
                                                ? route('front.mazmoon-nigaar.detail', ['id' => $author->id, 'slug' => $author->name])
                                                : null;
                                        @endphp
                                        @if ($authorUrl)
                                            <a class="front-taza-author" href="{{ $authorUrl }}">
                                        @else
                                            <div class="front-taza-author">
                                        @endif
                                            @if ($author->picture)
                                                <img src="{{ asset($author->picture) }}"
                                                    alt="{{ $author->name ?: 'Author' }}" width="48" height="48">
                                            @endif
                                            @if ($author->name)
                                                <span>{{ $author->name }}</span>
                                            @endif
                                        @if ($authorUrl)
                                            </a>
                                        @else
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </section>
                        @endif

                        @if ($sidebarCover || ($tazaShumara->show_title && $magazine->title))
                            <section class="front-taza-sidebar-card front-taza-sidebar-magazine">
                                <h2>ہفتہ وار میگزین</h2>
                                @if ($sidebarCover)
                                    <img src="{{ asset($sidebarCover) }}" alt="Magazine cover" width="420"
                                        height="560">
                                @endif
                                @if ($tazaShumara->show_title && $magazine->title)
                                    <p>{{ $magazine->title }}</p>
                                @endif
                                @if ($magazine->publish_date)
                                    <time class="front-taza-meta front-ui"
                                        datetime="{{ $magazine->publish_date->toDateString() }}">{{ $magazine->publish_date->format('d M Y') }}</time>
                                @endif
                                @if ($magazine->issue_number)
                                    <p class="front-taza-meta front-ui">شمارہ نمبر: {{ $magazine->issue_number }}</p>
                                @endif
                                <a class="front-taza-button front-taza-button--block front-ui"
                                    href="{{ $magazine->frontend_url }}">آن لائن پڑھیں</a>
                            </section>
                        @endif

                        @if ($topics->whereNotNull('name')->isNotEmpty())
                            <section class="front-taza-sidebar-card">
                                <h2>موضوعات</h2>
                                <ul class="front-taza-topic-list">
                                    @foreach ($topics as $topic)
                                        @if ($topic->name)
                                            <li>
                                                @if ($topic->getKey())
                                                    <a href="{{ route('mozu-detail', ['id' => $topic->id, 'mozuName' => $topic->name]) }}">{{ $topic->name }}</a>
                                                @else
                                                    <span>{{ $topic->name }}</span>
                                                @endif
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </section>
                        @endif

                        @if ($tags->whereNotNull('name')->isNotEmpty())
                            <section class="front-taza-sidebar-card">
                                <h2>ٹیگز</h2>
                                <div class="front-taza-tags">
                                    @foreach ($tags as $tag)
                                        @if ($tag->name)
                                            @if ($tag->getKey())
                                                <a class="front-ui"
                                                    href="{{ route('sabqa-shumare', ['tag' => $tag->id]) }}">{{ $tag->name }}</a>
                                            @else
                                                <span class="front-ui">{{ $tag->name }}</span>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </section>
                        @endif
                    </aside>

                    <aside class="col-lg-3 order-3 order-lg-1 front-taza-ads" aria-labelledby="taza-ads-heading">
                        <section class="front-taza-sidebar-card">
                            <h2 id="taza-ads-heading">اشتہارات</h2>
                            <div class="front-taza-ad-slots">
                                <x-frontend.ad-slot :ad="$frontendAds->get('taza_shumara')?->get('taza_sidebar_1_normal')" size="250 × 300" />
                                <x-frontend.ad-slot :ad="$frontendAds->get('taza_shumara')?->get('taza_sidebar_1_tall')" size="600 × 300" :tall="true" />
                                <x-frontend.ad-slot :ad="$frontendAds->get('taza_shumara')?->get('taza_sidebar_2_normal')" size="250 × 300" />
                                <x-frontend.ad-slot :ad="$frontendAds->get('taza_shumara')?->get('taza_sidebar_2_tall')" size="600 × 300" :tall="true" />
                            </div>
                        </section>
                    </aside>
                </div>

                @if ($magazine->relatedMagazines->isNotEmpty())
                    <section class="front-taza-compact-section pt-5" aria-labelledby="related-magazines-heading">
                        <h2 class="front-taza-section-title" id="related-magazines-heading">متعلقہ شمارے</h2>
                        <div class="row g-3">
                            @foreach ($magazine->relatedMagazines as $relatedMagazine)
                                <div class="col-sm-6 col-lg-3">
                                    <article class="front-weekly-card front-taza-related-magazine-card">
                                        @if ($relatedMagazine->cover_image)
                                            <a href="{{ $relatedMagazine->frontend_url }}"><img
                                                    class="front-weekly-card__cover"
                                                    src="{{ asset($relatedMagazine->cover_image) }}" alt="Magazine cover"
                                                    width="300" height="500"></a>
                                        @endif
                                        @if ($relatedMagazine->title)
                                            <h3><a
                                                    href="{{ $relatedMagazine->frontend_url }}">{{ $relatedMagazine->title }}</a>
                                            </h3>
                                        @endif
                                        @if ($relatedMagazine->publish_date)
                                            <time class="front-taza-meta front-ui"
                                                datetime="{{ $relatedMagazine->publish_date->toDateString() }}">{{ $relatedMagazine->publish_date->format('d M Y') }}</time>
                                        @endif
                                        @if ($relatedMagazine->issue_number)
                                            <p class="front-taza-meta front-ui">شمارہ نمبر: {{ $relatedMagazine->issue_number }}</p>
                                        @endif
                                        <a class="front-weekly-card__button front-ui"
                                            href="{{ $relatedMagazine->frontend_url }}">آن لائن پڑھیں</a>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            @else
                <section class="front-taza-content">
                    <h1 class="front-taza-section-title">تازہ شمارہ</h1>
                    <p class="front-ui mb-0">اس زبان کے لیے فی الحال کوئی فعال تازہ شمارہ دستیاب نہیں۔</p>
                </section>
            @endif
        </div>
    </div>
@endsection
