@extends('layouts.adminLayout.admin-design')

@section('title', 'FAQs')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">FAQs</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">FAQs</li>
                    </ol>
                </nav>
            </div>
            @can('faqs.create')
            <a class="btn btn-primary admin-primary-button" href="{{ route('admin.faq.create') }}">
                <i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Add FAQ
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
                                <th>Category</th>
                                <th>Question</th>
                                <th>Answer</th>
                                <th>Status</th>
                                @role('super-admin')
                                    <th>Created By</th>
                                @endrole
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($faqs as $faq)
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>{{ $faq->category?->name ?? '—' }}</td>
                                    <td title="{{ $faq->question }}">{{ str($faq->question)->limit(90) }}</td>
                                    <td title="{{ $faq->answer }}">{{ str($faq->answer)->limit(120) }}</td>
                                    <td>
                                        @if ($faq->isActive)
                                            <span class="badge text-bg-success">Active</span>
                                        @else
                                            <span class="badge text-bg-secondary">Deactive</span>
                                        @endif
                                    </td>
                                    @role('super-admin')
                                        <td>@include('admin.partials.created-by', ['record' => $faq])</td>
                                    @endrole
                                    <td>
                                        <div class="d-flex gap-2">
                                            @can('faqs.edit')
                                            <a class="btn btn-sm btn-outline-primary"
                                                href="{{ route('admin.faq.edit', ['id' => $faq->id]) }}"
                                                aria-label="Edit FAQ">
                                                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                            </a>
                                            @endcan
                                            @can('faqs.delete')
                                            <form method="POST"
                                                action="{{ route('admin.faq.destroy', ['id' => $faq->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit"
                                                    data-delete-confirm data-delete-title="Delete this FAQ?"
                                                    data-delete-text="This FAQ will be permanently deleted."
                                                    aria-label="Delete FAQ">
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
