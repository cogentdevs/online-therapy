@extends('layouts.frontLayout.front-design')

@section('title', 'Plan Videos')
@section('meta_description', 'Watch Videos included in your active subscription plan')

@section('content')
    <div class="front-account-page">
        <div class="container-fluid front-account-container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="Breadcrumb">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> Home</a>
                <span aria-hidden="true">/</span><a href="{{ route('front.account') }}">My Account</a>
                <span aria-hidden="true">/</span><a href="{{ route('front.account.subscriptions') }}">My Subscriptions</a>
                <span aria-hidden="true">/</span><span aria-current="page">Plan Videos</span>
            </nav>

            <header class="front-account-heading">
                <h1>{{ $userSubscription->product_name ?: $userSubscription->subscriptionProduct?->name }}</h1>
                <span aria-hidden="true"></span>
                <p>Videos included in your active subscription plan.</p>
            </header>

            <div class="row g-4 align-items-start front-account-layout">
                <aside class="col-12 col-lg-3">@include('frontend.user-account.partials.sidebar')</aside>
                <div class="col-12 col-lg-9">
                    <section class="front-account-card user-subscription-history">
                        <header class="user-subscriptions-section-header">
                            <div>
                                <h2>Included Videos</h2>
                                <p>Valid until: {{ $userSubscription->end_date?->format('d M Y') ?? 'No end date' }}</p>
                            </div>
                            <a href="{{ route('front.account.subscriptions') }}"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> My Subscriptions</a>
                        </header>

                        @forelse ($userSubscription->subscriptionProduct->videos as $video)
                            <article class="border-bottom py-4 d-flex flex-column flex-md-row align-items-md-center gap-3">
                                @if ($video->thumbnail)
                                    <img src="{{ asset($video->thumbnail) }}" alt="{{ $video->title }}" class="rounded" width="144" height="90" style="object-fit: cover;">
                                @endif
                                <div class="flex-grow-1">
                                    <h3 class="h5 mb-2">{{ $video->title }}</h3>
                                    @if ($video->short_description)
                                        <p class="mb-0 text-muted">{{ $video->short_description }}</p>
                                    @endif
                                </div>
                                <a class="btn btn-outline-primary flex-shrink-0" href="{{ route('front.account.subscriptions.videos.watch', [$userSubscription, $video]) }}" target="_blank" rel="noopener">
                                    <i class="fa-solid fa-play me-1" aria-hidden="true"></i> Watch
                                </a>
                            </article>
                        @empty
                            <div class="user-subscriptions-empty">
                                <i class="fa-solid fa-video" aria-hidden="true"></i>
                                <p>No videos are currently available for this plan.</p>
                            </div>
                        @endforelse
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
