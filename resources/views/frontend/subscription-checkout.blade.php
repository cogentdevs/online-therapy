@extends('layouts.frontLayout.front-design')

@section('title', 'Checkout')
@section('meta_description', 'Selected subscription details and payment method')

@section('content')
    @php
        $currentPrice = $subscriptionProduct->frontend_has_discount
            ? $subscriptionProduct->frontend_discounted_price
            : $subscriptionProduct->frontend_price;
    @endphp

    <main class="subscription-checkout-page" data-subscription-checkout>
        <div class="container-fluid subscription-checkout-page__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('front.subscriptions') }}">سبسکرپشن</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">چیک آؤٹ</span>
            </nav>

            <header class="subscription-checkout-page__heading">
                <h1>چیک آؤٹ</h1>
                <span aria-hidden="true"></span>
                <p>اپنی سبسکرپشن کی تفصیل دیکھیں اور ادائیگی کا طریقہ منتخب کریں۔</p>
            </header>

            @if (session('success'))
                <div class="alert alert-success subscription-checkout-page__alert" role="alert">{{ session('success') }}</div>
            @endif
            @if ($errors->has('subscription'))
                <div class="alert alert-danger subscription-checkout-page__alert" role="alert">{{ $errors->first('subscription') }}</div>
            @endif
            @isset($paymentResubmission)
                <div class="alert alert-warning subscription-checkout-page__alert" role="alert">
                    <strong>Previous payment was rejected.</strong>
                    {{ $paymentResubmission->rejection_reason }}
                    Upload corrected payment evidence below. Your original plan, amount, duration and modules will remain unchanged.
                </div>
            @endisset

            <div class="row g-4 align-items-start subscription-checkout-page__content">
                <div class="col-12 col-lg-5">
                    <article class="subscription-checkout-product">
                        <span class="subscription-checkout-product__kind">{{ $subscriptionProduct->frontend_product_for }}</span>
                        <div class="subscription-checkout-product__icon">
                            <i class="fa-solid {{ $subscriptionProduct->product_for === \App\Models\SubscriptionProduct::FOR_MEMBERSHIP ? 'fa-crown' : 'fa-calendar-check' }}" aria-hidden="true"></i>
                        </div>
                        <h2>{{ $subscriptionProduct->frontend_membership_name }}</h2>

                        @if ($subscriptionProduct->frontend_has_discount)
                            <div class="subscription-checkout-product__discount">
                                <del dir="ltr">{{ $subscriptionProduct->frontend_currency }} {{ $subscriptionProduct->frontend_price }}</del>
                                <span>{{ $subscriptionProduct->frontend_discount_label }}</span>
                            </div>
                        @endif

                        <p class="subscription-checkout-product__price" dir="ltr">
                            {{ $subscriptionProduct->frontend_currency }} {{ $currentPrice }}
                        </p>
                        <p class="subscription-checkout-product__duration">مدت: {{ $subscriptionProduct->frontend_duration }}</p>

                        <div class="subscription-checkout-product__modules">
                            <h3>شامل ماڈیولز</h3>
                            @forelse ($subscriptionProduct->subscriptionTypes as $subscriptionType)
                                <div class="subscription-checkout-product__module">
                                    <i class="fa-solid {{ $subscriptionType->frontend_membership_icon }}" aria-hidden="true"></i>
                                    <span>{{ $subscriptionType->frontend_membership_label }}</span>
                                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                                </div>
                            @empty
                                <p class="subscription-checkout-product__empty">اس سبسکرپشن کے ماڈیولز دستیاب نہیں ہیں۔</p>
                            @endforelse
                        </div>
                    </article>
                </div>

                <div class="col-12 col-lg-7">
                    <section class="subscription-checkout-summary h-100" aria-labelledby="checkout-summary-heading">
                        <h2 id="checkout-summary-heading">آرڈر کا خلاصہ</h2>

                        <dl class="subscription-checkout-summary__details">
                            <div><dt>سبسکرپشن</dt><dd>{{ $subscriptionProduct->frontend_membership_name }}</dd></div>
                            <div><dt>قیمت</dt><dd dir="ltr">{{ $subscriptionProduct->frontend_currency }} {{ $currentPrice }}</dd></div>
                            <div><dt>سب ٹوٹل</dt><dd dir="ltr">{{ $subscriptionProduct->frontend_currency }} {{ $currentPrice }}</dd></div>
                            <div><dt>رعایت</dt><dd dir="ltr">{{ $subscriptionProduct->frontend_currency }} 0</dd></div>
                            <div class="subscription-checkout-summary__total"><dt>کل رقم</dt><dd dir="ltr">{{ $subscriptionProduct->frontend_currency }} {{ $currentPrice }}</dd></div>
                        </dl>

                        <div class="subscription-coupon">
                            <h3>کوپن کوڈ</h3>
                            {{-- Coupon functionality will be implemented in a later checkout step. --}}
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="کوپن کوڈ درج کریں" aria-label="کوپن کوڈ">
                                <button class="btn btn-outline-primary" type="button" disabled>لاگو کریں</button>
                            </div>
                        </div>

                        <form method="POST" action="{{ isset($paymentResubmission) ? route('front.account.subscriptions.resubmit.store', $paymentResubmission) : route('front.subscriptions.checkout.store', $subscriptionProduct) }}" enctype="multipart/form-data">
                            @csrf
                        <fieldset class="subscription-payment-methods" @disabled($paymentAccounts->isEmpty())>
                            <legend>ادائیگی کا اکاؤنٹ منتخب کریں</legend>
                            @forelse ($paymentAccounts as $paymentAccount)
                                <label class="subscription-payment-option">
                                    <input type="radio" name="payment_account_id" value="{{ $paymentAccount->id }}" @checked((string) old('payment_account_id') === (string) $paymentAccount->id)>
                                    <span class="subscription-payment-option__mark" aria-hidden="true"></span>
                                    <i class="fa-solid fa-building-columns" aria-hidden="true"></i>
                                    <span class="subscription-payment-option__details">
                                        <strong>{{ $paymentAccount->bank_name }}</strong>
                                        <small>{{ $paymentAccount->account_title }}</small>
                                        <small dir="ltr">Account No: {{ $paymentAccount->account_no }}</small>
                                        @if ($paymentAccount->iban)
                                            <small dir="ltr">IBAN: {{ $paymentAccount->iban }}</small>
                                        @endif
                                        @if ($paymentAccount->branch_code)
                                            <small dir="ltr">Branch Code: {{ $paymentAccount->branch_code }}</small>
                                        @endif
                                    </span>
                                </label>
                            @empty
                                <div class="alert alert-warning mb-0" role="alert">فی الحال کوئی ادائیگی اکاؤنٹ دستیاب نہیں ہے۔</div>
                            @endforelse
                        </fieldset>

                        @error('payment_account_id')
                            <p class="subscription-checkout-summary__error">{{ $message }}</p>
                        @enderror

                        <div class="subscription-payment-proof">
                            <div>
                                <label class="form-label" for="transaction_id">ٹرانزیکشن / حوالہ نمبر</label>
                                <input class="form-control @error('transaction_id') is-invalid @enderror" id="transaction_id" name="transaction_id" type="text" maxlength="100" value="{{ old('transaction_id') }}" dir="ltr" autocomplete="off">
                                @error('transaction_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="form-label" for="payment_slip">ادائیگی کی رسید <span class="text-danger">*</span></label>
                                <input class="form-control @error('payment_slip') is-invalid @enderror" id="payment_slip" name="payment_slip" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf,image/jpeg,image/png,image/webp,application/pdf" required>
                                <div class="form-text">JPG، JPEG، PNG، WEBP یا PDF — زیادہ سے زیادہ 5 MB</div>
                                @error('payment_slip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <button type="submit" class="subscription-checkout-summary__action" data-checkout-action disabled @disabled($paymentAccounts->isEmpty())>
                            {{ isset($paymentResubmission) ? 'Resubmit Payment' : 'سبسکرپشن مکمل کریں' }}
                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        </button>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </main>
@endsection
