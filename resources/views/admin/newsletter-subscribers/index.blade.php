@extends('layouts.adminLayout.admin-design')

@section('title', 'Newsletter Subscribers')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Newsletter Subscribers</h2>
                <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active" aria-current="page">Newsletter Subscribers</li></ol></nav>
            </div>
        </div>

        @if (session('status'))<div class="alert alert-success" role="alert">{{ session('status') }}</div>@endif
        @if (session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif

        <div class="card admin-settings-card">
            <div class="card-header">
                <h3>Subscribers</h3>
                <p>Local Newsletter subscription state and dedicated Brevo list synchronization.</p>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @foreach (['all' => 'All', 'subscribed' => 'Subscribed', 'unsubscribed' => 'Unsubscribed', 'failed' => 'Sync Failed'] as $key => $label)
                        <a class="btn btn-sm {{ $filter === $key ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.newsletter-subscribers.index', ['filter' => $key]) }}">{{ $label }}</a>
                    @endforeach
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" data-admin-datatable>
                        <thead><tr><th>Email</th><th>Subscription Status</th><th>Subscribed At</th><th>Unsubscribed At</th><th>Brevo Sync</th><th>Last Sync</th><th>Actions</th></tr></thead>
                        <tbody>
                            @forelse ($subscribers as $subscriber)
                                <tr>
                                    <td>{{ $subscriber->email }}</td>
                                    <td><span class="badge {{ $subscriber->status === \App\Models\NewsletterSubscriber::STATUS_SUBSCRIBED ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $subscriber->statusLabel() }}</span></td>
                                    <td>{{ $subscriber->subscribed_at?->format('d M Y, h:i A') ?? '—' }}</td>
                                    <td>{{ $subscriber->unsubscribed_at?->format('d M Y, h:i A') ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $subscriber->brevo_sync_status === 'synced' ? 'text-bg-success' : ($subscriber->brevo_sync_status === 'failed' ? 'text-bg-danger' : 'text-bg-warning') }}">{{ $subscriber->syncStatusLabel() }}</span>
                                        @if ($subscriber->brevo_error)<small class="d-block text-danger mt-1" title="{{ $subscriber->brevo_error }}">{{ \Illuminate\Support\Str::limit($subscriber->brevo_error, 80) }}</small>@endif
                                    </td>
                                    <td>{{ $subscriber->brevo_synced_at?->format('d M Y, h:i A') ?? '—' }}</td>
                                    <td>
                                        @can('newsletter-subscribers.edit')
                                            @if ($subscriber->brevo_sync_status === \App\Models\NewsletterSubscriber::SYNC_FAILED)
                                                <form method="post" action="{{ route('admin.newsletter-subscribers.resync', $subscriber) }}">@csrf<button class="btn btn-sm btn-outline-primary" type="submit">Re-sync with Brevo</button></form>
                                            @else — @endif
                                        @else — @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-4">No Newsletter subscribers found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
