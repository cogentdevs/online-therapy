<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Language;
use App\Models\Magazine;
use App\Models\MediaStorageLocation;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use App\Services\Storage\MediaStorageService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();

    $this->superAdmin = User::factory()->create(['name' => 'Primary Super Admin', 'is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
    $this->language = Language::query()->create([
        'name' => 'English',
        'code' => 'en',
        'is_active' => true,
    ]);
});

test('super admin create records both ownership actors', function () {
    $this->actingAs($this->superAdmin)
        ->post(route('admin.categories.store'), [
            'language' => $this->language->code,
            'entries' => [['name' => 'Business']],
        ])
        ->assertRedirect(route('admin.categories.index'));

    $category = Category::query()->where('name', 'Business')->firstOrFail();

    expect($category->created_by)->toBe($this->superAdmin->id)
        ->and($category->updated_by)->toBe($this->superAdmin->id);
});

test('custom admin create records the authenticated actor', function () {
    $role = Role::findOrCreate('category-editor', 'web');
    $role->syncPermissions(['admin.access', 'categories.create']);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    $this->actingAs($admin)
        ->post(route('admin.categories.store'), [
            'language' => $this->language->code,
            'entries' => [['name' => 'Technology']],
        ])
        ->assertRedirect(route('admin.categories.index'));

    $category = Category::query()->where('name', 'Technology')->firstOrFail();

    expect($category->created_by)->toBe($admin->id)
        ->and($category->updated_by)->toBe($admin->id);
});

test('update preserves creator and records the latest updater', function () {
    $category = Category::query()->forceCreate([
        'language' => $this->language->code,
        'name' => 'Original',
        'isActive' => true,
        'created_by' => $this->superAdmin->id,
        'updated_by' => $this->superAdmin->id,
    ]);
    $role = Role::findOrCreate('category-updater', 'web');
    $role->syncPermissions(['admin.access', 'categories.edit']);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    $this->actingAs($admin)
        ->post(route('admin.categories.update', ['id' => $category->id]), [
            'language' => $this->language->code,
            'name' => 'Updated',
            'isActive' => '1',
        ])
        ->assertRedirect(route('admin.categories.index'));

    $category->refresh();

    expect($category->created_by)->toBe($this->superAdmin->id)
        ->and($category->updated_by)->toBe($admin->id);
});

test('magazine and article publish record the publisher', function () {
    $magazine = Magazine::query()->forceCreate([
        'language' => $this->language->code,
        'title' => 'August Edition',
        'isActive' => true,
        'status' => Magazine::STATUS_DRAFT,
    ]);
    $article = Article::query()->forceCreate([
        'language' => $this->language->code,
        'title' => 'Editorial',
        'article' => '<p>Publishable article body.</p>',
        'isActive' => true,
        'status' => Article::STATUS_DRAFT,
    ]);

    $this->mock(MediaStorageService::class, function ($mock): void {
        $mock->shouldReceive('resolve')->once()->andReturn(new MediaStorageLocation);
    });

    $this->actingAs($this->superAdmin)
        ->post(route('admin.magazine.publish', ['id' => $magazine->id]))
        ->assertSessionHas('status');
    $this->post(route('admin.article.publish', ['id' => $article->id]))
        ->assertSessionHas('status');

    expect($magazine->refresh()->published_by)->toBe($this->superAdmin->id)
        ->and($article->refresh()->published_by)->toBe($this->superAdmin->id);
});

test('deleting an actor nulls ownership without deleting the business record', function () {
    $admin = User::factory()->create();
    $category = Category::query()->forceCreate([
        'name' => 'Retained Category',
        'created_by' => $admin->id,
        'updated_by' => $admin->id,
    ]);

    $admin->delete();

    expect(Category::query()->find($category->id))->not->toBeNull()
        ->and($category->refresh()->created_by)->toBeNull()
        ->and($category->updated_by)->toBeNull();
});

test('nullable legacy ownership is safe and only super admin sees the listing column', function () {
    Category::query()->forceCreate([
        'language' => $this->language->code,
        'name' => 'Legacy Category',
        'isActive' => true,
    ]);

    $this->actingAs($this->superAdmin)
        ->get(route('admin.categories.index'))
        ->assertSuccessful()
        ->assertSee('Created By')
        ->assertSee('System / Legacy');

    $role = Role::findOrCreate('category-viewer', 'web');
    $role->syncPermissions(['admin.access', 'categories.view']);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    $this->actingAs($admin)
        ->get(route('admin.categories.index'))
        ->assertSuccessful()
        ->assertDontSee('Created By');
});
