@extends('layouts.adminLayout.admin-design')
@section('title', 'Subscription Plans')
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Subscription Plans</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Subscription Plans</li>
                    </ol>
                </nav>
            </div>
            @can('subscription-plans.create')
            <a class="btn btn-primary admin-primary-button" href="{{ route('admin.subscription-plan.create') }}"><i
                    class="fa-solid fa-plus me-2"></i>Add Subscription Plan</a>
            @endcan
        </div>
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="card admin-settings-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" data-admin-datatable data-admin-serials>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Subscription Type</th>
                                <th>Currency</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Discount</th>
                                <th>Status</th>
                                @role('super-admin')
                                    <th>Created By</th>
                                @endrole
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($plans as $plan)
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>{{ $plan->name }}</td>
                                    <td>{{ $plan->subscriptionTypes->first()?->name ?? '—' }}</td>
                                    <td>{{ $plan->currency ? trim(($plan->currency->code ?? '') . ' - ' . ($plan->currency->name ?? ''), ' -') : '—' }}
                                    </td>
                                    <td>{{ number_format((float) $plan->price, 2) }}</td>
                                    <td>{{ $plan->duration_value }} {{ ucfirst($plan->duration_unit ?? '') }}</td>
                                    <td>{{ $plan->discount_type ? ucfirst($plan->discount_type) . ': ' . number_format((float) $plan->discount_value, 2) . ($plan->discount_type === 'percentage' ? '%' : '') : '—' }}
                                    </td>
                                    <td><span
                                            class="badge {{ $plan->isActive ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $plan->isActive ? 'Active' : 'Deactive' }}</span>
                                    </td>
                                    @role('super-admin')
                                        <td>@include('admin.partials.created-by', ['record' => $plan])</td>
                                    @endrole
                                    <td>
                                        <div class="d-flex gap-2">@can('subscription-plans.edit')<a class="btn btn-sm btn-outline-primary"
                                                href="{{ route('admin.subscription-plan.edit', ['id' => $plan->id]) }}"
                                                aria-label="Edit Subscription Plan"><i
                                                    class="fa-solid fa-pen-to-square"></i></a>
                                            @endcan
                                            @can('subscription-plans.delete')
                                            <form method="POST"
                                                action="{{ route('admin.subscription-plan.destroy', ['id' => $plan->id]) }}">
                                                @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"
                                                    type="submit" data-delete-confirm
                                                    data-delete-title="Delete this Subscription Plan?"
                                                    data-delete-text="The plan and its type mapping will be permanently deleted."
                                                    aria-label="Delete Subscription Plan"><i
                                                        class="fa-solid fa-trash"></i></button></form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
