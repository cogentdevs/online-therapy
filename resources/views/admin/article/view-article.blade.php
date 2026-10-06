@extends('layouts.adminLayout.admin-design')
@section('title', 'Articles')
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="mb-1">Articles</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Articles</li>
                    </ol>
                </nav>
            </div>
            @can('articles.create')
                <a class="btn btn-primary admin-primary-button" href="{{ route('admin.article.create') }}"><i
                        class="fa-solid fa-plus me-2"></i>Add Article</a>
            @endcan
        </div>
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="card admin-settings-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" data-admin-datatable data-admin-serials>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Publish</th>
                                <th>Categories</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th>Free</th>
                                {{-- @role('super-admin') --}}
                                <th>Created By</th>
                                {{-- @endrole --}}
                                <th>
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($articles as $article)
                                <tr>
                                    <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($article->image)
                                            <img class="admin-table-thumbnail" src="{{ asset($article->image) }}"
                                            alt="{{ $article->title }} image">@else<span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $article->title ?? 'Untitled' }}</td>
                                    <td>{{ $article->publish_date?->format('d M Y') ?? '—' }}</td>
                                    <td>
                                        @forelse ($article->categories as $category)
                                            <span
                                            class="badge text-bg-light border me-1 mb-1">{{ $category->name ?? 'Untitled' }}</span>@empty<span
                                                class="text-muted">—</span>
                                        @endforelse
                                    </td>
                                    <td><span
                                            class="badge text-bg-{{ $article->status === 'published' ? 'success' : 'secondary' }}">{{ ucfirst($article->status ?? 'draft') }}</span>
                                    </td>
                                    <td>{{ $article->isFeatured ? 'Yes' : 'No' }}</td>
                                    <td>
                                        @if (!$article->isFree)
                                            <span class="badge text-bg-secondary">Paid</span>
                                        @elseif ($article->free_until)
                                            <span class="badge text-bg-success">Free</span>
                                            <small class="d-block text-muted mt-1">Until:
                                                {{ $article->free_until->format('d M Y') }}</small>
                                        @else
                                            <span class="badge text-bg-success">Permanent Free</span>
                                        @endif
                                    </td>
                                    {{-- @role('super-admin') --}}
                                    <td>@include('admin.partials.created-by', ['record' => $article])</td>
                                    {{-- @endrole --}}
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a class="btn btn-sm btn-outline-info"
                                                href="{{ route('admin.article.show', ['id' => $article->id]) }}"
                                                title="View Details"><i class="fa-solid fa-eye"></i></a>
                                            @can('articles.edit')
                                                <a class="btn btn-sm btn-outline-primary"
                                                    href="{{ route('admin.article.edit', ['id' => $article->id]) }}"
                                                    title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                            @endcan
                                            @can('articles.publish')
                                                @if ($article->status !== 'published')
                                                    <form method="POST"
                                                        action="{{ route('admin.article.publish', ['id' => $article->id]) }}">
                                                        @csrf<button class="btn btn-sm btn-outline-success" type="submit"
                                                            data-publish-confirm data-publish-title="Publish this Article?"
                                                            data-publish-text="Once published it will be eligible for frontend display."
                                                            title="Publish"><i class="fa-solid fa-upload"></i></button></form>
                                                @endif
                                            @endcan
                                            @can('articles.delete')
                                                <form method="POST"
                                                    action="{{ route('admin.article.destroy', ['id' => $article->id]) }}">@csrf
                                                    @method('DELETE')<button class="btn btn-sm btn-outline-danger"
                                                        type="submit" data-delete-confirm
                                                        data-delete-title="Delete this Article?" title="Delete"><i
                                                            class="fa-solid fa-trash"></i></button></form>
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
