@extends('layouts.adminLayout.admin-design')
@section('title', 'Edit Course')
@section('content')
<div class="container-fluid"><div class="mb-4"><h2>Edit Course</h2></div><form method="POST" action="{{ route('admin.course.update', $course->id) }}" enctype="multipart/form-data">@csrf <section class="card admin-settings-card"><div class="card-header"><h3>Course Information</h3><p>Editing does not change publication state.</p></div><div class="card-body">@include('admin.course._form')</div></section><div class="d-flex justify-content-end gap-2 mt-3"><a class="btn btn-outline-secondary" href="{{ route('admin.course.index') }}">Cancel</a><button class="btn btn-primary admin-primary-button">Update Course</button></div></form></div>
@endsection
