<?php

use App\Models\About;
use App\Models\ActivityLog;
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
    $this->originalAboutPublicPath = public_path();
    $this->temporaryAboutPublicPath = storage_path('framework/testing/about-public-'.Str::uuid());
    File::ensureDirectoryExists($this->temporaryAboutPublicPath);
    app()->usePublicPath($this->temporaryAboutPublicPath);
    $this->language = Language::query()->create(['name' => 'English', 'code' => 'en', 'is_active' => true]);
    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

afterEach(function () {
    app()->usePublicPath($this->originalAboutPublicPath);
    File::deleteDirectory($this->temporaryAboutPublicPath);
});

function aboutAdmin(array $permissions): User
{
    $role = Role::findOrCreate('about-admin-'.Str::random(8), 'web');
    $role->syncPermissions(['admin.access', ...$permissions]);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    return $admin;
}

function aboutPayload(array $overrides = []): array
{
    return array_merge(['language' => 'en', 'section_condition' => 1, 'title' => 'About Digital Magazine', 'description' => 'Independent publishing for informed readers.', 'is_active' => '1'], $overrides);
}

test('About permissions are synchronized from the dynamic Website registry', function () {
    expect(config('admin_modules.modules.abouts.group'))->toBe('Website')
        ->and(config('admin_modules.modules.abouts.actions'))->toBe(['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete']);

    foreach (['abouts.view', 'abouts.create', 'abouts.edit', 'abouts.delete'] as $permission) {
        $this->assertDatabaseHas('permissions', ['name' => $permission, 'guard_name' => 'web']);
    }
});

test('Super Admin and authorized Admin can access About while unauthorized Admin is denied', function () {
    $this->actingAs($this->superAdmin)->get(route('admin.about.index'))->assertSuccessful()->assertSee('About');
    $this->actingAs(aboutAdmin(['abouts.view']))->get(route('admin.about.index'))->assertSuccessful();
    $this->actingAs(aboutAdmin(['dashboard.view']))->get(route('admin.about.index'))->assertForbidden();
});

test('About sidebar visibility follows effective view permission', function () {
    $this->actingAs(aboutAdmin(['dashboard.view', 'abouts.view']))->get(route('admin.dashboard'))->assertSuccessful()->assertSee(route('admin.about.index'), false);
    $this->actingAs(aboutAdmin(['dashboard.view']))->get(route('admin.dashboard'))->assertSuccessful()->assertDontSee(route('admin.about.index'), false);
});

test('Super Admin creates About with audit ownership and one Activity Log', function () {
    $this->actingAs($this->superAdmin)->post(route('admin.about.store'), aboutPayload())->assertRedirect(route('admin.about.index'));
    $about = About::query()->sole();
    expect($about->is_active)->toBeTrue()->and($about->created_by)->toBe($this->superAdmin->id)->and($about->updated_by)->toBe($this->superAdmin->id);
    $logs = ActivityLog::query()->where('module', 'abouts')->where('subject_id', $about->id)->get();
    expect($logs)->toHaveCount(1)->and($logs->first()->action)->toBe('created');
});

test('About detail renders TinyMCE descriptions as rich HTML', function () {
    $about = About::query()->create([
        'language' => 'en',
        'section_condition' => 5,
        'title' => 'First section',
        'description' => '<p><strong>First rich description</strong></p>',
        'title_2' => 'Second section',
        'description_2' => '<ul><li>Second rich description</li></ul>',
        'is_active' => true,
    ]);

    $this->actingAs($this->superAdmin)
        ->get(route('admin.about.show', ['id' => $about->id]))
        ->assertSuccessful()
        ->assertSee('<p><strong>First rich description</strong></p>', false)
        ->assertSee('<ul><li>Second rich description</li></ul>', false);
});

test('About rejects unsupported conditions and condition-required images', function () {
    $this->actingAs($this->superAdmin)->post(route('admin.about.store'), aboutPayload(['section_condition' => 7]))->assertSessionHasErrors('section_condition');
    $this->actingAs($this->superAdmin)->post(route('admin.about.store'), aboutPayload(['section_condition' => 6, 'description' => null]))->assertSessionHasErrors(['image', 'image_2']);
    expect(About::query()->count())->toBe(0);
});

test('image layouts store safe unique About images', function () {
    $this->actingAs($this->superAdmin)->post(route('admin.about.store'), aboutPayload([
        'section_condition' => 6, 'description' => null,
        'image' => UploadedFile::fake()->image('same.png'), 'image_2' => UploadedFile::fake()->image('same.png'),
    ]))->assertRedirect(route('admin.about.index'));
    $about = About::query()->sole();
    expect($about->image)->toStartWith('images/backend-images/about/')->and($about->image_2)->toStartWith('images/backend-images/about/')
        ->and($about->image)->not->toBe($about->image_2)->and(File::exists(public_path($about->image)))->toBeTrue()->and(File::exists(public_path($about->image_2)))->toBeTrue();
});

test('About update retains or replaces images and records updater and status activity', function () {
    $oldPath = 'images/backend-images/about/old.png';
    File::ensureDirectoryExists(public_path('images/backend-images/about'));
    File::put(public_path($oldPath), 'old');
    $about = About::query()->create(['language' => 'en', 'section_condition' => 2, 'image' => $oldPath, 'is_active' => true]);
    $editor = aboutAdmin(['abouts.edit']);

    $this->actingAs($editor)->post(route('admin.about.update', ['id' => $about->id]), ['language' => 'en', 'section_condition' => 2, 'is_active' => '1'])->assertRedirect(route('admin.about.index'));
    expect($about->refresh()->image)->toBe($oldPath)->and(File::exists(public_path($oldPath)))->toBeTrue();

    $this->actingAs($editor)->post(route('admin.about.update', ['id' => $about->id]), [
        'language' => 'en', 'section_condition' => 2, 'is_active' => '0', 'image' => UploadedFile::fake()->image('replacement.jpg'),
    ])->assertRedirect(route('admin.about.index'));
    $about->refresh();
    expect($about->image)->not->toBe($oldPath)->and($about->updated_by)->toBe($editor->id)->and(File::exists(public_path($oldPath)))->toBeFalse()
        ->and(File::exists(public_path($about->image)))->toBeTrue()
        ->and(ActivityLog::query()->where('module', 'abouts')->where('subject_id', $about->id)->where('action', 'status_changed')->count())->toBe(1);
});

test('deleting About removes its record and managed images', function () {
    $first = 'images/backend-images/about/delete-one.png';
    $second = 'images/backend-images/about/delete-two.png';
    File::ensureDirectoryExists(public_path('images/backend-images/about'));
    File::put(public_path($first), 'one');
    File::put(public_path($second), 'two');
    $about = About::query()->create(['language' => 'en', 'section_condition' => 6, 'image' => $first, 'image_2' => $second]);
    $this->actingAs($this->superAdmin)->delete(route('admin.about.destroy', ['id' => $about->id]))->assertRedirect(route('admin.about.index'));
    $this->assertModelMissing($about);
    expect(File::exists(public_path($first)))->toBeFalse()->and(File::exists(public_path($second)))->toBeFalse()
        ->and(ActivityLog::query()->where('module', 'abouts')->where('action', 'deleted')->count())->toBe(1);
});
