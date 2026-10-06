@extends('layouts.adminLayout.admin-design')

@section('title', 'Advertising Request '.$adRequest->request_no)

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div><h2 class="mb-1">{{ $adRequest->request_no }}</h2><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.advertising-requests.index') }}">Advertising Requests</a></li><li class="breadcrumb-item active" aria-current="page">Detail</li></ol></nav></div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.advertising-requests.index') }}"><i class="fa-solid fa-arrow-left me-2" aria-hidden="true"></i>Back to Advertising Requests</a>
    </div>
    @if (session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
    <div class="card admin-settings-card mb-4"><div class="card-header"><h3>Request Details</h3></div><div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><strong>Name</strong><div>{{ $adRequest->name }}</div></div>
            <div class="col-md-4"><strong>Email</strong><div>{{ $adRequest->email }}</div></div>
            <div class="col-md-4"><strong>Phone</strong><div>{{ $adRequest->phone }}</div></div>
            <div class="col-md-4"><strong>Company</strong><div>{{ $adRequest->company ?: '—' }}</div></div>
            <div class="col-md-4"><strong>From / To</strong><div>{{ $adRequest->from_date->format('d M Y') }} — {{ $adRequest->to_date->format('d M Y') }}</div></div>
            <div class="col-md-4"><strong>Duration</strong><div>{{ $adRequest->durationDays() }} days</div></div>
            <div class="col-md-4"><strong>Status</strong><div>{{ str_replace('_', ' ', ucfirst($adRequest->status)) }}</div></div>
            <div class="col-md-4"><strong>Submitted</strong><div>{{ $adRequest->created_at?->format('d M Y, h:i A') }}</div></div>
            <div class="col-12"><strong>Details</strong><div style="white-space:pre-line">{{ $adRequest->details ?: '—' }}</div></div>
        </div>
        @can('ad-requests.edit')
            <div class="d-flex flex-wrap gap-2 mt-4">
                @foreach (['quote_sent' => 'Mark Quote Sent', 'rejected' => 'Reject', 'cancelled' => 'Cancel'] as $action => $label)
                    @if (($action === 'quote_sent' || $action === 'rejected') && $adRequest->status === 'pending' || $action === 'rejected' && $adRequest->status === 'quote_sent' || $action === 'cancelled' && in_array($adRequest->status, ['pending', 'quote_sent', 'confirmed'], true))
                        <form method="POST" action="{{ route('admin.advertising-requests.status', $adRequest) }}">@csrf<input type="hidden" name="action" value="{{ $action }}"><button class="btn btn-outline-primary" type="submit" data-action-confirm data-action-title="{{ match ($action) { 'quote_sent' => 'Send quote for this request?', 'rejected' => 'Reject this request?', 'cancelled' => 'Cancel this request?' } }}" data-action-text="{{ match ($action) { 'quote_sent' => 'The request and pending placements will be marked as quoted.', 'rejected' => 'The request will be rejected.', 'cancelled' => 'The request and eligible placements will be cancelled.' } }}" data-action-confirm-text="Yes, {{ strtolower($label) }}" data-action-icon="{{ $action === 'quote_sent' ? 'question' : 'warning' }}">{{ $label }}</button></form>
                    @endif
                @endforeach
            </div>
        @endcan
    </div></div>
    <div class="card admin-settings-card"><div class="card-header"><h3>Requested Placements</h3></div><div class="card-body"><div class="table-responsive"><table class="table table-hover align-middle w-100"><thead><tr><th>Page</th><th>Placement</th><th>Status</th><th>Availability</th><th>Linked Ad</th><th>Action</th></tr></thead><tbody>
        @foreach ($adRequest->placements as $placement)
            <tr>
                <td>{{ \App\Models\Ad::PAGE_PLACEMENTS[$placement->page_name]['label'] ?? 'Placement' }}</td>
                <td>{{ $placement->displayLabel() }}</td>
                <td><span class="badge text-bg-secondary">{{ ucfirst($placement->status) }}</span></td>
                <td>{{ $placement->ad ? 'Published as Ad' : (($availabilityByPlacement[$placement->id] ?? false) ? 'Available' : 'Conflict') }}</td>
                <td>
                    @if ($placement->ad)
                        #{{ $placement->ad->id }} — {{ $placement->ad->title }}<br>
                        {{ $placement->ad->start_date?->format('d M Y') }} — {{ $placement->ad->expiry_date?->format('d M Y') }}<br>
                        {{ $placement->ad->isActive ? 'Active' : 'Inactive' }} · {{ number_format($placement->ad->click_count) }} clicks
                        @can('ads.view') <a href="{{ route('admin.ads.show', $placement->ad) }}">View Ad</a> @endcan
                    @else — @endif
                </td>
                <td>
                    @can('ad-requests.edit')
                        @if (in_array($adRequest->status, ['pending', 'quote_sent', 'confirmed'], true) && in_array($placement->status, ['pending', 'quoted', 'conflicted'], true))
                            <form method="POST" action="{{ route('admin.advertising-requests.placements.confirm', [$adRequest, $placement]) }}">@csrf<button class="btn btn-sm btn-primary" type="submit" data-action-confirm data-action-title="Confirm this placement?" data-action-text="This will reserve the selected placement and date range if it is still available." data-action-confirm-text="Yes, confirm placement" data-action-icon="question">Confirm</button></form>
                        @endif
                    @endcan
                    @if ($placement->status === 'confirmed') @can('ads.create')<a class="btn btn-sm btn-outline-primary" href="{{ route('admin.ads.create', ['ad_request_placement_id' => $placement->id]) }}">Create Ad</a>@endcan @endif
                </td>
            </tr>
        @endforeach
    </tbody></table></div></div></div>
</div>
@endsection
