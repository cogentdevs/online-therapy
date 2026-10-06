@extends('layouts.frontLayout.front-design')

@section('title', 'تصدیقی کوڈ')

@section('content')
    <div class="front-account-page">
        <div class="container-fluid front-account-container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}">صفحہ اول</a><span>/</span>
                <a href="{{ route('front.account.two-factor-authentication') }}">دو مرحلہ توثیق</a><span>/</span>
                <span aria-current="page">تصدیق</span>
            </nav>
            <header class="front-account-heading">
                <h1>{{ match($purpose) { 'disable' => 'دو مرحلہ توثیق غیر فعال کریں', 'change_method' => 'نئے طریقے کی تصدیق', default => 'تصدیقی کوڈ' } }}</h1>
                <span></span><p>اپنی ای میل پر موصول ہونے والا 6 ہندسوں کا کوڈ درج کریں۔</p>
            </header>
            <div class="row g-4 align-items-start front-account-layout">
                <aside class="col-12 col-lg-3">@include('frontend.user-account.partials.sidebar')</aside>
                <div class="col-12 col-lg-9">
                    <section class="front-account-card front-account-security-card">
                        @if (session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
                        @if ($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                        <form method="post" action="{{ $verifyRoute }}" class="front-account-otp-form">
                            @csrf
                            <div class="front-auth-field"><label for="two-factor-otp">تصدیقی کوڈ</label><div class="front-auth-control">
                                <input id="two-factor-otp" name="otp" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" dir="ltr" required>
                                <i class="fa-solid fa-key front-auth-field__icon"></i>
                            </div></div>
                            <button class="front-auth-submit" type="submit">تصدیق کریں</button>
                        </form>
                        <form method="post" action="{{ $resendRoute }}" class="front-account-resend-form">
                            @csrf
                            @if ($purpose === 'enable')<input type="hidden" name="method" value="email">@endif
                            <button type="submit">کوڈ دوبارہ بھیجیں</button>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
