<div class="border rounded p-3" data-taza-article-row>
    @if (filled(data_get($articlePlacement, 'id')))
        <input type="hidden" name="articles[{{ $index }}][id]" value="{{ data_get($articlePlacement, 'id') }}">
    @endif
    <div class="row g-3 align-items-end">
        <div class="col-lg-5">
            <label class="form-label" for="articles_{{ $index }}_article_id">Article <span class="text-danger">*</span></label>
            <select class="form-select select2 @error("articles.$index.article_id") is-invalid @enderror" id="articles_{{ $index }}_article_id" name="articles[{{ $index }}][article_id]">
                <option value="">Select Article</option>
                @foreach ($articles as $article)
                    <option value="{{ $article->id }}" @selected((string) data_get($articlePlacement, 'article_id') === (string) $article->id)>
                        {{ $article->title ?: 'Untitled Article' }}{{ $article->categories->first()?->name ? ' — '.$article->categories->first()->name : '' }}{{ $article->publish_date ? ' — '.$article->publish_date->format('d M Y') : '' }} ({{ $article->language }})
                    </option>
                @endforeach
            </select>
            @error("articles.$index.article_id")<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 col-lg-3">
            <label class="form-label" for="articles_{{ $index }}_display_width">Display Layout <span class="text-danger">*</span></label>
            <select class="form-select select2 @error("articles.$index.display_width") is-invalid @enderror" id="articles_{{ $index }}_display_width" name="articles[{{ $index }}][display_width]">
                @foreach ($displayWidths as $value => $label)
                    <option value="{{ $value }}" @selected(data_get($articlePlacement, 'display_width', 'full') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error("articles.$index.display_width")<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3 col-lg-2">
            <label class="form-label" for="articles_{{ $index }}_position">Position <span class="text-danger">*</span></label>
            <select class="form-select select2 @error("articles.$index.position") is-invalid @enderror" id="articles_{{ $index }}_position" name="articles[{{ $index }}][position]">
                @foreach ($positions as $value => $label)
                    <option value="{{ $value }}" @selected(data_get($articlePlacement, 'position', 'top') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error("articles.$index.position")<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3 col-lg-1">
            <label class="form-label" for="articles_{{ $index }}_sort_order">Sort Order <span class="text-danger">*</span></label>
            <input class="form-control @error("articles.$index.sort_order") is-invalid @enderror" id="articles_{{ $index }}_sort_order" type="number" name="articles[{{ $index }}][sort_order]" min="0" value="{{ data_get($articlePlacement, 'sort_order', 0) }}">
            @error("articles.$index.sort_order")<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2 col-lg-1">
            <button class="btn btn-outline-danger w-100" type="button" data-taza-remove-article aria-label="Remove Article"><i class="fa-solid fa-trash"></i></button>
        </div>
    </div>
</div>
