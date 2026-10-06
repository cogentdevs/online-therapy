<?php

use App\Models\Article;
use App\Models\Bookmark;
use App\Models\Magazine;
use App\Models\MediaStorageLocation;
use App\Models\StorageProvider;
use App\Models\User;
use App\Services\BookmarkPresentationService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create(['is_active' => true]);
    $this->user->assignRole('user');
    $this->otherUser = User::factory()->create(['is_active' => true]);
    $this->otherUser->assignRole('user');
    $this->magazine = Magazine::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Bookmarkable Magazine',
        'publish_date' => today(),
        'published_at' => now(),
        'description' => 'Magazine description',
        'isFree' => true,
        'is_downloadable' => false,
        'isActive' => true,
        'status' => Magazine::STATUS_PUBLISHED,
    ]);
});

afterEach(fn () => Carbon::setTestNow());

function attachBookmarkableMagazinePdf(Magazine $magazine): void
{
    Storage::fake('bookmark_magazine_media');
    Storage::disk('bookmark_magazine_media')->put('magazines/bookmark.pdf', '%PDF-1.4 test');
    $provider = StorageProvider::query()->forceCreate([
        'name' => 'Bookmark Media',
        'slug' => 'bookmark-media',
        'disk' => 'bookmark_magazine_media',
        'provider_type' => StorageProvider::TYPE_LOCAL,
        'is_active' => true,
        'is_default' => true,
        'priority' => 1,
    ]);
    MediaStorageLocation::query()->forceCreate([
        'media_type' => MediaStorageLocation::MEDIA_MAGAZINE,
        'media_id' => $magazine->id,
        'storage_provider_id' => $provider->id,
        'path' => 'magazines/bookmark.pdf',
        'file_name' => 'bookmark.pdf',
        'mime_type' => 'application/pdf',
        'is_primary' => true,
        'priority' => 1,
        'status' => MediaStorageLocation::STATUS_AVAILABLE,
    ]);
}

test('authenticated user bookmarks a magazine at the validated pdf page', function () {
    $this->actingAs($this->user, 'web')
        ->postJson(route('front.magazine-bookmarks.store', $this->magazine), [
            'pdf_page' => 17,
            'user_id' => $this->otherUser->id,
            'bookmarkable_type' => Article::class,
        ])
        ->assertSuccessful()
        ->assertJson([
            'success' => true,
            'bookmarked' => true,
            'pdf_page' => 17,
            'created' => true,
        ]);

    $bookmark = Bookmark::query()->sole();
    expect($bookmark->user->is($this->user))->toBeTrue()
        ->and($bookmark->bookmarkable->is($this->magazine))->toBeTrue()
        ->and($bookmark->bookmarkable_type)->toBe(Magazine::class)
        ->and($bookmark->pdf_page)->toBe(17);
});

test('magazine bookmark page validation accepts positive integers', function (int $page) {
    $this->actingAs($this->user, 'web')
        ->postJson(route('front.magazine-bookmarks.store', $this->magazine), ['pdf_page' => $page])
        ->assertSuccessful()
        ->assertJsonPath('pdf_page', $page);
})->with([1, 2, 37]);

test('magazine bookmark page validation rejects invalid values', function (mixed $page) {
    $this->actingAs($this->user, 'web')
        ->postJson(route('front.magazine-bookmarks.store', $this->magazine), ['pdf_page' => $page])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('pdf_page');
})->with([0, -1, 'page-two', 1.5, null]);

test('saving another page updates the single existing magazine bookmark', function () {
    Carbon::setTestNow('2026-09-12 10:00:00');
    $route = route('front.magazine-bookmarks.store', $this->magazine);
    $this->actingAs($this->user, 'web')->postJson($route, ['pdf_page' => 5])->assertSuccessful();
    $bookmark = Bookmark::query()->sole();

    Carbon::setTestNow('2026-09-12 10:05:00');
    $this->postJson($route, ['pdf_page' => 20])
        ->assertSuccessful()
        ->assertJson(['created' => false, 'pdf_page' => 20]);

    expect(Bookmark::query()->count())->toBe(1)
        ->and($bookmark->fresh()->pdf_page)->toBe(20)
        ->and($bookmark->fresh()->updated_at->toDateTimeString())->toBe('2026-09-12 10:05:00');

    Carbon::setTestNow();
});

test('owner removes magazine bookmark while another users bookmark and magazine remain', function () {
    $ownBookmark = $this->magazine->bookmarks()->create(['user_id' => $this->user->id, 'pdf_page' => 4]);
    $otherBookmark = $this->magazine->bookmarks()->create(['user_id' => $this->otherUser->id, 'pdf_page' => 9]);

    $this->actingAs($this->user, 'web')
        ->deleteJson(route('front.magazine-bookmarks.destroy', $this->magazine))
        ->assertSuccessful()
        ->assertJson(['bookmarked' => false, 'pdf_page' => null]);

    $this->assertModelMissing($ownBookmark);
    $this->assertModelExists($otherBookmark);
    $this->assertModelExists($this->magazine);
});

test('guest cannot persist update or remove a magazine bookmark and sees existing login modal action', function () {
    $storeRoute = route('front.magazine-bookmarks.store', $this->magazine);
    $destroyRoute = route('front.magazine-bookmarks.destroy', $this->magazine);

    $this->postJson($storeRoute, ['pdf_page' => 2])->assertRedirect(route('front.login'));
    $this->deleteJson($destroyRoute)->assertRedirect(route('front.login'));

    attachBookmarkableMagazinePdf($this->magazine);
    $this->get(route('shumara-detail', [$this->magazine->id, 'bookmarkable-magazine']))
        ->assertSuccessful()
        ->assertSee('data-bs-target="#subscription-login-modal"', false);

    expect(Bookmark::query()->count())->toBe(0);
});

test('shumara detail loads only the authenticated users bookmark state', function () {
    attachBookmarkableMagazinePdf($this->magazine);
    $this->magazine->bookmarks()->create(['user_id' => $this->otherUser->id, 'pdf_page' => 22]);

    $this->actingAs($this->user, 'web')
        ->get(route('shumara-detail', [$this->magazine->id, 'bookmarkable-magazine']))
        ->assertSuccessful()
        ->assertSee('data-bookmarked="false"', false)
        ->assertViewHas('magazineBookmark', null);

    $this->magazine->bookmarks()->create(['user_id' => $this->user->id, 'pdf_page' => 17]);
    $this->get(route('shumara-detail', [$this->magazine->id, 'bookmarkable-magazine']))
        ->assertSuccessful()
        ->assertSee('data-bookmarked="true"', false)
        ->assertViewHas('magazineBookmark', fn (Bookmark $bookmark): bool => $bookmark->pdf_page === 17);
});

test('account magazine view URL includes only a valid saved page while article URL stays unchanged', function () {
    $magazineBookmark = $this->magazine->bookmarks()->create(['user_id' => $this->user->id, 'pdf_page' => 17]);
    $article = Article::query()->forceCreate(['title' => 'Unchanged Article']);
    $articleBookmark = $article->bookmarks()->create(['user_id' => $this->user->id, 'pdf_page' => null]);
    $presenter = app(BookmarkPresentationService::class);
    $presenter->prepare([$magazineBookmark, $articleBookmark]);

    expect($magazineBookmark->frontend_url)->toBe(route('shumara-detail', [
        'id' => $this->magazine->id,
        'slug' => 'bookmarkable-magazine',
        'page' => 17,
    ]))->and($articleBookmark->frontend_url)->toBe(route('mazmoon-detail', [
        'id' => $article->id,
        'slug' => 'unchanged-article',
    ]));

    $magazineBookmark->pdf_page = null;
    $presenter->prepare([$magazineBookmark]);
    expect($magazineBookmark->frontend_url)->toBe(route('shumara-detail', [
        'id' => $this->magazine->id,
        'slug' => 'bookmarkable-magazine',
    ]));
});

test('requested page initializes the existing viewer and invalid values fall back to page one', function () {
    attachBookmarkableMagazinePdf($this->magazine);
    $route = route('shumara-detail', [$this->magazine->id, 'bookmarkable-magazine']);

    $this->get($route.'?page=17')->assertSuccessful()->assertSee('data-initial-page="17"', false);
    $this->get($route)->assertSuccessful()->assertSee('data-initial-page="1"', false);
    $this->get($route.'?page=0')->assertSuccessful()->assertSee('data-initial-page="1"', false);
    $this->get($route.'?page=invalid')->assertSuccessful()->assertSee('data-initial-page="1"', false);

    expect(file_get_contents(resource_path('js/front/pdf-viewer.js')))
        ->toContain('Math.min(Math.max(requestedInitialPage, 1), pdfDocument.numPages)');
});

test('page parameter does not bypass the existing paid magazine access gate', function () {
    $this->magazine->update(['isFree' => false]);

    $this->actingAs($this->user, 'web')
        ->get(route('shumara-detail', [$this->magazine->id, 'bookmarkable-magazine']).'?page=17')
        ->assertRedirect(route('front.subscriptions'));
});

test('article bookmark behavior remains isolated with a null pdf page', function () {
    $article = Article::query()->forceCreate(['title' => 'Article Regression']);

    $this->actingAs($this->user, 'web')
        ->postJson(route('front.article-bookmarks.store', $article), ['pdf_page' => 20])
        ->assertSuccessful();

    expect(Bookmark::query()->sole()->pdf_page)->toBeNull();
});
