@extends('layouts.frontLayout.front-design')

@section('title', 'Home')

@section('content')
    @php
        $homeSectionHeadings = $homeSectionHeadings ?? collect();
        $categories = $categories ?? collect();
        $latestArticles = $latestArticles ?? collect();
        $editorialCenterArticles = $editorialCenterArticles ?? collect();
        $editorialFeaturedArticle = $editorialFeaturedArticle ?? null;
        $topViewedMagazines = $topViewedMagazines ?? collect();
        $topViewedArticles = $topViewedArticles ?? collect();
        $topSearchedMagazines = $topSearchedMagazines ?? collect();
        $topSearchedArticles = $topSearchedArticles ?? collect();
        $hasRenderableCategories = $categories->contains(
            static fn ($category): bool => filled($category->image) || filled($category->name),
        );
        $latestArticlesHeading = $homeSectionHeadings->get('latest_articles');
        $belowSliderHomeCardsHeading = $homeSectionHeadings->get('below_slider_home_cards');
        $editorialHeading = $homeSectionHeadings->get('editorial');
        $weeklyMagazineHeading = $homeSectionHeadings->get('weekly_magazine');
        $audioHeading = $homeSectionHeadings->get('audio');
        $newsletterHeading = $homeSectionHeadings->get('newsletter');
        $advertiseWithUsHeading = $homeSectionHeadings->get('advertise_with_us');
        $categoriesHeading = $homeSectionHeadings->get('categories');
        $aboveFooterHomeCardsHeading = $homeSectionHeadings->get('above_footer_home_cards');
        $headingPositionClass = static fn ($heading): string => match ($heading?->content_position) {
            'left' => 'front-home-heading--left',
            'center' => 'front-home-heading--center',
            'right' => 'front-home-heading--right',
            default => '',
        };
    @endphp

    {{-- Hero slider and selected articles --}}
    <section class="front-hero" aria-labelledby="hero-heading">
        <div class="container-fluid front-hero__container">
            <h1 class="visually-hidden" id="hero-heading">ڈیجیٹل میگزین کی اہم خبریں</h1>
            <div class="row g-3 align-items-stretch front-hero__row">
                <div class="col-lg-8 order-1 order-lg-2">
                    @if ($sliders->isNotEmpty())
                        <div class="carousel slide front-hero-carousel" id="frontHeroCarousel" data-bs-ride="carousel"
                            data-bs-interval="6000" data-bs-pause="hover" data-front-carousel>
                            <div class="carousel-indicators">
                                @foreach ($sliders as $slider)
                                    <button class="{{ $loop->first ? 'active' : '' }}" type="button"
                                        data-bs-target="#frontHeroCarousel" data-bs-slide-to="{{ $loop->index }}"
                                        @if ($loop->first) aria-current="true" @endif
                                        aria-label="سلائیڈ {{ $loop->iteration }}"></button>
                                @endforeach
                            </div>
                            <div class="carousel-inner">
                                @foreach ($sliders as $slider)
                                    @php
                                        $positionClass = match ($slider->content_position) {
                                            'top' => 'front-slider-content--top',
                                            'bottom' => 'front-slider-content--bottom',
                                            default => 'front-slider-content--center',
                                        };
                                        $hasCaption =
                                            $slider->top_heading ||
                                            $slider->main_heading ||
                                            $slider->bottom_text ||
                                            ($slider->button_label && $slider->button_url);
                                    @endphp
                                    <article class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                        @if ($slider->image)
                                            @if ($slider->button_url)
                                                <a href="{{ $slider->button_url }}">
                                                    <img class="front-object-cover" src="{{ asset($slider->image) }}"
                                                        alt="{{ $slider->main_heading ?: $slider->top_heading ?: 'Slider image' }}"
                                                        width="960" height="620">
                                                </a>
                                            @else
                                                <img class="front-object-cover" src="{{ asset($slider->image) }}"
                                                    alt="{{ $slider->main_heading ?: $slider->top_heading ?: 'Slider image' }}"
                                                    width="960" height="620">
                                            @endif
                                        @endif
                                        @if ($hasCaption)
                                            <div class="carousel-caption front-slider-content {{ $positionClass }}">
                                                @if ($slider->top_heading)
                                                    <span class="front-tag">{{ $slider->top_heading }}</span>
                                                @endif
                                                @if ($slider->main_heading)
                                                    <h2>
                                                        @if ($slider->button_url)
                                                            <a
                                                                href="{{ $slider->button_url }}">{{ $slider->main_heading }}</a>
                                                        @else
                                                            {{ $slider->main_heading }}
                                                        @endif
                                                    </h2>
                                                @endif
                                                @if ($slider->bottom_text)
                                                    <p>{{ $slider->bottom_text }}</p>
                                                @endif
                                                @if ($slider->button_label && $slider->button_url)
                                                    <a class="front-slider-content__button front-ui"
                                                        href="{{ $slider->button_url }}">{{ $slider->button_label }} <i
                                                            class="fa-solid fa-arrow-left" aria-hidden="true"></i></a>
                                                @endif
                                            </div>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#frontHeroCarousel"
                                data-bs-slide="prev"><span class="carousel-control-prev-icon"
                                    aria-hidden="true"></span><span class="visually-hidden">پچھلی سلائیڈ</span></button>
                            <button class="carousel-control-next" type="button" data-bs-target="#frontHeroCarousel"
                                data-bs-slide="next"><span class="carousel-control-next-icon"
                                    aria-hidden="true"></span><span class="visually-hidden">اگلی سلائیڈ</span></button>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4 order-2 order-lg-1">
                    <aside class="front-latest"
                        @if ($latestArticlesHeading?->main_title) aria-labelledby="latest-heading" @endif>
                        <div class="front-latest__heading">
                            @if ($latestArticlesHeading?->main_title)
                                <h2 class="{{ $headingPositionClass($latestArticlesHeading) }}" id="latest-heading">
                                    {{ $latestArticlesHeading->main_title }}</h2>
                            @endif
                            <a class="front-link" href="{{ route('mazameen') }}">سب دیکھیں</a>
                        </div>
                        @foreach ($latestArticles as $article)
                            @php
                                $categoryName = $article->categories->first()?->name;
                            @endphp
                            <article class="front-latest-item {{ $article->image_available ? '' : 'front-latest-item--without-image' }}">
                                @if ($article->image_available)
                                    <a class="front-latest-item__image" href="{{ $article->frontend_url }}"><img
                                            src="{{ asset($article->image) }}" alt="{{ $article->title ?: 'Article image' }}"
                                            width="132" height="112"></a>
                                @endif
                                <div class="front-latest-item__content">
                                    @if ($categoryName)
                                        <span class="front-meta">{{ $categoryName }}</span>
                                    @endif
                                    @if ($article->title)
                                        <h3><a class="front-article-title-clamp" href="{{ $article->frontend_url }}">{{ $article->title }}</a></h3>
                                    @endif
                                    @if ($article->publish_date)
                                        <time class="front-latest-item__date front-ui"
                                            datetime="{{ $article->publish_date->toDateString() }}">{{ $article->publish_date->format('d M Y') }}</time>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </aside>
                </div>
            </div>
        </div>
    </section>

    {{-- Home cards --}}
    @if ($belowSliderCards->isNotEmpty())
        @php
            $belowSliderCardTitleClass = match ($belowSliderTitlePosition) {
                'left' => 'front-home-card__title--left',
                'center' => 'front-home-card__title--center',
                default => 'front-home-card__title--right',
            };
        @endphp
        <section class="front-home-section-spacing"
            @if ($belowSliderHomeCardsHeading?->main_title) aria-labelledby="cards-heading" @endif>
            <div class="container-fluid front-home-cards__container">
                @if ($belowSliderHomeCardsHeading?->short_title || $belowSliderHomeCardsHeading?->main_title || $belowSliderHomeCardsHeading?->short_detail)
                    <div
                        class="front-section-heading {{ $headingPositionClass($belowSliderHomeCardsHeading) }}">
                        @if ($belowSliderHomeCardsHeading?->short_title)
                            <span
                                class="front-section-heading__eyebrow">{{ $belowSliderHomeCardsHeading->short_title }}</span>
                        @endif
                        @if ($belowSliderHomeCardsHeading?->main_title)
                            <h2 id="cards-heading">{{ $belowSliderHomeCardsHeading->main_title }}</h2>
                        @endif
                        @if ($belowSliderHomeCardsHeading?->short_detail)
                            <p class="front-section-heading__detail">{{ $belowSliderHomeCardsHeading->short_detail }}</p>
                        @endif
                        <span class="front-section-heading__line"></span>
                    </div>
                @endif
                <div class="row g-4">
                    @foreach ($belowSliderCards as $homeCard)
                        <div class="col-sm-6 col-xl-3">
                            <article class="front-card">
                                @if ($homeCard->image && $homeCard->image_available)
                                    <div class="front-home-card__media front-home-card__media--{{ $homeCard->image_fit }}">
                                        <img class="front-home-card__image front-home-card__image--{{ $homeCard->image_fit }}"
                                            src="{{ asset($homeCard->image) }}"
                                            alt="{{ $homeCard->title ?: 'Home card image' }}">
                                    </div>
                                @endif
                                @if ($homeCard->title || $homeCard->description)
                                    <div class="front-card__body">
                                        @if ($homeCard->title)
                                            <h3 class="front-card__title {{ $belowSliderCardTitleClass }}">
                                                {{ $homeCard->title }}</h3>
                                        @endif
                                        @if ($homeCard->description)
                                            <p class="front-home-card__description">{{ $homeCard->description }}</p>
                                        @endif
                                    </div>
                                @endif
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Temporarily hidden - feature retained for future use.
    <section class="front-home-section-spacing" aria-label="اشتہار">
        <div class="container-fluid front-home-ads__container">
            <div class="row g-3">
                <div class="col-lg-8 order-1 order-lg-2">
                    <div class="front-ad-slot front-ad-slot--home">
                        <x-frontend.ad-slot :ad="$frontendAds->get('home')?->get('home_horizontal_large')" size="150 × 1020" />
                    </div>
                </div>
                <div class="col-lg-4 order-2 order-lg-1">
                    <div class="front-ad-slot front-ad-slot--home">
                        <x-frontend.ad-slot :ad="$frontendAds->get('home')?->get('home_horizontal_small')" size="150 × 500" />
                    </div>
                </div>
            </div>
        </div>
    </section>
    --}}

    {{-- Temporarily hidden - feature retained for future use. --}}
    @if (false)
    {{-- Editorial feature and weekly magazine --}}
    <section class="front-editorial-magazine front-home-section-spacing"
        @if ($editorialHeading?->main_title) aria-labelledby="editorial-heading" @endif>
        <div class="container-fluid front-editorial-magazine__container">
            <div class="row g-3 align-items-stretch">
                <div class="col-12">
                    <div class="front-editorial-block">
                        @if ($editorialHeading?->main_title)
                            <h2 class="front-editorial-block__heading {{ $headingPositionClass($editorialHeading) }}"
                                id="editorial-heading">{{ $editorialHeading->main_title }}</h2>
                        @endif
                        <div class="row g-3 align-items-stretch">
                            <div class="col-md-7 order-1 order-md-2">
                                @if ($editorialFeaturedArticle)
                                    @php
                                        $featuredCategoryName = $editorialFeaturedArticle->categories->first()?->name;
                                    @endphp
                                    <article class="front-editorial-main">
                                        <a class="front-editorial-main__image"
                                            href="{{ $editorialFeaturedArticle->frontend_url }}">
                                            <img class="front-object-cover"
                                                src="{{ asset($editorialFeaturedArticle->image_available ? $editorialFeaturedArticle->image : 'images/backend-images/articles/placeholder.png') }}"
                                                alt="{{ $editorialFeaturedArticle->title ?: 'Featured article image' }}"
                                                width="820" height="520">
                                            @if ($editorialFeaturedArticle->title)
                                                <h3>{{ $editorialFeaturedArticle->title }}</h3>
                                            @endif
                                        </a>
                                        <div class="front-editorial-main__body">
                                            @if ($featuredCategoryName)
                                                <span class="front-meta">{{ $featuredCategoryName }}</span>
                                            @endif
                                            @if ($editorialFeaturedArticle->publish_date)
                                                <time class="front-ui"
                                                    datetime="{{ $editorialFeaturedArticle->publish_date->toDateString() }}">{{ $editorialFeaturedArticle->publish_date->format('d M Y') }}</time>
                                            @endif
                                            @if ($editorialFeaturedArticle->short_description)
                                                <p class="front-article-description-clamp">{{ $editorialFeaturedArticle->short_description }}</p>
                                            @endif
                                            <a class="front-editorial-main__button front-ui"
                                                href="{{ $editorialFeaturedArticle->frontend_url }}">مکمل پڑھیں</a>
                                        </div>
                                    </article>
                                @endif
                            </div>

                            <div class="col-md-5 order-2 order-md-1">
                                <div class="front-editorial-list">
                                    @foreach ($editorialCenterArticles as $article)
                                        @php
                                            $categoryName = $article->categories->first()?->name;
                                        @endphp
                                        <article class="front-editorial-item">
                                            <a href="{{ $article->frontend_url }}"><img
                                                    src="{{ asset($article->image_available ? $article->image : 'images/backend-images/articles/placeholder.png') }}"
                                                    alt="{{ $article->title ?: 'Article image' }}" width="94" height="82"></a>
                                            <div>
                                                @if ($categoryName)
                                                    <span class="front-meta">{{ $categoryName }}</span>
                                                @endif
                                                @if ($article->title)
                                                    <h3><a class="front-article-title-clamp"
                                                            href="{{ $article->frontend_url }}">{{ $article->title }}</a></h3>
                                                @endif
                                                @if ($article->publish_date)
                                                    <time class="front-ui"
                                                        datetime="{{ $article->publish_date->toDateString() }}">{{ $article->publish_date->format('d M Y') }}</time>
                                                @endif
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Temporarily hidden - feature retained for future use.
                <div class="col-lg-4 order-2 order-lg-1">
                    @if (($tazaShumara ?? null)?->magazine)
                        @php
                            $weeklyMagazine = $tazaShumara->magazine;
                            $weeklyMagazineCover = $weeklyMagazine->cover_image;
                        @endphp
                        <aside class="front-weekly-card"
                            @if ($weeklyMagazineHeading?->main_title) aria-labelledby="magazine-heading" @endif>
                            @if ($weeklyMagazineHeading?->main_title)
                                <h2 class="{{ $headingPositionClass($weeklyMagazineHeading) }}" id="magazine-heading">
                                    {{ $weeklyMagazineHeading->main_title }}</h2>
                            @endif
                            @if ($weeklyMagazineCover)
                                <img class="front-weekly-card__cover" src="{{ asset($weeklyMagazineCover) }}"
                                    alt="Magazine cover" width="420" height="560">
                            @endif
                            @if ($tazaShumara->show_title && $weeklyMagazine->title)
                                <h3>{{ $weeklyMagazine->title }}</h3>
                            @endif
                            @if ($weeklyMagazine->publish_date)
                                <time class="front-ui"
                                    datetime="{{ $weeklyMagazine->publish_date->toDateString() }}">{{ $weeklyMagazine->publish_date->format('d M Y') }}</time>
                            @endif
                            @if ($weeklyMagazine->issue_number)
                                <p class="front-taza-meta front-ui">شمارہ نمبر: {{ $weeklyMagazine->issue_number }}</p>
                            @endif
                            <a class="front-weekly-card__button front-ui"
                                href="{{ $tazaShumara->magazine_url }}">آن لائن پڑھیں</a>
                        </aside>
                    @endif
                </div>
                --}}
            </div>
        </div>
    </section>
    @endif

    {{-- Banners and frontend login --}}
    @php
        $homepageSideBySideBanner = $sideBySideBanner ?? null;
    @endphp
    <section class="front-home-banners-login front-home-section-spacing" aria-labelledby="front-login-heading">
        <div class="container-fluid front-home-banners-login__container">
            <div class="row g-3 align-items-stretch">
                <div class="col-lg-8 order-1 order-lg-2">
                    <div class="row g-3 front-home-banners-login__banners">
                        <div class="col-md-6">
                            <a class="front-home-banner" href="#">
                                @if ($homepageSideBySideBanner?->image_available)
                                    <img src="{{ asset($homepageSideBySideBanner->image) }}"
                                        alt="Digital Magazine special offer" width="500" height="350">
                                @else
                                    <span class="front-ad-placeholder">
                                        <span class="front-ad-placeholder__label">Advertisement</span>
                                        <span class="front-ad-placeholder__size">500 × 350</span>
                                    </span>
                                @endif
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a class="front-home-banner" href="#">
                                @if ($homepageSideBySideBanner?->image_2_available)
                                    <img src="{{ asset($homepageSideBySideBanner->image_2) }}"
                                        alt="Digital Magazine featured reading" width="500" height="350">
                                @else
                                    <span class="front-ad-placeholder">
                                        <span class="front-ad-placeholder__label">Advertisement</span>
                                        <span class="front-ad-placeholder__size">500 × 350</span>
                                    </span>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 order-2 order-lg-1">
                    <div class="front-home-login-card">
                        @guest('web')
                            <h2 id="front-login-heading">Log In</h2>
                            @if ($errors->any())
                                <div class="alert alert-danger py-2" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form class="front-home-login-card__form" method="post"
                                action="{{ route('front.login.store') }}">
                                @csrf
                                <input type="hidden" name="login_source" value="homepage">
                                <div>
                                    <label class="form-label" for="front-login-email">Email</label>
                                    <input class="form-control front-ui" id="front-login-email" name="email"
                                        type="email" value="{{ old('email') }}" autocomplete="email" inputmode="email"
                                        placeholder="Enter your email" required>
                                </div>
                                <div>
                                    <label class="form-label" for="front-login-password">Password</label>
                                    <div class="front-auth-control front-auth-control--password">
                                        <input class="form-control front-ui" id="front-login-password" name="password"
                                            type="password" autocomplete="current-password"
                                            placeholder="Enter your password" required>
                                        <button class="front-auth-password-toggle" type="button"
                                            data-front-password-toggle aria-controls="front-login-password"
                                            aria-pressed="false" aria-label="Show password"
                                            data-show-label="Show password" data-hide-label="Hide password">
                                            <i class="fa-regular fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="front-home-login-card__options front-ui">
                                    <label class="form-check"><input class="form-check-input" name="remember"
                                            type="checkbox" value="1" @checked(old('remember'))><span
                                            class="form-check-label">Remember me</span></label>
                                    <a href="{{ route('front.password.request') }}">Forgot password?</a>
                                </div>
                                <button class="front-home-login-card__button front-ui" type="submit">Log In</button>
                            </form>
                            <p class="front-home-login-card__footer front-ui">Don't have an account?
                                <a href="{{ route('front.register') }}">Register</a>
                            </p>
                        @else
                            <h2 id="front-login-heading">My Account</h2>
                            <a class="front-home-login-card__button front-ui d-block text-center"
                                href="{{ route('front.account') }}">View My Account</a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="front-most-viewed front-home-section-spacing" aria-labelledby="most-viewed-heading" data-ranked-content>
        <div class="container-fluid front-most-viewed__container">
            <div class="front-most-viewed__header">
                <h2 id="most-viewed-heading"><i class="fa-solid fa-chart-column" aria-hidden="true"></i> سب سے زیادہ دیکھے گئے</h2>
                <div class="nav front-most-viewed__tabs" id="mostViewedTabs" role="tablist" aria-label="سب سے زیادہ دیکھے گئے مواد" data-ranked-tabs>
                    {{-- Temporarily hidden - feature retained for future use.
                    <button class="nav-link active" id="most-viewed-magazines-tab" data-bs-toggle="tab" data-bs-target="#most-viewed-magazines" type="button" role="tab" aria-controls="most-viewed-magazines" aria-selected="true">شمارے</button>
                    --}}
                    <button class="nav-link active" id="most-viewed-articles-tab" data-bs-toggle="tab" data-bs-target="#most-viewed-articles" type="button" role="tab" aria-controls="most-viewed-articles" aria-selected="true">مضامین</button>
                </div>
            </div>

            <div class="tab-content front-most-viewed__content">
                {{-- Temporarily hidden - feature retained for future use.
                <div class="tab-pane fade show active" id="most-viewed-magazines" role="tabpanel" aria-labelledby="most-viewed-magazines-tab" tabindex="0">
                    @if ($topViewedMagazines->isNotEmpty())
                        <div class="front-most-viewed__carousel" data-ranked-carousel>
                            <button class="front-most-viewed__control front-most-viewed__control--previous" type="button" data-ranked-previous aria-label="پچھلا شمارہ"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                            <div class="front-most-viewed__track" data-ranked-track>
                                @foreach ($topViewedMagazines as $magazine)
                                    <article class="front-most-viewed-card" data-ranked-item>
                                        <a class="front-most-viewed-card__media front-most-viewed-card__media--magazine" href="{{ $magazine->frontend_url }}">
                                            @if ($magazine->image_available)
                                                <img src="{{ asset($magazine->cover_image) }}" alt="{{ $magazine->title }}" width="330" height="430">
                                            @else
                                                <span class="front-most-viewed-card__fallback" aria-hidden="true"><i class="fa-regular fa-image"></i></span>
                                            @endif
                                        </a>
                                        <div class="front-most-viewed-card__body">
                                            <h3><a href="{{ $magazine->frontend_url }}">{{ $magazine->title }}</a></h3>
                                            @if ($magazine->publish_date)
                                                <time class="front-ui" datetime="{{ $magazine->publish_date->toDateString() }}">{{ $magazine->publish_date->format('d M Y') }}</time>
                                            @endif
                                            @if ($magazine->issue_number)
                                                <p class="front-ui">شمارہ نمبر: {{ $magazine->issue_number }}</p>
                                            @endif
                                            <span class="front-most-viewed-card__views front-ui" data-most-viewed-count="{{ $magazine->site_visits_count }}"><i class="fa-regular fa-eye" aria-hidden="true"></i> {{ number_format($magazine->site_visits_count) }}</span>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                            <button class="front-most-viewed__control front-most-viewed__control--next" type="button" data-ranked-next aria-label="اگلا شمارہ"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                        </div>
                    @else
                        <p class="front-most-viewed__empty">فی الحال کوئی ڈیٹا دستیاب نہیں</p>
                    @endif
                </div>
                --}}

                <div class="tab-pane fade show active" id="most-viewed-articles" role="tabpanel" aria-labelledby="most-viewed-articles-tab" tabindex="0">
                    @if ($topViewedArticles->isNotEmpty())
                        <div class="front-most-viewed__carousel" data-ranked-carousel>
                            <button class="front-most-viewed__control front-most-viewed__control--previous" type="button" data-ranked-previous aria-label="پچھلا مضمون"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                            <div class="front-most-viewed__track" data-ranked-track>
                                @foreach ($topViewedArticles as $article)
                                    <article class="front-most-viewed-card" data-ranked-item>
                                        <a class="front-most-viewed-card__media" href="{{ $article->frontend_url }}">
                                            @if ($article->image_available)
                                                <img src="{{ asset($article->image) }}" alt="{{ $article->title }}" width="440" height="300">
                                            @else
                                                <span class="front-most-viewed-card__fallback" aria-hidden="true"><i class="fa-regular fa-image"></i></span>
                                            @endif
                                        </a>
                                        <div class="front-most-viewed-card__body">
                                            @if ($article->categories->first()?->name)
                                                <span class="front-most-viewed-card__category">{{ $article->categories->first()->name }}</span>
                                            @endif
                                            <h3><a href="{{ $article->frontend_url }}">{{ $article->title }}</a></h3>
                                            @if ($article->publish_date)
                                                <time class="front-ui" datetime="{{ $article->publish_date->toDateString() }}">{{ $article->publish_date->format('d M Y') }}</time>
                                            @endif
                                            <span class="front-most-viewed-card__views front-ui" data-most-viewed-count="{{ $article->site_visits_count }}"><i class="fa-regular fa-eye" aria-hidden="true"></i> {{ number_format($article->site_visits_count) }}</span>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                            <button class="front-most-viewed__control front-most-viewed__control--next" type="button" data-ranked-next aria-label="اگلا مضمون"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                        </div>
                    @else
                        <p class="front-most-viewed__empty">فی الحال کوئی ڈیٹا دستیاب نہیں</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Audio interviews and newsletter --}}
    <section class="front-audio-newsletter front-home-section-spacing">
        <div class="container-fluid front-audio-newsletter__container">
            <div class="row g-3 align-items-stretch">
                {{-- Temporarily hidden - feature retained for future use.
                <div class="col-lg-8 order-1 order-lg-2">
                    <div class="front-audio-area">
                        <div class="front-audio-area__heading">
                            @if ($audioHeading?->main_title)
                                <h2 class="{{ $headingPositionClass($audioHeading) }}" id="audio-heading">
                                    {{ $audioHeading->main_title }}</h2>
                            @endif
                            <a class="front-link" href="#">تمام دیکھیں</a>
                        </div>
                        <div class="row g-3">
                            @foreach ([['audio-author.svg', 'معاشرے پھر سے خصوصی گفتگو', '18:45', '۱۸ اپریل ۲۰۲۶'], ['audio-expert.svg', 'سیاست دان کے ساتھ خصوصی نشست', '22:10', '۱۷ اپریل ۲۰۲۶'], ['audio-artist.svg', 'ٹیکنالوجی کے مستقبل پر گفتگو', '15:30', '۱۶ اپریل ۲۰۲۶']] as [$image, $title, $duration, $date])
                                <div class="col-md-4">
                                    <article class="front-audio-interview-card">
                                        <a class="front-audio-interview-card__media" href="#">
                                            <img src="{{ asset('images/frontend-images/audio/' . $image) }}"
                                                alt="{{ $title }}" width="480" height="270">
                                            <span class="front-audio-interview-card__play" aria-hidden="true"><i
                                                    class="fa-solid fa-play"></i></span>
                                            <span
                                                class="front-audio-interview-card__duration front-ui">{{ $duration }}</span>
                                        </a>
                                        <div class="front-audio-interview-card__body">
                                            <h3><a href="#">{{ $title }}</a></h3>
                                            <time class="front-ui">{{ $date }}</time>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                --}}

                <div class="col-12">
                    <aside class="front-newsletter"
                        @if ($newsletterHeading?->main_title) aria-labelledby="newsletter-heading" @endif>
                        <div class="front-newsletter__content">
                            @if ($newsletterHeading?->main_title || $newsletterHeading?->short_detail)
                                <div class="{{ $headingPositionClass($newsletterHeading) }}">
                                    @if ($newsletterHeading?->main_title)
                                        <h2 id="newsletter-heading">{{ $newsletterHeading->main_title }}</h2>
                                    @endif
                                    @if ($newsletterHeading?->short_detail)
                                        <p>{{ $newsletterHeading->short_detail }}</p>
                                    @endif
                                </div>
                            @endif
                            <form class="front-newsletter__form" method="post" action="{{ route('front.newsletter.subscribe') }}" data-newsletter-form>
                                @csrf
                                <label class="visually-hidden" for="front-newsletter-email">ای میل ایڈریس</label>
                                <input class="form-control front-ui" id="front-newsletter-email" type="email"
                                    name="email" maxlength="255" required autocomplete="email" placeholder="ای میل درج کریں">
                                <div class="front-newsletter__feedback alert d-none mb-0 py-2 px-3" data-newsletter-feedback role="status" aria-live="polite"></div>
                                <button class="front-newsletter__button front-ui" type="submit">سبسکرائب کریں <i
                                        class="fa-regular fa-envelope" aria-hidden="true"></i></button>
                            </form>
                        </div>
                        <i class="fa-regular fa-envelope-open front-newsletter__visual" aria-hidden="true"></i>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    <section class="front-most-viewed front-most-searched front-home-section-spacing" aria-labelledby="most-searched-heading" data-ranked-content>
        <div class="container-fluid front-most-viewed__container">
            <div class="front-most-viewed__header">
                <h2 id="most-searched-heading"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> سب سے زیادہ تلاش کیے گئے</h2>
                <div class="nav front-most-viewed__tabs" id="mostSearchedTabs" role="tablist" aria-label="سب سے زیادہ تلاش کیے گئے مواد" data-ranked-tabs>
                    {{-- Temporarily hidden - feature retained for future use.
                    <button class="nav-link active" id="most-searched-magazines-tab" data-bs-toggle="tab" data-bs-target="#most-searched-magazines" type="button" role="tab" aria-controls="most-searched-magazines" aria-selected="true">شمارے</button>
                    --}}
                    <button class="nav-link active" id="most-searched-articles-tab" data-bs-toggle="tab" data-bs-target="#most-searched-articles" type="button" role="tab" aria-controls="most-searched-articles" aria-selected="true">مضامین</button>
                </div>
            </div>

            <div class="tab-content front-most-viewed__content">
                {{-- Temporarily hidden - feature retained for future use.
                <div class="tab-pane fade show active" id="most-searched-magazines" role="tabpanel" aria-labelledby="most-searched-magazines-tab" tabindex="0">
                    @if ($topSearchedMagazines->isNotEmpty())
                        <div class="front-most-viewed__carousel" data-ranked-carousel>
                            <button class="front-most-viewed__control front-most-viewed__control--previous" type="button" data-ranked-previous aria-label="پچھلا شمارہ"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                            <div class="front-most-viewed__track" data-ranked-track>
                                @foreach ($topSearchedMagazines as $magazine)
                                    <article class="front-most-viewed-card" data-ranked-item>
                                        <a class="front-most-viewed-card__media front-most-viewed-card__media--magazine" href="{{ $magazine->frontend_url }}">
                                            @if ($magazine->image_available)
                                                <img src="{{ asset($magazine->cover_image) }}" alt="{{ $magazine->title }}" width="330" height="430">
                                            @else
                                                <span class="front-most-viewed-card__fallback" aria-hidden="true"><i class="fa-regular fa-image"></i></span>
                                            @endif
                                        </a>
                                        <div class="front-most-viewed-card__body">
                                            <h3><a href="{{ $magazine->frontend_url }}">{{ $magazine->title }}</a></h3>
                                            @if ($magazine->publish_date)
                                                <time class="front-ui" datetime="{{ $magazine->publish_date->toDateString() }}">{{ $magazine->publish_date->format('d M Y') }}</time>
                                            @endif
                                            @if ($magazine->issue_number)
                                                <p class="front-ui">شمارہ نمبر: {{ $magazine->issue_number }}</p>
                                            @endif
                                            <span class="front-most-viewed-card__views front-ui" data-most-searched-count="{{ $magazine->search_contents_count }}"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> {{ number_format($magazine->search_contents_count) }}</span>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                            <button class="front-most-viewed__control front-most-viewed__control--next" type="button" data-ranked-next aria-label="اگلا شمارہ"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                        </div>
                    @else
                        <p class="front-most-viewed__empty">فی الحال کوئی ڈیٹا دستیاب نہیں</p>
                    @endif
                </div>
                --}}

                <div class="tab-pane fade show active" id="most-searched-articles" role="tabpanel" aria-labelledby="most-searched-articles-tab" tabindex="0">
                    @if ($topSearchedArticles->isNotEmpty())
                        <div class="front-most-viewed__carousel" data-ranked-carousel>
                            <button class="front-most-viewed__control front-most-viewed__control--previous" type="button" data-ranked-previous aria-label="پچھلا مضمون"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                            <div class="front-most-viewed__track" data-ranked-track>
                                @foreach ($topSearchedArticles as $article)
                                    <article class="front-most-viewed-card" data-ranked-item>
                                        <a class="front-most-viewed-card__media" href="{{ $article->frontend_url }}">
                                            @if ($article->image_available)
                                                <img src="{{ asset($article->image) }}" alt="{{ $article->title }}" width="440" height="300">
                                            @else
                                                <span class="front-most-viewed-card__fallback" aria-hidden="true"><i class="fa-regular fa-image"></i></span>
                                            @endif
                                        </a>
                                        <div class="front-most-viewed-card__body">
                                            @if ($article->categories->first()?->name)
                                                <span class="front-most-viewed-card__category">{{ $article->categories->first()->name }}</span>
                                            @endif
                                            <h3><a href="{{ $article->frontend_url }}">{{ $article->title }}</a></h3>
                                            @if ($article->publish_date)
                                                <time class="front-ui" datetime="{{ $article->publish_date->toDateString() }}">{{ $article->publish_date->format('d M Y') }}</time>
                                            @endif
                                            <span class="front-most-viewed-card__views front-ui" data-most-searched-count="{{ $article->search_contents_count }}"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> {{ number_format($article->search_contents_count) }}</span>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                            <button class="front-most-viewed__control front-most-viewed__control--next" type="button" data-ranked-next aria-label="اگلا مضمون"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                        </div>
                    @else
                        <p class="front-most-viewed__empty">فی الحال کوئی ڈیٹا دستیاب نہیں</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Temporarily hidden - feature retained for future use.
    <section class="front-home-promo front-home-section-spacing"
        @if ($advertiseWithUsHeading?->main_title) aria-labelledby="home-promo-heading" @endif>
        <div class="container-fluid front-home-promo__container">
            <div class="row">
                <div class="col-12">
                    <div class="front-home-promo__banner">
                        <div class="front-home-promo__content">
                            @if ($advertiseWithUsHeading?->main_title || $advertiseWithUsHeading?->short_detail)
                                <div class="{{ $headingPositionClass($advertiseWithUsHeading) }}">
                                    @if ($advertiseWithUsHeading?->main_title)
                                        <h2 id="home-promo-heading">{{ $advertiseWithUsHeading->main_title }}</h2>
                                    @endif
                                    @if ($advertiseWithUsHeading?->short_detail)
                                        <p>{{ $advertiseWithUsHeading->short_detail }}</p>
                                    @endif
                                </div>
                            @endif
                            <a class="front-home-promo__button front-ui" href="{{ route('front.advertise') }}">مزید جانیں</a>
                        </div>
                        <div class="front-home-promo__visual">
                            <img src="{{ asset('images/frontend-images/magazine/newspaper-spread.svg') }}"
                                alt="میگزین اور اخبار" width="720" height="230">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    --}}

    {{-- Temporarily hidden - feature retained for future use.
    <section class="front-popular-topics front-home-section-spacing pt-3"
        @if ($categoriesHeading?->main_title) aria-labelledby="popular-topics-heading" @endif>
        <div class="container-fluid front-popular-topics__container">
            @if ($categoriesHeading?->short_title || $categoriesHeading?->main_title || $categoriesHeading?->short_detail)
                <div class="front-section-heading {{ $headingPositionClass($categoriesHeading) }}">
                    @if ($categoriesHeading?->short_title)
                        <span class="front-section-heading__eyebrow">{{ $categoriesHeading->short_title }}</span>
                    @endif
                    @if ($categoriesHeading?->main_title)
                        <h2 id="popular-topics-heading">{{ $categoriesHeading->main_title }}</h2>
                    @endif
                    @if ($categoriesHeading?->short_detail)
                        <p class="front-section-heading__detail">{{ $categoriesHeading->short_detail }}</p>
                    @endif
                    <span class="front-section-heading__line"></span>
                </div>
            @endif
            @if ($hasRenderableCategories)
                <div class="row row-cols-2 row-cols-sm-4 row-cols-xl-8 g-3">
                    @foreach ($categories as $category)
                        @if (filled($category->image) || filled($category->name))
                            <div class="col">
                                <a class="front-topic-card" href="{{ $category->frontend_url }}">
                                    @if ($category->image_available)
                                        <img src="{{ asset($category->image) }}"
                                            alt="{{ $category->name ?: 'Category image' }}">
                                    @else
                                        <span class="front-topic-card__fallback" aria-hidden="true"><i class="fa-solid fa-folder-open"></i></span>
                                    @endif
                                    @if (filled($category->name))
                                        <span class="front-topic-card__name">{{ $category->name }}</span>
                                    @endif
                                    <small>{{ $category->total_content_count }} مواد</small>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="front-popular-topics__action front-ui">
                    <a class="front-taza-button" href="{{ route('front.mozoaat') }}">تمام موضوعات دیکھیں</a>
                </div>
            @endif
        </div>
    </section>
    --}}

    @if ($aboveFooterCards->isNotEmpty())
        @php
            $aboveFooterCardTitleClass = match ($aboveFooterTitlePosition) {
                'left' => 'front-home-card__title--left',
                'center' => 'front-home-card__title--center',
                default => 'front-home-card__title--right',
            };
        @endphp
        <section class="front-home-section-spacing"
            @if ($aboveFooterHomeCardsHeading?->main_title) aria-labelledby="above-footer-cards-heading" @endif>
            <div class="container-fluid front-home-cards__container">
                @if ($aboveFooterHomeCardsHeading?->short_title || $aboveFooterHomeCardsHeading?->main_title || $aboveFooterHomeCardsHeading?->short_detail)
                    <div
                        class="front-section-heading {{ $headingPositionClass($aboveFooterHomeCardsHeading) }}">
                        @if ($aboveFooterHomeCardsHeading?->short_title)
                            <span
                                class="front-section-heading__eyebrow">{{ $aboveFooterHomeCardsHeading->short_title }}</span>
                        @endif
                        @if ($aboveFooterHomeCardsHeading?->main_title)
                            <h2 id="above-footer-cards-heading">{{ $aboveFooterHomeCardsHeading->main_title }}</h2>
                        @endif
                        @if ($aboveFooterHomeCardsHeading?->short_detail)
                            <p class="front-section-heading__detail">{{ $aboveFooterHomeCardsHeading->short_detail }}</p>
                        @endif
                        <span class="front-section-heading__line"></span>
                    </div>
                @endif
                <div class="row g-4">
                    @foreach ($aboveFooterCards as $homeCard)
                        <div class="col-sm-6 col-xl-3">
                            <article class="front-card">
                                @if ($homeCard->image && $homeCard->image_available)
                                    <div
                                        class="front-home-card__media front-home-card__media--{{ $homeCard->image_fit }}">
                                        <img class="front-home-card__image front-home-card__image--{{ $homeCard->image_fit }}"
                                            src="{{ asset($homeCard->image) }}"
                                            alt="{{ $homeCard->title ?: 'Home card image' }}">
                                    </div>
                                @endif
                                @if ($homeCard->title || $homeCard->description)
                                    <div class="front-card__body">
                                        @if ($homeCard->title)
                                            <h3 class="front-card__title {{ $aboveFooterCardTitleClass }}">
                                                {{ $homeCard->title }}</h3>
                                        @endif
                                        @if ($homeCard->description)
                                            <p class="front-home-card__description">{{ $homeCard->description }}</p>
                                        @endif
                                    </div>
                                @endif
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
