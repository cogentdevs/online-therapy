@extends('layouts.adminLayout.admin-design')

@section('title', 'Advertising Requests')

@section('content')
<div class="container-fluid">
    <div class="mb-4"><h2 class="mb-1">Advertising Requests</h2><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active" aria-current="page">Advertising Requests</li></ol></nav></div>
    <div class="card admin-settings-card"><div class="card-header"><h3>Advertising Requests</h3><p>Review enquiries and manage each requested placement.</p></div><div class="card-body"><div class="table-responsive">
        <table class="table table-hover align-middle w-100" data-admin-datatable data-admin-serials>
            <thead><tr><th>#</th><th>Request No.</th><th>Name / User</th><th>Company</th><th>From</th><th>To</th><th>Placements</th><th>Status</th><th>Submitted</th><th>Action</th></tr></thead>
            <tbody>
                @foreach ($requests as $adRequest)
                    <tr>
                        <td data-admin-serial-value>{{ $loop->iteration }}</td>
                        <td>{{ $adRequest->request_no }}</td>
                        <td>{{ $adRequest->name }}<small class="d-block text-muted">{{ $adRequest->user?->name }}</small></td>
                        <td>{{ $adRequest->company ?: '—' }}</td>
                        <td>{{ $adRequest->from_date->format('d M Y') }}</td><td>{{ $adRequest->to_date->format('d M Y') }}</td>
                        <td>{{ $adRequest->placements_count }}</td>
                        <td><span class="badge text-bg-secondary">{{ str_replace('_', ' ', ucfirst($adRequest->status)) }}</span></td>
                        <td>{{ $adRequest->created_at?->format('d M Y, h:i A') }}</td>
                        <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.advertising-requests.show', $adRequest) }}">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div></div></div>
</div>
@endsection
