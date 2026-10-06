<?php

use App\Models\Article;
use App\Models\Magazine;
use App\Models\SearchContent;
use App\Models\SiteVisit;
use App\Services\SiteVisitService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    app()->instance('geoip', new class
    {
        /** @return array<string, null> */
        public function getLocation(string $ip): array
        {
            return ['country' => null, 'iso_code' => null, 'city' => null, 'state_name' => null, 'lat' => null, 'lon' => null];
        }
    });
});

function viewableArticle(array $attributes = []): Article
{
    return Article::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Mobile view article',
        'publish_date' => today(),
        'published_at' => now(),
        'article' => 'Readable body',
        'isFree' => true,
        'show_visit_counter' => true,
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
        ...$attributes,
    ]);
}

function viewableMagazine(array $attributes = []): Magazine
{
    return Magazine::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Mobile view magazine',
        'publish_date' => today(),
        'published_at' => now(),
        'description' => 'Magazine description',
        'isFree' => true,
        'show_visit_counter' => true,
        'isActive' => true,
        'status' => Magazine::STATUS_PUBLISHED,
        ...$attributes,
    ]);
}

function websiteDetailRequest(string $routeName): Request
{
    $route = Route::getRoutes()->getByName($routeName);
    $request = Request::create('/website-detail', 'GET');
    $request->setRouteResolver(fn () => $route);

    return $request;
}

test('eligible article detail records the existing site visit relation without changing its JSON contract', function () {
    $article = viewableArticle();

    $this->withHeader('User-Agent', 'DigitalMagazineMobile/1.0')
        ->getJson('/api/articles/'.$article->id.'?views=500')
        ->assertSuccessful()
        ->assertJsonPath('data.article.id', $article->id)
        ->assertJsonPath('data.article.content', 'Readable body')
        ->assertJsonPath('data.article.view_count', 0)
        ->assertJsonMissingPath('data.article.analytics');

    $visit = SiteVisit::query()->sole();
    expect($visit->visitable->is($article))->toBeTrue()
        ->and($visit->page_key)->toBe('mazmoon-detail')
        ->and($visit->route_name)->toBe('api.articles.show')
        ->and($visit->user_agent)->toBe('DigitalMagazineMobile/1.0')
        ->and($article->siteVisits()->count())->toBe(1);
});

test('eligible magazine detail records the existing site visit relation', function () {
    $magazine = viewableMagazine();

    $this->getJson('/api/magazines/'.$magazine->id)
        ->assertSuccessful()
        ->assertJsonPath('data.magazine.id', $magazine->id)
        ->assertJsonPath('data.magazine.view_count', 0)
        ->assertJsonPath('data.magazine.pdf.available', false);

    $visit = SiteVisit::query()->sole();
    expect($visit->visitable->is($magazine))->toBeTrue()
        ->and($visit->page_key)->toBe('shumara-detail')
        ->and($visit->route_name)->toBe('api.magazines.show');
});

test('ineligible missing and protected article details do not record visits', function () {
    $draft = viewableArticle(['status' => Article::STATUS_DRAFT]);
    $paid = viewableArticle(['title' => 'Protected article', 'isFree' => false]);

    $this->getJson('/api/articles/'.$draft->id)->assertNotFound();
    $this->getJson('/api/articles/999999')->assertNotFound();
    $this->getJson('/api/articles/'.$paid->id)
        ->assertSuccessful()
        ->assertJsonPath('data.article.content', null)
        ->assertJsonPath('data.article.access.requires_subscription', true);

    expect(SiteVisit::query()->count())->toBe(0);
});

test('ineligible missing and protected magazine details do not record visits', function () {
    $future = viewableMagazine(['publish_date' => today()->addDay()]);
    $paid = viewableMagazine(['title' => 'Protected magazine', 'isFree' => false]);

    $this->getJson('/api/magazines/'.$future->id)->assertNotFound();
    $this->getJson('/api/magazines/999999')->assertNotFound();
    $this->getJson('/api/magazines/'.$paid->id)
        ->assertSuccessful()
        ->assertJsonPath('data.magazine.access.requires_subscription', true)
        ->assertJsonPath('data.magazine.pdf.read_url', null);

    expect(SiteVisit::query()->count())->toBe(0);
});

test('counter visibility affects response display but never eligible visit recording', function () {
    $article = viewableArticle(['show_visit_counter' => false]);
    $magazine = viewableMagazine(['show_visit_counter' => false]);

    $this->getJson('/api/articles/'.$article->id)->assertSuccessful()->assertJsonPath('data.article.view_count', null);
    $this->getJson('/api/magazines/'.$magazine->id)->assertSuccessful()->assertJsonPath('data.magazine.view_count', null);

    expect($article->siteVisits()->count())->toBe(1)
        ->and($magazine->siteVisits()->count())->toBe(1);
});

test('each repeated successful detail GET creates a separate visit under existing semantics', function () {
    $article = viewableArticle();
    $magazine = viewableMagazine();

    $this->getJson('/api/articles/'.$article->id)->assertSuccessful();
    $this->getJson('/api/articles/'.$article->id)->assertSuccessful()->assertJsonPath('data.article.view_count', 1);
    $this->getJson('/api/magazines/'.$magazine->id)->assertSuccessful();
    $this->getJson('/api/magazines/'.$magazine->id)->assertSuccessful()->assertJsonPath('data.magazine.view_count', 1);

    expect($article->siteVisits()->count())->toBe(2)
        ->and($magazine->siteVisits()->count())->toBe(2);
});

test('article and magazine listings and searches never record content views', function () {
    viewableArticle();
    viewableMagazine();

    $this->getJson('/api/articles?search=Mobile')->assertSuccessful();
    $this->getJson('/api/magazines?search=Mobile')->assertSuccessful();
    $this->getJson('/api/articles')->assertSuccessful();
    $this->getJson('/api/magazines')->assertSuccessful();

    expect(SiteVisit::query()->count())->toBe(0)
        ->and(SearchContent::query()->count())->toBe(0);
});

test('search result clicks and subsequent detail views remain independent analytics events', function () {
    $article = viewableArticle();
    $magazine = viewableMagazine();

    $this->postJson('/api/analytics/search-clicks', ['type' => 'article', 'content_id' => $article->id, 'search_keyword' => 'تعلیم'])->assertSuccessful();
    $this->getJson('/api/articles/'.$article->id)->assertSuccessful();
    $this->postJson('/api/analytics/search-clicks', ['type' => 'magazine', 'content_id' => $magazine->id, 'search_keyword' => 'اسلام'])->assertSuccessful();
    $this->getJson('/api/magazines/'.$magazine->id)->assertSuccessful();

    expect(SearchContent::query()->count())->toBe(2)
        ->and(SiteVisit::query()->count())->toBe(2)
        ->and($article->searchContents()->count())->toBe(1)
        ->and($article->siteVisits()->count())->toBe(1)
        ->and($magazine->searchContents()->count())->toBe(1)
        ->and($magazine->siteVisits()->count())->toBe(1);
});

test('most viewed rankings combine preexisting and mobile detail visits', function () {
    $article = viewableArticle();
    $magazine = viewableMagazine();

    app(SiteVisitService::class)->record(websiteDetailRequest('mazmoon-detail'), $article);
    app(SiteVisitService::class)->record(websiteDetailRequest('shumara-detail'), $magazine);
    $this->getJson('/api/articles/'.$article->id)->assertSuccessful();
    $this->getJson('/api/magazines/'.$magazine->id)->assertSuccessful();

    $this->getJson('/api/rankings?metric=viewed&type=articles')
        ->assertSuccessful()->assertJsonPath('data.items.0.id', $article->id)->assertJsonPath('data.items.0.count', 2);
    $this->getJson('/api/rankings?metric=viewed&type=magazines')
        ->assertSuccessful()->assertJsonPath('data.items.0.id', $magazine->id)->assertJsonPath('data.items.0.count', 2);
});
