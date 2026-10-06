@extends('layouts.frontLayout.front-design')

@section('title', 'پروفائل')
@section('meta_description', 'اپنی ڈیجیٹل میگزین پروفائل معلومات اپ ڈیٹ کریں')

@section('content')
    <div class="front-account-page">
        <div class="container-fluid front-account-container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span><a href="{{ route('front.account') }}">میرا اکاؤنٹ</a>
                <span aria-hidden="true">/</span><span aria-current="page">پروفائل</span>
            </nav>

            <header class="front-account-heading">
                <h1>پروفائل</h1><span aria-hidden="true"></span>
                <p>اپنا نام، ای میل اور پروفائل تصویر یہاں اپ ڈیٹ کریں۔</p>
            </header>

            <div class="row g-4 align-items-start front-account-layout">
                <aside class="col-12 col-lg-3">@include('frontend.user-account.partials.sidebar')</aside>
                <div class="col-12 col-lg-9">
                    <section class="front-account-card front-account-profile-card">
                        <header><h2>پروفائل</h2><p>آپ کی تازہ معلومات آپ کے اکاؤنٹ میں دکھائی جائیں گی۔</p></header>

                        @if (session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                        @endif

                        <form class="front-account-profile-form" method="post" enctype="multipart/form-data"
                            action="{{ route('front.account.profile.update') }}" data-front-profile-form>
                            @csrf
                            @method('PATCH')
                            <div class="front-account-profile-image">
                                <img src="{{ $user->frontendProfileImageUrl() }}" alt="{{ $user->name }}"
                                    data-front-profile-preview data-default-src="{{ asset('images/frontend-images/users/user-avatar.png') }}">
                                <div>
                                    <label class="front-account-image-picker" for="profile-image">تصویر منتخب کریں</label>
                                    <input id="profile-image" name="profile_image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-front-profile-input>
                                    @if ($user->hasCustomFrontendProfileImage())
                                        <button class="front-account-image-remove" type="button" data-front-profile-remove>تصویر ہٹائیں</button>
                                    @endif
                                    <input name="remove_profile_image" type="hidden" value="0" data-front-profile-remove-value>
                                    <small>JPG، JPEG، PNG یا WEBP، زیادہ سے زیادہ 2 ایم بی۔</small>
                                </div>
                            </div>

                            <div class="front-auth-field"><label for="profile-name">نام</label><div class="front-auth-control">
                                <input id="profile-name" name="name" type="text" value="{{ old('name', $user->name) }}" maxlength="255" required>
                                <i class="fa-regular fa-user front-auth-field__icon" aria-hidden="true"></i>
                            </div></div>
                            <div class="front-auth-field"><label for="profile-email">ای میل</label><div class="front-auth-control">
                                <input id="profile-email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" dir="ltr" required>
                                <i class="fa-regular fa-envelope front-auth-field__icon" aria-hidden="true"></i>
                            </div></div>
                            <div class="front-auth-field"><label for="profile-phone">فون نمبر</label><div class="front-auth-control">
                                <input id="profile-phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" maxlength="255" autocomplete="tel" dir="ltr" required>
                                <i class="fa-solid fa-phone front-auth-field__icon" aria-hidden="true"></i>
                            </div></div>
                            <button class="front-auth-submit" type="submit">پروفائل اپ ڈیٹ کریں <i class="fa-solid fa-arrow-left" aria-hidden="true"></i></button>
                        </form>
                    </section>

                </div>
            </div>
        </div>
    </div>
@endsection
