@extends('layouts.frontLayout.front-design')

@section('title', $magazine->title)
@section('meta_description', $magazine->description ?: $magazine->title)

@section('content')
    <div class="front-shumara-detail">
        <div class="container-fluid front-shumara-detail__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('sabqa-shumare') }}">سابقہ شمارے</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $magazine->title }}</span>
            </nav>

            <div class="row g-4 align-items-start front-shumara-detail__layout">
                <aside class="col-lg-3 order-2 order-lg-1">
                    <div class="front-sabqa-sidebar">
                        <article class="front-shumara-detail__card front-shumara-detail__sidebar-card">
                            @if ($magazine->cover_image)
                                <div class="front-shumara-detail__cover">
                                    <img src="{{ asset($magazine->cover_image) }}" alt="{{ $magazine->title }}"
                                        width="370" height="475">
                                </div>
                            @endif
                            <div class="front-shumara-detail__content">
                                <h1>{{ $magazine->title }}</h1>
                                @if ($magazine->publish_date)
                                    <time class="front-taza-meta front-ui"
                                        datetime="{{ $magazine->publish_date->toDateString() }}">{{ $magazine->publish_date->format('d M Y') }}</time>
                                @endif
                                @if ($magazine->issue_number)
                                    <p class="front-shumara-detail__meta front-ui"><strong>شمارہ نمبر:</strong>
                                        {{ $magazine->issue_number }}</p>
                                @endif
                                @if ($magazine->show_visit_counter)
                                    <span class="d-inline-flex align-items-center gap-1 text-muted small"
                                        data-magazine-visit-count="{{ $magazine->site_visits_count ?? 0 }}">
                                        <i class="fa-regular fa-eye" aria-hidden="true"></i>
                                        {{ number_format($magazine->site_visits_count ?? 0) }}
                                    </span>
                                @endif
                                {{-- @if ($magazine->authors->isNotEmpty())
                                    <p class="front-shumara-detail__meta"><strong>مضمون نگار:</strong> {{ $magazine->authors->pluck('name')->filter()->implode(', ') }}</p>
                                @endif --}}
                                {{-- @if ($magazine->description)
                                    <p class="front-shumara-detail__description">{{ $magazine->description }}</p>
                                @endif --}}
                            </div>
                        </article>

                        @if ($magazine->authors->isNotEmpty())
                            <section class="front-taza-sidebar-card" aria-labelledby="detail-authors-heading">
                                <h2 id="detail-authors-heading">مضمون نگار</h2>
                                <div class="front-sabqa-authors">
                                    @foreach ($magazine->authors as $author)
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
                                            @if ($author->picture)
                                                <img src="{{ asset($author->picture) }}" alt="{{ $author->name }}">
                                            @endif
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

                        @if ($magazine->categories->isNotEmpty())
                            <section class="front-taza-sidebar-card" aria-labelledby="detail-categories-heading">
                                <h2 id="detail-categories-heading">موضوعات</h2>
                                <ul class="front-sabqa-filter-list">
                                    @foreach ($magazine->categories as $category)
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

                        @if ($magazine->tags->isNotEmpty())
                            <section class="front-taza-sidebar-card" aria-labelledby="detail-tags-heading">
                                <h2 id="detail-tags-heading">ٹیگز</h2>
                                <div class="front-sabqa-tags">
                                    @foreach ($magazine->tags as $tag)
                                        @if ($tag->getKey() && filled($tag->name))
                                            <a class="front-ui"
                                                href="{{ route('sabqa-shumare', ['tag' => $tag->id]) }}">{{ $tag->name }}</a>
                                        @else
                                            <span class="front-ui">{{ $tag->name }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </section>
                        @endif
                    </div>
                </aside>

                <main class="col-lg-9 order-1 order-lg-2">
                    <section class="front-shumara-reader" aria-labelledby="shumara-reader-heading">
                        <h2 class="front-taza-section-title" id="shumara-reader-heading">شمارہ پڑھیں</h2>
                        @if ($pdfLocation)
                            <div class="front-pdf-viewer" data-pdf-viewer
                                data-pdf-url="{{ route('shumara-detail.pdf', ['id' => $magazine->id, 'slug' => request()->route('slug')]) }}"
                                data-initial-page="{{ $initialPdfPage }}"
                                data-downloadable="{{ $magazine->is_downloadable ? '1' : '0' }}"
                                @if ($magazine->is_downloadable) data-download-url="{{ route('shumara-detail.download', ['id' => $magazine->id, 'slug' => request()->route('slug')]) }}" @endif>
                                <div class="front-pdf-viewer__toolbar front-ui" role="toolbar" aria-label="پی ڈی ایف کنٹرولز">
                                    <button type="button" data-pdf-previous aria-label="پچھلا صفحہ" title="پچھلا صفحہ"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                                    <span class="front-pdf-viewer__counter"><span data-pdf-current-page>1</span> / <span data-pdf-total-pages>—</span></span>
                                    <button type="button" data-pdf-next aria-label="اگلا صفحہ" title="اگلا صفحہ"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                                    <span class="front-pdf-viewer__separator" aria-hidden="true"></span>
                                    <button type="button" data-pdf-zoom-out aria-label="زوم کم کریں" title="زوم کم کریں"><i class="fa-solid fa-minus" aria-hidden="true"></i></button>
                                    <span class="front-pdf-viewer__zoom" data-pdf-zoom>100%</span>
                                    <button type="button" data-pdf-zoom-in aria-label="زوم بڑھائیں" title="زوم بڑھائیں"><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
                                    <button type="button" data-pdf-fit-width aria-label="صفحہ چوڑائی کے مطابق کریں" title="چوڑائی کے مطابق"><i class="fa-solid fa-arrows-left-right" aria-hidden="true"></i></button>
                                    <span class="front-pdf-viewer__separator" aria-hidden="true"></span>
                                    @auth('web')
                                        <span class="front-magazine-bookmark-controls"
                                            data-magazine-bookmark-controls
                                            data-bookmarked="{{ $magazineBookmark ? 'true' : 'false' }}"
                                            data-store-url="{{ route('front.magazine-bookmarks.store', $magazine) }}"
                                            data-destroy-url="{{ route('front.magazine-bookmarks.destroy', $magazine) }}">
                                            <button type="button" data-magazine-bookmark-save disabled>
                                                <i class="{{ $magazineBookmark ? 'fa-solid' : 'fa-regular' }} fa-bookmark" aria-hidden="true"></i>
                                                <span data-magazine-bookmark-save-label>{{ $magazineBookmark ? 'موجودہ صفحہ محفوظ کریں' : 'بک مارک کریں' }}</span>
                                            </button>
                                            <button type="button" data-magazine-bookmark-remove @if (! $magazineBookmark) hidden @endif>
                                                <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                                                <span>بک مارک سے ہٹائیں</span>
                                            </button>
                                            <small data-magazine-bookmark-page @if (! $magazineBookmark?->pdf_page) hidden @endif>
                                                محفوظ شدہ صفحہ: <b>{{ $magazineBookmark?->pdf_page }}</b>
                                            </small>
                                        </span>
                                    @else
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#subscription-login-modal">
                                            <i class="fa-regular fa-bookmark" aria-hidden="true"></i>
                                            <span>بک مارک کریں</span>
                                        </button>
                                    @endauth
                                    @if ($magazine->is_downloadable)
                                        <span class="front-pdf-viewer__actions">
                                            <button type="button" data-pdf-print aria-label="پی ڈی ایف پرنٹ کریں" title="پرنٹ"><i class="fa-solid fa-print" aria-hidden="true"></i></button>
                                            <a href="{{ route('shumara-detail.download', ['id' => $magazine->id, 'slug' => request()->route('slug')]) }}" aria-label="پی ڈی ایف ڈاؤن لوڈ کریں" title="ڈاؤن لوڈ" data-pdf-download><i class="fa-solid fa-download" aria-hidden="true"></i></a>
                                        </span>
                                    @endif
                                </div>
                                <div class="front-pdf-viewer__loading" data-pdf-loading>پی ڈی ایف لوڈ ہو رہی ہے…</div>
                                <div class="front-pdf-viewer__error" data-pdf-error hidden>پی ڈی ایف لوڈ نہیں ہو سکی۔</div>
                                <div class="front-pdf-viewer__canvas-area" data-pdf-canvas-area hidden>
                                    <canvas data-pdf-canvas aria-label="{{ $magazine->title }} PDF"></canvas>
                                </div>
                            </div>
                        @else
                            <div class="front-sabqa-empty">
                                <p class="mb-0">اس شمارے کی پی ڈی ایف دستیاب نہیں۔</p>
                            </div>
                        @endif
                    </section>

                    @if ($magazine->relatedMagazines->isNotEmpty())
                        <section class="front-shumara-related" aria-labelledby="detail-related-heading">
                            <h2 class="front-taza-section-title" id="detail-related-heading">متعلقہ شمارے</h2>
                            <div class="row g-3">
                                @foreach ($magazine->relatedMagazines as $relatedMagazine)
                                    <div class="col-sm-6 col-md-4 col-lg-3">
                                        <article class="front-weekly-card front-taza-related-magazine-card">
                                            @if ($relatedMagazine->cover_image)
                                                <a href="{{ $relatedMagazine->frontend_url }}"><img
                                                        class="front-weekly-card__cover"
                                                        src="{{ asset($relatedMagazine->cover_image) }}"
                                                        alt="{{ $relatedMagazine->title }}" width="300"
                                                        height="475"></a>
                                            @endif
                                            <h3><a
                                                    href="{{ $relatedMagazine->frontend_url }}">{{ $relatedMagazine->title }}</a>
                                            </h3>
                                            @if ($relatedMagazine->publish_date)
                                                <time class="front-taza-meta front-ui"
                                                    datetime="{{ $relatedMagazine->publish_date->toDateString() }}">{{ $relatedMagazine->publish_date->format('d M Y') }}</time>
                                            @endif
                                            @if ($relatedMagazine->issue_number)
                                                <p class="front-taza-meta front-ui">شمارہ نمبر:
                                                    {{ $relatedMagazine->issue_number }}</p>
                                            @endif
                                            <a class="front-weekly-card__button front-ui"
                                                href="{{ $relatedMagazine->frontend_url }}">آن لائن پڑھیں</a>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </main>
            </div>
        </div>
    </div>
@endsection
