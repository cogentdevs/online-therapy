<?php

use App\Models\Language;
use App\Models\MetaTag;
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

test('Meta Tag create and edit pages do not render visible Language controls', function (): void {
    $metaTag = MetaTag::query()->forceCreate([
        'language' => 'en',
        'title' => 'Home',
        'slug_url' => 'home',
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.meta-tags.create'))
        ->assertSuccessful()
        ->assertDontSee('name="language"', false)
        ->assertDontSee('Select Language')
        ->assertSee('Title / Page');

    $this->get(route('admin.meta-tags.edit', ['id' => $metaTag->id]))
        ->assertSuccessful()
        ->assertDontSee('value="English" readonly', false)
        ->assertSee('Title / Page');
});

test('Meta Tags save with fixed English without submitted language', function (): void {
    $this->actingAs($this->admin)
        ->post(route('admin.meta-tags.store'), [
            'title' => 'Home',
            'keywords' => 'home, seo',
            'description' => 'Homepage SEO.',
        ])
        ->assertRedirect(route('admin.meta-tags.index'));

    $metaTag = MetaTag::query()->where('title', 'Home')->firstOrFail();

    expect($metaTag->language)->toBe('en');

    $this->post(route('admin.meta-tags.update', ['id' => $metaTag->id]), [
        'keywords' => 'updated, seo',
        'description' => 'Updated homepage SEO.',
    ])->assertRedirect(route('admin.meta-tags.index'));

    expect($metaTag->refresh()->language)->toBe('en')
        ->and($metaTag->keywords)->toBe('updated, seo');
});
