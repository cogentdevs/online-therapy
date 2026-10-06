@extends('layouts.adminLayout.admin-design')

@section('title', 'Videos')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1">Videos</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Videos</li>
                    </ol>
                </nav>
            </div>
            @can('videos.create')
                <a class="btn btn-primary admin-primary-button" href="{{ route('admin.video.create') }}"><i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Add Video</a>
            @endcan
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif

        <div class="card admin-settings-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" data-admin-datatable>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Thumbnail</th>
                                <th>Title</th>
                                <th>Video Link</th>
                                <th>Short Description</th>
                                <th>Access</th>
                                <th>Visibility</th>
                                <th>Publication Status</th>
                                <th>Share</th>
                                @role('super-admin')<th>Created By</th>@endrole
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($videos as $video)
                                <tr>
                                    <td>{{ $video->id }}</td>
                                    <td>
                                        @if ($video->thumbnail)
                                            <img class="admin-table-thumbnail" src="{{ asset($video->thumbnail) }}" alt="{{ $video->title }} thumbnail">
                                        @else
                                            <span class="text-muted">No thumbnail</span>
                                        @endif
                                    </td>
                                    <td>{{ $video->title }}</td>
                                    <td><a href="{{ $video->video_link }}" target="_blank" rel="noopener noreferrer">Open Video</a></td>
                                    <td>{{ Illuminate\Support\Str::limit($video->short_description ?: '—', 100) }}</td>
                                    <td><span class="badge {{ $video->is_free ? 'text-bg-success' : 'text-bg-warning' }}">{{ $video->is_free ? 'Free' : 'Paid' }}</span></td>
                                    <td><span class="badge {{ $video->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $video->is_active ? 'Active' : 'Deactive' }}</span></td>
                                    <td><span class="badge {{ $video->status === \App\Models\Video::STATUS_PUBLISHED ? 'text-bg-success' : 'text-bg-secondary' }}">{{ ucfirst($video->status ?? \App\Models\Video::STATUS_DRAFT) }}</span></td>
                                    <td><span class="badge {{ $video->is_share ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $video->is_share ? 'Enabled' : 'Disabled' }}</span></td>
                                    @role('super-admin')
                                        <td>@include('admin.partials.created-by', ['record' => $video])</td>
                                    @endrole
                                    <td>{{ $video->created_at?->format('d M Y, h:i A') ?? '—' }}</td>
                                    <td>
                                        <div class="d-flex flex-column align-items-center gap-2">
                                            @can('videos.edit')
                                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.video.edit', ['id' => $video->id]) }}" aria-label="Edit Video"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a>
                                            @endcan
                                            @can('videos.publish')
                                                @if ($video->status !== \App\Models\Video::STATUS_PUBLISHED)
                                                    <form method="POST" action="{{ route('admin.video.publish', ['id' => $video->id]) }}">
                                                        @csrf
                                                        <button class="btn btn-sm btn-outline-success" type="submit" data-publish-confirm data-publish-title="Publish this Video?" data-publish-text="Once published it will be eligible for frontend display." title="Publish" aria-label="Publish Video"><i class="fa-solid fa-upload" aria-hidden="true"></i></button>
                                                    </form>
                                                @endif
                                            @endcan
                                            @can('videos.delete')
                                                <form method="POST" action="{{ route('admin.video.destroy', ['id' => $video->id]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" type="submit" data-delete-confirm data-delete-title="Delete this Video?" data-delete-text="The Video and its thumbnail will be permanently deleted." aria-label="Delete Video"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
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
