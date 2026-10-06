@extends('layouts.adminLayout.admin-design')

@section('title', 'Advertisements')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Advertisements</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Advertisements</li>
                    </ol>
                </nav>
            </div>
            @can('ads.create')
                <a class="btn btn-primary admin-primary-button" href="{{ route('admin.ads.create') }}">
                    <i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Add Advertisement
                </a>
            @endcan
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Something went wrong.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card admin-settings-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" data-admin-datatable data-admin-serials>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Page</th>
                                <th>Place</th>
                                <th>Type</th>
                                <th>Clicks</th>
                                <th>Start</th>
                                <th>Expiry</th>
                                <th>Status</th>
                                @role('super-admin')<th>Created By</th>@endrole
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ads as $ad)
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>{{ $ad->title }}</td>
                                    <td>{{ $pagePlacements[$ad->page_name]['label'] ?? $ad->page_name }}</td>
                                    <td>{{ $pagePlacements[$ad->page_name]['places'][$ad->place] ?? $ad->place }}</td>
                                    <td>{{ filled($ad->google_ad_code) ? 'Google' : (filled($ad->ad_image) ? 'Image' : 'Draft') }}</td>
                                    <td>{{ number_format($ad->click_count) }}</td>
                                    <td>{{ $ad->start_date?->format('d M Y') ?? '—' }}</td>
                                    <td>{{ $ad->expiry_date?->format('d M Y') ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $ad->isActive ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $ad->isActive ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    @role('super-admin')
                                        <td>@include('admin.partials.created-by', ['record' => $ad])</td>
                                    @endrole
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can('ads.view')
                                                <a class="btn btn-sm btn-outline-secondary"
                                                    href="{{ route('admin.ads.show', ['id' => $ad->id]) }}"
                                                    aria-label="View advertisement">
                                                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                                </a>
                                            @endcan
                                            @can('ads.edit')
                                                <a class="btn btn-sm btn-outline-primary"
                                                    href="{{ route('admin.ads.edit', ['id' => $ad->id]) }}"
                                                    aria-label="Edit advertisement">
                                                    <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                                </a>
                                            @endcan
                                            @can('ads.delete')
                                                <form method="POST" action="{{ route('admin.ads.destroy', ['id' => $ad->id]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" type="submit"
                                                        data-delete-confirm data-delete-title="Delete this advertisement?"
                                                        data-delete-text="The advertisement and its click history will be permanently deleted."
                                                        aria-label="Delete advertisement">
                                                        <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
