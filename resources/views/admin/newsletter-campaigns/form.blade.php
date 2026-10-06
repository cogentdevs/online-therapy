@extends('layouts.adminLayout.admin-design')

@section('title', $campaign->exists ? 'Edit Newsletter Campaign' : 'Create Newsletter Campaign')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="mb-1">{{ $campaign->exists ? 'Edit' : 'Create' }} Newsletter Campaign</h2>
            <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.newsletter-campaigns.index') }}">Newsletter Campaigns</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $campaign->exists ? 'Edit' : 'Create' }}</li>
            </ol></nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Please correct the highlighted fields.</strong></div>
        @endif

        <form method="POST" action="{{ $campaign->exists ? route('admin.newsletter-campaigns.update', $campaign) : route('admin.newsletter-campaigns.store') }}" data-newsletter-campaign-form>
            @csrf
            @if ($campaign->exists) @method('PUT') @endif

            <section class="card admin-settings-card mb-4">
                <div class="card-header">
                    <h3>Campaign Information</h3>
                    <p>Set the Newsletter title, introduction and ordered content.</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label" for="newsletter-title">Title <span class="text-danger">*</span></label>
                            <input class="form-control @error('title') is-invalid @enderror" id="newsletter-title" name="title" required maxlength="255" value="{{ old('title', $campaign->title) }}">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="newsletter-description">Short Description</label>
                            <textarea class="form-control @error('short_description') is-invalid @enderror" id="newsletter-description" name="short_description" rows="4" maxlength="2000">{{ old('short_description', $campaign->short_description) }}</textarea>
                            @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </section>

            <section class="card admin-settings-card mb-4">
                <div class="card-header">
                    <h3>Campaign Content</h3>
                    <p>Select published Articles and Magazines, then arrange their delivery order.</p>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-10">
                            <label class="form-label" for="newsletter-content-select">Published Content</label>
                            <select class="form-select select2" id="newsletter-content-select" data-newsletter-content-select>
                                <option value="">Select published content</option>
                                @foreach ($articles as $article)<option value="article:{{ $article->id }}">Article — {{ $article->title }} ({{ $article->language }})</option>@endforeach
                                @foreach ($magazines as $magazine)<option value="magazine:{{ $magazine->id }}">Magazine — {{ $magazine->title }}@if($magazine->issue_number) — {{ $magazine->issue_number }}@endif ({{ $magazine->language }})</option>@endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-outline-primary w-100" type="button" data-newsletter-content-add><i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Add</button>
                        </div>
                    </div>

                    <div class="vstack gap-2 mt-4" data-newsletter-content-list>
                        @foreach (old('contents', $campaign->contents ?? []) as $i => $item)
                            @php($type = is_array($item) ? $item['type'] : $item->content_type)
                            @php($id = is_array($item) ? $item['id'] : $item->content_id)
                            <div class="input-group" data-content-row>
                                <span class="input-group-text">{{ ucfirst($type) }} #{{ $id }}</span>
                                <input type="hidden" name="contents[{{ $i }}][type]" value="{{ $type }}">
                                <input type="hidden" name="contents[{{ $i }}][id]" value="{{ $id }}">
                                <span class="input-group-text">Sort Order</span>
                                <input class="form-control" type="number" min="1" name="contents[{{ $i }}][sort_order]" value="{{ $i + 1 }}" aria-label="Sort order">
                                <button class="btn btn-outline-danger" type="button" data-content-remove><i class="fa-solid fa-xmark me-1" aria-hidden="true"></i>Remove</button>
                            </div>
                        @endforeach
                    </div>
                    @error('contents')<div class="invalid-feedback d-block mt-2">{{ $message }}</div>@enderror
                    <div class="form-text mt-2">At least one published content item is required before production delivery.</div>
                </div>
            </section>

            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.newsletter-campaigns.index') }}">Cancel</a>
                <button class="btn btn-primary admin-primary-button" type="submit"><i class="fa-solid fa-floppy-disk me-2" aria-hidden="true"></i>Save Draft</button>
            </div>
        </form>
    </div>
@endsection
