@extends('layouts.adminLayout.admin-design')
@section('title', 'Home Card Detail')
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="mb-1">Home Card Detail</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.home-cards.index') }}">Home Cards</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </nav>
            </div>
            @can('home-cards.edit')
                <a class="btn btn-primary admin-primary-button"
                    href="{{ route('admin.home-cards.edit', ['id' => $homeCard->id]) }}"><i
                        class="fa-solid fa-pen-to-square me-2"></i>Edit</a>
            @endcan
        </div>
        <div class="card admin-settings-card">
            <div class="card-header">
                <h3>{{ $homeCard->title ?: 'Untitled Home Card' }}</h3>
                <p>{{ $languageName ?? $homeCard->language }} ·
                    {{ \App\Models\HomeCard::POSITIONS[$homeCard->position] ?? $homeCard->position }}</p>
            </div>
            <div class="card-body">
                @if ($homeCard->image)
                    <img class="img-fluid rounded mb-4" width="200" src="{{ asset($homeCard->image) }}"
                        alt="{{ $homeCard->title ?: 'Home Card' }}">
                @endif
                <dl class="row mb-0">
                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $homeCard->description ?: '—' }}</dd>
                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">{{ $homeCard->is_active ? 'Active' : 'Deactive' }}</dd>
                    @role('super-admin')
                        <dt class="col-sm-3">Created By</dt>
                        <dd class="col-sm-9">{{ $homeCard->creator?->name ?? 'System / Legacy' }}</dd>
                        <dt class="col-sm-3">Updated By</dt>
                        <dd class="col-sm-9">{{ $homeCard->updater?->name ?? 'System / Legacy' }}</dd>
                    @endrole
                </dl>
            </div>
        </div>
    </div>
@endsection
