@extends('layouts.adminLayout.admin-design')

@section('title', 'Newsletter Campaigns')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Newsletter Campaigns</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Newsletter Campaigns</li>
                    </ol>
                </nav>
            </div>
            @can('newsletter-campaigns.create')
                <a class="btn btn-primary admin-primary-button" href="{{ route('admin.newsletter-campaigns.create') }}">
                    <i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Create Campaign
                </a>
            @endcan
        </div>

        @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
        @if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        <div class="card admin-settings-card">
            <div class="card-header">
                <h3>Newsletter Campaigns</h3>
                <p>Create, preview and manage Newsletter deliveries.</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" data-admin-datatable>
                        <thead>
                            <tr><th>Title</th><th>Content</th><th>Status</th><th>Brevo ID</th><th>Sent</th><th>Created By</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($campaigns as $campaign)
                                <tr>
                                    <td>{{ $campaign->title }}</td>
                                    <td>{{ $campaign->contents_count }}</td>
                                    <td><span class="badge {{ $campaign->statusBadgeClass() }}">{{ $campaign->statusLabel() }}</span></td>
                                    <td>{{ $campaign->brevo_campaign_id ?? '—' }}</td>
                                    <td>{{ $campaign->sent_at?->format('d M Y, h:i A') ?? '—' }}</td>
                                    <td>{{ $campaign->creator?->name ?? '—' }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.newsletter-campaigns.show', $campaign) }}"><i class="fa-solid fa-eye me-1" aria-hidden="true"></i>View</a>
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.newsletter-campaigns.preview', $campaign) }}"><i class="fa-solid fa-file-lines me-1" aria-hidden="true"></i>Preview</a>
                                            @if ($campaign->status === \App\Models\NewsletterCampaign::STATUS_DRAFT)
                                                @can('newsletter-campaigns.edit')<a class="btn btn-sm btn-outline-primary" href="{{ route('admin.newsletter-campaigns.edit', $campaign) }}"><i class="fa-solid fa-pen-to-square me-1" aria-hidden="true"></i>Edit</a>@endcan
                                                @can('newsletter-campaigns.send')<form method="POST" action="{{ route('admin.newsletter-campaigns.send', $campaign) }}">@csrf<button class="btn btn-sm btn-success" type="submit" data-action-confirm data-action-title="Send this newsletter now?" data-action-text="It will be sent to the configured Newsletter Brevo list." data-action-confirm-text="Yes, Send Now" data-action-icon="question"><i class="fa-solid fa-paper-plane me-1" aria-hidden="true"></i>Send Now</button></form>@endcan
                                                @can('newsletter-campaigns.delete')<form method="POST" action="{{ route('admin.newsletter-campaigns.destroy', $campaign) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit" data-action-confirm data-action-title="Delete draft campaign?" data-action-confirm-text="Yes, Delete" data-action-icon="warning"><i class="fa-solid fa-trash me-1" aria-hidden="true"></i>Delete</button></form>@endcan
                                            @elseif ($campaign->status === \App\Models\NewsletterCampaign::STATUS_FAILED)
                                                @can('newsletter-campaigns.send')<form method="POST" action="{{ route('admin.newsletter-campaigns.retry', $campaign) }}">@csrf<button class="btn btn-sm btn-warning" type="submit" data-action-confirm data-action-title="Retry this failed newsletter?" data-action-text="An existing Brevo campaign will be reused when available." data-action-confirm-text="Yes, Retry" data-action-icon="question"><i class="fa-solid fa-rotate-right me-1" aria-hidden="true"></i>Retry</button></form>@endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $campaigns->links() }}
            </div>
        </div>
    </div>
@endsection
