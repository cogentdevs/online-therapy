<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Login | {{ $generalSetting?->app_name ?? 'Digital Magazine' }}</title>
    @if ($generalSetting?->favicon)
        <link rel="icon" href="{{ asset($generalSetting->favicon) }}">
    @endif

    @vite(['resources/sass/admin/login.scss', 'resources/js/admin/login.js'])
</head>

<body>
    <main class="admin-login-page">
        <div class="admin-login-decoration admin-login-decoration-top" aria-hidden="true"></div>
        <div class="admin-login-decoration admin-login-decoration-bottom" aria-hidden="true"></div>

        <section class="admin-login-card" aria-labelledby="admin-login-heading">
            <div class="admin-brand" aria-label="{{ $generalSetting?->app_name ?? 'Digital Magazine' }}">
                @if (!empty($generalSetting?->logo))
                    <img class="admin-brand-logo" src="{{ asset($generalSetting->logo) }}"
                        alt="{{ $generalSetting?->app_name ?? 'Digital Magazine' }} logo">
                @else
                    <span class="admin-brand-mark" aria-hidden="true">DM</span>
                @endif
                <span class="admin-brand-name">{{ $generalSetting?->app_name ?? 'Digital Magazine' }}</span>
                <span class="admin-brand-label">Admin Portal</span>
            </div>

            <header class="admin-login-header">
                {{-- <h1 id="admin-login-heading">Welcome Back!</h1> --}}
                <p>Sign in to access the admin dashboard</p>
            </header>

            <form method="POST" action="{{ route('admin.login.submit') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text" aria-hidden="true">
                            <svg viewBox="0 0 24 24" role="img">
                                <path d="M4 6.5h16v11H4zM4.5 7l7.5 6 7.5-6" />
                            </svg>
                        </span>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" placeholder="Enter your email address"
                            autocomplete="email" autofocus required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text" aria-hidden="true">
                            <svg viewBox="0 0 24 24" role="img">
                                <path d="M7 10V8a5 5 0 0 1 10 0v2M5 10h14v10H5z" />
                            </svg>
                        </span>
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" name="password"
                            placeholder="Enter your password" autocomplete="current-password" required>
                        <button class="btn password-toggle" type="button" data-password-toggle
                            aria-label="Show password" aria-pressed="false">
                            <svg class="password-eye" viewBox="0 0 24 24" role="img">
                                <path d="M2.5 12s3.5-5 9.5-5 9.5 5 9.5 5-3.5 5-9.5 5-9.5-5-9.5-5z" />
                                <circle cx="12" cy="12" r="2.5" />
                            </svg>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="admin-login-options">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember"
                            @checked(old('remember'))>
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Forgot Password?</a>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary admin-login-submit">
                    <span>Log In</span>
                    <span aria-hidden="true">&rarr;</span>
                </button>
            </form>

            <footer class="admin-login-footer">
                &copy; {{ now()->year }} {{ $generalSetting?->app_name ?? 'Digital Magazine' }}. All rights
                reserved.
            </footer>
        </section>
    </main>
</body>

</html>
