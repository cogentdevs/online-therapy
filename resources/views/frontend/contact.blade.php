@extends('layouts.frontLayout.front-design')

@section('title', 'Contact')
@section('meta_description', 'Contact Digital Magazine')

@section('content')
    <div class="front-contact">
        <div class="container-fluid front-contact__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="Breadcrumb">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> Home</a>
                <span aria-hidden="true">/</span><span aria-current="page">Contact</span>
            </nav>

            <header class="front-contact__hero">
                <h1>Contact Us</h1>
                <div class="front-contact__eyebrow"><span></span><strong>We would love to hear from you</strong><span></span>
                </div>
                <p>Share your thoughts, questions, or suggestions with us.</p>
            </header>

            @if (session('success'))
                <div class="alert alert-success front-contact__alert" role="status">{{ session('success') }}</div>
            @endif

            <div class="row g-4 align-items-start front-contact__layout">
                <div class="col-12 col-lg-8">
                    <section class="front-contact__card front-contact__form-card">
                        <header class="front-contact__card-heading">
                            <span><i class="fa-regular fa-message" aria-hidden="true"></i></span>
                            <div>
                                <h2>Send Us a Message</h2>
                                <p>Complete the form and we will get back to you shortly.</p>
                            </div>
                        </header>

                        <form method="post" action="{{ route('front.contact.store') }}" class="front-contact__form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="contact-name">Name <span>*</span></label>
                                    <div class="front-contact__control"><i class="fa-regular fa-user"
                                            aria-hidden="true"></i><input id="contact-name" name="name" type="text"
                                            value="{{ old('name') }}" maxlength="150" placeholder="Enter your name"
                                            class="@error('name') is-invalid @enderror" required></div>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="contact-email">Email <span>*</span></label>
                                    <div class="front-contact__control front-contact__control--ltr"><i
                                            class="fa-regular fa-envelope" aria-hidden="true"></i><input id="contact-email"
                                            name="email" type="email" dir="ltr" value="{{ old('email') }}"
                                            maxlength="255" placeholder="email@example.com"
                                            class="@error('email') is-invalid @enderror" required></div>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="contact-phone">Phone Number <span>*</span></label>
                                    <div class="front-contact__control front-contact__control--ltr"><i
                                            class="fa-solid fa-phone" aria-hidden="true"></i><input id="contact-phone"
                                            name="phone" type="text" dir="ltr" value="{{ old('phone') }}"
                                            maxlength="50" placeholder="Enter your phone number"
                                            class="@error('phone') is-invalid @enderror" required></div>
                                    @error('phone')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="contact-subject">Subject <span>*</span></label>
                                    <div class="front-contact__control"><i class="fa-regular fa-rectangle-list"
                                            aria-hidden="true"></i><input id="contact-subject" name="subject" type="text"
                                            value="{{ old('subject') }}" maxlength="255"
                                            placeholder="Enter your message subject"
                                            class="@error('subject') is-invalid @enderror" required></div>
                                    @error('subject')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="contact-message">Message <span>*</span></label>
                                    <div class="front-contact__control front-contact__control--message"><i
                                            class="fa-regular fa-comment-dots" aria-hidden="true"></i>
                                        <textarea id="contact-message" name="message" maxlength="5000" rows="6" placeholder="Write your message"
                                            class="@error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
                                    </div>
                                    @error('message')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="front-contact__captcha">
                                {!! app('captcha')->display() !!}
                            </div>
                            @error('g-recaptcha-response')
                                <div class="invalid-feedback d-block front-contact__captcha-error">{{ $message }}</div>
                            @enderror

                            <button class="front-contact__submit" type="submit"><i class="fa-regular fa-paper-plane"
                                    aria-hidden="true"></i> Send Message</button>
                        </form>
                    </section>
                </div>

                <div class="col-12 col-lg-4 front-contact__info-column">
                    <aside class="front-contact__card front-contact__info-card">
                        <header class="front-contact__card-heading">
                            <span><i class="fa-regular fa-address-card" aria-hidden="true"></i></span>
                            <div>
                                <h2>Contact Information</h2>
                                <p>You can also reach us through the following channels.</p>
                            </div>
                        </header>

                        <div class="front-contact__details">
                            @if ($generalSetting?->contact_1)
                                <a href="tel:{{ preg_replace('/[^+\d]/', '', $generalSetting->contact_1) }}"><span><i
                                            class="fa-solid fa-phone" aria-hidden="true"></i></span>
                                    <div><strong>Phone</strong><b dir="ltr">{{ $generalSetting->contact_1 }}</b></div>
                                </a>
                            @endif
                            @if ($generalSetting?->email)
                                <a href="mailto:{{ $generalSetting->email }}"><span><i class="fa-solid fa-envelope"
                                            aria-hidden="true"></i></span>
                                    <div><strong>Email</strong><b dir="ltr">{{ $generalSetting->email }}</b></div>
                                </a>
                            @endif
                            @if ($generalSetting?->address)
                                <div class="front-contact__detail"><span><i class="fa-solid fa-location-dot"
                                            aria-hidden="true"></i></span>
                                    <div><strong>Address</strong><b>{{ $generalSetting->address }}</b></div>
                                </div>
                            @endif
                        </div>

                        <section class="front-contact__social-section">
                            <h3>Follow Us</h3>
                            <p>Stay connected for the latest updates.</p>
                            <div class="front-contact__socials front-ui">
                                @foreach ([['url' => $generalSetting?->youtube, 'label' => 'یوٹیوب', 'icon' => 'fa-youtube'], ['url' => $generalSetting?->instagram, 'label' => 'انسٹاگرام', 'icon' => 'fa-instagram'], ['url' => $generalSetting?->facebook, 'label' => 'فیس بک', 'icon' => 'fa-facebook-f'], ['url' => $generalSetting?->x, 'label' => 'ایکس', 'icon' => 'fa-x-twitter'], ['url' => $generalSetting?->linkedin, 'label' => 'لنکڈ اِن', 'icon' => 'fa-linkedin-in'], ['url' => $generalSetting?->tiktok, 'label' => 'ٹک ٹاک', 'icon' => 'fa-tiktok']] as $social)
                                    @if ($social['url'])
                                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                                            aria-label="{{ $social['label'] }}"><i
                                                class="fa-brands {{ $social['icon'] }}" aria-hidden="true"></i></a>
                                    @endif
                                @endforeach
                            </div>
                        </section>
                    </aside>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! app('captcha')->renderJs('ur') !!}
@endpush
