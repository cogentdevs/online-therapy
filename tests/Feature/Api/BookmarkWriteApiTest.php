<?php

use App\Models\Article;
use App\Models\Bookmark;
use App\Models\Magazine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->reader = User::factory()->create(['is_active' => true]);
    $this->otherReader = User::factory()->create(['is_active' => true]);
    $this->article = Article::query()->forceCreate([
        'language' => 'ur',
        'title' => 'API Bookmark Article',
        'publish_date' => today(),
        'published_at' => now(),
        'article' => 'Article body',
        'isFree' => true,
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
    ]);
    $this->magazine = Magazine::query()->forceCreate([
        'language' => 'ur',
        'title' => 'API Bookmark Magazine',
        'publish_date' => today(),
        'published_at' => now(),
        'description' => 'Magazine description',
        'isFree' => true,
        'isActive' => true,
        'status' => Magazine::STATUS_PUBLISHED,
    ]);
});

test('bookmark write endpoints require Sanctum authentication', function (string $method, string $url, array $payload = []) {
    $this->json($method, $url, $payload)->assertUnauthorized();
})->with([
    'article add' => ['PUT', '/api/articles/1/bookmark'],
    'article remove' => ['DELETE', '/api/articles/1/bookmark'],
    'magazine page add' => ['PUT', '/api/magazines/1/pdf/bookmark', ['pdf_page' => 5]],
    'magazine page remove' => ['DELETE', '/api/magazines/1/pdf/bookmark'],
]);

test('authenticated user idempotently stores an article bookmark using token ownership', function () {
    Sanctum::actingAs($this->reader);
    $url = route('api.articles.bookmark.store', $this->article);

    $this->putJson($url, ['user_id' => $this->otherReader->id])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.bookmark.content_type', 'article')
        ->assertJsonPath('data.bookmark.content_id', $this->article->id)
        ->assertJsonPath('data.bookmark.pdf_page', null);

    $this->putJson($url)->assertOk();

    expect(Bookmark::query()->count())->toBe(1);
    $bookmark = Bookmark::query()->sole();
    expect($bookmark->user_id)->toBe($this->reader->id)
        ->and($bookmark->bookmarkable->is($this->article))->toBeTrue()
        ->and($bookmark->pdf_page)->toBeNull();
});

test('article removal is idempotent and only removes the authenticated users bookmark', function () {
    $own = $this->article->bookmarks()->create(['user_id' => $this->reader->id]);
    $other = $this->article->bookmarks()->create(['user_id' => $this->otherReader->id]);
    Sanctum::actingAs($this->reader);

    $this->deleteJson(route('api.articles.bookmark.destroy', $this->article))
        ->assertOk()
        ->assertJson(['success' => true, 'message' => 'Bookmark removed successfully.']);
    $this->deleteJson(route('api.articles.bookmark.destroy', $this->article))->assertOk();

    $this->assertModelMissing($own);
    $this->assertModelExists($other);
    $this->assertModelExists($this->article);
});

test('article bookmark writes are immediately reflected by the existing bookmark list', function () {
    Sanctum::actingAs($this->reader);
    $storeUrl = route('api.articles.bookmark.store', $this->article);
    $destroyUrl = route('api.articles.bookmark.destroy', $this->article);

    $this->putJson($storeUrl)->assertOk();
    $this->getJson(route('api.me.bookmarks.index'))
        ->assertOk()
        ->assertJsonPath('data.pagination.total', 1)
        ->assertJsonPath('data.bookmarks.0.content_id', $this->article->id);

    $this->deleteJson($destroyUrl)->assertOk();
    $this->getJson(route('api.me.bookmarks.index'))
        ->assertOk()
        ->assertJsonPath('data.pagination.total', 0)
        ->assertJsonPath('data.bookmarks', []);
});

test('invalid article and magazine route bindings return not found', function () {
    Sanctum::actingAs($this->reader);

    $this->putJson('/api/articles/999999/bookmark')->assertNotFound();
    $this->deleteJson('/api/articles/999999/bookmark')->assertNotFound();
    $this->putJson('/api/magazines/999999/pdf/bookmark', ['pdf_page' => 1])->assertNotFound();
    $this->deleteJson('/api/magazines/999999/pdf/bookmark')->assertNotFound();
});

test('magazine pdf page validation rejects missing zero negative and non numeric values', function (mixed $pdfPage, bool $includeField = true) {
    Sanctum::actingAs($this->reader);
    $payload = $includeField ? ['pdf_page' => $pdfPage] : [];

    $this->putJson(route('api.magazines.pdf.bookmark.store', $this->magazine), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('pdf_page');
})->with([
    'missing' => [null, false],
    'zero' => [0],
    'negative' => [-1],
    'non numeric' => ['abc'],
]);

test('magazine pdf page one is valid and malicious ownership input is ignored', function () {
    Sanctum::actingAs($this->reader);

    $this->putJson(route('api.magazines.pdf.bookmark.store', $this->magazine), [
        'pdf_page' => 1,
        'user_id' => $this->otherReader->id,
        'magazine_id' => 999999,
    ])->assertOk()
        ->assertJsonPath('data.bookmark.content_type', 'magazine')
        ->assertJsonPath('data.bookmark.content_id', $this->magazine->id)
        ->assertJsonPath('data.bookmark.pdf_page', 1);

    $bookmark = Bookmark::query()->sole();
    expect($bookmark->user_id)->toBe($this->reader->id)
        ->and($bookmark->bookmarkable->is($this->magazine))->toBeTrue();
});

test('saving another magazine pdf page updates the same logical bookmark', function () {
    Sanctum::actingAs($this->reader);
    $url = route('api.magazines.pdf.bookmark.store', $this->magazine);

    $this->putJson($url, ['pdf_page' => 5])->assertOk();
    $this->putJson($url, ['pdf_page' => 8])
        ->assertOk()
        ->assertJsonPath('data.bookmark.pdf_page', 8);
    $this->putJson($url, ['pdf_page' => 8])->assertOk();

    expect(Bookmark::query()->count())->toBe(1)
        ->and(Bookmark::query()->sole()->pdf_page)->toBe(8);
    $this->getJson(route('api.me.bookmarks.index'))
        ->assertOk()
        ->assertJsonPath('data.bookmarks.0.pdf_page', 8);
});

test('magazine pdf removal is idempotent and preserves another users bookmark', function () {
    $own = $this->magazine->bookmarks()->create(['user_id' => $this->reader->id, 'pdf_page' => 5]);
    $other = $this->magazine->bookmarks()->create(['user_id' => $this->otherReader->id, 'pdf_page' => 9]);
    Sanctum::actingAs($this->reader);
    $url = route('api.magazines.pdf.bookmark.destroy', $this->magazine);

    $this->deleteJson($url)->assertOk();
    $this->deleteJson($url)->assertOk();

    $this->assertModelMissing($own);
    $this->assertModelExists($other);
    $this->getJson(route('api.me.bookmarks.index'))
        ->assertOk()
        ->assertJsonPath('data.pagination.total', 0);
});

test('normal magazine bookmark routes are not introduced', function () {
    expect(Route::has('api.magazines.bookmark.store'))->toBeFalse()
        ->and(Route::has('api.magazines.bookmark.destroy'))->toBeFalse();
});
