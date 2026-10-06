@extends('layouts.frontLayout.front-design')

@section('title', 'Set a New Password')

@section('content')
    <div class="front-auth-page">
        <div class="container-fluid front-auth-page__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="Breadcrumb">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> Home</a>
                <span aria-hidden="true">/</span><span aria-current="page">New Password</span>
            </nav>
            <div class="front-auth-card front-auth-card--compact">
                <section class="front-auth-card__content">
                    <header class="front-auth-heading"><h1>Set a New Password</h1><span aria-hidden="true"></span><p>Choose a new password with at least 8 characters for your account.</p></header>
                    @if (! $isValid)
                        <div class="alert alert-danger" role="alert">This password-reset link is invalid or has expired.</div>
                        <a class="front-auth-submit" href="{{ route('front.password.request') }}">Request a New Reset Link</a>
                    @else
                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                        @endif
                        <form class="front-auth-form front-auth-form--login" method="post" action="{{ route('front.password.update') }}" data-front-reset-form>
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="front-auth-field"><label for="reset-email">Email</label><div class="front-auth-control">
                                <input id="reset-email" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="email" dir="ltr" readonly required>
                                <i class="fa-regular fa-envelope front-auth-field__icon" aria-hidden="true"></i>
                            </div></div>
                            @foreach ([['password', 'New Password', 'new-password'], ['password_confirmation', 'Confirm Password', 'new-password']] as [$name, $label, $autocomplete])
                                <div class="front-auth-field"><label for="reset-{{ $name }}">{{ $label }}</label><div class="front-auth-control front-auth-control--password">
                                    <input id="reset-{{ $name }}" name="{{ $name }}" type="password" autocomplete="{{ $autocomplete }}" minlength="8" required>
                                    <i class="fa-solid fa-lock front-auth-field__icon" aria-hidden="true"></i>
                                    <button class="front-auth-password-toggle" type="button" data-front-password-toggle aria-controls="reset-{{ $name }}" aria-pressed="false" aria-label="Show password" data-show-label="Show password" data-hide-label="Hide password"><i class="fa-regular fa-eye" aria-hidden="true"></i></button>
                                </div></div>
                            @endforeach
                            <button class="front-auth-submit" type="submit" disabled>Change Password <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                        </form>
                    @endif
                </section>
            </div>
        </div>
    </div>
@endsection
