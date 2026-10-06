@extends('layouts.adminLayout.admin-design')
@section('title', 'Edit FAQ Category')
@section('content')
<div class="container-fluid"><div class="mb-4"><h2 class="mb-1">Edit FAQ Category</h2><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.faq-categories.index') }}">FAQ Categories</a></li><li class="breadcrumb-item active">Edit</li></ol></nav></div>
@if ($errors->any())<div class="alert alert-danger">Please correct the highlighted fields.</div>@endif
<form method="POST" action="{{ route('admin.faq-categories.update', ['id' => $category->id]) }}" enctype="multipart/form-data">@csrf<section class="card admin-settings-card"><div class="card-header"><h3>FAQ Category Information</h3><p>Update the language, name, icon/image, and status.</p></div><div class="card-body">@include('admin.faq-category._form-fields')</div></section><div class="d-flex justify-content-end gap-2"><a class="btn btn-outline-secondary" href="{{ route('admin.faq-categories.index') }}">Cancel</a><button class="btn btn-primary admin-primary-button" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Update FAQ Category</button></div></form></div>
@endsection
