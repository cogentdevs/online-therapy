@php($selectedCondition = (int) old('section_condition', $homeSection?->section_condition))

<div class="row g-4" data-home-section-form>
    <div class="col-lg-4">
        <label class="form-label" for="language">Language <span class="text-danger">*</span></label>
        <select class="form-select select2 @error('language') is-invalid @enderror" id="language" name="language" required>
            <option value="">Select Language</option>
            @foreach ($languages as $language)
                <option value="{{ $language->code }}" @selected(old('language', $homeSection?->language) === $language->code)>{{ $language->name ?? $language->code }}</option>
            @endforeach
        </select>
        @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-lg-4">
        <label class="form-label" for="position">Position <span class="text-danger">*</span></label>
        <select class="form-select select2 @error('position') is-invalid @enderror" id="position" name="position" required>
            <option value="">Select Position</option>
            @foreach ($positions as $value => $label)
                <option value="{{ $value }}" @selected(old('position', $homeSection?->position) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-lg-4">
        <label class="form-label" for="section_condition">Section Condition <span class="text-danger">*</span></label>
        <select class="form-select select2 @error('section_condition') is-invalid @enderror" id="section_condition"
            name="section_condition" data-home-section-condition required>
            <option value="">Select Section Condition</option>
            @foreach ($sectionConditions as $value => $label)
                <option value="{{ $value }}" @selected($selectedCondition === $value)>{{ $value }}. {{ $label }}</option>
            @endforeach
        </select>
        @error('section_condition')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-lg-6" data-home-section-field="title">
        <label class="form-label" for="title">Title</label>
        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $homeSection?->title) }}" maxlength="255">
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12" data-home-section-field="description">
        <label class="form-label" for="description">Description <span class="text-danger">*</span></label>
        <textarea class="form-control article-editor @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $homeSection?->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-lg-6" data-home-section-field="title_2">
        <label class="form-label" for="title_2">Title 2</label>
        <input class="form-control @error('title_2') is-invalid @enderror" id="title_2" name="title_2" value="{{ old('title_2', $homeSection?->title_2) }}" maxlength="255">
        @error('title_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12" data-home-section-field="description_2">
        <label class="form-label" for="description_2">Description 2 <span class="text-danger">*</span></label>
        <textarea class="form-control article-editor @error('description_2') is-invalid @enderror" id="description_2" name="description_2" rows="5">{{ old('description_2', $homeSection?->description_2) }}</textarea>
        @error('description_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @foreach (['image' => 'Image', 'image_2' => 'Image 2'] as $field => $label)
        <div class="col-lg-6" data-home-section-field="{{ $field }}">
            <label class="form-label" for="{{ $field }}">{{ $label }} <span class="text-danger" data-home-section-required-marker>*</span></label>
            @if ($homeSection?->getAttribute($field))
                <div class="admin-image-preview mb-3"><img src="{{ asset($homeSection->getAttribute($field)) }}" alt="Current {{ strtolower($label) }}"></div>
            @endif
            <input class="form-control @error($field) is-invalid @enderror" id="{{ $field }}" type="file" name="{{ $field }}"
                accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp"
                data-has-existing-image="{{ $homeSection?->getAttribute($field) ? 'true' : 'false' }}">
            <div class="form-text">PNG, JPG, JPEG, or WEBP. Maximum 5 MB.{{ $homeSection?->getAttribute($field) ? ' Leave blank to retain the current image.' : '' }}</div>
            @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endforeach

    <div class="col-12">
        <span class="form-label d-block">Status <span class="text-danger">*</span></span>
        <div class="d-flex flex-wrap gap-4">
            @foreach ([1 => 'Active', 0 => 'Deactive'] as $value => $label)
                <div class="form-check">
                    <input class="form-check-input @error('is_active') is-invalid @enderror" id="status_{{ $value }}"
                        name="is_active" type="radio" value="{{ $value }}" @checked((string) old('is_active', $homeSection ? (int) $homeSection->is_active : 1) === (string) $value)>
                    <label class="form-check-label" for="status_{{ $value }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>
        @error('is_active')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
</div>
