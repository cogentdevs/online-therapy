@extends('layouts.adminLayout.admin-design')
@section('title', 'Currencies')
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Currencies</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Currencies</li>
                    </ol>
                </nav>
            </div>
            @can('currency.create')
            <a class="btn btn-primary admin-primary-button" href="{{ route('admin.currency.create') }}"><i
                    class="fa-solid fa-plus me-2"></i>Add Currency</a>
            @endcan
        </div>
        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
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
                                <th>Name</th>
                                <th>Code</th>
                                <th>Symbol</th>
                                <th>Status</th>
                                @role('super-admin')
                                    <th>Created By</th>
                                @endrole
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($currencies as $currency)
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>{{ $currency->name ?? '—' }}</td>
                                    <td>{{ $currency->code ?? '—' }}</td>
                                    <td>{{ $currency->symbol ?? '—' }}</td>
                                    <td><span
                                            class="badge {{ $currency->isActive ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $currency->isActive ? 'Active' : 'Deactive' }}</span>
                                    </td>
                                    @role('super-admin')
                                        <td>@include('admin.partials.created-by', ['record' => $currency])</td>
                                    @endrole
                                    <td>
                                        <div class="d-flex gap-2">@can('currency.edit')<a class="btn btn-sm btn-outline-primary"
                                                href="{{ route('admin.currency.edit', ['id' => $currency->id]) }}"
                                                aria-label="Edit Currency"><i class="fa-solid fa-pen-to-square"></i></a>
                                            @endcan
                                            @can('currency.delete')
                                            <form method="POST"
                                                action="{{ route('admin.currency.destroy', ['id' => $currency->id]) }}">
                                                @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"
                                                    type="submit" data-delete-confirm
                                                    data-delete-title="Delete this Currency?"
                                                    data-delete-text="This currency will be permanently deleted."
                                                    aria-label="Delete Currency"><i class="fa-solid fa-trash"></i></button>
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
