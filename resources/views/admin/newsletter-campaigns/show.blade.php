@extends('layouts.adminLayout.admin-design')

@section('title', $campaign->title)

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div><h2 class="mb-1">{{ $campaign->title }}</h2><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.newsletter-campaigns.index') }}">Newsletter Campaigns</a></li><li class="breadcrumb-item active" aria-current="page">Detail</li></ol></nav></div>
            <a class="btn btn-outline-secondary" href="{{ route('admin.newsletter-campaigns.index') }}"><i class="fa-solid fa-arrow-left me-2" aria-hidden="true"></i>Back</a>
        </div>
        @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        <section class="card admin-settings-card mb-4">
            <div class="card-header"><h3>Campaign Details</h3><p>{{ $campaign->short_description ?: 'No short description provided.' }}</p></div>
            <div class="card-body"><div class="row g-4">
                <div class="col-sm-6 col-xl-3"><small class="text-muted d-block mb-1">Status</small><span class="badge {{ $campaign->statusBadgeClass() }}">{{ $campaign->statusLabel() }}</span></div>
                <div class="col-sm-6 col-xl-3"><small class="text-muted d-block mb-1">Brevo Campaign ID</small><strong>{{ $campaign->brevo_campaign_id ?? '—' }}</strong></div>
                <div class="col-sm-6 col-xl-3"><small class="text-muted d-block mb-1">Sent</small><strong>{{ $campaign->sent_at?->format('d M Y, h:i A') ?? '—' }}</strong></div>
                <div class="col-sm-6 col-xl-3"><small class="text-muted d-block mb-1">Created By</small><strong>{{ $campaign->creator?->name ?? '—' }}</strong></div>
            </div>@if($campaign->status === \App\Models\NewsletterCampaign::STATUS_FAILED && $campaign->brevo_error)<div class="alert alert-danger mt-4 mb-0"><strong>Delivery failure:</strong> {{ $campaign->brevo_error }}</div>@endif</div>
        </section>

        <section class="card admin-settings-card mb-4">
            <div class="card-header"><h3>Campaign Actions</h3><p>Preview, test or deliver this campaign according to its current status.</p></div>
            <div class="card-body"><div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.newsletter-campaigns.preview', $campaign) }}"><i class="fa-solid fa-file-lines me-2" aria-hidden="true"></i>Preview</a>
                @if($campaign->status === \App\Models\NewsletterCampaign::STATUS_DRAFT)
                    @can('newsletter-campaigns.edit')<a class="btn btn-outline-primary" href="{{ route('admin.newsletter-campaigns.edit', $campaign) }}"><i class="fa-solid fa-pen-to-square me-2" aria-hidden="true"></i>Edit</a><form method="POST" action="{{ route('admin.newsletter-campaigns.test', $campaign) }}" class="d-flex flex-wrap gap-2">@csrf<input class="form-control" type="email" name="email" required value="{{ auth()->user()->email }}" aria-label="Test email recipient"><button class="btn btn-primary admin-primary-button text-nowrap"><i class="fa-solid fa-envelope me-2" aria-hidden="true"></i>Send Test Email</button></form>@endcan
                    @can('newsletter-campaigns.send')<form method="POST" action="{{ route('admin.newsletter-campaigns.send', $campaign) }}">@csrf<button class="btn btn-success" type="submit" data-action-confirm data-action-title="Send this newsletter now?" data-action-text="It will be sent to the configured Newsletter Brevo list." data-action-confirm-text="Yes, Send Now" data-action-icon="question"><i class="fa-solid fa-paper-plane me-2" aria-hidden="true"></i>Send Now</button></form>@endcan
                @elseif($campaign->status === \App\Models\NewsletterCampaign::STATUS_FAILED)
                    @can('newsletter-campaigns.send')<form method="POST" action="{{ route('admin.newsletter-campaigns.retry', $campaign) }}">@csrf<button class="btn btn-warning" type="submit" data-action-confirm data-action-title="Retry this failed newsletter?" data-action-text="An existing Brevo campaign will be reused when available." data-action-confirm-text="Yes, Retry" data-action-icon="question"><i class="fa-solid fa-rotate-right me-2" aria-hidden="true"></i>Retry</button></form>@endcan
                @endif
            </div></div>
        </section>

        <section class="card admin-settings-card">
            <div class="card-header"><h3>Selected Content</h3><p>Content appears in the saved campaign order.</p></div>
            <div class="card-body"><div class="vstack gap-3">@forelse($campaign->contents as $item)<article class="border rounded p-3"><div class="d-flex flex-wrap justify-content-between gap-2 mb-2"><span class="badge text-bg-light border">{{ ucfirst($item->content_type) }}</span><small class="text-muted">Sort order: {{ $item->sort_order }}</small></div><h4 class="h5">{{ $item->displayTitle() }}</h4><p class="mb-2">{{ $item->truncatedDescription() }}</p>@if($item->frontendUrl())<a class="btn btn-sm btn-outline-primary" href="{{ $item->frontendUrl() }}" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square me-1" aria-hidden="true"></i>Frontend destination</a>@endif</article>@empty<div class="text-muted text-center py-4">No content selected.</div>@endforelse</div></div>
        </section>
    </div>
@endsection
