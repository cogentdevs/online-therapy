<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CategoryIndexRequest;
use App\Http\Requests\Api\CategoryShowRequest;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Magazine;
use App\Models\Tags;
use App\Services\CategoryApiService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryApiService $categoryApiService) {}

    public function index(CategoryIndexRequest $request): JsonResponse
    {
        $categories = $this->categoryApiService->paginate();

        return response()->json(['success' => true, 'data' => [
            'categories' => collect($categories->items())->map(fn (Category $category): array => $this->categoryData($category))->values(),
            'pagination' => $this->paginationData($categories),
        ]]);
    }

    public function show(CategoryShowRequest $request, int $category): JsonResponse
    {
        $categoryModel = $this->categoryApiService->findEligible($category);
        $validated = $request->validated();
        $contentType = $validated['type'] ?? 'magazines';
        $content = $this->categoryApiService->content($categoryModel, $validated);
        $banner = $this->categoryApiService->banner();

        return response()->json(['success' => true, 'data' => [
            'category' => [
                ...$this->categoryData($categoryModel),
                'banner_image_url' => $this->assetUrl($banner?->image),
            ],
            'content_type' => $contentType,
            'items' => $content['items']->map(fn (Article|Magazine $item): array => $item instanceof Article
                ? $this->articleData($item)
                : $this->magazineData($item))->values(),
            'pagination' => [
                ...$this->paginationData($content['pagination']),
                'unit' => 'publication_year',
            ],
            'filters' => [
                'search' => $validated['search'] ?? null,
                'selected_year' => isset($validated['year']) ? (int) $validated['year'] : null,
                'years' => $content['years']->values(),
            ],
        ]]);
    }

    /** @return array<string, mixed> */
    private function categoryData(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'image_url' => $this->assetUrl($category->image),
            'article_count' => (int) $category->public_articles_count,
            'magazine_count' => (int) $category->public_magazines_count,
            'detail_url' => route('api.categories.show', $category->id),
        ];
    }

    /** @return array<string, mixed> */
    private function articleData(Article $article): array
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'issue_number' => $article->issue_number,
            'short_description' => $article->short_description,
            'image_url' => $this->assetUrl($article->image),
            'published_date' => $article->publish_date?->toDateString(),
            'publication_year' => $article->publish_date?->year,
            'is_free' => (bool) $article->isFree,
            'free_until' => $article->free_until?->toDateString(),
            'view_count' => $article->show_visit_counter ? (int) $article->site_visits_count : null,
            'authors' => $article->authors->map(fn (Author $author): array => [
                'id' => $author->id,
                'name' => $author->name,
                'picture_url' => $this->assetUrl($author->picture),
            ])->values(),
            'categories' => $article->categories->map(fn (Category $category): array => ['id' => $category->id, 'name' => $category->name])->values(),
            'tags' => $article->tags->map(fn (Tags $tag): array => ['id' => $tag->id, 'name' => $tag->name])->values(),
            'detail_url' => route('api.articles.show', $article->id),
        ];
    }

    /** @return array<string, mixed> */
    private function magazineData(Magazine $magazine): array
    {
        return [
            'id' => $magazine->id,
            'title' => $magazine->title,
            'issue_number' => $magazine->issue_number,
            'description' => $magazine->description,
            'cover_image_url' => $this->assetUrl($magazine->cover_image),
            'published_date' => $magazine->publish_date?->toDateString(),
            'publication_year' => $magazine->publish_date?->year,
            'is_free' => (bool) $magazine->isFree,
            'free_until' => $magazine->free_until?->toDateString(),
            'is_downloadable' => (bool) $magazine->is_downloadable,
            'view_count' => $magazine->show_visit_counter ? (int) $magazine->site_visits_count : null,
            'authors' => $magazine->authors->map(fn (Author $author): array => [
                'id' => $author->id,
                'name' => $author->name,
                'picture_url' => $this->assetUrl($author->picture),
            ])->values(),
            'categories' => $magazine->categories->map(fn (Category $category): array => ['id' => $category->id, 'name' => $category->name])->values(),
            'tags' => $magazine->tags->map(fn (Tags $tag): array => ['id' => $tag->id, 'name' => $tag->name])->values(),
            'detail_url' => route('api.magazines.show', $magazine->id),
        ];
    }

    /** @return array<string, mixed> */
    private function paginationData(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'next_page_url' => $paginator->nextPageUrl(),
            'previous_page_url' => $paginator->previousPageUrl(),
        ];
    }

    private function assetUrl(?string $path): ?string
    {
        return filled($path) ? asset(ltrim(str_replace('\\', '/', $path), '/')) : null;
    }
}
