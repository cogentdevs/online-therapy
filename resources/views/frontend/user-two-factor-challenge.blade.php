@extends('layouts.frontLayout.front-design')

@section('title', 'Login Verification')
@section('meta_description', 'Enter your secure verification code to complete login')

@section('content')
    <div class="front-auth-page front-auth-page--login">
        <div class="container-fluid front-auth-page__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span><span aria-current="page">لاگ ان کی تصدیق</span>
            </nav>

            <div class="front-auth-card front-auth-card--compact">
                <section class="front-auth-card__content">
                    <header class="front-auth-heading">
                        <h1>لاگ ان کی تصدیق</h1><span aria-hidden="true"></span>
                        <p><span dir="ltr">{{ $maskedEmail }}</span> پر بھیجا گیا 6 ہندسوں کا کوڈ درج کریں۔</p>
                    </header>

                    @if (session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
                    @if ($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

                    <form class="front-auth-form front-auth-form--login" method="post" action="{{ route('front.two-factor.verify') }}">
                        @csrf
                        <div class="front-auth-field">
                            <label for="login-two-factor-otp">تصدیقی کوڈ</label>
                            <div class="front-auth-control front-two-factor-code">
                                <input id="login-two-factor-otp" name="otp" type="text" inputmode="numeric"
                                    autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" dir="ltr" required autofocus>
                                <i class="fa-solid fa-key front-auth-field__icon" aria-hidden="true"></i>
                            </div>
                        </div>
                        <button class="front-auth-submit" type="submit">تصدیق کریں <i class="fa-solid fa-arrow-left" aria-hidden="true"></i></button>
                    </form>
                    <form class="front-two-factor-resend" method="post" action="{{ route('front.two-factor.resend') }}">
                        @csrf
                        <button type="submit">تصدیقی کوڈ دوبارہ بھیجیں</button>
                    </form>
                </section>
            </div>
        </div>
    </div>
@endsection
