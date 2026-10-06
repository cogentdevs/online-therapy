<?php

namespace App\Services;

use App\Models\Author;
use App\Models\AuthorGeneralSetting;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AuthorApiService
{
    public function __construct(private readonly ArticleApiService $articleApiService) {}

    /** @param array{search?: ?string, page?: ?int} $filters */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return $this->publicQuery()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $pattern = '%'.$search.'%';
                $query->where(fn (Builder $query): Builder => $query
                    ->where('name', 'like', $pattern)
                    ->orWhere('speciality', 'like', $pattern)
                    ->orWhere('qualification', 'like', $pattern));
            })
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(12)
            ->withQueryString();
    }

    public function findEligible(int $authorId): Author
    {
        return $this->publicQuery()->findOrFail($authorId);
    }

    /**
     * @param  array{search?: ?string, category_id?: ?int, year?: ?int, page?: ?int}  $filters
     */
    public function articles(Author $author, array $filters): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return $this->eligibleArticleQuery($author)
            ->when($filters['category_id'] ?? null, fn (Builder $query, int $categoryId): Builder => $query
                ->whereHas('categories', fn (Builder $categoryQuery): Builder => $categoryQuery->whereKey($categoryId)))
            ->when($filters['year'] ?? null, fn (Builder $query, int $year): Builder => $query->whereYear('articles.publish_date', $year))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $pattern = '%'.$search.'%';
                $query->where(fn (Builder $query): Builder => $query
                    ->where('articles.title', 'like', $pattern)
                    ->orWhere('articles.short_description', 'like', $pattern));
            })
            ->withCount('siteVisits')
            ->with([
                'authors' => fn (Relation $query): Relation => $query->where('isActive', true)->orderBy('name')->select(['authors.id', 'name', 'picture']),
                'categories' => fn (Relation $query): Relation => $query->where('categories.language', ArticleApiService::LANGUAGE)->where('categories.isActive', true)->orderBy('name')->select(['categories.id', 'name']),
                'tags' => fn (Relation $query): Relation => $query->where('tags.language', ArticleApiService::LANGUAGE)->where('tags.isActive', true)->orderBy('name')->select(['tags.id', 'name']),
            ])
            ->orderByDesc('articles.publish_date')
            ->orderByDesc('articles.id')
            ->paginate(12, [
                'articles.id', 'articles.title', 'articles.issue_number', 'articles.publish_date',
                'articles.short_description', 'articles.image', 'articles.isFree',
                'articles.free_until', 'articles.show_visit_counter',
            ])
            ->withQueryString();
    }

    /** @return Collection<int, Category> */
    public function categories(Author $author): Collection
    {
        $eligibleAuthorArticles = fn (Builder $query): Builder|Relation => $this->articleApiService
            ->applyEligibility($query)
            ->whereHas('authors', fn (Builder $authorQuery): Builder => $authorQuery->whereKey($author->id));

        return Category::query()
            ->where('language', ArticleApiService::LANGUAGE)
            ->where('isActive', true)
            ->whereHas('articles', $eligibleAuthorArticles)
            ->withCount(['articles as author_articles_count' => $eligibleAuthorArticles])
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /** @return Collection<int, object{publication_year: mixed, article_count: int}> */
    public function years(Author $author): Collection
    {
        return $this->eligibleArticleQuery($author)
            ->selectRaw($this->yearExpression().' as publication_year, COUNT(*) as article_count')
            ->groupBy(DB::raw($this->yearExpression()))
            ->orderByDesc('publication_year')
            ->get();
    }

    /** @return Collection<int, Author> */
    public function otherAuthors(Author $author): Collection
    {
        return $this->publicQuery()
            ->whereKeyNot($author->id)
            ->orderBy('name')
            ->limit(5)
            ->get();
    }

    /** @return array<string, bool> */
    public function defaultVisibility(): array
    {
        $storedSettings = AuthorGeneralSetting::query()
            ->whereIn('column_name', AuthorGeneralSetting::configurableFields())
            ->get(['column_name', 'isView'])
            ->keyBy('column_name');
        $visibility = [];

        foreach (AuthorGeneralSetting::DEFAULT_VISIBILITY as $field => $defaultValue) {
            $visibility[$field] = (bool) ($storedSettings->get($field)?->isView ?? $defaultValue);
        }

        return $visibility;
    }

    private function publicQuery(): Builder
    {
        return Author::query()
            ->where('isActive', true)
            ->with(['visibilities' => fn (Relation $query): Relation => $query
                ->whereIn('column_name', AuthorGeneralSetting::configurableFields())
                ->select(['id', 'author_id', 'column_name', 'isView'])]);
    }

    private function eligibleArticleQuery(Author $author): Builder
    {
        return $this->articleApiService->eligibleQuery()
            ->whereHas('authors', fn (Builder $query): Builder => $query->whereKey($author->id));
    }

    private function yearExpression(): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y', articles.publish_date)",
            'pgsql' => 'EXTRACT(YEAR FROM articles.publish_date)',
            default => 'YEAR(articles.publish_date)',
        };
    }
}
