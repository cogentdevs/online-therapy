<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RankingIndexRequest;
use App\Models\Article;
use App\Models\Magazine;
use App\Services\RankingApiService;
use Illuminate\Http\JsonResponse;

class RankingController extends Controller
{
    public function __construct(private readonly RankingApiService $rankingApiService) {}

    public function index(RankingIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $items = $this->rankingApiService->ranked($validated['metric'], $validated['type']);

        return response()->json([
            'success' => true,
            'data' => [
                'metric' => $validated['metric'],
                'type' => $validated['type'],
                'period' => ['type' => 'all_time', 'start_date' => null, 'end_date' => null],
                'limit' => RankingApiService::LIMIT,
                'items' => $items->map(fn (Article|Magazine $item): array => $this->item($item, $validated['metric']))->values(),
            ],
        ]);
    }

    /** @return array<string, mixed> */
    private function item(Article|Magazine $item, string $metric): array
    {
        $isArticle = $item instanceof Article;

        return [
            'id' => $item->id,
            'title' => $item->title,
            'issue_number' => $isArticle ? null : $item->issue_number,
            'image_url' => $this->assetUrl($isArticle ? $item->image : $item->cover_image),
            'published_date' => $item->publish_date?->toDateString(),
            'count' => (int) $item->getAttribute($metric === 'viewed' ? 'site_visits_count' : 'search_contents_count'),
            'detail_url' => route($isArticle ? 'api.articles.show' : 'api.magazines.show', $item->id),
        ];
    }

    private function assetUrl(?string $path): ?string
    {
        return filled($path) ? asset(ltrim(str_replace('\\', '/', $path), '/')) : null;
    }
}
