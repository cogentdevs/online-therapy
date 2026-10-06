@extends('layouts.adminLayout.admin-design')
@section('title', 'Home Cards')
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Home Cards</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Home Cards</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @can('home-cards.view')
                    <a class="btn btn-outline-primary" href="{{ route('admin.home-cards.card-title-position') }}"><i
                            class="fa-solid fa-align-left me-2"></i>Card Title Position</a>
                @endcan
                @can('home-cards.create')
                    <a class="btn btn-primary admin-primary-button" href="{{ route('admin.home-cards.create') }}"><i
                            class="fa-solid fa-plus me-2"></i>Add Home Card</a>
                @endcan
            </div>
        </div>
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <div class="card admin-settings-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" data-admin-datatable data-admin-serials>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Position</th>
                                <th>Title</th>
                                <th>Image</th>
                                <th>Status</th>
                                @role('super-admin')
                                    <th>Created By</th>
                                @endrole
                                <th>
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($homeCards as $homeCard)
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>{{ \App\Models\HomeCard::POSITIONS[$homeCard->position] ?? $homeCard->position }}
                                    </td>
                                    <td>{{ $homeCard->title ?: '—' }}</td>
                                    <td>
                                        @if ($homeCard->image)
                                            <img class="admin-table-image" width="100"
                                                src="{{ asset($homeCard->image) }}"
                                            alt="{{ $homeCard->title ?: 'Home Card' }}">@else<span class="text-muted">No
                                                image</span>
                                        @endif
                                    </td>
                                    <td><span
                                            class="badge {{ $homeCard->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $homeCard->is_active ? 'Active' : 'Deactive' }}</span>
                                    </td>
                                    @role('super-admin')
                                        <td>
                                            @if ($homeCard->creator)
                                                {{ $homeCard->creator->name }}
                                                <small
                                                class="d-block text-muted">({{ $homeCard->creator->roles->first()?->name ? \Illuminate\Support\Str::headline($homeCard->creator->roles->first()->name) : 'No Role' }})</small>@else<span
                                                    class="text-muted">System / Legacy</span>
                                            @endif
                                        </td>
                                    @endrole
                                    <td>
                                        <div class="d-flex flex-wrap gap-2"><a class="btn btn-sm btn-outline-secondary"
                                                href="{{ route('admin.home-cards.show', ['id' => $homeCard->id]) }}"
                                                aria-label="View Home Card"><i class="fa-solid fa-eye"></i></a>
                                            @can('home-cards.edit')
                                                <a class="btn btn-sm btn-outline-primary"
                                                    href="{{ route('admin.home-cards.edit', ['id' => $homeCard->id]) }}"
                                                    aria-label="Edit Home Card"><i class="fa-solid fa-pen-to-square"></i></a>
                                                @endcan @can('home-cards.delete')
                                                <form method="POST"
                                                    action="{{ route('admin.home-cards.destroy', ['id' => $homeCard->id]) }}">
                                                    @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"
                                                        type="submit" data-delete-confirm
                                                        data-delete-title="Delete this Home Card?"
                                                        aria-label="Delete Home Card"><i class="fa-solid fa-trash"></i></button>
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
