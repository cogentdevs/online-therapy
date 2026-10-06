<?php

use App\Models\Article;
use App\Models\Language;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();
    Language::query()->create(['name' => 'Urdu', 'code' => 'ur', 'is_active' => true]);
    Language::query()->create(['name' => 'English', 'code' => 'en', 'is_active' => true]);
    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

function homeArticleAdmin(array $permissions): User
{
    $role = Role::findOrCreate('home-article-admin-'.Str::random(8), 'web');
    $role->syncPermissions(['admin.access', ...$permissions]);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    return $admin;
}

function homeArticlePayload(string $title, array $overrides = []): array
{
    return array_merge([
        'language' => 'ur',
        'title' => $title,
        'article' => 'Article body content.',
        'isFree' => '0',
        'isFeatured' => '0',
        'show_on_latest' => '0',
        'show_on_editorial_center' => '0',
        'show_on_editorial_featured' => '0',
        'show_visit_counter' => '0',
    ], $overrides);
}

test('migration adds homepage placement flags with false defaults and boolean casts', function () {
    expect(Schema::hasColumns((new Article)->getTable(), [
        'show_on_latest',
        'show_on_editorial_center',
        'show_on_editorial_featured',
        'show_visit_counter',
    ]))->toBeTrue();

    $article = Article::query()->forceCreate(['title' => 'Default flags']);

    expect($article->show_on_latest)->toBeFalse()
        ->and($article->show_on_editorial_center)->toBeFalse()
        ->and($article->show_on_editorial_featured)->toBeFalse()
        ->and($article->show_visit_counter)->toBeFalse();
});

test('Article CRUD saves all placement flags and displays them in add and edit forms', function () {
    $this->actingAs($this->superAdmin)
        ->get(route('admin.article.create'))
        ->assertSuccessful()
        ->assertSee('Homepage &amp; Display Settings', false)
        ->assertSee('name="show_on_latest"', false)
        ->assertSee('name="show_on_editorial_center"', false)
        ->assertSee('name="show_on_editorial_featured"', false)
        ->assertSee('name="show_visit_counter"', false);

    $this->post(route('admin.article.store'), homeArticlePayload('Placement Article', [
        'show_on_latest' => '1',
        'show_on_editorial_center' => '1',
        'show_on_editorial_featured' => '1',
        'show_visit_counter' => '1',
    ]))->assertRedirect(route('admin.article.index'));

    $article = Article::query()->where('title', 'Placement Article')->firstOrFail();

    expect($article->show_on_latest)->toBeTrue()
        ->and($article->show_on_editorial_center)->toBeTrue()
        ->and($article->show_on_editorial_featured)->toBeTrue()
        ->and($article->show_visit_counter)->toBeTrue();

    $this->get(route('admin.article.edit', ['id' => $article->id]))
        ->assertSuccessful()
        ->assertSee('id="show_on_latest_yes" name="show_on_latest" type="radio" value="1" checked', false)
        ->assertSee('id="show_visit_counter_yes" name="show_visit_counter" type="radio" value="1" checked', false);
});

test('normal Article CRUD replaces the globally featured Editorial Article', function () {
    $this->actingAs($this->superAdmin)
        ->post(route('admin.article.store'), homeArticlePayload('Featured A', [
            'show_on_editorial_featured' => '1',
        ]))
        ->assertRedirect(route('admin.article.index'));

    $this->post(route('admin.article.store'), homeArticlePayload('Featured B', [
        'show_on_editorial_featured' => '1',
    ]))->assertRedirect(route('admin.article.index'));

    expect(Article::query()->where('show_on_editorial_featured', true)->count())->toBe(1)
        ->and(Article::query()->where('title', 'Featured A')->firstOrFail()->show_on_editorial_featured)->toBeFalse()
        ->and(Article::query()->where('title', 'Featured B')->firstOrFail()->show_on_editorial_featured)->toBeTrue();

    $articleA = Article::query()->where('title', 'Featured A')->firstOrFail();

    $this->post(route('admin.article.update', ['id' => $articleA->id]), homeArticlePayload('Featured A', [
        'isActive' => '1',
        'show_on_editorial_featured' => '1',
    ]))->assertRedirect(route('admin.article.index'));

    expect(Article::query()->where('show_on_editorial_featured', true)->count())->toBe(1)
        ->and($articleA->refresh()->show_on_editorial_featured)->toBeTrue()
        ->and(Article::query()->where('title', 'Featured B')->firstOrFail()->show_on_editorial_featured)->toBeFalse();
});

test('Home Article permissions and Urdu Select2 options follow the Website registry', function () {
    expect(config('admin_modules.modules.home-article.group'))->toBe('Website')
        ->and(config('admin_modules.modules.home-article.actions'))->toBe(['view' => 'View', 'edit' => 'Edit']);

    Article::query()->forceCreate(['language' => 'ur', 'title' => 'Urdu Option']);
    Article::query()->forceCreate(['language' => 'en', 'title' => 'English Option']);
    $viewer = homeArticleAdmin(['home-article.view']);

    $this->actingAs($viewer)
        ->get(route('admin.home-article.index'))
        ->assertSuccessful()
        ->assertSee('Home Article')
        ->assertSee('class="form-select select2', false)
        ->assertSee('Urdu Option')
        ->assertDontSee('English Option');

    $this->put(route('admin.home-article.update'), [])->assertForbidden();
    $this->actingAs(homeArticleAdmin(['dashboard.view']))
        ->get(route('admin.home-article.index'))
        ->assertForbidden();
});

test('Home Article update replaces all placements while preserving visit-counter settings', function () {
    $articles = collect(range(1, 5))->map(fn (int $number) => Article::query()->forceCreate([
        'language' => 'ur',
        'title' => "Article {$number}",
        'show_on_latest' => $number === 5,
        'show_on_editorial_center' => $number === 5,
        'show_on_editorial_featured' => $number === 5,
        'show_visit_counter' => $number === 5,
    ]));

    $this->actingAs($this->superAdmin)
        ->put(route('admin.home-article.update'), [
            'latest_article_ids' => $articles->take(3)->pluck('id')->all(),
            'editorial_center_article_ids' => $articles->slice(1, 3)->pluck('id')->all(),
            'editorial_featured_article_id' => $articles[1]->id,
        ])
        ->assertRedirect(route('admin.home-article.index'))
        ->assertSessionHas('status');

    expect(Article::query()->where('show_on_latest', true)->orderBy('id')->pluck('id')->all())
        ->toBe($articles->take(3)->pluck('id')->all())
        ->and(Article::query()->where('show_on_editorial_center', true)->orderBy('id')->pluck('id')->all())
        ->toBe($articles->slice(1, 3)->pluck('id')->all())
        ->and(Article::query()->where('show_on_editorial_featured', true)->pluck('id')->all())
        ->toBe([$articles[1]->id])
        ->and($articles[4]->refresh()->show_visit_counter)->toBeTrue();

    $this->put(route('admin.home-article.update'), [])
        ->assertRedirect(route('admin.home-article.index'));

    expect(Article::query()->where('show_on_latest', true)->exists())->toBeFalse()
        ->and(Article::query()->where('show_on_editorial_center', true)->exists())->toBeFalse()
        ->and(Article::query()->where('show_on_editorial_featured', true)->exists())->toBeFalse()
        ->and($articles[4]->refresh()->show_visit_counter)->toBeTrue();
});

test('Home Article validation enforces selection limits distinct IDs and existing Articles', function () {
    $articleIds = collect(range(1, 4))->map(fn (int $number) => Article::query()->forceCreate([
        'language' => 'ur',
        'title' => "Validation Article {$number}",
    ])->id)->all();

    $this->actingAs($this->superAdmin)
        ->put(route('admin.home-article.update'), [
            'latest_article_ids' => $articleIds,
            'editorial_center_article_ids' => $articleIds,
        ])
        ->assertSessionHasErrors(['latest_article_ids', 'editorial_center_article_ids']);

    $this->put(route('admin.home-article.update'), [
        'latest_article_ids' => [$articleIds[0], $articleIds[0]],
        'editorial_featured_article_id' => 999999,
    ])->assertSessionHasErrors(['latest_article_ids.1', 'editorial_featured_article_id']);

    expect(Article::query()->where('show_on_latest', true)->exists())->toBeFalse()
        ->and(Article::query()->where('show_on_editorial_center', true)->exists())->toBeFalse()
        ->and(Article::query()->where('show_on_editorial_featured', true)->exists())->toBeFalse();
});
