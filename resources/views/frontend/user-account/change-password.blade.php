@extends('layouts.frontLayout.front-design')

@section('title', 'پاس ورڈ تبدیل کریں')
@section('meta_description', 'اپنے ڈیجیٹل میگزین اکاؤنٹ کا پاس ورڈ تبدیل کریں')

@section('content')
    <div class="front-account-page">
        <div class="container-fluid front-account-container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('front.account') }}">میرا اکاؤنٹ</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">پاس ورڈ تبدیل کریں</span>
            </nav>

            <header class="front-account-heading">
                <h1>پاس ورڈ تبدیل کریں</h1>
                <span aria-hidden="true"></span>
                <p>اپنے اکاؤنٹ کی حفاظت کے لیے ایک مضبوط اور منفرد پاس ورڈ منتخب کریں۔</p>
            </header>

            <div class="row g-4 align-items-start front-account-layout">
                <aside class="col-12 col-lg-3">
                    @include('frontend.user-account.partials.sidebar')
                </aside>

                <div class="col-12 col-lg-9">
                    <section class="front-account-card front-account-password-card">
                        <header>
                            <h2>پاس ورڈ تبدیل کریں</h2>
                            <p>موجودہ پاس ورڈ کی تصدیق کے بعد کم از کم 8 حروف کا نیا پاس ورڈ درج کریں۔</p>
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

                        <form class="front-auth-form front-account-password-form" method="post"
                            action="{{ route('front.account.change-password.update') }}" data-front-change-password-form>
                            @csrf
                            @foreach ([
                                ['current_password', 'موجودہ پاس ورڈ', 'current-password'],
                                ['password', 'نیا پاس ورڈ', 'new-password'],
                                ['password_confirmation', 'نئے پاس ورڈ کی تصدیق', 'new-password'],
                            ] as [$name, $label, $autocomplete])
                                <div class="front-auth-field">
                                    <label for="account-{{ $name }}">{{ $label }}</label>
                                    <div class="front-auth-control front-auth-control--password">
                                        <input id="account-{{ $name }}" name="{{ $name }}" type="password"
                                            autocomplete="{{ $autocomplete }}" @if ($name !== 'current_password') minlength="8" @endif required>
                                        <i class="fa-solid fa-lock front-auth-field__icon" aria-hidden="true"></i>
                                        <button class="front-auth-password-toggle" type="button" data-front-password-toggle
                                            aria-controls="account-{{ $name }}" aria-pressed="false"
                                            aria-label="پاس ورڈ دکھائیں" data-show-label="پاس ورڈ دکھائیں"
                                            data-hide-label="پاس ورڈ چھپائیں">
                                            <i class="fa-regular fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach

                            <button class="front-auth-submit" type="submit" disabled>
                                پاس ورڈ تبدیل کریں <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                            </button>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
