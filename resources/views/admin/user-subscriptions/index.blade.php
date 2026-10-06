@extends('layouts.adminLayout.admin-design')

@section('title', 'User Subscriptions')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="mb-1">User Subscriptions</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">User Subscriptions</li>
            </ol>
        </nav>
    </div>

    <div class="card admin-settings-card">
        <div class="card-header">
            <h3>Subscription History</h3>
            <p>Review Plan and Membership purchases, including pending payment submissions.</p>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-4" data-admin-datatable-filters="user-subscriptions-table">
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label" for="subscription-type-filter">Type</label>
                    <select class="form-select" id="subscription-type-filter" data-admin-column-filter="2">
                        <option value="">All types</option><option value="Plan">Plan</option><option value="Membership">Membership</option>
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label" for="subscription-payment-status-filter">Payment Status</label>
                    <select class="form-select" id="subscription-payment-status-filter" data-admin-column-filter="6">
                        <option value="">All payment statuses</option><option value="Pending">Pending</option><option value="Approved">Approved</option><option value="Rejected">Rejected</option><option value="Legacy">Legacy / Not Recorded</option>
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label" for="subscription-status-filter">Subscription Status</label>
                    <select class="form-select" id="subscription-status-filter" data-admin-column-filter="7">
                        <option value="">All statuses</option><option value="Pending">Pending</option><option value="Active">Active</option><option value="Scheduled">Scheduled</option><option value="Expired">Expired</option><option value="Rejected">Rejected</option><option value="Inactive">Inactive</option><option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100" type="button" data-admin-filters-reset>Reset Filters</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="user-subscriptions-table" data-admin-datatable data-admin-serials>
                    <thead><tr><th>#</th><th>User</th><th>Type</th><th>Product</th><th>Amount</th><th>Payment Method</th><th>Payment Status</th><th>Subscription Status</th><th>Submitted At</th><th>Payment Account</th><th>Action</th></tr></thead>
                    <tbody>
                        @foreach ($subscriptions as $subscription)
                            @php
                                $typeLabel = match ($subscription->product_for) { 'plan' => 'Plan', 'membership' => 'Membership', default => 'Subscription' };
                                $effectiveStatus = $subscription->frontend_effective_status;
                                $statusLabel = match ($effectiveStatus) { 'active' => 'Active', 'future' => 'Scheduled', 'expired' => 'Expired', 'inactive' => 'Inactive', 'cancelled', 'canceled' => 'Cancelled', default => ucfirst(str_replace('_', ' ', $effectiveStatus)) };
                                $statusClass = match ($effectiveStatus) { 'active' => 'text-bg-success', 'future' => 'text-bg-info', 'expired' => 'text-bg-secondary', 'cancelled', 'canceled' => 'text-bg-danger', default => 'text-bg-warning' };
                                $paymentStatus = $subscription->payment_status ?: 'legacy';
                                $paymentStatusClass = match ($paymentStatus) { 'approved' => 'text-bg-success', 'rejected' => 'text-bg-danger', 'pending' => 'text-bg-warning', default => 'text-bg-secondary' };
                                $currencyLabel = $subscription->currency?->symbol ?: $subscription->currency?->code;
                            @endphp
                            <tr>
                                <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                <td><strong>{{ $subscription->user?->name ?: '—' }}</strong><small class="d-block text-muted">{{ $subscription->user?->email ?: '—' }}</small></td>
                                <td>{{ $typeLabel }}</td>
                                <td>{{ $subscription->product_name ?: '—' }}</td>
                                <td>{{ $currencyLabel ? $currencyLabel.' ' : '' }}{{ $subscription->frontend_amount }}</td>
                                <td>{{ $subscription->frontend_payment_method }}</td>
                                <td><span class="badge {{ $paymentStatusClass }}">{{ ucfirst($paymentStatus === 'legacy' ? 'Legacy / Not Recorded' : $paymentStatus) }}</span></td>
                                <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                <td>{{ $subscription->payment_submitted_at?->format('d M Y, h:i A') ?: '—' }}</td>
                                <td><strong>{{ $subscription->paymentAccount?->bank_name ?: '—' }}</strong><small class="d-block text-muted">{{ $subscription->paymentAccount?->account_title ?: '' }}</small></td>
                                <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.user-subscriptions.show', $subscription) }}"><i class="fa-solid fa-eye me-1" aria-hidden="true"></i>View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
