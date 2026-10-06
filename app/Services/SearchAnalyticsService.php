<?php

namespace App\Services;

use App\Models\SearchContent;

class SearchAnalyticsService
{
    public function __construct(
        private readonly ArticleApiService $articleApiService,
        private readonly MagazineApiService $magazineApiService,
    ) {}

    public function track(string $type, int $contentId, string $keyword): void
    {
        $content = $type === 'article'
            ? $this->articleApiService->eligibleQuery()->findOrFail($contentId)
            : $this->magazineApiService->eligibleQuery()->findOrFail($contentId);

        SearchContent::query()->create([
            'content_type' => $content->getMorphClass(),
            'content_id' => $content->getKey(),
            'search_keyword' => $keyword,
            'created_at' => now(),
        ]);
    }
}
