@extends('layouts.frontLayout.front-design')

@section('title', $language === 'en' ? 'Frequently Asked Questions' : 'عمومی سوالات')

@section('content')
    @php
        $isUrdu = $language === 'ur';
        $primaryContact = $generalSetting?->contact_1 ?: $generalSetting?->contact_2;
    @endphp

    <section class="faq-page" dir="{{ $isUrdu ? 'rtl' : 'ltr' }}">
        <div class="container-fluid faq-page__container">
            <nav class="front-taza-breadcrumb front-ui faq-page__breadcrumb"
                aria-label="{{ $isUrdu ? 'بریڈ کرمب' : 'Breadcrumb' }}">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i>
                    {{ $isUrdu ? 'صفحہ اول' : 'Home' }}</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $isUrdu ? 'عمومی سوالات (FAQ)' : 'Frequently Asked Questions (FAQ)' }}</span>
            </nav>

            <header class="faq-page__hero">
                <span class="faq-page__hero-icon" aria-hidden="true"><i class="fa-regular fa-circle-question"></i></span>
                <h1>{{ $isUrdu ? 'عمومی سوالات' : 'Frequently Asked Questions (FAQ)' }}</h1>
                <p>{{ $isUrdu ? 'آپ کے سوالات کے جوابات ایک ہی جگہ پر' : 'Answers to your questions, all in one place.' }}
                </p>
            </header>

            <div class="row g-4 align-items-start faq-page__main">
                <div class="col-12 col-lg-9">
                    <div class="faq-content-card" data-faq-search-root>
                        @if ($faqCategories->isNotEmpty())
                            <div class="nav faq-category-tabs" id="faq-category-tabs" role="tablist"
                                aria-label="{{ $isUrdu ? 'عمومی سوالات کے زمرے' : 'FAQ categories' }}">
                                @foreach ($faqCategories as $category)
                                    <button class="nav-link @if ($loop->first) active @endif"
                                        id="faq-category-tab-{{ $category->id }}" data-bs-toggle="pill"
                                        data-bs-target="#faq-category-panel-{{ $category->id }}" type="button"
                                        role="tab" aria-controls="faq-category-panel-{{ $category->id }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                        @if ($category->icon_available)
                                            <img src="{{ asset($category->icon) }}" alt="" width="34"
                                                height="34">
                                        @endif
                                        <span>{{ $category->name }}</span>
                                    </button>
                                @endforeach
                            </div>

                            <div class="faq-search" role="search">
                                <label class="visually-hidden" for="faq-question-search">{{ $isUrdu ? 'فعال زمرے کے سوالات تلاش کریں' : 'Search questions in the active category' }}</label>
                                <span class="faq-search__icon" aria-hidden="true"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input class="form-control faq-search__input" id="faq-question-search" type="search" autocomplete="off" placeholder="{{ $isUrdu ? 'سوال تلاش کریں۔۔۔' : 'Search questions...' }}" data-faq-search-input>
                                <button class="faq-search__clear" type="button" data-faq-search-clear hidden aria-label="{{ $isUrdu ? 'تلاش صاف کریں' : 'Clear search' }}">
                                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                                </button>
                            </div>

                            <div class="tab-content faq-category-content" id="faq-category-content">
                                @foreach ($faqCategories as $category)
                                    <section class="tab-pane fade @if ($loop->first) show active @endif"
                                        id="faq-category-panel-{{ $category->id }}" role="tabpanel"
                                        aria-labelledby="faq-category-tab-{{ $category->id }}" tabindex="0">
                                        <div class="faq-category-intro">
                                            @if ($category->icon_available)
                                                <span class="faq-category-intro__icon"><img
                                                        src="{{ asset($category->icon) }}" alt="" width="42"
                                                        height="42"></span>
                                            @endif
                                            <h2>{{ $category->name }}</h2>
                                        </div>

                                        @if ($category->faqs->isNotEmpty())
                                            <div class="accordion faq-accordion" id="faq-accordion-{{ $category->id }}">
                                                @foreach ($category->faqs as $faq)
                                                    <div class="accordion-item" data-faq-search-item>
                                                        <h3 class="accordion-header"
                                                            id="faq-heading-{{ $category->id }}-{{ $faq->id }}">
                                                            <button
                                                                class="accordion-button @unless ($loop->first) collapsed @endunless"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#faq-collapse-{{ $category->id }}-{{ $faq->id }}"
                                                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                                                aria-controls="faq-collapse-{{ $category->id }}-{{ $faq->id }}">
                                                                {{ $faq->question }}
                                                            </button>
                                                        </h3>
                                                        <div id="faq-collapse-{{ $category->id }}-{{ $faq->id }}"
                                                            class="accordion-collapse collapse @if ($loop->first) show @endif"
                                                            aria-labelledby="faq-heading-{{ $category->id }}-{{ $faq->id }}"
                                                            data-bs-parent="#faq-accordion-{{ $category->id }}">
                                                            <div class="accordion-body">{!! nl2br(e($faq->answer)) !!}</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="faq-search-empty" data-faq-search-empty hidden>
                                                <i class="fa-regular fa-face-frown" aria-hidden="true"></i>
                                                <p>{{ $isUrdu ? 'اس تلاش کے مطابق کوئی سوال موجود نہیں۔' : 'No questions match this search.' }}</p>
                                            </div>
                                        @else
                                            <div class="faq-empty-state">
                                                <i class="fa-regular fa-message" aria-hidden="true"></i>
                                                <p>{{ $isUrdu ? 'اس زمرے میں فی الحال کوئی سوال موجود نہیں۔' : 'There are currently no questions in this category.' }}
                                                </p>
                                            </div>
                                        @endif
                                    </section>
                                @endforeach
                            </div>
                        @else
                            <div class="faq-empty-state faq-empty-state--page">
                                <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
                                <h2>{{ $isUrdu ? 'عمومی سوالات جلد دستیاب ہوں گے' : 'Frequently asked questions will be available soon.' }}
                                </h2>
                                <p>{{ $isUrdu ? 'اس وقت کوئی فعال زمرہ موجود نہیں۔' : 'There are no active FAQ categories at this time.' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <aside class="col-12 col-lg-3 faq-support"
                    aria-label="{{ $isUrdu ? 'مدد اور رابطہ' : 'Help and contact' }}">
                    <div class="faq-support-card faq-support-card--primary">
                        <span class="faq-support-card__hero-icon" aria-hidden="true"><i
                                class="fa-solid fa-headset"></i></span>
                        <h2>{{ $isUrdu ? 'اب بھی کوئی سوال ہے؟' : 'Still have a question?' }}</h2>
                        <p>{{ $isUrdu ? 'اگر آپ کے سوال کا جواب یہاں موجود نہیں تو ہم سے براہ راست رابطہ کریں۔' : 'If your question is not answered here, contact us directly.' }}
                        </p>

                        <div class="faq-support-links">
                            @if ($generalSetting?->email)
                                <a href="mailto:{{ $generalSetting->email }}">
                                    <span><i class="fa-regular fa-envelope" aria-hidden="true"></i></span>
                                    <strong>{{ $isUrdu ? 'ای میل کریں' : 'Email us' }}</strong>
                                    <small dir="ltr">{{ $generalSetting->email }}</small>
                                </a>
                            @endif
                            @if ($primaryContact)
                                <a href="tel:{{ preg_replace('/[^+\d]/', '', $primaryContact) }}">
                                    <span><i class="fa-solid fa-phone" aria-hidden="true"></i></span>
                                    <strong>{{ $isUrdu ? 'فون کریں' : 'Call us' }}</strong>
                                    <small>{{ $primaryContact }}</small>
                                </a>
                            @endif
                            <a href="{{ route('front.contact') }}">
                                <span><i class="fa-regular fa-message" aria-hidden="true"></i></span>
                                <strong>{{ $isUrdu ? 'پیغام بھیجیں' : 'Send a message' }}</strong>
                                <small>{{ $isUrdu ? 'رابطہ فارم پر جائیں' : 'Go to the contact form' }}</small>
                            </a>
                        </div>
                    </div>

                    <div class="faq-support-card faq-support-card--quick">
                        <span class="faq-support-card__hero-icon" aria-hidden="true"><i
                                class="fa-regular fa-lightbulb"></i></span>
                        <h2>{{ $isUrdu ? 'فوری مدد' : 'Quick help' }}</h2>
                        <p>{{ $isUrdu ? 'اپنے سوال کا جواب نہ ملنے پر ہماری مددگار ٹیم سے رابطہ کریں۔' : 'Contact our support team if you cannot find the answer you need.' }}
                        </p>
                        <a class="faq-primary-button"
                            href="{{ route('front.contact') }}">{{ $isUrdu ? 'رابطہ کریں' : 'Contact us' }} <i
                                class="fa-solid fa-arrow-left-long" aria-hidden="true"></i></a>
                    </div>
                </aside>
            </div>

            {{-- <section class="faq-bottom-cta">
                <span class="faq-bottom-cta__icon" aria-hidden="true"><i class="fa-solid fa-book-open"></i></span>
                <div>
                    <h2>{{ $isUrdu ? 'اب بھی مدد درکار ہے؟' : 'Still need help?' }}</h2>
                    <p>{{ $isUrdu ? 'ہم آپ کے تجربے کو بہتر بنانے کے لیے ہر وقت موجود ہیں۔' : 'We are here to help make your experience better.' }}
                    </p>
                </div>
                <a class="faq-primary-button"
                    href="{{ route('front.contact') }}">{{ $isUrdu ? 'ہم سے رابطہ کریں' : 'Contact us' }} <i
                        class="fa-solid fa-arrow-left-long" aria-hidden="true"></i></a>
            </section> --}}
        </div>
    </section>
@endsection
