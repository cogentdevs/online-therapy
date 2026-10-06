<?php

use App\Models\GeneralSetting;
use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

function createLanguageViewSuperAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole(Role::findOrCreate('super-admin'));

    return $user;
}

test('language model casts flags and general settings belongs to its default language', function () {
    $language = Language::query()->create([
        'name' => 'English',
        'code' => 'en',
        'is_default' => 1,
        'is_active' => 1,
    ]);
    $generalSetting = GeneralSetting::query()->create(['default_language_id' => $language->id]);

    expect($language->is_default)->toBeTrue()
        ->and($language->is_active)->toBeTrue()
        ->and($generalSetting->defaultLanguage->is($language))->toBeTrue();
});

test('general settings and active languages are globally available to admin views', function () {
    $this->withoutVite();
    $superAdmin = createLanguageViewSuperAdmin();
    $activeLanguage = Language::query()->create([
        'name' => 'English',
        'code' => 'en',
        'is_active' => true,
    ]);
    Language::query()->create([
        'name' => 'Inactive Language',
        'code' => 'xx',
        'is_active' => false,
    ]);
    GeneralSetting::query()->create([
        'app_name' => 'Digital Magazine Global',
        'favicon' => 'images/backend-images/logo/favicon-global.png',
        'default_language_id' => $activeLanguage->id,
    ]);

    $this->actingAs($superAdmin)
        ->get(route('admin.general-setting.edit'))
        ->assertSuccessful()
        ->assertSee('Digital Magazine Global')
        ->assertSee('English')
        ->assertDontSee('Inactive Language')
        ->assertSee('value="'.$activeLanguage->id.'" selected', false)
        ->assertSee(asset('images/backend-images/logo/favicon-global.png'), false);
});
