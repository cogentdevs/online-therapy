@extends('layouts.adminLayout.admin-design')
@section('title', 'Edit Article Placement')
@section('content')
<div class="container-fluid">
    <div class="mb-4"><h2 class="mb-1">Edit Article Placement</h2><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.taza-shumara.index') }}">Taza Shumara</a></li><li class="breadcrumb-item"><a href="{{ route('admin.taza-shumara.manage', ['tazaShumara' => $tazaShumara->id]) }}">Manage Articles</a></li><li class="breadcrumb-item active">Edit</li></ol></nav></div>
    @if ($errors->any())<div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div>@endif
    <form method="POST" action="{{ route('admin.taza-shumara.articles.update', ['tazaShumara' => $tazaShumara->id, 'placement' => $articlePlacement->id]) }}">@csrf
        <div class="card admin-settings-card"><div class="card-header"><h3>Article Placement</h3><p>Update layout, position, or sort order independently of the Magazine.</p></div><div class="card-body">@include('admin.taza-shumara._article-fields')</div></div>
        <div class="d-flex justify-content-end gap-2 mt-4"><a class="btn btn-outline-secondary" href="{{ route('admin.taza-shumara.manage', ['tazaShumara' => $tazaShumara->id]) }}">Cancel</a><button class="btn btn-primary admin-primary-button" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Update Placement</button></div>
    </form>
</div>
@endsection
