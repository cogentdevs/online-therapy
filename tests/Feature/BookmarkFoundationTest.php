<?php

use App\Models\Article;
use App\Models\Bookmark;
use App\Models\Magazine;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(LazilyRefreshDatabase::class);

test('bookmarks table has the reusable polymorphic foundation', function () {
    expect(Schema::hasColumns('bookmarks', [
        'id',
        'user_id',
        'bookmarkable_type',
        'bookmarkable_id',
        'pdf_page',
        'created_at',
        'updated_at',
    ]))->toBeTrue();
});

test('article bookmark resolves all relationships and has no pdf page', function () {
    $user = User::factory()->create();
    $article = Article::query()->create(['title' => 'Bookmarked Article']);
    $bookmark = $article->bookmarks()->create([
        'user_id' => $user->id,
        'pdf_page' => null,
    ]);

    expect($bookmark->user->is($user))->toBeTrue()
        ->and($bookmark->bookmarkable->is($article))->toBeTrue()
        ->and($bookmark->pdf_page)->toBeNull()
        ->and($user->bookmarks()->sole()->is($bookmark))->toBeTrue()
        ->and($article->bookmarks()->sole()->is($bookmark))->toBeTrue();
});

test('magazine bookmark stores a nullable or integer pdf page', function () {
    $user = User::factory()->create();
    $magazineWithoutPage = Magazine::query()->create(['title' => 'Whole Magazine']);
    $magazineWithPage = Magazine::query()->create(['title' => 'Magazine Page']);

    $withoutPage = $magazineWithoutPage->bookmarks()->create([
        'user_id' => $user->id,
        'pdf_page' => null,
    ]);
    $withPage = $magazineWithPage->bookmarks()->create([
        'user_id' => $user->id,
        'pdf_page' => 17,
    ]);

    expect($withoutPage->pdf_page)->toBeNull()
        ->and($withPage->pdf_page)->toBe(17)
        ->and($withPage->bookmarkable->is($magazineWithPage))->toBeTrue()
        ->and($magazineWithPage->bookmarks()->sole()->is($withPage))->toBeTrue();
});

test('same user cannot bookmark the same article twice', function () {
    $user = User::factory()->create();
    $article = Article::query()->create(['title' => 'Unique Article']);
    $article->bookmarks()->create(['user_id' => $user->id]);

    expect(fn () => $article->bookmarks()->create(['user_id' => $user->id]))
        ->toThrow(QueryException::class);
});

test('same user cannot bookmark the same magazine twice even with another pdf page', function () {
    $user = User::factory()->create();
    $magazine = Magazine::query()->create(['title' => 'Unique Magazine']);
    $magazine->bookmarks()->create(['user_id' => $user->id, 'pdf_page' => 1]);

    expect(fn () => $magazine->bookmarks()->create([
        'user_id' => $user->id,
        'pdf_page' => 17,
    ]))->toThrow(QueryException::class);
});

test('different users may bookmark the same article and magazine', function () {
    $firstUser = User::factory()->create();
    $secondUser = User::factory()->create();
    $article = Article::query()->create(['title' => 'Shared Article']);
    $magazine = Magazine::query()->create(['title' => 'Shared Magazine']);

    foreach ([$firstUser, $secondUser] as $user) {
        $article->bookmarks()->create(['user_id' => $user->id]);
        $magazine->bookmarks()->create(['user_id' => $user->id]);
    }

    expect($article->bookmarks()->count())->toBe(2)
        ->and($magazine->bookmarks()->count())->toBe(2)
        ->and(Bookmark::query()->count())->toBe(4);
});

test('deleting a user cascades their bookmarks', function () {
    $user = User::factory()->create();
    $article = Article::query()->create(['title' => 'Cascade Article']);
    $bookmark = $article->bookmarks()->create(['user_id' => $user->id]);

    $user->delete();

    $this->assertModelMissing($bookmark);
    $this->assertModelExists($article);
});
