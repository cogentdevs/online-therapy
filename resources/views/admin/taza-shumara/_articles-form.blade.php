@php
    $submittedPlacements = old('articles');
    $formPlacements = $submittedPlacements !== null
        ? collect($submittedPlacements)
        : $articlePlacements;
@endphp

<div data-taza-articles-form data-next-index="{{ max(1, $formPlacements->count()) }}">
    <div class="d-grid gap-3" data-taza-article-rows>
        @forelse ($formPlacements as $index => $articlePlacement)
            @include('admin.taza-shumara._article-row', ['index' => $index])
        @empty
            @include('admin.taza-shumara._article-row', ['index' => 0, 'articlePlacement' => []])
        @endforelse
    </div>
    <button class="btn btn-outline-primary mt-3" type="button" data-taza-add-article><i class="fa-solid fa-plus me-2"></i>Add Another Article</button>
    @error('articles')<div class="text-danger small mt-2">{{ $message }}</div>@enderror

    <template data-taza-article-template>
        @include('admin.taza-shumara._article-row', ['index' => '__INDEX__', 'articlePlacement' => []])
    </template>
</div>
