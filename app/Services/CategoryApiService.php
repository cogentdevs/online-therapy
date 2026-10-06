<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Magazine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CategoryApiService
{
    public function __construct(
        private readonly ArticleApiService $articleApiService,
        private readonly MagazineApiService $magazineApiService,
    ) {}

    private function language(): string
    {
        return config('content_language.code');
    }

    public function paginate(): LengthAwarePaginator
    {
        return $this->publicQuery(withContentCounts: true)
            ->orderBy('categories.id')
            ->paginate(16, ['categories.id', 'categories.name', 'categories.image'])
            ->withQueryString();
    }

    public function findEligible(int $categoryId): Category
    {
        return $this->publicQuery(withContentCounts: true)
            ->findOrFail($categoryId, ['categories.id', 'categories.name', 'categories.image']);
    }

    /**
     * @param  array{type?: string, search?: ?string, year?: ?int, page?: ?int}  $filters
     * @return array{items: Collection<int, Article|Magazine>, years: Collection<int, int>, pagination: Paginator}
     */
    public function content(Category $category, array $filters): array
    {
        $type = $filters['type'] ?? 'magazines';
        $search = trim((string) ($filters['search'] ?? ''));
        $query = $type === 'articles'
            ? $this->articleQuery($category, $search)
            : $this->magazineQuery($category, $search);
        $table = $type === 'articles' ? 'articles' : 'magazines';
        $yearExpression = $this->yearExpression($table);
        $years = (clone $query)
            ->selectRaw($yearExpression.' as publication_year')
            ->distinct()
            ->orderByDesc('publication_year')
            ->pluck('publication_year')
            ->map(fn (mixed $year): int => (int) $year)
            ->values();

        $selectedYear = isset($filters['year']) ? (int) $filters['year'] : null;
        $contentYears = $selectedYear
            ? $years->filter(fn (int $year): bool => $year === $selectedYear)->values()
            : $years;
        $page = max(1, (int) ($filters['page'] ?? 1));
        $visibleYears = $contentYears->forPage($page, 4)->values();
        $pagination = new Paginator(
            $visibleYears,
            $contentYears->count(),
            4,
            $page,
            ['path' => request()->url(), 'query' => request()->query()],
        );
        $items = collect();

        if ($visibleYears->isNotEmpty()) {
            $items = $query
                ->whereIn(DB::raw($yearExpression), $visibleYears->map(fn (int $year): string => (string) $year)->all())
                ->orderByDesc($table.'.publish_date')
                ->orderByDesc($table.'.id')
                ->get();
        }

        return compact('items', 'years', 'pagination');
    }

    public function banner(): ?Banner
    {
        return Banner::query()
            ->where('language', $this->language())
            ->where('type', 'full')
            ->where('position', 'category-detail-top-full')
            ->where('isActive', true)
            ->latest('id')
            ->first(['id', 'image']);
    }

    private function publicQuery(bool $withContentCounts = false): Builder
    {
        return Category::query()
            ->where('categories.language', $this->language())
            ->where('categories.isActive', true)
            ->whereNotNull('categories.name')
            ->where('categories.name', '!=', '')
            ->when($withContentCounts, fn (Builder $query): Builder => $query->withCount([
                'articles as public_articles_count' => fn (Builder $query): Builder|Relation => $this->articleApiService->applyEligibility($query),
                'magazines as public_magazines_count' => fn (Builder $query): Builder|Relation => $this->magazineApiService->applyEligibility($query),
            ]));
    }

    private function articleQuery(Category $category, string $search): Builder
    {
        return $this->articleApiService->eligibleQuery()
            ->whereHas('categories', fn (Builder $query): Builder => $query->whereKey($category->id))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $pattern = '%'.$search.'%';
                $query->where(fn (Builder $query): Builder => $query
                    ->where('articles.title', 'like', $pattern)
                    ->orWhere('articles.short_description', 'like', $pattern)
                    ->orWhere('articles.issue_number', 'like', $pattern));
            })
            ->withCount('siteVisits')
            ->with([
                'authors' => fn (Relation $query): Relation => $query->where('isActive', true)->orderBy('name')->select(['authors.id', 'name', 'picture']),
                'categories' => fn (Relation $query): Relation => $query->where('categories.language', $this->language())->where('categories.isActive', true)->orderBy('name')->select(['categories.id', 'name']),
                'tags' => fn (Relation $query): Relation => $query->where('tags.language', $this->language())->where('tags.isActive', true)->orderBy('name')->select(['tags.id', 'name']),
            ])
            ->select(['articles.id', 'articles.title', 'articles.issue_number', 'articles.short_description', 'articles.image', 'articles.publish_date', 'articles.isFree', 'articles.free_until', 'articles.show_visit_counter']);
    }

    private function magazineQuery(Category $category, string $search): Builder
    {
        return $this->magazineApiService->eligibleQuery()
            ->whereHas('categories', fn (Builder $query): Builder => $query->whereKey($category->id))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $pattern = '%'.$search.'%';
                $query->where(fn (Builder $query): Builder => $query
                    ->where('magazines.title', 'like', $pattern)
                    ->orWhere('magazines.description', 'like', $pattern)
                    ->orWhere('magazines.issue_number', 'like', $pattern));
            })
            ->withCount('siteVisits')
            ->with([
                'authors' => fn (Relation $query): Relation => $query->where('isActive', true)->orderBy('name')->select(['authors.id', 'name', 'picture']),
                'categories' => fn (Relation $query): Relation => $query->where('categories.language', $this->language())->where('categories.isActive', true)->orderBy('name')->select(['categories.id', 'name']),
                'tags' => fn (Relation $query): Relation => $query->where('tags.language', $this->language())->where('tags.isActive', true)->orderBy('name')->select(['tags.id', 'name']),
            ])
            ->select(['magazines.id', 'magazines.title', 'magazines.issue_number', 'magazines.description', 'magazines.cover_image', 'magazines.publish_date', 'magazines.isFree', 'magazines.free_until', 'magazines.is_downloadable', 'magazines.show_visit_counter']);
    }

    private function yearExpression(string $table): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y', {$table}.publish_date)",
            'pgsql' => "EXTRACT(YEAR FROM {$table}.publish_date)",
            default => "YEAR({$table}.publish_date)",
        };
    }
}
