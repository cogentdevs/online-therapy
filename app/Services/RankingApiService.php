<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Magazine;
use App\Models\SearchContent;
use App\Models\SiteVisit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class RankingApiService
{
    public const LIMIT = 4;

    public function __construct(
        private readonly ArticleApiService $articleApiService,
        private readonly MagazineApiService $magazineApiService,
    ) {}

    /** @return Collection<int, Article|Magazine> */
    public function ranked(string $metric, string $type): Collection
    {
        $query = $type === 'articles'
            ? $this->articleApiService->eligibleQuery()->select(['articles.id', 'articles.title', 'articles.publish_date', 'articles.image'])
            : $this->magazineApiService->eligibleQuery()->select(['magazines.id', 'magazines.title', 'magazines.issue_number', 'magazines.publish_date', 'magazines.cover_image']);
        $relation = $metric === 'viewed' ? 'siteVisits' : 'searchContents';
        $content = $type === 'articles' ? new Article : new Magazine;
        $contentTable = $content->getTable();
        $countQuery = $metric === 'viewed'
            ? SiteVisit::query()->selectRaw('COUNT(*)')
                ->whereColumn('visitable_id', $contentTable.'.id')
                ->where('visitable_type', $content->getMorphClass())
            : SearchContent::query()->selectRaw('COUNT(*)')
                ->whereColumn('content_id', $contentTable.'.id')
                ->where('content_type', $content->getMorphClass());

        return $query
            ->when($metric === 'searched', fn (Builder $query): Builder => $query->has($relation))
            ->withCount($relation)
            ->reorder()
            ->orderByDesc($countQuery)
            ->orderByDesc($type === 'articles' ? 'articles.id' : 'magazines.id')
            ->limit(self::LIMIT)
            ->get();
    }
}
