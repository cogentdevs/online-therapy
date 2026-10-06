<?php

use App\Models\Article;
use App\Models\Bookmark;
use App\Models\Magazine;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create(['is_active' => true]);
    $this->user->assignRole('user');
    $this->otherUser = User::factory()->create(['is_active' => true]);
    $this->otherUser->assignRole('user');
});

function accountBookmarkArticle(string $title): Article
{
    return Article::query()->forceCreate([
        'language' => 'ur',
        'title' => $title,
        'publish_date' => today(),
        'published_at' => now(),
        'article' => 'Article content',
        'isFree' => true,
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
    ]);
}

function accountBookmarkMagazine(string $title): Magazine
{
    return Magazine::query()->forceCreate([
        'language' => 'ur',
        'title' => $title,
        'publish_date' => today(),
        'published_at' => now(),
        'description' => 'Magazine content',
        'isFree' => true,
        'isActive' => true,
        'status' => Magazine::STATUS_PUBLISHED,
    ]);
}

function createDatedAccountBookmark(User $user, Article|Magazine $bookmarkable, string $createdAt, ?int $pdfPage = null): Bookmark
{
    return Bookmark::query()->forceCreate([
        'user_id' => $user->id,
        'bookmarkable_type' => $bookmarkable->getMorphClass(),
        'bookmarkable_id' => $bookmarkable->getKey(),
        'pdf_page' => $pdfPage,
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ]);
}

test('account bookmarks page requires a frontend account', function () {
    $this->get(route('front.account.bookmarks'))->assertRedirect(route('front.login'));

    $this->actingAs($this->user, 'web')->get(route('front.account.bookmarks'))
        ->assertSuccessful()
        ->assertViewIs('frontend.user-account.bookmarks');
});

test('full listing is scoped to its user and ordered newest first', function () {
    $older = accountBookmarkArticle('Older Own Bookmark');
    $newer = accountBookmarkArticle('Newest Own Bookmark');
    $other = accountBookmarkArticle('Another User Bookmark');
    createDatedAccountBookmark($this->user, $older, '2026-09-01 10:00:00');
    createDatedAccountBookmark($this->user, $newer, '2026-09-02 10:00:00');
    createDatedAccountBookmark($this->otherUser, $other, '2026-09-03 10:00:00');

    $response = $this->actingAs($this->user, 'web')->get(route('front.account.bookmarks'))
        ->assertSuccessful()
        ->assertSee('Newest Own Bookmark')
        ->assertSee('Older Own Bookmark')
        ->assertDontSee('Another User Bookmark');

    expect(strpos($response->getContent(), 'Newest Own Bookmark'))
        ->toBeLessThan(strpos($response->getContent(), 'Older Own Bookmark'));
});

test('full listing paginates ten bookmarks per page', function () {
    foreach (range(1, 11) as $number) {
        createDatedAccountBookmark(
            $this->user,
            accountBookmarkArticle('Paginated Bookmark '.$number),
            Carbon::parse('2026-09-01')->addMinutes($number)->toDateTimeString(),
        );
    }

    $this->actingAs($this->user, 'web')->get(route('front.account.bookmarks'))
        ->assertSuccessful()
        ->assertViewHas('bookmarks', function (LengthAwarePaginator $bookmarks): bool {
            return $bookmarks->perPage() === 10
                && $bookmarks->count() === 10
                && $bookmarks->total() === 11;
        })
        ->assertSee('page=2', false);
});

test('article and magazine bookmarks use clean labels titles dates and existing view routes', function () {
    $article = accountBookmarkArticle('Saved Article');
    $magazine = accountBookmarkMagazine('Saved Magazine');
    createDatedAccountBookmark($this->user, $article, '2026-09-05 12:00:00');
    createDatedAccountBookmark($this->user, $magazine, '2026-09-06 12:00:00', 17);

    $this->actingAs($this->user, 'web')->get(route('front.account.bookmarks'))
        ->assertSuccessful()
        ->assertSee('مضمون')
        ->assertSee('شمارہ')
        ->assertSee('Saved Article')
        ->assertSee('Saved Magazine')
        ->assertSee('05 Sep 2026')
        ->assertSee('06 Sep 2026')
        ->assertSee(route('mazmoon-detail', [$article->id, 'saved-article']), false)
        ->assertSee(route('shumara-detail', [$magazine->id, 'saved-magazine']), false)
        ->assertDontSee('page=17', false)
        ->assertDontSee('#page=17', false);
});

test('dashboard shows only the current users latest five bookmarks and full-list link', function () {
    foreach (range(1, 6) as $number) {
        createDatedAccountBookmark(
            $this->user,
            accountBookmarkArticle('Dashboard Bookmark '.$number),
            Carbon::parse('2026-09-01')->addMinutes($number)->toDateTimeString(),
        );
    }
    createDatedAccountBookmark($this->otherUser, accountBookmarkArticle('Other Dashboard Bookmark'), '2026-09-10 10:00:00');

    $response = $this->actingAs($this->user, 'web')->get(route('front.account'))
        ->assertSuccessful()
        ->assertSee('Dashboard Bookmark 6')
        ->assertSee('Dashboard Bookmark 2')
        ->assertDontSee('Dashboard Bookmark 1')
        ->assertDontSee('Other Dashboard Bookmark')
        ->assertSee(route('front.account.bookmarks'), false);

    expect(strpos($response->getContent(), 'Dashboard Bookmark 6'))
        ->toBeLessThan(strpos($response->getContent(), 'Dashboard Bookmark 5'));
});

test('dashboard and full listing show bookmark empty states', function () {
    $this->actingAs($this->user, 'web')->get(route('front.account'))
        ->assertSuccessful()
        ->assertSee('ابھی کوئی بک مارک محفوظ نہیں کیا گیا۔');

    $this->get(route('front.account.bookmarks'))
        ->assertSuccessful()
        ->assertSee('ابھی کوئی بک مارک محفوظ نہیں کیا گیا۔');
});

test('account removal deletes only the owners bookmark and preserves its content target', function () {
    $article = accountBookmarkArticle('Own Removable Bookmark');
    $ownBookmark = createDatedAccountBookmark($this->user, $article, '2026-09-05 10:00:00');
    $otherBookmark = createDatedAccountBookmark($this->otherUser, $article, '2026-09-06 10:00:00');

    $this->actingAs($this->user, 'web')
        ->deleteJson(route('front.account.bookmarks.destroy', $ownBookmark))
        ->assertSuccessful()
        ->assertJson(['success' => true, 'removed' => true]);

    $this->assertModelMissing($ownBookmark);
    $this->assertModelExists($otherBookmark);
    $this->assertModelExists($article);

    $this->deleteJson(route('front.account.bookmarks.destroy', $otherBookmark))
        ->assertSuccessful();
    $this->assertModelExists($otherBookmark);
});

test('missing account bookmark removal is safely idempotent', function () {
    $this->actingAs($this->user, 'web')
        ->deleteJson(route('front.account.bookmarks.destroy', 999999))
        ->assertSuccessful()
        ->assertJson([
            'success' => true,
            'removed' => true,
            'message' => 'یہ بک مارک موجود نہیں تھا۔',
        ]);
});

test('orphaned bookmark renders unavailable and can still be removed by its owner', function () {
    $orphan = Bookmark::query()->forceCreate([
        'user_id' => $this->user->id,
        'bookmarkable_type' => Article::class,
        'bookmarkable_id' => 999999,
        'pdf_page' => null,
    ]);

    $this->actingAs($this->user, 'web')->get(route('front.account.bookmarks'))
        ->assertSuccessful()
        ->assertSee('مواد دستیاب نہیں ہے')
        ->assertSee('دستیاب نہیں');

    $this->deleteJson(route('front.account.bookmarks.destroy', $orphan))->assertSuccessful();
    $this->assertModelMissing($orphan);
});

test('article detail bookmark endpoints remain available without magazine or pdf bookmark endpoints', function () {
    $article = accountBookmarkArticle('Article Bookmark Regression');

    $this->actingAs($this->user, 'web')
        ->postJson(route('front.article-bookmarks.store', $article))
        ->assertSuccessful();
    $this->deleteJson(route('front.article-bookmarks.destroy', $article))
        ->assertSuccessful();

    expect(Route::has('front.magazine-bookmarks.store'))->toBeFalse()
        ->and(Route::has('front.magazine-bookmarks.destroy'))->toBeFalse();
});
