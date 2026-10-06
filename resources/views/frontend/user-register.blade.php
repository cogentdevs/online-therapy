@extends('layouts.frontLayout.front-design')

@section('title', 'Register')
@section('meta_description', 'Create a new Digital Magazine user account')

@section('content')
    <div class="front-auth-page front-auth-page--register">
        <div class="container-fluid front-auth-page__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">رجسٹریشن</span>
            </nav>

            <div class="front-auth-card">
                <aside class="front-auth-card__aside">
                    <div class="front-auth-brand">
                        @if ($generalSetting?->logo)
                            <img src="{{ asset($generalSetting->logo) }}" alt="{{ $generalSetting->app_name ?? 'Digital Magazine' }}">
                        @else
                            <strong>Digital Magazine</strong>
                        @endif
                    </div>
                    <h2>آج ہی رجسٹر ہوں</h2>
                    <p>اپنا اکاؤنٹ بنائیں اور معیاری مضامین اور خصوصی مواد تک رسائی حاصل کریں۔</p>
                    <ul class="front-auth-benefits">
                        <li><span><i class="fa-regular fa-file-lines" aria-hidden="true"></i></span><div><strong>مضامین تک رسائی</strong><small>معیاری اور مستند مضامین پڑھیں</small></div></li>
                        {{-- Temporarily hidden - feature retained for future use.
                        <li><span><i class="fa-solid fa-book-open" aria-hidden="true"></i></span><div><strong>ہفتہ وار میگزین</strong><small>تازہ شمارے آن لائن پڑھیں</small></div></li>
                        --}}
                        <li><span><i class="fa-regular fa-bell" aria-hidden="true"></i></span><div><strong>تازہ ترین اپڈیٹس</strong><small>اہم خبروں اور مضامین سے باخبر رہیں</small></div></li>
                    </ul>
                    <div class="front-auth-illustration" aria-hidden="true">
                        <i class="fa-regular fa-user"></i>
                        <span class="front-auth-illustration__line"></span>
                        <span class="front-auth-illustration__line front-auth-illustration__line--short"></span>
                    </div>
                </aside>

                <section class="front-auth-card__content">
                    <header class="front-auth-heading">
                        <h1>رجسٹریشن</h1>
                        <span aria-hidden="true"></span>
                        <p>اپنا اکاؤنٹ بنائیں</p>
                    </header>

                    <form class="front-auth-form" method="post" action="{{ route('front.register.store') }}" data-front-register-form>
                        @csrf
                        @if (session('success'))
                            <div class="alert alert-success" role="status">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-12 col-md-6 front-auth-field">
                                <label for="register-first-name">پہلا نام <span aria-hidden="true">*</span></label>
                                <div class="front-auth-control">
                                    <input id="register-first-name" name="first_name" value="{{ old('first_name') }}" type="text" autocomplete="given-name" placeholder="اپنا پہلا نام درج کریں" required>
                                    <i class="fa-regular fa-user front-auth-field__icon" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 front-auth-field">
                                <label for="register-last-name">آخری نام <span aria-hidden="true">*</span></label>
                                <div class="front-auth-control">
                                    <input id="register-last-name" name="last_name" value="{{ old('last_name') }}" type="text" autocomplete="family-name" placeholder="اپنا آخری نام درج کریں" required>
                                    <i class="fa-regular fa-user front-auth-field__icon" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 front-auth-field">
                                <label for="register-email">ای میل <span aria-hidden="true">*</span></label>
                                <div class="front-auth-control">
                                    <input id="register-email" name="email" value="{{ old('email') }}" type="email" autocomplete="email" inputmode="email" placeholder="اپنی ای میل درج کریں" required dir="ltr">
                                    <i class="fa-regular fa-envelope front-auth-field__icon" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 front-auth-field">
                                <label for="register-phone">فون نمبر <span aria-hidden="true">*</span></label>
                                <div class="front-auth-control">
                                    <input id="register-phone" name="phone" value="{{ old('phone') }}" type="tel" autocomplete="tel" inputmode="tel" placeholder="اپنا فون نمبر درج کریں" required dir="ltr">
                                    <i class="fa-solid fa-phone front-auth-field__icon" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 front-auth-field">
                                <label for="register-password">پاس ورڈ <span aria-hidden="true">*</span></label>
                                <div class="front-auth-control front-auth-control--password">
                                    <input id="register-password" name="password" type="password" autocomplete="new-password" minlength="8" placeholder="پاس ورڈ درج کریں" required>
                                    <i class="fa-solid fa-lock front-auth-field__icon" aria-hidden="true"></i>
                                    <button class="front-auth-password-toggle" type="button" data-front-password-toggle aria-controls="register-password" aria-pressed="false" aria-label="پاس ورڈ دکھائیں" data-show-label="پاس ورڈ دکھائیں" data-hide-label="پاس ورڈ چھپائیں"><i class="fa-regular fa-eye" aria-hidden="true"></i></button>
                                </div>
                                <small>کم از کم 8 حروف</small>
                            </div>
                            <div class="col-12 col-md-6 front-auth-field">
                                <label for="register-password-confirmation">پاس ورڈ کی تصدیق کریں <span aria-hidden="true">*</span></label>
                                <div class="front-auth-control front-auth-control--password">
                                    <input id="register-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" placeholder="پاس ورڈ دوبارہ درج کریں" required>
                                    <i class="fa-solid fa-lock front-auth-field__icon" aria-hidden="true"></i>
                                    <button class="front-auth-password-toggle" type="button" data-front-password-toggle aria-controls="register-password-confirmation" aria-pressed="false" aria-label="پاس ورڈ دکھائیں" data-show-label="پاس ورڈ دکھائیں" data-hide-label="پاس ورڈ چھپائیں"><i class="fa-regular fa-eye" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="front-register-captcha">
                            {!! app('captcha')->display(['data-callback' => 'frontRegisterCaptchaComplete', 'data-expired-callback' => 'frontRegisterCaptchaReset', 'data-error-callback' => 'frontRegisterCaptchaReset']) !!}
                        </div>

                        <button class="front-auth-submit" type="submit" disabled>رجسٹر کریں <i class="fa-solid fa-arrow-left" aria-hidden="true"></i></button>
                        <p class="front-auth-switch">پہلے سے اکاؤنٹ موجود ہے؟ <a href="{{ route('front.login') }}">لاگ ان کریں</a></p>
                    </form>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.frontRegisterCaptchaComplete = function () {
            window.frontRegisterCaptchaReady = true;
            window.dispatchEvent(new Event('front-register-captcha'));
        };
        window.frontRegisterCaptchaReset = function () {
            window.frontRegisterCaptchaReady = false;
            window.dispatchEvent(new Event('front-register-captcha'));
        };
    </script>
    {!! app('captcha')->renderJs('ur') !!}
@endpush
