<div class="row g-3">
    <div class="col-lg-5">
        <label class="form-label" for="article_id">Article <span class="text-danger">*</span></label>
        <select class="form-select select2 @error('article_id') is-invalid @enderror" id="article_id" name="article_id" required>
            <option value="">Select Article</option>
            @foreach ($articles as $article)
                <option value="{{ $article->id }}" @selected((string) old('article_id', $articlePlacement?->article_id) === (string) $article->id)>
                    {{ $article->title ?: 'Untitled Article' }}{{ $article->categories->first()?->name ? ' — '.$article->categories->first()->name : '' }}{{ $article->publish_date ? ' — '.$article->publish_date->format('d M Y') : '' }} ({{ $article->language }})
                </option>
            @endforeach
        </select>
        @error('article_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 col-lg-3">
        <label class="form-label" for="display_width">Display Layout <span class="text-danger">*</span></label>
        <select class="form-select select2 @error('display_width') is-invalid @enderror" id="display_width" name="display_width" required>
            @foreach ($displayWidths as $value => $label)
                <option value="{{ $value }}" @selected(old('display_width', $articlePlacement?->display_width ?? 'full') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('display_width')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 col-lg-2">
        <label class="form-label" for="position">Position <span class="text-danger">*</span></label>
        <select class="form-select select2 @error('position') is-invalid @enderror" id="position" name="position" required>
            @foreach ($positions as $value => $label)
                <option value="{{ $value }}" @selected(old('position', $articlePlacement?->position ?? 'top') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 col-lg-2">
        <label class="form-label" for="sort_order">Sort Order <span class="text-danger">*</span></label>
        <input class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" type="number" name="sort_order" min="0" value="{{ old('sort_order', $articlePlacement?->sort_order ?? 0) }}" required>
        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
