<?php

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\SiteVisit;
use App\Models\Tags;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;

uses(LazilyRefreshDatabase::class);

function publicListingArticle(string $title, array $attributes = []): Article
{
    return Article::query()->forceCreate(array_merge([
        'language' => 'ur',
        'title' => $title,
        'issue_number' => 'A-2026-01',
        'publish_date' => '2026-08-15',
        'published_at' => '2026-08-15 10:00:00',
        'short_description' => 'Searchable public article description',
        'image' => 'images/backend-images/articles/listing.webp',
        'article' => 'Complete article body',
        'isFree' => true,
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
    ], $attributes));
}

function articleAnalyticsVisit(Article $article, array $attributes = []): SiteVisit
{
    return SiteVisit::query()->create([
        'visitor_id' => (string) Str::uuid(),
        'public_token_hash' => hash('sha256', Str::random(64)),
        'page_key' => 'mazmoon-detail',
        'route_name' => 'mazmoon-detail',
        'visitable_type' => $article->getMorphClass(),
        'visitable_id' => $article->id,
        'started_at' => now(),
        ...$attributes,
    ]);
}

test('mazameen lists only eligible articles newest first with article fields and detail links', function () {
    $this->withoutVite();
    $older = publicListingArticle('Older Public Article', ['publish_date' => '2026-07-01']);
    $newer = publicListingArticle('Newest Public Article', ['publish_date' => '2026-08-01']);
    publicListingArticle('Inactive Article', ['isActive' => false]);
    publicListingArticle('Draft Article', ['status' => Article::STATUS_DRAFT]);
    $author = Author::query()->forceCreate(['name' => 'Article Author', 'isActive' => true]);
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Article Category', 'isActive' => true]);
    $tag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Article Tag', 'isActive' => true]);
    $newer->authors()->attach($author);
    $newer->categories()->attach($category);
    $newer->tags()->attach($tag);

    $response = $this->get(route('mazameen'));

    $response->assertSuccessful()
        ->assertSee('Newest Public Article')->assertSee('Older Public Article')
        ->assertDontSee('Inactive Article')->assertDontSee('Draft Article')
        ->assertSee('A-2026-01')->assertSee('Article Author')
        ->assertSee('Article Category')->assertSee('Article Tag')
        ->assertSee('Searchable public article description')
        ->assertSee(route('mazmoon-detail', ['id' => $newer->id, 'slug' => 'newest-public-article']), false)
        ->assertSee('front-mazameen-card__description', false)
        ->assertSee('col-lg-3 order-2 order-lg-1', false)
        ->assertSee('col-lg-9 order-1 order-lg-2', false);

    expect(strpos($response->getContent(), 'Newest Public Article'))
        ->toBeLessThan(strpos($response->getContent(), 'Older Public Article'));
});

test('article filters and search combine through article mapping tables', function () {
    $this->withoutVite();
    $match = publicListingArticle('Matched Business Article', ['publish_date' => '2026-06-10']);
    $other = publicListingArticle('Other Business Article', ['publish_date' => '2025-06-10']);
    $author = Author::query()->forceCreate(['name' => 'Mapped Writer', 'isActive' => true]);
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Mapped Topic', 'isActive' => true]);
    $tag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Mapped Tag', 'isActive' => true]);
    $match->authors()->attach($author);
    $match->categories()->attach($category);
    $match->tags()->attach($tag);

    $response = $this->get(route('mazameen', [
        'author' => $author->id,
        'category' => $category->id,
        'tag' => $tag->id,
        'year' => 2026,
        'search' => 'Mapped Writer',
    ]));

    $response->assertSuccessful()->assertSee($match->title)->assertDontSee($other->title)
        ->assertSee('name="author"', false)->assertSee('value="Mapped Writer"', false)
        ->assertSee('class="front-ui active"', false)
        ->assertSee('tag='.$tag->id, false);

    expect($response->getContent())
        ->toContain('search=Mapped%20Writer')
        ->toContain('author='.$author->id);
});

test('mazameen paginates eight articles and preserves filters', function () {
    $this->withoutVite();
    $tag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Paginated Tag', 'isActive' => true]);
    foreach (range(1, 9) as $number) {
        $article = publicListingArticle('Paginated Article '.$number, ['publish_date' => '2026-05-'.str_pad((string) $number, 2, '0', STR_PAD_LEFT)]);
        $article->tags()->attach($tag);
    }

    $response = $this->get(route('mazameen', ['search' => 'Paginated', 'year' => 2026, 'tag' => $tag->id]));

    $response->assertSuccessful()->assertSee('page=2', false)->assertSee('search=Paginated', false)
        ->assertSee('year=2026', false)->assertSee('tag='.$tag->id, false);
    expect(substr_count($response->getContent(), 'front-mazameen-card mb-3'))->toBe(8);
});

test('mazameen shows enabled article analytics counts without creating article visits', function () {
    $this->withoutVite();
    $visible = publicListingArticle('Visible Visit Counter', ['show_visit_counter' => true]);
    $hidden = publicListingArticle('Hidden Visit Counter', ['show_visit_counter' => false]);
    $other = publicListingArticle('Other Article Counter', ['show_visit_counter' => true]);
    articleAnalyticsVisit($visible);
    articleAnalyticsVisit($visible);
    articleAnalyticsVisit($other);
    SiteVisit::query()->create([
        'visitor_id' => (string) Str::uuid(),
        'public_token_hash' => hash('sha256', Str::random(64)),
        'page_key' => 'category-detail',
        'route_name' => 'mozu-detail',
        'visitable_type' => Category::class,
        'visitable_id' => 999,
        'started_at' => now(),
    ]);
    $articleVisitCount = SiteVisit::query()->where('visitable_type', $visible->getMorphClass())->count();

    $response = $this->get(route('mazameen'))->assertSuccessful();

    expect(substr_count($response->getContent(), 'data-article-visit-count'))->toBe(2)
        ->and(SiteVisit::query()->where('visitable_type', $visible->getMorphClass())->count())->toBe($articleVisitCount);
    $response->assertSee('data-article-visit-count="2"', false)
        ->assertSee('Visible Visit Counter')
        ->assertSee('Hidden Visit Counter');
});

test('mazmoon detail renders selected article mappings full content and public related articles', function () {
    $this->withoutVite();
    $article = publicListingArticle('Readable Article', [
        'image' => 'images/backend-images/articles/placeholder.png',
        'article' => '<h2>Full Article Section</h2><p>Complete rich article body.</p>',
    ]);
    $related = publicListingArticle('Related Public Article', ['publish_date' => '2026-07-01']);
    $draftRelated = publicListingArticle('Related Draft Article', ['status' => Article::STATUS_DRAFT]);
    $author = Author::query()->forceCreate(['name' => 'Selected Detail Author', 'isActive' => true]);
    Author::query()->forceCreate(['name' => 'Unrelated Detail Author', 'isActive' => true]);
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Selected Detail Category', 'isActive' => true]);
    Category::query()->forceCreate(['language' => 'ur', 'name' => 'Unrelated Detail Category', 'isActive' => true]);
    $tag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Selected Detail Tag', 'isActive' => true]);
    Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Unrelated Detail Tag', 'isActive' => true]);
    $article->authors()->attach($author);
    $article->categories()->attach($category);
    $article->tags()->attach($tag);
    $article->relatedArticles()->attach([$article->id, $related->id, $draftRelated->id]);

    $response = $this->get(route('mazmoon-detail', ['id' => $article->id, 'slug' => 'anything']));

    $response->assertSuccessful()
        ->assertSee('صفحہ اول')->assertSee('مضامین')->assertSee('Readable Article')
        ->assertSee('15 Aug 2026')->assertSee('A-2026-01')
        ->assertSee('Selected Detail Author')->assertSee('Selected Detail Category')->assertSee('Selected Detail Tag')
        ->assertSee(route('front.mazmoon-nigaar.detail', ['id' => $author->id, 'slug' => $author->name]), false)
        ->assertSee(route('mozu-detail', ['id' => $category->id, 'mozuName' => $category->name]), false)
        ->assertSee(route('mazameen', ['tag' => $tag->id]), false)
        ->assertDontSee('Unrelated Detail Author')->assertDontSee('Unrelated Detail Tag')
        ->assertSee('images/backend-images/articles/placeholder.png', false)
        ->assertSee('<h2>Full Article Section</h2><p>Complete rich article body.</p>', false)
        ->assertSee('Related Public Article')->assertDontSee('Related Draft Article')
        ->assertSee(route('mazmoon-detail', ['id' => $related->id, 'slug' => 'related-public-article']), false)
        ->assertSee('col-lg-3 order-2 order-lg-1', false)
        ->assertSee('col-lg-9 order-1 order-lg-2', false);

    $html = $response->getContent();
    $sidebarHtml = substr($html, strpos($html, 'data-article-sidebar'), strpos($html, 'data-article-detail') - strpos($html, 'data-article-sidebar'));

    expect(substr_count($html, 'Readable Article'))->toBeGreaterThan(1)
        ->and(strpos($html, 'data-article-detail'))->toBeGreaterThan(strpos($html, 'data-article-sidebar'))
        ->and($sidebarHtml)->not->toContain('Unrelated Detail Category');
});

test('mazmoon detail conditionally displays the persisted article visit count without duplicate tracking', function (bool $showCounter) {
    $this->withoutVite();
    $article = publicListingArticle('Detail Visit Counter', ['show_visit_counter' => $showCounter]);
    $other = publicListingArticle('Other Detail Counter');
    articleAnalyticsVisit($article);
    articleAnalyticsVisit($article);
    articleAnalyticsVisit($other);

    $response = $this->get(route('mazmoon-detail', ['id' => $article->id, 'slug' => 'detail-visit-counter']))
        ->assertSuccessful();

    if ($showCounter) {
        $response->assertSee('data-article-visit-count="2"', false);
    } else {
        $response->assertDontSee('data-article-visit-count', false);
    }

    expect($article->siteVisits()->count())->toBe(3)
        ->and($other->siteVisits()->count())->toBe(1);
})->with([true, false]);

test('non public articles cannot open mazmoon detail', function (array $attributes) {
    $article = publicListingArticle('Unavailable Article', $attributes);

    $this->get(route('mazmoon-detail', ['id' => $article->id, 'slug' => 'unavailable']))->assertNotFound();
})->with([
    'inactive' => [['isActive' => false]],
    'draft' => [['status' => Article::STATUS_DRAFT]],
    'future publish date' => [['publish_date' => '2099-01-01', 'published_at' => '2099-01-01 10:00:00']],
]);
