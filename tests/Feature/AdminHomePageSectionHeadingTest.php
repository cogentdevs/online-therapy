<?php

use App\Models\HomePageSectionHeading;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();
    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

function homeHeadingAdmin(array $permissions): User
{
    $role = Role::findOrCreate('home-heading-admin-'.Str::random(8), 'web');
    $role->syncPermissions(['admin.access', ...$permissions]);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    return $admin;
}

function homeHeadingPayload(array $overrides = []): array
{
    $sections = [
        'latest_articles' => ['main_title' => 'Latest Articles', 'content_position' => 'right'],
        'below_slider_home_cards' => ['short_title' => 'Fresh Picks', 'main_title' => 'Important Topics', 'short_detail' => 'Selected stories', 'content_position' => 'center'],
        'editorial' => ['main_title' => 'Editorial', 'content_position' => 'left'],
        'weekly_magazine' => ['main_title' => 'Weekly Magazine', 'content_position' => 'right'],
        'audio' => ['main_title' => 'Audio', 'content_position' => 'right'],
        'newsletter' => ['main_title' => 'Newsletter', 'short_detail' => 'Subscribe for updates', 'content_position' => 'center'],
        'advertise_with_us' => ['main_title' => 'Advertise With Us', 'short_detail' => 'Reach our readers', 'content_position' => 'left'],
        'categories' => ['short_title' => 'Browse', 'main_title' => 'Popular Topics', 'short_detail' => 'Explore categories', 'content_position' => 'right'],
        'above_footer_home_cards' => ['short_title' => 'More Picks', 'main_title' => 'Important Topics', 'short_detail' => 'More selected stories', 'content_position' => 'center'],
    ];

    return ['sections' => array_replace_recursive($sections, $overrides)];
}

test('Home Headings permissions are synchronized from the Website registry', function () {
    expect(config('admin_modules.modules.home-headings.group'))->toBe('Website')
        ->and(config('admin_modules.modules.home-headings.actions'))->toBe(['view' => 'View', 'edit' => 'Edit']);

    foreach (['home-headings.view', 'home-headings.edit'] as $permission) {
        $this->assertDatabaseHas('permissions', ['name' => $permission, 'guard_name' => 'web']);
    }
});

test('settings page loads without rows and respects view and edit permissions', function () {
    $viewer = homeHeadingAdmin(['banners.view', 'home-headings.view']);

    $this->actingAs($viewer)
        ->get(route('admin.home-headings.index'))
        ->assertSuccessful()
        ->assertSee('Home Headings')
        ->assertSee('Slider Right - Latest Articles')
        ->assertSee('Above Footer - Home Cards')
        ->assertSee('class="form-select select2', false)
        ->assertSeeInOrder([
            route('admin.banner.index'),
            route('admin.home-headings.index'),
        ], false);

    expect(HomePageSectionHeading::query()->count())->toBe(0);

    $this->actingAs($viewer)
        ->put(route('admin.home-headings.update'), homeHeadingPayload())
        ->assertForbidden();

    $this->actingAs(homeHeadingAdmin(['dashboard.view']))
        ->get(route('admin.home-headings.index'))
        ->assertForbidden();
});

test('first save creates exactly nine Urdu rows with applicable values only', function () {
    $this->actingAs($this->superAdmin)
        ->put(route('admin.home-headings.update'), homeHeadingPayload())
        ->assertRedirect(route('admin.home-headings.index'))
        ->assertSessionHas('status');

    expect(HomePageSectionHeading::query()->where('language', 'ur')->count())->toBe(9);
    $this->assertDatabaseHas('home_page_section_headings', [
        'language' => 'ur',
        'section_name' => 'categories',
        'content_position' => 'right',
        'short_title' => 'Browse',
        'main_title' => 'Popular Topics',
        'short_detail' => 'Explore categories',
    ]);
    $this->assertDatabaseHas('home_page_section_headings', [
        'language' => 'ur',
        'section_name' => 'latest_articles',
        'short_title' => null,
        'main_title' => 'Latest Articles',
        'short_detail' => null,
    ]);
});

test('repeated save updates the same rows without duplicates', function () {
    $this->actingAs($this->superAdmin)->put(route('admin.home-headings.update'), homeHeadingPayload());

    $this->actingAs($this->superAdmin)
        ->put(route('admin.home-headings.update'), homeHeadingPayload([
            'categories' => ['main_title' => 'Updated Topics', 'content_position' => 'left'],
        ]))
        ->assertRedirect(route('admin.home-headings.index'));

    expect(HomePageSectionHeading::query()->count())->toBe(9)
        ->and(HomePageSectionHeading::query()->where('language', 'ur')->where('section_name', 'categories')->value('main_title'))->toBe('Updated Topics')
        ->and(HomePageSectionHeading::query()->where('language', 'ur')->where('section_name', 'categories')->value('content_position'))->toBe('left');
});

test('all settings accept null values and unknown sections are ignored', function () {
    $this->actingAs($this->superAdmin)
        ->put(route('admin.home-headings.update'), [
            'sections' => [
                'latest_articles' => ['main_title' => null, 'content_position' => null],
                'not_allowed' => ['main_title' => 'Ignored'],
            ],
        ])
        ->assertRedirect(route('admin.home-headings.index'));

    expect(HomePageSectionHeading::query()->count())->toBe(9)
        ->and(HomePageSectionHeading::query()->where('section_name', 'not_allowed')->exists())->toBeFalse()
        ->and(HomePageSectionHeading::query()->whereNotNull('content_position')->exists())->toBeFalse()
        ->and(HomePageSectionHeading::query()->whereNotNull('short_title')->exists())->toBeFalse()
        ->and(HomePageSectionHeading::query()->whereNotNull('main_title')->exists())->toBeFalse()
        ->and(HomePageSectionHeading::query()->whereNotNull('short_detail')->exists())->toBeFalse();
});

test('invalid content position is rejected without creating rows', function () {
    $this->actingAs($this->superAdmin)
        ->put(route('admin.home-headings.update'), homeHeadingPayload([
            'audio' => ['content_position' => 'top'],
        ]))
        ->assertSessionHasErrors('sections.audio.content_position');

    expect(HomePageSectionHeading::query()->count())->toBe(0);
});
