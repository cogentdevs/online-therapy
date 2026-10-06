@extends('layouts.adminLayout.admin-design')

@section('title', 'Site Analytics')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Site Analytics</h2>
                <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Site Analytics</li></ol></nav>
            </div>
            @can('site-analytics.export')
                <a class="btn btn-outline-success" href="{{ route('admin.site-analytics.export', $filters) }}"><i class="fa-solid fa-file-excel me-2"></i>Export Excel</a>
            @endcan
        </div>

        @if ($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

        <div class="card admin-settings-card mb-4">
            <div class="card-header"><h3>Filters</h3></div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.site-analytics.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-3"><label class="form-label" for="type">Analytics Type</label><select class="form-select select2" id="type" name="type">@foreach ($types as $key => $definition)<option value="{{ $key }}" @selected($filters['type'] === $key)>{{ $definition['label'] }}</option>@endforeach</select></div>
                        <div class="col-md-4 col-lg-3"><label class="form-label" for="from_date">From Date</label><input class="form-control" id="from_date" name="from_date" type="date" value="{{ $filters['from_date'] }}"></div>
                        <div class="col-md-4 col-lg-3"><label class="form-label" for="to_date">To Date</label><input class="form-control" id="to_date" name="to_date" type="date" value="{{ $filters['to_date'] }}"></div>
                        <div class="col-md-4 col-lg-3 d-flex flex-wrap flex-sm-nowrap gap-2"><button class="btn btn-primary admin-primary-button flex-grow-1" type="submit"><i class="fa-solid fa-filter me-2"></i>Filter</button><a class="btn btn-outline-secondary flex-grow-1" href="{{ route('admin.site-analytics.index') }}">Reset</a></div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card admin-settings-card">
            <div class="card-header"><h3>{{ $selectedType['label'] }} Analytics</h3></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" data-site-analytics-summary data-source-url="{{ route('admin.site-analytics.data', $filters) }}">
                        <thead><tr><th>S No.</th><th>{{ $selectedType['column_label'] }}</th><th>Visit Count</th><th>Last Visit From</th><th>Last Visit On</th><th>Action</th></tr></thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
