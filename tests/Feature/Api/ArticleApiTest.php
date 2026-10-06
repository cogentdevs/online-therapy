<?php

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Tags;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

function apiArticle(string $title, array $attributes = []): Article
{
    return Article::query()->forceCreate(array_merge([
        'language' => 'ur',
        'title' => $title,
        'issue_number' => 'API-2026',
        'publish_date' => '2026-08-15',
        'published_at' => '2026-08-15 10:00:00',
        'short_description' => 'Searchable API description',
        'image' => 'images/backend-images/articles/api.webp',
        'article' => '<p>Protected article body</p>',
        'isFree' => true,
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
    ], $attributes));
}

test('article listing is public eligible ordered paginated and explicitly shaped', function () {
    $older = apiArticle('Older API Article', ['publish_date' => '2026-07-01']);
    $newer = apiArticle('Newer API Article', ['publish_date' => '2026-08-01']);
    apiArticle('Inactive API Article', ['isActive' => false]);
    apiArticle('Draft API Article', ['status' => Article::STATUS_DRAFT]);
    apiArticle('English API Article', ['language' => 'en']);
    apiArticle('Future API Article', ['publish_date' => '2099-01-01', 'published_at' => '2099-01-01 10:00:00']);

    foreach (range(1, 7) as $number) {
        apiArticle('Extra API Article '.$number, ['publish_date' => '2026-06-'.str_pad((string) $number, 2, '0', STR_PAD_LEFT)]);
    }

    $response = $this->getJson('/api/articles');

    $response->assertOk()->assertJsonPath('success', true)
        ->assertJsonCount(8, 'data.articles')
        ->assertJsonPath('data.articles.0.id', $newer->id)
        ->assertJsonPath('data.pagination.total', 9)
        ->assertJsonPath('data.pagination.per_page', 8)
        ->assertJsonMissing(['title' => 'Inactive API Article'])
        ->assertJsonMissing(['title' => 'Draft API Article'])
        ->assertJsonMissing(['title' => 'English API Article'])
        ->assertJsonMissing(['title' => 'Future API Article']);

    expect($response->json('data.articles.0'))->not->toHaveKeys(['article', 'created_by', 'updated_by', 'published_by', 'owner_admin_id'])
        ->and($response->json('data.articles.1.id'))->toBe($older->id);

    $this->getJson('/api/articles?page=2')->assertOk()->assertJsonPath('data.pagination.current_page', 2);
});

test('search and all article filters combine with website semantics', function () {
    $match = apiArticle('Combined Filter Article', ['publish_date' => '2026-06-10']);
    $other = apiArticle('Other Filter Article', ['publish_date' => '2025-06-10']);
    $author = Author::query()->forceCreate(['name' => 'API Writer', 'isActive' => true]);
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'API Topic', 'isActive' => true]);
    $tag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'API Tag', 'isActive' => true]);
    $match->authors()->attach($author);
    $match->categories()->attach($category);
    $match->tags()->attach($tag);

    $url = '/api/articles?'.http_build_query([
        'search' => 'API Writer',
        'author_id' => $author->id,
        'category_id' => $category->id,
        'tag_id' => $tag->id,
        'year' => 2026,
    ]);

    $this->getJson($url)->assertOk()->assertJsonCount(1, 'data.articles')
        ->assertJsonPath('data.articles.0.id', $match->id)
        ->assertJsonMissing(['id' => $other->id]);

    $this->getJson('/api/articles?search=Searchable%20API')->assertOk()->assertJsonCount(2, 'data.articles');
});

test('malformed article filters return JSON validation errors', function (string $query, string $field) {
    $this->getJson('/api/articles?'.$query)->assertUnprocessable()->assertJsonValidationErrors($field);
})->with([
    ['author_id=abc', 'author_id'],
    ['category_id=-1', 'category_id'],
    ['tag_id=0', 'tag_id'],
    ['year=twenty', 'year'],
    ['page=0', 'page'],
    ['per_page=500', 'per_page'],
]);

test('filter metadata uses the unfiltered base eligible article dataset', function () {
    $eligible = apiArticle('Eligible Metadata Article', ['publish_date' => '2025-05-01']);
    $ineligible = apiArticle('Draft Metadata Article', ['status' => Article::STATUS_DRAFT, 'publish_date' => '2024-05-01']);
    $eligibleAuthor = Author::query()->forceCreate(['name' => 'Eligible Author', 'isActive' => true]);
    $draftOnlyAuthor = Author::query()->forceCreate(['name' => 'Draft Only Author', 'isActive' => true]);
    $eligibleCategory = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Eligible Category', 'isActive' => true]);
    $draftOnlyCategory = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Draft Category', 'isActive' => true]);
    $eligibleTag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Eligible Tag', 'isActive' => true]);
    $draftOnlyTag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Draft Tag', 'isActive' => true]);
    $eligible->authors()->attach($eligibleAuthor);
    $eligible->categories()->attach($eligibleCategory);
    $eligible->tags()->attach($eligibleTag);
    $ineligible->authors()->attach($draftOnlyAuthor);
    $ineligible->categories()->attach($draftOnlyCategory);
    $ineligible->tags()->attach($draftOnlyTag);

    $response = $this->getJson('/api/articles?author_id='.$draftOnlyAuthor->id)->assertOk();

    $response->assertJsonCount(0, 'data.articles')
        ->assertJsonFragment(['id' => $eligibleAuthor->id, 'name' => 'Eligible Author'])
        ->assertJsonFragment(['id' => $eligibleCategory->id, 'name' => 'Eligible Category'])
        ->assertJsonFragment(['id' => $eligibleTag->id, 'name' => 'Eligible Tag'])
        ->assertJsonMissing(['name' => 'Draft Only Author'])
        ->assertJsonMissing(['name' => 'Draft Category'])
        ->assertJsonMissing(['name' => 'Draft Tag'])
        ->assertJsonPath('data.filters.years.0', 2025);
});

test('article detail includes public relationships and eligible related articles', function () {
    $article = apiArticle('API Detail Article', ['article' => '<h2>Full public API body</h2>']);
    $related = apiArticle('API Related Article', ['publish_date' => '2026-07-01']);
    $draftRelated = apiArticle('API Draft Related', ['status' => Article::STATUS_DRAFT]);
    $author = Author::query()->forceCreate(['name' => 'Detail API Author', 'isActive' => true]);
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Detail API Category', 'isActive' => true]);
    $tag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Detail API Tag', 'isActive' => true]);
    $article->authors()->attach($author);
    $article->categories()->attach($category);
    $article->tags()->attach($tag);
    $article->relatedArticles()->attach([$article->id, $related->id, $draftRelated->id]);

    $response = $this->getJson('/api/articles/'.$article->id);

    $response->assertOk()
        ->assertJsonPath('data.article.content', '<h2>Full public API body</h2>')
        ->assertJsonPath('data.article.access.is_publicly_accessible', true)
        ->assertJsonPath('data.article.authors.0.id', $author->id)
        ->assertJsonPath('data.article.categories.0.id', $category->id)
        ->assertJsonPath('data.article.tags.0.id', $tag->id)
        ->assertJsonPath('data.article.related_articles.0.id', $related->id)
        ->assertJsonMissing(['title' => 'API Draft Related']);

    expect($response->getContent())->not->toContain('created_by')->not->toContain('owner_admin_id');
});

test('paid article detail never exposes protected body', function () {
    $paid = apiArticle('Paid API Article', ['isFree' => false, 'article' => 'SECRET PAID BODY']);
    $expiredFree = apiArticle('Expired Free API Article', ['isFree' => true, 'free_until' => '2026-01-01', 'article' => 'EXPIRED FREE BODY']);

    foreach ([$paid, $expiredFree] as $article) {
        $response = $this->getJson('/api/articles/'.$article->id)->assertOk()
            ->assertJsonPath('data.article.content', null)
            ->assertJsonPath('data.article.access.is_publicly_accessible', false)
            ->assertJsonPath('data.article.access.requires_subscription', true);
        expect($response->getContent())->not->toContain((string) $article->article);
    }
});

test('unknown and noneligible article detail returns not found', function () {
    $draft = apiArticle('Unavailable API Article', ['status' => Article::STATUS_DRAFT]);

    $this->getJson('/api/articles/'.$draft->id)->assertNotFound();
    $this->getJson('/api/articles/999999')->assertNotFound();
});

test('article routes have one api prefix and existing web article routes still render', function () {
    $article = apiArticle('Web Regression Article');
    $this->withoutVite();

    expect(route('api.articles.index', absolute: false))->toBe('/api/articles')
        ->and(route('api.articles.show', $article, absolute: false))->toBe('/api/articles/'.$article->id);

    $this->getJson('/api/v1/articles')->assertNotFound();
    $this->getJson('/api/api/articles')->assertNotFound();
    $this->get(route('mazameen'))->assertOk();
    $this->get(route('mazmoon-detail', ['id' => $article->id, 'slug' => 'web-regression-article']))->assertOk();
});
