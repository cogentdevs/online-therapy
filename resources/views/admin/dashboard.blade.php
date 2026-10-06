@extends('layouts.adminLayout.admin-design')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="admin-page-intro">
            <div>
                <span class="admin-page-eyebrow">Overview</span>
                <h2>Dashboard</h2>
                <p>Welcome, {{ auth()->user()->name }}. Review current publishing activity and administration totals.</p>
            </div>
        </div>

        <div class="row g-4 mb-4">
            @can('articles.view')
                @if (isset($summaries['articles']))
                    <div class="col-12 col-md-6 col-xl-4" data-dashboard-module="articles">
                        <article class="card admin-dashboard-summary-card h-100">
                            <div class="card-body">
                                <div class="admin-dashboard-card-heading">
                                    <span class="admin-dashboard-card-icon"><i class="fa-solid fa-newspaper" aria-hidden="true"></i></span>
                                    <h3>Articles</h3>
                                </div>
                                <div class="admin-dashboard-metrics">
                                    <div><strong data-dashboard-total="{{ $summaries['articles']['total'] }}">{{ number_format($summaries['articles']['total']) }}</strong><span>Total</span></div>
                                    <div><strong data-dashboard-published="{{ $summaries['articles']['published'] }}">{{ number_format($summaries['articles']['published']) }}</strong><span>Published</span></div>
                                </div>
                                <a class="admin-dashboard-card-link stretched-link" href="{{ route('admin.article.index') }}">View Articles <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i></a>
                            </div>
                        </article>
                    </div>
                @endif
            @endcan
            {{-- Advertising Requests temporarily hidden - feature retained for future use --}}
            {{-- Ask Questions temporarily hidden - feature retained for future use --}}
            {{-- Magazines temporarily hidden - feature retained for future use --}}

            @can('users.view')
                @if (isset($summaries['users']))
                    <div class="col-12 col-md-6 col-xl-4" data-dashboard-module="users">
                        <article class="card admin-dashboard-summary-card h-100">
                            <div class="card-body">
                                <div class="admin-dashboard-card-heading">
                                    <span class="admin-dashboard-card-icon"><i class="fa-solid fa-user-shield" aria-hidden="true"></i></span>
                                    <h3>Users</h3>
                                </div>
                                <div class="admin-dashboard-metrics">
                                    <div><strong data-dashboard-total="{{ $summaries['users']['total'] }}">{{ number_format($summaries['users']['total']) }}</strong><span>Total</span></div>
                                </div>
                                <a class="admin-dashboard-card-link stretched-link" href="{{ route('admin.users.index') }}">View Users <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i></a>
                            </div>
                        </article>
                    </div>
                @endif
            @endcan

            @can('contacts.view')
                @if (isset($summaries['contacts']))
                    <div class="col-12 col-md-6 col-xl-4" data-dashboard-module="contacts">
                        <article class="card admin-dashboard-summary-card h-100">
                            <div class="card-body">
                                <div class="admin-dashboard-card-heading">
                                    <span class="admin-dashboard-card-icon"><i class="fa-solid fa-envelope" aria-hidden="true"></i></span>
                                    <h3>Contact Messages</h3>
                                </div>
                                <div class="admin-dashboard-metrics">
                                    <div><strong data-dashboard-unread="{{ $summaries['contacts']['unread'] }}">{{ number_format($summaries['contacts']['unread']) }}</strong><span>Unread</span></div>
                                </div>
                                <a class="admin-dashboard-card-link stretched-link" href="{{ route('admin.contacts.index') }}">View Contacts <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i></a>
                            </div>
                        </article>
                    </div>
                @endif
            @endcan
        </div>

        <section class="card admin-settings-card admin-subscription-trend" @if ($hasSubscriptionTrendData) data-subscription-trend @else data-subscription-trend-empty @endif>
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3>Subscription Trend</h3>
                    <p>Frontend subscription activity over time.</p>
                </div>
                <span class="badge text-bg-light border">Last 30 Days</span>
            </div>
            <div class="card-body">
                @if (! $hasSubscriptionTrendData)
                    <div class="admin-subscription-empty-state">
                        <div class="admin-chart-placeholder" aria-hidden="true"><i class="fa-solid fa-chart-line"></i></div>
                        <h4>No subscription data available yet.</h4>
                        <p>Subscription trends will appear here once user subscription activity begins.</p>
                    </div>
                @else
                    <div class="admin-subscription-chart" data-subscription-trend-chart
                        data-labels='@json($subscriptionTrendLabels)'
                        data-values='@json($subscriptionTrendData)'>
                        <canvas aria-label="Daily subscription purchases during the last 30 days" role="img"></canvas>
                        <div class="admin-subscription-chart__tooltip" data-subscription-trend-tooltip hidden></div>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
