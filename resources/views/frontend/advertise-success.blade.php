@extends('layouts.frontLayout.front-design')

@section('title', 'تشہیری درخواست موصول ہو گئی')

@section('content')
    <div class="front-advertise front-advertise--success" dir="rtl">
        <nav class="front-advertise__breadcrumb front-ui" aria-label="بریڈ کرمب">
            <div class="container-fluid front-advertise__container">
                <a href="{{ route('frontend.home') }}">صفحہ اول</a><span aria-hidden="true">›</span>
                <a href="{{ route('front.advertise') }}">تشہیر کیجئے</a><span aria-hidden="true">›</span>
                <span aria-current="page">درخواست موصول ہو گئی</span>
            </div>
        </nav>
        <div class="container-fluid front-advertise__container">
            <section class="front-advertise__card front-advertise__confirmation" role="status">
                <span class="front-advertise__success-icon"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                <h1>آپ کی تشہیری درخواست موصول ہو گئی ہے</h1>
                <p>ہماری ٹیم جلد آپ سے رابطہ کرے گی۔</p>
                <p class="front-advertise__reference">درخواست نمبر: <strong dir="ltr">{{ $adRequest->request_no }}</strong></p>
                <a class="front-advertise__submit" href="{{ route('frontend.home') }}">صفحہ اول پر جائیں</a>
            </section>
        </div>
    </div>
@endsection
