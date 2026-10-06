@extends('layouts.adminLayout.admin-design')
@section('title', 'Magazines')
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="mb-1">Magazines</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Magazines</li>
                    </ol>
                </nav>
            </div>
            @can('magazines.create')
                <a class="btn btn-primary admin-primary-button" href="{{ route('admin.magazine.create') }}">
                    <i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Add Magazine
                </a>
            @endcan
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card admin-settings-card">
            <div class="card-header">
                <h3>Magazine Library</h3>
                <p>Manage drafts, publication state, relationships, and stored PDF copies.</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" data-admin-datatable data-admin-serials>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Issue</th>
                                <th>Publish Date</th>
                                <th>Categories</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th>Free</th>
                                <th>Download</th>
                                {{-- @role('super-admin') --}}
                                <th>Created By</th>
                                {{-- @endrole --}}
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($magazines as $magazine)
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($magazine->cover_image)
                                            <img class="admin-table-thumbnail" src="{{ asset($magazine->cover_image) }}"
                                                alt="{{ $magazine->title }} cover">
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $magazine->title ?? '—' }}</td>
                                    <td>{{ $magazine->issue_number ?? '—' }}</td>
                                    <td>{{ $magazine->publish_date?->format('d M Y') ?? '—' }}</td>
                                    <td>{{ $magazine->categories->pluck('name')->filter()->join(', ') ?: '—' }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $magazine->status === 'published' ? 'text-bg-success' : 'text-bg-warning' }}">
                                            {{ ucfirst($magazine->status ?? 'draft') }}
                                        </span>
                                    </td>
                                    <td><span
                                            class="badge {{ $magazine->isFeatured ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $magazine->isFeatured ? 'Yes' : 'No' }}</span></td>
                                    <td>
                                        @if (!$magazine->isFree)
                                            <span class="badge text-bg-secondary">Paid</span>
                                        @elseif ($magazine->free_until)
                                            <span class="badge text-bg-success">Free</span>
                                            <small class="d-block text-muted mt-1">Until:
                                                {{ $magazine->free_until->format('d M Y') }}</small>
                                        @else
                                            <span class="badge text-bg-success">Permanent Free</span>
                                        @endif
                                    </td>
                                    <td><span
                                            class="badge {{ $magazine->is_downloadable ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $magazine->is_downloadable ? 'Allowed' : 'View Only' }}</span></td>
                                    {{-- @role('super-admin') --}}
                                    <td>@include('admin.partials.created-by', ['record' => $magazine])</td>
                                    {{-- @endrole --}}
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a class="btn btn-sm btn-outline-secondary"
                                                href="{{ route('admin.magazine.show', ['id' => $magazine->id]) }}"
                                                aria-label="View Magazine details"><i class="fa-solid fa-eye"></i></a>
                                            @can('magazines.edit')
                                                <a class="btn btn-sm btn-outline-primary"
                                                    href="{{ route('admin.magazine.edit', ['id' => $magazine->id]) }}"
                                                    aria-label="Edit Magazine"><i class="fa-solid fa-pen-to-square"></i></a>
                                            @endcan
                                            @can('magazines.publish')
                                                @if ($magazine->status !== 'published')
                                                    <form method="POST"
                                                        action="{{ route('admin.magazine.publish', ['id' => $magazine->id]) }}">
                                                        @csrf
                                                        <button class="btn btn-sm btn-outline-success" type="submit"
                                                            data-publish-confirm aria-label="Publish Magazine">
                                                            <i class="fa-solid fa-upload"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                            @can('magazines.delete')
                                                <form method="POST"
                                                    action="{{ route('admin.magazine.destroy', ['id' => $magazine->id]) }}">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" type="submit"
                                                        data-delete-confirm data-delete-title="Delete this Magazine?"
                                                        aria-label="Delete Magazine"><i class="fa-solid fa-trash"></i></button>
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
