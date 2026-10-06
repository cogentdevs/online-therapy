@extends('layouts.frontLayout.front-design')

@section('title', 'سوال موصول ہو گیا')

@section('content')
    <div class="front-ask-question front-ask-question--success" dir="rtl">
        <nav class="front-ask-question__breadcrumb front-ui" aria-label="بریڈ کرمب">
            <div class="container-fluid front-ask-question__container">
                <a href="{{ route('frontend.home') }}">صفحہ اول</a><span aria-hidden="true">›</span>
                <a href="{{ route('front.ask-question') }}">سوال پوچھیں</a><span aria-hidden="true">›</span>
                <span aria-current="page">سوال موصول ہو گیا</span>
            </div>
        </nav>
        <div class="container-fluid front-ask-question__container">
            <section class="front-ask-question__card front-ask-question__confirmation" role="status">
                <span class="front-ask-question__success-icon"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                <h1>آپ کا سوال کامیابی سے موصول ہو گیا ہے</h1>
                <p>ہماری ٹیم آپ کے سوال کا جائزہ لے کر جواب فراہم کرے گی۔</p>
                <p class="front-ask-question__reference">سوال نمبر: <strong dir="ltr">{{ $askQuestion->question_no }}</strong></p>
                <a class="front-ask-question__submit" href="{{ route('frontend.home') }}">صفحہ اول پر جائیں</a>
            </section>
        </div>
    </div>
@endsection
