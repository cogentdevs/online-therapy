@extends('layouts.adminLayout.admin-design')

@section('title', 'Authors')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Authors</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Authors</li>
                    </ol>
                </nav>
            </div>
            @can('authors.create')
            <a class="btn btn-primary admin-primary-button" href="{{ route('admin.author.create') }}">
                <i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Add Author
            </a>
            @endcan
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
        @endif

        <div class="card admin-settings-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" data-admin-datatable data-admin-serials>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Picture</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                                <th>Speciality</th>
                                <th>Experience Years</th>
                                <th>Status</th>
                                @role('super-admin')
                                    <th>Created By</th>
                                @endrole
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($authors as $author)
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($author->picture)
                                            <img src="{{ asset($author->picture) }}" alt="{{ $author->name ?? 'Author' }}"
                                                class="admin-table-thumbnail">
                                        @else
                                            <span class="text-muted">&mdash;</span>
                                        @endif
                                    </td>
                                    <td title="{{ $author->name }}">{{ str($author->name)->limit(35) ?: '—' }}</td>
                                    <td title="{{ $author->email }}">{{ str($author->email)->limit(40) ?: '—' }}</td>
                                    <td>{{ $author->contact_number ?? '—' }}</td>
                                    <td title="{{ $author->speciality }}">{{ str($author->speciality)->limit(35) ?: '—' }}
                                    </td>
                                    <td>{{ $author->experience_years ?? '—' }}</td>
                                    <td>
                                        @if ($author->isActive)
                                            <span class="badge text-bg-success">Active</span>
                                        @else
                                            <span class="badge text-bg-secondary">Deactive</span>
                                        @endif
                                    </td>
                                    @role('super-admin')
                                        <td>@include('admin.partials.created-by', ['record' => $author])</td>
                                    @endrole
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can('authors.edit')
                                            <a class="btn btn-sm btn-outline-primary"
                                                href="{{ route('admin.author.edit', ['id' => $author->id]) }}"
                                                aria-label="Edit Author">
                                                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                            </a>
                                            @endcan
                                            @can('authors.delete')
                                            <form method="POST"
                                                action="{{ route('admin.author.destroy', ['id' => $author->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit"
                                                    data-delete-confirm data-delete-title="Delete this Author?"
                                                    data-delete-text="This author and its visibility settings will be permanently deleted."
                                                    aria-label="Delete Author">
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
