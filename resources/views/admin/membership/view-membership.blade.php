@extends('layouts.adminLayout.admin-design')
@section('title', 'Memberships')
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Memberships</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Memberships</li>
                    </ol>
                </nav>
            </div>@can('memberships.create')<a class="btn btn-primary admin-primary-button" href="{{ route('admin.membership.create') }}"><i
                    class="fa-solid fa-plus me-2"></i>Add Membership</a>
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
                                <th>Subscription Types</th>
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
                            @foreach ($memberships as $membership)
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>{{ $membership->name }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($membership->subscriptionTypes as $type)
                                                <span class="badge text-bg-light border">{{ $type->name }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>{{ $membership->currency ? trim(($membership->currency->code ?? '') . ' - ' . ($membership->currency->name ?? ''), ' -') : '—' }}
                                    </td>
                                    <td>{{ number_format((float) $membership->price, 2) }}</td>
                                    <td>{{ $membership->duration_value }} {{ ucfirst($membership->duration_unit ?? '') }}
                                    </td>
                                    <td>{{ $membership->discount_type ? ucfirst($membership->discount_type) . ': ' . number_format((float) $membership->discount_value, 2) . ($membership->discount_type === 'percentage' ? '%' : '') : '—' }}
                                    </td>
                                    <td><span
                                            class="badge {{ $membership->isActive ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $membership->isActive ? 'Active' : 'Deactive' }}</span>
                                    </td>
                                    @role('super-admin')
                                        <td>@include('admin.partials.created-by', ['record' => $membership])</td>
                                    @endrole
                                    <td>
                                        <div class="d-flex gap-2">@can('memberships.edit')<a class="btn btn-sm btn-outline-primary"
                                                href="{{ route('admin.membership.edit', ['id' => $membership->id]) }}"
                                                aria-label="Edit Membership"><i class="fa-solid fa-pen-to-square"></i></a>
                                            @endcan
                                            @can('memberships.delete')
                                            <form method="POST"
                                                action="{{ route('admin.membership.destroy', ['id' => $membership->id]) }}">
                                                @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"
                                                    type="submit" data-delete-confirm
                                                    data-delete-title="Delete this Membership?"
                                                    data-delete-text="The membership and its type mappings will be permanently deleted."
                                                    aria-label="Delete Membership"><i
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
