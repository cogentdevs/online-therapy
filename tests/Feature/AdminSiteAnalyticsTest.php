<?php

use App\Exports\SiteAnalyticsDetailExport;
use App\Exports\SiteAnalyticsSummaryExport;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Magazine;
use App\Models\SiteVisit;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();
});

function analyticsAdmin(array $permissions): User
{
    $role = Role::findOrCreate('analytics-admin-'.Str::random(6), 'web');
    $role->syncPermissions(['admin.access', ...$permissions]);
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole($role);

    return $user;
}

function adminSiteVisit(array $attributes = []): SiteVisit
{
    return SiteVisit::query()->create([
        'visitor_id' => (string) Str::uuid(),
        'public_token_hash' => hash('sha256', Str::random(64)),
        'page_key' => 'home',
        'country' => 'Pakistan',
        'city' => 'Karachi',
        'region' => 'Sindh',
        'duration_seconds' => 138,
        'started_at' => Carbon::parse('2026-09-12 12:00:00'),
        ...$attributes,
    ]);
}

test('site analytics pages require the configured admin permission', function () {
    $viewer = analyticsAdmin(['site-analytics.view']);
    $unauthorizedAdmin = analyticsAdmin(['dashboard.view']);

    $this->get(route('admin.site-analytics.index'))->assertRedirect(route('admin.login'));
    $this->actingAs(User::factory()->create())->get(route('admin.site-analytics.index'))->assertForbidden();
    $this->actingAs($unauthorizedAdmin)->get(route('admin.site-analytics.index'))->assertForbidden();
    $this->actingAs($viewer)->get(route('admin.site-analytics.index'))
        ->assertOk()
        ->assertSee('Site Analytics')
        ->assertSee(route('admin.site-analytics.index'));

    expect(config('admin_modules.modules.site-analytics.actions'))->toBe(['view' => 'View', 'export' => 'Export']);
});

test('pages mode aggregates static pages and excludes model backed visits', function () {
    $admin = analyticsAdmin(['site-analytics.view']);
    adminSiteVisit(['page_key' => 'home', 'started_at' => '2026-09-10 10:00:00']);
    adminSiteVisit(['page_key' => 'home', 'city' => 'Lahore', 'started_at' => '2026-09-12 15:00:00']);
    adminSiteVisit(['page_key' => 'about']);
    adminSiteVisit(['page_key' => 'mazmoon-detail', 'visitable_type' => Article::class, 'visitable_id' => 999]);

    $response = $this->actingAs($admin)->getJson(route('admin.site-analytics.data', [
        'type' => 'pages', 'draw' => 1, 'start' => 0, 'length' => 10,
    ]))->assertOk();

    $home = collect($response->json('data'))->firstWhere('name', 'Home');
    expect($response->json('recordsTotal'))->toBe(2)
        ->and($home['visit_count'])->toBe(2)
        ->and($home['last_location'])->toBe('Pakistan, Lahore');
});

test('dynamic analytics group by allowed model and preserve orphan history', function () {
    $admin = analyticsAdmin(['site-analytics.view']);
    $article = Article::query()->forceCreate([
        'language' => 'ur', 'title' => 'Analytics Article', 'article' => 'Body',
        'isFree' => true, 'isActive' => true, 'status' => Article::STATUS_PUBLISHED,
    ]);
    adminSiteVisit(['page_key' => 'mazmoon-detail', 'visitable_type' => Article::class, 'visitable_id' => $article->id]);
    adminSiteVisit(['page_key' => 'mazmoon-detail', 'visitable_type' => Article::class, 'visitable_id' => $article->id]);
    adminSiteVisit(['page_key' => 'mazmoon-detail', 'visitable_type' => Article::class, 'visitable_id' => 999999]);

    $response = $this->actingAs($admin)->getJson(route('admin.site-analytics.data', [
        'type' => 'articles', 'draw' => 1, 'start' => 0, 'length' => 10,
    ]))->assertOk();

    $rows = collect($response->json('data'));
    expect($rows->firstWhere('name', 'Analytics Article')['visit_count'])->toBe(2)
        ->and($rows->pluck('name'))->toContain('Deleted / Unavailable Content');
});

test('magazine author and category modes use the generic analytics registry', function () {
    $admin = analyticsAdmin(['site-analytics.view']);
    $magazine = Magazine::query()->forceCreate(['language' => 'ur', 'title' => 'Registry Magazine', 'isFree' => true, 'isActive' => true, 'status' => Magazine::STATUS_PUBLISHED]);
    $author = Author::query()->forceCreate(['name' => 'Registry Author', 'isActive' => true]);
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Registry Category', 'isActive' => true]);

    foreach ([
        'magazines' => [$magazine, 'Registry Magazine'],
        'authors' => [$author, 'Registry Author'],
        'categories' => [$category, 'Registry Category'],
    ] as $type => [$model, $expectedName]) {
        adminSiteVisit(['visitable_type' => $model->getMorphClass(), 'visitable_id' => $model->getKey()]);
        $response = $this->actingAs($admin)->getJson(route('admin.site-analytics.data', [
            'type' => $type, 'draw' => 1, 'start' => 0, 'length' => 10,
        ]))->assertOk();
        expect($response->json('data.0.name'))->toBe($expectedName);
    }
});

test('inclusive date filters and invalid ranges are handled', function () {
    $admin = analyticsAdmin(['site-analytics.view']);
    adminSiteVisit(['started_at' => '2026-09-01 00:00:00']);
    adminSiteVisit(['started_at' => '2026-09-12 23:59:59']);
    adminSiteVisit(['started_at' => '2026-09-13 00:00:00']);

    $response = $this->actingAs($admin)->getJson(route('admin.site-analytics.data', [
        'type' => 'pages', 'from_date' => '2026-09-01', 'to_date' => '2026-09-12',
        'draw' => 1, 'start' => 0, 'length' => 10,
    ]))->assertOk();

    expect($response->json('data.0.visit_count'))->toBe(2);

    $this->get(route('admin.site-analytics.index', [
        'from_date' => '2026-09-12', 'to_date' => '2026-09-01',
    ]))->assertSessionHasErrors('to_date');
});

test('detail report scopes visits and calculates deterministic non-null location summaries', function () {
    $admin = analyticsAdmin(['site-analytics.view']);
    adminSiteVisit();
    adminSiteVisit();
    adminSiteVisit(['country' => 'United States', 'city' => null, 'region' => null]);
    adminSiteVisit(['page_key' => 'about', 'country' => 'Canada']);

    $this->actingAs($admin)->get(route('admin.site-analytics.detail', ['type' => 'pages', 'identifier' => 'home']))
        ->assertOk()->assertSee('Pakistan')->assertSee('Karachi')->assertSee('Sindh')->assertSee('2 Visits');

    $data = $this->getJson(route('admin.site-analytics.detail.data', [
        'type' => 'pages', 'identifier' => 'home', 'draw' => 1, 'start' => 0, 'length' => 10,
    ]))->assertOk();
    expect($data->json('recordsTotal'))->toBe(3)
        ->and($data->json('data.0.duration'))->toBe('2 min 18 sec');
});

test('invalid analytics types and arbitrary model classes are rejected', function () {
    $admin = analyticsAdmin(['site-analytics.view']);

    $this->actingAs($admin)->get(route('admin.site-analytics.index', ['type' => 'App\\Models\\User']))
        ->assertSessionHasErrors('type');
    $this->get(route('admin.site-analytics.detail', ['type' => 'users', 'identifier' => '1']))
        ->assertSessionHasErrors('type');
});

test('summary and detail exports retain filters and exclude security tokens', function () {
    Carbon::setTestNow('2026-09-12 16:00:00');
    $admin = analyticsAdmin(['site-analytics.view', 'site-analytics.export']);
    adminSiteVisit(['started_at' => '2026-09-12 12:00:00']);
    adminSiteVisit(['started_at' => '2026-08-01 12:00:00']);
    Excel::fake();

    $this->actingAs($admin)->get(route('admin.site-analytics.export', [
        'type' => 'pages', 'from_date' => '2026-09-01',
    ]))->assertOk();
    Excel::assertDownloaded('site-analytics-pages-2026-09-12-160000.xlsx', fn (SiteAnalyticsSummaryExport $export): bool => $export->collection()->first()[2] === 1);

    $this->get(route('admin.site-analytics.detail.export', [
        'type' => 'pages', 'identifier' => 'home', 'from_date' => '2026-09-01',
    ]))->assertOk();
    Excel::assertDownloaded('site-analytics-pages-home-2026-09-12-160000.xlsx', function (SiteAnalyticsDetailExport $export): bool {
        return $export->headings() === ['Visitor Type', 'Country', 'City', 'Region', 'Device', 'Browser', 'Browser Version', 'OS', 'OS Version', 'Duration Seconds', 'Time Spent', 'Started At']
            && $export->collection()->count() === 1;
    });
});

test('export permission is enforced independently from view permission', function () {
    $viewer = analyticsAdmin(['site-analytics.view']);

    $this->actingAs($viewer)->get(route('admin.site-analytics.export'))->assertForbidden();
});
