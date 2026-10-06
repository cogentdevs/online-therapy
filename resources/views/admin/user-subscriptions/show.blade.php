@extends('layouts.adminLayout.admin-design')

@section('title', 'User Subscription #'.$userSubscription->id)

@section('content')
@php
    $typeLabel = match ($userSubscription->product_for) { 'plan' => 'Plan', 'membership' => 'Membership', default => 'Subscription' };
    $effectiveStatus = $userSubscription->frontend_effective_status;
    $statusLabel = match ($effectiveStatus) { 'active' => 'Active', 'future' => 'Scheduled', 'expired' => 'Expired', 'inactive' => 'Inactive', 'cancelled', 'canceled' => 'Cancelled', default => ucfirst(str_replace('_', ' ', $effectiveStatus)) };
    $statusClass = match ($effectiveStatus) { 'active' => 'text-bg-success', 'future' => 'text-bg-info', 'expired' => 'text-bg-secondary', 'cancelled', 'canceled' => 'text-bg-danger', default => 'text-bg-warning' };
    $currency = $userSubscription->currency;
    $currencyPrefix = $currency?->symbol ?: $currency?->code;
    $amount = fn ($value) => is_numeric($value) ? ($currencyPrefix ? $currencyPrefix.' ' : '').number_format((float) $value, 2) : '—';
    $isPendingReview = $userSubscription->payment_status === \App\Models\UserSubscription::PAYMENT_STATUS_PENDING
        && $userSubscription->status === \App\Models\UserSubscription::STATUS_PENDING
        && ! $userSubscription->is_active;
    $paymentStatusClass = match ($userSubscription->payment_status) { 'approved' => 'text-bg-success', 'rejected' => 'text-bg-danger', 'pending' => 'text-bg-warning', default => 'text-bg-secondary' };
@endphp
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h2 class="mb-1">User Subscription #{{ $userSubscription->id }}</h2>
            <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.user-subscriptions.index') }}">User Subscriptions</a></li><li class="breadcrumb-item active" aria-current="page">Detail</li></ol></nav>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.user-subscriptions.index') }}"><i class="fa-solid fa-arrow-left me-2" aria-hidden="true"></i>Back to User Subscriptions</a>
    </div>

    @if (session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
    @if ($errors->has('subscription'))<div class="alert alert-danger" role="alert">{{ $errors->first('subscription') }}</div>@endif

    <section class="card admin-settings-card mb-4"><div class="card-header"><h3>User Information</h3></div><div class="card-body"><div class="row g-3">
        <div class="col-md-3"><strong>Name</strong><div>{{ $userSubscription->user?->name ?: '—' }}</div></div>
        <div class="col-md-3"><strong>Email</strong><div>{{ $userSubscription->user?->email ?: '—' }}</div></div>
        <div class="col-md-3"><strong>Phone</strong><div>{{ $userSubscription->user?->phone ?: '—' }}</div></div>
        <div class="col-md-3"><strong>User ID</strong><div>{{ $userSubscription->user_id ?: '—' }}</div></div>
    </div></div></section>

    <section class="card admin-settings-card mb-4"><div class="card-header"><h3>Subscription Information</h3></div><div class="card-body"><div class="row g-3">
        <div class="col-md-3"><strong>Subscription ID</strong><div>{{ $userSubscription->id }}</div></div>
        <div class="col-md-3"><strong>Type</strong><div>{{ $typeLabel }}</div></div>
        <div class="col-md-6"><strong>Product Snapshot</strong><div>{{ $userSubscription->product_name ?: '—' }}</div></div>
        <div class="col-md-3"><strong>Order No.</strong><div>{{ $userSubscription->order_no ?: '—' }}</div></div>
        <div class="col-md-3"><strong>Invoice No.</strong><div>{{ $userSubscription->invoice_no ?: '—' }}</div></div>
        <div class="col-12"><strong>Entitlements</strong><div class="d-flex flex-wrap gap-2 mt-1">
            @forelse ($userSubscription->subscriptionTypes as $type)
                <span class="badge text-bg-light border">{{ $type->name }}</span>
            @empty
                <span>—</span>
            @endforelse
        </div></div>
    </div></div></section>

    <section class="card admin-settings-card mb-4"><div class="card-header"><h3>Billing / Payment Information</h3></div><div class="card-body"><div class="row g-3">
        <div class="col-md-4"><strong>Currency</strong><div>{{ $currency ? trim(($currency->name ?: '').' '.($currency->code ? '('.$currency->code.')' : '')) : '—' }}</div></div>
        <div class="col-md-4"><strong>Stored Price</strong><div>{{ $amount($userSubscription->price) }}</div></div>
        <div class="col-md-4"><strong>Stored Discount</strong><div>{{ $amount($userSubscription->discount) }}</div></div>
        <div class="col-md-4"><strong>Stored Total</strong><div>{{ $amount($userSubscription->total) }}</div></div>
        <div class="col-md-4"><strong>Payment Method</strong><div>{{ $userSubscription->frontend_payment_method }}</div></div>
        <div class="col-md-4"><strong>Transaction ID</strong><div>{{ $userSubscription->transaction_id ?: '—' }}</div></div>
    </div></div></section>

    <section class="card admin-settings-card mb-4"><div class="card-header"><h3>Payment Review</h3><p>Private payment evidence and review status for this subscription.</p></div><div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-4"><strong>Payment Status</strong><div><span class="badge {{ $paymentStatusClass }}">{{ ucfirst($userSubscription->payment_status ?: 'Not Recorded') }}</span></div></div>
            <div class="col-md-4"><strong>Submitted At</strong><div>{{ $userSubscription->payment_submitted_at?->format('d M Y, h:i A') ?: '—' }}</div></div>
            <div class="col-md-4"><strong>Selected Account</strong><div>{{ $userSubscription->paymentAccount?->bank_name ?: '—' }}</div></div>
            <div class="col-md-4"><strong>Account Title</strong><div>{{ $userSubscription->paymentAccount?->account_title ?: '—' }}</div></div>
            <div class="col-md-4"><strong>Account Number</strong><div dir="ltr">{{ $userSubscription->paymentAccount?->account_no ?: '—' }}</div></div>
            <div class="col-md-4"><strong>IBAN</strong><div dir="ltr" class="text-break">{{ $userSubscription->paymentAccount?->iban ?: '—' }}</div></div>
            <div class="col-md-4"><strong>Branch Code</strong><div dir="ltr">{{ $userSubscription->paymentAccount?->branch_code ?: '—' }}</div></div>
            <div class="col-md-4"><strong>Reviewer</strong><div>{{ $userSubscription->reviewer?->name ?: '—' }}</div></div>
            <div class="col-md-4"><strong>Reviewed At</strong><div>{{ $userSubscription->reviewed_at?->format('d M Y, h:i A') ?: '—' }}</div></div>
            @if ($userSubscription->rejection_reason)
                <div class="col-12"><strong>Rejection Reason</strong><div class="border rounded bg-light p-3 mt-1">{{ $userSubscription->rejection_reason }}</div></div>
            @endif
        </div>

        @if ($userSubscription->payment_slip)
            @can('user-subscriptions.view')
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <a class="btn btn-outline-primary" href="{{ route('admin.user-subscriptions.payment-slip.view', $userSubscription) }}" target="_blank" rel="noopener"><i class="fa-solid fa-eye me-2" aria-hidden="true"></i>View Slip</a>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.user-subscriptions.payment-slip.download', $userSubscription) }}"><i class="fa-solid fa-download me-2" aria-hidden="true"></i>Download Slip</a>
                </div>
            @endcan
        @endif

        @if ($isPendingReview)
            <div class="row g-3">
                @can('user-subscriptions.approve')
                    <div class="col-lg-4">
                        <form method="POST" action="{{ route('admin.user-subscriptions.approve', $userSubscription) }}">
                            @csrf
                            <button class="btn btn-success w-100" type="submit" data-action-confirm data-action-title="Approve this payment?" data-action-text="The subscription will become active from today." data-action-confirm-text="Yes, Approve" data-action-icon="question"><i class="fa-solid fa-circle-check me-2" aria-hidden="true"></i>Approve Payment</button>
                        </form>
                    </div>
                @endcan
                @can('user-subscriptions.reject')
                    <div class="col-lg-8">
                        <form method="POST" action="{{ route('admin.user-subscriptions.reject', $userSubscription) }}">
                            @csrf
                            <label class="form-label" for="rejection_reason">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('rejection_reason') is-invalid @enderror" id="rejection_reason" name="rejection_reason" rows="3" maxlength="2000" required>{{ old('rejection_reason') }}</textarea>
                            @error('rejection_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <button class="btn btn-danger mt-2" type="submit" data-action-confirm data-action-title="Reject this payment?" data-action-text="The subscription request will remain inactive for audit history." data-action-confirm-text="Yes, Reject" data-action-icon="warning"><i class="fa-solid fa-circle-xmark me-2" aria-hidden="true"></i>Reject Payment</button>
                        </form>
                    </div>
                @endcan
            </div>
        @else
            <div class="alert alert-light border mb-0">This payment request is no longer awaiting review.</div>
        @endif
    </div></section>

    <section class="card admin-settings-card"><div class="card-header"><h3>Dates / Status</h3></div><div class="card-body"><div class="row g-3">
        <div class="col-md-4"><strong>Purchase Date</strong><div>{{ $userSubscription->created_at?->format('d M Y, h:i A') ?: '—' }}</div></div>
        <div class="col-md-4"><strong>Start Date</strong><div>{{ $userSubscription->start_date?->format('d M Y') ?: '—' }}</div></div>
        <div class="col-md-4"><strong>End Date</strong><div>{{ $userSubscription->end_date?->format('d M Y') ?: '—' }}</div></div>
        <div class="col-md-4"><strong>Effective Status</strong><div><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></div></div>
        <div class="col-md-4"><strong>Stored Status</strong><div>{{ $userSubscription->status ?: '—' }}</div></div>
        <div class="col-md-4"><strong>Active Flag</strong><div>{{ $userSubscription->is_active ? 'Yes' : 'No' }}</div></div>
    </div></div></section>
</div>
@endsection
