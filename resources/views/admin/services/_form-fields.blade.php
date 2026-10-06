<div class="row g-4">
    <div class="col-12">
        <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text"
            value="{{ old('name', $service?->name) }}" maxlength="255" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="description">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
            rows="6">{{ old('description', $service?->description) }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-lg-6">
        <label class="form-label" for="image">Image @unless ($service)<span class="text-danger">*</span>@endunless</label>
        @if ($service?->image)
            <div class="admin-image-preview mb-3">
                <img src="{{ asset($service->image) }}" alt="Current Service image">
            </div>
        @endif
        <input class="form-control @error('image') is-invalid @enderror" id="image" name="image" type="file"
            accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp" @required(! $service)>
        <div class="form-text">PNG, JPG, JPEG, or WEBP. Maximum 5 MB.{{ $service?->image ? ' Leave blank to retain the current image.' : '' }}</div>
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <span class="form-label d-block">Status <span class="text-danger">*</span></span>
        <div class="d-flex flex-wrap gap-4">
            @foreach ([1 => 'Active', 0 => 'Deactive'] as $value => $label)
                <div class="form-check">
                    <input class="form-check-input" id="status_{{ $value }}" name="isactive" type="radio"
                        value="{{ $value }}" @checked((string) old('isactive', $service ? (int) $service->isactive : 1) === (string) $value)>
                    <label class="form-check-label" for="status_{{ $value }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>
        @error('isactive')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
