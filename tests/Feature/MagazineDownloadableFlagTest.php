<?php

use App\Models\ActivityLog;
use App\Models\Language;
use App\Models\Magazine;
use App\Models\MediaStorageLocation;
use App\Models\StorageProvider;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use App\Services\Storage\MediaStorageService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();

    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
    $this->language = Language::query()->create([
        'name' => 'English',
        'code' => 'en',
        'is_active' => true,
    ]);
    $this->provider = StorageProvider::query()->create([
        'name' => 'Local Server',
        'slug' => 'local',
        'disk' => 'local_media',
        'provider_type' => StorageProvider::TYPE_LOCAL,
        'is_active' => true,
        'is_default' => true,
        'priority' => 1,
    ]);
});

function magazineStorePayloadForDownloadableFlag(bool $isDownloadable): array
{
    return [
        'language' => 'en',
        'title' => $isDownloadable ? 'Download Edition' : 'View Only Edition',
        'pdf' => UploadedFile::fake()->create('Digital-Magazine.pdf', 100, 'application/pdf'),
        'provider_ids' => [test()->provider->id],
        'isFree' => '0',
        'isFeatured' => '0',
        'is_downloadable' => $isDownloadable ? '1' : '0',
    ];
}

function mockSuccessfulMagazinePdfStore(): void
{
    test()->mock(MediaStorageService::class, function ($mock): void {
        $mock->shouldReceive('store')->once()->andReturn(new Collection([
            new MediaStorageLocation(['status' => MediaStorageLocation::STATUS_AVAILABLE]),
        ]));
    });
}

test('database and model default new magazines to view only', function () {
    $magazine = Magazine::query()->create([
        'language' => 'en',
        'title' => 'Default Edition',
    ]);

    expect($magazine->refresh()->is_downloadable)->toBeFalse()
        ->and($magazine->getRawOriginal('is_downloadable'))->toBe(0);
});

test('magazine visit counter defaults hidden and admin can enable it on create', function () {
    $defaultMagazine = Magazine::query()->create([
        'language' => 'en',
        'title' => 'Default Counter Edition',
    ]);

    expect($defaultMagazine->refresh()->show_visit_counter)->toBeFalse()
        ->and($defaultMagazine->getRawOriginal('show_visit_counter'))->toBe(0);

    mockSuccessfulMagazinePdfStore();
    $payload = magazineStorePayloadForDownloadableFlag(false);
    $payload['title'] = 'Visible Counter Edition';
    $payload['show_visit_counter'] = '1';

    $this->actingAs($this->superAdmin)
        ->post(route('admin.magazine.store'), $payload)
        ->assertRedirect(route('admin.magazine.index'));

    expect(Magazine::query()->where('title', 'Visible Counter Edition')->firstOrFail()->show_visit_counter)
        ->toBeTrue();
});

test('magazine edit loads and updates visit counter visibility', function () {
    $magazine = Magazine::query()->create([
        'language' => 'en',
        'title' => 'Counter Toggle Edition',
        'isFree' => false,
        'isFeatured' => false,
        'isActive' => true,
        'show_visit_counter' => true,
    ]);

    $this->actingAs($this->superAdmin)
        ->get(route('admin.magazine.edit', ['id' => $magazine->id]))
        ->assertSuccessful()
        ->assertSee('Show Visit Counter')
        ->assertSee('name="show_visit_counter" type="radio" value="1" checked', false);

    $this->post(route('admin.magazine.update', ['id' => $magazine->id]), [
        'language' => 'en',
        'title' => 'Counter Toggle Edition',
        'isFree' => '0',
        'isFeatured' => '0',
        'isActive' => '1',
        'is_downloadable' => '0',
        'show_visit_counter' => '0',
    ])->assertRedirect(route('admin.magazine.index'));

    expect($magazine->refresh()->show_visit_counter)->toBeFalse();
});

test('admin can create a view only magazine', function () {
    mockSuccessfulMagazinePdfStore();

    $this->actingAs($this->superAdmin)
        ->post(route('admin.magazine.store'), magazineStorePayloadForDownloadableFlag(false))
        ->assertRedirect(route('admin.magazine.index'));

    expect(Magazine::query()->where('title', 'View Only Edition')->firstOrFail()->is_downloadable)->toBeFalse();
});

test('admin can create a downloadable magazine', function () {
    mockSuccessfulMagazinePdfStore();

    $this->actingAs($this->superAdmin)
        ->post(route('admin.magazine.store'), magazineStorePayloadForDownloadableFlag(true))
        ->assertRedirect(route('admin.magazine.index'));

    expect(Magazine::query()->where('title', 'Download Edition')->firstOrFail()->is_downloadable)->toBeTrue();
});

test('admin can toggle downloadability without changing pdf storage metadata', function () {
    $magazine = Magazine::query()->create([
        'language' => 'en',
        'title' => 'Toggle Edition',
        'isFree' => false,
        'isFeatured' => false,
        'isActive' => true,
        'is_downloadable' => false,
    ]);
    $location = MediaStorageLocation::query()->create([
        'media_type' => MediaStorageLocation::MEDIA_MAGAZINE,
        'media_id' => $magazine->id,
        'storage_provider_id' => $this->provider->id,
        'path' => 'magazine/'.$magazine->id.'/private-file.pdf',
        'file_name' => 'Digital-Magazine.pdf',
        'size' => 102400,
        'mime_type' => 'application/pdf',
        'checksum' => 'unchanged-checksum',
        'is_primary' => true,
        'priority' => 1,
        'status' => MediaStorageLocation::STATUS_AVAILABLE,
        'verification_status' => MediaStorageLocation::VERIFICATION_PENDING,
    ]);
    $originalStorage = $location->only(['path', 'file_name', 'size', 'mime_type', 'checksum', 'status']);

    $this->actingAs($this->superAdmin)
        ->post(route('admin.magazine.update', ['id' => $magazine->id]), [
            'language' => 'en',
            'title' => 'Toggle Edition',
            'isFree' => '0',
            'isFeatured' => '0',
            'isActive' => '1',
            'is_downloadable' => '1',
        ])->assertRedirect(route('admin.magazine.index'));

    $magazine->refresh();

    expect($magazine->is_downloadable)->toBeTrue()
        ->and($magazine->updated_by)->toBe($this->superAdmin->id)
        ->and($location->refresh()->only(array_keys($originalStorage)))->toBe($originalStorage);

    $activityLog = ActivityLog::query()
        ->where('module', 'magazines')
        ->where('subject_id', $magazine->id)
        ->where('action', 'updated')
        ->latest('id')
        ->firstOrFail();

    expect((bool) $activityLog->old_values['is_downloadable'])->toBeFalse()
        ->and((bool) $activityLog->new_values['is_downloadable'])->toBeTrue();

    $this->post(route('admin.magazine.update', ['id' => $magazine->id]), [
        'language' => 'en',
        'title' => 'Toggle Edition',
        'isFree' => '0',
        'isFeatured' => '0',
        'isActive' => '1',
        'is_downloadable' => '0',
    ])->assertRedirect(route('admin.magazine.index'));

    expect($magazine->refresh()->is_downloadable)->toBeFalse()
        ->and($location->refresh()->only(array_keys($originalStorage)))->toBe($originalStorage);
});

test('admin listing shows allowed and view only download states', function () {
    $downloadableMagazine = Magazine::query()->create(['language' => 'en', 'title' => 'Allowed Edition', 'is_downloadable' => true]);
    Magazine::query()->create(['language' => 'en', 'title' => 'Restricted Edition', 'is_downloadable' => false]);

    $this->actingAs($this->superAdmin)
        ->get(route('admin.magazine.index'))
        ->assertSuccessful()
        ->assertSee('Download')
        ->assertSee('Allowed')
        ->assertSee('View Only');

    $this->get(route('admin.magazine.create'))
        ->assertSuccessful()
        ->assertSee('Allow PDF Download')
        ->assertSee('No — View/Read Only');

    $this->get(route('admin.magazine.edit', ['id' => $downloadableMagazine->id]))
        ->assertSuccessful()
        ->assertSee('Yes — Download Allowed');
});
