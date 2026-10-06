<div class="front-account-sidebar">
    <nav aria-label="اکاؤنٹ مینو">
        <a class="front-account-nav-link {{ request()->routeIs('front.account') ? 'active' : '' }}"
            href="{{ route('front.account') }}" @if (request()->routeIs('front.account')) aria-current="page" @endif>
            <i class="fa-solid fa-house" aria-hidden="true"></i><span>میرا اکاؤنٹ</span>
        </a>
        <a class="front-account-nav-link {{ request()->routeIs('front.account.bookmarks*') ? 'active' : '' }}"
            href="{{ route('front.account.bookmarks') }}"
            @if (request()->routeIs('front.account.bookmarks*')) aria-current="page" @endif>
            <i class="fa-regular fa-bookmark" aria-hidden="true"></i><span>بک مارکس</span>
        </a>
        <a class="front-account-nav-link {{ request()->routeIs('front.account.recent-activities*') ? 'active' : '' }}"
            href="{{ route('front.account.recent-activities') }}"
            @if (request()->routeIs('front.account.recent-activities*')) aria-current="page" @endif>
            <i class="fa-regular fa-clock" aria-hidden="true"></i><span>حالیہ سرگرمیاں</span>
        </a>
        <a class="front-account-nav-link {{ request()->routeIs('front.account.profile*') ? 'active' : '' }}"
            href="{{ route('front.account.profile') }}"
            @if (request()->routeIs('front.account.profile*')) aria-current="page" @endif>
            <i class="fa-regular fa-user" aria-hidden="true"></i><span>پروفائل</span>
        </a>
        <a class="front-account-nav-link {{ request()->routeIs('front.account.subscriptions*') ? 'active' : '' }}"
            href="{{ route('front.account.subscriptions') }}"
            @if (request()->routeIs('front.account.subscriptions*')) aria-current="page" @endif>
            <i class="fa-solid fa-layer-group" aria-hidden="true"></i><span>میری سبسکرپشنز</span>
        </a>
        <a class="front-account-nav-link {{ request()->routeIs('front.account.advertising-requests.*') ? 'active' : '' }}"
            href="{{ route('front.account.advertising-requests.index') }}"
            @if (request()->routeIs('front.account.advertising-requests.*')) aria-current="page" @endif>
            <i class="fa-solid fa-bullhorn" aria-hidden="true"></i><span>میری تشہیری درخواستیں</span>
        </a>
        <a class="front-account-nav-link {{ request()->routeIs('front.account.questions.*') ? 'active' : '' }}"
            href="{{ route('front.account.questions.index') }}"
            @if (request()->routeIs('front.account.questions.*')) aria-current="page" @endif>
            <i class="fa-regular fa-circle-question" aria-hidden="true"></i><span>میرے سوالات</span>
        </a>
        <a class="front-account-nav-link {{ request()->routeIs('front.account.two-factor-authentication*') ? 'active' : '' }}"
            href="{{ route('front.account.two-factor-authentication') }}"
            @if (request()->routeIs('front.account.two-factor-authentication*')) aria-current="page" @endif>
            <i class="fa-solid fa-shield-halved" aria-hidden="true"></i><span>دو مرحلہ توثیق</span>
        </a>
        <a class="front-account-nav-link {{ request()->routeIs('front.account.change-password*') ? 'active' : '' }}"
            href="{{ route('front.account.change-password') }}"
            @if (request()->routeIs('front.account.change-password*')) aria-current="page" @endif>
            <i class="fa-solid fa-lock" aria-hidden="true"></i><span>پاس ورڈ تبدیل کریں</span>
        </a>
        <form method="post" action="{{ route('front.logout') }}">
            @csrf
            <button class="front-account-nav-link front-account-logout" type="submit">
                <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i><span>لاگ آؤٹ</span>
            </button>
        </form>
    </nav>
    <div class="front-account-sidebar-art" aria-hidden="true">
        <img src="{{ asset('images/frontend-images/magazine/newspaper-spread.svg') }}" alt="">
        <span></span>
    </div>
</div>
