@extends('layouts.adminLayout.admin-design')
@section('title', 'FAQ Categories')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div><h2 class="mb-1">FAQ Categories</h2><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">FAQ Categories</li></ol></nav></div>
        @can('faq-categories.create')<a class="btn btn-primary admin-primary-button" href="{{ route('admin.faq-categories.create') }}"><i class="fa-solid fa-plus me-2"></i>Add FAQ Category</a>@endcan
    </div>
    @if (session('status'))<div class="alert alert-success" role="alert">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
    <div class="card admin-settings-card"><div class="card-body"><div class="table-responsive">
        <table class="table table-hover align-middle w-100" data-admin-datatable data-admin-serials>
            <thead><tr><th>#</th><th>Name</th><th>Icon</th><th>FAQs</th><th>Status</th>@role('super-admin')<th>Created By</th>@endrole<th>Actions</th></tr></thead>
            <tbody>@foreach ($categories as $category)<tr>
                <td data-admin-serial-value>{{ $loop->iteration }}</td><td>{{ $category->name }}</td>
                <td>@if ($category->icon)<img src="{{ asset($category->icon) }}" alt="{{ $category->name }} icon" width="44" height="44" class="rounded border bg-light p-1" style="object-fit: contain;">@else<span class="text-muted">—</span>@endif</td>
                <td>{{ number_format($category->faqs_count) }}</td><td><span class="badge text-bg-{{ $category->isActive ? 'success' : 'secondary' }}">{{ $category->isActive ? 'Active' : 'Deactive' }}</span></td>
                @role('super-admin')<td>@include('admin.partials.created-by', ['record' => $category])</td>@endrole
                <td><div class="d-flex gap-2">@can('faq-categories.edit')<a class="btn btn-sm btn-outline-primary" href="{{ route('admin.faq-categories.edit', ['id' => $category->id]) }}" aria-label="Edit FAQ Category"><i class="fa-solid fa-pen-to-square"></i></a>@endcan @can('faq-categories.delete')<form method="POST" action="{{ route('admin.faq-categories.destroy', ['id' => $category->id]) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit" data-delete-confirm data-delete-title="Delete this FAQ Category?" data-delete-text="This category can only be deleted when no FAQs are attached." aria-label="Delete FAQ Category"><i class="fa-solid fa-trash"></i></button></form>@endcan</div></td>
            </tr>@endforeach</tbody>
        </table>
    </div></div></div>
</div>
@endsection
