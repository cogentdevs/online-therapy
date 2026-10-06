<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ArticleIndexRequest;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Magazine;
use App\Models\Tags;
use App\Models\User;
use App\Services\ArticleApiService;
use App\Services\SiteVisitService;
use App\Services\SubscriptionContentAccessService;
use App\Services\UserContentVisitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleApiService $articleApiService,
        private readonly SubscriptionContentAccessService $contentAccessService,
        private readonly SiteVisitService $siteVisitService,
        private readonly UserContentVisitService $userContentVisitService,
    ) {}

    public function index(ArticleIndexRequest $request): JsonResponse
    {
        $articles = $this->articleApiService->paginate($request->validated());
        $filters = $this->articleApiService->filterOptions();

        return response()->json([
            'success' => true,
            'data' => [
                'articles' => collect($articles->items())->map(fn (Article $article): array => $this->listArticle($article))->values(),
                'pagination' => [
                    'current_page' => $articles->currentPage(),
                    'last_page' => $articles->lastPage(),
                    'per_page' => $articles->perPage(),
                    'total' => $articles->total(),
                    'from' => $articles->firstItem(),
                    'to' => $articles->lastItem(),
                    'next_page_url' => $articles->nextPageUrl(),
                    'previous_page_url' => $articles->previousPageUrl(),
                ],
                'filters' => [
                    'authors' => $filters['authors']->map(fn (Author $author): array => [
                        'id' => $author->id,
                        'name' => $author->name,
                        'picture_url' => $this->assetUrl($author->picture),
                    ])->values(),
                    'categories' => $filters['categories']->map(fn (Category $category): array => ['id' => $category->id, 'name' => $category->name])->values(),
                    'tags' => $filters['tags']->map(fn (Tags $tag): array => ['id' => $tag->id, 'name' => $tag->name])->values(),
                    'years' => $filters['years']->values(),
                ],
            ],
        ]);
    }

    public function show(Request $request, int $article): JsonResponse
    {
        $articleModel = $this->articleApiService->findEligible($article);
        $isPubliclyAccessible = $this->contentAccessService->isPubliclyAccessible($articleModel);
        $user = $request->user('sanctum');
        $canAccess = $isPubliclyAccessible
            || ($user instanceof User && $this->contentAccessService->canAccess(
                $user,
                $articleModel,
                SubscriptionContentAccessService::MODULE_ARTICLES,
            ));

        if ($canAccess) {
            $this->siteVisitService->record($request, $articleModel);
        }

        if ($canAccess && $user?->hasRole('user', 'web')) {
            $this->userContentVisitService->record($user, $articleModel);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'article' => [
                    ...$this->listArticle($articleModel),
                    'content' => $canAccess ? $articleModel->article : null,
                    'access' => [
                        'is_publicly_accessible' => $isPubliclyAccessible,
                        'requires_subscription' => ! $isPubliclyAccessible,
                        'can_access' => $canAccess,
                    ],
                    'related_articles' => $articleModel->relatedArticles
                        ->map(fn (Article $relatedArticle): array => $this->relatedArticle($relatedArticle))->values(),
                ],
            ],
        ]);
    }

    /** @return array<string, mixed> */
    private function listArticle(Article $article): array
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
            'magazine' => $article->magazine ? $this->magazineData($article->magazine) : null,
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
    private function relatedArticle(Article $article): array
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'image_url' => $this->assetUrl($article->image),
            'published_date' => $article->publish_date?->toDateString(),
            'is_free' => (bool) $article->isFree,
            'free_until' => $article->free_until?->toDateString(),
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
            'published_date' => $magazine->publish_date?->toDateString(),
            'cover_image_url' => $this->assetUrl($magazine->cover_image),
        ];
    }

    private function assetUrl(?string $path): ?string
    {
        return filled($path) ? asset(ltrim(str_replace('\\', '/', $path), '/')) : null;
    }
}
