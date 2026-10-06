@extends('layouts.adminLayout.admin-design')
@section('title', 'Categories')
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Categories</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Categories</li>
                    </ol>
                </nav>
            </div>@can('categories.create')<a class="btn btn-primary admin-primary-button" href="{{ route('admin.categories.create') }}"><i
                    class="fa-solid fa-plus me-2"></i>Add Category</a>
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
                                <th>Image</th>
                                <th>Name</th>
                                <th>Status</th>
                                @role('super-admin')
                                    <th>Created By</th>
                                @endrole
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($category->image)
                                            <img class="admin-table-thumbnail" src="{{ asset($category->image) }}"
                                            alt="{{ $category->name ?? 'Category' }}">@else<span
                                                class="text-muted">&mdash;</span>
                                        @endif
                                    </td>
                                    <td>{{ $category->name ?? '—' }}</td>
                                    <td><span
                                            class="badge {{ $category->isActive ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $category->isActive ? 'Active' : 'Deactive' }}</span>
                                    </td>
                                    @role('super-admin')
                                        <td>@include('admin.partials.created-by', ['record' => $category])</td>
                                    @endrole
                                    <td>
                                        <div class="d-flex gap-2">@can('categories.edit')<a class="btn btn-sm btn-outline-primary"
                                                href="{{ route('admin.categories.edit', ['id' => $category->id]) }}"
                                                aria-label="Edit Category"><i class="fa-solid fa-pen-to-square"></i></a>
                                            @endcan
                                            @can('categories.delete')
                                            <form method="POST"
                                                action="{{ route('admin.categories.destroy', ['id' => $category->id]) }}">
                                                @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"
                                                    type="submit" data-delete-confirm
                                                    data-delete-title="Delete this Category?"
                                                    data-delete-text="This category and its image will be permanently deleted."
                                                    aria-label="Delete Category"><i class="fa-solid fa-trash"></i></button>
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
