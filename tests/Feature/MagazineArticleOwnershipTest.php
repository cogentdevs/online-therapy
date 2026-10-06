<?php

use App\Models\Article;
use App\Models\Language;
use App\Models\Magazine;
use App\Models\MediaStorageLocation;
use App\Models\StorageProvider;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use App\Services\Storage\MediaStorageService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();

    $this->superAdmin = User::factory()->create(['name' => 'Ownership Root Admin', 'is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
    $this->language = Language::query()->create(['name' => 'English', 'code' => 'en', 'is_active' => true]);
    $this->provider = StorageProvider::query()->create([
        'name' => 'Local Server',
        'slug' => 'local',
        'disk' => 'local_media',
        'provider_type' => StorageProvider::TYPE_LOCAL,
        'is_active' => true,
        'is_default' => true,
        'priority' => 1,
    ]);
});

function createContentOwnershipRole(string $name, array $permissions): Role
{
    $role = Role::findOrCreate($name, 'web');
    $role->syncPermissions(array_values(array_unique(['admin.access', ...$permissions])));

    return $role;
}

function createContentOwnershipAdmin(string $name, Role $role, User $parentAdmin): User
{
    $admin = User::factory()->create([
        'name' => $name,
        'is_active' => true,
        'parent_admin_id' => $parentAdmin->id,
    ]);
    $admin->syncRoles([$role]);

    return $admin;
}

function contentOwnershipArticlePayload(string $title, array $extra = []): array
{
    return [
        'language' => 'en',
        'title' => $title,
        'article' => '<p>Operational ownership article body.</p>',
        'isFree' => '0',
        'isFeatured' => '0',
        ...$extra,
    ];
}

function contentOwnershipMagazinePayload(int $providerId, string $title, array $extra = []): array
{
    return [
        'language' => 'en',
        'title' => $title,
        'pdf' => UploadedFile::fake()->create($title.'.pdf', 100, 'application/pdf'),
        'provider_ids' => [$providerId],
        'isFree' => '0',
        'isFeatured' => '0',
        'is_downloadable' => '0',
        ...$extra,
    ];
}

test('ownership columns are nullable indexed foreign keys with null on delete', function () {
    foreach (['magazines', 'articles'] as $tableName) {
        expect(Schema::hasColumn($tableName, 'owner_admin_id'))->toBeTrue();

        $ownerColumn = collect(Schema::getColumns($tableName))->firstWhere('name', 'owner_admin_id');
        $ownerForeignKey = collect(Schema::getForeignKeys($tableName))
            ->first(fn (array $foreignKey): bool => $foreignKey['columns'] === ['owner_admin_id']);
        $ownerIndex = collect(Schema::getIndexes($tableName))
            ->first(fn (array $index): bool => $index['columns'] === ['owner_admin_id']);

        expect($ownerColumn['nullable'])->toBeTrue()
            ->and($ownerForeignKey['foreign_table'])->toBe('users')
            ->and($ownerForeignKey['foreign_columns'])->toBe(['id'])
            ->and(mb_strtolower($ownerForeignKey['on_delete']))->toBe('set null')
            ->and($ownerIndex)->not->toBeNull();
    }

    $owner = User::factory()->create();
    $magazine = Magazine::query()->forceCreate(['title' => 'Null On Delete Magazine', 'owner_admin_id' => $owner->id]);
    $article = Article::query()->forceCreate(['title' => 'Null On Delete Article', 'owner_admin_id' => $owner->id]);
    $owner->delete();

    expect($magazine->refresh()->owner_admin_id)->toBeNull()
        ->and($article->refresh()->owner_admin_id)->toBeNull();
});

test('backfill assigns only clearly eligible backend Admin creators', function () {
    $customRole = createContentOwnershipRole('ownership-backfill-admin', []);
    $customAdmin = createContentOwnershipAdmin('Backfill Custom Admin', $customRole, $this->superAdmin);
    $frontendUser = User::factory()->create();
    $frontendUser->assignRole(Role::findOrCreate('user', 'web'));

    $superMagazine = Magazine::query()->forceCreate(['title' => 'Super Backfill', 'created_by' => $this->superAdmin->id]);
    $customArticle = Article::query()->forceCreate(['title' => 'Custom Backfill', 'created_by' => $customAdmin->id]);
    $frontendMagazine = Magazine::query()->forceCreate(['title' => 'Frontend Legacy', 'created_by' => $frontendUser->id]);
    $systemArticle = Article::query()->forceCreate(['title' => 'System Legacy']);

    $backfillMigration = require database_path('migrations/2026_08_29_050742_backfill_magazine_and_article_owner_admin_ids.php');
    $backfillMigration->up();

    expect($superMagazine->refresh()->owner_admin_id)->toBe($this->superAdmin->id)
        ->and($customArticle->refresh()->owner_admin_id)->toBe($customAdmin->id)
        ->and($frontendMagazine->refresh()->owner_admin_id)->toBeNull()
        ->and($systemArticle->refresh()->owner_admin_id)->toBeNull();
});

test('Magazine and Article creation assign audit creator and operational owner independently', function () {
    $role = createContentOwnershipRole('ownership-creator', ['magazines.create', 'articles.create']);
    $admin = createContentOwnershipAdmin('Ownership Creator', $role, $this->superAdmin);

    $this->mock(MediaStorageService::class, function ($mock): void {
        $mock->shouldReceive('store')->once()->andReturn(new Collection([
            new MediaStorageLocation(['status' => MediaStorageLocation::STATUS_AVAILABLE]),
        ]));
    });

    $this->actingAs($admin)
        ->post(route('admin.magazine.store'), contentOwnershipMagazinePayload($this->provider->id, 'Owned Magazine'))
        ->assertRedirect(route('admin.magazine.index'));
    $this->post(route('admin.article.store'), contentOwnershipArticlePayload('Owned Article'))
        ->assertRedirect(route('admin.article.index'));

    $magazine = Magazine::query()->where('title', 'Owned Magazine')->firstOrFail();
    $article = Article::query()->where('title', 'Owned Article')->firstOrFail();

    expect($magazine->created_by)->toBe($admin->id)
        ->and($magazine->owner_admin_id)->toBe($admin->id)
        ->and($magazine->ownerAdmin->is($admin))->toBeTrue()
        ->and($article->created_by)->toBe($admin->id)
        ->and($article->owner_admin_id)->toBe($admin->id)
        ->and($article->ownerAdmin->is($admin))->toBeTrue();
});

test('Magazine and Article listings follow full descendant ownership scope', function () {
    $role = createContentOwnershipRole('ownership-list-viewer', ['magazines.view', 'articles.view']);
    $adminA = createContentOwnershipAdmin('Ownership Admin A', $role, $this->superAdmin);
    $adminB = createContentOwnershipAdmin('Ownership Admin B', $role, $adminA);
    $adminC = createContentOwnershipAdmin('Ownership Admin C', $role, $adminB);
    $adminX = createContentOwnershipAdmin('Ownership Admin X', $role, $this->superAdmin);

    foreach ([$adminA, $adminB, $adminC, $adminX] as $admin) {
        Magazine::query()->forceCreate(['title' => 'Magazine '.$admin->name, 'owner_admin_id' => $admin->id]);
        Article::query()->forceCreate(['title' => 'Article '.$admin->name, 'article' => 'Body', 'owner_admin_id' => $admin->id]);
    }

    $this->actingAs($adminA)->get(route('admin.magazine.index'))
        ->assertSuccessful()->assertSee('Magazine Ownership Admin A')->assertSee('Magazine Ownership Admin B')
        ->assertSee('Magazine Ownership Admin C')->assertDontSee('Magazine Ownership Admin X');
    $this->get(route('admin.article.index'))
        ->assertSuccessful()->assertSee('Article Ownership Admin A')->assertSee('Article Ownership Admin B')
        ->assertSee('Article Ownership Admin C')->assertDontSee('Article Ownership Admin X');
    $this->get(route('admin.magazine.show', ['id' => Magazine::query()->where('owner_admin_id', $adminC->id)->value('id')]))
        ->assertSuccessful();
    $this->get(route('admin.article.show', ['id' => Article::query()->where('owner_admin_id', $adminC->id)->value('id')]))
        ->assertSuccessful();

    $this->actingAs($adminB)->get(route('admin.magazine.index'))
        ->assertSuccessful()->assertDontSee('Magazine Ownership Admin A')->assertSee('Magazine Ownership Admin B')
        ->assertSee('Magazine Ownership Admin C')->assertDontSee('Magazine Ownership Admin X');
    $this->get(route('admin.article.index'))
        ->assertSuccessful()->assertDontSee('Article Ownership Admin A')->assertSee('Article Ownership Admin B')
        ->assertSee('Article Ownership Admin C')->assertDontSee('Article Ownership Admin X');

    $this->actingAs($adminC)->get(route('admin.magazine.index'))
        ->assertSuccessful()->assertDontSee('Magazine Ownership Admin A')->assertDontSee('Magazine Ownership Admin B')
        ->assertSee('Magazine Ownership Admin C')->assertDontSee('Magazine Ownership Admin X');
    $this->get(route('admin.article.index'))
        ->assertSuccessful()->assertDontSee('Article Ownership Admin A')->assertDontSee('Article Ownership Admin B')
        ->assertSee('Article Ownership Admin C')->assertDontSee('Article Ownership Admin X');

    $this->actingAs($this->superAdmin)->get(route('admin.magazine.index'))
        ->assertSuccessful()->assertSee('Magazine Ownership Admin A')->assertSee('Magazine Ownership Admin X');
    $this->get(route('admin.article.index'))
        ->assertSuccessful()->assertSee('Article Ownership Admin A')->assertSee('Article Ownership Admin X');
});

test('direct Magazine and Article actions reject unrelated ownership including publish and PDF access', function () {
    $managerRole = createContentOwnershipRole('ownership-action-manager', [
        'magazines.view', 'magazines.edit', 'magazines.delete', 'magazines.publish',
        'magazines.pdf.view', 'magazines.pdf.download', 'magazines.related.remove',
        'articles.view', 'articles.edit', 'articles.delete', 'articles.publish', 'articles.related.remove',
    ]);
    $ownerRole = createContentOwnershipRole('ownership-action-owner', []);
    $adminA = createContentOwnershipAdmin('Action Admin A', $managerRole, $this->superAdmin);
    $adminX = createContentOwnershipAdmin('Action Admin X', $ownerRole, $this->superAdmin);
    $magazine = Magazine::query()->forceCreate([
        'language' => 'en', 'title' => 'Unrelated Magazine', 'isActive' => true,
        'status' => Magazine::STATUS_DRAFT, 'owner_admin_id' => $adminX->id,
    ]);
    $article = Article::query()->forceCreate([
        'language' => 'en', 'title' => 'Unrelated Article', 'article' => 'Body', 'isActive' => true,
        'status' => Article::STATUS_DRAFT, 'owner_admin_id' => $adminX->id,
    ]);

    $this->actingAs($adminA)->get(route('admin.magazine.show', ['id' => $magazine->id]))->assertForbidden();
    $this->get(route('admin.magazine.edit', ['id' => $magazine->id]))->assertForbidden();
    $this->post(route('admin.magazine.update', ['id' => $magazine->id]), [
        ...contentOwnershipMagazinePayload($this->provider->id, $magazine->title),
        'pdf' => null,
        'isActive' => '1',
    ])->assertForbidden();
    $this->post(route('admin.magazine.publish', ['id' => $magazine->id]))->assertForbidden();
    $this->get(route('admin.magazine.pdf.view', ['magazine' => $magazine->id, 'storageLocation' => 999]))->assertForbidden();
    $this->get(route('admin.magazine.pdf.download', ['magazine' => $magazine->id, 'storageLocation' => 999]))->assertForbidden();
    $this->delete(route('admin.magazine.destroy', ['id' => $magazine->id]))->assertForbidden();

    $this->get(route('admin.article.show', ['id' => $article->id]))->assertForbidden();
    $this->get(route('admin.article.edit', ['id' => $article->id]))->assertForbidden();
    $this->post(route('admin.article.update', ['id' => $article->id]), [
        ...contentOwnershipArticlePayload($article->title),
        'isActive' => '1',
    ])->assertForbidden();
    $this->post(route('admin.article.publish', ['id' => $article->id]))->assertForbidden();
    $this->delete(route('admin.article.destroy', ['id' => $article->id]))->assertForbidden();

    expect(Magazine::query()->find($magazine->id))->not->toBeNull()
        ->and(Article::query()->find($article->id))->not->toBeNull();
});

test('null owners are Super Admin-only and ordinary updates cannot reassign ownership', function () {
    $role = createContentOwnershipRole('ownership-null-manager', [
        'magazines.view', 'magazines.edit', 'articles.view', 'articles.edit',
    ]);
    $admin = createContentOwnershipAdmin('Null Owner Admin', $role, $this->superAdmin);
    $otherAdmin = createContentOwnershipAdmin('Injected Owner Admin', $role, $this->superAdmin);
    $legacyMagazine = Magazine::query()->forceCreate(['language' => 'en', 'title' => 'Legacy Magazine']);
    $legacyArticle = Article::query()->forceCreate(['language' => 'en', 'title' => 'Legacy Article', 'article' => 'Body']);
    $ownedMagazine = Magazine::query()->forceCreate([
        'language' => 'en', 'title' => 'Stable Magazine Owner', 'owner_admin_id' => $admin->id,
    ]);
    $ownedArticle = Article::query()->forceCreate([
        'language' => 'en', 'title' => 'Stable Article Owner', 'article' => 'Body', 'owner_admin_id' => $admin->id,
    ]);

    $this->actingAs($admin)->get(route('admin.magazine.show', ['id' => $legacyMagazine->id]))->assertForbidden();
    $this->get(route('admin.article.show', ['id' => $legacyArticle->id]))->assertForbidden();

    $this->post(route('admin.magazine.update', ['id' => $ownedMagazine->id]), [
        'language' => 'en', 'title' => $ownedMagazine->title, 'isFree' => '0', 'isFeatured' => '0',
        'is_downloadable' => '0', 'isActive' => '1', 'owner_admin_id' => $otherAdmin->id,
    ])->assertRedirect(route('admin.magazine.index'));
    $this->post(route('admin.article.update', ['id' => $ownedArticle->id]), [
        ...contentOwnershipArticlePayload($ownedArticle->title),
        'isActive' => '1', 'owner_admin_id' => $otherAdmin->id,
    ])->assertRedirect(route('admin.article.index'));

    expect($ownedMagazine->refresh()->owner_admin_id)->toBe($admin->id)
        ->and($ownedArticle->refresh()->owner_admin_id)->toBe($admin->id);

    $this->actingAs($this->superAdmin)->get(route('admin.magazine.show', ['id' => $legacyMagazine->id]))->assertSuccessful();
    $this->get(route('admin.article.show', ['id' => $legacyArticle->id]))->assertSuccessful();
});

test('related content options and mutations stay inside Ownership Scope', function () {
    $role = createContentOwnershipRole('ownership-related-manager', [
        'magazines.view', 'magazines.create', 'magazines.edit', 'magazines.related.remove',
        'articles.view', 'articles.create', 'articles.edit', 'articles.related.remove',
    ]);
    $adminA = createContentOwnershipAdmin('Related Admin A', $role, $this->superAdmin);
    $adminX = createContentOwnershipAdmin('Related Admin X', $role, $this->superAdmin);
    $ownedMagazine = Magazine::query()->forceCreate(['title' => 'Owned Related Magazine', 'owner_admin_id' => $adminA->id]);
    $unrelatedMagazine = Magazine::query()->forceCreate(['title' => 'Unrelated Related Magazine', 'owner_admin_id' => $adminX->id]);
    $ownedArticle = Article::query()->forceCreate(['title' => 'Owned Related Article', 'article' => 'Body', 'owner_admin_id' => $adminA->id]);
    $unrelatedArticle = Article::query()->forceCreate(['title' => 'Unrelated Related Article', 'article' => 'Body', 'owner_admin_id' => $adminX->id]);

    $this->actingAs($adminA)->get(route('admin.magazine.create'))
        ->assertSuccessful()->assertSee('Owned Related Magazine')->assertDontSee('Unrelated Related Magazine');
    $this->get(route('admin.article.create'))
        ->assertSuccessful()->assertSee('Owned Related Article')->assertDontSee('Unrelated Related Article');

    $this->post(route('admin.magazine.update', ['id' => $ownedMagazine->id]), [
        'language' => 'en', 'title' => $ownedMagazine->title, 'isFree' => '0', 'isFeatured' => '0',
        'is_downloadable' => '0', 'isActive' => '1', 'related_magazine_ids' => [$unrelatedMagazine->id],
    ])->assertSessionHasErrors('related_magazine_ids');
    $this->post(route('admin.article.update', ['id' => $ownedArticle->id]), [
        ...contentOwnershipArticlePayload($ownedArticle->title),
        'isActive' => '1', 'related_article_ids' => [$unrelatedArticle->id],
    ])->assertSessionHasErrors('related_article_ids');

    $ownedMagazine->relatedMagazines()->attach($unrelatedMagazine->id);
    $ownedArticle->relatedArticles()->attach($unrelatedArticle->id);
    $this->get(route('admin.magazine.show', ['id' => $ownedMagazine->id]))
        ->assertSuccessful()->assertDontSee('Unrelated Related Magazine');
    $this->get(route('admin.article.show', ['id' => $ownedArticle->id]))
        ->assertSuccessful()->assertDontSee('Unrelated Related Article');
    $this->delete(route('admin.magazine.related.remove', [
        'magazine' => $ownedMagazine->id, 'relatedMagazine' => $unrelatedMagazine->id,
    ]))->assertForbidden();
    $this->delete(route('admin.article.related.remove', [
        'article' => $ownedArticle->id, 'relatedArticle' => $unrelatedArticle->id,
    ]))->assertForbidden();
});

test('RBAC and operational ownership must both allow access', function () {
    $viewRole = createContentOwnershipRole('ownership-rbac-view', ['magazines.view', 'articles.view']);
    $noViewRole = createContentOwnershipRole('ownership-rbac-no-view', []);
    $ownerWithPermission = createContentOwnershipAdmin('Owner With Permission', $viewRole, $this->superAdmin);
    $ownerWithoutPermission = createContentOwnershipAdmin('Owner Without Permission', $noViewRole, $this->superAdmin);
    $unrelatedAdmin = createContentOwnershipAdmin('Unrelated With Permission', $viewRole, $this->superAdmin);
    $allowedMagazine = Magazine::query()->forceCreate(['title' => 'Allowed Ownership Magazine', 'owner_admin_id' => $ownerWithPermission->id]);
    $deniedMagazine = Magazine::query()->forceCreate(['title' => 'Denied Ownership Magazine', 'owner_admin_id' => $ownerWithoutPermission->id]);
    $allowedArticle = Article::query()->forceCreate(['title' => 'Allowed Ownership Article', 'article' => 'Body', 'owner_admin_id' => $ownerWithPermission->id]);
    $deniedArticle = Article::query()->forceCreate(['title' => 'Denied Ownership Article', 'article' => 'Body', 'owner_admin_id' => $ownerWithoutPermission->id]);

    $this->actingAs($ownerWithPermission)->get(route('admin.magazine.show', ['id' => $allowedMagazine->id]))->assertSuccessful();
    $this->get(route('admin.article.show', ['id' => $allowedArticle->id]))->assertSuccessful();
    $this->actingAs($ownerWithoutPermission)->get(route('admin.magazine.show', ['id' => $deniedMagazine->id]))->assertForbidden();
    $this->get(route('admin.article.show', ['id' => $deniedArticle->id]))->assertForbidden();
    $this->actingAs($unrelatedAdmin)->get(route('admin.magazine.show', ['id' => $allowedMagazine->id]))->assertForbidden();
    $this->get(route('admin.article.show', ['id' => $allowedArticle->id]))->assertForbidden();

    $ownerWithPermission->parent_admin_id = $this->superAdmin->id;
    $ownerWithPermission->save();

    expect($allowedMagazine->owner_admin_id)->toBe($ownerWithPermission->id)
        ->and($allowedArticle->owner_admin_id)->toBe($ownerWithPermission->id);
});
