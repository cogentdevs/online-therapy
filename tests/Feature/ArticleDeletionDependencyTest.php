<?php

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Language;
use App\Models\Magazine;
use App\Models\MetaTag;
use App\Models\Tags;
use App\Models\TazaShumara;
use App\Models\TazaShumaraArticle;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();

    $this->admin = User::factory()->create(['is_active' => true]);
    $this->admin->assignRole(Role::findOrCreate('super-admin', 'web'));
    Language::query()->create(['name' => 'English', 'code' => 'en', 'is_active' => true]);
});

function deletableArticle(User $admin, string $title): Article
{
    return Article::query()->forceCreate([
        'language' => 'en',
        'title' => $title,
        'article' => '<p>Article body.</p>',
        'owner_admin_id' => $admin->id,
    ]);
}

function articleDeletionMagazine(): Magazine
{
    return Magazine::query()->forceCreate([
        'language' => 'en',
        'title' => 'Deletion Issue',
        'issue_number' => 'ISSUE-DELETE',
        'isActive' => true,
    ]);
}

test('an Article with no dependent Taza Shumara placement deletes successfully', function (): void {
    $article = deletableArticle($this->admin, 'Independent Article');

    $this->actingAs($this->admin)
        ->delete(route('admin.article.destroy', ['id' => $article->id]))
        ->assertRedirect(route('admin.article.index'))
        ->assertSessionHas('status', 'Article deleted successfully.');

    expect(Article::query()->find($article->id))->toBeNull();
});

test('deleting an Article removes only its Taza Shumara placement and preserves shared records', function (): void {
    $magazine = articleDeletionMagazine();
    $category = Category::query()->forceCreate(['language' => 'en', 'name' => 'Retained Category', 'isActive' => true]);
    $author = Author::query()->forceCreate(['name' => 'Retained Author', 'isActive' => true]);
    $tag = Tags::query()->forceCreate(['language' => 'en', 'name' => 'Retained Tag', 'isActive' => true]);
    $article = deletableArticle($this->admin, 'Placed Article');
    $article->update(['magazine_id' => $magazine->id, 'issue_number' => $magazine->issue_number]);
    $article->categories()->attach($category);
    $article->authors()->attach($author);
    $article->tags()->attach($tag);

    $otherArticle = deletableArticle($this->admin, 'Retained Article');
    $article->relatedArticles()->attach($otherArticle);

    $tazaShumara = TazaShumara::query()->forceCreate([
        'language' => 'en',
        'magazine_id' => $magazine->id,
        'is_active' => true,
    ]);
    $removedPlacement = $tazaShumara->articlePlacements()->create([
        'article_id' => $article->id,
        'display_width' => 'full',
        'position' => 'top',
        'sort_order' => 1,
    ]);
    $retainedPlacement = $tazaShumara->articlePlacements()->create([
        'article_id' => $otherArticle->id,
        'display_width' => 'half',
        'position' => 'center',
        'sort_order' => 2,
    ]);
    MetaTag::query()->create([
        'table_name' => $article->getTable(),
        'table_id' => $article->id,
        'language' => 'en',
        'title' => $article->title,
    ]);

    $this->actingAs($this->admin)
        ->delete(route('admin.article.destroy', ['id' => $article->id]))
        ->assertRedirect(route('admin.article.index'))
        ->assertSessionHas('status', 'Article deleted successfully.');

    expect(Article::query()->find($article->id))->toBeNull()
        ->and(TazaShumaraArticle::query()->find($removedPlacement->id))->toBeNull()
        ->and(TazaShumara::query()->find($tazaShumara->id))->not->toBeNull()
        ->and(TazaShumaraArticle::query()->find($retainedPlacement->id)?->article_id)->toBe($otherArticle->id)
        ->and(Category::query()->find($category->id))->not->toBeNull()
        ->and(Author::query()->find($author->id))->not->toBeNull()
        ->and(Tags::query()->find($tag->id))->not->toBeNull()
        ->and(Magazine::query()->find($magazine->id))->not->toBeNull()
        ->and(MetaTag::query()->where('table_name', $article->getTable())->where('table_id', $article->id)->exists())->toBeFalse()
        ->and($otherArticle->relatedArticles()->whereKey($article->id)->exists())->toBeFalse();
});
