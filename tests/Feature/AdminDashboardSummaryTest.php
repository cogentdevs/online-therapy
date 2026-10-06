<?php

use App\Models\AdRequest;
use App\Models\Article;
use App\Models\Currency;
use App\Models\Magazine;
use App\Models\SubscriptionProduct;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\Authorization\AdminUserPermissionService;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();

    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

function dashboardAdmin(
    array $permissions,
    string $mode = AdminUserPermissionService::MODE_ROLE,
    array $directPermissions = [],
    ?User $parentAdmin = null,
): User {
    $role = Role::findOrCreate('dashboard-role-'.str()->random(8), 'web');
    $role->syncPermissions(array_values(array_unique(['admin.access', 'dashboard.view', ...$permissions])));
    $user = User::factory()->create([
        'is_active' => true,
        'permission_mode' => $mode,
        'parent_admin_id' => $parentAdmin?->id,
    ]);
    $user->assignRole($role);

    if ($mode === AdminUserPermissionService::MODE_CUSTOM) {
        $user->syncPermissions($directPermissions);
    }

    return $user;
}

test('super admin sees accurate article magazine and managed admin user summaries', function () {
    Article::query()->create(['title' => 'Article Draft', 'status' => Article::STATUS_DRAFT]);
    Article::query()->create(['title' => 'Article Published One', 'status' => Article::STATUS_PUBLISHED]);
    Article::query()->create(['title' => 'Article Published Two', 'status' => Article::STATUS_PUBLISHED]);
    Magazine::query()->create(['title' => 'Magazine Draft', 'status' => Magazine::STATUS_DRAFT]);
    Magazine::query()->create(['title' => 'Magazine Published', 'status' => Magazine::STATUS_PUBLISHED]);

    dashboardAdmin(['articles.view']);
    $frontendUser = User::factory()->create();
    $frontendUser->assignRole(Role::findOrCreate('user', 'web'));
    $consultant = User::factory()->create();
    $consultant->assignRole(Role::findOrCreate('consultant', 'web'));
    $roleWithoutAdminAccess = Role::findOrCreate('dashboard-non-admin', 'web');
    $roleWithoutAdminAccess->syncPermissions(['articles.view']);
    User::factory()->create()->assignRole($roleWithoutAdminAccess);

    $response = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'));

    $response->assertSuccessful()
        ->assertSee('data-dashboard-module="articles"', false)
        ->assertSee('data-dashboard-total="3"', false)
        ->assertSee('data-dashboard-published="2"', false)
        ->assertSee('data-dashboard-module="magazines"', false)
        ->assertSee('data-dashboard-total="2"', false)
        ->assertSee('data-dashboard-published="1"', false)
        ->assertSee('data-dashboard-module="users"', false)
        ->assertSee(route('admin.article.index'))
        ->assertSee(route('admin.magazine.index'))
        ->assertSee(route('admin.users.index'));

    expect((int) data_get($response->viewData('summaries'), 'users.total'))->toBe(2);
});

test('full role admin sees only cards allowed by effective permissions', function (string $permission, string $visibleModule, array $hiddenModules) {
    $admin = dashboardAdmin([$permission]);
    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertSuccessful()->assertSee('data-dashboard-module="'.$visibleModule.'"', false);

    foreach ($hiddenModules as $hiddenModule) {
        $response->assertDontSee('data-dashboard-module="'.$hiddenModule.'"', false);
    }
})->with([
    'articles' => ['articles.view', 'articles', ['magazines', 'users']],
    'magazines' => ['magazines.view', 'magazines', ['articles', 'users']],
    'users' => ['users.view', 'users', ['articles', 'magazines']],
]);

test('limited permission mode controls cards and prevents unauthorized counter queries', function () {
    $admin = dashboardAdmin(
        ['articles.view', 'magazines.view', 'users.view'],
        AdminUserPermissionService::MODE_CUSTOM,
        ['dashboard.view', 'articles.view'],
    );
    $queries = [];
    DB::listen(function ($query) use (&$queries): void {
        $queries[] = strtolower($query->sql);
    });

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));
    $executedSql = implode("\n", $queries);

    $response->assertSuccessful()
        ->assertSee('data-dashboard-module="articles"', false)
        ->assertDontSee('data-dashboard-module="magazines"', false)
        ->assertDontSee('data-dashboard-module="users"', false);

    expect($executedSql)->toContain('from "articles"')
        ->and($executedSql)->not->toContain('from "magazines"')
        ->and($executedSql)->not->toContain('count(*) as aggregate from "users"');
});

test('Custom Admin dashboard summaries match descendant ownership and Admin hierarchy scope', function () {
    $permissions = ['articles.view', 'magazines.view', 'users.view'];
    $adminA = dashboardAdmin($permissions, parentAdmin: $this->superAdmin);
    $adminB = dashboardAdmin($permissions, parentAdmin: $adminA);
    $adminC = dashboardAdmin($permissions, parentAdmin: $adminB);
    $adminX = dashboardAdmin($permissions, parentAdmin: $this->superAdmin);

    Article::query()->forceCreate(['title' => 'A Article', 'status' => Article::STATUS_PUBLISHED, 'owner_admin_id' => $adminA->id]);
    Article::query()->forceCreate(['title' => 'B Article', 'status' => Article::STATUS_DRAFT, 'owner_admin_id' => $adminB->id]);
    Article::query()->forceCreate(['title' => 'C Article', 'status' => Article::STATUS_PUBLISHED, 'owner_admin_id' => $adminC->id]);
    Article::query()->forceCreate(['title' => 'X Article', 'status' => Article::STATUS_PUBLISHED, 'owner_admin_id' => $adminX->id]);
    Article::query()->forceCreate(['title' => 'Unowned Article', 'status' => Article::STATUS_DRAFT, 'owner_admin_id' => null]);

    Magazine::query()->forceCreate(['title' => 'A Magazine', 'status' => Magazine::STATUS_DRAFT, 'owner_admin_id' => $adminA->id]);
    Magazine::query()->forceCreate(['title' => 'B Magazine', 'status' => Magazine::STATUS_PUBLISHED, 'owner_admin_id' => $adminB->id]);
    Magazine::query()->forceCreate(['title' => 'C Magazine', 'status' => Magazine::STATUS_PUBLISHED, 'owner_admin_id' => $adminC->id]);
    Magazine::query()->forceCreate(['title' => 'X Magazine', 'status' => Magazine::STATUS_DRAFT, 'owner_admin_id' => $adminX->id]);
    Magazine::query()->forceCreate(['title' => 'Unowned Magazine', 'status' => Magazine::STATUS_PUBLISHED, 'owner_admin_id' => null]);

    $superSummaries = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'))->viewData('summaries');
    $adminASummaries = $this->actingAs($adminA)->get(route('admin.dashboard'))->viewData('summaries');
    $adminBSummaries = $this->actingAs($adminB)->get(route('admin.dashboard'))->viewData('summaries');
    $adminCSummaries = $this->actingAs($adminC)->get(route('admin.dashboard'))->viewData('summaries');

    expect($superSummaries)->toMatchArray([
        'articles' => ['total' => 5, 'published' => 3],
        'magazines' => ['total' => 5, 'published' => 3],
        'users' => ['total' => 5],
    ])->and($adminASummaries)->toMatchArray([
        'articles' => ['total' => 3, 'published' => 2],
        'magazines' => ['total' => 3, 'published' => 2],
        'users' => ['total' => 3],
    ])->and($adminBSummaries)->toMatchArray([
        'articles' => ['total' => 2, 'published' => 1],
        'magazines' => ['total' => 2, 'published' => 2],
        'users' => ['total' => 2],
    ])->and($adminCSummaries)->toMatchArray([
        'articles' => ['total' => 1, 'published' => 1],
        'magazines' => ['total' => 1, 'published' => 1],
        'users' => ['total' => 1],
    ]);
});

test('subscription trend is an explicit empty state without fabricated graph data', function () {
    $this->travelTo('2026-09-24 12:00:00');
    $response = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'));

    $response->assertSuccessful()
        ->assertSee('Subscription Trend')
        ->assertSee('Last 30 Days')
        ->assertSee('No subscription data available yet.')
        ->assertSee('Subscription trends will appear here once user subscription activity begins.')
        ->assertSee('data-subscription-trend-empty', false);

    expect($response->viewData('subscriptionTrendLabels'))->toHaveCount(30)
        ->and($response->viewData('subscriptionTrendLabels')[0])->toBe('26 Aug')
        ->and($response->viewData('subscriptionTrendLabels')[29])->toBe('24 Sep')
        ->and($response->viewData('subscriptionTrendData'))->toBe(array_fill(0, 30, 0));
});

test('subscription trend counts active plan and membership purchases by creation date and zero fills the last thirty days', function () {
    $this->travelTo('2026-09-24 12:00:00');
    $currency = Currency::query()->create([
        'name' => 'Pakistani Rupee',
        'code' => 'PKR',
        'symbol' => 'Rs',
        'isActive' => true,
    ]);
    $plan = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_PLAN,
        'name' => 'Plan',
        'currency_id' => $currency->id,
        'price' => 500,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    $membership = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_MEMBERSHIP,
        'name' => 'Membership',
        'currency_id' => $currency->id,
        'price' => 1000,
        'duration_value' => 1,
        'duration_unit' => 'year',
        'isActive' => true,
    ]);
    $customer = User::factory()->create();

    foreach ([
        [$plan, 'active', '2026-09-24 08:00:00'],
        [$membership, 'active', '2026-09-24 10:00:00'],
        [$plan, 'active', '2026-09-22 10:00:00'],
        [$plan, 'expired', '2026-09-23 10:00:00'],
        [$membership, 'active', '2026-08-25 23:59:59'],
    ] as [$product, $status, $createdAt]) {
        UserSubscription::query()->forceCreate([
            'user_id' => $customer->id,
            'subscription_product_id' => $product->id,
            'product_for' => $product->product_for,
            'product_name' => $product->name,
            'currency_id' => $currency->id,
            'price' => $product->price,
            'discount' => 0,
            'total' => $product->price,
            'payment_method' => 'card',
            'status' => $status,
            'is_active' => $status === 'active',
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    $response = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'));
    $labels = $response->viewData('subscriptionTrendLabels');
    $data = $response->viewData('subscriptionTrendData');

    $response->assertSuccessful()
        ->assertSee('data-subscription-trend', false)
        ->assertDontSee('No subscription data available yet.');
    expect($labels)->toHaveCount(30)
        ->and($data)->toHaveCount(30)
        ->and($labels[0])->toBe('26 Aug')
        ->and($labels[27])->toBe('22 Sep')
        ->and($labels[28])->toBe('23 Sep')
        ->and($labels[29])->toBe('24 Sep')
        ->and($data[array_search('22 Sep', $labels, true)])->toBe(1)
        ->and($data[array_search('23 Sep', $labels, true)])->toBe(0)
        ->and($data[array_search('24 Sep', $labels, true)])->toBe(2)
        ->and(array_sum($data))->toBe(3);
});

test('advertising request card counts only pending parent requests and links to their list', function () {
    $advertiser = User::factory()->create();

    foreach (['pending', 'pending', 'quote_sent', 'confirmed', 'published', 'rejected', 'cancelled'] as $status) {
        $request = AdRequest::query()->create([
            'user_id' => $advertiser->id,
            'name' => 'Advertiser',
            'email' => 'advertiser@example.test',
            'phone' => '03001234567',
            'from_date' => '2026-10-01',
            'to_date' => '2026-10-03',
        ]);
        $request->forceFill(['status' => $status])->save();
    }

    $response = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'));

    $response->assertSuccessful()
        ->assertSee('data-dashboard-module="advertising-requests"', false)
        ->assertSee('data-dashboard-pending="2"', false)
        ->assertSee(route('admin.advertising-requests.index'));

    expect(data_get($response->viewData('summaries'), 'advertising_requests.pending'))->toBe(2);
});

test('advertising request card and count query follow the existing view permission', function () {
    $permitted = dashboardAdmin(['ad-requests.view']);
    $this->actingAs($permitted)->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('data-dashboard-module="advertising-requests"', false)
        ->assertSee(route('admin.advertising-requests.index'));

    $unpermitted = dashboardAdmin([]);
    $queries = [];
    DB::listen(function ($query) use (&$queries): void {
        $queries[] = strtolower($query->sql);
    });

    $response = $this->actingAs($unpermitted)->get(route('admin.dashboard'));
    $response->assertSuccessful()
        ->assertDontSee('data-dashboard-module="advertising-requests"', false)
        ->assertDontSee(route('admin.advertising-requests.index'));

    expect($response->viewData('summaries'))->not->toHaveKey('advertising_requests')
        ->and(implode("\n", $queries))->not->toContain('from "ad_requests"');
});
