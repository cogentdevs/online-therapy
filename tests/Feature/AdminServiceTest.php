<?php

use App\Models\ActivityLog;
use App\Models\Service;
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
    $this->originalServicePublicPath = public_path();
    $this->temporaryServicePublicPath = storage_path('framework/testing/service-public-'.Str::uuid());
    File::ensureDirectoryExists($this->temporaryServicePublicPath);
    app()->usePublicPath($this->temporaryServicePublicPath);
    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

afterEach(function () {
    app()->usePublicPath($this->originalServicePublicPath);
    File::deleteDirectory($this->temporaryServicePublicPath);
});

function serviceAdmin(array $permissions): User
{
    $role = Role::findOrCreate('service-admin-'.Str::random(8), 'web');
    $role->syncPermissions(['admin.access', ...$permissions]);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    return $admin;
}

function servicePayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Editorial Consulting',
        'image' => UploadedFile::fake()->image('service.png'),
        'description' => 'Professional editorial consulting.',
        'isactive' => '1',
    ], $overrides);
}

test('Services permissions are synchronized from the Website registry', function () {
    expect(config('admin_modules.modules.services.group'))->toBe('Website')
        ->and(config('admin_modules.modules.services.actions'))->toBe([
            'view' => 'View',
            'create' => 'Create',
            'edit' => 'Edit',
            'delete' => 'Delete',
        ]);

    foreach (['services.view', 'services.create', 'services.edit', 'services.delete'] as $permission) {
        $this->assertDatabaseHas('permissions', ['name' => $permission, 'guard_name' => 'web']);
    }
});

test('Services routes and sidebar enforce effective permissions', function () {
    $viewer = serviceAdmin(['dashboard.view', 'services.view']);
    $unauthorizedAdmin = serviceAdmin(['dashboard.view']);

    $this->actingAs($viewer)->get(route('admin.services.index'))->assertSuccessful();
    $this->actingAs($unauthorizedAdmin)->get(route('admin.services.index'))->assertForbidden();
    $this->actingAs($viewer)->get(route('admin.dashboard'))->assertSuccessful()->assertSee(route('admin.services.index'), false);
    $this->actingAs($unauthorizedAdmin)->get(route('admin.dashboard'))->assertSuccessful()->assertDontSee(route('admin.services.index'), false);
});

test('Service creation validates input stores a safe image and records ownership and activity', function () {
    $this->actingAs($this->superAdmin)
        ->post(route('admin.services.store'), servicePayload())
        ->assertRedirect(route('admin.services.index'));

    $service = Service::query()->sole();

    expect($service->name)->toBe('Editorial Consulting')
        ->and($service->image)->toStartWith('backend-images/services/')
        ->and($service->image)->not->toContain('service.png')
        ->and($service->isactive)->toBeTrue()
        ->and($service->created_by)->toBe($this->superAdmin->id)
        ->and($service->updated_by)->toBe($this->superAdmin->id)
        ->and(File::exists(public_path($service->image)))->toBeTrue()
        ->and(ActivityLog::query()->where('module', 'services')->where('subject_id', $service->id)->where('action', 'created')->count())->toBe(1);
});

test('Service creation rejects invalid required fields and files', function () {
    $this->actingAs($this->superAdmin)
        ->post(route('admin.services.store'), servicePayload([
            'name' => '',
            'image' => UploadedFile::fake()->create('service.pdf', 50, 'application/pdf'),
            'isactive' => 'invalid',
        ]))
        ->assertSessionHasErrors(['name', 'image', 'isactive']);

    expect(Service::query()->count())->toBe(0);
});

test('Service update retains then replaces its image and logs status changes', function () {
    $oldPath = 'backend-images/services/old.png';
    File::ensureDirectoryExists(public_path('backend-images/services'));
    File::put(public_path($oldPath), 'old');
    $this->actingAs($this->superAdmin);
    $service = Service::query()->create([
        'name' => 'Original Service',
        'image' => $oldPath,
        'description' => 'Original description.',
        'isactive' => true,
    ]);
    $editor = serviceAdmin(['services.edit']);

    $this->actingAs($editor)->post(route('admin.services.update', ['id' => $service->id]), [
        'name' => 'Updated Service',
        'description' => 'Updated description.',
        'isactive' => '1',
    ])->assertRedirect(route('admin.services.index'));

    expect($service->refresh()->image)->toBe($oldPath)
        ->and(File::exists(public_path($oldPath)))->toBeTrue();

    $this->actingAs($editor)->post(route('admin.services.update', ['id' => $service->id]), [
        'name' => 'Updated Service',
        'image' => UploadedFile::fake()->image('replacement.jpg'),
        'description' => 'Updated description.',
        'isactive' => '0',
    ])->assertRedirect(route('admin.services.index'));

    $service->refresh();

    expect($service->image)->not->toBe($oldPath)
        ->and($service->isactive)->toBeFalse()
        ->and($service->created_by)->toBe($this->superAdmin->id)
        ->and($service->updated_by)->toBe($editor->id)
        ->and(File::exists(public_path($oldPath)))->toBeFalse()
        ->and(File::exists(public_path($service->image)))->toBeTrue()
        ->and(ActivityLog::query()->where('module', 'services')->where('subject_id', $service->id)->where('action', 'status_changed')->count())->toBe(1);
});

test('Service deletion removes its record and managed image and records activity', function () {
    $path = 'backend-images/services/delete.png';
    File::ensureDirectoryExists(public_path('backend-images/services'));
    File::put(public_path($path), 'image');
    $service = Service::query()->create([
        'name' => 'Delete Service',
        'image' => $path,
        'description' => null,
        'isactive' => true,
    ]);

    $this->actingAs($this->superAdmin)
        ->delete(route('admin.services.destroy', ['id' => $service->id]))
        ->assertRedirect(route('admin.services.index'));

    $this->assertModelMissing($service);
    expect(File::exists(public_path($path)))->toBeFalse()
        ->and(ActivityLog::query()->where('module', 'services')->where('action', 'deleted')->count())->toBe(1);
});
