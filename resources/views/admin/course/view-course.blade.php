@extends('layouts.adminLayout.admin-design')

@section('title', 'Courses')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div><h2 class="mb-1">Courses</h2></div>
            @can('courses.create')
                <a class="btn btn-primary admin-primary-button" href="{{ route('admin.course.create') }}"><i class="fa-solid fa-plus me-2"></i>Add Course</a>
            @endcan
        </div>
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="card admin-settings-card"><div class="card-body"><div class="table-responsive">
            <table class="table table-hover align-middle" data-admin-datatable data-admin-serials>
                <thead><tr><th>#</th><th>Image</th><th>Title</th><th>Duration</th><th>Visibility</th><th>Featured</th><th>Publication Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach ($courses as $course)
                        <tr>
                            <td data-admin-serial-value>{{ $loop->iteration }}</td>
                            <td>
                                @if ($course->image)
                                    <img class="admin-table-thumbnail" src="{{ asset($course->image) }}" alt="{{ $course->title }} image">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $course->title }}</td>
                            <td>{{ $course->duration_value }} {{ $course::DURATION_TYPES[$course->duration_type] ?? $course->duration_type }}</td>
                            <td><span class="badge text-bg-{{ $course->isActive ? 'success' : 'secondary' }}">{{ $course->isActive ? 'Active' : 'Deactive' }}</span></td>
                            <td>{{ $course->isFeatured ? 'Yes' : 'No' }}</td>
                            <td><span class="badge text-bg-{{ $course->status === \App\Models\Course::STATUS_PUBLISHED ? 'success' : 'secondary' }}">{{ ucfirst($course->status) }}</span></td>
                            <td><div class="d-flex flex-wrap gap-2">
                                <a class="btn btn-sm btn-outline-info" href="{{ route('admin.course.show', ['id' => $course->id]) }}"><i class="fa-solid fa-eye"></i></a>
                                @can('courses.edit')
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.course.edit', ['id' => $course->id]) }}"><i class="fa-solid fa-pen-to-square"></i></a>
                                @endcan
                                @can('courses.publish')
                                    @if ($course->status !== \App\Models\Course::STATUS_PUBLISHED)
                                        <form method="POST" action="{{ route('admin.course.publish', ['id' => $course->id]) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success" type="submit" data-publish-confirm><i class="fa-solid fa-upload"></i></button>
                                        </form>
                                    @endif
                                @endcan
                                @can('courses.delete')
                                    <form method="POST" action="{{ route('admin.course.destroy', ['id' => $course->id]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" data-delete-confirm><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                @endcan
                            </div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div></div></div>
    </div>
@endsection
