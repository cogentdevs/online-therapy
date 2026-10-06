<div class="row g-4">
    <div class="col-lg-6">
        <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" type="text"
            value="{{ old('title', $video?->title) }}" maxlength="255" required>
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-lg-6">
        <label class="form-label" for="video_link">Video Link <span class="text-danger">*</span></label>
        <input class="form-control @error('video_link') is-invalid @enderror" id="video_link" name="video_link"
            type="url" value="{{ old('video_link', $video?->video_link) }}" maxlength="2048" required>
        @error('video_link')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="short_description">Short Description</label>
        <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description"
            name="short_description" rows="3">{{ old('short_description', $video?->short_description) }}</textarea>
        @error('short_description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-lg-8">
        <label class="form-label" for="thumbnail">Thumbnail</label>
        @if ($video?->thumbnail)
            <div class="mb-2">
                <img class="admin-current-image" src="{{ asset($video->thumbnail) }}" alt="Current Video thumbnail">
            </div>
        @endif
        <input class="form-control @error('thumbnail') is-invalid @enderror" id="thumbnail" name="thumbnail"
            type="file" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp">
        <div class="form-text">PNG, JPG, JPEG, or WEBP. Maximum 5 MB.{{ $video?->thumbnail ? ' Leave empty to retain the current thumbnail.' : '' }}</div>
        @error('thumbnail')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @foreach ([
        'is_free' => ['Free Access', 'Free', 'Paid', 0],
        'is_active' => ['Visibility', 'Active', 'Deactive', 1],
        'is_share' => ['Sharing', 'Enabled', 'Disabled', 0],
    ] as $field => [$label, $yesLabel, $noLabel, $default])
        <div class="col-lg-4">
            <span class="form-label d-block">{{ $label }} <span class="text-danger">*</span></span>
            <div class="d-flex gap-4">
                <div class="form-check">
                    <input class="form-check-input" id="{{ $field }}_yes" name="{{ $field }}" type="radio"
                        value="1" @checked((string) old($field, $video ? (int) $video->getAttribute($field) : $default) === '1')>
                    <label class="form-check-label" for="{{ $field }}_yes">{{ $yesLabel }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" id="{{ $field }}_no" name="{{ $field }}" type="radio"
                        value="0" @checked((string) old($field, $video ? (int) $video->getAttribute($field) : $default) === '0')>
                    <label class="form-check-label" for="{{ $field }}_no">{{ $noLabel }}</label>
                </div>
            </div>
            @error($field)
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    @endforeach
</div>
