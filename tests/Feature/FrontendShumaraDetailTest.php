<?php

use App\Models\Author;
use App\Models\Category;
use App\Models\Magazine;
use App\Models\MediaStorageLocation;
use App\Models\SiteVisit;
use App\Models\StorageProvider;
use App\Models\Tags;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(LazilyRefreshDatabase::class);

function publicDetailMagazine(string $title = 'Public Detail Issue', array $attributes = []): Magazine
{
    return Magazine::query()->forceCreate(array_merge([
        'language' => 'ur', 'title' => $title, 'issue_number' => 'M-2026-01',
        'publish_date' => '2026-08-15', 'published_at' => '2026-08-15 10:00:00',
        'cover_image' => 'images/backend-images/magazines/detail.webp',
        'description' => 'Complete detail description without truncation.',
        'isFree' => true,
        'is_downloadable' => false, 'isActive' => true, 'status' => Magazine::STATUS_PUBLISHED,
    ], $attributes));
}

function attachDetailPdf(Magazine $magazine): MediaStorageLocation
{
    Storage::fake('local_media');
    Storage::disk('local_media')->put('magazines/detail.pdf', '%PDF-1.4 test');
    $provider = StorageProvider::query()->forceCreate([
        'name' => 'Local Media', 'slug' => 'local-media', 'disk' => 'local_media',
        'provider_type' => StorageProvider::TYPE_LOCAL, 'is_active' => true,
        'is_default' => true, 'priority' => 1,
    ]);

    return MediaStorageLocation::query()->forceCreate([
        'media_type' => MediaStorageLocation::MEDIA_MAGAZINE, 'media_id' => $magazine->id,
        'storage_provider_id' => $provider->id, 'path' => 'magazines/detail.pdf',
        'file_name' => 'detail.pdf', 'mime_type' => 'application/pdf', 'is_primary' => true,
        'priority' => 1, 'status' => MediaStorageLocation::STATUS_AVAILABLE,
    ]);
}

function magazineDetailAnalyticsVisit(Magazine $magazine): SiteVisit
{
    return SiteVisit::query()->create([
        'visitor_id' => (string) Str::uuid(),
        'public_token_hash' => hash('sha256', Str::random(64)),
        'page_key' => 'shumara-detail',
        'route_name' => 'shumara-detail',
        'visitable_type' => $magazine->getMorphClass(),
        'visitable_id' => $magazine->id,
        'started_at' => now(),
    ]);
}

test('public magazine detail renders full data breadcrumb and selected mappings', function () {
    $this->withoutVite();
    $magazine = publicDetailMagazine();
    $author = Author::query()->forceCreate(['name' => 'Selected Author', 'isActive' => true]);
    Author::query()->forceCreate(['name' => 'Unrelated Author', 'isActive' => true]);
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Selected Category', 'isActive' => true]);
    Category::query()->forceCreate(['language' => 'en', 'name' => 'Unrelated Category', 'isActive' => true]);
    $tag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Selected Tag', 'isActive' => true]);
    Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Unrelated Tag', 'isActive' => true]);
    $magazine->authors()->attach($author);
    $magazine->categories()->attach($category);
    $magazine->tags()->attach($tag);

    $response = $this->get(route('shumara-detail', ['id' => $magazine->id, 'slug' => 'readable-slug']));

    $response
        ->assertSuccessful()->assertSee('صفحہ اول')->assertSee('سابقہ شمارے')
        ->assertSee($magazine->title)->assertSee('15 Aug 2026')
        ->assertSee('M-2026-01')
        ->assertSee('Complete detail description without truncation.')
        ->assertSee('Selected Author')->assertSee('Selected Category')->assertSee('Selected Tag')
        ->assertSee(route('front.mazmoon-nigaar.detail', ['id' => $author->id, 'slug' => $author->name]), false)
        ->assertSee(route('mozu-detail', ['id' => $category->id, 'mozuName' => $category->name]), false)
        ->assertSee(route('sabqa-shumare', ['tag' => $tag->id]), false)
        ->assertDontSee('Unrelated Author')->assertDontSee('Unrelated Category')->assertDontSee('Unrelated Tag')
        ->assertSee(asset($magazine->cover_image), false);

    $html = $response->getContent();
    expect(substr_count($html, 'front-shumara-detail__sidebar-card'))->toBe(1)
        ->and(strpos($html, 'front-shumara-detail__sidebar-card'))->toBeLessThan(strpos($html, 'detail-authors-heading'))
        ->and(strpos($html, 'detail-authors-heading'))->toBeLessThan(strpos($html, 'detail-categories-heading'))
        ->and(strpos($html, 'detail-categories-heading'))->toBeLessThan(strpos($html, 'detail-tags-heading'))
        ->and(strpos($html, 'detail-tags-heading'))->toBeLessThan(strpos($html, 'front-shumara-reader'))
        ->and($html)->toContain('col-lg-3 order-2 order-lg-1')
        ->toContain('col-lg-9 order-1 order-lg-2');
});

test('shumara detail conditionally displays only its persisted magazine visit count', function () {
    $this->withoutVite();
    $visible = publicDetailMagazine('Visible Detail Counter', ['show_visit_counter' => true]);
    $other = publicDetailMagazine('Other Detail Counter', ['show_visit_counter' => true]);
    magazineDetailAnalyticsVisit($visible);
    magazineDetailAnalyticsVisit($visible);
    magazineDetailAnalyticsVisit($other);

    $response = $this->get(route('shumara-detail', ['id' => $visible->id, 'slug' => 'visible-detail-counter']));

    $response->assertSuccessful()->assertSee('data-magazine-visit-count="2"', false);
    expect($visible->siteVisits()->count())->toBe(3);

    $hidden = publicDetailMagazine('Hidden Detail Counter', ['show_visit_counter' => false]);
    magazineDetailAnalyticsVisit($hidden);

    $this->get(route('shumara-detail', ['id' => $hidden->id, 'slug' => 'hidden-detail-counter']))
        ->assertSuccessful()
        ->assertDontSee('data-magazine-visit-count', false);
});

test('non public magazines cannot open detail or pdf endpoints', function (array $attributes) {
    $magazine = publicDetailMagazine('Unavailable Issue', $attributes);
    $parameters = ['id' => $magazine->id, 'slug' => 'unavailable'];

    $this->get(route('shumara-detail', $parameters))->assertNotFound();
    $this->get(route('shumara-detail.pdf', $parameters))->assertNotFound();
})->with([
    'inactive' => [['isActive' => false]],
    'draft' => [['status' => Magazine::STATUS_DRAFT]],
    'future' => [['publish_date' => '2099-01-01', 'published_at' => '2099-01-01 10:00:00']],
]);

test('pdf viewer streams resolved file and download follows admin permission', function () {
    $magazine = publicDetailMagazine();
    attachDetailPdf($magazine);
    $parameters = ['id' => $magazine->id, 'slug' => 'public-detail-issue'];
    $detailUrl = route('shumara-detail', $parameters);
    $pdfUrl = route('shumara-detail.pdf', $parameters);
    $downloadUrl = route('shumara-detail.download', $parameters);

    $viewOnlyResponse = $this->get($detailUrl);
    $viewOnlyResponse
        ->assertSuccessful()
        ->assertSee('data-pdf-viewer', false)
        ->assertSee('data-downloadable="0"', false)
        ->assertSee($pdfUrl, false)
        ->assertDontSee($downloadUrl, false)
        ->assertDontSee('data-pdf-download', false)
        ->assertDontSee('data-pdf-print', false)
        ->assertDontSee('<iframe class="front-shumara-reader__frame"', false)
        ->assertDontSee('magazines/detail.pdf', false);
    $inlineResponse = $this->get($pdfUrl)->assertSuccessful();
    expect($inlineResponse->headers->get('content-disposition'))->toStartWith('inline;');
    $this->get($downloadUrl)->assertNotFound();
    $magazine->update(['is_downloadable' => true]);
    $this->get($detailUrl)
        ->assertSuccessful()
        ->assertSee('data-downloadable="1"', false)
        ->assertSee('data-pdf-download', false)
        ->assertSee('data-pdf-print', false)
        ->assertSee($downloadUrl, false);
    $downloadResponse = $this->get($downloadUrl)->assertSuccessful();
    expect($downloadResponse->headers->get('content-disposition'))->toStartWith('attachment;');
});

test('missing pdf keeps detail available without a broken viewer', function () {
    $magazine = publicDetailMagazine();

    $this->get(route('shumara-detail', ['id' => $magazine->id, 'slug' => 'public-detail-issue']))
        ->assertSuccessful()->assertSee('اس شمارے کی پی ڈی ایف دستیاب نہیں۔')
        ->assertDontSee('data-pdf-viewer', false);
});

test('explicit related magazines are public only and link to detail route', function () {
    $magazine = publicDetailMagazine('Main Issue');
    $related = publicDetailMagazine('Related Public Issue', ['publish_date' => '2026-07-01', 'published_at' => '2026-07-01 10:00:00']);
    $draft = publicDetailMagazine('Related Draft Issue', ['status' => Magazine::STATUS_DRAFT]);
    $magazine->relatedMagazines()->attach([$magazine->id, $related->id, $draft->id]);

    $response = $this->get(route('shumara-detail', ['id' => $magazine->id, 'slug' => 'main-issue']));

    $response
        ->assertSuccessful()->assertSee('Related Public Issue')
        ->assertSee(route('shumara-detail', ['id' => $related->id, 'slug' => 'related-public-issue']), false)
        ->assertDontSee('Related Draft Issue')
        ->assertSee('col-sm-6 col-md-4 col-lg-3', false);

    expect(strpos($response->getContent(), 'front-shumara-reader'))
        ->toBeLessThan(strpos($response->getContent(), 'front-shumara-related'));
});
