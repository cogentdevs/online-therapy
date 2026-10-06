@extends('layouts.frontLayout.front-design')

@section('title', 'حالیہ سرگرمیاں')
@section('meta_description', 'اپنے حالیہ دیکھے گئے مضامین اور شمارے دیکھیں')

@section('content')
    <div class="front-account-page">
        <div class="container-fluid front-account-container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span><a href="{{ route('front.account') }}">میرا اکاؤنٹ</a>
                <span aria-hidden="true">/</span><span aria-current="page">حالیہ سرگرمیاں</span>
            </nav>

            <header class="front-account-heading">
                <h1>حالیہ سرگرمیاں</h1><span aria-hidden="true"></span>
                <p>آپ کے حالیہ دیکھے گئے مضامین اور شمارے۔</p>
            </header>

            <div class="row g-4 align-items-start front-account-layout">
                <aside class="col-12 col-lg-3">@include('frontend.user-account.partials.sidebar')</aside>
                <div class="col-12 col-lg-9">
                    <section class="front-account-card front-account-activities">
                        <header class="front-account-activities__header">
                            <div><h2>تمام حالیہ سرگرمیاں</h2><p>تازہ ترین دیکھی گئی سرگرمی سے پرانی سرگرمی تک۔</p></div>
                        </header>

                        <div class="table-responsive user-subscription-history-table-wrap">
                            <table class="table user-subscription-history-table align-middle" data-front-account-datatable="activities"
                                data-source-url="{{ route('front.account.recent-activities', ['datatable' => 1]) }}">
                                <thead><tr><th>ماڈیول</th><th>عنوان</th><th>آخری بار دیکھا</th><th>دیکھیں</th></tr></thead>
                                <tbody>
                                    @foreach ($activities as $activity)
                                        <tr>
                                            <td>{{ $activity->frontend_module_label }}</td>
                                            <td><span class="front-account-datatable-title" title="{{ $activity->frontend_title }}">{{ $activity->frontend_title }}</span></td>
                                            <td><span class="front-account-activity-time" dir="ltr"><span>{{ $activity->last_visited_at?->format('d M Y, h:i A') ?? '—' }}</span><span>PKT (UTC+5)</span></span></td>
                                            <td>{{ $activity->frontend_url ? 'دیکھیں' : 'دستیاب نہیں' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($activities->isEmpty())<span class="visually-hidden">ابھی کوئی حالیہ سرگرمی موجود نہیں۔</span>@endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
