@extends('layouts.frontLayout.front-design')

@section('title', 'Log In')
@section('meta_description', 'Log in to your Digital Magazine account')

@section('content')
    <div class="front-auth-page front-auth-page--login">
        <div class="container-fluid front-auth-page__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="Breadcrumb">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> Home</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Log In</span>
            </nav>

            <div class="front-auth-card front-auth-card--login">
                <aside class="front-auth-card__aside">
                    <div class="front-auth-brand">
                        @if ($generalSetting?->logo)
                            <img src="{{ asset($generalSetting->logo) }}" alt="{{ $generalSetting->app_name ?? 'Digital Magazine' }}">
                        @else
                            <strong>Digital Magazine</strong>
                        @endif
                    </div>
                    <h2>Welcome Back</h2>
                    <p>Log in to access quality articles, magazines, and other exclusive content.</p>
                    <div class="front-auth-illustration front-auth-illustration--login" aria-hidden="true">
                        <i class="fa-regular fa-user"></i>
                        <span class="front-auth-illustration__line"></span>
                        <span class="front-auth-illustration__line front-auth-illustration__line--short"></span>
                        <i class="fa-solid fa-lock front-auth-illustration__lock"></i>
                    </div>
                </aside>

                <section class="front-auth-card__content">
                    <header class="front-auth-heading">
                        <h1>Log In</h1>
                        <span aria-hidden="true"></span>
                        <p>Access your account</p>
                    </header>

                    @if (session('success'))
                        <div class="alert alert-success" role="status">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="front-auth-form front-auth-form--login" method="post" action="{{ route('front.login.store') }}">
                        @csrf
                        <div class="front-auth-field">
                            <label for="login-email">Email</label>
                            <div class="front-auth-control">
                                <input id="login-email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" inputmode="email" placeholder="Enter your email" required>
                                <i class="fa-regular fa-envelope front-auth-field__icon" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="front-auth-field">
                            <label for="login-password">Password</label>
                            <div class="front-auth-control front-auth-control--password">
                                <input id="login-password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required>
                                <i class="fa-solid fa-lock front-auth-field__icon" aria-hidden="true"></i>
                                <button class="front-auth-password-toggle" type="button" data-front-password-toggle aria-controls="login-password" aria-pressed="false" aria-label="Show password" data-show-label="Show password" data-hide-label="Hide password"><i class="fa-regular fa-eye" aria-hidden="true"></i></button>
                            </div>
                        </div>
                        <div class="front-auth-options">
                            <label class="front-auth-remember" for="remember">
                                <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))>
                                <span>Remember me</span>
                            </label>
                            <a class="front-auth-forgot" href="{{ route('front.password.request') }}">Forgot password?</a>
                        </div>

                        <button class="front-auth-submit" type="submit">Log In <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                        <p class="front-auth-switch">Don't have an account? <a href="{{ route('front.register') }}">Register</a></p>
                    </form>
                </section>
            </div>
        </div>
    </div>
@endsection
