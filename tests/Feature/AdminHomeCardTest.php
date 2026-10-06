<?php

use App\Models\ActivityLog;
use App\Models\HomeCard;
use App\Models\HomeCardTitlePosition;
use App\Models\Language;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();
    $this->originalHomeCardPublicPath = public_path();
    $this->temporaryHomeCardPublicPath = storage_path('framework/testing/home-card-public-'.Str::uuid());
    File::ensureDirectoryExists($this->temporaryHomeCardPublicPath);
    app()->usePublicPath($this->temporaryHomeCardPublicPath);
    Language::query()->create(['name' => 'English', 'code' => 'en', 'is_active' => true]);
    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

afterEach(function () {
    app()->usePublicPath($this->originalHomeCardPublicPath);
    File::deleteDirectory($this->temporaryHomeCardPublicPath);
});

function homeCardAdmin(array $permissions): User
{
    $role = Role::findOrCreate('home-card-admin-'.Str::random(8), 'web');
    $role->syncPermissions(['admin.access', ...$permissions]);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    return $admin;
}

function homeCardPayload(array $overrides = []): array
{
    return array_merge(['language' => 'en', 'position' => 'below slider', 'title' => 'Featured Reading', 'description' => 'Explore the latest issue.', 'is_active' => '1'], $overrides);
}

test('Home Cards permissions are synchronized from the Website registry', function () {
    expect(config('admin_modules.modules.home-cards.group'))->toBe('Website')
        ->and(config('admin_modules.modules.home-cards.actions'))->toBe(['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete']);

    foreach (['home-cards.view', 'home-cards.create', 'home-cards.edit', 'home-cards.delete'] as $permission) {
        $this->assertDatabaseHas('permissions', ['name' => $permission, 'guard_name' => 'web']);
    }
});

test('Super Admin and authorized Admin can access Home Cards while unauthorized Admin is denied', function () {
    $this->actingAs($this->superAdmin)->get(route('admin.home-cards.index'))->assertSuccessful()->assertSee('Home Cards');
    $this->actingAs(homeCardAdmin(['home-cards.view']))->get(route('admin.home-cards.index'))->assertSuccessful();
    $this->actingAs(homeCardAdmin(['dashboard.view']))->get(route('admin.home-cards.index'))->assertForbidden();
});

test('Home Card title position settings default to right and respect view and edit permissions', function () {
    $this->actingAs(homeCardAdmin(['home-cards.view']))
        ->get(route('admin.home-cards.card-title-position'))
        ->assertSuccessful()
        ->assertSee('name="below_slider_title_position"', false)
        ->assertSee('name="above_footer_title_position"', false)
        ->assertSee('value="right" selected', false);

    $this->actingAs(homeCardAdmin(['home-cards.view']))
        ->put(route('admin.home-cards.card-title-position.update'), [
            'below_slider_title_position' => 'center',
            'above_footer_title_position' => 'right',
        ])
        ->assertForbidden();
});

test('Home Card title positions are created and updated without duplicate Urdu rows', function () {
    $response = $this->actingAs($this->superAdmin)
        ->put(route('admin.home-cards.card-title-position.update'), [
            'below_slider_title_position' => 'center',
            'above_footer_title_position' => 'right',
        ]);

    $response->assertRedirect(route('admin.home-cards.card-title-position'));
    expect(HomeCardTitlePosition::query()->where('language', 'ur')->count())->toBe(2)
        ->and(HomeCardTitlePosition::query()->where('card_position', 'below slider')->value('title_position'))->toBe('center')
        ->and(HomeCardTitlePosition::query()->where('card_position', 'above footer')->value('title_position'))->toBe('right');

    $this->actingAs($this->superAdmin)
        ->put(route('admin.home-cards.card-title-position.update'), [
            'below_slider_title_position' => 'left',
            'above_footer_title_position' => 'center',
        ])
        ->assertRedirect(route('admin.home-cards.card-title-position'));

    expect(HomeCardTitlePosition::query()->where('language', 'ur')->count())->toBe(2)
        ->and(HomeCardTitlePosition::query()->where('card_position', 'below slider')->value('title_position'))->toBe('left')
        ->and(HomeCardTitlePosition::query()->where('card_position', 'above footer')->value('title_position'))->toBe('center');
});

test('Home Card title position settings reject missing and invalid values', function () {
    $this->actingAs($this->superAdmin)
        ->put(route('admin.home-cards.card-title-position.update'), [
            'below_slider_title_position' => 'invalid',
        ])
        ->assertSessionHasErrors(['below_slider_title_position', 'above_footer_title_position']);

    expect(HomeCardTitlePosition::query()->count())->toBe(0);
});

test('Home Cards sidebar visibility follows effective view permission', function () {
    $this->actingAs(homeCardAdmin(['dashboard.view', 'home-cards.view']))->get(route('admin.dashboard'))->assertSuccessful()->assertSee(route('admin.home-cards.index'), false);
    $this->actingAs(homeCardAdmin(['dashboard.view']))->get(route('admin.dashboard'))->assertSuccessful()->assertDontSee(route('admin.home-cards.index'), false);
});

test('Home Card creation supports optional content and records audit ownership once', function () {
    $this->actingAs($this->superAdmin)->post(route('admin.home-cards.store'), homeCardPayload(['position' => 'above footer', 'title' => null, 'description' => null]))->assertRedirect(route('admin.home-cards.index'));
    $homeCard = HomeCard::query()->sole();

    expect($homeCard->position)->toBe('above footer')->and($homeCard->title)->toBeNull()->and($homeCard->description)->toBeNull()
        ->and($homeCard->created_by)->toBe($this->superAdmin->id)->and($homeCard->updated_by)->toBe($this->superAdmin->id)
        ->and(ActivityLog::query()->where('module', 'home_cards')->where('subject_id', $homeCard->id)->where('action', 'created')->count())->toBe(1);
});

test('Home Card validates language position and accepted position values', function () {
    $this->actingAs($this->superAdmin)->post(route('admin.home-cards.store'), homeCardPayload(['language' => null, 'position' => null]))->assertSessionHasErrors(['language', 'position']);
    $this->actingAs($this->superAdmin)->post(route('admin.home-cards.store'), homeCardPayload(['position' => 'middle']))->assertSessionHasErrors('position');
    expect(HomeCard::query()->count())->toBe(0);
});

test('Home Card image upload uses a safe path and edit retains selected position', function () {
    $this->actingAs($this->superAdmin)->post(route('admin.home-cards.store'), homeCardPayload(['image' => UploadedFile::fake()->image('original.png')]))->assertRedirect(route('admin.home-cards.index'));
    $homeCard = HomeCard::query()->sole();

    expect($homeCard->image)->toStartWith('images/backend-images/home-cards/')->and($homeCard->image)->not->toContain('original.png')->and(File::exists(public_path($homeCard->image)))->toBeTrue();
    $this->actingAs($this->superAdmin)->get(route('admin.home-cards.edit', ['id' => $homeCard->id]))->assertSuccessful()->assertSee('value="below slider" selected', false);
});

test('Home Card update retains then safely replaces image and updates audit actor', function () {
    $oldPath = 'images/backend-images/home-cards/old.png';
    File::ensureDirectoryExists(public_path('images/backend-images/home-cards'));
    File::put(public_path($oldPath), 'old');
    $homeCard = HomeCard::query()->create([...homeCardPayload(), 'image' => $oldPath]);
    $editor = homeCardAdmin(['home-cards.edit']);

    $this->actingAs($editor)->post(route('admin.home-cards.update', ['id' => $homeCard->id]), homeCardPayload())->assertRedirect(route('admin.home-cards.index'));
    expect($homeCard->refresh()->image)->toBe($oldPath)->and(File::exists(public_path($oldPath)))->toBeTrue();

    $this->actingAs($editor)->post(route('admin.home-cards.update', ['id' => $homeCard->id]), homeCardPayload(['position' => 'above footer', 'is_active' => '0', 'image' => UploadedFile::fake()->image('replacement.jpg')]))->assertRedirect(route('admin.home-cards.index'));
    $homeCard->refresh();

    expect($homeCard->image)->not->toBe($oldPath)->and($homeCard->position)->toBe('above footer')->and($homeCard->updated_by)->toBe($editor->id)
        ->and(File::exists(public_path($oldPath)))->toBeFalse()->and(File::exists(public_path($homeCard->image)))->toBeTrue()
        ->and(ActivityLog::query()->where('module', 'home_cards')->where('subject_id', $homeCard->id)->where('action', 'status_changed')->count())->toBe(1);
});

test('deleting Home Card removes its record and managed image', function () {
    $path = 'images/backend-images/home-cards/delete.png';
    File::ensureDirectoryExists(public_path('images/backend-images/home-cards'));
    File::put(public_path($path), 'image');
    $homeCard = HomeCard::query()->create([...homeCardPayload(), 'image' => $path]);

    $this->actingAs($this->superAdmin)->delete(route('admin.home-cards.destroy', ['id' => $homeCard->id]))->assertRedirect(route('admin.home-cards.index'));
    $this->assertModelMissing($homeCard);
    expect(File::exists(public_path($path)))->toBeFalse()->and(ActivityLog::query()->where('module', 'home_cards')->where('action', 'deleted')->count())->toBe(1);
});
