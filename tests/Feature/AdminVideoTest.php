<?php

use App\Models\ActivityLog;
use App\Models\Currency;
use App\Models\SubscriptionProduct;
use App\Models\User;
use App\Models\Video;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();
    $this->originalVideoPublicPath = public_path();
    $this->temporaryVideoPublicPath = storage_path('framework/testing/video-public-'.Str::uuid());
    File::ensureDirectoryExists($this->temporaryVideoPublicPath);
    app()->usePublicPath($this->temporaryVideoPublicPath);
    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

afterEach(function () {
    app()->usePublicPath($this->originalVideoPublicPath);
    File::deleteDirectory($this->temporaryVideoPublicPath);
});

function videoAdmin(array $permissions): User
{
    $role = Role::findOrCreate('video-admin-'.Str::random(8), 'web');
    $role->syncPermissions(['admin.access', ...$permissions]);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    return $admin;
}

function videoPayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'Editorial Interview',
        'thumbnail' => UploadedFile::fake()->image('video.png'),
        'video_link' => 'https://example.com/videos/editorial-interview',
        'short_description' => 'A short editorial interview.',
        'is_free' => '1',
        'is_active' => '1',
        'is_share' => '1',
    ], $overrides);
}

test('Videos permissions are registered in Content Setup', function () {
    expect(config('admin_modules.modules.videos.group'))->toBe('Content Setup')
        ->and(config('admin_modules.modules.videos.actions'))->toBe([
            'view' => 'View',
            'create' => 'Create',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'publish' => 'Publish',
        ]);

    foreach (['videos.view', 'videos.create', 'videos.edit', 'videos.delete', 'videos.publish'] as $permission) {
        $this->assertDatabaseHas('permissions', ['name' => $permission, 'guard_name' => 'web']);
    }
});

test('Video routes and sidebar enforce permissions', function () {
    $viewer = videoAdmin(['dashboard.view', 'videos.view']);
    $unauthorizedAdmin = videoAdmin(['dashboard.view']);

    $this->actingAs($viewer)->get(route('admin.video.index'))->assertSuccessful();
    $this->actingAs($unauthorizedAdmin)->get(route('admin.video.index'))->assertForbidden();
    $this->actingAs($viewer)->get(route('admin.dashboard'))->assertSuccessful()->assertSee(route('admin.video.index'), false);
    $this->actingAs($unauthorizedAdmin)->get(route('admin.dashboard'))->assertSuccessful()->assertDontSee(route('admin.video.index'), false);
});

test('Video creation stores safe data ownership thumbnail and activity', function () {
    $this->actingAs($this->superAdmin)
        ->post(route('admin.video.store'), videoPayload(['status' => Video::STATUS_PUBLISHED]))
        ->assertRedirect(route('admin.video.index'));

    $video = Video::query()->sole();

    expect($video->thumbnail)->toStartWith('backend-images/video/')
        ->and($video->thumbnail)->not->toContain('video.png')
        ->and($video->is_free)->toBeTrue()
        ->and($video->is_active)->toBeTrue()
        ->and($video->is_share)->toBeTrue()
        ->and($video->status)->toBe(Video::STATUS_DRAFT)
        ->and($video->published_at)->toBeNull()
        ->and($video->published_by)->toBeNull()
        ->and($video->created_by)->toBe($this->superAdmin->id)
        ->and($video->updated_by)->toBe($this->superAdmin->id)
        ->and(File::exists(public_path($video->thumbnail)))->toBeTrue()
        ->and(ActivityLog::query()->where('module', 'videos')->where('subject_id', $video->id)->where('action', 'created')->count())->toBe(1);
});

test('legacy videos receive the migration default published status', function () {
    $legacyVideoId = DB::table('videos')->insertGetId([
        'title' => 'Legacy Video',
        'video_link' => 'https://example.com/legacy-video',
        'is_free' => false,
        'is_active' => true,
        'is_share' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $legacyVideo = Video::query()->findOrFail($legacyVideoId);

    expect($legacyVideo->status)->toBe(Video::STATUS_PUBLISHED)
        ->and($legacyVideo->published_at)->toBeNull()
        ->and($legacyVideo->published_by)->toBeNull();
});

test('Video validation rejects invalid input', function () {
    $this->actingAs($this->superAdmin)
        ->post(route('admin.video.store'), videoPayload([
            'title' => '',
            'thumbnail' => UploadedFile::fake()->create('video.pdf', 50, 'application/pdf'),
            'video_link' => 'not-a-url',
            'is_free' => 'invalid',
            'is_active' => 'invalid',
            'is_share' => 'invalid',
        ]))
        ->assertSessionHasErrors(['title', 'thumbnail', 'video_link', 'is_free', 'is_active', 'is_share']);

    expect(Video::query()->count())->toBe(0);
});

test('Video update retains and replaces thumbnail while logging status changes', function () {
    $oldPath = 'backend-images/video/old.png';
    File::ensureDirectoryExists(public_path('backend-images/video'));
    File::put(public_path($oldPath), 'old');
    $this->actingAs($this->superAdmin);
    $video = Video::query()->create([
        'title' => 'Original Video',
        'thumbnail' => $oldPath,
        'video_link' => 'https://example.com/original',
        'short_description' => 'Original description.',
        'is_free' => false,
        'is_active' => true,
        'is_share' => false,
    ]);
    $editor = videoAdmin(['videos.edit']);

    $this->actingAs($editor)->post(route('admin.video.update', ['id' => $video->id]), [
        'title' => 'Updated Video',
        'video_link' => 'https://example.com/updated',
        'short_description' => 'Updated description.',
        'is_free' => '1',
        'is_active' => '0',
        'is_share' => '1',
    ])->assertRedirect(route('admin.video.index'));

    expect($video->refresh()->thumbnail)->toBe($oldPath)
        ->and($video->is_active)->toBeFalse()
        ->and($video->updated_by)->toBe($editor->id)
        ->and(File::exists(public_path($oldPath)))->toBeTrue()
        ->and(ActivityLog::query()->where('module', 'videos')->where('subject_id', $video->id)->where('action', 'status_changed')->count())->toBe(1);

    $this->actingAs($editor)->post(route('admin.video.update', ['id' => $video->id]), [
        'title' => 'Updated Video',
        'thumbnail' => UploadedFile::fake()->image('replacement.jpg'),
        'video_link' => 'https://example.com/updated',
        'short_description' => 'Updated description.',
        'is_free' => '1',
        'is_active' => '0',
        'is_share' => '1',
    ])->assertRedirect(route('admin.video.index'));

    expect($video->refresh()->thumbnail)->not->toBe($oldPath)
        ->and(File::exists(public_path($oldPath)))->toBeFalse()
        ->and(File::exists(public_path($video->thumbnail)))->toBeTrue();
});

test('Video listing keeps visibility and publication status separate with the publish action', function () {
    $this->actingAs($this->superAdmin);
    $draftVideo = Video::query()->create([
        'title' => 'Draft Video',
        'video_link' => 'https://example.com/draft-video',
        'is_active' => true,
        'status' => Video::STATUS_DRAFT,
    ]);
    $publishedVideo = Video::query()->create([
        'title' => 'Published Video',
        'video_link' => 'https://example.com/published-video',
        'is_active' => false,
        'status' => Video::STATUS_PUBLISHED,
    ]);

    $this->get(route('admin.video.index'))
        ->assertSuccessful()
        ->assertSee('Visibility')
        ->assertSee('Publication Status')
        ->assertSee('Draft')
        ->assertSee('Published')
        ->assertSee('Active')
        ->assertSee('Deactive')
        ->assertSee(route('admin.video.publish', ['id' => $draftVideo->id]), false)
        ->assertDontSee(route('admin.video.publish', ['id' => $publishedVideo->id]), false);

    $viewer = videoAdmin(['videos.view']);

    $this->actingAs($viewer)
        ->get(route('admin.video.index'))
        ->assertSuccessful()
        ->assertDontSee(route('admin.video.publish', ['id' => $draftVideo->id]), false);

    $this->post(route('admin.video.publish', ['id' => $draftVideo->id]))
        ->assertForbidden();
});

test('an active draft Video can be published without changing its plan assignments', function () {
    $this->actingAs($this->superAdmin);
    $video = Video::query()->create([
        'title' => 'Publishable Video',
        'video_link' => 'https://example.com/publishable-video',
        'is_active' => true,
        'status' => Video::STATUS_DRAFT,
    ]);
    $plan = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_PLAN,
        'name' => 'Assigned Video Plan',
        'currency_id' => Currency::query()->create([
            'name' => 'Pakistani Rupee',
            'code' => 'PKR',
            'symbol' => 'Rs',
            'isActive' => true,
        ])->id,
        'price' => 100,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    $plan->videos()->attach($video->id);
    $publisher = videoAdmin(['videos.publish']);

    $this->actingAs($publisher)
        ->post(route('admin.video.publish', ['id' => $video->id]))
        ->assertSessionHas('status', 'Video published successfully.');

    $video->refresh();

    expect($video->status)->toBe(Video::STATUS_PUBLISHED)
        ->and($video->published_at)->not->toBeNull()
        ->and($video->published_by)->toBe($publisher->id)
        ->and($plan->videos()->whereKey($video)->exists())->toBeTrue()
        ->and(ActivityLog::query()->where('module', 'videos')->where('subject_id', $video->id)->where('action', 'published')->where('user_id', $publisher->id)->exists())->toBeTrue();
});

test('a Video cannot be published twice or while inactive', function () {
    $this->actingAs($this->superAdmin);
    $publishedVideo = Video::query()->forceCreate([
        'title' => 'Already Published Video',
        'video_link' => 'https://example.com/already-published-video',
        'is_active' => true,
        'status' => Video::STATUS_PUBLISHED,
        'published_at' => now()->subHour(),
        'published_by' => $this->superAdmin->id,
    ]);
    $inactiveVideo = Video::query()->create([
        'title' => 'Inactive Video',
        'video_link' => 'https://example.com/inactive-video',
        'is_active' => false,
        'status' => Video::STATUS_DRAFT,
    ]);

    $this->post(route('admin.video.publish', ['id' => $publishedVideo->id]))
        ->assertSessionHas('error', 'This Video is already published.');
    $this->post(route('admin.video.publish', ['id' => $inactiveVideo->id]))
        ->assertSessionHas('error', 'Activate the Video before publishing it.');

    expect($publishedVideo->refresh()->published_by)->toBe($this->superAdmin->id)
        ->and($inactiveVideo->refresh()->status)->toBe(Video::STATUS_DRAFT)
        ->and(ActivityLog::query()->where('module', 'videos')->where('subject_id', $publishedVideo->id)->where('action', 'published')->count())->toBe(0);
});

test('a draft Video requires a title and link before publishing', function () {
    $this->actingAs($this->superAdmin);
    $video = Video::query()->create([
        'title' => '',
        'video_link' => '',
        'is_active' => true,
        'status' => Video::STATUS_DRAFT,
    ]);

    $this->post(route('admin.video.publish', ['id' => $video->id]))
        ->assertSessionHas('error', 'A title and Video link are required before publishing.');

    expect($video->refresh()->status)->toBe(Video::STATUS_DRAFT)
        ->and($video->published_at)->toBeNull()
        ->and($video->published_by)->toBeNull();
});

test('Video deletion removes its managed thumbnail and records activity', function () {
    $path = 'backend-images/video/delete.png';
    File::ensureDirectoryExists(public_path('backend-images/video'));
    File::put(public_path($path), 'image');
    $this->actingAs($this->superAdmin);
    $video = Video::query()->create([
        'title' => 'Delete Video',
        'thumbnail' => $path,
        'video_link' => 'https://example.com/delete',
        'is_free' => false,
        'is_active' => true,
        'is_share' => false,
    ]);

    $this->delete(route('admin.video.destroy', ['id' => $video->id]))
        ->assertRedirect(route('admin.video.index'));

    $this->assertModelMissing($video);
    expect(File::exists(public_path($path)))->toBeFalse()
        ->and(ActivityLog::query()->where('module', 'videos')->where('action', 'deleted')->count())->toBe(1);
});
