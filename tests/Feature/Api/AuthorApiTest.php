<?php

use App\Models\Article;
use App\Models\Author;
use App\Models\AuthorGeneralSetting;
use App\Models\Category;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

function apiAuthor(array $attributes = []): Author
{
    return Author::query()->forceCreate([
        'name' => 'Author Name',
        'contact_number' => '03001234567',
        'email' => 'author@example.test',
        'qualification' => 'PhD',
        'experience_detail' => 'Research experience',
        'experience_years' => 10,
        'speciality' => 'Education',
        'picture' => 'images/backend-images/author/missing.png',
        'isActive' => true,
        ...$attributes,
    ]);
}

function apiAuthorCategory(string $name): Category
{
    return Category::query()->forceCreate(['language' => 'ur', 'name' => $name, 'isActive' => true]);
}

function apiAuthorArticle(Author $author, Category $category, string $title, string $date, array $attributes = []): Article
{
    $article = Article::query()->forceCreate([
        'language' => 'ur',
        'title' => $title,
        'article' => '<p>Protected article body</p>',
        'short_description' => 'Public summary',
        'publish_date' => $date,
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
        ...$attributes,
    ]);
    $article->authors()->attach($author);
    $article->categories()->attach($category);

    return $article;
}

test('author index is public searchable ordered and paginated using current website rules', function () {
    collect(range(1, 13))->each(fn (int $number): Author => apiAuthor([
        'name' => 'Author '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
        'speciality' => $number === 13 ? 'Economics' : 'Education',
    ]));
    apiAuthor(['name' => 'Inactive', 'isActive' => false]);

    $response = $this->getJson('/api/authors');

    $response->assertOk()
        ->assertJsonCount(12, 'data.authors')
        ->assertJsonPath('data.authors.0.name', 'Author 01')
        ->assertJsonPath('data.authors.0.picture_url', asset('images/backend-images/author/author.png'))
        ->assertJsonPath('data.pagination.per_page', 12)
        ->assertJsonPath('data.pagination.total', 13)
        ->assertJsonMissing(['email' => 'author@example.test'])
        ->assertJsonMissing(['contact_number' => '03001234567'])
        ->assertJsonMissing(['created_by' => null]);

    $this->getJson('/api/authors?search=Economics')
        ->assertOk()
        ->assertJsonCount(1, 'data.authors')
        ->assertJsonPath('data.authors.0.name', 'Author 13');
});

test('global visibility and per author overrides govern profile fields without leaking values', function () {
    AuthorGeneralSetting::query()->forceCreate(['column_name' => 'email', 'isView' => false]);
    AuthorGeneralSetting::query()->forceCreate(['column_name' => 'contact_number', 'isView' => true]);
    AuthorGeneralSetting::query()->forceCreate(['column_name' => 'qualification', 'isView' => true]);
    $author = apiAuthor();
    $author->visibilities()->createMany([
        ['column_name' => 'email', 'isView' => true],
        ['column_name' => 'contact_number', 'isView' => false],
        ['column_name' => 'qualification', 'isView' => false],
    ]);

    $this->getJson("/api/authors/{$author->id}")
        ->assertOk()
        ->assertJsonPath('data.author.email', 'author@example.test')
        ->assertJsonPath('data.author.contact_number', null)
        ->assertJsonPath('data.author.qualification', null)
        ->assertJsonMissing(['contact_number' => '03001234567'])
        ->assertJsonMissing(['qualification' => 'PhD']);
});

test('author detail excludes ineligible articles and never exposes article bodies', function () {
    $author = apiAuthor();
    $category = apiAuthorCategory('Education');
    $eligible = apiAuthorArticle($author, $category, 'Eligible', '2026-01-01', ['isFree' => false]);
    apiAuthorArticle($author, $category, 'Draft', '2026-01-02', ['status' => Article::STATUS_DRAFT]);
    apiAuthorArticle($author, $category, 'Inactive', '2026-01-03', ['isActive' => false]);
    apiAuthorArticle($author, $category, 'Future', '2099-01-01');
    apiAuthorArticle($author, $category, 'English', '2026-01-04', ['language' => 'en']);

    $this->getJson("/api/authors/{$author->id}")
        ->assertOk()
        ->assertJsonCount(1, 'data.articles')
        ->assertJsonPath('data.articles.0.id', $eligible->id)
        ->assertJsonPath('data.articles.0.detail_url', route('api.articles.show', $eligible->id))
        ->assertJsonMissingPath('data.articles.0.content')
        ->assertJsonMissing(['article' => '<p>Protected article body</p>'])
        ->assertJsonPath('data.pagination.per_page', 12);
});

test('author article search category and year filters combine with AND semantics', function () {
    $author = apiAuthor();
    $otherAuthor = apiAuthor(['name' => 'Other Author']);
    $education = apiAuthorCategory('Education');
    $health = apiAuthorCategory('Health');
    $matching = apiAuthorArticle($author, $education, 'Education research', '2026-02-01');
    apiAuthorArticle($author, $education, 'Education old', '2025-02-01');
    apiAuthorArticle($author, $health, 'Health research', '2026-02-01');
    apiAuthorArticle($otherAuthor, $education, 'Education research other', '2026-02-01');

    $this->getJson("/api/authors/{$author->id}?search=research&category_id={$education->id}&year=2026")
        ->assertOk()
        ->assertJsonCount(1, 'data.articles')
        ->assertJsonPath('data.articles.0.id', $matching->id)
        ->assertJsonPath('data.filters.selected_category_id', $education->id)
        ->assertJsonPath('data.filters.selected_year', 2026);

    $this->getJson("/api/authors/{$author->id}?category_id=999999")
        ->assertOk()
        ->assertJsonCount(0, 'data.articles');
});

test('filter metadata uses the authors complete eligible article dataset with scoped counts', function () {
    $author = apiAuthor();
    $otherAuthor = apiAuthor(['name' => 'Other']);
    $education = apiAuthorCategory('Education');
    $health = apiAuthorCategory('Health');
    apiAuthorArticle($author, $education, 'One', '2026-01-01');
    apiAuthorArticle($author, $education, 'Two', '2026-02-01');
    apiAuthorArticle($author, $health, 'Three', '2025-01-01');
    apiAuthorArticle($otherAuthor, $education, 'Other article', '2026-01-01');
    apiAuthorArticle($author, $health, 'Draft', '2024-01-01', ['status' => Article::STATUS_DRAFT]);

    $this->getJson("/api/authors/{$author->id}?search=One")
        ->assertOk()
        ->assertJsonPath('data.filters.categories.0.name', 'Education')
        ->assertJsonPath('data.filters.categories.0.article_count', 2)
        ->assertJsonPath('data.filters.categories.0.detail_url', route('api.categories.show', $education->id))
        ->assertJsonPath('data.filters.categories.1.article_count', 1)
        ->assertJsonPath('data.filters.years.0.year', 2026)
        ->assertJsonPath('data.filters.years.0.article_count', 2)
        ->assertJsonPath('data.filters.years.1.year', 2025);
});

test('other authors are active ordered limited visibility safe and exclude selected author', function () {
    $selected = apiAuthor(['name' => 'Selected']);
    AuthorGeneralSetting::query()->forceCreate(['column_name' => 'name', 'isView' => true]);
    $others = collect(range(1, 6))->map(fn (int $number): Author => apiAuthor(['name' => "Other {$number}"]));
    $others->first()->visibilities()->create(['column_name' => 'name', 'isView' => false]);
    apiAuthor(['name' => 'Inactive other', 'isActive' => false]);

    $response = $this->getJson("/api/authors/{$selected->id}");

    $response->assertOk()
        ->assertJsonCount(5, 'data.other_authors')
        ->assertJsonPath('data.other_authors.0.name', null)
        ->assertJsonMissing(['name' => 'Inactive other'])
        ->assertJsonMissing(['email' => 'author@example.test']);

    expect(collect($response->json('data.other_authors'))->pluck('id'))->not->toContain($selected->id);
});

test('inactive and unknown authors are unavailable and query validation is predictable', function () {
    $author = apiAuthor();
    $inactive = apiAuthor(['name' => 'Inactive', 'isActive' => false]);

    $this->getJson('/api/authors/999999')->assertNotFound();
    $this->getJson("/api/authors/{$inactive->id}")->assertNotFound();
    $this->getJson("/api/authors/{$author->id}?category_id=abc")->assertUnprocessable();
    $this->getJson("/api/authors/{$author->id}?year=abc")->assertUnprocessable();
    $this->getJson('/api/authors?page=abc')->assertUnprocessable();
    $this->getJson('/api/v1/authors')->assertNotFound();
    $this->getJson('/api/api/authors')->assertNotFound();
});
