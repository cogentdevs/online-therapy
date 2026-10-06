@extends('layouts.adminLayout.admin-design')
@section('title', 'Taza Shumara')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div><h2 class="mb-1">Taza Shumara</h2><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Taza Shumara</li></ol></nav></div>
        @can('taza-shumara.create')<a class="btn btn-primary admin-primary-button" href="{{ route('admin.taza-shumara.create') }}"><i class="fa-solid fa-plus me-2"></i>Add Taza Shumara</a>@endcan
    </div>
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    <div class="card admin-settings-card"><div class="card-body"><div class="table-responsive">
        <table class="table table-hover align-middle w-100" data-admin-datatable data-admin-serials>
            <thead><tr><th>#</th><th>Magazine</th><th>Custom Cover</th><th>Articles</th><th>Status</th><th>Created At</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach ($tazaShumaras as $record)
                    <tr>
                        <td data-admin-serial-value>{{ $loop->iteration }}</td>
                        <td>{{ $record->magazine?->issue_number ?: '—' }} — {{ $record->magazine?->title ?: '—' }}</td>
                        <td>@if ($record->cover_image)<img class="admin-table-image" src="{{ asset($record->cover_image) }}" alt="Custom cover" width="70">@else<span class="text-muted">Magazine cover</span>@endif</td>
                        <td>{{ $record->article_placements_count }}</td>
                        <td><span class="badge {{ $record->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $record->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>{{ $record->created_at?->format('d M Y') }}</td>
                        <td><div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.taza-shumara.manage', ['tazaShumara' => $record->id]) }}" aria-label="Manage Taza Shumara"><i class="fa-solid fa-list-check"></i></a>
                            @can('taza-shumara.edit')<a class="btn btn-sm btn-outline-primary" href="{{ route('admin.taza-shumara.edit', ['tazaShumara' => $record->id]) }}" aria-label="Edit Taza Shumara"><i class="fa-solid fa-pen-to-square"></i></a>@endcan
                            @can('taza-shumara.delete')<form method="POST" action="{{ route('admin.taza-shumara.destroy', ['tazaShumara' => $record->id]) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit" data-delete-confirm data-delete-title="Delete this Taza Shumara?" aria-label="Delete Taza Shumara"><i class="fa-solid fa-trash"></i></button></form>@endcan
                        </div></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div></div></div>
</div>
@endsection
