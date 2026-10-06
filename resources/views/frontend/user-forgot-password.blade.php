@extends('layouts.frontLayout.front-design')

@section('title', 'Forgot Password?')

@section('content')
    <div class="front-auth-page">
        <div class="container-fluid front-auth-page__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="Breadcrumb">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> Home</a>
                <span aria-hidden="true">/</span><span aria-current="page">Forgot Password?</span>
            </nav>

            <div class="front-auth-card front-auth-card--compact">
                <section class="front-auth-card__content">
                    <header class="front-auth-heading">
                        <h1>Forgot Password?</h1><span aria-hidden="true"></span>
                        <p>Enter your registered email and we will send you a secure password-reset link.</p>
                    </header>

                    @if (session('success'))
                        <div class="alert alert-success" role="status">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                    @endif

                    <form class="front-auth-form front-auth-form--login" method="post" action="{{ route('front.password.email') }}">
                        @csrf
                        <div class="front-auth-field">
                            <label for="forgot-email">Email</label>
                            <div class="front-auth-control">
                                <input id="forgot-email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" inputmode="email" dir="ltr" required>
                                <i class="fa-regular fa-envelope front-auth-field__icon" aria-hidden="true"></i>
                            </div>
                        </div>
                        <button class="front-auth-submit" type="submit">Send Reset Link <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                        <p class="front-auth-switch"><a href="{{ route('front.login') }}">Back to Log In</a></p>
                    </form>
                </section>
            </div>
        </div>
    </div>
@endsection
