@extends('layouts.adminLayout.admin-design')
@section('title', 'Edit Home Card')
@section('content')
<div class="container-fluid">
    <div class="mb-4"><h2 class="mb-1">Edit Home Card</h2><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.home-cards.index') }}">Home Cards</a></li><li class="breadcrumb-item active">Edit</li></ol></nav></div>
    @if ($errors->any())<div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div>@endif
    <form method="POST" action="{{ route('admin.home-cards.update', ['id' => $homeCard->id]) }}" enctype="multipart/form-data">@csrf
        <div class="card admin-settings-card"><div class="card-header"><h3>Home Card Information</h3><p>Update the selected Home Card.</p></div><div class="card-body">@include('admin.home-cards._form-fields')</div></div>
        <div class="d-flex justify-content-end gap-2 mt-4"><a class="btn btn-outline-secondary" href="{{ route('admin.home-cards.index') }}">Cancel</a><button class="btn btn-primary admin-primary-button" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Update Home Card</button></div>
    </form>
</div>
@endsection
