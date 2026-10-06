<header class="front-header">
    <div class="front-topbar">
        <div class="front-header-shell front-topbar__inner">
            <div class="front-topbar__socials" aria-label="Social media links">
                @if ($generalSetting?->facebook)
                    <a href="{{ $generalSetting->facebook }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
                @endif
                @if ($generalSetting?->x)
                    <a href="{{ $generalSetting->x }}" target="_blank" rel="noopener noreferrer" aria-label="X"><i class="fa-brands fa-x-twitter" aria-hidden="true"></i></a>
                @endif
                @if ($generalSetting?->instagram)
                    <a href="{{ $generalSetting->instagram }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>
                @endif
                @if ($generalSetting?->youtube)
                    <a href="{{ $generalSetting->youtube }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fa-brands fa-youtube" aria-hidden="true"></i></a>
                @endif
                @if ($generalSetting?->linkedin)
                    <a href="{{ $generalSetting->linkedin }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></a>
                @endif
                @if ($generalSetting?->tiktok)
                    <a href="{{ $generalSetting->tiktok }}" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><i class="fa-brands fa-tiktok" aria-hidden="true"></i></a>
                @endif
            </div>

            <div class="front-topbar__contact">
                @if ($generalSetting?->email)
                    <a href="mailto:{{ $generalSetting->email }}"><i class="fa-regular fa-envelope" aria-hidden="true"></i><span>{{ $generalSetting->email }}</span></a>
                @endif
                @if ($generalSetting?->contact_1)
                    <a href="tel:{{ preg_replace('/[^+\d]/', '', $generalSetting->contact_1) }}"><i class="fa-solid fa-phone" aria-hidden="true"></i><span dir="ltr">{{ $generalSetting->contact_1 }}</span></a>
                @elseif ($generalSetting?->contact_2)
                    <a href="tel:{{ preg_replace('/[^+\d]/', '', $generalSetting->contact_2) }}"><i class="fa-solid fa-phone" aria-hidden="true"></i><span dir="ltr">{{ $generalSetting->contact_2 }}</span></a>
                @endif
            </div>
        </div>
    </div>

    <div class="front-brand-row">
        <div class="front-header-shell front-brand-row__inner">
            <a class="front-logo" href="{{ route('frontend.home') }}" aria-label="Digital Magazine home">
                @if ($generalSetting?->logo)
                    <img class="front-logo__image" src="{{ asset($generalSetting->logo) }}"
                        alt="{{ $generalSetting->app_name ?? 'Digital Magazine' }}">
                @else
                    <span class="front-logo__mark">DM</span>
                @endif
                {{-- <span class="front-logo__text">
                    <strong>{{ $generalSetting?->app_name ?? 'Digital Magazine' }}</strong>
                    <small>علم، آگہی اور معتبر صحافت</small>
                </span> --}}
            </a>

            {{-- Temporarily hidden - feature retained for future use.
            <div class="front-header-ad front-header-ad--desktop">
                <x-frontend.ad-slot :ad="$frontendAds->get('header')?->get('header_ad')" size="728 × 90" />
            </div>
            --}}

            <div class="front-header-actions front-ui">
                <a class="front-subscribe" href="{{ route('front.subscriptions') }}">Subscribe</a>
                <div class="dropdown front-profile">
                    <button class="front-profile-button" type="button" data-bs-toggle="dropdown"
                        data-bs-display="static" aria-expanded="false" aria-label="User menu">
                        <i class="fa-regular fa-user" aria-hidden="true"></i>
                    </button>
                    <ul class="dropdown-menu front-profile-menu text-end">
                        @auth('web')
                            <li><span class="dropdown-item-text">{{ auth('web')->user()->name }}</span></li>
                            @if (auth('web')->user()->hasRole('user', 'web') && auth('web')->user()->is_active)
                                <li><a class="dropdown-item" href="{{ route('front.account') }}"><i class="fa-regular fa-user"
                                            aria-hidden="true"></i> My Account</a></li>
                                <li>
                                    <form method="post" action="{{ route('front.logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Log Out</button>
                                    </form>
                                </li>
                            @elseif (auth('web')->user()->canAccessAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin Panel</a></li>
                            @endif
                        @else
                            <li><a class="dropdown-item" href="{{ route('front.login') }}"><i class="fa-solid fa-right-to-bracket"
                                        aria-hidden="true"></i> Log In</a></li>
                            <li><a class="dropdown-item" href="{{ route('front.register') }}"><i class="fa-regular fa-user"
                                        aria-hidden="true"></i> Register</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            {{-- Temporarily hidden - feature retained for future use.
            <div class="front-header-ad front-header-ad--mobile">
                <x-frontend.ad-slot :ad="$frontendAds->get('header')?->get('header_ad')" size="728 × 90" />
            </div>
            --}}
        </div>
    </div>

    <div class="front-main-nav">
        <nav class="navbar navbar-expand-lg" aria-label="Main navigation">
            <div class="front-header-shell front-nav-shell">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#frontNavigation" aria-controls="frontNavigation" aria-expanded="false"
                    aria-label="Open navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-center" id="frontNavigation">
                    <ul class="navbar-nav front-ui align-items-lg-center">
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.home') ? 'active' : '' }}"
                                href="{{ route('frontend.home') }}" @if (request()->routeIs('frontend.home')) aria-current="page" @endif>Home</a>
                        </li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('mazameen', 'mazmoon-detail') ? 'active' : '' }}"
                                href="{{ route('mazameen') }}" @if (request()->routeIs('mazameen', 'mazmoon-detail')) aria-current="page" @endif>Articles</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('front.consultancies.*') ? 'active' : '' }}"
                                href="{{ route('front.consultancies.index') }}" @if (request()->routeIs('front.consultancies.*')) aria-current="page" @endif>Consultancy</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('front.courses.*') ? 'active' : '' }}"
                                href="{{ route('front.courses.index') }}" @if (request()->routeIs('front.courses.*')) aria-current="page" @endif>Courses</a></li>
                        {{-- Temporarily hidden - feature retained for future use.
                        <li class="nav-item dropdown front-nav-dropdown">
                            <div class="front-nav-parent">
                                <a class="nav-link front-nav-parent-link {{ request()->routeIs('taza.*', 'sabqa-shumare') ? 'active' : '' }}"
                                    href="#" data-front-mobile-submenu>ہفتہ
                                    وار میگزین</a>
                                <button class="front-nav-submenu-toggle dropdown-toggle d-lg-none" type="button"
                                    data-front-mobile-submenu-toggle aria-expanded="false"
                                    aria-label="ہفتہ وار میگزین ذیلی مینو کھولیں"></button>
                            </div>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item {{ request()->routeIs('taza.shumara') ? 'active' : '' }}"
                                        href="{{ route('taza.shumara') }}" @if (request()->routeIs('taza.shumara')) aria-current="page" @endif>تازہ شمارہ</a></li>
                                <li><a class="dropdown-item {{ request()->routeIs('sabqa-shumare') ? 'active' : '' }}"
                                        href="{{ route('sabqa-shumare') }}" @if (request()->routeIs('sabqa-shumare')) aria-current="page" @endif>سابقہ شمارے</a></li>
                            </ul>
                        </li>
                        --}}
                        {{-- Temporarily hidden - feature retained for future use.
                        <li class="nav-item dropdown front-nav-dropdown">
                            <div class="front-nav-parent">
                                <a class="nav-link front-nav-parent-link {{ request()->routeIs('front.mozoaat', 'mozu-detail') ? 'active' : '' }}" href="{{ route('front.mozoaat') }}"
                                    data-front-mobile-submenu>موضوعات</a>
                                <button class="front-nav-submenu-toggle dropdown-toggle d-lg-none" type="button"
                                    data-front-mobile-submenu-toggle aria-expanded="false"
                                    aria-label="موضوعات کی اقسام کھولیں"></button>
                            </div>
                            <ul class="dropdown-menu">
                                @foreach ($navigationCategories as $category)
                                    <li><a class="dropdown-item"
                                            href="{{ $category->navigation_url }}">{{ $category->name }}</a></li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item {{ request()->routeIs('front.mozoaat') ? 'active' : '' }}"
                                        href="{{ route('front.mozoaat') }}">تمام موضوعات</a></li>
                            </ul>
                        </li>
                        --}}
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('front.mazmoon-nigaar') ? 'active' : '' }}"
                                href="{{ route('front.mazmoon-nigaar') }}">Authors</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('front.taaruf') ? 'active' : '' }}"
                                href="{{ route('front.taaruf') }}" @if (request()->routeIs('front.taaruf')) aria-current="page" @endif>About</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('front.ask-question*') ? 'active' : '' }}" href="{{ route('front.ask-question') }}" @if (request()->routeIs('front.ask-question*')) aria-current="page" @endif>Ask a Question</a></li>
                        {{-- Temporarily hidden - feature retained for future use.
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('front.advertise*') ? 'active' : '' }}" href="{{ route('front.advertise') }}" @if (request()->routeIs('front.advertise*')) aria-current="page" @endif>تشہیر کیجئے</a></li>
                        --}}
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('front.contact*') ? 'active' : '' }}"
                                href="{{ route('front.contact') }}" @if (request()->routeIs('front.contact*')) aria-current="page" @endif>Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>
