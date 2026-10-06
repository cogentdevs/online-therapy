@extends('layouts.adminLayout.admin-design')
@section('title', 'Home Sections')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4"><div><h2 class="mb-1">Home Sections</h2><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Home Sections</li></ol></nav></div>@can('home-sections.create')<a class="btn btn-primary admin-primary-button" href="{{ route('admin.home-sections.create') }}"><i class="fa-solid fa-plus me-2"></i>Add Home Section</a>@endcan</div>
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger"><strong>Something went wrong.</strong><ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="card admin-settings-card"><div class="card-body"><div class="table-responsive">
        <table class="table table-hover align-middle w-100" data-admin-datatable data-admin-serials>
            <thead><tr><th>#</th><th>Position</th><th>Section Condition</th><th>Title</th><th>Status</th>@role('super-admin')<th>Created By</th>@endrole<th>Actions</th></tr></thead>
            <tbody>@foreach ($homeSections as $homeSection)<tr>
                <td data-admin-serial-value>{{ $loop->iteration }}</td>
                <td>{{ \App\Models\HomeSection::POSITIONS[$homeSection->position] ?? $homeSection->position }}</td>
                <td>{{ \App\Models\HomeSection::SECTION_LABELS[$homeSection->section_condition] ?? 'Unknown' }}</td>
                <td>{{ in_array($homeSection->section_condition, [1, 3, 4, 5], true) ? ($homeSection->title ?: '—') : '—' }}</td>
                <td><span class="badge {{ $homeSection->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $homeSection->is_active ? 'Active' : 'Deactive' }}</span></td>
                @role('super-admin')<td>@if ($homeSection->creator){{ $homeSection->creator->name }}<small class="d-block text-muted">({{ $homeSection->creator->roles->first()?->name ? \Illuminate\Support\Str::headline($homeSection->creator->roles->first()->name) : 'No Role' }})</small>@else<span class="text-muted">System / Legacy</span>@endif</td>@endrole
                <td><div class="d-flex flex-wrap gap-2"><a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.home-sections.show', ['id' => $homeSection->id]) }}" aria-label="View Home Section"><i class="fa-solid fa-eye"></i></a>@can('home-sections.edit')<a class="btn btn-sm btn-outline-primary" href="{{ route('admin.home-sections.edit', ['id' => $homeSection->id]) }}" aria-label="Edit Home Section"><i class="fa-solid fa-pen-to-square"></i></a>@endcan @can('home-sections.delete')<form method="POST" action="{{ route('admin.home-sections.destroy', ['id' => $homeSection->id]) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit" data-delete-confirm data-delete-title="Delete this Home Section?" aria-label="Delete Home Section"><i class="fa-solid fa-trash"></i></button></form>@endcan</div></td>
            </tr>@endforeach</tbody>
        </table>
    </div></div></div>
</div>
@endsection
