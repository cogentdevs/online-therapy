@extends('layouts.adminLayout.admin-design')
@section('title', 'Add Taza Shumara')
@section('content')
<div class="container-fluid">
    <div class="mb-4"><h2 class="mb-1">Add Taza Shumara</h2><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.taza-shumara.index') }}">Taza Shumara</a></li><li class="breadcrumb-item active">Add</li></ol></nav></div>
    @if ($errors->any())<div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div>@endif
    <form method="POST" action="{{ route('admin.taza-shumara.store') }}" enctype="multipart/form-data">@csrf
        <div class="card admin-settings-card"><div class="card-header"><h3>Taza Shumara Information</h3><p>Select a Magazine and configure its display options.</p></div><div class="card-body">@include('admin.taza-shumara._form-fields')</div></div>
        <div class="card admin-settings-card mt-4"><div class="card-header"><h3>Taza Shumara Articles</h3><p>Select overall available Articles and configure each placement.</p></div><div class="card-body">@include('admin.taza-shumara._articles-form')</div></div>
        <div class="d-flex justify-content-end gap-2 mt-4"><a class="btn btn-outline-secondary" href="{{ route('admin.taza-shumara.index') }}">Cancel</a><button class="btn btn-primary admin-primary-button" type="submit"><i class="fa-solid fa-plus me-2"></i>Add Taza Shumara</button></div>
    </form>
</div>
@endsection
