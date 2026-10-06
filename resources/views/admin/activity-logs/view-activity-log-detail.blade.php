@extends('layouts.adminLayout.admin-design')

@section('title', 'Activity Log Detail')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div><h2 class="mb-1">Activity Log Detail</h2><p class="text-muted mb-0">Immutable Admin action record.</p></div>
            <a class="btn btn-outline-secondary" href="{{ url()->previous() }}"><i class="fa-solid fa-arrow-left me-2"></i>Back</a>
        </div>
        <div class="card admin-settings-card">
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4"><strong>Date/Time</strong><div>{{ $activityLog->created_at?->format('d M Y, h:i:s A') }}</div></div>
                    <div class="col-md-4"><strong>User</strong><div>{{ $activityLog->user_name ?? 'System / Deleted User' }}</div></div>
                    <div class="col-md-4"><strong>Role snapshot</strong><div>{{ str($activityLog->role_name ?? 'Unknown')->headline() }}</div></div>
                    <div class="col-md-4"><strong>Module</strong><div>{{ str($activityLog->module)->replace('_', ' ')->headline() }}</div></div>
                    <div class="col-md-4"><strong>Action</strong><div>{{ str($activityLog->action)->replace('_', ' ')->headline() }}</div></div>
                    <div class="col-md-4"><strong>IP address</strong><div>{{ $activityLog->ip_address ?? '—' }}</div></div>
                    <div class="col-md-6"><strong>Subject type</strong><div>{{ $activityLog->subject_type ?? '—' }}</div></div>
                    <div class="col-md-6"><strong>Subject ID</strong><div>{{ $activityLog->subject_id ?? '—' }}</div></div>
                    <div class="col-12"><strong>Description</strong><div>{{ $activityLog->description }}</div></div>
                    <div class="col-12"><strong>User agent</strong><div class="text-break">{{ $activityLog->user_agent ?? '—' }}</div></div>
                </div>
                <div class="row g-3">
                    <div class="col-lg-6"><h4>Old Values</h4><pre class="bg-light border rounded p-3 mb-0 text-wrap">{{ $activityLog->old_values === null ? 'None' : json_encode($activityLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre></div>
                    <div class="col-lg-6"><h4>New Values</h4><pre class="bg-light border rounded p-3 mb-0 text-wrap">{{ $activityLog->new_values === null ? 'None' : json_encode($activityLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre></div>
                </div>
            </div>
        </div>
    </div>
@endsection
