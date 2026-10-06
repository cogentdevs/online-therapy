@extends('layouts.frontLayout.front-design')

@section('title', 'میرے بک مارکس')
@section('meta_description', 'اپنے محفوظ کردہ مضامین اور شمارے دیکھیں')

@section('content')
    <div class="front-account-page">
        <div class="container-fluid front-account-container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span><a href="{{ route('front.account') }}">میرا اکاؤنٹ</a>
                <span aria-hidden="true">/</span><span aria-current="page">بک مارکس</span>
            </nav>

            <header class="front-account-heading">
                <h1>میرے بک مارکس</h1><span aria-hidden="true"></span>
                <p>اپنے محفوظ کردہ مضامین اور شماروں کا انتظام کریں۔</p>
            </header>

            <div class="row g-4 align-items-start front-account-layout">
                <aside class="col-12 col-lg-3">@include('frontend.user-account.partials.sidebar')</aside>
                <div class="col-12 col-lg-9">
                    <section class="front-account-card front-account-bookmarks" data-account-bookmarks>
                        <header class="front-account-bookmarks__header">
                            <div><h2>تمام بک مارکس</h2><p>تازہ ترین سے پرانے محفوظ کردہ مواد تک۔</p></div>
                        </header>

                        <div class="table-responsive user-subscription-history-table-wrap">
                            <table class="table user-subscription-history-table align-middle" data-front-account-datatable="bookmarks"
                                data-source-url="{{ route('front.account.bookmarks', ['datatable' => 1]) }}">
                                <thead><tr><th>ماڈیول</th><th>عنوان</th><th>بک مارک کی تاریخ</th><th>دیکھیں</th><th>ہٹائیں</th></tr></thead>
                                <tbody>
                                    @foreach ($bookmarks as $bookmark)
                                        <tr>
                                            <td>{{ $bookmark->frontend_module_label }}</td>
                                            <td><span class="front-account-datatable-title" title="{{ $bookmark->frontend_title }}">{{ $bookmark->frontend_title }}</span></td>
                                            <td>{{ $bookmark->created_at?->format('d M Y') ?? '—' }}</td>
                                            <td>{{ $bookmark->frontend_url ? 'دیکھیں' : 'دستیاب نہیں' }}</td>
                                            <td>ہٹائیں</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($bookmarks->isEmpty())<span class="visually-hidden">ابھی کوئی بک مارک محفوظ نہیں کیا گیا۔</span>@endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
