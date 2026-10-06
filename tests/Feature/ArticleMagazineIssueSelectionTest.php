<?php

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Language;
use App\Models\Magazine;
use App\Models\Tags;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();

    $this->admin = User::factory()->create(['is_active' => true]);
    $this->admin->assignRole(Role::findOrCreate('super-admin', 'web'));
    Language::query()->create(['name' => 'English', 'code' => 'en', 'is_active' => true]);
});

function articleMagazinePayload(array $overrides = []): array
{
    return [
        'language' => 'en',
        'title' => 'Magazine-linked Article',
        'article' => '<p>Article body.</p>',
        'isFree' => '0',
        'isFeatured' => '0',
        'isActive' => '0',
        ...$overrides,
    ];
}

function articleIssueMagazine(string $title, string $issueNumber, bool $isActive = true): Magazine
{
    return Magazine::query()->forceCreate([
        'language' => 'en',
        'title' => $title,
        'issue_number' => $issueNumber,
        'isActive' => $isActive,
    ]);
}

test('magazine id is nullable and magazine deletion retains the article issue snapshot', function () {
    $column = collect(Schema::getColumns('articles'))->firstWhere('name', 'magazine_id');
    $foreignKey = collect(Schema::getForeignKeys('articles'))
        ->first(fn (array $foreignKey): bool => $foreignKey['columns'] === ['magazine_id']);

    expect($column['nullable'])->toBeTrue()
        ->and($foreignKey['foreign_table'])->toBe('magazines')
        ->and($foreignKey['foreign_columns'])->toBe(['id'])
        ->and(mb_strtolower($foreignKey['on_delete']))->toBe('set null');

    $magazine = articleIssueMagazine('Deletion Test Magazine', 'M-DELETE');
    $article = Article::query()->forceCreate([
        'title' => 'Article retained after magazine deletion',
        'magazine_id' => $magazine->id,
        'issue_number' => $magazine->issue_number,
    ]);

    $magazine->delete();

    expect($article->refresh()->magazine_id)->toBeNull()
        ->and($article->issue_number)->toBe('M-DELETE');
});

test('article belongs to its selected magazine', function () {
    $magazine = articleIssueMagazine('Relationship Magazine', 'M-REL');
    $article = Article::query()->forceCreate(['title' => 'Related Article', 'magazine_id' => $magazine->id]);

    expect($article->magazine->is($magazine))->toBeTrue();
});

test('article forms and listing hide magazine issue controls and columns', function () {
    $activeMagazine = articleIssueMagazine('Active Magazine', 'M-100');
    $inactiveMagazine = articleIssueMagazine('Inactive Selected Magazine', 'M-200', false);
    $article = Article::query()->forceCreate([
        'language' => 'en',
        'title' => 'Existing Article',
        'article' => '<p>Body</p>',
        'magazine_id' => $inactiveMagazine->id,
        'issue_number' => $inactiveMagazine->issue_number,
        'owner_admin_id' => $this->admin->id,
    ]);

    $this->actingAs($this->admin)->get(route('admin.article.create'))
        ->assertSuccessful()
        ->assertDontSee('name="magazine_id"', false)
        ->assertDontSee('Magazine / Issue')
        ->assertDontSee('Active Magazine — M-100')
        ->assertDontSee('name="issue_number"', false);

    $this->get(route('admin.article.edit', ['id' => $article->id]))
        ->assertSuccessful()
        ->assertDontSee('name="magazine_id"', false)
        ->assertDontSee('Magazine / Issue')
        ->assertDontSee('Inactive Selected Magazine — M-200')
        ->assertDontSee('name="issue_number"', false);

    $this->get(route('admin.article.index'))
        ->assertSuccessful()
        ->assertDontSee('<th>Issue #</th>', false)
        ->assertDontSee('M-200');
});

test('creating an article derives issue from magazine and ignores submitted issue number', function () {
    $magazine = articleIssueMagazine('Create Magazine', 'M-CREATE');

    $this->actingAs($this->admin)->post(route('admin.article.store'), articleMagazinePayload([
        'magazine_id' => $magazine->id,
        'issue_number' => 'FORGED-ISSUE',
    ]))->assertRedirect(route('admin.article.index'));

    $article = Article::query()->where('title', 'Magazine-linked Article')->firstOrFail();

    expect($article->magazine_id)->toBe($magazine->id)
        ->and($article->issue_number)->toBe('M-CREATE');
});

test('creating an article always assigns the fixed English content language', function () {
    Language::query()->create(['name' => 'Urdu', 'code' => 'ur', 'is_active' => true]);

    $this->actingAs($this->admin)->post(route('admin.article.store'), articleMagazinePayload([
        'language' => 'ur',
        'title' => 'Fixed English Article',
    ]))->assertRedirect(route('admin.article.index'));

    expect(Article::query()->where('title', 'Fixed English Article')->value('language'))->toBe('en');
});

test('creating an article succeeds without language input when English is inactive', function () {
    Language::query()->where('code', 'en')->update(['is_active' => false]);

    $this->actingAs($this->admin)->post(route('admin.article.store'), articleMagazinePayload([
        'title' => 'Inactive English Article',
        'language' => null,
    ]))->assertRedirect(route('admin.article.index'));

    expect(Article::query()->where('title', 'Inactive English Article')->value('language'))->toBe('en');
});

test('article can be created without a magazine payload and submitted issue number is ignored', function () {
    $this->actingAs($this->admin)->post(route('admin.article.store'), articleMagazinePayload([
        'issue_number' => 'FORGED-ISSUE',
    ]))->assertRedirect(route('admin.article.index'));

    $article = Article::query()->where('title', 'Magazine-linked Article')->firstOrFail();

    expect($article->magazine_id)->toBeNull()
        ->and($article->issue_number)->toBeNull();
});

test('updating an article without a hidden magazine payload preserves its magazine and issue snapshot', function () {
    $magazine = articleIssueMagazine('Preserved Magazine', 'M-PRESERVE');
    $article = Article::query()->forceCreate([
        'language' => 'en',
        'title' => 'Existing Magazine Article',
        'article' => '<p>Old body</p>',
        'magazine_id' => $magazine->id,
        'issue_number' => $magazine->issue_number,
        'owner_admin_id' => $this->admin->id,
    ]);

    $this->actingAs($this->admin)->post(route('admin.article.update', ['id' => $article->id]), articleMagazinePayload([
        'title' => 'Updated Magazine Article',
    ]))->assertRedirect(route('admin.article.index'));

    expect($article->refresh()->title)->toBe('Updated Magazine Article')
        ->and($article->magazine_id)->toBe($magazine->id)
        ->and($article->issue_number)->toBe('M-PRESERVE');
});

test('updating and clearing magazine selection synchronizes both article fields', function () {
    $firstMagazine = articleIssueMagazine('First Magazine', 'M-FIRST');
    $secondMagazine = articleIssueMagazine('Second Magazine', 'M-SECOND');
    $article = Article::query()->forceCreate([
        'language' => 'en',
        'title' => 'Article to update',
        'article' => '<p>Old body</p>',
        'magazine_id' => $firstMagazine->id,
        'issue_number' => $firstMagazine->issue_number,
        'owner_admin_id' => $this->admin->id,
    ]);

    $this->actingAs($this->admin)->post(route('admin.article.update', ['id' => $article->id]), articleMagazinePayload([
        'title' => $article->title,
        'magazine_id' => $secondMagazine->id,
        'issue_number' => 'FORGED-ISSUE',
    ]))->assertRedirect(route('admin.article.index'));

    expect($article->refresh()->magazine_id)->toBe($secondMagazine->id)
        ->and($article->issue_number)->toBe('M-SECOND');

    $this->post(route('admin.article.update', ['id' => $article->id]), articleMagazinePayload([
        'title' => $article->title,
        'magazine_id' => null,
    ]))->assertRedirect(route('admin.article.index'));

    expect($article->refresh()->magazine_id)->toBeNull()
        ->and($article->issue_number)->toBeNull();
});

test('invalid magazine id fails validation', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.article.store'), articleMagazinePayload(['magazine_id' => 999999]))
        ->assertSessionHasErrors('magazine_id');

    expect(Article::query()->where('title', 'Magazine-linked Article')->exists())->toBeFalse();
});

test('Article category is a single select and store and update synchronize one category', function () {
    $firstCategory = Category::query()->forceCreate([
        'language' => 'en',
        'name' => 'First Category',
        'isActive' => true,
    ]);
    $secondCategory = Category::query()->forceCreate([
        'language' => 'en',
        'name' => 'Second Category',
        'isActive' => true,
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.article.create'))
        ->assertSuccessful()
        ->assertSee('name="category_id"', false)
        ->assertDontSee('name="category_ids[]"', false)
        ->assertDontSee('id="category_id" name="category_id" data-magazine-language-options multiple', false);

    $this->post(route('admin.article.store'), articleMagazinePayload([
        'category_id' => $firstCategory->id,
    ]))->assertRedirect(route('admin.article.index'));

    $article = Article::query()->where('title', 'Magazine-linked Article')->firstOrFail();

    expect($article->categories()->pluck('categories.id')->all())->toBe([$firstCategory->id]);

    $this->post(route('admin.article.update', ['id' => $article->id]), articleMagazinePayload([
        'title' => $article->title,
        'category_id' => $secondCategory->id,
    ]))->assertRedirect(route('admin.article.index'));

    expect($article->refresh()->categories()->pluck('categories.id')->all())->toBe([$secondCategory->id]);
});

test('editing a legacy multi-category Article does not change its categories until it is saved', function () {
    $firstCategory = Category::query()->forceCreate([
        'language' => 'en',
        'name' => 'Legacy First Category',
        'isActive' => true,
    ]);
    $secondCategory = Category::query()->forceCreate([
        'language' => 'en',
        'name' => 'Legacy Second Category',
        'isActive' => true,
    ]);
    $article = Article::query()->forceCreate([
        'language' => 'en',
        'title' => 'Legacy Multi-category Article',
        'article' => '<p>Body</p>',
        'owner_admin_id' => $this->admin->id,
    ]);
    $article->categories()->attach([$firstCategory->id, $secondCategory->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.article.edit', ['id' => $article->id]))
        ->assertSuccessful()
        ->assertSee('name="category_id"', false)
        ->assertSee('value="'.$firstCategory->id.'"', false);

    expect($article->refresh()->categories()->pluck('categories.id')->sort()->values()->all())
        ->toBe([$firstCategory->id, $secondCategory->id]);
});

test('Article category validation rejects multiple submitted values', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.article.store'), articleMagazinePayload([
            'category_id' => [1, 2],
        ]))
        ->assertSessionHasErrors('category_id');
});

test('Article author is a single select and store and update synchronize one Author', function () {
    $firstAuthor = Author::query()->forceCreate([
        'name' => 'First Author',
        'isActive' => true,
    ]);
    $secondAuthor = Author::query()->forceCreate([
        'name' => 'Second Author',
        'isActive' => true,
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.article.create'))
        ->assertSuccessful()
        ->assertSee('name="author_id"', false)
        ->assertDontSee('name="author_ids[]"', false)
        ->assertDontSee('id="author_id" name="author_id" multiple', false);

    $this->post(route('admin.article.store'), articleMagazinePayload([
        'author_id' => $firstAuthor->id,
    ]))->assertRedirect(route('admin.article.index'));

    $article = Article::query()->where('title', 'Magazine-linked Article')->firstOrFail();

    expect($article->authors()->pluck('authors.id')->all())->toBe([$firstAuthor->id]);

    $this->post(route('admin.article.update', ['id' => $article->id]), articleMagazinePayload([
        'title' => $article->title,
        'author_id' => $secondAuthor->id,
    ]))->assertRedirect(route('admin.article.index'));

    expect($article->refresh()->authors()->pluck('authors.id')->all())->toBe([$secondAuthor->id]);
});

test('editing a legacy multi-author Article does not change its authors until it is saved', function () {
    $firstAuthor = Author::query()->forceCreate([
        'name' => 'Legacy First Author',
        'isActive' => true,
    ]);
    $secondAuthor = Author::query()->forceCreate([
        'name' => 'Legacy Second Author',
        'isActive' => true,
    ]);
    $article = Article::query()->forceCreate([
        'language' => 'en',
        'title' => 'Legacy Multi-author Article',
        'article' => '<p>Body</p>',
        'owner_admin_id' => $this->admin->id,
    ]);
    $article->authors()->attach([$firstAuthor->id, $secondAuthor->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.article.edit', ['id' => $article->id]))
        ->assertSuccessful()
        ->assertSee('name="author_id"', false)
        ->assertSee('value="'.$firstAuthor->id.'"', false);

    expect($article->refresh()->authors()->pluck('authors.id')->sort()->values()->all())
        ->toBe([$firstAuthor->id, $secondAuthor->id]);
});

test('Article author validation rejects multiple submitted values', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.article.store'), articleMagazinePayload([
            'author_id' => [1, 2],
        ]))
        ->assertSessionHasErrors('author_id');
});

test('hidden Article Tags are not rendered or detached when an Article is updated', function () {
    $tag = Tags::query()->forceCreate([
        'language' => 'en',
        'name' => 'Retained Tag',
        'isActive' => true,
    ]);
    $article = Article::query()->forceCreate([
        'language' => 'en',
        'title' => 'Tagged Article',
        'article' => '<p>Body</p>',
        'owner_admin_id' => $this->admin->id,
    ]);
    $article->tags()->attach($tag);

    $this->actingAs($this->admin)
        ->get(route('admin.article.edit', ['id' => $article->id]))
        ->assertSuccessful()
        ->assertDontSee('name="tag_ids[]"', false)
        ->assertDontSee('for="tag_ids"', false);

    $this->post(route('admin.article.update', ['id' => $article->id]), articleMagazinePayload([
        'title' => $article->title,
    ]))->assertRedirect(route('admin.article.index'));

    expect($article->refresh()->tags()->pluck('tags.id')->all())->toBe([$tag->id]);
});
