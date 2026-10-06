<footer class="front-footer">
    <div class="container-fluid front-footer__container">
        <div class="row g-4 front-footer__grid">
            <div class="col-12 col-md-4 front-footer__column front-footer__brand-column">
                <div class="front-footer__brand">
                    <a class="front-logo" href="{{ route('frontend.home') }}">
                        @if ($generalSetting?->footer_logo || $generalSetting?->logo)
                            <img class="front-logo__image front-logo__image--footer"
                                src="{{ asset($generalSetting->footer_logo ?: $generalSetting->logo) }}"
                                alt="{{ $generalSetting->app_name ?? 'Digital Magazine' }}">
                        @else
                            <span class="front-logo__mark">DM</span>
                        @endif
                    </a>
                </div>
                @if ($generalSetting?->footer_text)
                    <p class="front-footer__about">{{ $generalSetting->footer_text }}</p>
                @endif
                <div class="front-socials front-ui" aria-label="Social media links">
                    @if ($generalSetting?->facebook)
                        <a href="{{ $generalSetting->facebook }}" target="_blank" rel="noopener noreferrer"
                            aria-label="Facebook"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
                    @endif
                    @if ($generalSetting?->x)
                        <a href="{{ $generalSetting->x }}" target="_blank" rel="noopener noreferrer"
                            aria-label="X"><i class="fa-brands fa-x-twitter" aria-hidden="true"></i></a>
                    @endif
                    @if ($generalSetting?->instagram)
                        <a href="{{ $generalSetting->instagram }}" target="_blank" rel="noopener noreferrer"
                            aria-label="Instagram"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>
                    @endif
                    @if ($generalSetting?->youtube)
                        <a href="{{ $generalSetting->youtube }}" target="_blank" rel="noopener noreferrer"
                            aria-label="YouTube"><i class="fa-brands fa-youtube" aria-hidden="true"></i></a>
                    @endif
                    @if ($generalSetting?->linkedin)
                        <a href="{{ $generalSetting->linkedin }}" target="_blank" rel="noopener noreferrer"
                            aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></a>
                    @endif
                    @if ($generalSetting?->tiktok)
                        <a href="{{ $generalSetting->tiktok }}" target="_blank" rel="noopener noreferrer"
                            aria-label="TikTok"><i class="fa-brands fa-tiktok" aria-hidden="true"></i></a>
                    @endif
                </div>
            </div>

            <div class="col-6 col-md-4 front-footer__column">
                <h2 class="front-footer__title">Quick Links</h2>
                <ul class="front-footer__links">
                    <li><a href="{{ route('frontend.home') }}">Home</a></li>
                    <li><a href="{{ route('mazameen') }}">Articles</a></li>
                    {{-- Temporarily hidden - feature retained for future use.
                    <li><a href="#">ہفتہ وار میگزین</a></li>
                    --}}
                    {{-- Temporarily hidden - feature retained for future use.
                    <li><a href="{{ route('front.mazmoon-nigaar') }}">مضمون نگار</a></li>
                    --}}
                    <li><a href="{{ route('front.taaruf') }}">About</a></li>
                    {{-- Temporarily hidden - feature retained for future use.
                    <li><a href="{{ route('front.ask-question') }}">سوال پوچھیں</a></li>
                    --}}
                    {{-- Temporarily hidden - feature retained for future use.
                    <li><a href="{{ route('front.advertise') }}">تشہیر کیجئے</a></li>
                    --}}
                    <li><a href="{{ route('front.contact') }}">Contact</a></li>
                </ul>
            </div>

            {{-- Temporarily hidden - feature retained for future use.
            <div class="col-6 col-md-3 col-xl-2 front-footer__column">
                <h2 class="front-footer__title">موضوعات</h2>
                <ul class="front-footer__links">
                    @foreach ($footerCategories as $category)
                        <li><a href="{{ $category->navigation_url }}">{{ $category->name }}</a></li>
                    @endforeach
                    <li><a href="{{ route('front.mozoaat') }}">تمام موضوعات</a></li>
                </ul>
            </div>
            --}}

            <div class="col-6 col-md-4 front-footer__column">
                <h2 class="front-footer__title">Helpful Links</h2>
                <ul class="front-footer__links">
                    <li><a href="{{ route('front.faqs') }}">FAQs</a></li>
                    <li><a href="{{ route('front.privacy-policy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('front.terms-and-conditions') }}">Terms &amp; Conditions</a></li>
                    {{-- Temporarily hidden - feature retained for future use.
                    <li><a href="{{ route('front.disclaimer') }}">{{ ($infoPageLanguage ?? 'ur') === 'en' ? 'Disclaimer' : 'دستبرداری' }}</a></li>
                    --}}
                </ul>
                <div class="front-footer__contact front-ui">
                    @if ($generalSetting?->email)
                        <a href="mailto:{{ $generalSetting->email }}"><i class="fa-regular fa-envelope"
                                aria-hidden="true"></i><span>{{ $generalSetting->email }}</span></a>
                    @endif
                    @if ($generalSetting?->contact_1)
                        <a href="tel:{{ preg_replace('/[^+\d]/', '', $generalSetting->contact_1) }}"><i
                                class="fa-solid fa-phone" aria-hidden="true"></i><span
                                dir="ltr">{{ $generalSetting->contact_1 }}</span></a>
                    @endif
                    @if ($generalSetting?->contact_2)
                        <a href="tel:{{ preg_replace('/[^+\d]/', '', $generalSetting->contact_2) }}"><i
                                class="fa-solid fa-phone" aria-hidden="true"></i><span
                                dir="ltr">{{ $generalSetting->contact_2 }}</span></a>
                    @endif
                    @if ($generalSetting?->address)
                        <p><i class="fa-solid fa-location-dot"
                                aria-hidden="true"></i><span>{{ $generalSetting->address }}</span></p>
                    @endif
                </div>
            </div>

            {{-- Temporarily hidden - feature retained for future use.
            <div class="col-12 col-md-6 col-xl-3 front-footer__column front-footer__app-column">
                @if ($generalSetting?->app_section_heading)
                    <h2 class="front-footer__title">{{ $generalSetting->app_section_heading }}</h2>
                @endif
                @if ($generalSetting?->app_section_text)
                    <p class="front-footer__app-text">{{ $generalSetting->app_section_text }}</p>
                @endif
                @if ($generalSetting?->play_store_icon || $generalSetting?->app_store_icon)
                    <div class="front-footer__app-badges">
                        @if ($generalSetting?->play_store_icon)
                            @if ($generalSetting?->play_store_link)
                                <a href="{{ $generalSetting->play_store_link }}" target="_blank"
                                    rel="noopener noreferrer" aria-label="گوگل پلے ایپ">
                                    <img src="{{ asset($generalSetting->play_store_icon) }}"
                                        alt="Google Play پر حاصل کریں">
                                </a>
                            @else
                                <img src="{{ asset($generalSetting->play_store_icon) }}"
                                    alt="Google Play پر حاصل کریں">
                            @endif
                        @endif
                        @if ($generalSetting?->app_store_icon)
                            @if ($generalSetting?->app_store_link)
                                <a href="{{ $generalSetting->app_store_link }}" target="_blank"
                                    rel="noopener noreferrer" aria-label="ایپل ایپ اسٹور">
                                    <img src="{{ asset($generalSetting->app_store_icon) }}"
                                        alt="App Store سے ڈاؤن لوڈ کریں">
                                </a>
                            @else
                                <img src="{{ asset($generalSetting->app_store_icon) }}"
                                    alt="App Store سے ڈاؤن لوڈ کریں">
                            @endif
                        @endif
                    </div>
                @endif
            </div>
            --}}
        </div>

        <div class="front-footer__bottom d-flex flex-column flex-md-row justify-content-between gap-2">
            <span>© {{ date('Y') }} {{ $generalSetting?->app_name ?? 'Digital Magazine' }}. All rights reserved.</span>
            <span>Knowledge and insight for everyone</span>
        </div>
    </div>
</footer>
