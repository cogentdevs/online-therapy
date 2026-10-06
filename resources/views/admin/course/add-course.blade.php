@extends('layouts.adminLayout.admin-design')
@section('title', 'Add Course')
@section('content')
<div class="container-fluid"><div class="mb-4"><h2>Add Course</h2><nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.course.index') }}">Courses</a></li><li class="breadcrumb-item active">Add</li></ol></nav></div><form method="POST" action="{{ route('admin.course.store') }}" enctype="multipart/form-data">@csrf <section class="card admin-settings-card"><div class="card-header"><h3>Course Information</h3><p>Create the Course as a draft.</p></div><div class="card-body">@include('admin.course._form')</div></section><div class="d-flex justify-content-end gap-2 mt-3"><a class="btn btn-outline-secondary" href="{{ route('admin.course.index') }}">Cancel</a><button class="btn btn-primary admin-primary-button">Add Course</button></div></form></div>
@endsection
