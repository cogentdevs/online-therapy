@extends('layouts.adminLayout.admin-design')

@section('title', 'Services')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Services</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Services</li>
                    </ol>
                </nav>
            </div>
            @can('services.create')
                <a class="btn btn-primary admin-primary-button" href="{{ route('admin.services.create') }}"><i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Add Service</a>
            @endcan
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif

        <div class="card admin-settings-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" data-admin-datatable>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Description</th>
                                <th>Status</th>
                                @role('super-admin')<th>Created By</th>@endrole
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($services as $service)
                                <tr>
                                    <td>{{ $service->id }}</td>
                                    <td>{{ $service->name }}</td>
                                    <td><img class="admin-table-thumbnail" src="{{ asset($service->image) }}" alt="{{ $service->name }} image"></td>
                                    <td>{{ Illuminate\Support\Str::limit($service->description ?: '—', 100) }}</td>
                                    <td><span class="badge {{ $service->isactive ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $service->isactive ? 'Active' : 'Deactive' }}</span></td>
                                    @role('super-admin')
                                        <td>@include('admin.partials.created-by', ['record' => $service])</td>
                                    @endrole
                                    <td>{{ $service->created_at?->format('d M Y, h:i A') ?? '—' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can('services.edit')
                                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.services.edit', ['id' => $service->id]) }}" aria-label="Edit Service"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a>
                                            @endcan
                                            @can('services.delete')
                                                <form method="POST" action="{{ route('admin.services.destroy', ['id' => $service->id]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" type="submit" data-delete-confirm data-delete-title="Delete this Service?" data-delete-text="The Service and its image will be permanently deleted." aria-label="Delete Service"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
                                                </form>
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
