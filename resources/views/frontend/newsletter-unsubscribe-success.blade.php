@extends('layouts.frontLayout.front-design')

@section('title', 'نیوز لیٹر ان سبسکرپشن مکمل')

@section('content')
    <div class="newsletter-unsubscribe-page">
        <div class="container-fluid newsletter-unsubscribe-page__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span><span aria-current="page">ان سبسکرپشن مکمل</span>
            </nav>
            <section class="newsletter-unsubscribe-card text-center">
                <div class="newsletter-unsubscribe-card__icon newsletter-unsubscribe-card__icon--success"><i class="fa-solid fa-check" aria-hidden="true"></i></div>
                <h1>آپ نیوز لیٹر سے کامیابی کے ساتھ ان سبسکرائب ہو گئے ہیں۔</h1>
                <p>آپ کی ترجیح محفوظ کر لی گئی ہے۔</p>
                <a class="newsletter-unsubscribe-card__home" href="{{ route('frontend.home') }}">ہوم پیج پر واپس جائیں</a>
            </section>
        </div>
    </div>
@endsection
