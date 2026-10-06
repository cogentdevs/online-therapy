@extends('layouts.adminLayout.admin-design')
@section('title', 'Consultancy')
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="mb-1">Consultancy</h2>
                <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Consultancy</li></ol></nav>
            </div>
            @can('consultancies.create')
                <a class="btn btn-primary admin-primary-button" href="{{ route('admin.consultancy.create') }}"><i class="fa-solid fa-plus me-2"></i>Add Consultancy</a>
            @endcan
        </div>

        @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
        @if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        <div class="card admin-settings-card"><div class="card-body"><div class="table-responsive">
            <table class="table table-hover align-middle" data-admin-datatable data-admin-serials>
                <thead><tr><th>#</th><th>Image</th><th>Title</th><th>Duration</th><th>Medium</th><th>Visibility</th><th>Publication Status</th><th>Featured</th><th>Created By</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach ($consultancies as $consultancy)
                        <tr>
                            <td data-admin-serial-value>{{ $loop->iteration }}</td>
                            <td>@if ($consultancy->image)<img class="admin-table-thumbnail" src="{{ asset($consultancy->image) }}" alt="{{ $consultancy->title }} image">@else<span class="text-muted">—</span>@endif</td>
                            <td>{{ $consultancy->title }}</td>
                            <td>{{ $consultancy->duration_value }} {{ $consultancy::DURATION_TYPES[$consultancy->duration_type] ?? $consultancy->duration_type }}</td>
                            <td>{{ $consultancy::MEDIUMS[$consultancy->consultancy_medium] ?? $consultancy->consultancy_medium }}</td>
                            <td><span class="badge text-bg-{{ $consultancy->isActive ? 'success' : 'secondary' }}">{{ $consultancy->isActive ? 'Active' : 'Deactive' }}</span></td>
                            <td><span class="badge text-bg-{{ $consultancy->status === \App\Models\Consultancy::STATUS_PUBLISHED ? 'success' : 'secondary' }}">{{ ucfirst($consultancy->status) }}</span></td>
                            <td>{{ $consultancy->isFeatured ? 'Yes' : 'No' }}</td>
                            <td>@include('admin.partials.created-by', ['record' => $consultancy])</td>
                            <td><div class="d-flex flex-wrap gap-2">
                                <a class="btn btn-sm btn-outline-info" href="{{ route('admin.consultancy.show', ['id' => $consultancy->id]) }}" title="View Details"><i class="fa-solid fa-eye"></i></a>
                                @can('consultancies.edit')<a class="btn btn-sm btn-outline-primary" href="{{ route('admin.consultancy.edit', ['id' => $consultancy->id]) }}" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>@endcan
                                @can('consultancies.publish')
                                    @if ($consultancy->status !== \App\Models\Consultancy::STATUS_PUBLISHED)
                                        <form method="POST" action="{{ route('admin.consultancy.publish', ['id' => $consultancy->id]) }}">@csrf<button class="btn btn-sm btn-outline-success" type="submit" data-publish-confirm data-publish-title="Publish this Consultancy?" data-publish-text="Once published it will be eligible for future frontend display." title="Publish"><i class="fa-solid fa-upload"></i></button></form>
                                    @endif
                                @endcan
                                @can('consultancies.delete')
                                    <form method="POST" action="{{ route('admin.consultancy.destroy', ['id' => $consultancy->id]) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit" data-delete-confirm data-delete-title="Delete this Consultancy?" title="Delete"><i class="fa-solid fa-trash"></i></button></form>
                                @endcan
                            </div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div></div></div>
    </div>
@endsection
