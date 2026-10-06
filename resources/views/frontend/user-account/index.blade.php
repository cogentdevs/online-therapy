@extends('layouts.frontLayout.front-design')

@section('title', 'میرا اکاؤنٹ')
@section('meta_description', 'اپنے ڈیجیٹل میگزین اکاؤنٹ کا خلاصہ دیکھیں')

@section('content')
    <div class="front-account-page">
        <div class="container-fluid front-account-container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">میرا اکاؤنٹ</span>
            </nav>

            <header class="front-account-heading">
                <h1>میرا اکاؤنٹ</h1>
                <span aria-hidden="true"></span>
                <p>اپنی معلومات اور اکاؤنٹ کا خلاصہ یہاں دیکھیں۔</p>
            </header>

            <div class="row g-4 align-items-start front-account-layout">
                <aside class="col-12 col-lg-3">
                    @include('frontend.user-account.partials.sidebar')
                </aside>

                <div class="col-12 col-lg-9">
                    <div class="row g-4 front-account-card-grid">
                        <div class="col-12 col-xl-6">
                            <section class="front-account-card front-account-summary">
                                <div class="front-account-avatar">
                                    <img src="{{ $user->frontendProfileImageUrl() }}" alt="{{ $user->name }}">
                                </div>
                                <div class="front-account-user">
                                    <div class="front-account-user-title">
                                        <h2>{{ $user->name }}</h2>
                                        <span class="front-account-status"><i class="fa-solid fa-circle"
                                                aria-hidden="true"></i> فعال رکن</span>
                                    </div>
                                    <p dir="ltr"><i class="fa-regular fa-envelope" aria-hidden="true"></i>
                                        {{ $user->email }}</p>
                                    <p dir="ltr"><i class="fa-solid fa-phone" aria-hidden="true"></i>
                                        {{ $user->phone ?: '—' }}</p>
                                </div>
                            </section>
                        </div>
                        <div class="col-12 col-xl-6">
                            <section class="front-account-card front-account-empty-card front-account-subscription-summary">
                                <div class="front-account-card-heading">
                                    <span class="front-account-card-icon"><i class="fa-solid fa-crown"
                                            aria-hidden="true"></i></span>
                                    <div><small>موجودہ اکاؤنٹ</small>
                                        <h2>فعال سبسکرپشنز</h2>
                                    </div>
                                </div>

                                @if ($activeSubscriptions->isEmpty())
                                    <p>آپ کی کوئی فعال سبسکرپشن موجود نہیں ہے۔</p>
                                    <a class="front-account-outline-action" href="{{ route('front.subscriptions') }}">پلانز
                                        دیکھیں <i class="fa-solid fa-arrow-left" aria-hidden="true"></i></a>
                                @else
                                    <div class="front-account-subscription-count">
                                        <strong>{{ $activeSubscriptions->count() }}</strong>
                                        <span>فعال سبسکرپشنز</span>
                                    </div>

                                    <section class="front-account-active-access" aria-labelledby="active-access-title">
                                        <h3 id="active-access-title">فعال رسائی</h3>
                                        <div class="front-account-active-modules">
                                            @foreach ($activeEntitlementTypes as $type)
                                                <span data-active-access-module="{{ $type->getKey() }}">
                                                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                                                    {{ $type->frontend_label }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </section>

                                    <section class="front-account-active-packages" aria-labelledby="active-packages-title">
                                        @if ($remainingActiveSubscriptionCount > 0)
                                            <p class="front-account-active-more">+{{ $remainingActiveSubscriptionCount }}
                                                مزید فعال سبسکرپشن</p>
                                        @endif
                                    </section>

                                    <a class="front-account-outline-action"
                                        href="{{ route('front.account.subscriptions') }}">
                                        تمام سبسکرپشنز دیکھیں <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                                    </a>
                                @endif
                            </section>
                        </div>
                        <div class="col-12 col-xl-6">
                            <section class="front-account-card front-account-list-card">
                                <header>
                                    <h2><i class="fa-regular fa-bookmark" aria-hidden="true"></i> بک مارکس</h2>
                                    <a href="{{ route('front.account.bookmarks') }}">تمام بک مارکس دیکھیں <i class="fa-solid fa-arrow-left" aria-hidden="true"></i></a>
                                </header>
                                @forelse ($recentBookmarks as $bookmark)
                                    @if ($loop->first)<div class="front-account-bookmark-list">@endif
                                    <article class="front-account-bookmark-item">
                                        <span>{{ $bookmark->frontend_module_label }}</span>
                                        <div>
                                            <strong>
                                                @if ($bookmark->frontend_url)
                                                    <a href="{{ $bookmark->frontend_url }}">{{ $bookmark->frontend_title }}</a>
                                                @else
                                                    {{ $bookmark->frontend_title }}
                                                @endif
                                            </strong>
                                            <time datetime="{{ $bookmark->created_at?->toDateString() }}">{{ $bookmark->created_at?->format('d M Y') ?? '—' }}</time>
                                        </div>
                                        @if ($bookmark->frontend_url)
                                            <a href="{{ $bookmark->frontend_url }}" aria-label="{{ $bookmark->frontend_title }} دیکھیں"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i></a>
                                        @else
                                            <span class="is-unavailable" aria-label="مواد دستیاب نہیں"><i class="fa-solid fa-ban" aria-hidden="true"></i></span>
                                        @endif
                                    </article>
                                    @if ($loop->last)</div>@endif
                                @empty
                                    <div class="front-account-empty-state"><i class="fa-regular fa-bookmark" aria-hidden="true"></i>
                                        <p>ابھی کوئی بک مارک محفوظ نہیں کیا گیا۔</p>
                                    </div>
                                @endforelse
                            </section>
                        </div>
                        <div class="col-12 col-xl-6">
                            <section class="front-account-card front-account-list-card">
                                <header>
                                    <h2><i class="fa-regular fa-clock" aria-hidden="true"></i> حالیہ سرگرمیاں</h2>
                                    <a href="{{ route('front.account.recent-activities') }}">تمام سرگرمیاں دیکھیں <i class="fa-solid fa-arrow-left" aria-hidden="true"></i></a>
                                </header>
                                @forelse ($recentActivities as $activity)
                                    @if ($loop->first)<div class="front-account-activity-list">@endif
                                    <article class="front-account-activity-item">
                                        <span>{{ $activity->frontend_module_label }}</span>
                                        <div>
                                            <strong>
                                                @if ($activity->frontend_url)
                                                    <a href="{{ $activity->frontend_url }}">{{ $activity->frontend_title }}</a>
                                                @else
                                                    {{ $activity->frontend_title }}
                                                @endif
                                            </strong>
                                            <time datetime="{{ $activity->last_visited_at?->toIso8601String() }}">{{ $activity->frontend_last_visited_at ?? '—' }}</time>
                                        </div>
                                        @if ($activity->frontend_url)
                                            <a href="{{ $activity->frontend_url }}" aria-label="{{ $activity->frontend_title }} دیکھیں"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i></a>
                                        @else
                                            <span class="is-unavailable" aria-label="مواد دستیاب نہیں"><i class="fa-solid fa-ban" aria-hidden="true"></i></span>
                                        @endif
                                    </article>
                                    @if ($loop->last)</div>@endif
                                @empty
                                    <div class="front-account-empty-state"><i class="fa-regular fa-clock" aria-hidden="true"></i>
                                        <p>ابھی کوئی حالیہ سرگرمی موجود نہیں۔</p>
                                    </div>
                                @endforelse
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
