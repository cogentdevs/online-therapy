@extends('layouts.adminLayout.admin-design')
@section('title', 'Manage Taza Shumara Articles')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div><h2 class="mb-1">Manage Taza Shumara Articles</h2><p class="text-muted mb-0">{{ $tazaShumara->magazine?->title ?: 'Untitled Magazine' }} · {{ strtoupper($tazaShumara->language) }}</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.taza-shumara.index') }}">Back to Taza Shumara</a>
    </div>
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div>@endif
    @can('taza-shumara.edit')
        <form method="POST" action="{{ route('admin.taza-shumara.articles.store', ['tazaShumara' => $tazaShumara->id]) }}">@csrf
            <div class="card admin-settings-card mb-4"><div class="card-header"><h3>Add Article Placement</h3><p>Articles are available independently of the selected Magazine.</p></div><div class="card-body">@include('admin.taza-shumara._article-fields', ['articlePlacement' => null])</div><div class="card-footer text-end"><button class="btn btn-primary admin-primary-button" type="submit"><i class="fa-solid fa-plus me-2"></i>Add Article</button></div></div>
        </form>
    @endcan
    <div class="card admin-settings-card"><div class="card-header"><h3>Configured Articles</h3><p>Placements are ordered by zone and sort order.</p></div><div class="card-body"><div class="table-responsive">
        <table class="table table-hover align-middle w-100" data-admin-datatable data-admin-serials><thead><tr><th>#</th><th>Article</th><th>Display Layout</th><th>Position</th><th>Sort Order</th><th>Actions</th></tr></thead><tbody>
            @foreach ($tazaShumara->articlePlacements as $placement)
                <tr><td data-admin-serial-value>{{ $loop->iteration }}</td><td>{{ $placement->article?->title ?: '—' }}</td><td>{{ $displayWidths[$placement->display_width] ?? $placement->display_width }}</td><td>{{ $positions[$placement->position] ?? $placement->position }}</td><td>{{ $placement->sort_order }}</td><td><div class="d-flex gap-2">@can('taza-shumara.edit')<a class="btn btn-sm btn-outline-primary" href="{{ route('admin.taza-shumara.articles.edit', ['tazaShumara' => $tazaShumara->id, 'placement' => $placement->id]) }}"><i class="fa-solid fa-pen-to-square"></i></a><form method="POST" action="{{ route('admin.taza-shumara.articles.destroy', ['tazaShumara' => $tazaShumara->id, 'placement' => $placement->id]) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit" data-delete-confirm data-delete-title="Delete this Article placement?"><i class="fa-solid fa-trash"></i></button></form>@endcan</div></td></tr>
            @endforeach
        </tbody></table>
    </div></div></div>
</div>
@endsection
