<?php

use App\Models\Article;
use App\Models\Magazine;
use App\Models\SearchContent;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\URL;

uses(LazilyRefreshDatabase::class);

function searchableArticle(string $title = 'Search Result Article', array $attributes = []): Article
{
    return Article::query()->forceCreate(array_merge([
        'language' => 'ur',
        'title' => $title,
        'publish_date' => '2026-08-15',
        'published_at' => '2026-08-15 10:00:00',
        'short_description' => 'Search result article description',
        'article' => 'Article body',
        'isFree' => true,
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
    ], $attributes));
}

function searchableMagazine(string $title = 'Search Result Magazine', array $attributes = []): Magazine
{
    return Magazine::query()->forceCreate(array_merge([
        'language' => 'ur',
        'title' => $title,
        'issue_number' => 'SEARCH-1',
        'publish_date' => '2026-08-15',
        'published_at' => '2026-08-15 10:00:00',
        'description' => 'Search result magazine description',
        'isActive' => true,
        'status' => Magazine::STATUS_PUBLISHED,
    ], $attributes));
}

test('active listing searches use signed tracking links while ordinary listings keep clean detail links', function () {
    $this->withoutVite();
    $article = searchableArticle();
    $magazine = searchableMagazine();
    $articleTrackingUrl = URL::signedRoute('front.search-content.track', [
        'type' => 'article',
        'id' => $article->id,
        'search' => 'Search Result',
    ]);
    $magazineTrackingUrl = URL::signedRoute('front.search-content.track', [
        'type' => 'magazine',
        'id' => $magazine->id,
        'search' => 'Search Result',
    ]);

    $this->get(route('mazameen', ['search' => 'Search Result']))
        ->assertSuccessful()
        ->assertSee($articleTrackingUrl);
    $this->get(route('sabqa-shumare', ['search' => 'Search Result']))
        ->assertSuccessful()
        ->assertSee($magazineTrackingUrl);

    expect(SearchContent::query()->count())->toBe(0);

    $this->get(route('mazameen'))
        ->assertSuccessful()
        ->assertSee(route('mazmoon-detail', ['id' => $article->id, 'slug' => 'search-result-article']), false)
        ->assertDontSee($articleTrackingUrl, false);
    $this->get(route('sabqa-shumare'))
        ->assertSuccessful()
        ->assertSee(route('shumara-detail', ['id' => $magazine->id, 'slug' => 'search-result-magazine']), false)
        ->assertDontSee($magazineTrackingUrl, false);
});

test('valid signed article and magazine clicks create one event and redirect to clean detail URLs', function () {
    $article = searchableArticle();
    $magazine = searchableMagazine();

    $this->get(URL::signedRoute('front.search-content.track', [
        'type' => 'article',
        'id' => $article->id,
        'search' => '  کاروبار  ',
    ]))->assertRedirect(route('mazmoon-detail', ['id' => $article->id, 'slug' => 'search-result-article']));

    $this->get(URL::signedRoute('front.search-content.track', [
        'type' => 'magazine',
        'id' => $magazine->id,
        'search' => 'Digital Magazine',
    ]))->assertRedirect(route('shumara-detail', ['id' => $magazine->id, 'slug' => 'search-result-magazine']));

    $this->assertDatabaseHas('search_contents', [
        'content_type' => $article->getMorphClass(),
        'content_id' => $article->id,
        'search_keyword' => 'کاروبار',
    ])->assertDatabaseHas('search_contents', [
        'content_type' => $magazine->getMorphClass(),
        'content_id' => $magazine->id,
        'search_keyword' => 'Digital Magazine',
    ]);
    expect(SearchContent::query()->count())->toBe(2);
});

test('unsigned tampered unsupported and blank search tracking requests are rejected', function () {
    $article = searchableArticle();
    $signedUrl = URL::signedRoute('front.search-content.track', [
        'type' => 'article',
        'id' => $article->id,
        'search' => 'Original',
    ]);

    $this->get(route('front.search-content.track', ['type' => 'article', 'id' => $article->id, 'search' => 'Original']))
        ->assertForbidden();
    $this->get(str_replace('search=Original', 'search=Changed', $signedUrl))->assertForbidden();
    $this->get(URL::signedRoute('front.search-content.track', [
        'type' => 'unsupported',
        'id' => $article->id,
        'search' => 'Original',
    ]))->assertNotFound();
    $this->get(URL::signedRoute('front.search-content.track', [
        'type' => 'article',
        'id' => $article->id,
        'search' => '   ',
    ]))->assertUnprocessable();

    expect(SearchContent::query()->count())->toBe(0);
});

test('direct detail requests and refreshes never create search click events', function () {
    $this->withoutVite();
    $article = searchableArticle();
    $magazine = searchableMagazine();
    $articleUrl = route('mazmoon-detail', ['id' => $article->id, 'slug' => 'search-result-article']);
    $magazineUrl = route('shumara-detail', ['id' => $magazine->id, 'slug' => 'search-result-magazine']);

    $this->get($articleUrl);
    $this->get($articleUrl);
    $this->get($magazineUrl);
    $this->get($magazineUrl);

    expect(SearchContent::query()->count())->toBe(0);
});
