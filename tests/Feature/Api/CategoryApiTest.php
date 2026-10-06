<?php

use App\Models\Article;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Magazine;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

function apiCategory(string $name, array $overrides = []): Category
{
    return Category::query()->create([
        'language' => 'ur',
        'name' => $name,
        'image' => 'images/backend-images/categories/category.webp',
        'isActive' => true,
        ...$overrides,
    ]);
}

function apiCategoryArticle(string $title, string $date, array $overrides = []): Article
{
    return Article::query()->forceCreate([
        'language' => 'ur',
        'title' => $title,
        'article' => '<p>Protected full body</p>',
        'short_description' => 'Public summary',
        'publish_date' => $date,
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
        ...$overrides,
    ]);
}

function apiCategoryMagazine(string $title, string $date, array $overrides = []): Magazine
{
    return Magazine::query()->forceCreate([
        'language' => 'ur',
        'title' => $title,
        'description' => 'Public magazine summary',
        'publish_date' => $date,
        'isActive' => true,
        'status' => Magazine::STATUS_PUBLISHED,
        ...$overrides,
    ]);
}

test('category index is public paginated and returns only active Urdu named categories in id order', function () {
    $categories = collect(range(1, 17))->map(fn (int $number): Category => apiCategory("Category {$number}"));
    apiCategory('Inactive', ['isActive' => false]);
    apiCategory('English', ['language' => 'en']);
    apiCategory('', ['name' => '']);

    $response = $this->getJson('/api/categories');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(16, 'data.categories')
        ->assertJsonPath('data.categories.0.id', $categories->first()->id)
        ->assertJsonPath('data.categories.0.image_url', asset('images/backend-images/categories/category.webp'))
        ->assertJsonPath('data.categories.0.detail_url', route('api.categories.show', $categories->first()->id))
        ->assertJsonPath('data.pagination.per_page', 16)
        ->assertJsonPath('data.pagination.total', 17)
        ->assertJsonMissing(['created_by' => null])
        ->assertJsonMissing(['updated_by' => null]);

    $this->getJson('/api/categories?page=2')
        ->assertOk()
        ->assertJsonCount(1, 'data.categories')
        ->assertJsonPath('data.pagination.current_page', 2);
});

test('category counts include only content eligible in the article and magazine APIs', function () {
    $category = apiCategory('Counts');
    $article = apiCategoryArticle('Eligible article', '2026-01-01');
    $draftArticle = apiCategoryArticle('Draft article', '2026-01-02', ['status' => Article::STATUS_DRAFT]);
    $futureArticle = apiCategoryArticle('Future article', '2099-01-01');
    $magazine = apiCategoryMagazine('Eligible magazine', '2026-01-01');
    $inactiveMagazine = apiCategoryMagazine('Inactive magazine', '2026-01-02', ['isActive' => false]);
    $futureMagazine = apiCategoryMagazine('Future magazine', '2099-01-01');
    $category->articles()->attach([$article->id, $draftArticle->id, $futureArticle->id]);
    $category->magazines()->attach([$magazine->id, $inactiveMagazine->id, $futureMagazine->id]);

    $this->getJson('/api/categories')
        ->assertOk()
        ->assertJsonPath('data.categories.0.article_count', 1)
        ->assertJsonPath('data.categories.0.magazine_count', 1);
});

test('category detail defaults to magazines and exposes the current generic category banner safely', function () {
    $category = apiCategory('Detail');
    $magazine = apiCategoryMagazine('Default magazine', '2026-04-01');
    $category->magazines()->attach($magazine);
    Banner::query()->create([
        'language' => 'ur',
        'type' => 'full',
        'position' => 'category-detail-top-full',
        'image' => 'images/backend-images/banners/category.webp',
        'isActive' => true,
    ]);

    $this->getJson("/api/categories/{$category->id}")
        ->assertOk()
        ->assertJsonPath('data.content_type', 'magazines')
        ->assertJsonPath('data.category.id', $category->id)
        ->assertJsonPath('data.category.banner_image_url', asset('images/backend-images/banners/category.webp'))
        ->assertJsonPath('data.items.0.id', $magazine->id)
        ->assertJsonPath('data.pagination.unit', 'publication_year');

    $this->getJson('/api/categories/999999')->assertNotFound();
    $inactive = apiCategory('Inactive detail', ['isActive' => false]);
    $english = apiCategory('English detail', ['language' => 'en']);
    $this->getJson("/api/categories/{$inactive->id}")->assertNotFound();
    $this->getJson("/api/categories/{$english->id}")->assertNotFound();
});

test('articles mode is category scoped searchable year filtered and does not leak full content', function () {
    $category = apiCategory('Articles');
    $otherCategory = apiCategory('Other');
    $matching = apiCategoryArticle('Matching 2026 article', '2026-02-01', ['isFree' => false]);
    $older = apiCategoryArticle('Matching 2025 article', '2025-02-01');
    $unrelated = apiCategoryArticle('Matching unrelated article', '2026-03-01');
    $draft = apiCategoryArticle('Matching draft article', '2026-04-01', ['status' => Article::STATUS_DRAFT]);
    $category->articles()->attach([$matching->id, $older->id, $draft->id]);
    $otherCategory->articles()->attach($unrelated);

    $response = $this->getJson("/api/categories/{$category->id}?type=articles&search=Matching&year=2026");

    $response->assertOk()
        ->assertJsonPath('data.content_type', 'articles')
        ->assertJsonCount(1, 'data.items')
        ->assertJsonPath('data.items.0.id', $matching->id)
        ->assertJsonPath('data.items.0.detail_url', route('api.articles.show', $matching->id))
        ->assertJsonPath('data.filters.years', [2026, 2025])
        ->assertJsonPath('data.filters.selected_year', 2026)
        ->assertJsonMissing(['article' => '<p>Protected full body</p>'])
        ->assertJsonMissingPath('data.items.0.content');
});

test('magazines mode paginates four complete year groups and never exposes a PDF path', function () {
    $category = apiCategory('Magazines');

    foreach ([2026, 2025, 2024, 2023, 2022] as $year) {
        $magazine = apiCategoryMagazine("Magazine {$year}", "{$year}-01-01", ['is_downloadable' => true]);
        $category->magazines()->attach($magazine);
    }

    $this->getJson("/api/categories/{$category->id}?type=magazines")
        ->assertOk()
        ->assertJsonCount(4, 'data.items')
        ->assertJsonPath('data.pagination.total', 5)
        ->assertJsonPath('data.pagination.per_page', 4)
        ->assertJsonPath('data.pagination.unit', 'publication_year')
        ->assertJsonMissingPath('data.items.0.pdf')
        ->assertJsonMissingPath('data.items.0.pdf_path');

    $this->getJson("/api/categories/{$category->id}?type=magazines&page=2")
        ->assertOk()
        ->assertJsonCount(1, 'data.items')
        ->assertJsonPath('data.items.0.title', 'Magazine 2022');
});

test('detail search and year filters use AND semantics and empty valid years are safe', function () {
    $category = apiCategory('Filters');
    $matching = apiCategoryMagazine('Economy special', '2026-01-01');
    $wrongYear = apiCategoryMagazine('Economy old', '2025-01-01');
    $wrongSearch = apiCategoryMagazine('Health special', '2026-02-01');
    $category->magazines()->attach([$matching->id, $wrongYear->id, $wrongSearch->id]);

    $this->getJson("/api/categories/{$category->id}?type=magazines&search=Economy&year=2026")
        ->assertOk()
        ->assertJsonCount(1, 'data.items')
        ->assertJsonPath('data.items.0.id', $matching->id);

    $this->getJson("/api/categories/{$category->id}?type=magazines&year=1999")
        ->assertOk()
        ->assertJsonCount(0, 'data.items');
});

test('category API validates supported parameters and has no alternate prefixes', function () {
    $category = apiCategory('Validation');

    $this->getJson("/api/categories/{$category->id}?type=invalid")->assertUnprocessable();
    $this->getJson("/api/categories/{$category->id}?year=abc")->assertUnprocessable();
    $this->getJson('/api/categories?page=0')->assertUnprocessable();
    $this->getJson('/api/v1/categories')->assertNotFound();
    $this->getJson('/api/api/categories')->assertNotFound();
});
