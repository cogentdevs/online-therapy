@extends('layouts.frontLayout.front-design')

@section('title', 'سوال '.$question->question_no)
@section('meta_description', 'اپنے سوال اور انتظامیہ کی جانب سے موصول ہونے والا جواب دیکھیں')

@section('content')
    <div class="front-account-page">
        <div class="container-fluid front-account-container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span><a href="{{ route('front.account') }}">میرا اکاؤنٹ</a>
                <span aria-hidden="true">/</span><a href="{{ route('front.account.questions.index') }}">میرے سوالات</a>
                <span aria-hidden="true">/</span><span aria-current="page" dir="ltr">{{ $question->question_no }}</span>
            </nav>

            <header class="front-account-heading">
                <h1>سوال کی تفصیل</h1><span aria-hidden="true"></span>
                <p dir="ltr">{{ $question->question_no }}</p>
            </header>

            <div class="row g-4 align-items-start front-account-layout">
                <aside class="col-12 col-lg-3">@include('frontend.user-account.partials.sidebar')</aside>
                <div class="col-12 col-lg-9">
                    <section class="front-account-card front-ad-request-card">
                        <header class="user-subscriptions-section-header">
                            <div><h2>سوال کی معلومات</h2><p>جمع کرائی گئی اصل معلومات اور موجودہ حالت۔</p></div>
                            <span class="front-ad-request-badge">{{ $question->userStatusLabel() }}</span>
                        </header>
                        <dl class="front-ad-request-detail-grid">
                            <div><dt>سوال نمبر</dt><dd dir="ltr">{{ $question->question_no }}</dd></div>
                            <div><dt>جمع کرانے کی تاریخ</dt><dd>{{ $question->created_at?->format('d M Y, h:i A') }}</dd></div>
                            <div><dt>نام</dt><dd>{{ $question->name }}</dd></div>
                            <div><dt>ای میل</dt><dd dir="ltr">{{ $question->email }}</dd></div>
                            <div><dt>فون نمبر</dt><dd dir="ltr">{{ $question->phone }}</dd></div>
                            <div class="front-ad-request-detail-grid__full"><dt>موضوع</dt><dd>{{ $question->subject }}</dd></div>
                            <div class="front-ad-request-detail-grid__full"><dt>اصل سوال</dt><dd class="front-ad-request-message">{{ $question->sawal }}</dd></div>
                        </dl>
                    </section>

                    <section class="front-account-card front-ad-request-card mt-4">
                        <header class="user-subscriptions-section-header"><div><h2>جواب</h2><p>ماہرین کی جانب سے فراہم کردہ جواب کی موجودہ صورتِ حال۔</p></div></header>
                        @if (filled($question->admin_response))
                            <div class="front-ad-request-message">{{ $question->admin_response }}</div>
                        @elseif ($question->status === \App\Models\AskQuestion::STATUS_PENDING)
                            <p class="front-ad-request-note">آپ کا سوال موصول ہو چکا ہے اور ہماری ٹیم اس کا جائزہ لے رہی ہے۔ جواب موصول ہونے پر آپ کو ای میل کے ذریعے بھی مطلع کیا جائے گا۔</p>
                        @elseif ($question->status === \App\Models\AskQuestion::STATUS_CLOSED)
                            <p class="front-ad-request-note">یہ سوال بند کر دیا گیا ہے۔</p>
                        @else
                            <p class="front-ad-request-note">جواب ابھی دستیاب نہیں ہے۔</p>
                        @endif
                    </section>

                    <div class="mt-3">
                        <a class="front-account-outline-action" href="{{ route('front.ask-question') }}">نیا سوال پوچھیں</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
