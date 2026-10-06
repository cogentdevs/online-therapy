@extends('layouts.adminLayout.admin-design')
@section('title', 'Magazine Details')
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div><h2 class="mb-1">Magazine Details</h2><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.magazine.index') }}">Magazines</a></li><li class="breadcrumb-item active">Details</li></ol></nav></div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.magazine.index') }}"><i class="fa-solid fa-arrow-left me-2"></i>Back to Magazines</a>
                @can('magazines.edit')<a class="btn btn-outline-primary" href="{{ route('admin.magazine.edit', ['id' => $magazine->id]) }}"><i class="fa-solid fa-pen-to-square me-2"></i>Edit</a>@endcan
                @can('magazines.publish')
                @if ($magazine->status !== 'published')
                    <form method="POST" action="{{ route('admin.magazine.publish', ['id' => $magazine->id]) }}">@csrf<button class="btn btn-success" type="submit" data-publish-confirm><i class="fa-solid fa-upload me-2"></i>Publish</button></form>
                @endif
                @endcan
            </div>
        </div>
        @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
        @if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        <section class="card admin-settings-card">
            <div class="card-header"><h3>Main Magazine Information</h3><p>Complete editorial and publication overview.</p></div>
            <div class="card-body"><div class="row g-4">
                <div class="col-lg-3">@if ($magazine->cover_image)<img class="img-fluid rounded border" src="{{ asset($magazine->cover_image) }}" alt="{{ $magazine->title }} cover">@else<span class="text-muted">No cover image</span>@endif</div>
                <div class="col-lg-9"><div class="row g-3">
                    @foreach ([
                        'Title' => $magazine->title,
                        'Language' => $languageName ?? $magazine->language,
                        'Issue Number' => $magazine->issue_number,
                        'Publish Date' => $magazine->publish_date?->format('d M Y'),
                        'Free' => $magazine->isFree ? 'Yes' : 'No',
                        'Featured' => $magazine->isFeatured ? 'Yes' : 'No',
                        'Active' => $magazine->isActive ? 'Yes' : 'No',
                        'Status' => ucfirst($magazine->status ?? 'draft'),
                        'Published At' => $magazine->published_at?->format('d M Y, h:i A'),
                        'Created At' => $magazine->created_at?->format('d M Y, h:i A'),
                        'Updated At' => $magazine->updated_at?->format('d M Y, h:i A'),
                    ] as $label => $value)
                        <div class="col-md-6"><small class="text-muted d-block">{{ $label }}</small><strong>{{ $value ?: '—' }}</strong></div>
                    @endforeach
                    <div class="col-12"><small class="text-muted d-block">Description</small><div class="text-break">{{ $magazine->description ?: '—' }}</div></div>
                </div></div>
            </div></div>
        </section>

        <section class="card admin-settings-card">
            <div class="card-header"><h3>Relationships</h3><p>Connected categories and authors.</p></div>
            <div class="card-body"><div class="row g-4">
                <div class="col-lg-6"><h4 class="h6">Categories</h4>@forelse ($magazine->categories as $category)<span class="badge text-bg-light border me-1 mb-1">{{ $category->name ?? 'Untitled' }}</span>@empty<span class="text-muted">None</span>@endforelse</div>
                <div class="col-lg-6"><h4 class="h6">Authors</h4>@forelse ($magazine->authors as $author)<span class="badge text-bg-light border me-1 mb-1">{{ $author->name ?? 'Untitled' }}</span>@empty<span class="text-muted">None</span>@endforelse</div>
            </div></div>
        </section>

        <section class="card admin-settings-card">
            <div class="card-header"><h3>Related Magazines</h3><p>Directional Magazine relationships managed for this record.</p></div>
            <div class="card-body">
                <div class="row g-3">
                    @forelse ($magazine->relatedMagazines as $relatedMagazine)
                        <div class="col-xl-4 col-md-6">
                            <div class="border rounded p-3 h-100 d-flex gap-3">
                                <div class="flex-shrink-0">
                                    @if ($relatedMagazine->cover_image)
                                        <img class="rounded border object-fit-cover" src="{{ asset($relatedMagazine->cover_image) }}" alt="{{ $relatedMagazine->title }} cover" width="64" height="84">
                                    @else
                                        <div class="bg-light border rounded p-3 text-muted"><i class="fa-solid fa-book-open"></i></div>
                                    @endif
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h4 class="h6 text-break mb-1">{{ $relatedMagazine->title ?? 'Untitled Magazine' }}</h4>
                                    <div class="small text-muted mb-1">Issue: {{ $relatedMagazine->issue_number ?: '—' }}</div>
                                    <span class="badge text-bg-{{ $relatedMagazine->status === 'published' ? 'success' : 'secondary' }} mb-3">{{ ucfirst($relatedMagazine->status ?? 'draft') }}</span>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.magazine.show', ['id' => $relatedMagazine->id]) }}"><i class="fa-solid fa-eye me-1"></i>View</a>
                                        @can('magazines.related.remove')<form method="POST" action="{{ route('admin.magazine.related.remove', ['magazine' => $magazine->id, 'relatedMagazine' => $relatedMagazine->id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit" data-delete-confirm data-delete-title="Remove this related Magazine?" data-delete-text="Only the relationship will be removed. Neither Magazine will be deleted."><i class="fa-solid fa-link-slash me-1"></i>Remove</button>
                                        </form>@endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12"><p class="text-muted mb-0">No related Magazines selected.</p></div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="card admin-settings-card">
            <div class="card-header"><h3>SEO</h3><p>Connected Magazine MetaTag record.</p></div>
            <div class="card-body"><div class="row g-3">
                <div class="col-lg-6"><small class="text-muted d-block">Meta Title</small><div>{{ $metaTag?->title ?? '—' }}</div></div>
                <div class="col-lg-6"><small class="text-muted d-block">Slug</small><div>{{ $metaTag?->slug_url ?? '—' }}</div></div>
                <div class="col-lg-6"><small class="text-muted d-block">Keywords</small><div class="text-break">{{ $metaTag?->keywords ?? '—' }}</div></div>
                <div class="col-lg-6"><small class="text-muted d-block">Canonical</small><div class="text-break">{{ $metaTag?->canonical_url ?: 'Auto-generated from current domain, Magazine route, and slug' }}</div></div>
                <div class="col-12"><small class="text-muted d-block">Meta Description</small><div class="text-break">{{ $metaTag?->description ?? '—' }}</div></div>
            </div></div>
        </section>

        <section class="card admin-settings-card">
            <div class="card-header"><h3>PDF Storage</h3><p>Provider-independent primary and fallback copies.</p></div>
            <div class="card-body"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Provider</th><th>Primary</th><th>Priority</th><th>Status</th><th>Verification</th><th>File Name</th><th>Size</th><th>Mime Type</th><th>Last Verified</th><th>Error</th><th>Actions</th></tr></thead><tbody>
                @forelse ($magazine->storageLocations as $location)
                    <tr><td>{{ $location->storageProvider?->name ?? 'Unknown' }}</td><td>{{ $location->is_primary ? 'Yes' : 'No' }}</td><td>{{ $location->priority ?? '—' }}</td><td><span class="badge text-bg-{{ $location->status === 'available' ? 'success' : ($location->status === 'failed' ? 'danger' : 'secondary') }}">{{ ucfirst($location->status ?? 'unknown') }}</span></td><td>{{ ucfirst($location->verification_status ?? 'pending') }}</td><td>{{ $location->file_name ?? '—' }}</td><td>{{ $location->size ? Illuminate\Support\Number::fileSize($location->size) : '—' }}</td><td>{{ $location->mime_type ?? '—' }}</td><td>{{ $location->last_verified_at?->format('d M Y, h:i A') ?? '—' }}</td><td class="text-danger text-break">{{ $location->error_message ?? '—' }}</td><td>@if ($location->status === 'available' && $location->storageProvider)<div class="d-flex flex-wrap gap-2">@can('magazines.pdf.view')<a class="btn btn-sm btn-outline-primary" href="{{ route('admin.magazine.pdf.view', ['magazine' => $magazine->id, 'storageLocation' => $location->id]) }}" target="_blank" rel="noopener"><i class="fa-solid fa-eye me-1"></i>View PDF</a>@endcan @can('magazines.pdf.download')<a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.magazine.pdf.download', ['magazine' => $magazine->id, 'storageLocation' => $location->id]) }}"><i class="fa-solid fa-download me-1"></i>Download</a>@endcan</div>@else<span class="text-muted">Unavailable</span>@endif</td></tr>
                @empty
                    <tr><td class="text-muted text-center" colspan="11">No PDF storage locations recorded.</td></tr>
                @endforelse
            </tbody></table></div></div>
        </section>

        <div class="d-flex flex-wrap justify-content-between gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('admin.magazine.index') }}"><i class="fa-solid fa-arrow-left me-2"></i>Back to Magazines</a>
            @can('magazines.publish')@if ($magazine->status !== 'published')<form method="POST" action="{{ route('admin.magazine.publish', ['id' => $magazine->id]) }}">@csrf<button class="btn btn-success" type="submit" data-publish-confirm><i class="fa-solid fa-upload me-2"></i>Publish</button></form>@endif @endcan
        </div>
    </div>
@endsection
