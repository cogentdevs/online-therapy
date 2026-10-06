<?php

use App\Models\ActivityLog;
use App\Models\Consultancy;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    Permission::findOrCreate('admin.access', 'web');
    foreach (array_keys(config('admin_modules.modules.consultancies.actions')) as $action) {
        Permission::findOrCreate('consultancies.'.$action, 'web');
    }
    Permission::findOrCreate('dashboard.view', 'web');
    Permission::findOrCreate('roles.view', 'web');
    Permission::findOrCreate('roles.create', 'web');

    $this->originalConsultancyPublicPath = public_path();
    $this->temporaryConsultancyPublicPath = storage_path('framework/testing/consultancy-public-'.Str::uuid());
    File::ensureDirectoryExists($this->temporaryConsultancyPublicPath);
    app()->usePublicPath($this->temporaryConsultancyPublicPath);

    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

afterEach(function (): void {
    app()->usePublicPath($this->originalConsultancyPublicPath);
    File::deleteDirectory($this->temporaryConsultancyPublicPath);
});

function consultancyAdmin(array $permissions): User
{
    $role = Role::findOrCreate('consultancy-admin-'.Str::random(8), 'web');
    $role->syncPermissions(['admin.access', ...$permissions]);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    return $admin;
}

function consultancyPayload(array $overrides = []): array
{
    return array_replace([
        'title' => 'Strategic Editorial Consultancy',
        'button_label' => 'Book Consultancy',
        'image' => UploadedFile::fake()->image('consultancy.png'),
        'short_description' => 'A concise editorial advisory service.',
        'description' => '<p>Structured editorial guidance for publication teams.</p>',
        'duration_type' => 'hours',
        'duration_value' => 2,
        'consultancy_medium' => 'video_call',
        'isActive' => '1',
        'isFeatured' => '0',
    ], $overrides);
}

function ownedConsultancy(User $owner, array $attributes = []): Consultancy
{
    return Consultancy::query()->forceCreate(array_replace([
        'language' => 'en',
        'title' => 'Owned Consultancy '.Str::random(8),
        'button_label' => 'Book Consultancy',
        'image' => null,
        'short_description' => 'Short description',
        'description' => '<p>Meaningful consultancy description.</p>',
        'duration_type' => 'hours',
        'duration_value' => 1,
        'consultancy_medium' => 'video_call',
        'isActive' => true,
        'isFeatured' => false,
        'status' => Consultancy::STATUS_DRAFT,
        'owner_admin_id' => $owner->id,
        'created_by' => $owner->id,
        'updated_by' => $owner->id,
    ], $attributes));
}

test('Consultancy permissions are registered by the Content Setup module configuration', function (): void {
    expect(config('admin_modules.modules.consultancies.group'))->toBe('Content Setup')
        ->and(config('admin_modules.modules.consultancies.actions'))->toBe([
            'view' => 'View',
            'create' => 'Create',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'publish' => 'Publish',
            'related.remove' => 'Remove Related Consultancy',
        ]);
});

test('authorized Admin can access Consultancy navigation while unauthorized Admin cannot', function (): void {
    $viewer = consultancyAdmin(['dashboard.view', 'consultancies.view']);
    $unauthorizedAdmin = consultancyAdmin(['dashboard.view']);

    $this->actingAs($viewer)->get(route('admin.consultancy.index'))->assertSuccessful();
    $this->actingAs($unauthorizedAdmin)->get(route('admin.consultancy.index'))->assertForbidden();
    $dashboard = $this->actingAs($viewer)->get(route('admin.dashboard'));
    $dashboard->assertSuccessful()->assertSee(route('admin.consultancy.index'), false);
    $this->actingAs($unauthorizedAdmin)->get(route('admin.dashboard'))->assertSuccessful()->assertDontSee(route('admin.consultancy.index'), false);

    expect(mb_strpos($dashboard->getContent(), route('admin.consultancy.index')))
        ->toBeLessThan(mb_strpos($dashboard->getContent(), 'id="contentManagementMenu"'));
    $this->actingAs($viewer)
        ->get(route('admin.consultancy.index'))
        ->assertDontSee('collapse show" id="contentManagementMenu"', false);
});

test('Consultancy permission group is visible in Role Management', function (): void {
    $roleManager = consultancyAdmin(['roles.view', 'roles.create']);

    $this->actingAs($roleManager)
        ->get(route('admin.roles.create'))
        ->assertSuccessful()
        ->assertSee('Consultancy')
        ->assertSee('Publish');
});

test('Consultancy creation stores fixed English draft ownership image and activity', function (): void {
    $related = ownedConsultancy($this->superAdmin, ['title' => 'Related Consultancy']);

    $this->actingAs($this->superAdmin)
        ->post(route('admin.consultancy.store'), consultancyPayload([
            'language' => 'ur',
            'isFeatured' => '1',
            'related_consultancy_ids' => [$related->id],
        ]))
        ->assertRedirect(route('admin.consultancy.index'));

    $consultancy = Consultancy::query()->where('title', 'Strategic Editorial Consultancy')->sole();

    expect($consultancy->language)->toBe('en')
        ->and($consultancy->status)->toBe(Consultancy::STATUS_DRAFT)
        ->and($consultancy->published_at)->toBeNull()
        ->and($consultancy->published_by)->toBeNull()
        ->and($consultancy->owner_admin_id)->toBe($this->superAdmin->id)
        ->and($consultancy->created_by)->toBe($this->superAdmin->id)
        ->and($consultancy->updated_by)->toBe($this->superAdmin->id)
        ->and($consultancy->isActive)->toBeTrue()
        ->and($consultancy->isFeatured)->toBeTrue()
        ->and($consultancy->button_label)->toBe('Book Consultancy')
        ->and($consultancy->duration_type)->toBe('hours')
        ->and($consultancy->duration_value)->toBe(2)
        ->and($consultancy->consultancy_medium)->toBe('video_call')
        ->and($consultancy->image)->toStartWith('backend-images/consultancy/')
        ->and(File::exists(public_path($consultancy->image)))->toBeTrue()
        ->and($consultancy->relatedConsultancies()->whereKey($related)->exists())->toBeTrue()
        ->and(ActivityLog::query()->where('module', 'consultancies')->where('subject_id', $consultancy->id)->where('action', 'created')->exists())->toBeTrue();
});

test('Consultancy validation rejects invalid data', function (): void {
    $this->actingAs($this->superAdmin)
        ->post(route('admin.consultancy.store'), consultancyPayload([
            'title' => '',
            'button_label' => str_repeat('a', 101),
            'image' => UploadedFile::fake()->create('consultancy.pdf', 100, 'application/pdf'),
            'description' => '',
            'duration_type' => 'years',
            'duration_value' => 0,
            'consultancy_medium' => 'online',
            'isActive' => 'invalid',
            'isFeatured' => 'invalid',
            'related_consultancy_ids' => [999999, 999999],
        ]))
        ->assertSessionHasErrors([
            'title', 'button_label', 'image', 'description', 'duration_type', 'duration_value', 'consultancy_medium',
            'isActive', 'isFeatured', 'related_consultancy_ids.0', 'related_consultancy_ids.1',
        ]);

    expect(Consultancy::query()->count())->toBe(0);
});

test('Consultancy button label is required', function (): void {
    $this->actingAs($this->superAdmin)
        ->post(route('admin.consultancy.store'), consultancyPayload(['button_label' => '']))
        ->assertSessionHasErrors('button_label');

    expect(Consultancy::query()->count())->toBe(0);
});

test('Consultancy update retains publication and ownership fields while safely replacing its image', function (): void {
    $oldPath = 'backend-images/consultancy/old.png';
    File::ensureDirectoryExists(public_path('backend-images/consultancy'));
    File::put(public_path($oldPath), 'old image');
    $editor = consultancyAdmin(['consultancies.edit']);
    $consultancy = ownedConsultancy($editor, [
        'image' => $oldPath,
        'status' => Consultancy::STATUS_PUBLISHED,
        'published_at' => now()->subDay(),
        'published_by' => $editor->id,
    ]);

    $this->actingAs($editor)
        ->get(route('admin.consultancy.edit', ['id' => $consultancy->id]))
        ->assertSuccessful()
        ->assertSee('value="Book Consultancy"', false);

    $this->actingAs($editor)
        ->post(route('admin.consultancy.update', ['id' => $consultancy->id]), consultancyPayload([
            'image' => null,
            'title' => 'Updated Consultancy',
            'button_label' => 'Get Consultation',
            'isActive' => '0',
            'isFeatured' => '1',
        ]))
        ->assertRedirect(route('admin.consultancy.index'));

    expect($consultancy->refresh()->image)->toBe($oldPath)
        ->and($consultancy->status)->toBe(Consultancy::STATUS_PUBLISHED)
        ->and($consultancy->published_by)->toBe($editor->id)
        ->and($consultancy->created_by)->toBe($editor->id)
        ->and($consultancy->owner_admin_id)->toBe($editor->id)
        ->and($consultancy->button_label)->toBe('Get Consultation')
        ->and($consultancy->updated_by)->toBe($editor->id)
        ->and(File::exists(public_path($oldPath)))->toBeTrue();

    $this->actingAs($editor)
        ->post(route('admin.consultancy.update', ['id' => $consultancy->id]), consultancyPayload([
            'image' => UploadedFile::fake()->image('replacement.jpg'),
            'title' => 'Updated Consultancy',
            'button_label' => 'Contact Us',
            'isActive' => '0',
            'isFeatured' => '1',
        ]))
        ->assertRedirect(route('admin.consultancy.index'));

    expect($consultancy->refresh()->image)->not->toBe($oldPath)
        ->and($consultancy->button_label)->toBe('Contact Us')
        ->and(File::exists(public_path($oldPath)))->toBeFalse()
        ->and(File::exists(public_path($consultancy->image)))->toBeTrue()
        ->and(ActivityLog::query()->where('module', 'consultancies')->where('subject_id', $consultancy->id)->where('action', 'status_changed')->exists())->toBeTrue();
});

test('an active valid Consultancy can be published once and logs the publication', function (): void {
    $publisher = consultancyAdmin(['consultancies.publish']);
    $consultancy = ownedConsultancy($publisher);

    $this->actingAs($publisher)
        ->post(route('admin.consultancy.publish', ['id' => $consultancy->id]))
        ->assertSessionHas('status', 'Consultancy published successfully.');

    expect($consultancy->refresh()->status)->toBe(Consultancy::STATUS_PUBLISHED)
        ->and($consultancy->published_at)->not->toBeNull()
        ->and($consultancy->published_by)->toBe($publisher->id)
        ->and(ActivityLog::query()->where('module', 'consultancies')->where('subject_id', $consultancy->id)->where('action', 'published')->exists())->toBeTrue();

    $this->post(route('admin.consultancy.publish', ['id' => $consultancy->id]))
        ->assertSessionHas('error', 'This Consultancy is already published.');
});

test('inactive or incomplete Consultancy cannot be published and publish permission is enforced', function (): void {
    $inactive = ownedConsultancy($this->superAdmin, ['isActive' => false]);
    $incomplete = ownedConsultancy($this->superAdmin, ['description' => '<p><br></p>']);
    $unauthorizedAdmin = consultancyAdmin([]);

    $this->actingAs($this->superAdmin)
        ->post(route('admin.consultancy.publish', ['id' => $inactive->id]))
        ->assertSessionHas('error', 'Activate the Consultancy before publishing it.');
    $this->post(route('admin.consultancy.publish', ['id' => $incomplete->id]))
        ->assertSessionHas('error', 'A title and Consultancy description are required before publishing.');
    $this->actingAs($unauthorizedAdmin)
        ->post(route('admin.consultancy.publish', ['id' => $inactive->id]))
        ->assertForbidden();
});

test('related Consultancies are directional and can be removed without deleting the related record', function (): void {
    $consultancy = ownedConsultancy($this->superAdmin, ['title' => 'Consultancy A']);
    $related = ownedConsultancy($this->superAdmin, ['title' => 'Consultancy B']);
    $consultancy->relatedConsultancies()->attach($related->id);

    expect($consultancy->relatedConsultancies()->whereKey($related)->exists())->toBeTrue()
        ->and($related->relatedConsultancies()->whereKey($consultancy)->exists())->toBeFalse();

    $this->actingAs($this->superAdmin)
        ->delete(route('admin.consultancy.related.remove', [
            'consultancy' => $consultancy->id,
            'relatedConsultancy' => $related->id,
        ]))
        ->assertSessionHas('status', 'Related Consultancy removed successfully.');

    expect($consultancy->relatedConsultancies()->whereKey($related)->exists())->toBeFalse()
        ->and(Consultancy::query()->find($related->id))->not->toBeNull()
        ->and(ActivityLog::query()->where('module', 'consultancies')->where('subject_id', $consultancy->id)->where('action', 'related_removed')->exists())->toBeTrue();
});

test('self or out of ownership related Consultancies are rejected and deleting a Consultancy only removes its pivot rows', function (): void {
    $ownerRole = Role::findOrCreate('consultancy-owner-'.Str::random(8), 'web');
    $ownerRole->syncPermissions(['admin.access', 'consultancies.create', 'consultancies.edit', 'consultancies.delete']);
    $owner = User::factory()->create(['is_active' => true, 'parent_admin_id' => $this->superAdmin->id]);
    $owner->assignRole($ownerRole);
    $unrelatedOwner = User::factory()->create(['is_active' => true, 'parent_admin_id' => $this->superAdmin->id]);
    $unrelatedOwner->assignRole(Role::findOrCreate('consultancy-unrelated-'.Str::random(8), 'web'));
    $consultancy = ownedConsultancy($owner);
    $unrelated = ownedConsultancy($unrelatedOwner);

    $this->actingAs($owner)
        ->post(route('admin.consultancy.update', ['id' => $consultancy->id]), consultancyPayload([
            'image' => null,
            'related_consultancy_ids' => [$consultancy->id],
        ]))
        ->assertSessionHasErrors('related_consultancy_ids.0');
    $this->post(route('admin.consultancy.update', ['id' => $consultancy->id]), consultancyPayload([
        'image' => null,
        'related_consultancy_ids' => [$unrelated->id],
    ]))->assertSessionHasErrors('related_consultancy_ids');

    $consultancy->relatedConsultancies()->attach($unrelated->id);
    $unrelated->relatedConsultancies()->attach($consultancy->id);
    $this->actingAs($this->superAdmin)
        ->delete(route('admin.consultancy.destroy', ['id' => $consultancy->id]))
        ->assertRedirect(route('admin.consultancy.index'));

    expect(Consultancy::query()->find($consultancy->id))->toBeNull()
        ->and(Consultancy::query()->find($unrelated->id))->not->toBeNull()
        ->and($unrelated->relatedConsultancies()->whereKey($consultancy)->exists())->toBeFalse();
});
