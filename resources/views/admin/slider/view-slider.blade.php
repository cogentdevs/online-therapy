@extends('layouts.adminLayout.admin-design')

@section('title', 'Sliders')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Sliders</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sliders</li>
                    </ol>
                </nav>
            </div>
            @can('sliders.create')
            <a class="btn btn-primary admin-primary-button" href="{{ route('admin.slider.create') }}">
                <i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Add Slider
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
                                <th>Content Position</th>
                                <th>Top Heading</th>
                                <th>Main Heading</th>
                                <th>Button Label</th>
                                <th>Status</th>
                                @role('super-admin')
                                    <th>Created By</th>
                                @endrole
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sliders as $slider)
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($slider->image)
                                            <img class="admin-table-thumbnail" src="{{ asset($slider->image) }}"
                                                alt="Slider {{ $slider->id }} thumbnail">
                                        @else
                                            <span class="text-muted">No image</span>
                                        @endif
                                    </td>
                                    <td>{{ $slider->content_position ? ucfirst($slider->content_position) : '—' }}</td>
                                    <td>{{ $slider->top_heading ?? '—' }}</td>
                                    <td>{{ $slider->main_heading ?? '—' }}</td>
                                    <td>{{ $slider->button_label ?? '—' }}</td>
                                    <td>
                                        @if ($slider->isActive)
                                            <span class="badge text-bg-success">Active</span>
                                        @else
                                            <span class="badge text-bg-secondary">Deactive</span>
                                        @endif
                                    </td>
                                    @role('super-admin')
                                        <td>@include('admin.partials.created-by', ['record' => $slider])</td>
                                    @endrole
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can('sliders.edit')
                                            <a class="btn btn-sm btn-outline-primary"
                                                href="{{ route('admin.slider.edit', ['id' => $slider->id]) }}"
                                                aria-label="Edit slider">
                                                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                            </a>
                                            @endcan
                                            @can('sliders.delete')
                                            <form method="POST"
                                                action="{{ route('admin.slider.destroy', ['id' => $slider->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit"
                                                    data-delete-confirm data-delete-title="Delete this slider?"
                                                    data-delete-text="The slider and its image will be permanently deleted."
                                                    aria-label="Delete slider">
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
