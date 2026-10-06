@extends('layouts.frontLayout.front-design')

@section('title', 'دو مرحلہ توثیق')

@section('content')
    <div class="front-account-page">
        <div class="container-fluid front-account-container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span>/</span><a href="{{ route('front.account') }}">میرا اکاؤنٹ</a><span>/</span>
                <span aria-current="page">دو مرحلہ توثیق</span>
            </nav>
            <header class="front-account-heading"><h1>دو مرحلہ توثیق</h1><span></span><p>اپنے اکاؤنٹ کی حفاظت کے لیے اضافی تصدیقی مرحلہ فعال کریں۔</p></header>
            <div class="row g-4 align-items-start front-account-layout">
                <aside class="col-12 col-lg-3">@include('frontend.user-account.partials.sidebar')</aside>
                <div class="col-12 col-lg-9">
                    <section class="front-account-card front-account-security-card">
                        <header>
                            <div><h2>دو مرحلہ توثیق</h2><p>لاگ ان سکیورٹی کے لیے اپنا تصدیقی طریقہ منتخب کریں۔</p></div>
                            <span class="front-account-status {{ $setting->is_enabled ? '' : 'is-off' }}">{{ $setting->is_enabled ? 'فعال' : 'غیر فعال' }}</span>
                        </header>
                        @if (session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
                        @if ($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

                        @if ($setting->is_enabled)
                            <div class="front-account-security-enabled"><i class="fa-solid fa-circle-check"></i><div><strong>دو مرحلہ توثیق فعال ہے</strong><p>موجودہ طریقہ: {{ $setting->method === 'phone' ? 'فون' : 'ای میل' }}</p>@if ($setting->verified_at)<small>تصدیق: {{ $setting->verified_at->format('d-m-Y') }}</small>@endif</div></div>
                            <div class="front-account-two-factor-actions">
                                <section>
                                    <h3>طریقہ تبدیل کریں</h3>
                                    <form method="post" action="{{ route('front.account.two-factor-authentication.change-method.send') }}" class="front-account-security-form">
                                        @csrf
                                        <label class="front-account-method-option {{ $setting->method === 'email' ? 'is-disabled' : '' }}">
                                            <input type="radio" name="method" value="email" @disabled($setting->method === 'email')>
                                            <i class="fa-regular fa-envelope"></i><span><strong>ای میل</strong><small>{{ $setting->method === 'email' ? 'پہلے ہی فعال ہے' : 'نئے طریقے کے طور پر منتخب کریں' }}</small></span>
                                        </label>
                                        <label class="front-account-method-option is-disabled">
                                            <input type="radio" name="method" value="phone" disabled>
                                            <i class="fa-solid fa-mobile-screen"></i><span><strong>فون</strong><small>جلد دستیاب ہوگا</small></span>
                                        </label>
                                        <button class="front-auth-submit" type="submit">طریقہ تبدیل کریں</button>
                                    </form>
                                </section>
                                <section>
                                    <h3>دو مرحلہ توثیق غیر فعال کریں</h3>
                                    <p>غیر فعال کرنے سے پہلے موجودہ پاس ورڈ اور ای میل کوڈ کی تصدیق ضروری ہے۔</p>
                                    <form method="post" action="{{ route('front.account.two-factor-authentication.disable.send') }}" class="front-account-security-form">
                                        @csrf
                                        <div class="front-auth-field"><label for="disable-current-password">موجودہ پاس ورڈ</label><div class="front-auth-control front-auth-control--password">
                                            <input id="disable-current-password" name="current_password" type="password" autocomplete="current-password" required>
                                            <i class="fa-solid fa-lock front-auth-field__icon"></i>
                                            <button class="front-auth-password-toggle" type="button" data-front-password-toggle aria-controls="disable-current-password" aria-pressed="false" aria-label="پاس ورڈ دکھائیں" data-show-label="پاس ورڈ دکھائیں" data-hide-label="پاس ورڈ چھپائیں"><i class="fa-regular fa-eye"></i></button>
                                        </div></div>
                                        <button class="front-account-danger-action" type="submit">دو مرحلہ توثیق غیر فعال کریں</button>
                                    </form>
                                </section>
                            </div>
                        @else
                            <form method="post" action="{{ route('front.account.two-factor-authentication.send') }}" class="front-account-security-form">
                                @csrf
                                <label class="front-account-method-option">
                                    <input type="radio" name="method" value="email" checked>
                                    <i class="fa-regular fa-envelope"></i><span><strong>ای میل</strong><small>تصدیقی کوڈ آپ کی رجسٹرڈ ای میل پر بھیجا جائے گا۔</small></span>
                                </label>
                                <label class="front-account-method-option is-disabled">
                                    <input type="radio" name="method" value="phone" disabled>
                                    <i class="fa-solid fa-mobile-screen"></i><span><strong>فون</strong><small>جلد دستیاب ہوگا</small></span>
                                </label>
                                <button class="front-auth-submit" type="submit">فعال کریں <i class="fa-solid fa-arrow-left"></i></button>
                            </form>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
