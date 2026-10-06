<?php

use App\Models\Ad;
use App\Models\AdClick;
use App\Models\Article;
use App\Models\Magazine;
use App\Models\SearchContent;
use App\Models\SiteVisit;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;

uses(LazilyRefreshDatabase::class);

function apiAd(array $attributes = []): Ad
{
    return Ad::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Mobile advertisement',
        'page_name' => 'home',
        'place' => 'home_horizontal_large',
        'ad_image' => 'images/backend-images/ads/mobile.png',
        'ad_url' => 'https://advertiser.example/landing',
        'isActive' => true,
        ...$attributes,
    ]);
}

function rankedArticle(array $attributes = []): Article
{
    return Article::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Ranked article',
        'publish_date' => today(),
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
        ...$attributes,
    ]);
}

function rankedMagazine(array $attributes = []): Magazine
{
    return Magazine::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Ranked magazine',
        'publish_date' => today(),
        'isActive' => true,
        'status' => Magazine::STATUS_PUBLISHED,
        ...$attributes,
    ]);
}

function recordVisit(Article|Magazine $content): SiteVisit
{
    return SiteVisit::query()->forceCreate([
        'visitor_id' => (string) Str::uuid(),
        'public_token_hash' => hash('sha256', Str::random()),
        'page_key' => $content instanceof Article ? 'mazmoon-detail' : 'shumara-detail',
        'visitable_type' => $content->getMorphClass(),
        'visitable_id' => $content->id,
        'started_at' => now(),
    ]);
}

test('public ads endpoint filters page placement eligibility and never tracks impressions', function () {
    $eligible = apiAd();
    apiAd(['title' => 'Inactive', 'isActive' => false]);
    apiAd(['title' => 'Expired', 'expiry_date' => today()->subDay()]);
    apiAd(['title' => 'Future', 'start_date' => today()->addDay()]);
    apiAd(['title' => 'Other placement', 'place' => 'home_horizontal_small']);
    apiAd(['title' => 'Google code only', 'ad_image' => null, 'google_ad_code' => '<script>unsafe()</script>']);

    $this->getJson('/api/ads?page=home&placement=home_horizontal_large')
        ->assertSuccessful()
        ->assertJsonPath('data.ads.0.id', $eligible->id)
        ->assertJsonPath('data.ads.0.type', 'image')
        ->assertJsonPath('data.ads.0.target_url', 'https://advertiser.example/landing')
        ->assertJsonCount(1, 'data.ads')
        ->assertJsonMissing(['google_code' => '<script>unsafe()</script>'])
        ->assertJsonMissingPath('data.ads.0.click_count');

    expect($eligible->fresh()->click_count)->toBe(0)
        ->and(AdClick::query()->count())->toBe(0);
});

test('ads endpoint validates exact page and placement combinations and returns empty arrays', function () {
    $this->getJson('/api/ads?page=home&placement=header_ad')->assertUnprocessable();
    $this->getJson('/api/ads?page=unknown')->assertUnprocessable();
    $this->getJson('/api/ads?page=home&placement=home_horizontal_large')
        ->assertSuccessful()->assertJsonCount(0, 'data.ads');
});

test('eligible ad clicks are transactional while ineligible ads cannot inflate analytics', function () {
    $eligible = apiAd();
    $inactive = apiAd(['isActive' => false]);

    $this->postJson(route('api.ads.click', $eligible))
        ->assertSuccessful()
        ->assertExactJson([
            'success' => true,
            'data' => ['tracked' => true, 'target_url' => 'https://advertiser.example/landing'],
        ]);
    $this->postJson(route('api.ads.click', $inactive))->assertNotFound();
    $this->postJson('/api/ads/999999/click')->assertNotFound();

    expect($eligible->fresh()->click_count)->toBe(1)
        ->and(AdClick::query()->whereBelongsTo($eligible)->count())->toBe(1)
        ->and($inactive->fresh()->click_count)->toBe(0);
});

test('expired future and unsafe advertisement destinations cannot inflate clicks', function (array $attributes) {
    $ad = apiAd($attributes);

    $this->postJson(route('api.ads.click', $ad))->assertNotFound();

    expect($ad->fresh()->click_count)->toBe(0)
        ->and(AdClick::query()->whereBelongsTo($ad)->count())->toBe(0);
})->with([
    'expired' => [['expiry_date' => today()->subDay()]],
    'future' => [['start_date' => today()->addDay()]],
    'unsafe URL' => [['ad_url' => 'javascript:alert(1)']],
]);

test('rankings preserve all-time count order limit and public eligibility', function () {
    $first = rankedArticle(['title' => 'First']);
    $second = rankedArticle(['title' => 'Second']);
    $ineligible = rankedArticle(['title' => 'Draft', 'status' => Article::STATUS_DRAFT]);
    recordVisit($first);
    recordVisit($first);
    recordVisit($second);
    recordVisit($ineligible);

    $this->getJson('/api/rankings?metric=viewed&type=articles')
        ->assertSuccessful()
        ->assertJsonPath('data.period.type', 'all_time')
        ->assertJsonPath('data.limit', 4)
        ->assertJsonPath('data.items.0.id', $first->id)
        ->assertJsonPath('data.items.0.count', 2)
        ->assertJsonPath('data.items.0.detail_url', route('api.articles.show', $first))
        ->assertJsonMissing(['title' => 'Draft'])
        ->assertJsonMissingPath('data.items.0.article');
});

test('searched rankings use genuine search click rows and exclude ineligible content', function () {
    $magazine = rankedMagazine();
    $ineligible = rankedMagazine(['title' => 'Future', 'publish_date' => today()->addDay()]);

    foreach ([$magazine, $magazine, $ineligible] as $content) {
        SearchContent::query()->create([
            'content_type' => $content->getMorphClass(),
            'content_id' => $content->id,
            'search_keyword' => 'business',
            'created_at' => now(),
        ]);
    }

    $this->getJson('/api/rankings?metric=searched&type=magazines')
        ->assertSuccessful()
        ->assertJsonCount(1, 'data.items')
        ->assertJsonPath('data.items.0.id', $magazine->id)
        ->assertJsonPath('data.items.0.count', 2)
        ->assertJsonPath('data.items.0.detail_url', route('api.magazines.show', $magazine))
        ->assertJsonMissingPath('data.items.0.pdf');
});

test('ranking validation and empty states are predictable and read only', function () {
    $this->getJson('/api/rankings?metric=invalid&type=articles')->assertUnprocessable();
    $this->getJson('/api/rankings?metric=viewed&type=invalid')->assertUnprocessable();
    $this->getJson('/api/rankings?metric=searched&type=articles')
        ->assertSuccessful()->assertJsonCount(0, 'data.items');
    $this->getJson('/api/rankings?metric=viewed&type=magazines')
        ->assertSuccessful()->assertJsonPath('data.type', 'magazines');

    expect(SiteVisit::query()->count())->toBe(0)
        ->and(SearchContent::query()->count())->toBe(0);
});

test('genuine article and magazine search result taps store exact normalized analytics', function () {
    $article = rankedArticle();
    $magazine = rankedMagazine();

    $this->postJson('/api/analytics/search-clicks', [
        'type' => 'article', 'content_id' => $article->id, 'search_keyword' => '  تعلیم  ',
    ])->assertSuccessful()->assertExactJson(['success' => true, 'data' => ['tracked' => true]]);
    $this->postJson('/api/analytics/search-clicks', [
        'type' => 'magazine', 'content_id' => $magazine->id, 'search_keyword' => 'اسلام',
    ])->assertSuccessful();

    expect(SearchContent::query()->where('content_type', $article->getMorphClass())->where('content_id', $article->id)->value('search_keyword'))->toBe('تعلیم')
        ->and(SearchContent::query()->where('content_type', $magazine->getMorphClass())->where('content_id', $magazine->id)->value('search_keyword'))->toBe('اسلام');
});

test('search click validation rejects arbitrary types malformed ids and ineligible content', function () {
    $draft = rankedArticle(['status' => Article::STATUS_DRAFT]);

    $this->postJson('/api/analytics/search-clicks', ['type' => 'user', 'content_id' => 1, 'search_keyword' => 'x'])->assertUnprocessable();
    $this->postJson('/api/analytics/search-clicks', ['type' => 'article', 'content_id' => 'bad', 'search_keyword' => 'x'])->assertUnprocessable();
    $this->postJson('/api/analytics/search-clicks', ['type' => 'article', 'content_id' => $draft->id, 'search_keyword' => 'x'])->assertNotFound();
    $this->postJson('/api/analytics/search-clicks', ['type' => 'magazine', 'content_id' => $draft->id, 'search_keyword' => 'x'])->assertNotFound();

    expect(SearchContent::query()->count())->toBe(0);
});

test('search retrieval and filters do not create click rows and invalid route prefixes stay absent', function () {
    rankedArticle();
    rankedMagazine();

    $this->getJson('/api/articles?search=Ranked')->assertSuccessful();
    $this->getJson('/api/articles?year='.today()->year)->assertSuccessful();
    $this->getJson('/api/magazines?search=Ranked')->assertSuccessful();

    expect(SearchContent::query()->count())->toBe(0);

    $this->getJson('/api/v1/ads?page=home')->assertNotFound();
    $this->getJson('/api/api/ads?page=home')->assertNotFound();
    $this->getJson('/api/search')->assertNotFound();
});
