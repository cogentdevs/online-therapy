<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Tags;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ArticleApiService
{
    private function language(): string
    {
        return config('content_language.code');
    }

    /**
     * @param  array{search?: ?string, author_id?: ?int, category_id?: ?int, tag_id?: ?int, year?: ?int, page?: ?int, per_page?: ?int}  $filters
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return $this->eligibleQuery()
            ->select([
                'articles.id', 'articles.magazine_id', 'articles.title', 'articles.issue_number',
                'articles.publish_date', 'articles.short_description', 'articles.image',
                'articles.isFree', 'articles.free_until', 'articles.show_visit_counter',
            ])
            ->withCount('siteVisits')
            ->with($this->publicRelations())
            ->when($filters['author_id'] ?? null, fn (Builder $query, int $id): Builder => $query->whereHas('authors', fn (Builder $relation): Builder => $relation->whereKey($id)))
            ->when($filters['category_id'] ?? null, fn (Builder $query, int $id): Builder => $query->whereHas('categories', fn (Builder $relation): Builder => $relation->whereKey($id)))
            ->when($filters['tag_id'] ?? null, fn (Builder $query, int $id): Builder => $query->whereHas('tags', fn (Builder $relation): Builder => $relation->whereKey($id)))
            ->when($filters['year'] ?? null, fn (Builder $query, int $year): Builder => $query->whereYear('articles.publish_date', $year))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $pattern = '%'.$search.'%';

                $query->where(function (Builder $searchQuery) use ($pattern): void {
                    $searchQuery->where('articles.title', 'like', $pattern)
                        ->orWhere('articles.short_description', 'like', $pattern)
                        ->orWhere('articles.issue_number', 'like', $pattern)
                        ->orWhereHas('authors', fn (Builder $authorQuery): Builder => $authorQuery
                            ->where('isActive', true)
                            ->where('name', 'like', $pattern));
                });
            })
            ->orderByDesc('articles.publish_date')
            ->orderByDesc('articles.id')
            ->paginate((int) ($filters['per_page'] ?? 8))
            ->withQueryString();
    }

    public function findEligible(int $articleId): Article
    {
        return $this->eligibleQuery()
            ->withCount('siteVisits')
            ->with([
                ...$this->publicRelations(),
                'relatedArticles' => fn (Relation $query): Builder|Relation => $this->applyEligibility($query)
                    ->whereKeyNot($articleId)
                    ->orderByDesc('articles.publish_date')
                    ->orderByDesc('articles.id')
                    ->limit(4)
                    ->select(['articles.id', 'articles.title', 'articles.publish_date', 'articles.image', 'articles.isFree', 'articles.free_until']),
            ])
            ->findOrFail($articleId);
    }

    /** @return array{authors: Collection<int, Author>, categories: Collection<int, Category>, tags: Collection<int, Tags>, years: Collection<int, int>} */
    public function filterOptions(): array
    {
        $eligibleArticles = fn (Builder $query): Builder => $this->applyEligibility($query);
        $authors = Author::query()->where('isActive', true)
            ->whereHas('articles', $eligibleArticles)->orderBy('name')->get(['id', 'name', 'picture']);
        $categories = Category::query()->where('language', $this->language())->where('isActive', true)
            ->whereHas('articles', $eligibleArticles)->orderBy('name')->get(['id', 'name']);
        $tags = Tags::query()->where('language', $this->language())->where('isActive', true)
            ->whereHas('articles', $eligibleArticles)->orderBy('name')->get(['id', 'name']);
        $years = $this->eligibleQuery()->selectRaw($this->publicationYearExpression().' as publication_year')
            ->distinct()->orderByDesc('publication_year')->pluck('publication_year')
            ->map(fn (mixed $year): int => (int) $year);

        return compact('authors', 'categories', 'tags', 'years');
    }

    public function eligibleQuery(): Builder
    {
        return $this->applyEligibility(Article::query());
    }

    public function applyEligibility(Builder|Relation $query): Builder|Relation
    {
        return $query->where('articles.language', $this->language())
            ->where('articles.isActive', true)
            ->where('articles.status', Article::STATUS_PUBLISHED)
            ->whereNotNull('articles.publish_date')
            ->whereDate('articles.publish_date', '<=', today())
            ->where(function (Builder $query): void {
                $query->whereNull('articles.published_at')->orWhere('articles.published_at', '<=', now());
            });
    }

    /** @return array<string, callable> */
    private function publicRelations(): array
    {
        return [
            'magazine' => fn (Relation $query): Relation => $query->select(['id', 'title', 'issue_number', 'publish_date', 'cover_image']),
            'authors' => fn (Relation $query): Relation => $query->where('isActive', true)->orderBy('name')->select(['authors.id', 'name', 'picture']),
            'categories' => fn (Relation $query): Relation => $query->where('categories.language', $this->language())->where('categories.isActive', true)->orderBy('name')->select(['categories.id', 'name']),
            'tags' => fn (Relation $query): Relation => $query->where('tags.language', $this->language())->where('tags.isActive', true)->orderBy('name')->select(['tags.id', 'name']),
        ];
    }

    private function publicationYearExpression(): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y', articles.publish_date)",
            'pgsql' => 'EXTRACT(YEAR FROM articles.publish_date)',
            default => 'YEAR(articles.publish_date)',
        };
    }
}
