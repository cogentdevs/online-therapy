<?php

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Magazine;
use App\Models\MediaStorageLocation;
use App\Models\StorageProvider;
use App\Models\Tags;
use App\Models\TazaShumara;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

function apiMagazine(string $title, array $attributes = []): Magazine
{
    return Magazine::query()->forceCreate(array_merge([
        'language' => 'ur', 'title' => $title, 'issue_number' => 'M-2026',
        'publish_date' => '2026-08-15', 'published_at' => '2026-08-15 10:00:00',
        'cover_image' => 'images/backend-images/magazines/api.webp', 'description' => 'Searchable Magazine description',
        'isFree' => true, 'is_downloadable' => false, 'isActive' => true, 'status' => Magazine::STATUS_PUBLISHED,
    ], $attributes));
}

function attachApiMagazinePdf(Magazine $magazine, bool $writeFile = true): void
{
    $disk = 'api_media_'.$magazine->id;
    Storage::fake($disk);
    if ($writeFile) {
        Storage::disk($disk)->put('magazines/api.pdf', '%PDF-1.4 API magazine');
    }
    $provider = StorageProvider::query()->forceCreate([
        'disk' => $disk, 'name' => 'API Media', 'slug' => 'api-media-'.$magazine->id,
        'provider_type' => StorageProvider::TYPE_LOCAL, 'is_active' => true, 'is_default' => true, 'priority' => 1,
    ]);
    MediaStorageLocation::query()->forceCreate([
        'media_type' => MediaStorageLocation::MEDIA_MAGAZINE, 'media_id' => $magazine->id,
        'storage_provider_id' => $provider->id, 'path' => 'magazines/api.pdf', 'file_name' => 'api.pdf',
        'mime_type' => 'application/pdf', 'is_primary' => true, 'priority' => 1, 'status' => MediaStorageLocation::STATUS_AVAILABLE,
    ]);
}

test('magazine listing is public eligible ordered and paginated by twelve', function () {
    $older = apiMagazine('Older Magazine', ['publish_date' => '2026-07-01']);
    $newer = apiMagazine('Newer Magazine', ['publish_date' => '2026-08-01']);
    apiMagazine('Draft Magazine', ['status' => Magazine::STATUS_DRAFT]);
    apiMagazine('English Magazine', ['language' => 'en']);
    foreach (range(1, 11) as $number) {
        apiMagazine('Extra Magazine '.$number, ['publish_date' => '2026-06-'.str_pad((string) $number, 2, '0', STR_PAD_LEFT)]);
    }

    $response = $this->getJson('/api/magazines')->assertOk()->assertJsonCount(12, 'data.magazines')
        ->assertJsonPath('data.magazines.0.id', $newer->id)->assertJsonPath('data.pagination.per_page', 12)
        ->assertJsonPath('data.pagination.total', 13)->assertJsonMissing(['title' => 'Draft Magazine'])
        ->assertJsonMissing(['title' => 'English Magazine']);
    expect($response->json('data.magazines.1.id'))->toBe($older->id);
    $this->getJson('/api/magazines?page=2')->assertOk()->assertJsonPath('data.pagination.current_page', 2);
});

test('magazine search and independent combined filters use AND semantics', function () {
    $match = apiMagazine('Combined Magazine', ['publish_date' => '2026-05-01']);
    apiMagazine('Other Magazine', ['publish_date' => '2025-05-01']);
    $author = Author::query()->forceCreate(['name' => 'Magazine Writer', 'isActive' => true]);
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Magazine Topic', 'isActive' => true]);
    $tag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Magazine Tag', 'isActive' => true]);
    $match->authors()->attach($author);
    $match->categories()->attach($category);
    $match->tags()->attach($tag);

    foreach ([
        ['author_id' => $author->id], ['category_id' => $category->id], ['tag_id' => $tag->id], ['year' => 2026],
        ['author_id' => $author->id, 'category_id' => $category->id],
        ['category_id' => $category->id, 'tag_id' => $tag->id],
        ['author_id' => $author->id, 'year' => 2026],
        ['search' => 'Magazine Writer', 'author_id' => $author->id, 'category_id' => $category->id, 'tag_id' => $tag->id, 'year' => 2026],
    ] as $query) {
        $this->getJson('/api/magazines?'.http_build_query($query))->assertOk()->assertJsonPath('data.magazines.0.id', $match->id);
    }
});

test('magazine malformed filters return validation errors', function (string $query, string $field) {
    $this->getJson('/api/magazines?'.$query)->assertUnprocessable()->assertJsonValidationErrors($field);
})->with([['author_id=abc', 'author_id'], ['category_id=0', 'category_id'], ['tag_id=-1', 'tag_id'], ['year=x', 'year'], ['per_page=99', 'per_page']]);

test('magazine filter metadata is base eligible and excludes article only or draft mappings', function () {
    $eligible = apiMagazine('Eligible Metadata', ['publish_date' => '2025-01-01']);
    $draft = apiMagazine('Draft Metadata', ['status' => Magazine::STATUS_DRAFT, 'publish_date' => '2024-01-01']);
    $goodAuthor = Author::query()->forceCreate(['name' => 'Good Author', 'isActive' => true]);
    $badAuthor = Author::query()->forceCreate(['name' => 'Draft Author', 'isActive' => true]);
    $goodCategory = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Good Category', 'isActive' => true]);
    $badCategory = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Draft Category', 'isActive' => true]);
    $goodTag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Good Tag', 'isActive' => true]);
    $badTag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Draft Tag', 'isActive' => true]);
    $eligible->authors()->attach($goodAuthor);
    $eligible->categories()->attach($goodCategory);
    $eligible->tags()->attach($goodTag);
    $draft->authors()->attach($badAuthor);
    $draft->categories()->attach($badCategory);
    $draft->tags()->attach($badTag);

    $response = $this->getJson('/api/magazines?author_id='.$badAuthor->id)->assertOk()->assertJsonCount(0, 'data.magazines');
    $response->assertJsonFragment(['name' => 'Good Author'])->assertJsonFragment(['name' => 'Good Category'])->assertJsonFragment(['name' => 'Good Tag'])
        ->assertJsonMissing(['name' => 'Draft Author'])->assertJsonMissing(['name' => 'Draft Category'])->assertJsonMissing(['name' => 'Draft Tag'])
        ->assertJsonPath('data.filters.years.0', 2025);
});

test('current Magazine returns configured issue placements and controlled PDF actions', function () {
    $magazine = apiMagazine('Current Magazine', ['is_downloadable' => true]);
    attachApiMagazinePdf($magazine);
    $taza = TazaShumara::query()->create(['language' => 'ur', 'magazine_id' => $magazine->id, 'show_title' => true, 'show_short_description' => false, 'is_active' => true]);
    $article = Article::query()->forceCreate(['language' => 'ur', 'title' => 'Placed Article', 'publish_date' => '2026-08-01', 'short_description' => 'Summary', 'article' => 'SECRET', 'isFree' => false, 'isActive' => true, 'status' => Article::STATUS_PUBLISHED]);
    $taza->articlePlacements()->create(['article_id' => $article->id, 'display_width' => 'half', 'position' => 'center', 'sort_order' => 3]);

    $this->getJson('/api/magazines/current')->assertOk()->assertJsonPath('data.taza_shumara.magazine.id', $magazine->id)
        ->assertJsonPath('data.taza_shumara.show_short_description', false)
        ->assertJsonPath('data.taza_shumara.articles.0.layout', 'half')->assertJsonPath('data.taza_shumara.articles.0.position', 'center')
        ->assertJsonPath('data.taza_shumara.articles.0.sort_order', 3)
        ->assertJsonPath('data.taza_shumara.magazine.pdf.read_url', route('api.magazines.pdf', $magazine))
        ->assertJsonPath('data.taza_shumara.magazine.pdf.download_url', route('api.magazines.download', $magazine))
        ->assertJsonMissing(['article' => 'SECRET']);
});

test('missing current Magazine returns a predictable response', function () {
    $this->getJson('/api/magazines/current')->assertNotFound()->assertJsonPath('success', false);
});

test('magazine detail includes mappings related items access and conditional PDF actions', function () {
    $magazine = apiMagazine('Detail Magazine', ['is_downloadable' => false]);
    attachApiMagazinePdf($magazine);
    $related = apiMagazine('Related Magazine', ['publish_date' => '2026-07-01']);
    $draft = apiMagazine('Draft Related', ['status' => Magazine::STATUS_DRAFT]);
    $author = Author::query()->forceCreate(['name' => 'Detail Author', 'isActive' => true]);
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Detail Category', 'isActive' => true]);
    $tag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Detail Tag', 'isActive' => true]);
    $magazine->authors()->attach($author);
    $magazine->categories()->attach($category);
    $magazine->tags()->attach($tag);
    $magazine->relatedMagazines()->attach([$magazine->id, $related->id, $draft->id]);

    $response = $this->getJson('/api/magazines/'.$magazine->id)->assertOk()
        ->assertJsonPath('data.magazine.authors.0.id', $author->id)->assertJsonPath('data.magazine.categories.0.id', $category->id)
        ->assertJsonPath('data.magazine.tags.0.id', $tag->id)->assertJsonPath('data.magazine.related_magazines.0.id', $related->id)
        ->assertJsonPath('data.magazine.pdf.available', true)->assertJsonPath('data.magazine.pdf.read_url', route('api.magazines.pdf', $magazine))
        ->assertJsonPath('data.magazine.pdf.download_url', null)->assertJsonMissing(['title' => 'Draft Related']);
    expect($response->getContent())->not->toContain('magazines/api.pdf')->not->toContain('storage_provider_id')->not->toContain('created_by');
});

test('PDF read and download enforce public access and download permission independently', function () {
    $viewOnly = apiMagazine('View Only');
    attachApiMagazinePdf($viewOnly);
    $inline = $this->get('/api/magazines/'.$viewOnly->id.'/pdf')->assertOk();
    expect($inline->headers->get('content-type'))->toContain('application/pdf');
    expect($inline->headers->get('content-disposition'))->toStartWith('inline;');
    $this->getJson('/api/magazines/'.$viewOnly->id.'/download')->assertForbidden();

    $downloadable = apiMagazine('Downloadable', ['is_downloadable' => true]);
    attachApiMagazinePdf($downloadable);
    $download = $this->get('/api/magazines/'.$downloadable->id.'/download')->assertOk();
    expect($download->headers->get('content-disposition'))->toStartWith('attachment;');

    $paid = apiMagazine('Paid Downloadable', ['isFree' => false, 'is_downloadable' => true]);
    attachApiMagazinePdf($paid);
    $this->getJson('/api/magazines/'.$paid->id.'/pdf')->assertForbidden()->assertJsonPath('data.access.requires_subscription', true);
    $this->getJson('/api/magazines/'.$paid->id.'/download')->assertForbidden()->assertJsonPath('data.access.requires_subscription', true);
});

test('missing PDF and missing physical file return safe not found responses', function () {
    $none = apiMagazine('No PDF');
    $this->getJson('/api/magazines/'.$none->id.'/pdf')->assertNotFound()->assertJsonMissingPath('path');
    $missing = apiMagazine('Missing PDF');
    attachApiMagazinePdf($missing, false);
    $this->getJson('/api/magazines/'.$missing->id.'/pdf')->assertNotFound()->assertJsonPath('message', 'Magazine PDF is not available.');
});

test('ineligible unknown and malformed Magazine routes cannot bypass access', function () {
    $draft = apiMagazine('Draft Route Magazine', ['status' => Magazine::STATUS_DRAFT]);
    foreach (['', '/pdf', '/download'] as $suffix) {
        $this->getJson('/api/magazines/'.$draft->id.$suffix)->assertNotFound();
        $this->getJson('/api/magazines/999999'.$suffix)->assertNotFound();
    }
    $this->getJson('/api/v1/magazines')->assertNotFound();
    $this->getJson('/api/api/magazines')->assertNotFound();
});

test('Magazine API routes are ordered and existing website Magazine routes still work', function () {
    $magazine = apiMagazine('Website Regression Magazine', ['is_downloadable' => true]);
    attachApiMagazinePdf($magazine);
    $this->withoutVite();
    expect(route('api.magazines.current', absolute: false))->toBe('/api/magazines/current');
    $this->get(route('sabqa-shumare'))->assertOk();
    $this->get(route('taza.shumara'))->assertOk();
    $this->get(route('shumara-detail', ['id' => $magazine->id, 'slug' => 'website-regression-magazine']))->assertOk();
    $this->get(route('shumara-detail.pdf', ['id' => $magazine->id, 'slug' => 'website-regression-magazine']))->assertOk();
    $this->get(route('shumara-detail.download', ['id' => $magazine->id, 'slug' => 'website-regression-magazine']))->assertOk();
});
