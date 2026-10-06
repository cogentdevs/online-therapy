<?php

use App\Models\Ad;
use App\Models\GeneralSetting;
use App\Models\Language;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();
    Language::query()->create(['name' => 'Urdu', 'code' => 'ur', 'is_active' => true]);
    $this->admin = User::factory()->create(['is_active' => true]);
    $this->admin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

test('ads routes enforce their matching permissions', function () {
    $ad = Ad::query()->create(['title' => 'Campaign', 'page_name' => 'header', 'place' => 'header_ad']);
    $user = User::factory()->create(['is_active' => true]);
    $role = Role::findOrCreate('ad-viewer', 'web');
    $role->givePermissionTo(['admin.access', 'ads.view']);
    $user->assignRole($role);

    $this->actingAs($user)->get(route('admin.ads.index'))->assertSuccessful();
    $this->actingAs($user)->get(route('admin.ads.create'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.ads.edit', $ad))->assertForbidden();
    $this->actingAs($user)->delete(route('admin.ads.destroy', $ad))->assertForbidden();
});

test('ads pages use the established Admin CRUD layout and controls', function () {
    $ad = Ad::query()->create(['title' => 'Styled campaign', 'page_name' => 'header', 'place' => 'header_ad']);

    $this->actingAs($this->admin)
        ->get(route('admin.ads.index'))
        ->assertSuccessful()
        ->assertSee('breadcrumb', false)
        ->assertSee('admin-settings-card', false)
        ->assertSee('data-admin-datatable', false)
        ->assertSee('admin-primary-button', false);

    $this->actingAs($this->admin)
        ->get(route('admin.ads.create'))
        ->assertSuccessful()
        ->assertSee('breadcrumb', false)
        ->assertSee('admin-settings-card', false)
        ->assertSee('data-ad-placement-form', false)
        ->assertSee('taza_sidebar_1_normal', false)
        ->assertSee('taza_sidebar_2_tall', false);

    $this->actingAs($this->admin)
        ->get(route('admin.ads.edit', $ad))
        ->assertSuccessful()
        ->assertSee('breadcrumb', false)
        ->assertSee('admin-settings-card', false);

    $this->actingAs($this->admin)
        ->get(route('admin.ads.show', $ad))
        ->assertSuccessful()
        ->assertSee('Back to Advertisements')
        ->assertSee('admin-settings-card', false);
});

test('only title page and matching place are required', function () {
    $this->actingAs($this->admin)->post(route('admin.ads.store'), [
        'title' => 'Draft', 'page_name' => 'home', 'place' => 'home_horizontal_large',
    ])->assertRedirect(route('admin.ads.index'));

    $this->assertDatabaseHas('ads', ['title' => 'Draft', 'language' => null, 'ad_image' => null, 'google_ad_code' => null]);
});

test('place must belong to selected page and expiry cannot precede start', function () {
    $this->actingAs($this->admin)->post(route('admin.ads.store'), [
        'title' => 'Invalid', 'page_name' => 'home', 'place' => 'header_ad',
        'start_date' => '2026-09-10', 'expiry_date' => '2026-09-09',
    ])->assertSessionHasErrors(['place', 'expiry_date']);
});

test('image and google ads save independently', function () {
    File::deleteDirectory(public_path('images/backend-images/ads'));
    $this->actingAs($this->admin)->post(route('admin.ads.store'), [
        'title' => 'Image', 'page_name' => 'header', 'place' => 'header_ad',
        'ad_image' => UploadedFile::fake()->image('campaign.jpg'),
    ])->assertSessionHasNoErrors();
    $this->actingAs($this->admin)->post(route('admin.ads.store'), [
        'title' => 'Google', 'page_name' => 'home', 'place' => 'home_horizontal_small',
        'google_ad_code' => '<ins data-ad-client="test"></ins>',
    ])->assertSessionHasNoErrors();
    expect(Ad::query()->whereNotNull('ad_image')->exists())->toBeTrue()
        ->and(Ad::query()->whereNotNull('google_ad_code')->exists())->toBeTrue();
    File::deleteDirectory(public_path('images/backend-images/ads'));
});

test('editing a missing ad image with the same filename keeps the replacement file', function () {
    $relativePath = 'images/backend-images/ads/recovered-campaign.jpg';
    File::delete(public_path($relativePath));
    $ad = Ad::query()->create([
        'title' => 'Missing image campaign',
        'page_name' => 'header',
        'place' => 'header_ad',
        'ad_image' => $relativePath,
    ]);

    $this->actingAs($this->admin)->put(route('admin.ads.update', $ad), [
        'title' => $ad->title,
        'page_name' => $ad->page_name,
        'place' => $ad->place,
        'ad_image' => UploadedFile::fake()->image('recovered-campaign.jpg'),
    ])->assertRedirect(route('admin.ads.index'));

    expect($ad->fresh()->ad_image)->toBe($relativePath)
        ->and(File::isFile(public_path($relativePath)))->toBeTrue();

    File::delete(public_path($relativePath));
});

test('expiry command deactivates only active ads before today without deleting them', function () {
    $expired = Ad::query()->create(['title' => 'Expired', 'page_name' => 'header', 'place' => 'header_ad', 'expiry_date' => today()->subDay(), 'isActive' => true]);
    $today = Ad::query()->create(['title' => 'Today', 'page_name' => 'header', 'place' => 'header_ad', 'expiry_date' => today(), 'isActive' => true]);
    $inactive = Ad::query()->create(['title' => 'Inactive', 'page_name' => 'header', 'place' => 'header_ad', 'expiry_date' => today()->subDay(), 'isActive' => false]);

    $this->artisan('ads:expire')->assertSuccessful();

    expect($expired->fresh()->isActive)->toBeFalse()->and($today->fresh()->isActive)->toBeTrue()->and($inactive->fresh()->isActive)->toBeFalse()->and(Ad::query()->count())->toBe(3);
});

test('general settings accepts nullable or valid Google Ads client ID and frontend head loads it once', function () {
    $this->actingAs($this->admin)->put(route('admin.general-setting.update'), ['google_ads_client_id' => null])->assertSessionHasNoErrors();
    $client = 'ca-pub-1234567890123456';
    $this->actingAs($this->admin)->put(route('admin.general-setting.update'), ['google_ads_client_id' => $client])->assertSessionHasNoErrors();
    expect(GeneralSetting::query()->first()->google_ads_client_id)->toBe($client);
    $this->get(route('frontend.home'))->assertSee($client)->assertSee('pagead2.googlesyndication.com');
});
