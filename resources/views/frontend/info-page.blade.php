@extends('layouts.frontLayout.front-design')

@section('title', $title)
@section('meta_description', $title)

@section('content')
    <main class="front-info-page" dir="{{ $language === 'ur' ? 'rtl' : 'ltr' }}">
        <div class="container-fluid front-info-page__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="{{ $language === 'ur' ? 'بریڈ کرمب' : 'Breadcrumb' }}">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> {{ $language === 'ur' ? 'صفحہ اول' : 'Home' }}</a>
                <span aria-hidden="true">/</span><span aria-current="page">{{ $title }}</span>
            </nav>

            <header class="front-info-page__header">
                <span>{{ $language === 'ur' ? 'معلومات' : 'Information' }}</span>
                <h1>{{ $title }}</h1>
            </header>

            <article class="front-info-page__content">
                @if (filled($infoPage?->description))
                    {!! $infoPage->description !!}
                @else
                    <p class="front-info-page__empty">{{ $language === 'ur' ? 'اس صفحے کی معلومات جلد دستیاب ہوں گی۔' : 'Content for this page will be available soon.' }}</p>
                @endif
            </article>
        </div>
    </main>
@endsection
