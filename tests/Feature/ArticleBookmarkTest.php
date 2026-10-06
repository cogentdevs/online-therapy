<?php

use App\Models\Article;
use App\Models\Bookmark;
use App\Models\Magazine;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create(['is_active' => true]);
    $this->user->assignRole('user');
    $this->article = Article::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Bookmarkable Article',
        'publish_date' => today(),
        'published_at' => now(),
        'article' => 'Bookmarkable article body',
        'isFree' => true,
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
    ]);
});

test('authenticated frontend user can bookmark an article with server-owned attributes', function () {
    $otherUser = User::factory()->create();

    $this->actingAs($this->user, 'web')->postJson(route('front.article-bookmarks.store', $this->article), [
        'user_id' => $otherUser->id,
        'bookmarkable_type' => Magazine::class,
        'pdf_page' => 17,
    ])->assertSuccessful()
        ->assertJson([
            'success' => true,
            'bookmarked' => true,
        ]);

    $bookmark = Bookmark::query()->sole();

    expect($bookmark->user->is($this->user))->toBeTrue()
        ->and($bookmark->bookmarkable->is($this->article))->toBeTrue()
        ->and($bookmark->bookmarkable_type)->toBe(Article::class)
        ->and($bookmark->pdf_page)->toBeNull();
});

test('bookmarking the same article twice is idempotent', function () {
    $route = route('front.article-bookmarks.store', $this->article);

    $this->actingAs($this->user, 'web')->postJson($route)->assertSuccessful();
    $this->postJson($route)->assertSuccessful()
        ->assertJson([
            'success' => true,
            'bookmarked' => true,
            'message' => 'یہ مضمون پہلے سے بک مارک ہے۔',
        ]);

    expect(Bookmark::query()->count())->toBe(1);
});

test('database uniqueness remains authoritative for article bookmarks', function () {
    $this->article->bookmarks()->create(['user_id' => $this->user->id]);

    expect(fn () => $this->article->bookmarks()->create([
        'user_id' => $this->user->id,
        'pdf_page' => null,
    ]))->toThrow(QueryException::class);
});

test('authenticated user removes only their own article bookmark', function () {
    $otherUser = User::factory()->create();
    $ownBookmark = $this->article->bookmarks()->create(['user_id' => $this->user->id]);
    $otherBookmark = $this->article->bookmarks()->create(['user_id' => $otherUser->id]);

    $this->actingAs($this->user, 'web')
        ->deleteJson(route('front.article-bookmarks.destroy', $this->article))
        ->assertSuccessful()
        ->assertJson(['success' => true, 'bookmarked' => false]);

    $this->assertModelMissing($ownBookmark);
    $this->assertModelExists($otherBookmark);
    $this->assertModelExists($this->article);
});

test('removing a missing own bookmark is idempotent and preserves another users bookmark', function () {
    $otherUser = User::factory()->create();
    $otherBookmark = $this->article->bookmarks()->create(['user_id' => $otherUser->id]);

    $this->actingAs($this->user, 'web')
        ->deleteJson(route('front.article-bookmarks.destroy', $this->article))
        ->assertSuccessful()
        ->assertJson([
            'success' => true,
            'bookmarked' => false,
            'message' => 'یہ مضمون بک مارک میں موجود نہیں تھا۔',
        ]);

    $this->assertModelExists($otherBookmark);
});

test('guest cannot mutate article bookmarks and sees the existing login modal action', function () {
    $this->postJson(route('front.article-bookmarks.store', $this->article))
        ->assertRedirect(route('front.login'));
    $this->deleteJson(route('front.article-bookmarks.destroy', $this->article))
        ->assertRedirect(route('front.login'));

    $this->get(route('mazmoon-detail', [$this->article->id, 'bookmarkable-article']))
        ->assertSuccessful()
        ->assertSee('data-bs-target="#subscription-login-modal"', false)
        ->assertSee('بک مارک کریں');

    expect(Bookmark::query()->count())->toBe(0);
});

test('article detail renders bookmark state for the authenticated user only', function () {
    $otherUser = User::factory()->create();
    $this->article->bookmarks()->create(['user_id' => $otherUser->id]);

    $this->actingAs($this->user, 'web')
        ->get(route('mazmoon-detail', [$this->article->id, 'bookmarkable-article']))
        ->assertSuccessful()
        ->assertSee('data-bookmarked="false"', false)
        ->assertSee('بک مارک کریں');

    $this->article->bookmarks()->create(['user_id' => $this->user->id]);

    $this->get(route('mazmoon-detail', [$this->article->id, 'bookmarkable-article']))
        ->assertSuccessful()
        ->assertSee('data-bookmarked="true"', false)
        ->assertSee('بک مارک سے ہٹائیں');
});

test('this chunk defines no magazine bookmark endpoints', function () {
    expect(Route::has('front.magazine-bookmarks.store'))->toBeFalse()
        ->and(Route::has('front.magazine-bookmarks.destroy'))->toBeFalse();
});
