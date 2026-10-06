@extends('layouts.frontLayout.front-design')

@section('title', 'میرے سوالات')
@section('meta_description', 'اپنے جمع کرائے گئے سوالات اور ان کی موجودہ حالت دیکھیں')

@section('content')
    <div class="front-account-page">
        <div class="container-fluid front-account-container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span><a href="{{ route('front.account') }}">میرا اکاؤنٹ</a>
                <span aria-hidden="true">/</span><span aria-current="page">میرے سوالات</span>
            </nav>

            <header class="front-account-heading">
                <h1>میرے سوالات</h1><span aria-hidden="true"></span>
                <p>اپنے سوالات، ان کی موجودہ حالت اور موصول ہونے والے جوابات دیکھیں۔</p>
            </header>

            <div class="row g-4 align-items-start front-account-layout">
                <aside class="col-12 col-lg-3">@include('frontend.user-account.partials.sidebar')</aside>
                <div class="col-12 col-lg-9">
                    <section class="front-account-card front-ad-request-card">
                        <header class="user-subscriptions-section-header">
                            <div><h2>تمام سوالات</h2><p>تازہ ترین سوال پہلے دکھایا گیا ہے۔</p></div>
                            <a class="front-account-outline-action" href="{{ route('front.ask-question') }}">نیا سوال پوچھیں</a>
                        </header>

                        @if ($questions->isNotEmpty())
                            <div class="table-responsive front-account-listing-table-wrap">
                                <table
                                    class="table align-middle front-account-listing-table"
                                    data-front-account-listing-datatable="questions"
                                    data-current-page="{{ $questions->currentPage() }}"
                                    data-total-pages="{{ $questions->lastPage() }}"
                                >
                                    <thead>
                                        <tr>
                                            <th>سوال نمبر</th>
                                            <th>موضوع</th>
                                            <th>حالت</th>
                                            <th>تاریخ</th>
                                            <th>تفصیل</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($questions as $question)
                                            <tr>
                                                <td><strong dir="ltr">{{ $question->question_no }}</strong></td>
                                                <td><span class="front-account-datatable-title" title="{{ $question->subject }}">{{ $question->subject }}</span></td>
                                                <td><span class="front-ad-request-badge">{{ $question->userStatusLabel() }}</span></td>
                                                <td data-order="{{ $question->created_at?->timestamp }}">
                                                    <time datetime="{{ $question->created_at?->toDateString() }}" dir="ltr">{{ $question->created_at?->format('d M Y') }}</time>
                                                </td>
                                                <td>
                                                    <a class="front-account-outline-action" href="{{ route('front.account.questions.show', $question) }}">
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
                                <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
                                <p>آپ نے ابھی تک کوئی سوال نہیں پوچھا۔</p>
                                <a class="front-account-outline-action" href="{{ route('front.ask-question') }}">اپنا پہلا سوال پوچھیں</a>
                            </div>
                        @endif

                        @if ($questions->hasPages())
                            <div class="user-subscriptions-pagination">{{ $questions->links() }}</div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
