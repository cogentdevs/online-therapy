@extends('layouts.frontLayout.front-design')

@section('title', 'تشہیری درخواست '.$adRequest->request_no)
@section('meta_description', 'اپنی تشہیری درخواست کی تفصیل اور مقامات کی حالت دیکھیں')

@section('content')
    <div class="front-account-page">
        <div class="container-fluid front-account-container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span><a href="{{ route('front.account') }}">میرا اکاؤنٹ</a>
                <span aria-hidden="true">/</span><a href="{{ route('front.account.advertising-requests.index') }}">میری تشہیری درخواستیں</a>
                <span aria-hidden="true">/</span><span aria-current="page" dir="ltr">{{ $adRequest->request_no }}</span>
            </nav>

            <header class="front-account-heading">
                <h1>تشہیری درخواست کی تفصیل</h1><span aria-hidden="true"></span>
                <p dir="ltr">{{ $adRequest->request_no }}</p>
            </header>

            <div class="row g-4 align-items-start front-account-layout">
                <aside class="col-12 col-lg-3">@include('frontend.user-account.partials.sidebar')</aside>
                <div class="col-12 col-lg-9">
                    <section class="front-account-card front-ad-request-card">
                        <header class="user-subscriptions-section-header">
                            <div><h2>درخواست کی معلومات</h2><p>جمع کرائی گئی معلومات اور موجودہ حالت۔</p></div>
                            <span class="front-ad-request-badge">{{ $requestStatusLabels[$adRequest->status] ?? $adRequest->status }}</span>
                        </header>
                        <dl class="front-ad-request-detail-grid">
                            <div><dt>درخواست نمبر</dt><dd dir="ltr">{{ $adRequest->request_no }}</dd></div>
                            <div><dt>درخواست کی تاریخ</dt><dd>{{ $adRequest->created_at?->format('d M Y, h:i A') }}</dd></div>
                            <div><dt>نام</dt><dd>{{ $adRequest->name }}</dd></div>
                            <div><dt>ای میل</dt><dd dir="ltr">{{ $adRequest->email }}</dd></div>
                            <div><dt>فون نمبر</dt><dd dir="ltr">{{ $adRequest->phone }}</dd></div>
                            @if (filled($adRequest->company))<div><dt>کمپنی / برانڈ</dt><dd>{{ $adRequest->company }}</dd></div>@endif
                            <div><dt>آغاز کی تاریخ</dt><dd>{{ $adRequest->from_date->format('d M Y') }}</dd></div>
                            <div><dt>اختتام کی تاریخ</dt><dd>{{ $adRequest->to_date->format('d M Y') }}</dd></div>
                            <div><dt>کل مدت</dt><dd>{{ $adRequest->durationDays() }} دن</dd></div>
                            @if (filled($adRequest->details))<div class="front-ad-request-detail-grid__full"><dt>مزید تفصیلات</dt><dd class="front-ad-request-message">{{ $adRequest->details }}</dd></div>@endif
                        </dl>
                    </section>

                    <section class="front-account-card front-ad-request-card front-ad-request-placements">
                        <header class="user-subscriptions-section-header"><div><h2>تشہیری مقامات</h2><p>ہر مقام کی حالت الگ دکھائی گئی ہے۔</p></div></header>
                        @foreach ($adRequest->placements as $placement)
                            <article class="front-ad-request-placement">
                                <div class="front-ad-request-item__heading">
                                    <h3>{{ $placement->displayLabel() }}</h3>
                                    <span class="front-ad-request-badge">{{ $placementStatusLabels[$placement->status] ?? $placement->status }}</span>
                                </div>

                                @if ($placement->ad)
                                    <div class="front-ad-request-linked-ad">
                                        <h4>شائع کردہ اشتہار</h4>
                                        <dl class="front-ad-request-detail-grid">
                                            <div><dt>عنوان</dt><dd>{{ $placement->ad->title }}</dd></div>
                                            <div><dt>اشتہار کی حالت</dt><dd>{{ $adDetails[$placement->id]['status'] }}</dd></div>
                                            <div><dt>آغاز کی تاریخ</dt><dd>{{ $placement->ad->start_date?->format('d M Y') ?? 'مقرر نہیں' }}</dd></div>
                                            <div><dt>اختتام کی تاریخ</dt><dd>{{ $placement->ad->expiry_date?->format('d M Y') ?? 'مقرر نہیں' }}</dd></div>
                                            <div class="front-ad-request-detail-grid__full"><dt>کلکس</dt><dd>
                                                @if ($adDetails[$placement->id]['click_count_available'])
                                                    کل کلکس: {{ $placement->ad->click_count }}
                                                @else
                                                    اس اشتہار کے لیے داخلی کلک ٹریکنگ دستیاب نہیں ہے۔
                                                @endif
                                            </dd></div>
                                        </dl>
                                    </div>
                                @elseif ($placement->status === 'confirmed')
                                    <p class="front-ad-request-note">مقام کی تصدیق ہو چکی ہے، اشتہار ابھی شائع نہیں ہوا۔</p>
                                @endif
                            </article>
                        @endforeach
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
