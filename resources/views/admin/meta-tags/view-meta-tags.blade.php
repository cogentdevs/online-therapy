@extends('layouts.adminLayout.admin-design')

@section('title', 'SEO / Meta Tags')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">SEO / Meta Tags</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">SEO / Meta Tags</li>
                    </ol>
                </nav>
            </div>
            @can('meta-tags.create')
            <a class="btn btn-primary admin-primary-button" href="{{ route('admin.meta-tags.create') }}">
                <i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Add Main Page SEO
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
                                <th>Type / Module</th>
                                <th>Title</th>
                                <th>Keywords</th>
                                <th>Description</th>
                                <th>Slug URL</th>
                                {{-- <th>Canonical URL</th> --}}
                                @role('super-admin')
                                    <th>Created By</th>
                                @endrole
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($metaTags as $metaTag)
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>{{ $metaTag->moduleLabel() }}</td>
                                    <td title="{{ $metaTag->title }}">{{ str($metaTag->title)->limit(45) ?: '—' }}</td>
                                    <td title="{{ $metaTag->keywords }}">{{ str($metaTag->keywords)->limit(60) ?: '—' }}
                                    </td>
                                    <td title="{{ $metaTag->description }}">
                                        {{ str($metaTag->description)->limit(80) ?: '—' }}</td>
                                    <td>{{ $metaTag->slug_url ?? '—' }}</td>
                                    {{-- <td title="{{ $metaTag->canonical_url }}">
                                        {{ str($metaTag->canonical_url)->limit(55) ?: '—' }}</td> --}}
                                    @role('super-admin')
                                        <td>@include('admin.partials.created-by', ['record' => $metaTag])</td>
                                    @endrole
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can('meta-tags.edit')
                                            <a class="btn btn-sm btn-outline-primary"
                                                href="{{ route('admin.meta-tags.edit', ['id' => $metaTag->id]) }}"
                                                aria-label="Edit Meta Tags">
                                                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                            </a>
                                            @endcan
                                            @can('meta-tags.delete')
                                            <form method="POST"
                                                action="{{ route('admin.meta-tags.destroy', ['id' => $metaTag->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit"
                                                    data-delete-confirm data-delete-title="Delete these Meta Tags?"
                                                    data-delete-text="This SEO record will be permanently deleted."
                                                    aria-label="Delete Meta Tags">
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
