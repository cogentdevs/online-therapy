@extends('layouts.adminLayout.admin-design')
@section('title', 'Tags')
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Tags</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tags</li>
                    </ol>
                </nav>
            </div>
            @can('tags.create')
            <a class="btn btn-primary admin-primary-button" href="{{ route('admin.tags.create') }}"><i
                    class="fa-solid fa-plus me-2"></i>Add Tag</a>
            @endcan
        </div>
        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
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
                                <th>Status</th>
                                @role('super-admin')
                                    <th>Created By</th>
                                @endrole
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tags as $tag)
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>{{ $tag->name ?? '—' }}</td>
                                    <td><span
                                            class="badge {{ $tag->isActive ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $tag->isActive ? 'Active' : 'Deactive' }}</span>
                                    </td>
                                    @role('super-admin')
                                        <td>@include('admin.partials.created-by', ['record' => $tag])</td>
                                    @endrole
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can('tags.edit')
                                            <a class="btn btn-sm btn-outline-primary"
                                                href="{{ route('admin.tags.edit', ['id' => $tag->id]) }}"
                                                aria-label="Edit Tag"><i class="fa-solid fa-pen-to-square"></i></a>
                                            @endcan
                                            @can('tags.delete')
                                            <form method="POST"
                                                action="{{ route('admin.tags.destroy', ['id' => $tag->id]) }}">@csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit"
                                                    data-delete-confirm data-delete-title="Delete this Tag?"
                                                    data-delete-text="This tag will be permanently deleted."
                                                    aria-label="Delete Tag"><i class="fa-solid fa-trash"></i></button>
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
