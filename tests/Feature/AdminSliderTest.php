<?php

use App\Models\Language;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->originalSliderPublicPath = public_path();
    $this->temporarySliderPublicPath = storage_path('framework/testing/slider-public-'.Str::uuid());
    File::ensureDirectoryExists($this->temporarySliderPublicPath);
    app()->usePublicPath($this->temporarySliderPublicPath);
});

afterEach(function () {
    app()->usePublicPath($this->originalSliderPublicPath);
    File::deleteDirectory($this->temporarySliderPublicPath);
});

function createSliderSuperAdmin(): User
{
    $user = User::factory()->create();
    $user->assignRole(Role::findOrCreate('super-admin'));

    return $user;
}

function createActiveSliderLanguage(): Language
{
    return Language::query()->create([
        'name' => 'English',
        'code' => 'en',
        'is_active' => true,
    ]);
}

test('slider routes require authentication', function () {
    $this->get(route('admin.slider.index'))->assertRedirect(route('admin.login'));
    $this->get(route('admin.slider.create'))->assertRedirect(route('admin.login'));
});

test('slider forms only offer active languages and use reusable admin plugins', function () {
    $superAdmin = createSliderSuperAdmin();
    createActiveSliderLanguage();
    Slider::query()->create([
        'language' => 'en',
        'content_position' => 'center',
        'main_heading' => 'Listed Slider',
        'isActive' => true,
    ]);
    Language::query()->create([
        'name' => 'Inactive Language',
        'code' => 'xx',
        'is_active' => false,
    ]);

    $this->actingAs($superAdmin)
        ->get(route('admin.slider.create'))
        ->assertSuccessful()
        ->assertSee('English')
        ->assertDontSee('Inactive Language')
        ->assertSee('select2', false)
        ->assertSee('Content Position')
        ->assertSee('1020 × 590 px');

    $this->actingAs($superAdmin)
        ->get(route('admin.slider.index'))
        ->assertSuccessful()
        ->assertSee('data-admin-datatable', false)
        ->assertSee('data-delete-confirm', false)
        ->assertSee('fa-trash', false)
        ->assertSee('Center')
        ->assertSee('Add Slider');

    $this->actingAs($superAdmin)
        ->get(route('admin.slider.edit', ['id' => Slider::query()->sole()->id]))
        ->assertSuccessful()
        ->assertSee('value="center" selected', false)
        ->assertSee('1020 × 590 px');
});

test('a super admin can create an active slider with a direct public image', function () {
    $superAdmin = createSliderSuperAdmin();
    createActiveSliderLanguage();

    $this->actingAs($superAdmin)
        ->post(route('admin.slider.store'), [
            'language' => 'en',
            'content_position' => 'top',
            'top_heading' => 'Featured',
            'main_heading' => 'Digital stories that matter',
            'bottom_text' => 'Read our latest editorial collection.',
            'button_label' => 'Explore Now',
            'button_url' => 'https://example.com/magazines',
            'image' => UploadedFile::fake()->image('slider.webp', 1600, 700),
        ])
        ->assertRedirect(route('admin.slider.index'))
        ->assertSessionHas('status', 'Slider added successfully.');

    $slider = Slider::query()->sole();

    expect($slider->language)->toBe('en')
        ->and($slider->content_position)->toBe('top')
        ->and($slider->isActive)->toBeTrue()
        ->and($slider->image)->toStartWith('images/backend-images/slider/slider')
        ->and(File::exists(public_path($slider->image)))->toBeTrue();
});

test('slider creation rejects inactive language codes and invalid images', function () {
    $superAdmin = createSliderSuperAdmin();
    Language::query()->create([
        'name' => 'Inactive Language',
        'code' => 'xx',
        'is_active' => false,
    ]);

    $this->actingAs($superAdmin)
        ->from(route('admin.slider.create'))
        ->post(route('admin.slider.store'), [
            'language' => 'xx',
            'image' => UploadedFile::fake()->create('slider.pdf', 10, 'application/pdf'),
        ])
        ->assertRedirect(route('admin.slider.create'))
        ->assertSessionHasErrors(['language', 'image']);

    expect(Slider::query()->count())->toBe(0);
});

test('slider content position is nullable and rejects unsupported values', function () {
    $superAdmin = createSliderSuperAdmin();
    createActiveSliderLanguage();

    $this->actingAs($superAdmin)
        ->post(route('admin.slider.store'), [
            'language' => 'en',
            'content_position' => null,
            'image' => UploadedFile::fake()->image('nullable-position.webp', 1020, 590),
        ])
        ->assertRedirect(route('admin.slider.index'));

    expect(Slider::query()->sole()->content_position)->toBeNull();

    $this->actingAs($superAdmin)
        ->from(route('admin.slider.create'))
        ->post(route('admin.slider.store'), [
            'language' => 'en',
            'content_position' => 'left',
            'image' => UploadedFile::fake()->image('invalid-position.webp', 1020, 590),
        ])
        ->assertRedirect(route('admin.slider.create'))
        ->assertSessionHasErrors('content_position');
});

test('slider update retains the current image or safely replaces it', function () {
    $superAdmin = createSliderSuperAdmin();
    createActiveSliderLanguage();
    $oldRelativePath = 'images/backend-images/slider/old-slider.png';
    File::ensureDirectoryExists(public_path('images/backend-images/slider'));
    File::put(public_path($oldRelativePath), 'old slider');
    $slider = Slider::query()->create([
        'language' => 'en',
        'content_position' => 'top',
        'main_heading' => 'Original heading',
        'image' => $oldRelativePath,
        'isActive' => true,
    ]);

    $this->actingAs($superAdmin)
        ->post(route('admin.slider.update', $slider), [
            'language' => 'en',
            'content_position' => 'center',
            'main_heading' => 'Updated heading',
            'isActive' => '0',
        ])
        ->assertRedirect(route('admin.slider.index'));

    $slider->refresh();

    expect($slider->image)->toBe($oldRelativePath)
        ->and($slider->content_position)->toBe('center')
        ->and($slider->isActive)->toBeFalse()
        ->and(File::exists(public_path($oldRelativePath)))->toBeTrue();

    $this->actingAs($superAdmin)
        ->post(route('admin.slider.update', $slider), [
            'language' => 'en',
            'content_position' => null,
            'isActive' => '1',
            'image' => UploadedFile::fake()->image('replacement.jpg', 1600, 700),
        ])
        ->assertRedirect(route('admin.slider.index'));

    $slider->refresh();

    expect($slider->image)->not->toBe($oldRelativePath)
        ->and($slider->content_position)->toBeNull()
        ->and(File::exists(public_path($oldRelativePath)))->toBeFalse()
        ->and(File::exists(public_path($slider->image)))->toBeTrue();
});

test('deleting a slider removes its managed image and database record', function () {
    $superAdmin = createSliderSuperAdmin();
    $relativePath = 'images/backend-images/slider/delete-slider.png';
    File::ensureDirectoryExists(public_path('images/backend-images/slider'));
    File::put(public_path($relativePath), 'delete slider');
    $slider = Slider::query()->create([
        'language' => 'en',
        'image' => $relativePath,
        'isActive' => true,
    ]);

    $this->actingAs($superAdmin)
        ->delete(route('admin.slider.destroy', $slider))
        ->assertRedirect(route('admin.slider.index'))
        ->assertSessionHas('status', 'Slider deleted successfully.');

    $this->assertModelMissing($slider);
    expect(File::exists(public_path($relativePath)))->toBeFalse();
});

test('deleting a slider never removes an image outside the managed slider directory', function () {
    $superAdmin = createSliderSuperAdmin();
    $outsideRelativePath = 'images/backend-images/outside-slider.png';
    File::ensureDirectoryExists(public_path('images/backend-images'));
    File::put(public_path($outsideRelativePath), 'outside slider');
    $slider = Slider::query()->create([
        'language' => 'en',
        'image' => $outsideRelativePath,
        'isActive' => true,
    ]);

    $this->actingAs($superAdmin)
        ->delete(route('admin.slider.destroy', $slider))
        ->assertRedirect(route('admin.slider.index'));

    $this->assertModelMissing($slider);
    expect(File::exists(public_path($outsideRelativePath)))->toBeTrue();
});
