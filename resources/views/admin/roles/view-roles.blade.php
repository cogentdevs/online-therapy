@extends('layouts.adminLayout.admin-design')
@section('title', 'Roles')
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Roles</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Roles</li>
                    </ol>
                </nav>
            </div>
            @can('roles.create')
                <a class="btn btn-primary admin-primary-button" href="{{ route('admin.roles.create') }}">
                    <i class="fa-solid fa-plus me-2"></i>Add Role
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
                                <th>Role Name</th>
                                <th>Type</th>
                                <th>Permissions</th>
                                <th>Assigned Users</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                @php($isSystemRole = in_array($role->name, $systemRoleNames, true))
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>{{ str($role->name)->headline() }}</td>
                                    <td><span class="badge {{ $isSystemRole ? 'text-bg-dark' : 'text-bg-info' }}">
                                            {{ $isSystemRole ? 'System' : 'Custom' }}
                                        </span></td>
                                    <td>{{ $role->permissions_count }}</td>
                                    <td>{{ $role->users_count }}</td>
                                    <td>
                                        @if ($isSystemRole)
                                            <span class="text-muted"><i class="fa-solid fa-lock me-1"></i>Protected</span>
                                        @else
                                            <div class="d-flex flex-wrap gap-2">
                                                @can('roles.edit')
                                                    <a class="btn btn-sm btn-outline-primary"
                                                        href="{{ route('admin.roles.edit', ['id' => $role->id]) }}"
                                                        aria-label="Edit {{ $role->name }} role">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                @endcan
                                                @can('roles.delete')
                                                    @if ($role->users_count > 0)
                                                        <button class="btn btn-sm btn-outline-secondary" type="button" disabled
                                                            title="This role is assigned to users and cannot be deleted.">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <form method="POST" action="{{ route('admin.roles.destroy', ['id' => $role->id]) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-outline-danger" type="submit"
                                                                data-delete-confirm data-delete-title="Delete this Role?"
                                                                data-delete-text="The role and its permission assignments will be removed."
                                                                aria-label="Delete {{ $role->name }} role">
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
