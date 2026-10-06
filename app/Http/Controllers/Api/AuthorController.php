<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthorIndexRequest;
use App\Http\Requests\Api\AuthorShowRequest;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Tags;
use App\Services\AuthorApiService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class AuthorController extends Controller
{
    public function __construct(private readonly AuthorApiService $authorApiService) {}

    public function index(AuthorIndexRequest $request): JsonResponse
    {
        $authors = $this->authorApiService->paginate($request->validated());
        $defaultVisibility = $this->authorApiService->defaultVisibility();

        return response()->json(['success' => true, 'data' => [
            'authors' => collect($authors->items())
                ->map(fn (Author $author): array => $this->authorData($author, $defaultVisibility, listing: true))
                ->values(),
            'pagination' => $this->paginationData($authors),
        ]]);
    }

    public function show(AuthorShowRequest $request, int $author): JsonResponse
    {
        $authorModel = $this->authorApiService->findEligible($author);
        $validated = $request->validated();
        $articles = $this->authorApiService->articles($authorModel, $validated);
        $categories = $this->authorApiService->categories($authorModel);
        $years = $this->authorApiService->years($authorModel);
        $otherAuthors = $this->authorApiService->otherAuthors($authorModel);
        $defaultVisibility = $this->authorApiService->defaultVisibility();

        return response()->json(['success' => true, 'data' => [
            'author' => $this->authorData($authorModel, $defaultVisibility),
            'articles' => collect($articles->items())->map(fn (Article $article): array => $this->articleData($article))->values(),
            'pagination' => $this->paginationData($articles),
            'filters' => [
                'search' => $validated['search'] ?? null,
                'selected_category_id' => isset($validated['category_id']) ? (int) $validated['category_id'] : null,
                'selected_year' => isset($validated['year']) ? (int) $validated['year'] : null,
                'categories' => $categories->map(fn (Category $category): array => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'article_count' => (int) $category->author_articles_count,
                    'detail_url' => route('api.categories.show', $category->id),
                ])->values(),
                'years' => $years->map(fn (Article $year): array => [
                    'year' => (int) $year->publication_year,
                    'article_count' => (int) $year->article_count,
                ])->values(),
            ],
            'other_authors' => $otherAuthors->map(fn (Author $otherAuthor): array => $this->otherAuthorData($otherAuthor, $defaultVisibility))->values(),
        ]]);
    }

    /** @param array<string, bool> $defaultVisibility */
    private function authorData(Author $author, array $defaultVisibility, bool $listing = false): array
    {
        $visibility = $author->effectiveVisibility($defaultVisibility);
        $data = [
            'id' => $author->id,
            'name' => $visibility['name'] ? $author->name : null,
            'picture_url' => $visibility['picture'] ? $this->authorPictureUrl($author) : null,
            'qualification' => $visibility['qualification'] ? $author->qualification : null,
            'experience_detail' => $visibility['experience_detail'] ? $author->experience_detail : null,
            'detail_url' => route('api.authors.show', $author->id),
        ];

        if (! $listing) {
            $data['experience_years'] = $visibility['experience_years'] ? $author->experience_years : null;
            $data['speciality'] = $visibility['speciality'] ? $author->speciality : null;
            $data['email'] = $visibility['email'] ? $author->email : null;
            $data['contact_number'] = $visibility['contact_number'] ? $author->contact_number : null;
        }

        return $data;
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
            'is_free' => (bool) $article->isFree,
            'free_until' => $article->free_until?->toDateString(),
            'view_count' => $article->show_visit_counter ? (int) $article->site_visits_count : null,
            'authors' => $article->authors->map(fn (Author $author): array => [
                'id' => $author->id,
                'name' => $author->name,
                'picture_url' => $this->assetUrl($author->picture),
            ])->values(),
            'categories' => $article->categories->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'detail_url' => route('api.categories.show', $category->id),
            ])->values(),
            'tags' => $article->tags->map(fn (Tags $tag): array => ['id' => $tag->id, 'name' => $tag->name])->values(),
            'detail_url' => route('api.articles.show', $article->id),
        ];
    }

    /** @param array<string, bool> $defaultVisibility */
    private function otherAuthorData(Author $author, array $defaultVisibility): array
    {
        $visibility = $author->effectiveVisibility($defaultVisibility);

        return [
            'id' => $author->id,
            'name' => $visibility['name'] ? $author->name : null,
            'picture_url' => $visibility['picture'] ? $this->authorPictureUrl($author) : null,
            'detail_url' => route('api.authors.show', $author->id),
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

    private function authorPictureUrl(Author $author): string
    {
        $path = filled($author->picture) && File::isFile(public_path($author->picture))
            ? $author->picture
            : 'images/backend-images/author/author.png';

        return asset($path);
    }

    private function assetUrl(?string $path): ?string
    {
        return filled($path) ? asset(ltrim(str_replace('\\', '/', $path), '/')) : null;
    }
}
