<div class="row g-4">
    <div class="col-lg-6">
        <label class="form-label" for="language">Language <span class="text-danger">*</span></label>
        <select class="form-select select2 @error('language') is-invalid @enderror" id="language" name="language" required>
            <option value="">Select Language</option>
            @foreach ($languages as $language)
                <option value="{{ $language->code }}" @selected(old('language', $category?->language) === $language->code)>{{ $language->name ?? $language->code }}</option>
            @endforeach
        </select>
        @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-lg-6">
        <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category?->name) }}" maxlength="150" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-lg-6">
        <label class="form-label" for="icon">Icon / Image <span class="text-muted">(optional)</span></label>
        <input class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" type="file" accept="image/png,image/webp">
        <div class="form-text">Upload a PNG or WebP image. Recommended display size: 70 × 70 px.</div>
        @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror

        @if ($category?->icon)
            <div class="mt-3 d-flex align-items-center gap-3">
                <img src="{{ asset($category->icon) }}" alt="Current {{ $category->name }} icon" width="70" height="70" class="rounded border bg-light p-1" style="object-fit: contain;">
                <div class="form-check">
                    <input type="hidden" name="remove_icon" value="0">
                    <input class="form-check-input" id="remove_icon" name="remove_icon" type="checkbox" value="1" @checked(old('remove_icon'))>
                    <label class="form-check-label" for="remove_icon">Delete current icon/image</label>
                </div>
            </div>
        @endif
    </div>
    @if ($category)
        <div class="col-12">
            <span class="form-label d-block">Status <span class="text-danger">*</span></span>
            <div class="d-flex flex-wrap gap-4">
                @foreach ([1 => 'Active', 0 => 'Deactive'] as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input @error('isActive') is-invalid @enderror" id="category-status-{{ $value }}" name="isActive" type="radio" value="{{ $value }}" @checked((string) old('isActive', (int) $category->isActive) === (string) $value)>
                        <label class="form-check-label" for="category-status-{{ $value }}">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
            @error('isActive')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
    @endif
</div>
