<header class="admin-header">
    <div class="admin-header-leading">
        <button
            class="admin-sidebar-toggle"
            type="button"
            data-sidebar-toggle
            aria-label="Toggle navigation"
            aria-controls="admin-sidebar"
            aria-expanded="false"
        >
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M4 7h16M4 12h16M4 17h16" />
            </svg>
        </button>

        <div class="admin-page-heading">
            <span>Admin Panel</span>
            <h1>@yield('title', 'Dashboard')</h1>
        </div>
    </div>

    <div class="dropdown">
        <button
            class="admin-profile-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
        >
            <span class="admin-profile-avatar" aria-hidden="true">
                {{ str(auth()->user()->name)->substr(0, 1)->upper() }}
            </span>
            <span class="admin-profile-copy">
                <strong>{{ auth()->user()->name }}</strong>
                <small>{{ auth()->user()->getRoleNames()->map(fn ($role) => str($role)->headline())->join(', ') ?: 'Admin' }}</small>
            </span>
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="m7 10 5 5 5-5" />
            </svg>
        </button>

        <div class="dropdown-menu dropdown-menu-end admin-profile-menu">
            <div class="admin-profile-menu-heading">
                <strong>{{ auth()->user()->name }}</strong>
                <span>{{ auth()->user()->email }}</span>
            </div>
            <div class="dropdown-divider"></div>
            @can('change-password.view')
            <a href="{{ route('admin.change-password') }}" class="dropdown-item admin-profile-action">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M7 10V8a5 5 0 0 1 10 0v2M5 10h14v10H5z" />
                </svg>
                Change Password
            </a>
            @endcan
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="dropdown-item admin-logout-button">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M10 5H5v14h5M14 8l4 4-4 4M8 12h10" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>
