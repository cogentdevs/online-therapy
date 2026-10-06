@extends('layouts.adminLayout.admin-design')

@section('title', 'Advertisement Details')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="mb-1">Advertisement Details</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.ads.index') }}">Advertisements</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.ads.index') }}">
                    <i class="fa-solid fa-arrow-left me-2" aria-hidden="true"></i>Back to Advertisements
                </a>
                @can('ads.edit')
                    <a class="btn btn-outline-primary" href="{{ route('admin.ads.edit', ['id' => $ad->id]) }}">
                        <i class="fa-solid fa-pen-to-square me-2" aria-hidden="true"></i>Edit
                    </a>
                @endcan
            </div>
        </div>

        <section class="card admin-settings-card">
            <div class="card-header">
                <h3>Advertisement Information</h3>
                <p>Complete targeting, content, schedule, and performance overview.</p>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @if ($ad->ad_image)
                        <div class="col-lg-3">
                            <img class="img-fluid rounded border" src="{{ asset($ad->ad_image) }}"
                                alt="{{ $ad->title }} advertisement">
                        </div>
                    @endif
                    <div class="{{ $ad->ad_image ? 'col-lg-9' : 'col-12' }}">
                        <div class="row g-3">
                            @foreach ([
                                'Title' => $ad->title,
                                'Language' => $ad->language ?: 'All Languages',
                                'Page' => $pagePlacements[$ad->page_name]['label'] ?? $ad->page_name,
                                'Place' => $pagePlacements[$ad->page_name]['places'][$ad->place] ?? $ad->place,
                                'Type' => filled($ad->google_ad_code) ? 'Google' : (filled($ad->ad_image) ? 'Image' : 'Draft'),
                                'Ad URL' => $ad->ad_url,
                                'Total Clicks' => number_format($ad->click_count),
                                'Start Date' => $ad->start_date?->format('d M Y'),
                                'Expiry Date' => $ad->expiry_date?->format('d M Y'),
                                'Status' => $ad->isActive ? 'Active' : 'Inactive',
                                'Created At' => $ad->created_at?->format('d M Y, h:i A'),
                                'Updated At' => $ad->updated_at?->format('d M Y, h:i A'),
                            ] as $label => $value)
                                <div class="col-md-6">
                                    <small class="text-muted d-block">{{ $label }}</small>
                                    <strong class="text-break">{{ $value ?: '—' }}</strong>
                                </div>
                            @endforeach
                            @role('super-admin')
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Created By</small>
                                    @include('admin.partials.created-by', ['record' => $ad])
                                </div>
                            @endrole
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="card admin-settings-card">
            <div class="card-header">
                <h3>Google Ad Code</h3>
                <p>The saved code is displayed as escaped text and is never executed in Admin.</p>
            </div>
            <div class="card-body">
                <pre class="border rounded bg-light p-3 mb-0 text-break"><code>{{ $ad->google_ad_code ?: '—' }}</code></pre>
            </div>
        </section>

        <div class="d-flex justify-content-end gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('admin.ads.index') }}">
                <i class="fa-solid fa-arrow-left me-2" aria-hidden="true"></i>Back to Advertisements
            </a>
        </div>
    </div>
@endsection
