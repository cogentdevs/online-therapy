<div class="row g-4">
    <div class="col-lg-6">
        <label class="form-label" for="language">Language <span class="text-danger">*</span></label>
        <select class="form-select select2 @error('language') is-invalid @enderror" id="language" name="language"
            required>
            <option value="">Select Language</option>
            @foreach ($languages as $language)
                <option value="{{ $language->code }}" @selected(old('language', $tazaShumara?->language) === $language->code)>{{ $language->name }}</option>
            @endforeach
        </select>
        @error('language')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-lg-6">
        <label class="form-label" for="magazine_id">Magazine <span class="text-danger">*</span></label>
        <select class="form-select select2 @error('magazine_id') is-invalid @enderror" id="magazine_id"
            name="magazine_id" required>
            <option value="">Select Magazine</option>
            @foreach ($magazines as $magazine)
                <option value="{{ $magazine->id }}" @selected((string) old('magazine_id', $tazaShumara?->magazine_id) === (string) $magazine->id)>
                    {{ $magazine->issue_number ?: 'No Issue' }} —
                    {{ $magazine->title ?: 'Untitled Magazine' }}{{ $magazine->publish_date ? ' — ' . $magazine->publish_date->format('d M Y') : '' }}
                    ({{ $magazine->language }})
                </option>
            @endforeach
        </select>
        @error('magazine_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-lg-6">
        <label class="form-label" for="cover_image">Custom Cover</label>
        @if ($tazaShumara?->cover_image)
            <div class="admin-image-preview mb-3"><img src="{{ asset($tazaShumara->cover_image) }}"
                    alt="Current custom cover"></div>
            <button class="btn btn-sm btn-outline-danger mb-3" type="submit" form="remove-taza-shumara-cover-form"
                data-delete-confirm data-delete-title="Remove this custom cover?"
                data-delete-text="Only the custom Taza Shumara cover will be removed. The Magazine cover will remain unchanged.">
                <i class="fa-solid fa-trash me-1"></i>Remove Cover
            </button>
        @endif
        <input class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" type="file"
            name="cover_image" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp">
        <div class="form-text">Recommended size: 1145 × 550 px. Optional override. PNG, JPG, JPEG, or WEBP. Maximum 5
            MB.{{ $tazaShumara?->cover_image ? ' Leave blank to retain the current cover.' : '' }}</div>
        @error('cover_image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <div class="row g-3">
            @foreach (['show_title' => 'Show Magazine Title', 'show_short_description' => 'Show Short Description', 'is_active' => 'Active'] as $field => $label)
                <div class="col-md-4">
                    <span class="form-label d-block">{{ $label }} <span class="text-danger">*</span></span>
                    <div class="d-flex gap-4">
                        @foreach ([1 => 'Yes', 0 => 'No'] as $value => $option)
                            <div class="form-check">
                                <input class="form-check-input" id="{{ $field }}_{{ $value }}"
                                    name="{{ $field }}" type="radio" value="{{ $value }}"
                                    @checked((string) old($field, $tazaShumara ? (int) $tazaShumara->{$field} : 1) === (string) $value)>
                                <label class="form-check-label"
                                    for="{{ $field }}_{{ $value }}">{{ $option }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error($field)
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach
        </div>
    </div>
</div>
