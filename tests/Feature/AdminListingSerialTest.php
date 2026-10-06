<?php

use App\Models\Article;
use App\Models\Magazine;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();

    $this->serialRootAdmin = User::factory()->create(['is_active' => true]);
    $this->serialRootAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));

    $role = Role::findOrCreate('listing-serial-viewer', 'web');
    $role->syncPermissions(['admin.access', 'articles.view', 'magazines.view']);
    $this->serialAdmin = User::factory()->create([
        'is_active' => true,
        'parent_admin_id' => $this->serialRootAdmin->id,
    ]);
    $this->serialAdmin->assignRole($role);
    $this->unrelatedSerialAdmin = User::factory()->create([
        'is_active' => true,
        'parent_admin_id' => $this->serialRootAdmin->id,
    ]);
    $this->unrelatedSerialAdmin->assignRole($role);
});

test('Article and Magazine listings number the accessible rendered dataset instead of using database IDs', function () {
    Article::query()->forceCreate(['title' => 'Hidden Article One', 'owner_admin_id' => $this->unrelatedSerialAdmin->id]);
    Article::query()->forceCreate(['title' => 'Hidden Article Two', 'owner_admin_id' => $this->unrelatedSerialAdmin->id]);
    $visibleArticle = Article::query()->forceCreate(['title' => 'Visible Article', 'owner_admin_id' => $this->serialAdmin->id]);

    Magazine::query()->forceCreate(['title' => 'Hidden Magazine One', 'owner_admin_id' => $this->unrelatedSerialAdmin->id]);
    Magazine::query()->forceCreate(['title' => 'Hidden Magazine Two', 'owner_admin_id' => $this->unrelatedSerialAdmin->id]);
    $visibleMagazine = Magazine::query()->forceCreate(['title' => 'Visible Magazine', 'owner_admin_id' => $this->serialAdmin->id]);

    $articleResponse = $this->actingAs($this->serialAdmin)->get(route('admin.article.index'));
    $magazineResponse = $this->get(route('admin.magazine.index'));

    $articleResponse->assertSuccessful()
        ->assertSee('<td data-admin-serial-value>1</td>', false)
        ->assertDontSee('<td data-admin-serial-value>'.$visibleArticle->id.'</td>', false);
    $magazineResponse->assertSuccessful()
        ->assertSee('<td data-admin-serial-value>1</td>', false)
        ->assertDontSee('<td data-admin-serial-value>'.$visibleMagazine->id.'</td>', false);
});

test('all current display serial tables opt in to centralized serial numbering', function (string $relativePath) {
    $contents = file_get_contents(resource_path('views/admin/'.$relativePath));

    expect($contents)->toContain('data-admin-datatable data-admin-serials')
        ->and($contents)->toContain('data-admin-serial-value>{{ $loop->iteration }}</td>');
})->with([
    'Articles' => 'article/view-article.blade.php',
    'Magazines' => 'magazine/view-magazine.blade.php',
    'Admin Users' => 'users/view-users.blade.php',
    'Categories' => 'categories/view-categories.blade.php',
    'Authors' => 'author/view-author.blade.php',
    'Tags' => 'tags/view-tags.blade.php',
    'Sliders' => 'slider/view-slider.blade.php',
    'Banners' => 'banner/view-banner.blade.php',
    'FAQs' => 'faq/view-faq.blade.php',
    'FAQ Categories' => 'faq-category/view-faq-category.blade.php',
    'Meta Tags' => 'meta-tags/view-meta-tags.blade.php',
    'Currencies' => 'currency/view-currency.blade.php',
    'Subscription Plans' => 'subscription-plan/view-subscription-plan.blade.php',
    'Memberships' => 'membership/view-membership.blade.php',
    'Roles' => 'roles/view-roles.blade.php',
]);

test('central DataTable serial numbering uses the filtered page offset', function () {
    $adminJavaScript = file_get_contents(resource_path('js/admin/admin.js'));

    expect($adminJavaScript)->toContain('dataTable.page.info().start')
        ->and($adminJavaScript)->toContain("{ page: 'current', search: 'applied', order: 'applied' }")
        ->and($adminJavaScript)->toContain('pageStart + index + 1');
});
