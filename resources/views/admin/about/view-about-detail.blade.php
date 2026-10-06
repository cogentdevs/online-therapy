@extends('layouts.adminLayout.admin-design')
@section('title', 'About Section Detail')
@section('content')
    @php($condition = $about->section_condition)
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="mb-1">About Section Detail</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.about.index') }}">About</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </nav>
            </div>
            @can('abouts.edit')
                <a class="btn btn-primary admin-primary-button" href="{{ route('admin.about.edit', ['id' => $about->id]) }}"><i
                        class="fa-solid fa-pen-to-square me-2"></i>Edit</a>
            @endcan
        </div>
        <div class="card admin-settings-card">
            <div class="card-header">
                <h3>{{ \App\Models\About::SECTION_LABELS[$condition] ?? 'About Section' }}</h3>
                <p>{{ $languageName ?? $about->language }} · {{ $about->is_active ? 'Active' : 'Deactive' }}</p>
            </div>
            <div class="card-body">
                @if ($condition === 4 && $about->image)
                    <img class="img-fluid rounded mb-3" src="{{ asset($about->image) }}" alt="About image">
                @endif
                @if (in_array($condition, [1, 3, 4, 5], true))
                    <h4>{{ $about->title ?: 'Untitled text' }}</h4>
                    <div class="article-content-preview text-break">{!! $about->description !!}</div>
                @endif
                @if ($condition === 5)
                    <hr>
                    <h4>{{ $about->title_2 ?: 'Untitled second text' }}</h4>
                    <div class="article-content-preview text-break">{!! $about->description_2 !!}</div>
                @endif
                @if (in_array($condition, [2, 3, 6], true) && $about->image)
                    <img class="img-fluid rounded mb-3" width="200" src="{{ asset($about->image) }}" alt="About image">
                @endif
                @if ($condition === 6 && $about->image_2)
                    <img class="img-fluid rounded mb-3" width="200" src="{{ asset($about->image_2) }}"
                        alt="About image 2">
                @endif
                @role('super-admin')
                    <hr>
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Created By</dt>
                        <dd class="col-sm-9">{{ $about->creator?->name ?? 'System / Legacy' }}</dd>
                        <dt class="col-sm-3">Updated By</dt>
                        <dd class="col-sm-9">{{ $about->updater?->name ?? 'System / Legacy' }}</dd>
                    </dl>
                @endrole
            </div>
        </div>
    </div>
@endsection
