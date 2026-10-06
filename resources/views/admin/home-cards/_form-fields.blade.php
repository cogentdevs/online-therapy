<div class="row g-4">
    <div class="col-lg-6">
        <label class="form-label" for="language">Language <span class="text-danger">*</span></label>
        <select class="form-select select2 @error('language') is-invalid @enderror" id="language" name="language"
            required>
            <option value="">Select Language</option>
            @foreach ($languages as $language)
                <option value="{{ $language->code }}" @selected(old('language', $homeCard?->language) === $language->code)>
                    {{ $language->name ?? $language->code }}</option>
            @endforeach
        </select>
        @error('language')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-lg-6">
        <label class="form-label" for="position">Position <span class="text-danger">*</span></label>
        <select class="form-select select2 @error('position') is-invalid @enderror" id="position" name="position"
            required>
            <option value="">Select Position</option>
            @foreach ($positions as $value => $label)
                <option value="{{ $value }}" @selected(old('position', $homeCard?->position) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('position')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="title">Title</label>
        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title"
            value="{{ old('title', $homeCard?->title) }}" maxlength="255">
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="description">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
            rows="5">{{ old('description', $homeCard?->description) }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-lg-6">
        <label class="form-label" for="image">Image</label>
        @if ($homeCard?->image)
            <div class="admin-image-preview mb-3"><img src="{{ asset($homeCard->image) }}"
                    alt="Current Home Card image"></div>
        @endif
        <input class="form-control @error('image') is-invalid @enderror" id="image" type="file" name="image"
            accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp">
        <div class="form-text">Recommended size: 365 × 230 px. PNG, JPG, JPEG, or WEBP. Maximum 5
            MB.{{ $homeCard?->image ? ' Leave blank to retain the current image.' : '' }}</div>
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <span class="form-label d-block">Status <span class="text-danger">*</span></span>
        <div class="d-flex flex-wrap gap-4">
            @foreach ([1 => 'Active', 0 => 'Deactive'] as $value => $label)
                <div class="form-check">
                    <input class="form-check-input" id="status_{{ $value }}" name="is_active" type="radio"
                        value="{{ $value }}" @checked((string) old('is_active', $homeCard ? (int) $homeCard->is_active : 1) === (string) $value)>
                    <label class="form-check-label" for="status_{{ $value }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>
        @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
