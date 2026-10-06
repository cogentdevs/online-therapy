<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MagazineIndexRequest;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Magazine;
use App\Models\MediaStorageLocation;
use App\Models\Tags;
use App\Models\TazaShumaraArticle;
use App\Models\User;
use App\Services\MagazineApiService;
use App\Services\SiteVisitService;
use App\Services\Storage\MediaStorageService;
use App\Services\SubscriptionContentAccessService;
use App\Services\UserContentVisitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MagazineController extends Controller
{
    public function __construct(
        private readonly MagazineApiService $magazineApiService,
        private readonly SubscriptionContentAccessService $contentAccessService,
        private readonly MediaStorageService $mediaStorageService,
        private readonly SiteVisitService $siteVisitService,
        private readonly UserContentVisitService $userContentVisitService,
    ) {}

    public function index(MagazineIndexRequest $request): JsonResponse
    {
        $magazines = $this->magazineApiService->paginate($request->validated());
        $filters = $this->magazineApiService->filterOptions();

        return response()->json(['success' => true, 'data' => [
            'magazines' => collect($magazines->items())->map(fn (Magazine $magazine): array => $this->magazineData($magazine))->values(),
            'pagination' => [
                'current_page' => $magazines->currentPage(), 'last_page' => $magazines->lastPage(),
                'per_page' => $magazines->perPage(), 'total' => $magazines->total(),
                'from' => $magazines->firstItem(), 'to' => $magazines->lastItem(),
                'next_page_url' => $magazines->nextPageUrl(), 'previous_page_url' => $magazines->previousPageUrl(),
            ],
            'filters' => [
                'authors' => $filters['authors']->map(fn (Author $author): array => ['id' => $author->id, 'name' => $author->name, 'picture_url' => $this->assetUrl($author->picture)])->values(),
                'categories' => $filters['categories']->map(fn (Category $category): array => ['id' => $category->id, 'name' => $category->name])->values(),
                'tags' => $filters['tags']->map(fn (Tags $tag): array => ['id' => $tag->id, 'name' => $tag->name])->values(),
                'years' => $filters['years']->values(),
            ],
        ]]);
    }

    public function current(): JsonResponse
    {
        $tazaShumara = $this->magazineApiService->current();

        if (! $tazaShumara?->magazine) {
            return response()->json(['success' => false, 'message' => 'No current Magazine is available.'], 404);
        }

        return response()->json(['success' => true, 'data' => [
            'taza_shumara' => [
                'id' => $tazaShumara->id,
                'show_title' => $tazaShumara->show_title,
                'show_short_description' => $tazaShumara->show_short_description,
                'cover_image_url' => $this->assetUrl($tazaShumara->cover_image ?: $tazaShumara->magazine->cover_image),
                'magazine' => $this->magazineData($tazaShumara->magazine, includePdf: true),
                'articles' => $tazaShumara->articlePlacements->filter->article->map(fn (TazaShumaraArticle $placement): array => $this->placementData($placement))->values(),
            ],
        ]]);
    }

    public function show(Request $request, int $magazine): JsonResponse
    {
        $model = $this->magazineApiService->findEligible($magazine);
        $isPubliclyAccessible = $this->contentAccessService->isPubliclyAccessible($model);
        $user = $request->user('sanctum');
        $canAccess = $isPubliclyAccessible
            || ($user instanceof User && $this->contentAccessService->canAccess(
                $user,
                $model,
                SubscriptionContentAccessService::MODULE_MAGAZINES,
            ));

        if ($canAccess) {
            $this->siteVisitService->record($request, $model);
        }

        if ($canAccess && $user?->hasRole('user', 'web')) {
            $this->userContentVisitService->record($user, $model);
        }

        return response()->json(['success' => true, 'data' => ['magazine' => [
            ...$this->magazineData($model, includePdf: true, canAccess: $canAccess),
            'related_magazines' => $model->relatedMagazines->map(fn (Magazine $related): array => $this->relatedData($related))->values(),
        ]]]);
    }

    public function pdf(Request $request, int $magazine): StreamedResponse|JsonResponse
    {
        return $this->pdfResponse($request, $magazine, inline: true);
    }

    public function download(Request $request, int $magazine): StreamedResponse|JsonResponse
    {
        return $this->pdfResponse($request, $magazine, inline: false);
    }

    private function pdfResponse(Request $request, int $magazineId, bool $inline): StreamedResponse|JsonResponse
    {
        $magazine = $this->magazineApiService->eligibleQuery()->findOrFail($magazineId);
        $user = $request->user('sanctum');
        $canAccess = $this->contentAccessService->isPubliclyAccessible($magazine)
            || ($user instanceof User && $this->contentAccessService->canAccess(
                $user,
                $magazine,
                SubscriptionContentAccessService::MODULE_MAGAZINES,
            ));

        if (! $canAccess) {
            return response()->json(['success' => false, 'message' => 'Subscription required.', 'data' => [
                'access' => ['is_publicly_accessible' => false, 'requires_subscription' => true],
            ]], 403);
        }

        if (! $inline && ! $magazine->is_downloadable) {
            return response()->json(['success' => false, 'message' => 'Magazine download is not available.'], 403);
        }

        $location = $this->mediaStorageService->resolve(MediaStorageLocation::MEDIA_MAGAZINE, $magazine->id);

        if (! $location) {
            return response()->json(['success' => false, 'message' => 'Magazine PDF is not available.'], 404);
        }

        try {
            $stream = $this->mediaStorageService->openReadStream($location);
        } catch (RuntimeException) {
            return response()->json(['success' => false, 'message' => 'Magazine PDF is not available.'], 404);
        }

        $fileName = filled($location->file_name) ? basename($location->file_name) : 'magazine.pdf';
        $mimeType = in_array($location->mime_type, ['application/pdf', 'application/x-pdf'], true) ? $location->mime_type : 'application/pdf';
        $disposition = HeaderUtils::makeDisposition($inline ? HeaderUtils::DISPOSITION_INLINE : HeaderUtils::DISPOSITION_ATTACHMENT, $fileName, 'magazine.pdf');

        return response()->stream(function () use ($stream): void {
            try {
                fpassthru($stream);
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
        }, 200, ['Content-Type' => $mimeType, 'Content-Disposition' => $disposition, 'X-Content-Type-Options' => 'nosniff']);
    }

    /** @return array<string, mixed> */
    private function magazineData(Magazine $magazine, bool $includePdf = false, ?bool $canAccess = null): array
    {
        $data = [
            'id' => $magazine->id, 'title' => $magazine->title, 'issue_number' => $magazine->issue_number,
            'description' => $magazine->description, 'cover_image_url' => $this->assetUrl($magazine->cover_image),
            'published_date' => $magazine->publish_date?->toDateString(), 'is_free' => (bool) $magazine->isFree,
            'free_until' => $magazine->free_until?->toDateString(), 'is_downloadable' => (bool) $magazine->is_downloadable,
            'view_count' => $magazine->show_visit_counter ? (int) $magazine->site_visits_count : null,
            'authors' => $magazine->authors->map(fn (Author $author): array => ['id' => $author->id, 'name' => $author->name, 'picture_url' => $this->assetUrl($author->picture)])->values(),
            'categories' => $magazine->categories->map(fn (Category $category): array => ['id' => $category->id, 'name' => $category->name])->values(),
            'tags' => $magazine->tags->map(fn (Tags $tag): array => ['id' => $tag->id, 'name' => $tag->name])->values(),
            'detail_url' => route('api.magazines.show', $magazine->id),
        ];

        if ($includePdf) {
            $isPublic = $this->contentAccessService->isPubliclyAccessible($magazine);
            $canAccess ??= $isPublic;
            $available = $this->mediaStorageService->resolve(MediaStorageLocation::MEDIA_MAGAZINE, $magazine->id) !== null;
            $data['access'] = [
                'is_publicly_accessible' => $isPublic,
                'requires_subscription' => ! $isPublic,
                'can_access' => $canAccess,
            ];
            $data['pdf'] = [
                'available' => $available,
                'read_url' => $available && $canAccess ? route('api.magazines.pdf', $magazine->id) : null,
                'download_url' => $available && $canAccess && $magazine->is_downloadable ? route('api.magazines.download', $magazine->id) : null,
            ];
        }

        return $data;
    }

    /** @return array<string, mixed> */
    private function placementData(TazaShumaraArticle $placement): array
    {
        /** @var Article $article */
        $article = $placement->article;

        return [
            'id' => $article->id, 'title' => $article->title, 'short_description' => $article->short_description,
            'image_url' => $this->assetUrl($article->image), 'published_date' => $article->publish_date?->toDateString(),
            'is_free' => (bool) $article->isFree, 'free_until' => $article->free_until?->toDateString(),
            'detail_url' => route('api.articles.show', $article->id), 'layout' => $placement->display_width,
            'position' => $placement->position, 'sort_order' => $placement->sort_order,
        ];
    }

    /** @return array<string, mixed> */
    private function relatedData(Magazine $magazine): array
    {
        return [
            'id' => $magazine->id, 'title' => $magazine->title, 'issue_number' => $magazine->issue_number,
            'published_date' => $magazine->publish_date?->toDateString(), 'cover_image_url' => $this->assetUrl($magazine->cover_image),
            'is_free' => (bool) $magazine->isFree, 'free_until' => $magazine->free_until?->toDateString(),
            'detail_url' => route('api.magazines.show', $magazine->id),
        ];
    }

    private function assetUrl(?string $path): ?string
    {
        return filled($path) ? asset(ltrim(str_replace('\\', '/', $path), '/')) : null;
    }
}
