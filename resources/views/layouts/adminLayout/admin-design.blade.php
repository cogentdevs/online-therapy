<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $generalSetting?->app_name ?? 'Digital Magazine') | {{$generalSetting?->app_name}}</title>

    @if ($generalSetting?->favicon)
        <link rel="icon" href="{{ asset($generalSetting->favicon) }}">
    @endif

    @vite(['resources/sass/admin/admin.scss', 'resources/js/admin/admin.js'])
    @stack('styles')
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        @include('layouts.adminLayout.admin-sidebar')

        <button class="admin-sidebar-backdrop" type="button" data-sidebar-close aria-label="Close navigation"></button>

        <div class="admin-main">
            @include('layouts.adminLayout.admin-header')

            <main class="admin-content">
                @yield('content')
            </main>

            @include('layouts.adminLayout.admin-footer')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
