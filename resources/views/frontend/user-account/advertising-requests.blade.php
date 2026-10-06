@extends('layouts.frontLayout.front-design')

@section('title', 'میری تشہیری درخواستیں')
@section('meta_description', 'اپنی تشہیری درخواستوں اور ان کی موجودہ حالت دیکھیں')

@section('content')
    <div class="front-account-page">
        <div class="container-fluid front-account-container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span><a href="{{ route('front.account') }}">میرا اکاؤنٹ</a>
                <span aria-hidden="true">/</span><span aria-current="page">میری تشہیری درخواستیں</span>
            </nav>

            <header class="front-account-heading">
                <h1>میری تشہیری درخواستیں</h1><span aria-hidden="true"></span>
                <p>اپنی درخواستوں کی موجودہ حالت اور تفصیل دیکھیں۔</p>
            </header>

            <div class="row g-4 align-items-start front-account-layout">
                <aside class="col-12 col-lg-3">@include('frontend.user-account.partials.sidebar')</aside>
                <div class="col-12 col-lg-9">
                    <section class="front-account-card front-ad-request-card">
                        <header class="user-subscriptions-section-header">
                            <div><h2>تمام درخواستیں</h2><p>تازہ ترین درخواست پہلے دکھائی گئی ہے۔</p></div>
                        </header>

                        @if ($adRequests->isNotEmpty())
                            <div class="table-responsive front-account-listing-table-wrap">
                                <table
                                    class="table align-middle front-account-listing-table"
                                    data-front-account-listing-datatable="advertising-requests"
                                    data-current-page="{{ $adRequests->currentPage() }}"
                                    data-total-pages="{{ $adRequests->lastPage() }}"
                                >
                                    <thead>
                                        <tr>
                                            <th>درخواست نمبر</th>
                                            <th>حالت</th>
                                            <th>تشہیری مدت</th>
                                            <th>درخواست کی تاریخ</th>
                                            <th>کل مدت</th>
                                            <th>مقامات</th>
                                            <th>تفصیل</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($adRequests as $adRequest)
                                            <tr>
                                                <td><strong dir="ltr">{{ $adRequest->request_no }}</strong></td>
                                                <td><span class="front-ad-request-badge">{{ $requestStatusLabels[$adRequest->status] ?? $adRequest->status }}</span></td>
                                                <td data-order="{{ $adRequest->from_date->format('Y-m-d') }}">
                                                    <span dir="ltr">{{ $adRequest->from_date->format('d M Y') }} — {{ $adRequest->to_date->format('d M Y') }}</span>
                                                </td>
                                                <td data-order="{{ $adRequest->created_at?->timestamp }}">
                                                    <time datetime="{{ $adRequest->created_at?->toDateString() }}" dir="ltr">{{ $adRequest->created_at?->format('d M Y') }}</time>
                                                </td>
                                                <td data-order="{{ $adRequest->durationDays() }}">{{ $adRequest->durationDays() }} دن</td>
                                                <td data-order="{{ $adRequest->placements_count }}">{{ $adRequest->placements_count }}</td>
                                                <td>
                                                    <a class="front-account-outline-action" href="{{ route('front.account.advertising-requests.show', $adRequest) }}">
                                                        تفصیل دیکھیں <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="front-account-empty-state">
                                <i class="fa-solid fa-bullhorn" aria-hidden="true"></i>
                                <p>آپ نے ابھی تک کوئی تشہیری درخواست جمع نہیں کرائی۔</p>
                                <a class="front-account-outline-action" href="{{ route('front.advertise') }}">تشہیر کے لیے درخواست دیں</a>
                            </div>
                        @endif

                        @if ($adRequests->hasPages())
                            <div class="user-subscriptions-pagination">{{ $adRequests->links() }}</div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
