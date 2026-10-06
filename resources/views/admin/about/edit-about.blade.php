@extends('layouts.adminLayout.admin-design')
@section('title', 'Edit About Section')
@section('content')
<div class="container-fluid">
    <div class="mb-4"><h2 class="mb-1">Edit About Section</h2><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.about.index') }}">About</a></li><li class="breadcrumb-item active">Edit</li></ol></nav></div>
    @if ($errors->any())<div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div>@endif
    <form method="POST" action="{{ route('admin.about.update', ['id' => $about->id]) }}" enctype="multipart/form-data">@csrf
        <section class="card admin-settings-card"><div class="card-header"><h3>About Information</h3><p>Update the layout, content, images, and status.</p></div><div class="card-body">@include('admin.about._form-fields')</div></section>
        <div class="d-flex justify-content-end gap-2"><a class="btn btn-outline-secondary" href="{{ route('admin.about.index') }}">Cancel</a><button class="btn btn-primary admin-primary-button" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Update About Section</button></div>
    </form>
</div>
@endsection
