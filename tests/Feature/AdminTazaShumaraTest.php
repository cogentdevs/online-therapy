<?php

use App\Models\Article;
use App\Models\Language;
use App\Models\Magazine;
use App\Models\TazaShumara;
use App\Models\TazaShumaraArticle;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();
    $this->urdu = Language::query()->create(['name' => 'Urdu', 'code' => 'ur', 'is_active' => true]);
    $this->english = Language::query()->create(['name' => 'English', 'code' => 'en', 'is_active' => true]);
    $this->admin = User::factory()->create(['is_active' => true]);
    $this->admin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

function tazaMagazine(string $title, string $language = 'ur'): Magazine
{
    return Magazine::query()->forceCreate([
        'language' => $language,
        'title' => $title,
        'issue_number' => fake()->unique()->numerify('ISS-###'),
        'isActive' => true,
    ]);
}

function tazaArticle(string $title, string $language = 'ur'): Article
{
    return Article::query()->forceCreate([
        'language' => $language,
        'title' => $title,
        'isActive' => true,
    ]);
}

test('Taza Shumara schema uses language codes and constrained Article placements', function () {
    expect(Schema::hasColumns('taza_shumara', [
        'language', 'magazine_id', 'cover_image', 'show_title', 'show_short_description', 'is_active',
    ]))->toBeTrue()
        ->and(Schema::hasColumn('taza_shumara', 'language_id'))->toBeFalse()
        ->and(Schema::hasColumns('taza_shumara_articles', [
            'taza_shumara_id', 'article_id', 'display_width', 'position', 'sort_order',
        ]))->toBeTrue();

    $record = TazaShumara::query()->create(['language' => 'ur', 'magazine_id' => tazaMagazine('Defaults')->id]);

    expect($record->show_title)->toBeTrue()
        ->and($record->show_short_description)->toBeTrue()
        ->and($record->is_active)->toBeTrue();
});

test('creating and reactivating records enforces one active record per language only', function () {
    $this->actingAs($this->admin);
    $urduOld = TazaShumara::query()->create(['language' => 'ur', 'magazine_id' => tazaMagazine('Urdu Old')->id]);
    $english = TazaShumara::query()->create(['language' => 'en', 'magazine_id' => tazaMagazine('English', 'en')->id]);

    $this->post(route('admin.taza-shumara.store'), [
        'language' => 'ur', 'magazine_id' => tazaMagazine('Urdu New')->id,
        'show_title' => '1', 'show_short_description' => '1', 'is_active' => '1',
    ])->assertRedirect();

    $urduNew = TazaShumara::query()->where('language', 'ur')->latest('id')->firstOrFail();
    expect($urduOld->refresh()->is_active)->toBeFalse()
        ->and($urduNew->is_active)->toBeTrue()
        ->and($english->refresh()->is_active)->toBeTrue()
        ->and(TazaShumara::query()->where('language', 'ur')->count())->toBe(2);

    $this->post(route('admin.taza-shumara.update', ['tazaShumara' => $urduOld->id]), [
        'language' => 'ur', 'magazine_id' => $urduOld->magazine_id,
        'show_title' => '1', 'show_short_description' => '0', 'is_active' => '1',
    ])->assertRedirect(route('admin.taza-shumara.index'));

    expect($urduOld->refresh()->is_active)->toBeTrue()
        ->and($urduNew->refresh()->is_active)->toBeFalse()
        ->and($english->refresh()->is_active)->toBeTrue();

    $this->post(route('admin.taza-shumara.update', ['tazaShumara' => $urduOld->id]), [
        'language' => 'ur', 'magazine_id' => $urduOld->magazine_id,
        'show_title' => '1', 'show_short_description' => '0', 'is_active' => '0',
    ]);

    expect(TazaShumara::query()->where('language', 'ur')->where('is_active', true)->exists())->toBeFalse()
        ->and($english->refresh()->is_active)->toBeTrue();
});

test('admin pages use system languages Select2 and overall Magazine and Article options', function () {
    $urduMagazine = tazaMagazine('Urdu Magazine');
    tazaMagazine('English Magazine', 'en');
    $record = TazaShumara::query()->create(['language' => 'ur', 'magazine_id' => $urduMagazine->id]);
    tazaArticle('Urdu Article');
    tazaArticle('English Article', 'en');

    $this->actingAs($this->admin)
        ->get(route('admin.taza-shumara.create'))
        ->assertSuccessful()
        ->assertSee('Urdu Magazine')
        ->assertSee('English Magazine')
        ->assertSee('value="ur"', false)
        ->assertSee('value="en"', false)
        ->assertSee('class="form-select select2', false)
        ->assertSee('Taza Shumara Articles')
        ->assertSee('name="articles[0][article_id]"', false)
        ->assertSee('name="articles[0][display_width]"', false)
        ->assertSee('name="articles[0][position]"', false)
        ->assertSee('name="articles[0][sort_order]"', false)
        ->assertSee('Add Another Article')
        ->assertSee('Recommended size: 755 × 440 px.');

    $this->get(route('admin.taza-shumara.manage', ['tazaShumara' => $record->id]))
        ->assertSuccessful()
        ->assertSee('Urdu Article')
        ->assertSee('English Article')
        ->assertSee('Articles are available independently of the selected Magazine.');
});

test('combined Add request creates parent and multiple Article placements atomically', function () {
    $magazine = tazaMagazine('Combined Add Magazine');
    $articles = collect([
        tazaArticle('Full Top'),
        tazaArticle('Half Center'),
        tazaArticle('Third Bottom'),
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.taza-shumara.store'), [
            'language' => 'ur',
            'magazine_id' => $magazine->id,
            'show_title' => '1',
            'show_short_description' => '1',
            'is_active' => '1',
            'articles' => [
                ['article_id' => $articles[0]->id, 'display_width' => 'full', 'position' => 'top', 'sort_order' => 1],
                ['article_id' => $articles[1]->id, 'display_width' => 'half', 'position' => 'center', 'sort_order' => 2],
                ['article_id' => $articles[2]->id, 'display_width' => 'third', 'position' => 'bottom', 'sort_order' => 3],
            ],
        ])
        ->assertRedirect(route('admin.taza-shumara.index'))
        ->assertSessionHas('status');

    $record = TazaShumara::query()->sole();
    $placements = TazaShumaraArticle::query()
        ->where('taza_shumara_id', $record->id)
        ->orderBy('id')
        ->get();

    expect($placements)->toHaveCount(3)
        ->and($placements->pluck('taza_shumara_id')->unique()->all())->toBe([$record->id])
        ->and($placements->pluck('display_width')->all())->toBe(['full', 'half', 'third'])
        ->and($placements->pluck('position')->all())->toBe(['top', 'center', 'bottom'])
        ->and($placements->pluck('sort_order')->all())->toBe([1, 2, 3]);
});

test('combined Add rejects duplicate Articles and leaves no partial parent record', function () {
    $article = tazaArticle('Duplicate Submission');

    $this->actingAs($this->admin)
        ->post(route('admin.taza-shumara.store'), [
            'language' => 'ur',
            'magazine_id' => tazaMagazine('Duplicate Magazine')->id,
            'show_title' => '1',
            'show_short_description' => '1',
            'is_active' => '1',
            'articles' => [
                ['article_id' => $article->id, 'display_width' => 'full', 'position' => 'top', 'sort_order' => 1],
                ['article_id' => $article->id, 'display_width' => 'half', 'position' => 'top', 'sort_order' => 2],
            ],
        ])
        ->assertSessionHasErrors('articles.1.article_id');

    expect(TazaShumara::query()->exists())->toBeFalse()
        ->and(TazaShumaraArticle::query()->exists())->toBeFalse();
});

test('Edit page preloads and synchronizes placements in one request without recreating retained rows', function () {
    $record = TazaShumara::query()->create(['language' => 'ur', 'magazine_id' => tazaMagazine('Edit Magazine')->id]);
    $retained = $record->articlePlacements()->create([
        'article_id' => tazaArticle('Retained')->id, 'display_width' => 'full', 'position' => 'top', 'sort_order' => 1,
    ]);
    $removed = $record->articlePlacements()->create([
        'article_id' => tazaArticle('Removed')->id, 'display_width' => 'half', 'position' => 'center', 'sort_order' => 2,
    ]);
    $addedArticle = tazaArticle('Added');

    $this->actingAs($this->admin)
        ->get(route('admin.taza-shumara.edit', ['tazaShumara' => $record->id]))
        ->assertSuccessful()
        ->assertSee('Retained')
        ->assertSee('Removed')
        ->assertSee('name="articles[0][id]"', false)
        ->assertSee('Taza Shumara Articles');

    $this->post(route('admin.taza-shumara.update', ['tazaShumara' => $record->id]), [
        'language' => 'ur',
        'magazine_id' => $record->magazine_id,
        'show_title' => '1',
        'show_short_description' => '0',
        'is_active' => '1',
        'articles' => [
            ['id' => $retained->id, 'article_id' => $retained->article_id, 'display_width' => 'third', 'position' => 'bottom', 'sort_order' => 8],
            ['article_id' => $addedArticle->id, 'display_width' => 'half', 'position' => 'center', 'sort_order' => 4],
        ],
    ])->assertRedirect(route('admin.taza-shumara.index'));

    expect($retained->refresh()->display_width)->toBe('third')
        ->and($retained->position)->toBe('bottom')
        ->and($retained->sort_order)->toBe(8)
        ->and(TazaShumaraArticle::query()->whereKey($removed->id)->exists())->toBeFalse()
        ->and($record->articlePlacements()->where('article_id', $addedArticle->id)->value('display_width'))->toBe('half')
        ->and($record->articlePlacements()->count())->toBe(2);
});

test('Article placements validate allowed values ordering and duplicate scope', function () {
    $this->actingAs($this->admin);
    $article = tazaArticle('Reusable Article');
    $first = TazaShumara::query()->create(['language' => 'ur', 'magazine_id' => tazaMagazine('First')->id]);
    $second = TazaShumara::query()->create(['language' => 'ur', 'magazine_id' => tazaMagazine('Second')->id, 'is_active' => false]);
    $payload = ['article_id' => $article->id, 'display_width' => 'half', 'position' => 'center', 'sort_order' => 2];

    $this->post(route('admin.taza-shumara.articles.store', ['tazaShumara' => $first->id]), $payload)
        ->assertSessionHasNoErrors();
    $this->post(route('admin.taza-shumara.articles.store', ['tazaShumara' => $first->id]), $payload)
        ->assertSessionHasErrors('article_id');
    $this->post(route('admin.taza-shumara.articles.store', ['tazaShumara' => $second->id]), $payload)
        ->assertSessionHasNoErrors();
    $this->post(route('admin.taza-shumara.articles.store', ['tazaShumara' => $second->id]), [
        'article_id' => tazaArticle('Invalid Layout')->id,
        'display_width' => 'quarter', 'position' => 'side', 'sort_order' => -1,
    ])->assertSessionHasErrors(['display_width', 'position', 'sort_order']);

    $placement = $first->articlePlacements()->firstOrFail();

    $this->post(route('admin.taza-shumara.articles.update', [
        'tazaShumara' => $first->id,
        'placement' => $placement->id,
    ]), [
        'article_id' => $article->id,
        'display_width' => 'third',
        'position' => 'bottom',
        'sort_order' => 1,
    ])->assertRedirect(route('admin.taza-shumara.manage', ['tazaShumara' => $first->id]));

    expect(TazaShumaraArticle::query()->where('article_id', $article->id)->count())->toBe(2)
        ->and($placement->refresh()->display_width)->toBe('third')
        ->and($placement->position)->toBe('bottom')
        ->and($placement->sort_order)->toBe(1);

    $this->delete(route('admin.taza-shumara.articles.destroy', [
        'tazaShumara' => $first->id,
        'placement' => $placement->id,
    ]))->assertSessionHasNoErrors();

    expect($first->articlePlacements()->exists())->toBeFalse()
        ->and($second->articlePlacements()->where('article_id', $article->id)->exists())->toBeTrue();
});

test('custom cover saves without modifying the Magazine cover', function () {
    $magazine = tazaMagazine('Cover Magazine');
    $magazine->update(['cover_image' => 'images/backend-images/magazines/original.jpg']);

    $this->actingAs($this->admin)
        ->post(route('admin.taza-shumara.store'), [
            'language' => 'ur', 'magazine_id' => $magazine->id,
            'cover_image' => UploadedFile::fake()->image('custom.webp'),
            'show_title' => '0', 'show_short_description' => '0', 'is_active' => '1',
        ])->assertRedirect();

    $record = TazaShumara::query()->firstOrFail();
    expect($record->cover_image)->toStartWith('images/backend-images/taza-shumara/')
        ->and($record->show_title)->toBeFalse()
        ->and($record->show_short_description)->toBeFalse()
        ->and($magazine->refresh()->cover_image)->toBe('images/backend-images/magazines/original.jpg');

    if (is_file(public_path($record->cover_image))) {
        unlink(public_path($record->cover_image));
    }
});

test('Admin can remove only the Taza Shumara custom cover while editing', function () {
    $magazine = tazaMagazine('Remove Custom Cover Magazine');
    $magazine->update(['cover_image' => 'images/backend-images/magazines/original-cover.webp']);

    $this->actingAs($this->admin)
        ->post(route('admin.taza-shumara.store'), [
            'language' => 'ur',
            'magazine_id' => $magazine->id,
            'cover_image' => UploadedFile::fake()->image('mistake.webp'),
            'show_title' => '1',
            'show_short_description' => '1',
            'is_active' => '1',
        ])
        ->assertSessionHasNoErrors();

    $record = TazaShumara::query()->firstOrFail();
    $customCoverPath = public_path($record->cover_image);

    expect(is_file($customCoverPath))->toBeTrue();

    $this->get(route('admin.taza-shumara.edit', ['tazaShumara' => $record->id]))
        ->assertSuccessful()
        ->assertSee('Remove Cover')
        ->assertSee('data-delete-confirm', false)
        ->assertSee('id="remove-taza-shumara-cover-form"', false)
        ->assertDontSee('name="remove_cover_image"', false);

    $this->delete(route('admin.taza-shumara.cover.destroy', ['tazaShumara' => $record->id]))
        ->assertRedirect(route('admin.taza-shumara.edit', ['tazaShumara' => $record->id]));

    expect($record->refresh()->cover_image)->toBeNull()
        ->and(is_file($customCoverPath))->toBeFalse()
        ->and($magazine->refresh()->cover_image)->toBe('images/backend-images/magazines/original-cover.webp');

    $adminJavaScript = file_get_contents(resource_path('js/admin/admin.js'));
    expect(strpos($adminJavaScript, 'deleteButton.form instanceof HTMLFormElement'))
        ->toBeLessThan(strpos($adminJavaScript, "deleteButton.closest('form')"));
});

test('Taza Shumara RBAC registry and routes protect each action', function () {
    expect(config('admin_modules.modules.taza-shumara'))->toBe([
        'label' => 'Taza Shumara',
        'group' => 'Website',
        'actions' => ['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'],
    ]);

    $role = Role::findOrCreate('taza-viewer', 'web');
    $role->syncPermissions(['admin.access', 'taza-shumara.view']);
    $viewer = User::factory()->create(['is_active' => true]);
    $viewer->assignRole($role);

    $this->actingAs($viewer)->get(route('admin.taza-shumara.index'))->assertSuccessful();
    $this->get(route('admin.taza-shumara.create'))->assertForbidden();
});
