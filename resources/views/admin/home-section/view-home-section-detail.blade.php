@extends('layouts.adminLayout.admin-design')
@section('title', 'Home Section Detail')
@section('content')
    @php($condition = $homeSection->section_condition)
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="mb-1">Home Section Detail</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.home-sections.index') }}">Home Sections</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </nav>
            </div>
            @can('home-sections.edit')
                <a class="btn btn-primary admin-primary-button"
                    href="{{ route('admin.home-sections.edit', ['id' => $homeSection->id]) }}"><i
                        class="fa-solid fa-pen-to-square me-2"></i>Edit</a>
            @endcan
        </div>
        <div class="card admin-settings-card">
            <div class="card-header">
                <h3>{{ \App\Models\HomeSection::SECTION_LABELS[$condition] ?? 'Home Section' }}</h3>
                <p>{{ $languageName ?? $homeSection->language }} ·
                    {{ \App\Models\HomeSection::POSITIONS[$homeSection->position] ?? $homeSection->position }} ·
                    {{ $homeSection->is_active ? 'Active' : 'Deactive' }}</p>
            </div>
            <div class="card-body">
                @if ($condition === 4 && $homeSection->image)
                    <img class="img-fluid rounded mb-3" src="{{ asset($homeSection->image) }}" alt="Home Section image">
                @endif
                @if (in_array($condition, [1, 3, 4, 5], true))
                    <h4>{{ $homeSection->title ?: 'Untitled text' }}</h4>
                    <div class="article-content-preview text-break">{!! $homeSection->description !!}</div>
                @endif
                @if ($condition === 5)
                    <hr>
                    <h4>{{ $homeSection->title_2 ?: 'Untitled second text' }}</h4>
                    <div class="article-content-preview text-break">{!! $homeSection->description_2 !!}</div>
                @endif
                @if (in_array($condition, [2, 3, 6], true) && $homeSection->image)
                    <img class="img-fluid rounded mb-3" width="200" src="{{ asset($homeSection->image) }}"
                        alt="Home Section image">
                @endif
                @if ($condition === 6 && $homeSection->image_2)
                    <img class="img-fluid rounded mb-3" width="200" src="{{ asset($homeSection->image_2) }}"
                        alt="Home Section image 2">
                @endif
                @role('super-admin')
                    <hr>
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Created By</dt>
                        <dd class="col-sm-9">{{ $homeSection->creator?->name ?? 'System / Legacy' }}</dd>
                        <dt class="col-sm-3">Updated By</dt>
                        <dd class="col-sm-9">{{ $homeSection->updater?->name ?? 'System / Legacy' }}</dd>
                    </dl>
                @endrole
            </div>
        </div>
    </div>
@endsection
