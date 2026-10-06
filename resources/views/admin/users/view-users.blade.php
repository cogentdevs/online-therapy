@extends('layouts.adminLayout.admin-design')
@section('title', 'Admin Users')
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Admin Users</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Admin Users</li>
                    </ol>
                </nav>
            </div>
            @can('users.create')
                <a class="btn btn-primary admin-primary-button" href="{{ route('admin.users.create') }}">
                    <i class="fa-solid fa-user-plus me-2"></i>Add Admin User
                </a>
            @endcan
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
        @endif

        <div class="card admin-settings-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" data-admin-datatable data-admin-serials>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Assigned Role</th>
                                <th>Status</th>
                                <th>Created At</th>
                                @role('super-admin')
                                    <th>Created By</th>
                                @endrole
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $managedUser)
                                @php
                                    $isSuperAdmin = $managedUser->roles->contains('name', 'super-admin');
                                    $assignedRole = $isSuperAdmin
                                        ? $managedUser->roles->firstWhere('name', 'super-admin')
                                        : $managedUser->roles->first(fn ($role) => $role->permissions->contains('name', config('admin_modules.access_permission')));
                                @endphp
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>{{ $managedUser->name }}</td>
                                    <td>{{ $managedUser->email }}</td>
                                    <td>{{ $managedUser->phone ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $isSuperAdmin ? 'text-bg-dark' : 'text-bg-info' }}">
                                            {{ $isSuperAdmin ? 'System / Super Admin' : str($assignedRole?->name)->headline() }}
                                        </span>
                                    </td>
                                    <td><span class="badge {{ $managedUser->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $managedUser->is_active ? 'Active' : 'Deactive' }}
                                        </span></td>
                                    <td>{{ $managedUser->created_at?->format('d M Y') ?? '—' }}</td>
                                    @role('super-admin')
                                        <td>@include('admin.partials.created-by', ['record' => $managedUser])</td>
                                    @endrole
                                    <td>
                                        @if ($isSuperAdmin)
                                            <span class="text-muted"><i class="fa-solid fa-lock me-1"></i>Protected</span>
                                        @else
                                            <div class="d-flex flex-wrap gap-2">
                                                @can('users.edit')
                                                    <a class="btn btn-sm btn-outline-primary"
                                                        href="{{ route('admin.users.edit', ['id' => $managedUser->id]) }}"
                                                        aria-label="Edit {{ $managedUser->name }}">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @if ($transferableSourceIds->contains($managedUser->id))
                                                        <a class="btn btn-sm btn-outline-warning"
                                                            href="{{ route('admin.users.transfer-ownership', ['id' => $managedUser->id]) }}"
                                                            title="Transfer Content Ownership"
                                                            aria-label="Transfer content owned by {{ $managedUser->name }}">
                                                            <i class="fa-solid fa-right-left"></i>
                                                        </a>
                                                    @endif
                                                @endcan
                                                @can('users.delete')
                                                    @if (auth()->id() === $managedUser->id)
                                                        <button class="btn btn-sm btn-outline-secondary" type="button" disabled
                                                            title="You cannot delete your own Admin account.">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <form method="POST" action="{{ route('admin.users.destroy', ['id' => $managedUser->id]) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-outline-danger" type="submit"
                                                                data-delete-confirm data-delete-title="Delete this Admin User?"
                                                                data-delete-text="This Admin account and its role assignment will be removed."
                                                                aria-label="Delete {{ $managedUser->name }}">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                            </div>
                                        @endif
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
