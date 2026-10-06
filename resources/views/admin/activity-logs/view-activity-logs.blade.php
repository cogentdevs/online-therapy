@extends('layouts.adminLayout.admin-design')

@section('title', 'Activity Logs')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Activity Logs</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Activity Logs</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-success" href="{{ route('admin.activity-logs.export.excel', request()->query()) }}">
                    <i class="fa-solid fa-file-excel me-2"></i>Excel
                </a>
                <a class="btn btn-outline-danger" href="{{ route('admin.activity-logs.export.pdf', request()->query()) }}">
                    <i class="fa-solid fa-file-pdf me-2"></i>PDF
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="card admin-settings-card mb-4">
            <div class="card-header"><h3>Filters</h3></div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.activity-logs.index') }}">
                    <div class="row g-3">
                        <div class="col-md-2"><label class="form-label" for="from_date">From Date</label><input class="form-control" id="from_date" name="from_date" type="date" value="{{ $filters['from_date'] ?? '' }}"></div>
                        <div class="col-md-2"><label class="form-label" for="to_date">To Date</label><input class="form-control" id="to_date" name="to_date" type="date" value="{{ $filters['to_date'] ?? '' }}"></div>
                        <div class="col-md-2"><label class="form-label" for="user_id">User</label><select class="form-select select2" id="user_id" name="user_id"><option value="">All Users</option>@foreach ($users as $user)<option value="{{ $user->user_id }}" @selected((string) ($filters['user_id'] ?? '') === (string) $user->user_id)>{{ $user->user_name }}</option>@endforeach</select></div>
                        <div class="col-md-2"><label class="form-label" for="role">Role</label><select class="form-select select2" id="role" name="role"><option value="">All Roles</option>@foreach ($roles as $role)<option value="{{ $role }}" @selected(($filters['role'] ?? '') === $role)>{{ str($role)->headline() }}</option>@endforeach</select></div>
                        <div class="col-md-2"><label class="form-label" for="module">Module</label><select class="form-select select2" id="module" name="module"><option value="">All Modules</option>@foreach ($modules as $module)<option value="{{ $module }}" @selected(($filters['module'] ?? '') === $module)>{{ str($module)->replace('_', ' ')->headline() }}</option>@endforeach</select></div>
                        <div class="col-md-2"><label class="form-label" for="action">Action</label><select class="form-select select2" id="action" name="action"><option value="">All Actions</option>@foreach ($actions as $action)<option value="{{ $action }}" @selected(($filters['action'] ?? '') === $action)>{{ str($action)->replace('_', ' ')->headline() }}</option>@endforeach</select></div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-primary admin-primary-button" type="submit"><i class="fa-solid fa-filter me-2"></i>Apply Filters</button>
                        <a class="btn btn-outline-secondary" href="{{ route('admin.activity-logs.index') }}">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card admin-settings-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100">
                        <thead><tr><th>Date/Time</th><th>User</th><th>Role</th><th>Module</th><th>Action</th><th>Description</th><th>Detail</th></tr></thead>
                        <tbody>
                            @forelse ($activityLogs as $activityLog)
                                <tr>
                                    <td>{{ $activityLog->created_at?->format('d M Y, h:i A') }}</td>
                                    <td>{{ $activityLog->user_name ?? 'System / Deleted User' }}</td>
                                    <td>{{ str($activityLog->role_name ?? 'Unknown')->headline() }}</td>
                                    <td>{{ str($activityLog->module)->replace('_', ' ')->headline() }}</td>
                                    <td><span class="badge text-bg-secondary">{{ str($activityLog->action)->replace('_', ' ')->headline() }}</span></td>
                                    <td>{{ str($activityLog->description)->limit(120) }}</td>
                                    <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.activity-logs.show', ['id' => $activityLog->id]) }}" aria-label="View activity detail"><i class="fa-solid fa-eye"></i></a></td>
                                </tr>
                            @empty
                                <tr><td class="text-center text-muted" colspan="7">No activity logs match the selected filters.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $activityLogs->links('pagination::bootstrap-5') }}</div>
            </div>
        </div>
    </div>
@endsection
