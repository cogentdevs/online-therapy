@extends('layouts.adminLayout.admin-design')
@section('title', 'About')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div><h2 class="mb-1">About</h2><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">About</li></ol></nav></div>
        @can('abouts.create')<a class="btn btn-primary admin-primary-button" href="{{ route('admin.about.create') }}"><i class="fa-solid fa-plus me-2"></i>Add About Section</a>@endcan
    </div>
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger"><strong>Something went wrong.</strong><ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="card admin-settings-card"><div class="card-body"><div class="table-responsive">
        <table class="table table-hover align-middle w-100" data-admin-datatable data-admin-serials>
            <thead><tr><th>#</th><th>Section Condition</th><th>Status</th>@role('super-admin')<th>Created By</th>@endrole<th>Actions</th></tr></thead>
            <tbody>@foreach ($abouts as $about)<tr>
                <td data-admin-serial-value>{{ $loop->iteration }}</td>
                <td>{{ \App\Models\About::SECTION_LABELS[$about->section_condition] ?? 'Unknown' }}</td>
                <td><span class="badge {{ $about->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $about->is_active ? 'Active' : 'Deactive' }}</span></td>
                @role('super-admin')<td>@if ($about->creator){{ $about->creator->name }}<small class="d-block text-muted">({{ $about->creator->roles->first()?->name ? \Illuminate\Support\Str::headline($about->creator->roles->first()->name) : 'No Role' }})</small>@else<span class="text-muted">System / Legacy</span>@endif</td>@endrole
                <td><div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.about.show', ['id' => $about->id]) }}" aria-label="View About section"><i class="fa-solid fa-eye"></i></a>
                    @can('abouts.edit')<a class="btn btn-sm btn-outline-primary" href="{{ route('admin.about.edit', ['id' => $about->id]) }}" aria-label="Edit About section"><i class="fa-solid fa-pen-to-square"></i></a>@endcan
                    @can('abouts.delete')<form method="POST" action="{{ route('admin.about.destroy', ['id' => $about->id]) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit" data-delete-confirm data-delete-title="Delete this About section?" aria-label="Delete About section"><i class="fa-solid fa-trash"></i></button></form>@endcan
                </div></td>
            </tr>@endforeach</tbody>
        </table>
    </div></div></div>
</div>
@endsection
