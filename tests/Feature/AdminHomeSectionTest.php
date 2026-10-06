<?php

use App\Models\ActivityLog;
use App\Models\HomeSection;
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
    $this->originalHomeSectionPublicPath = public_path();
    $this->temporaryHomeSectionPublicPath = storage_path('framework/testing/home-section-public-'.Str::uuid());
    File::ensureDirectoryExists($this->temporaryHomeSectionPublicPath);
    app()->usePublicPath($this->temporaryHomeSectionPublicPath);
    Language::query()->create(['name' => 'English', 'code' => 'en', 'is_active' => true]);
    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

afterEach(function () {
    app()->usePublicPath($this->originalHomeSectionPublicPath);
    File::deleteDirectory($this->temporaryHomeSectionPublicPath);
});

function homeSectionAdmin(array $permissions): User
{
    $role = Role::findOrCreate('home-section-admin-'.Str::random(8), 'web');
    $role->syncPermissions(['admin.access', ...$permissions]);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    return $admin;
}

function homeSectionPayload(array $overrides = []): array
{
    return array_merge([
        'language' => 'en',
        'position' => 'below slider',
        'section_condition' => 1,
        'title' => 'Featured Home Section',
        'description' => '<p>Editorial introduction.</p>',
        'is_active' => '1',
    ], $overrides);
}

function validHomeSectionPayloadForCondition(int $condition): array
{
    return match ($condition) {
        1 => homeSectionPayload(),
        2 => homeSectionPayload(['section_condition' => 2, 'description' => null, 'image' => UploadedFile::fake()->image('one.jpg')]),
        3, 4 => homeSectionPayload(['section_condition' => $condition, 'image' => UploadedFile::fake()->image('one.jpg')]),
        5 => homeSectionPayload(['section_condition' => 5, 'title_2' => 'Second title', 'description_2' => '<p>Second description.</p>']),
        6 => homeSectionPayload(['section_condition' => 6, 'description' => null, 'image' => UploadedFile::fake()->image('one.jpg'), 'image_2' => UploadedFile::fake()->image('two.jpg')]),
    };
}

test('Home Sections has independent synchronized Website permissions', function () {
    expect(config('admin_modules.modules.home-sections.group'))->toBe('Website')
        ->and(config('admin_modules.modules.home-sections.actions'))->toBe(['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'])
        ->and(config('admin_modules.modules.abouts'))->not->toBe(config('admin_modules.modules.home-sections'));

    foreach (['home-sections.view', 'home-sections.create', 'home-sections.edit', 'home-sections.delete'] as $permission) {
        $this->assertDatabaseHas('permissions', ['name' => $permission, 'guard_name' => 'web']);
    }
});

test('Super Admin and authorized Admin can access Home Sections while unauthorized Admin is denied', function () {
    $this->actingAs($this->superAdmin)->get(route('admin.home-sections.index'))->assertSuccessful()->assertSee('Home Sections');
    $this->actingAs(homeSectionAdmin(['home-sections.view']))->get(route('admin.home-sections.index'))->assertSuccessful();
    $this->actingAs(homeSectionAdmin(['dashboard.view']))->get(route('admin.home-sections.index'))->assertForbidden();
});

test('Home Sections sidebar visibility follows effective view permission', function () {
    $this->actingAs(homeSectionAdmin(['dashboard.view', 'home-sections.view']))->get(route('admin.dashboard'))->assertSuccessful()->assertSee(route('admin.home-sections.index'), false);
    $this->actingAs(homeSectionAdmin(['dashboard.view']))->get(route('admin.dashboard'))->assertSuccessful()->assertDontSee(route('admin.home-sections.index'), false);
});

test('Home Section creation records audit ownership and one independent Activity Log', function () {
    $this->actingAs($this->superAdmin)->post(route('admin.home-sections.store'), homeSectionPayload())->assertRedirect(route('admin.home-sections.index'));
    $homeSection = HomeSection::query()->sole();

    expect($homeSection->created_by)->toBe($this->superAdmin->id)
        ->and($homeSection->updated_by)->toBe($this->superAdmin->id)
        ->and($homeSection->is_active)->toBeTrue()
        ->and(ActivityLog::query()->where('module', 'home_sections')->where('subject_id', $homeSection->id)->where('action', 'created')->count())->toBe(1)
        ->and(ActivityLog::query()->where('module', 'abouts')->where('subject_id', $homeSection->id)->count())->toBe(0);
});

test('Home Section rejects missing language or position and unsupported values', function () {
    $this->actingAs($this->superAdmin)->post(route('admin.home-sections.store'), homeSectionPayload(['language' => null, 'position' => null]))->assertSessionHasErrors(['language', 'position']);
    $this->actingAs($this->superAdmin)->post(route('admin.home-sections.store'), homeSectionPayload(['position' => 'middle']))->assertSessionHasErrors('position');
    $this->actingAs($this->superAdmin)->post(route('admin.home-sections.store'), homeSectionPayload(['section_condition' => 7]))->assertSessionHasErrors('section_condition');
    expect(HomeSection::query()->count())->toBe(0);
});

test('Home Section accepts both controlled positions', function (string $position) {
    $this->actingAs($this->superAdmin)->post(route('admin.home-sections.store'), homeSectionPayload(['position' => $position]))->assertRedirect(route('admin.home-sections.index'));
    expect(HomeSection::query()->sole()->position)->toBe($position);
})->with(['below slider', 'above footer']);

test('all six Home Section conditions accept their valid fields', function (int $condition) {
    $this->actingAs($this->superAdmin)->post(route('admin.home-sections.store'), validHomeSectionPayloadForCondition($condition))->assertRedirect(route('admin.home-sections.index'));
    expect(HomeSection::query()->sole()->section_condition)->toBe($condition);
})->with([1, 2, 3, 4, 5, 6]);

test('Home Section condition validation requires applicable text and images', function () {
    $this->actingAs($this->superAdmin)->post(route('admin.home-sections.store'), homeSectionPayload(['description' => null]))->assertSessionHasErrors('description');
    $this->actingAs($this->superAdmin)->post(route('admin.home-sections.store'), homeSectionPayload(['section_condition' => 3]))->assertSessionHasErrors('image');
    $this->actingAs($this->superAdmin)->post(route('admin.home-sections.store'), homeSectionPayload(['section_condition' => 5, 'description_2' => null]))->assertSessionHasErrors('description_2');
    $this->actingAs($this->superAdmin)->post(route('admin.home-sections.store'), homeSectionPayload(['section_condition' => 6, 'description' => null]))->assertSessionHasErrors(['image', 'image_2']);
    expect(HomeSection::query()->count())->toBe(0);
});

test('Home Section Edit retains position condition and existing images', function () {
    $first = 'images/backend-images/home-sections/first.png';
    $second = 'images/backend-images/home-sections/second.png';
    File::ensureDirectoryExists(public_path('images/backend-images/home-sections'));
    File::put(public_path($first), 'one');
    File::put(public_path($second), 'two');
    $homeSection = HomeSection::query()->create(['language' => 'en', 'position' => 'above footer', 'section_condition' => 6, 'image' => $first, 'image_2' => $second]);

    $this->actingAs($this->superAdmin)->get(route('admin.home-sections.edit', ['id' => $homeSection->id]))
        ->assertSuccessful()->assertSee('value="above footer" selected', false)->assertSee('value="6" selected', false);

    $this->actingAs($this->superAdmin)->post(route('admin.home-sections.update', ['id' => $homeSection->id]), ['language' => 'en', 'position' => 'above footer', 'section_condition' => 6, 'is_active' => '1'])->assertRedirect(route('admin.home-sections.index'));
    expect($homeSection->refresh()->image)->toBe($first)->and($homeSection->image_2)->toBe($second);
});

test('Home Section replacement updates both images safely and records updater', function () {
    $first = 'images/backend-images/home-sections/first.png';
    $second = 'images/backend-images/home-sections/second.png';
    File::ensureDirectoryExists(public_path('images/backend-images/home-sections'));
    File::put(public_path($first), 'one');
    File::put(public_path($second), 'two');
    $homeSection = HomeSection::query()->create(['language' => 'en', 'position' => 'below slider', 'section_condition' => 6, 'image' => $first, 'image_2' => $second]);
    $editor = homeSectionAdmin(['home-sections.edit']);

    $this->actingAs($editor)->post(route('admin.home-sections.update', ['id' => $homeSection->id]), [
        'language' => 'en', 'position' => 'above footer', 'section_condition' => 6, 'is_active' => '0',
        'image' => UploadedFile::fake()->image('replacement-one.jpg'),
        'image_2' => UploadedFile::fake()->image('replacement-two.jpg'),
    ])->assertRedirect(route('admin.home-sections.index'));

    $homeSection->refresh();
    expect($homeSection->image)->toStartWith('images/backend-images/home-sections/')
        ->and($homeSection->image_2)->toStartWith('images/backend-images/home-sections/')
        ->and($homeSection->image)->not->toBe($homeSection->image_2)
        ->and($homeSection->updated_by)->toBe($editor->id)
        ->and(File::exists(public_path($first)))->toBeFalse()
        ->and(File::exists(public_path($second)))->toBeFalse()
        ->and(ActivityLog::query()->where('module', 'home_sections')->where('subject_id', $homeSection->id)->where('action', 'status_changed')->count())->toBe(1);
});

test('deleting Home Section removes its record and only its managed images', function () {
    $first = 'images/backend-images/home-sections/delete-one.png';
    $second = 'images/backend-images/home-sections/delete-two.png';
    $unrelated = 'images/backend-images/about/keep.png';
    File::ensureDirectoryExists(public_path('images/backend-images/home-sections'));
    File::ensureDirectoryExists(public_path('images/backend-images/about'));
    File::put(public_path($first), 'one');
    File::put(public_path($second), 'two');
    File::put(public_path($unrelated), 'keep');
    $homeSection = HomeSection::query()->create(['language' => 'en', 'position' => 'below slider', 'section_condition' => 6, 'image' => $first, 'image_2' => $second]);

    $this->actingAs($this->superAdmin)->delete(route('admin.home-sections.destroy', ['id' => $homeSection->id]))->assertRedirect(route('admin.home-sections.index'));

    $this->assertModelMissing($homeSection);
    expect(File::exists(public_path($first)))->toBeFalse()
        ->and(File::exists(public_path($second)))->toBeFalse()
        ->and(File::exists(public_path($unrelated)))->toBeTrue()
        ->and(ActivityLog::query()->where('module', 'home_sections')->where('action', 'deleted')->count())->toBe(1);
});

test('Home Section detail displays only condition-relevant rich content', function () {
    $homeSection = HomeSection::query()->create([
        'language' => 'en', 'position' => 'below slider', 'section_condition' => 5,
        'title' => 'First', 'description' => '<p><strong>First description</strong></p>',
        'title_2' => 'Second', 'description_2' => '<ul><li>Second description</li></ul>',
    ]);

    $this->actingAs($this->superAdmin)->get(route('admin.home-sections.show', ['id' => $homeSection->id]))
        ->assertSuccessful()
        ->assertSee('<p><strong>First description</strong></p>', false)
        ->assertSee('<ul><li>Second description</li></ul>', false);
});
