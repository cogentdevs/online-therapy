<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (request()->attributes->has('site_visit_token'))
        <meta name="site-visit-token" content="{{ request()->attributes->get('site_visit_token') }}">
        <meta name="site-visit-heartbeat-url" content="{{ route('front.site-visits.heartbeat') }}">
    @endif
    <meta name="description" content="@yield('meta_description', 'Digital Magazine — quality journalism, articles, and weekly issues')">

    <title>@yield('title', 'Home') | {{ $generalSetting?->app_name ?? 'Digital Magazine' }}</title>

    @if ($generalSetting?->favicon)
        <link rel="icon" href="{{ asset($generalSetting->favicon) }}">
    @endif

    @if ($generalSetting?->google_ads_client_id)
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ urlencode($generalSetting->google_ads_client_id) }}" crossorigin="anonymous"></script>
    @endif

    @vite(['resources/sass/front/front.scss', 'resources/js/front/front.js'])
    @stack('styles')
</head>
<body class="front-body">
    <a class="visually-hidden-focusable front-ui" href="#front-main-content">Skip to main content</a>

    @include('layouts.frontLayout.front-header')

    <main id="front-main-content">
        @yield('content')
    </main>

    @include('layouts.frontLayout.front-footer')

    @guest('web')
        @include('frontend.partials.login-modal')
    @endguest

    @stack('scripts')
</body>
</html>
