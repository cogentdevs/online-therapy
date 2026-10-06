<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Magazine;
use App\Models\Tags;
use App\Models\TazaShumara;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MagazineApiService
{
    private function language(): string
    {
        return config('content_language.code');
    }

    /** @param array<string, mixed> $filters */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return $this->eligibleQuery()->select([
            'magazines.id', 'magazines.title', 'magazines.issue_number', 'magazines.publish_date',
            'magazines.cover_image', 'magazines.description', 'magazines.isFree', 'magazines.free_until',
            'magazines.is_downloadable', 'magazines.show_visit_counter',
        ])->withCount('siteVisits')->with($this->publicRelations())
            ->when($filters['author_id'] ?? null, fn (Builder $query, int $id): Builder => $query->whereHas('authors', fn (Builder $relation): Builder => $relation->whereKey($id)))
            ->when($filters['category_id'] ?? null, fn (Builder $query, int $id): Builder => $query->whereHas('categories', fn (Builder $relation): Builder => $relation->whereKey($id)))
            ->when($filters['tag_id'] ?? null, fn (Builder $query, int $id): Builder => $query->whereHas('tags', fn (Builder $relation): Builder => $relation->whereKey($id)))
            ->when($filters['year'] ?? null, fn (Builder $query, int $year): Builder => $query->whereYear('magazines.publish_date', $year))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $pattern = '%'.$search.'%';
                $query->where(function (Builder $searchQuery) use ($pattern): void {
                    $searchQuery->where('magazines.title', 'like', $pattern)
                        ->orWhere('magazines.description', 'like', $pattern)
                        ->orWhere('magazines.issue_number', 'like', $pattern)
                        ->orWhereHas('authors', fn (Builder $authorQuery): Builder => $authorQuery->where('isActive', true)->where('name', 'like', $pattern));
                });
            })->orderByDesc('magazines.publish_date')->orderByDesc('magazines.id')
            ->paginate((int) ($filters['per_page'] ?? 12))->withQueryString();
    }

    public function findEligible(int $id): Magazine
    {
        return $this->eligibleQuery()->withCount('siteVisits')->with([
            ...$this->publicRelations(),
            'relatedMagazines' => fn (Relation $query): Builder|Relation => $this->applyEligibility($query)
                ->whereKeyNot($id)->orderByDesc('magazines.publish_date')->orderByDesc('magazines.id')->limit(4)
                ->select(['magazines.id', 'magazines.title', 'magazines.issue_number', 'magazines.publish_date', 'magazines.cover_image', 'magazines.isFree', 'magazines.free_until', 'magazines.is_downloadable']),
        ])->findOrFail($id);
    }

    public function current(): ?TazaShumara
    {
        return TazaShumara::query()->where('language', $this->language())->where('is_active', true)
            ->with([
                'magazine' => fn (Relation $query): Builder|Relation => $this->applyEligibility($query)->withCount('siteVisits')->with($this->publicRelations()),
                'articlePlacements' => fn (Relation $query): Relation => $query
                    ->select(['id', 'taza_shumara_id', 'article_id', 'display_width', 'position', 'sort_order'])
                    ->with(['article' => fn (Relation $articleQuery): Relation => $articleQuery
                        ->where('language', $this->language())->where('isActive', true)
                        ->where('status', Article::STATUS_PUBLISHED)
                        ->select(['id', 'title', 'publish_date', 'short_description', 'image', 'isFree', 'free_until'])])
                    ->orderByRaw("CASE position WHEN 'top' THEN 1 WHEN 'center' THEN 2 ELSE 3 END")
                    ->orderBy('sort_order')->orderBy('id'),
            ])->first();
    }

    /** @return array{authors: Collection, categories: Collection, tags: Collection, years: Collection} */
    public function filterOptions(): array
    {
        $eligible = fn (Builder $query): Builder|Relation => $this->applyEligibility($query);
        $authors = Author::query()->where('isActive', true)->whereHas('magazines', $eligible)->orderBy('name')->get(['id', 'name', 'picture']);
        $categories = Category::query()->where('language', $this->language())->where('isActive', true)->whereHas('magazines', $eligible)->orderBy('name')->get(['id', 'name']);
        $tags = Tags::query()->where('language', $this->language())->where('isActive', true)->whereHas('magazines', $eligible)->orderBy('name')->get(['id', 'name']);
        $years = $this->eligibleQuery()->selectRaw($this->yearExpression().' as publication_year')->distinct()->orderByDesc('publication_year')->pluck('publication_year')->map(fn (mixed $year): int => (int) $year);

        return compact('authors', 'categories', 'tags', 'years');
    }

    public function eligibleQuery(): Builder
    {
        return $this->applyEligibility(Magazine::query());
    }

    public function applyEligibility(Builder|Relation $query): Builder|Relation
    {
        return $query->where('magazines.language', $this->language())->where('magazines.isActive', true)
            ->where('magazines.status', Magazine::STATUS_PUBLISHED)->whereNotNull('magazines.publish_date')
            ->whereDate('magazines.publish_date', '<=', today())
            ->where(fn (Builder $query): Builder => $query->whereNull('magazines.published_at')->orWhere('magazines.published_at', '<=', now()));
    }

    /** @return array<string, callable> */
    private function publicRelations(): array
    {
        return [
            'authors' => fn (Relation $query): Relation => $query->where('isActive', true)->orderBy('name')->select(['authors.id', 'name', 'picture']),
            'categories' => fn (Relation $query): Relation => $query->where('categories.language', $this->language())->where('categories.isActive', true)->orderBy('name')->select(['categories.id', 'name']),
            'tags' => fn (Relation $query): Relation => $query->where('tags.language', $this->language())->where('tags.isActive', true)->orderBy('name')->select(['tags.id', 'name']),
        ];
    }

    private function yearExpression(): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y', magazines.publish_date)",
            'pgsql' => 'EXTRACT(YEAR FROM magazines.publish_date)',
            default => 'YEAR(magazines.publish_date)',
        };
    }
}
