<?php

use App\Exports\ActivityLogsExport;
use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Category;
use App\Models\Language;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Barryvdh\DomPDF\PDF;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();

    $this->superAdmin = User::factory()->create(['name' => 'Audit Super Admin', 'is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
    $this->language = Language::query()->create(['name' => 'English', 'code' => 'en', 'is_active' => true]);
});

function activityAdmin(array $permissions): User
{
    $role = Role::findOrCreate('audit-editor-'.str()->random(6), 'web');
    $role->syncPermissions(array_unique(['admin.access', ...$permissions]));
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole($role);

    return $user;
}

function createActivityCategory(User $actor, string $name = 'Technology'): Category
{
    test()->actingAs($actor)
        ->post(route('admin.categories.store'), [
            'language' => 'en',
            'entries' => [['name' => $name]],
        ])
        ->assertRedirect(route('admin.categories.index'));

    return Category::query()->where('name', $name)->firstOrFail();
}

test('super admin and custom admin successful creates store actor and role snapshots', function () {
    $category = createActivityCategory($this->superAdmin, 'Super Category');
    $superLog = ActivityLog::query()->where('subject_id', $category->id)->where('module', 'categories')->firstOrFail();

    expect($superLog->action)->toBe('created')
        ->and($superLog->user_id)->toBe($this->superAdmin->id)
        ->and($superLog->user_name)->toBe('Audit Super Admin')
        ->and($superLog->role_name)->toBe('super-admin')
        ->and($superLog->old_values)->toBeNull()
        ->and($superLog->new_values['name'])->toBe('Super Category');

    $admin = activityAdmin(['categories.create']);
    $customCategory = createActivityCategory($admin, 'Custom Category');
    $customLog = ActivityLog::query()->where('subject_id', $customCategory->id)->where('module', 'categories')->firstOrFail();

    expect($customLog->user_id)->toBe($admin->id)
        ->and($customLog->role_name)->toBe($admin->roles()->value('name'));
});

test('updates status changes deletes and publishing capture meaningful values', function () {
    $category = createActivityCategory($this->superAdmin);

    $this->post(route('admin.categories.update', ['id' => $category->id]), [
        'language' => 'en',
        'name' => 'Digital Technology',
        'isActive' => '1',
    ])->assertRedirect(route('admin.categories.index'));

    $updateLog = ActivityLog::query()->where('subject_id', $category->id)->where('action', 'updated')->firstOrFail();
    expect($updateLog->old_values['name'])->toBe('Technology')
        ->and($updateLog->new_values['name'])->toBe('Digital Technology');

    $this->post(route('admin.categories.update', ['id' => $category->id]), [
        'language' => 'en',
        'name' => 'Digital Technology',
        'isActive' => '0',
    ])->assertRedirect(route('admin.categories.index'));

    $statusLog = ActivityLog::query()->where('subject_id', $category->id)->where('action', 'status_changed')->firstOrFail();
    expect($statusLog->old_values)->toMatchArray(['isActive' => true])
        ->and($statusLog->new_values)->toMatchArray(['isActive' => false]);

    $article = Article::query()->create([
        'language' => 'en',
        'title' => 'Publishing Trends',
        'article' => '<p>Publishable content.</p>',
        'isActive' => true,
        'status' => Article::STATUS_DRAFT,
    ]);
    $this->post(route('admin.article.publish', ['id' => $article->id]))->assertSessionHas('status');
    expect(ActivityLog::query()->where('subject_id', $article->id)->where('module', 'articles')->where('action', 'published')->exists())->toBeTrue();

    $this->delete(route('admin.categories.destroy', ['id' => $category->id]))->assertRedirect(route('admin.categories.index'));
    $deleteLog = ActivityLog::query()->where('subject_id', $category->id)->where('module', 'categories')->where('action', 'deleted')->firstOrFail();
    expect($deleteLog->old_values['name'])->toBe('Digital Technology')
        ->and($deleteLog->new_values)->toBeNull();
});

test('failed validation and unauthorized requests do not create successful logs', function () {
    $this->actingAs($this->superAdmin)
        ->post(route('admin.categories.store'), ['language' => 'invalid', 'entries' => []])
        ->assertSessionHasErrors();
    expect(ActivityLog::query()->count())->toBe(0);

    $admin = activityAdmin(['categories.view']);
    $this->actingAs($admin)
        ->post(route('admin.categories.store'), [
            'language' => 'en',
            'entries' => [['name' => 'Forbidden Category']],
        ])
        ->assertForbidden();

    expect(ActivityLog::query()->count())->toBe(0);
});

test('admin user logs exclude passwords and capture role permission mode and status changes', function () {
    $role = Role::findOrCreate('managed-auditor', 'web');
    $role->syncPermissions(['admin.access', 'dashboard.view']);

    $this->actingAs($this->superAdmin)
        ->post(route('admin.users.store'), [
            'name' => 'Managed Auditor',
            'email' => 'managed-auditor@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $role->id,
            'permission_mode' => 'role',
            'permissions' => [],
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.users.index'));

    $log = ActivityLog::query()->where('module', 'users')->where('action', 'created')->firstOrFail();
    $encodedValues = json_encode($log->new_values);
    expect($encodedValues)->not->toContain('password')
        ->and($encodedValues)->not->toContain('password123')
        ->and($log->new_values)->toMatchArray(['role' => 'managed-auditor', 'permission_mode' => 'role']);
});

test('role permission changes are logged with old and new permission snapshots', function () {
    $this->actingAs($this->superAdmin)
        ->post(route('admin.roles.store'), [
            'name' => 'Loggable Role',
            'permissions' => ['categories.view'],
        ])->assertRedirect(route('admin.roles.index'));

    $role = Role::query()->where('name', 'Loggable Role')->firstOrFail();
    $this->post(route('admin.roles.update', ['id' => $role->id]), [
        'name' => 'Loggable Role',
        'permissions' => ['categories.view', 'categories.edit'],
    ])->assertRedirect(route('admin.roles.index'));

    $log = ActivityLog::query()->where('module', 'roles')->where('action', 'permissions_changed')->firstOrFail();
    expect($log->old_values['permissions'])->toContain('categories.view')
        ->and($log->old_values['permissions'])->not->toContain('categories.edit')
        ->and($log->new_values['permissions'])->toContain('categories.edit');
});

test('deleted admin nulls user id but preserves historical snapshots', function () {
    $admin = activityAdmin(['categories.create']);
    createActivityCategory($admin, 'Historical Category');
    $log = ActivityLog::query()->where('module', 'categories')->firstOrFail();
    $snapshotName = $log->user_name;
    $snapshotRole = $log->role_name;

    $admin->delete();
    $log->refresh();

    expect($log->user_id)->toBeNull()
        ->and($log->user_name)->toBe($snapshotName)
        ->and($log->role_name)->toBe($snapshotRole);
});

test('activity pages and sidebar are strictly super admin only', function () {
    $activityLog = ActivityLog::query()->create(['module' => 'categories', 'action' => 'created', 'description' => 'Created category.']);

    $this->actingAs($this->superAdmin)
        ->get(route('admin.activity-logs.index'))
        ->assertSuccessful()
        ->assertSee('Activity Logs');
    $this->get(route('admin.activity-logs.show', ['id' => $activityLog->id]))
        ->assertSuccessful()
        ->assertSee('Immutable Admin action record')
        ->assertSee('Created category.');
    $this->get(route('admin.activity-logs.export.pdf'))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');

    $admin = activityAdmin(['dashboard.view']);
    $this->actingAs($admin);
    foreach ([
        route('admin.activity-logs.index'),
        route('admin.activity-logs.show', ['id' => $activityLog->id]),
        route('admin.activity-logs.export.excel'),
        route('admin.activity-logs.export.pdf'),
    ] as $protectedUrl) {
        $this->get($protectedUrl)->assertForbidden();
    }
    $this->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertDontSee(route('admin.activity-logs.index'));
});

test('combined date user role module and action filters return only matching logs', function () {
    $matching = ActivityLog::query()->create([
        'user_id' => $this->superAdmin->id,
        'user_name' => $this->superAdmin->name,
        'role_name' => 'super-admin',
        'module' => 'magazines',
        'action' => 'updated',
        'description' => 'Matching activity marker',
        'created_at' => Carbon::parse('2026-08-15 12:00:00'),
    ]);
    ActivityLog::query()->create([
        'user_id' => $this->superAdmin->id,
        'user_name' => $this->superAdmin->name,
        'role_name' => 'super-admin',
        'module' => 'articles',
        'action' => 'updated',
        'description' => 'Excluded activity marker',
        'created_at' => Carbon::parse('2026-08-15 12:00:00'),
    ]);

    $filters = ['from_date' => '2026-08-01', 'to_date' => '2026-08-31', 'user_id' => $this->superAdmin->id, 'role' => 'super-admin', 'module' => 'magazines', 'action' => 'updated'];

    foreach ([
        ['from_date' => '2026-08-01'],
        ['to_date' => '2026-08-31'],
        ['user_id' => $this->superAdmin->id],
        ['role' => 'super-admin'],
        ['module' => 'magazines'],
        ['action' => 'updated'],
    ] as $individualFilter) {
        $this->actingAs($this->superAdmin)
            ->get(route('admin.activity-logs.index', $individualFilter))
            ->assertSuccessful()
            ->assertSee($matching->description);
    }

    $this->actingAs($this->superAdmin)
        ->get(route('admin.activity-logs.index', $filters))
        ->assertSuccessful()
        ->assertSee($matching->description)
        ->assertDontSee('Excluded activity marker');
});

test('excel and pdf exports receive the currently filtered records', function () {
    Carbon::setTestNow('2026-08-28 10:30:00');
    $matching = ActivityLog::query()->create(['module' => 'magazines', 'action' => 'updated', 'description' => 'Export matching']);
    ActivityLog::query()->create(['module' => 'articles', 'action' => 'updated', 'description' => 'Export excluded']);
    $filters = ['module' => 'magazines', 'action' => 'updated'];

    Excel::fake();
    $this->actingAs($this->superAdmin)
        ->get(route('admin.activity-logs.export.excel', $filters))
        ->assertSuccessful();
    Excel::assertDownloaded('activity-logs-2026-08-28-103000.xlsx', function (ActivityLogsExport $export) use ($matching): bool {
        return $export->query()->pluck('id')->all() === [$matching->id];
    });

    $pdfDocument = Mockery::mock(PDF::class);
    $pdfDocument->shouldReceive('loadView')->once()->with(
        'admin.activity-logs.activity-logs-pdf',
        Mockery::on(fn (array $data): bool => $data['activityLogs']->pluck('id')->all() === [$matching->id]),
    )->andReturnSelf();
    $pdfDocument->shouldReceive('setPaper')->once()->with('a4', 'landscape')->andReturnSelf();
    $pdfDocument->shouldReceive('download')->once()->andReturn(response('filtered-pdf'));
    app()->instance('dompdf.wrapper', $pdfDocument);

    $this->get(route('admin.activity-logs.export.pdf', $filters))
        ->assertSuccessful()
        ->assertSee('filtered-pdf');
});
