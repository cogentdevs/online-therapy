<aside id="admin-sidebar" class="admin-sidebar" aria-label="Admin navigation">
    <div class="admin-sidebar-brand">
        <span class="admin-sidebar-brand-mark" aria-hidden="true">DM</span>
        <span>
            <strong>{{ $generalSetting?->app_name ?? 'Digital Magazine' }}</strong>
            <small>Administration</small>
        </span>
        <button class="admin-sidebar-close" type="button" data-sidebar-close aria-label="Close navigation">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="m6 6 12 12M18 6 6 18" />
            </svg>
        </button>
    </div>

    <nav class="admin-navigation">
        <ul class="admin-nav-list">
            @can('dashboard.view')
            <li>
                <a class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <span class="admin-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 13h6V4H4zM14 20h6v-9h-6zM4 20h6v-3H4zM14 7h6V4h-6z" />
                        </svg>
                    </span>
                    <span>Dashboard</span>
                </a>
            </li>
            @endcan

            @can('site-analytics.view')
            <li>
                <a class="admin-nav-link {{ request()->routeIs('admin.site-analytics.*') ? 'active' : '' }}"
                    href="{{ route('admin.site-analytics.index') }}">
                    <span class="admin-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 20V10M10 20V4M16 20v-7M3 20h18" />
                        </svg>
                    </span>
                    <span>Site Analytics</span>
                </a>
            </li>
            @endcan

            @role('super-admin')
                <li>
                    <a class="admin-nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}"
                        href="{{ route('admin.activity-logs.index') }}">
                        <span class="admin-nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5" />
                            </svg>
                        </span>
                        <span>Activity Logs</span>
                    </a>
                </li>
            @endrole

            {{-- Temporarily hidden - feature retained for future use --}}

            @can('categories.view')
            <li>
                <a class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                    href="{{ route('admin.categories.index') }}">
                    <span class="admin-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 5h7v6H4zM13 5h7v6h-7zM4 13h7v6H4zM13 13h7v6h-7z" />
                        </svg>
                    </span>
                    <span>Categories</span>
                </a>
            </li>
            @endcan

            {{-- Temporarily hidden - feature retained for future use.
            @can('tags.view')
            <li>
                <a class="admin-nav-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}"
                    href="{{ route('admin.tags.index') }}">
                    <span class="admin-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 5h9l7 7-8 8-8-8z" />
                            <circle cx="9" cy="10" r="1" />
                        </svg>
                    </span>
                    <span>Tags</span>
                </a>
            </li>
            @endcan
            --}}

            @can('currency.view')
            <li>
                <a class="admin-nav-link {{ request()->routeIs('admin.currency.*') ? 'active' : '' }}"
                    href="{{ route('admin.currency.index') }}">
                    <span class="admin-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M15.5 8.5h-5a2 2 0 0 0 0 4h3a2 2 0 0 1 0 4h-5M12 6v12" />
                        </svg>
                    </span>
                    <span>Currency</span>
                </a>
            </li>
            @endcan

            @can('meta-tags.view')
            <li>
                <a class="admin-nav-link {{ request()->routeIs('admin.meta-tags.*') ? 'active' : '' }}"
                    href="{{ route('admin.meta-tags.index') }}">
                    <span class="admin-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 5h9l7 7-8 8-8-8zM8 9h3M8 13h6" />
                        </svg>
                    </span>
                    <span>SEO / Meta Tags</span>
                </a>
            </li>
            @endcan

            @can('consultancies.view')
            <li>
                <a class="admin-nav-link {{ request()->routeIs('admin.consultancy.*') ? 'active' : '' }}"
                    href="{{ route('admin.consultancy.index') }}">
                    <span class="admin-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 5h6l2 2h8v12H4zM8 11h8M8 15h6" />
                        </svg>
                    </span>
                    <span>Consultancy</span>
                </a>
            </li>
            @endcan

            @can('courses.view')
            <li>
                <a class="admin-nav-link {{ request()->routeIs('admin.course.*') ? 'active' : '' }}" href="{{ route('admin.course.index') }}">
                    <span class="admin-nav-icon" aria-hidden="true"><i class="fa-solid fa-graduation-cap"></i></span><span>Courses</span>
                </a>
            </li>
            @endcan

            @php($contentManagementActive = request()->routeIs('admin.magazine.*', 'admin.article.*', 'admin.video.*', 'admin.audios.*', 'admin.author.*'))
            <li class="admin-nav-group">
                <a class="admin-nav-group-toggle {{ $contentManagementActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#contentManagementMenu" role="button"
                    aria-expanded="{{ $contentManagementActive ? 'true' : 'false' }}"
                    aria-controls="contentManagementMenu">
                    <span class="admin-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 5h6l2 2h8v12H4zM8 11h8M8 15h6" />
                        </svg>
                    </span>
                    <span class="admin-nav-label">Content Setup</span>
                    <svg class="admin-nav-chevron" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m8 10 4 4 4-4" />
                    </svg>
                </a>
                <div class="collapse {{ $contentManagementActive ? 'show' : '' }}" id="contentManagementMenu">
                    <ul class="admin-nav-submenu">
                        {{-- Magazines temporarily hidden - feature retained for future use --}}
                        @can('articles.view')
                        <li><a class="{{ request()->routeIs('admin.article.*') ? 'active' : '' }}"
                                href="{{ route('admin.article.index') }}">Articles</a></li>
                        @endcan
                        @can('videos.view')
                        <li><a class="{{ request()->routeIs('admin.video.*') ? 'active' : '' }}"
                                href="{{ route('admin.video.index') }}">Videos</a></li>
                        @endcan
                        {{-- Audios temporarily hidden - feature retained for future use --}}
                        @can('authors.view')
                        <li><a class="{{ request()->routeIs('admin.author.index', 'admin.author.create', 'admin.author.edit') ? 'active' : '' }}"
                                href="{{ route('admin.author.index') }}">Authors</a></li>
                        @endcan
                        {{-- Author Settings temporarily hidden - feature retained for future use --}}
                    </ul>
                </div>
            </li>

            @php($userManagementActive = request()->routeIs('admin.users.*', 'admin.roles.*'))
            <li class="admin-nav-group">
                <a class="admin-nav-group-toggle {{ $userManagementActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#userManagementMenu" role="button"
                    aria-expanded="{{ $userManagementActive ? 'true' : 'false' }}" aria-controls="userManagementMenu">
                    <span class="admin-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <circle cx="9" cy="8" r="3" />
                            <path d="M3.5 19v-2a5.5 5.5 0 0 1 11 0v2M16 8a3 3 0 0 1 3 3v1M17 15h4M19 13v4" />
                        </svg>
                    </span>
                    <span class="admin-nav-label">User Management</span>
                    <svg class="admin-nav-chevron" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m8 10 4 4 4-4" />
                    </svg>
                </a>
                <div class="collapse {{ $userManagementActive ? 'show' : '' }}" id="userManagementMenu">
                    <ul class="admin-nav-submenu">
                        @can('users.view')
                            <li><a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                                    href="{{ route('admin.users.index') }}">Users</a></li>
                        @endcan
                        @can('roles.view')
                            <li><a class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
                                    href="{{ route('admin.roles.index') }}">Roles</a></li>
                        @endcan
                    </ul>
                </div>
            </li>

            @php($subscriptionsActive = request()->routeIs('admin.subscription-plan.*', 'admin.payment-account.*', 'admin.membership.*', 'admin.subscriptions.*', 'admin.subscription-notification-settings.*'))
            <li class="admin-nav-group">
                <a class="admin-nav-group-toggle {{ $subscriptionsActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#subscriptionsMenu" role="button"
                    aria-expanded="{{ $subscriptionsActive ? 'true' : 'false' }}" aria-controls="subscriptionsMenu">
                    <span class="admin-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="5" width="18" height="14" rx="2" />
                            <path d="M3 10h18M7 15h4" />
                        </svg>
                    </span>
                    <span class="admin-nav-label">Subscriptions</span>
                    <svg class="admin-nav-chevron" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m8 10 4 4 4-4" />
                    </svg>
                </a>
                <div class="collapse {{ $subscriptionsActive ? 'show' : '' }}" id="subscriptionsMenu">
                    <ul class="admin-nav-submenu">
                        @can('subscription-plans.view')
                        <li><a class="{{ request()->routeIs('admin.subscription-plan.*') ? 'active' : '' }}"
                                href="{{ route('admin.subscription-plan.index') }}">Subscription Plans</a></li>
                        @endcan
                        @can('payment-accounts.view')
                        <li><a class="{{ request()->routeIs('admin.payment-account.*') ? 'active' : '' }}"
                                href="{{ route('admin.payment-account.index') }}">Payment Accounts</a></li>
                        @endcan
                        {{-- Memberships temporarily hidden - feature retained for future use --}}
                        @can('subscription-reminder-settings.view')
                        <li><a class="{{ request()->routeIs('admin.subscription-notification-settings.*') ? 'active' : '' }}"
                                href="{{ route('admin.subscription-notification-settings.edit') }}">Reminder Settings</a></li>
                        @endcan
                        @can('user-subscriptions.view')
                        <li><a class="{{ request()->routeIs('admin.user-subscriptions.*') ? 'active' : '' }}"
                                href="{{ route('admin.user-subscriptions.index') }}">User Subscriptions</a></li>
                        @endcan
                    </ul>
                </div>
            </li>

            {{-- Monetization temporarily hidden - feature retained for future use --}}

            @php($websiteManagementActive = request()->routeIs('admin.slider.*', 'admin.banner.*', 'admin.home-headings.*', 'admin.home-article.*', 'admin.taza-shumara.*', 'admin.home-cards.*', 'admin.home-sections.*', 'admin.services.*', 'admin.about.*', 'admin.info-pages.*', 'admin.faq-categories.*', 'admin.faq.*', 'admin.languages.*'))
            <li class="admin-nav-group">
                <a class="admin-nav-group-toggle {{ $websiteManagementActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#websiteManagementMenu" role="button"
                    aria-expanded="{{ $websiteManagementActive ? 'true' : 'false' }}"
                    aria-controls="websiteManagementMenu">
                    <span class="admin-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18" />
                        </svg>
                    </span>
                    <span class="admin-nav-label">Website</span>
                    <svg class="admin-nav-chevron" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m8 10 4 4 4-4" />
                    </svg>
                </a>
                <div class="collapse {{ $websiteManagementActive ? 'show' : '' }}" id="websiteManagementMenu">
                    <ul class="admin-nav-submenu">
                        @can('sliders.view')
                        <li><a class="{{ request()->routeIs('admin.slider.*') ? 'active' : '' }}"
                                href="{{ route('admin.slider.index') }}">Sliders</a></li>
                        @endcan
                        @can('banners.view')
                        <li><a class="{{ request()->routeIs('admin.banner.*') ? 'active' : '' }}"
                                href="{{ route('admin.banner.index') }}">Banners</a></li>
                        @endcan
                        @can('home-headings.view')
                        <li><a class="{{ request()->routeIs('admin.home-headings.*') ? 'active' : '' }}"
                                href="{{ route('admin.home-headings.index') }}">Home Headings</a></li>
                        @endcan
                        @can('home-article.view')
                        <li><a class="{{ request()->routeIs('admin.home-article.*') ? 'active' : '' }}"
                                href="{{ route('admin.home-article.index') }}">Home Article</a></li>
                        @endcan
                        @can('taza-shumara.view')
                        <li><a class="{{ request()->routeIs('admin.taza-shumara.*') ? 'active' : '' }}"
                                href="{{ route('admin.taza-shumara.index') }}">Taza Shumara</a></li>
                        @endcan
                        @can('home-cards.view')
                        <li><a class="{{ request()->routeIs('admin.home-cards.*') ? 'active' : '' }}"
                                href="{{ route('admin.home-cards.index') }}">Home Cards</a></li>
                        @endcan
                        @can('home-sections.view')
                        <li><a class="{{ request()->routeIs('admin.home-sections.*') ? 'active' : '' }}"
                                href="{{ route('admin.home-sections.index') }}">Home Sections</a></li>
                        @endcan
                        @can('services.view')
                        <li><a class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}"
                                href="{{ route('admin.services.index') }}">Services</a></li>
                        @endcan
                        @can('abouts.view')
                        <li><a class="{{ request()->routeIs('admin.about.*') ? 'active' : '' }}"
                                href="{{ route('admin.about.index') }}">About</a></li>
                        @endcan
                        @can('privacy-policy.view')
                            <li><a class="{{ request()->routeIs('admin.info-pages.*') && request()->route('page') === 'privacy-policy' ? 'active' : '' }}"
                                    href="{{ route('admin.info-pages.edit', ['page' => 'privacy-policy', 'language' => config('content_language.code')]) }}">Privacy Policy</a></li>
                        @endcan
                        @can('terms-conditions.view')
                            <li><a class="{{ request()->routeIs('admin.info-pages.*') && request()->route('page') === 'terms-and-conditions' ? 'active' : '' }}"
                                    href="{{ route('admin.info-pages.edit', ['page' => 'terms-and-conditions', 'language' => config('content_language.code')]) }}">Terms &amp; Conditions</a></li>
                        @endcan
                        @can('disclaimer.view')
                            <li><a class="{{ request()->routeIs('admin.info-pages.*') && request()->route('page') === 'disclaimer' ? 'active' : '' }}"
                                    href="{{ route('admin.info-pages.edit', ['page' => 'disclaimer', 'language' => config('content_language.code')]) }}">Disclaimer</a></li>
                        @endcan
                        @can('faq-categories.view')
                        <li><a class="{{ request()->routeIs('admin.faq-categories.*') ? 'active' : '' }}"
                                href="{{ route('admin.faq-categories.index') }}">FAQ Categories</a></li>
                        @endcan
                        @can('faqs.view')
                        <li><a class="{{ request()->routeIs('admin.faq.*') ? 'active' : '' }}"
                                href="{{ route('admin.faq.index') }}">FAQs</a></li>
                        @endcan
                        <li><span>Languages</span></li>
                    </ul>
                </div>
            </li>

            @php($otherActive = request()->routeIs('admin.jobs.*', 'admin.contacts.*', 'admin.newsletter-subscribers.*', 'admin.newsletter-campaigns.*'))
            <li class="admin-nav-group">
                <a class="admin-nav-group-toggle {{ $otherActive ? '' : 'collapsed' }}" data-bs-toggle="collapse"
                    href="#otherMenu" role="button" aria-expanded="{{ $otherActive ? 'true' : 'false' }}"
                    aria-controls="otherMenu">
                    <span class="admin-nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <circle cx="5" cy="12" r="1" />
                            <circle cx="12" cy="12" r="1" />
                            <circle cx="19" cy="12" r="1" />
                        </svg>
                    </span>
                    <span class="admin-nav-label">Other</span>
                    <svg class="admin-nav-chevron" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m8 10 4 4 4-4" />
                    </svg>
                </a>
                <div class="collapse {{ $otherActive ? 'show' : '' }}" id="otherMenu">
                    <ul class="admin-nav-submenu">
                        <li><span>Jobs</span></li>
                        @can('contacts.view')
                            <li><a class="{{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}"
                                    href="{{ route('admin.contacts.index') }}">Contact Messages</a></li>
                        @endcan
                        @can('newsletter-subscribers.view')
                            <li><a class="{{ request()->routeIs('admin.newsletter-subscribers.*') ? 'active' : '' }}"
                                    href="{{ route('admin.newsletter-subscribers.index') }}">Newsletter Subscribers</a></li>
                        @endcan
                        @can('newsletter-campaigns.view')
                            <li><a class="{{ request()->routeIs('admin.newsletter-campaigns.*') ? 'active' : '' }}" href="{{ route('admin.newsletter-campaigns.index') }}">Newsletter Campaigns</a></li>
                        @endcan
                    </ul>
                </div>
            </li>
        </ul>
    </nav>

    <div class="admin-sidebar-footer">
        <span class="admin-nav-link admin-nav-placeholder" aria-disabled="true">
            <span class="admin-nav-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M5 19V9M12 19V5M19 19v-7" />
                </svg>
            </span>
            <span>Reports</span>
        </span>

        @can('general-settings.view')
        <a class="admin-nav-link {{ request()->routeIs('admin.general-setting.*') ? 'active' : '' }}"
            href="{{ route('admin.general-setting.edit') }}">
            <span class="admin-nav-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path
                        d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
            </span>
            <span>General Settings</span>
        </a>
        @endcan

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="admin-nav-link admin-sidebar-logout">
                <span class="admin-nav-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M10 5H5v14h5M14 8l4 4-4 4M8 12h10" />
                    </svg>
                </span>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
