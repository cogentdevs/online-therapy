@extends('layouts.adminLayout.admin-design')

@section('title', 'Banners')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Banners</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Banners</li>
                    </ol>
                </nav>
            </div>
            @can('banners.create')
            <a class="btn btn-primary admin-primary-button" href="{{ route('admin.banner.create') }}">
                <i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Add Banner
            </a>
            @endcan
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Something went wrong.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card admin-settings-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" data-admin-datatable data-admin-serials>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Type</th>
                                <th>Position</th>
                                <th>Status</th>
                                @role('super-admin')
                                    <th>Created By</th>
                                @endrole
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($banners as $banner)
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if ($banner->image)
                                                <img class="admin-table-thumbnail" src="{{ asset($banner->image) }}"
                                                    alt="Banner {{ $banner->id }} image 1">
                                            @else
                                                <span class="text-muted">No image</span>
                                            @endif
                                            @if ($banner->type === 'side-by-side' && $banner->image_2)
                                                <img class="admin-table-thumbnail" src="{{ asset($banner->image_2) }}"
                                                    alt="Banner {{ $banner->id }} image 2">
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $banner->type === 'side-by-side' ? 'Side by side' : 'Full' }}</td>
                                    <td>{{ str($banner->position)->replace('-', ' ')->title() }}</td>
                                    <td>
                                        @if ($banner->isActive)
                                            <span class="badge text-bg-success">Active</span>
                                        @else
                                            <span class="badge text-bg-secondary">Deactive</span>
                                        @endif
                                    </td>
                                    @role('super-admin')
                                        <td>@include('admin.partials.created-by', ['record' => $banner])</td>
                                    @endrole
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can('banners.edit')
                                            <a class="btn btn-sm btn-outline-primary"
                                                href="{{ route('admin.banner.edit', ['id' => $banner->id]) }}"
                                                aria-label="Edit banner">
                                                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                            </a>
                                            @endcan
                                            @can('banners.delete')
                                            <form method="POST"
                                                action="{{ route('admin.banner.destroy', ['id' => $banner->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit"
                                                    data-delete-confirm data-delete-title="Delete this banner?"
                                                    data-delete-text="The banner and its images will be permanently deleted."
                                                    aria-label="Delete banner">
                                                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                                </button>
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
