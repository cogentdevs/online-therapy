@extends('layouts.frontLayout.front-design')

@section('title', 'Account Activation')

@section('content')
    <div class="front-auth-page">
        <div class="container-fluid front-auth-page__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}">صفحہ اول</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">اکاؤنٹ ایکٹیویشن</span>
            </nav>
            <div class="front-auth-card front-auth-card--login">
                <aside class="front-auth-card__aside">
                    <div class="front-auth-brand">
                        @if ($generalSetting?->logo)
                            <img src="{{ asset($generalSetting->logo) }}"
                                alt="{{ $generalSetting->app_name ?? 'Digital Magazine' }}">
                        @else
                            <strong>Digital Magazine</strong>
                        @endif
                    </div>
                    <h2>خوش آمدید</h2>
                    <p>اپنے اکاؤنٹ کی ایکٹیویشن مکمل کریں۔</p>
                </aside>
                <section class="front-auth-card__content">
                    <header class="front-auth-heading">
                        <h1>اپنا اکاؤنٹ ایکٹیویٹ کریں</h1>
                        <span aria-hidden="true"></span>
                    </header>
                    @if ($state === 'pending')
                        <p role="status">آپ کا اکاؤنٹ ابھی ایکٹیویشن کا منتظر ہے۔</p>
                        <form method="post" action="{{ $activationAction }}" class="front-auth-form">
                            @csrf
                            <button type="submit" class="front-auth-submit">اکاؤنٹ ایکٹیویٹ کریں</button>
                        </form>
                    @elseif ($state === 'active')
                        <div class="alert alert-success" role="status">آپ کا اکاؤنٹ پہلے ہی ایکٹیویٹ ہے۔ آپ لاگ ان کر سکتے
                            ہیں۔</div>
                        <a href="{{ route('front.login') }}" class="front-auth-submit">لاگ ان کریں</a>
                    @elseif ($state === 'expired')
                        <div class="alert alert-warning" role="alert">یہ اکاؤنٹ ایکٹیویشن لنک ایکسپائر ہو چکا ہے۔</div>
                    @else
                        <div class="alert alert-danger" role="alert">یہ اکاؤنٹ ایکٹیویشن لنک درست نہیں ہے۔</div>
                    @endif
                </section>
            </div>
        </div>
    </div>
@endsection
