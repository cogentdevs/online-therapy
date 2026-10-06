<?php

use App\Http\Requests\Admin\StoreBannerRequest;
use App\Models\Banner;
use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

test('banner add and edit forms show the side by side image dimensions', function () {
    $this->withoutVite();

    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole(Role::findOrCreate('super-admin', 'web'));
    Language::query()->create(['name' => 'Urdu', 'code' => 'ur', 'is_active' => true]);
    $banner = Banner::query()->create([
        'language' => 'ur',
        'type' => 'side-by-side',
        'position' => 'category-detail-top-full',
        'image' => 'images/backend-images/banner/one.webp',
        'image_2' => 'images/backend-images/banner/two.webp',
        'isActive' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.banner.create'))
        ->assertSuccessful()
        ->assertSee('500 × 350 px')
        ->assertSee('1150 × 220 px')
        ->assertSee('value="category-detail-top-full"', false);

    $this->actingAs($admin)
        ->get(route('admin.banner.edit', ['id' => $banner->id]))
        ->assertSuccessful()
        ->assertSee('500 × 350 px')
        ->assertSee('Recommended size: 1150 × 220 px')
        ->assertSee('value="category-detail-top-full" selected', false);
});

test('category detail top full is an accepted banner position', function () {
    Language::query()->create(['name' => 'Urdu', 'code' => 'ur', 'is_active' => true]);
    $request = new StoreBannerRequest;
    $request->merge(['type' => 'full', 'position' => 'category-detail-top-full']);

    $validator = Validator::make([
        'language' => 'ur',
        'type' => 'full',
        'position' => 'category-detail-top-full',
        'image' => UploadedFile::fake()->image('category-banner.jpg', 500, 350),
    ], $request->rules());

    expect($validator->passes())->toBeTrue();
});

test('banner dimensions are guidance only for category detail and side by side images', function () {
    Language::query()->create(['name' => 'Urdu', 'code' => 'ur', 'is_active' => true]);
    $categoryRequest = new StoreBannerRequest;
    $categoryRequest->merge(['type' => 'full', 'position' => 'category-detail-top-full']);
    $sideBySideRequest = new StoreBannerRequest;
    $sideBySideRequest->merge(['type' => 'side-by-side', 'position' => 'center']);

    $categoryBanner = Validator::make([
        'language' => 'ur',
        'type' => 'full',
        'position' => 'category-detail-top-full',
        'image' => UploadedFile::fake()->image('wrong-category-banner.jpg', 500, 350),
    ], $categoryRequest->rules());

    $sideBySideBanner = Validator::make([
        'language' => 'ur',
        'type' => 'side-by-side',
        'position' => 'center',
        'image' => UploadedFile::fake()->image('side-one.jpg', 500, 350),
        'image_2' => UploadedFile::fake()->image('side-two.jpg', 500, 350),
    ], $sideBySideRequest->rules());

    expect($categoryBanner->passes())->toBeTrue()
        ->and($sideBySideBanner->passes())->toBeTrue();
});
