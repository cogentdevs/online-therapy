@extends('layouts.adminLayout.admin-design')

@section('title', 'Site Analytics Detail')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div><h2 class="mb-1">{{ $context['name'] }}</h2><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.site-analytics.index', $filters) }}">Site Analytics</a></li><li class="breadcrumb-item active">{{ $context['typeLabel'] }} Detail</li></ol></nav></div>
            <div class="d-flex gap-2">
                @can('site-analytics.export')<a class="btn btn-outline-success" href="{{ route('admin.site-analytics.detail.export', ['type' => $type, 'identifier' => $identifier, ...$filters]) }}"><i class="fa-solid fa-file-excel me-2"></i>Export Excel</a>@endcan
                <a class="btn btn-outline-secondary" href="{{ route('admin.site-analytics.index', $filters) }}"><i class="fa-solid fa-arrow-left me-2"></i>Back</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            @foreach (['country' => ['MOST VISIT COUNTRY', 'fa-earth-asia'], 'city' => ['MOST VISIT CITY', 'fa-city'], 'region' => ['MOST VISIT REGION', 'fa-map-location-dot']] as $key => [$label, $icon])
                <div class="col-md-4"><article class="card admin-dashboard-summary-card h-100"><div class="card-body d-flex align-items-center gap-3"><span class="admin-dashboard-summary-icon"><i class="fa-solid {{ $icon }}"></i></span><div><small class="text-muted">{{ $label }}</small><h3 class="mb-1">{{ $context['summaries'][$key]['value'] }}</h3><span>{{ $context['summaries'][$key]['count'] }} Visits</span></div></div></article></div>
            @endforeach
        </div>

        <div class="card admin-settings-card">
            <div class="card-header"><h3>Visit Details</h3></div>
            <div class="card-body"><div class="table-responsive"><table class="table table-hover align-middle w-100" data-site-analytics-detail data-source-url="{{ route('admin.site-analytics.detail.data', ['type' => $type, 'identifier' => $identifier, ...$filters]) }}"><thead><tr><th>S No.</th><th>Visitor</th><th>Country</th><th>City</th><th>Region</th><th>Device</th><th>Browser</th><th>OS</th><th>Time Spent</th><th>Date Time</th></tr></thead></table></div></div>
        </div>
    </div>
@endsection
