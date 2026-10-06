<?php

use App\Models\GeneralSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->originalPublicPath = public_path();
    $this->temporaryPublicPath = storage_path('framework/testing/public-'.Str::uuid());
    File::ensureDirectoryExists($this->temporaryPublicPath);
    app()->usePublicPath($this->temporaryPublicPath);
});

afterEach(function () {
    app()->usePublicPath($this->originalPublicPath);
    File::deleteDirectory($this->temporaryPublicPath);
});

function createGeneralSettingsSuperAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole(Role::findOrCreate('super-admin'));

    return $user;
}

test('general settings routes require authentication', function () {
    $this->get(route('admin.general-setting.edit'))
        ->assertRedirect(route('admin.login'));

    $this->put(route('admin.general-setting.update'))
        ->assertRedirect(route('admin.login'));
});

test('a super admin can open general settings before a record exists', function () {
    $superAdmin = createGeneralSettingsSuperAdmin();

    $this->actingAs($superAdmin)
        ->get(route('admin.general-setting.edit'))
        ->assertSuccessful()
        ->assertSee('General Settings')
        ->assertSee('Update Settings')
        ->assertSeeInOrder(['Reports', 'General Settings', 'Logout']);

    expect(GeneralSetting::query()->count())->toBe(0);
});

test('first update creates one singleton settings record and accepts nullable fields', function () {
    $superAdmin = createGeneralSettingsSuperAdmin();

    $this->actingAs($superAdmin)
        ->put(route('admin.general-setting.update'), [
            'app_name' => 'Digital Magazine',
            'url' => '',
            'email' => '',
            'footer_text' => '',
            'linkedin' => '',
            'app_section_heading' => '',
            'app_section_text' => '',
            'play_store_link' => '',
            'app_store_link' => '',
            'cookie_consent_enabled' => '',
            'maintenance_mode' => '',
        ])
        ->assertRedirect(route('admin.general-setting.edit'))
        ->assertSessionHas('status', 'General settings updated successfully.');

    $settings = GeneralSetting::query()->sole();

    expect($settings->app_name)->toBe('Digital Magazine')
        ->and($settings->url)->toBeNull()
        ->and($settings->email)->toBeNull()
        ->and($settings->footer_text)->toBeNull()
        ->and($settings->linkedin)->toBeNull()
        ->and($settings->app_section_heading)->toBeNull()
        ->and($settings->app_section_text)->toBeNull()
        ->and($settings->play_store_icon)->toBeNull()
        ->and($settings->play_store_link)->toBeNull()
        ->and($settings->app_store_icon)->toBeNull()
        ->and($settings->app_store_link)->toBeNull()
        ->and($settings->cookie_consent_enabled)->toBeNull()
        ->and($settings->maintenance_mode)->toBeNull();
});

test('app info content and store icons are saved and icons are retained without replacements', function () {
    $superAdmin = createGeneralSettingsSuperAdmin();

    $this->actingAs($superAdmin)
        ->put(route('admin.general-setting.update'), [
            'footer_text' => 'Trusted Urdu journalism.',
            'linkedin' => 'https://www.linkedin.com/company/digital-magazine',
            'app_section_heading' => 'Download our app',
            'app_section_text' => 'Read the latest issue anywhere.',
            'play_store_icon' => UploadedFile::fake()->image('play-store.webp'),
            'play_store_link' => 'https://play.google.com/store/apps/details?id=example',
            'app_store_icon' => UploadedFile::fake()->image('app-store.png'),
            'app_store_link' => 'https://apps.apple.com/app/example/id123',
        ])
        ->assertRedirect(route('admin.general-setting.edit'));

    $settings = GeneralSetting::query()->sole();

    expect($settings->footer_text)->toBe('Trusted Urdu journalism.')
        ->and($settings->linkedin)->toBe('https://www.linkedin.com/company/digital-magazine')
        ->and($settings->app_section_heading)->toBe('Download our app')
        ->and($settings->app_section_text)->toBe('Read the latest issue anywhere.')
        ->and($settings->play_store_link)->toBe('https://play.google.com/store/apps/details?id=example')
        ->and($settings->app_store_link)->toBe('https://apps.apple.com/app/example/id123')
        ->and(File::exists(public_path($settings->play_store_icon)))->toBeTrue()
        ->and(File::exists(public_path($settings->app_store_icon)))->toBeTrue();

    $storedIconPaths = [$settings->play_store_icon, $settings->app_store_icon];

    $this->actingAs($superAdmin)
        ->put(route('admin.general-setting.update'), [
            'app_section_heading' => 'Updated app heading',
        ])
        ->assertRedirect(route('admin.general-setting.edit'));

    $settings->refresh();

    expect([$settings->play_store_icon, $settings->app_store_icon])->toBe($storedIconPaths);
    foreach ($storedIconPaths as $storedIconPath) {
        expect(File::exists(public_path($storedIconPath)))->toBeTrue();
    }
});

test('branding images are replaced safely and retained when no replacement is uploaded', function () {
    $superAdmin = createGeneralSettingsSuperAdmin();
    $imageDirectory = public_path('images/backend-images/logo');
    File::ensureDirectoryExists($imageDirectory);
    File::put($imageDirectory.'/old-logo.png', 'old logo');
    File::put($imageDirectory.'/old-footer-logo.png', 'old footer logo');
    File::put($imageDirectory.'/old-favicon.png', 'old favicon');

    $settings = GeneralSetting::query()->create([
        'logo' => 'images/backend-images/logo/old-logo.png',
        'footer_logo' => 'images/backend-images/logo/old-footer-logo.png',
        'favicon' => 'images/backend-images/logo/old-favicon.png',
    ]);

    $this->actingAs($superAdmin)
        ->put(route('admin.general-setting.update'), [
            'app_name' => 'Digital Magazine',
            'logo' => UploadedFile::fake()->image('logo-header.webp'),
            'footer_logo' => UploadedFile::fake()->image('footer-logo-new.png'),
            'favicon' => UploadedFile::fake()->image('favicon-icon.jpg'),
        ])
        ->assertRedirect(route('admin.general-setting.edit'));

    $settings->refresh();

    expect(File::exists($imageDirectory.'/old-logo.png'))->toBeFalse()
        ->and(File::exists($imageDirectory.'/old-footer-logo.png'))->toBeFalse()
        ->and(File::exists($imageDirectory.'/old-favicon.png'))->toBeFalse()
        ->and(File::exists(public_path($settings->logo)))->toBeTrue()
        ->and(File::exists(public_path($settings->footer_logo)))->toBeTrue()
        ->and(File::exists(public_path($settings->favicon)))->toBeTrue()
        ->and($settings->logo)->toStartWith('images/backend-images/logo/logo-')
        ->and($settings->footer_logo)->toStartWith('images/backend-images/logo/footer-logo-')
        ->and($settings->favicon)->toStartWith('images/backend-images/logo/favicon-');

    $storedPaths = [$settings->logo, $settings->footer_logo, $settings->favicon];

    $this->actingAs($superAdmin)
        ->put(route('admin.general-setting.update'), [
            'app_name' => 'Updated Digital Magazine',
        ])
        ->assertRedirect(route('admin.general-setting.edit'));

    $settings->refresh();

    expect([$settings->logo, $settings->footer_logo, $settings->favicon])->toBe($storedPaths);
    foreach ($storedPaths as $storedPath) {
        expect(File::exists(public_path($storedPath)))->toBeTrue();
    }
});

test('general settings preview uses the relative public image path', function () {
    $superAdmin = createGeneralSettingsSuperAdmin();
    $relativePath = 'images/backend-images/logo/logo-existing.png';
    File::ensureDirectoryExists(public_path('images/backend-images/logo'));
    File::put(public_path($relativePath), 'logo');
    GeneralSetting::query()->create(['logo' => $relativePath]);

    $this->actingAs($superAdmin)
        ->get(route('admin.general-setting.edit'))
        ->assertSuccessful()
        ->assertSee(asset($relativePath), false);
});

test('image replacement never deletes a file outside the managed logo directory', function () {
    $superAdmin = createGeneralSettingsSuperAdmin();
    $outsideRelativePath = 'images/backend-images/outside-logo.png';
    File::ensureDirectoryExists(public_path('images/backend-images'));
    File::put(public_path($outsideRelativePath), 'outside logo');
    GeneralSetting::query()->create(['logo' => $outsideRelativePath]);

    $this->actingAs($superAdmin)
        ->put(route('admin.general-setting.update'), [
            'logo' => UploadedFile::fake()->image('replacement.png'),
        ])
        ->assertRedirect(route('admin.general-setting.edit'));

    expect(File::exists(public_path($outsideRelativePath)))->toBeTrue();
});

test('general settings validates optional values when they are provided', function () {
    $superAdmin = createGeneralSettingsSuperAdmin();

    $this->actingAs($superAdmin)
        ->from(route('admin.general-setting.edit'))
        ->put(route('admin.general-setting.update'), [
            'email' => 'not-an-email',
            'facebook' => 'not-a-url',
            'linkedin' => 'not-a-url',
            'play_store_link' => 'not-a-url',
            'app_store_link' => 'not-a-url',
            'max_devices_per_user' => 0,
            'logo' => UploadedFile::fake()->create('logo.pdf', 10, 'application/pdf'),
            'play_store_icon' => UploadedFile::fake()->create('play-store.svg', 10, 'image/svg+xml'),
            'app_store_icon' => UploadedFile::fake()->create('app-store.gif', 10, 'image/gif'),
        ])
        ->assertRedirect(route('admin.general-setting.edit'))
        ->assertSessionHasErrors([
            'email',
            'facebook',
            'linkedin',
            'play_store_link',
            'app_store_link',
            'max_devices_per_user',
            'logo',
            'play_store_icon',
            'app_store_icon',
        ]);

    expect(GeneralSetting::query()->count())->toBe(0);
});
