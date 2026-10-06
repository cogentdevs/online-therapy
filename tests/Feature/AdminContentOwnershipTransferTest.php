<?php

use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Magazine;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();

    $this->superAdmin = User::factory()->create(['name' => 'Transfer Root Admin', 'is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

function createOwnershipTransferRole(string $name, array $permissions = []): Role
{
    $role = Role::findOrCreate($name, 'web');
    $role->syncPermissions(array_values(array_unique(['admin.access', ...$permissions])));

    return $role;
}

function createOwnershipTransferAdmin(string $name, Role $role, User $parent, bool $active = true): User
{
    $admin = User::factory()->create([
        'name' => $name,
        'is_active' => $active,
        'parent_admin_id' => $parent->id,
    ]);
    $admin->syncRoles([$role]);

    return $admin;
}

function ownershipTransferPayload(User $newOwner, array $contentTypes, bool $deactivate = false): array
{
    return [
        'new_owner_id' => $newOwner->id,
        'content_types' => $contentTypes,
        'deactivate_source' => $deactivate ? '1' : '0',
        'confirm_transfer' => '1',
    ];
}

test('transfer page shows exact ownership counts and only eligible New Owners', function () {
    $managerRole = createOwnershipTransferRole('transfer-preview-manager', ['users.view', 'users.edit']);
    $adminRole = createOwnershipTransferRole('transfer-preview-admin');
    $actor = createOwnershipTransferAdmin('Preview Parent Admin', $managerRole, $this->superAdmin);
    $source = createOwnershipTransferAdmin('Preview Current Owner', $adminRole, $actor);
    $newOwner = createOwnershipTransferAdmin('Preview New Owner', $adminRole, $actor);
    $unrelated = createOwnershipTransferAdmin('Preview Unrelated Admin', $adminRole, $this->superAdmin);
    createOwnershipTransferAdmin('Preview Direct Child Admin', $adminRole, $source);
    $frontendChild = User::factory()->create(['is_active' => true, 'parent_admin_id' => $source->id]);
    $frontendChild->assignRole(Role::findOrCreate('consultant', 'web'));

    Magazine::query()->forceCreate(['title' => 'Direct Preview Magazine', 'owner_admin_id' => $source->id]);
    Article::query()->forceCreate(['title' => 'Direct Preview Article', 'owner_admin_id' => $source->id]);
    Magazine::query()->forceCreate(['title' => 'Unrelated Preview Magazine', 'owner_admin_id' => $unrelated->id]);

    $this->actingAs($actor)
        ->get(route('admin.users.transfer-ownership', ['id' => $source->id]))
        ->assertSuccessful()
        ->assertViewHas('ownedContentCounts.child_admins', 1)
        ->assertSee('Owned Magazines')
        ->assertSee('Owned Articles')
        ->assertSee('Child Admins')
        ->assertSee($newOwner->name)
        ->assertDontSee($unrelated->name)
        ->assertSee('1 currently owned')
        ->assertSee('1 directly assigned');
});

test('Parent Admin transfers both exact Magazine and Article ownership to a sibling Admin', function () {
    $managerRole = createOwnershipTransferRole('transfer-sibling-manager', ['users.edit']);
    $adminRole = createOwnershipTransferRole('transfer-sibling-admin');
    $actor = createOwnershipTransferAdmin('Transfer Parent Admin', $managerRole, $this->superAdmin);
    $source = createOwnershipTransferAdmin('Muzammil', $adminRole, $actor);
    $newOwner = createOwnershipTransferAdmin('Hammad', $adminRole, $actor);
    $descendant = createOwnershipTransferAdmin('Source Child Admin', $adminRole, $source);
    $publishedBy = createOwnershipTransferAdmin('Historical Publisher', $adminRole, $actor);
    $historicalUpdatedAt = now()->subMonth()->startOfSecond();

    $magazine = Magazine::query()->forceCreate([
        'title' => 'Transferred Magazine', 'owner_admin_id' => $source->id,
        'created_by' => $source->id, 'updated_by' => $source->id,
        'published_by' => $publishedBy->id, 'published_at' => now()->subMonths(2),
        'updated_at' => $historicalUpdatedAt,
    ]);
    $article = Article::query()->forceCreate([
        'title' => 'Transferred Article', 'article' => 'Body', 'owner_admin_id' => $source->id,
        'created_by' => $source->id, 'updated_by' => $source->id,
        'published_by' => $publishedBy->id, 'published_at' => now()->subMonths(2),
        'updated_at' => $historicalUpdatedAt,
    ]);
    $descendantMagazine = Magazine::query()->forceCreate([
        'title' => 'Descendant Owned Magazine', 'owner_admin_id' => $descendant->id,
    ]);

    $this->actingAs($actor)
        ->post(route('admin.users.transfer-ownership.store', ['id' => $source->id]), ownershipTransferPayload($newOwner, ['magazines', 'articles']))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('status', 'Ownership transferred successfully. Magazines transferred: 1; Articles transferred: 1; Child Admins reassigned: 0; Current Owner deactivated: No.');

    $magazine->refresh();
    $article->refresh();

    expect($magazine->owner_admin_id)->toBe($newOwner->id)
        ->and($article->owner_admin_id)->toBe($newOwner->id)
        ->and($descendantMagazine->refresh()->owner_admin_id)->toBe($descendant->id)
        ->and($magazine->created_by)->toBe($source->id)
        ->and($article->created_by)->toBe($source->id)
        ->and($magazine->updated_by)->toBe($source->id)
        ->and($article->updated_by)->toBe($source->id)
        ->and($magazine->published_by)->toBe($publishedBy->id)
        ->and($article->published_by)->toBe($publishedBy->id)
        ->and($magazine->updated_at->equalTo($historicalUpdatedAt))->toBeTrue()
        ->and($article->updated_at->equalTo($historicalUpdatedAt))->toBeTrue()
        ->and($source->refresh()->is_active)->toBeTrue();
});

test('selected content type only transfers that type and zero counts are safe', function () {
    $managerRole = createOwnershipTransferRole('transfer-selected-manager', ['users.edit']);
    $adminRole = createOwnershipTransferRole('transfer-selected-admin');
    $actor = createOwnershipTransferAdmin('Selected Parent Admin', $managerRole, $this->superAdmin);
    $source = createOwnershipTransferAdmin('Selected Current Owner', $adminRole, $actor);
    $newOwner = createOwnershipTransferAdmin('Selected New Owner', $adminRole, $actor);
    $article = Article::query()->forceCreate(['title' => 'Article Kept', 'article' => 'Body', 'owner_admin_id' => $source->id]);

    $this->actingAs($actor)
        ->post(route('admin.users.transfer-ownership.store', ['id' => $source->id]), ownershipTransferPayload($newOwner, ['magazines']))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('status', 'Ownership transferred successfully. Magazines transferred: 0; Articles transferred: 0; Child Admins reassigned: 0; Current Owner deactivated: No.');

    expect($article->refresh()->owner_admin_id)->toBe($source->id);
});

test('Parent Admin can take over a manageable Descendant Admin content', function () {
    $managerRole = createOwnershipTransferRole('transfer-takeover-manager', ['users.edit']);
    $adminRole = createOwnershipTransferRole('transfer-takeover-admin');
    $actor = createOwnershipTransferAdmin('Takeover Parent Admin', $managerRole, $this->superAdmin);
    $source = createOwnershipTransferAdmin('Takeover Child Admin', $adminRole, $actor);
    $magazine = Magazine::query()->forceCreate(['title' => 'Takeover Magazine', 'owner_admin_id' => $source->id]);

    $this->actingAs($actor)
        ->post(route('admin.users.transfer-ownership.store', ['id' => $source->id]), ownershipTransferPayload($actor, ['magazines']))
        ->assertRedirect(route('admin.users.index'));

    expect($magazine->refresh()->owner_admin_id)->toBe($actor->id);
});

test('Custom Admin cannot transfer to a sibling or use an unrelated Source Admin', function () {
    $managerRole = createOwnershipTransferRole('transfer-scope-manager', ['users.edit']);
    $adminRole = createOwnershipTransferRole('transfer-scope-admin');
    $parent = createOwnershipTransferAdmin('Scope Parent Admin', $managerRole, $this->superAdmin);
    $source = createOwnershipTransferAdmin('Scope Current Owner', $managerRole, $parent);
    $sibling = createOwnershipTransferAdmin('Scope Sibling Admin', $adminRole, $parent);
    $unrelated = createOwnershipTransferAdmin('Scope Unrelated Admin', $adminRole, $this->superAdmin);
    $magazine = Magazine::query()->forceCreate(['title' => 'Scope Magazine', 'owner_admin_id' => $source->id]);

    $this->actingAs($source)
        ->post(route('admin.users.transfer-ownership.store', ['id' => $source->id]), ownershipTransferPayload($sibling, ['magazines']))
        ->assertSessionHasErrors('new_owner_id');
    $this->post(route('admin.users.transfer-ownership.store', ['id' => $unrelated->id]), ownershipTransferPayload($source, ['magazines']))
        ->assertForbidden();

    expect($magazine->refresh()->owner_admin_id)->toBe($source->id);
});

test('Super Admin can transfer globally but cannot use Super Admin as Source or destination', function () {
    $adminRole = createOwnershipTransferRole('transfer-global-admin');
    $source = createOwnershipTransferAdmin('Global Current Owner', $adminRole, $this->superAdmin);
    $newOwner = createOwnershipTransferAdmin('Global New Owner', $adminRole, $this->superAdmin);
    $magazine = Magazine::query()->forceCreate(['title' => 'Global Magazine', 'owner_admin_id' => $source->id]);

    $this->actingAs($this->superAdmin)
        ->post(route('admin.users.transfer-ownership.store', ['id' => $source->id]), ownershipTransferPayload($newOwner, ['magazines']))
        ->assertRedirect(route('admin.users.index'));
    $this->get(route('admin.users.transfer-ownership', ['id' => $this->superAdmin->id]))->assertNotFound();
    $this->post(route('admin.users.transfer-ownership.store', ['id' => $newOwner->id]), ownershipTransferPayload($this->superAdmin, ['magazines']))
        ->assertSessionHasErrors('new_owner_id');

    expect($magazine->refresh()->owner_admin_id)->toBe($newOwner->id);
});

test('frontend roles inactive destinations same owner and invalid content are rejected', function () {
    $managerRole = createOwnershipTransferRole('transfer-validation-manager', ['users.edit']);
    $adminRole = createOwnershipTransferRole('transfer-validation-admin');
    $actor = createOwnershipTransferAdmin('Validation Parent Admin', $managerRole, $this->superAdmin);
    $source = createOwnershipTransferAdmin('Validation Current Owner', $adminRole, $actor);
    $inactive = createOwnershipTransferAdmin('Inactive New Owner', $adminRole, $actor, false);
    $frontend = User::factory()->create(['is_active' => true, 'parent_admin_id' => null]);
    $frontend->assignRole(Role::findOrCreate('user', 'web'));

    $this->actingAs($actor)
        ->post(route('admin.users.transfer-ownership.store', ['id' => $source->id]), ownershipTransferPayload($inactive, ['magazines']))
        ->assertSessionHasErrors('new_owner_id');
    $this->post(route('admin.users.transfer-ownership.store', ['id' => $source->id]), ownershipTransferPayload($frontend, ['magazines']))
        ->assertSessionHasErrors('new_owner_id');
    $this->post(route('admin.users.transfer-ownership.store', ['id' => $source->id]), ownershipTransferPayload($source, ['magazines']))
        ->assertSessionHasErrors('new_owner_id');
    $this->post(route('admin.users.transfer-ownership.store', ['id' => $source->id]), ownershipTransferPayload($actor, ['authors']))
        ->assertSessionHasErrors('content_types.0');
    $this->post(route('admin.users.transfer-ownership.store', ['id' => $frontend->id]), ownershipTransferPayload($actor, ['magazines']))
        ->assertNotFound();
});

test('optional deactivation occurs after transfer and active Child Admin safety prevents the combined operation', function () {
    $managerRole = createOwnershipTransferRole('transfer-deactivate-manager', ['users.edit']);
    $adminRole = createOwnershipTransferRole('transfer-deactivate-admin');
    $actor = createOwnershipTransferAdmin('Deactivate Parent Admin', $managerRole, $this->superAdmin);
    $safeSource = createOwnershipTransferAdmin('Safe Current Owner', $adminRole, $actor);
    $newOwner = createOwnershipTransferAdmin('Deactivate New Owner', $adminRole, $actor);
    $safeMagazine = Magazine::query()->forceCreate(['title' => 'Safe Deactivate Magazine', 'owner_admin_id' => $safeSource->id]);

    $this->actingAs($actor)
        ->post(route('admin.users.transfer-ownership.store', ['id' => $safeSource->id]), ownershipTransferPayload($newOwner, ['magazines'], true))
        ->assertRedirect(route('admin.users.index'));

    expect($safeMagazine->refresh()->owner_admin_id)->toBe($newOwner->id)
        ->and($safeSource->refresh()->is_active)->toBeFalse();

    $unsafeSource = createOwnershipTransferAdmin('Unsafe Current Owner', $adminRole, $actor);
    createOwnershipTransferAdmin('Active Child Admin', $adminRole, $unsafeSource);
    $unsafeArticle = Article::query()->forceCreate(['title' => 'Unsafe Article', 'article' => 'Body', 'owner_admin_id' => $unsafeSource->id]);

    $this->post(route('admin.users.transfer-ownership.store', ['id' => $unsafeSource->id]), ownershipTransferPayload($newOwner, ['articles'], true))
        ->assertSessionHasErrors('deactivate_source');

    expect($unsafeArticle->refresh()->owner_admin_id)->toBe($unsafeSource->id)
        ->and($unsafeSource->refresh()->is_active)->toBeTrue();
});

test('Activity Log records transfer metadata without sensitive data', function () {
    $managerRole = createOwnershipTransferRole('transfer-log-manager', ['users.edit']);
    $adminRole = createOwnershipTransferRole('transfer-log-admin');
    $actor = createOwnershipTransferAdmin('Logging Parent Admin', $managerRole, $this->superAdmin);
    $source = createOwnershipTransferAdmin('Logging Current Owner', $adminRole, $actor);
    $newOwner = createOwnershipTransferAdmin('Logging New Owner', $adminRole, $actor);
    Magazine::query()->forceCreate(['title' => 'Logged Magazine', 'owner_admin_id' => $source->id]);

    $this->actingAs($actor)
        ->withHeader('User-Agent', 'Ownership Transfer Test')
        ->post(route('admin.users.transfer-ownership.store', ['id' => $source->id]), ownershipTransferPayload($newOwner, ['magazines']))
        ->assertRedirect(route('admin.users.index'));

    $log = ActivityLog::query()->where('action', 'ownership_transferred')->sole();

    expect($log->user_id)->toBe($actor->id)
        ->and($log->subject_id)->toBe($source->id)
        ->and($log->old_values['source_admin']['id'])->toBe($source->id)
        ->and($log->new_values['new_owner']['id'])->toBe($newOwner->id)
        ->and($log->new_values['magazines_transferred'])->toBe(1)
        ->and($log->new_values['articles_transferred'])->toBe(0)
        ->and($log->new_values['child_admins_reassigned'])->toBe(0)
        ->and($log->new_values['source_deactivated'])->toBeFalse()
        ->and(json_encode([$log->old_values, $log->new_values]))->not->toContain('password')
        ->and($log->user_agent)->toContain('Ownership Transfer Test');
});

test('required audit failure rolls back ownership and deactivation atomically', function () {
    $managerRole = createOwnershipTransferRole('transfer-rollback-manager', ['users.edit']);
    $adminRole = createOwnershipTransferRole('transfer-rollback-admin');
    $actor = createOwnershipTransferAdmin('Rollback Parent Admin', $managerRole, $this->superAdmin);
    $source = createOwnershipTransferAdmin('Rollback Current Owner', $adminRole, $actor);
    $newOwner = createOwnershipTransferAdmin('Rollback New Owner', $adminRole, $actor);
    $childAdmin = createOwnershipTransferAdmin('Rollback Child Admin', $adminRole, $source);
    $magazine = Magazine::query()->forceCreate(['title' => 'Rollback Magazine', 'owner_admin_id' => $source->id]);
    $article = Article::query()->forceCreate(['title' => 'Rollback Article', 'article' => 'Body', 'owner_admin_id' => $source->id]);

    $this->mock(ActivityLogService::class, function ($mock): void {
        $mock->shouldReceive('log')->once()->andReturnNull();
    });

    $this->actingAs($actor)
        ->post(route('admin.users.transfer-ownership.store', ['id' => $source->id]), ownershipTransferPayload($newOwner, ['magazines', 'articles', 'child_admins'], true))
        ->assertServerError();

    expect($magazine->refresh()->owner_admin_id)->toBe($source->id)
        ->and($article->refresh()->owner_admin_id)->toBe($source->id)
        ->and($childAdmin->refresh()->parent_admin_id)->toBe($source->id)
        ->and($source->refresh()->is_active)->toBeTrue();
});

test('Child Admin only transfer moves direct children and preserves nested hierarchy and child-owned content', function () {
    $adminRole = createOwnershipTransferRole('transfer-child-only-admin');
    $source = createOwnershipTransferAdmin('Child Current Owner', $adminRole, $this->superAdmin);
    $newOwner = createOwnershipTransferAdmin('Child New Owner', $adminRole, $this->superAdmin);
    $directChild = createOwnershipTransferAdmin('Direct Child Admin', $adminRole, $source);
    $nestedChild = createOwnershipTransferAdmin('Nested Child Admin', $adminRole, $directChild);
    $nestedGrandchild = createOwnershipTransferAdmin('Nested Grandchild Admin', $adminRole, $nestedChild);
    $directChild->forceFill(['created_by' => $source->id])->saveQuietly();
    $childMagazine = Magazine::query()->forceCreate([
        'title' => 'Child Owned Magazine',
        'owner_admin_id' => $directChild->id,
    ]);
    $childArticle = Article::query()->forceCreate([
        'title' => 'Child Owned Article',
        'article' => 'Body',
        'owner_admin_id' => $directChild->id,
    ]);

    $this->actingAs($this->superAdmin)
        ->post(route('admin.users.transfer-ownership.store', ['id' => $source->id]), ownershipTransferPayload($newOwner, ['child_admins']))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('status', 'Ownership transferred successfully. Magazines transferred: 0; Articles transferred: 0; Child Admins reassigned: 1; Current Owner deactivated: No.');

    expect($directChild->refresh()->parent_admin_id)->toBe($newOwner->id)
        ->and($directChild->created_by)->toBe($source->id)
        ->and($directChild->updated_by)->toBe($this->superAdmin->id)
        ->and($nestedChild->refresh()->parent_admin_id)->toBe($directChild->id)
        ->and($nestedGrandchild->refresh()->parent_admin_id)->toBe($nestedChild->id)
        ->and($childMagazine->refresh()->owner_admin_id)->toBe($directChild->id)
        ->and($childArticle->refresh()->owner_admin_id)->toBe($directChild->id);
});

test('full replacement transfers direct content and children atomically before deactivating Source Admin', function () {
    $adminRole = createOwnershipTransferRole('transfer-full-replacement-admin');
    $source = createOwnershipTransferAdmin('Replacement Current Owner', $adminRole, $this->superAdmin);
    $newOwner = createOwnershipTransferAdmin('Replacement New Owner', $adminRole, $this->superAdmin);
    $childOne = createOwnershipTransferAdmin('Replacement Child One', $adminRole, $source);
    $childTwo = createOwnershipTransferAdmin('Replacement Child Two', $adminRole, $source);
    $sourceMagazine = Magazine::query()->forceCreate([
        'title' => 'Source Direct Magazine', 'owner_admin_id' => $source->id, 'created_by' => $source->id,
    ]);
    $sourceArticle = Article::query()->forceCreate([
        'title' => 'Source Direct Article', 'article' => 'Body', 'owner_admin_id' => $source->id, 'created_by' => $source->id,
    ]);
    $childMagazine = Magazine::query()->forceCreate(['title' => 'Child Direct Magazine', 'owner_admin_id' => $childOne->id]);
    $childArticle = Article::query()->forceCreate(['title' => 'Child Direct Article', 'article' => 'Body', 'owner_admin_id' => $childTwo->id]);

    $this->actingAs($this->superAdmin)
        ->post(route('admin.users.transfer-ownership.store', ['id' => $source->id]), ownershipTransferPayload(
            $newOwner,
            ['magazines', 'articles', 'child_admins'],
            true,
        ))
        ->assertRedirect(route('admin.users.index'));

    expect($sourceMagazine->refresh()->owner_admin_id)->toBe($newOwner->id)
        ->and($sourceArticle->refresh()->owner_admin_id)->toBe($newOwner->id)
        ->and($sourceMagazine->created_by)->toBe($source->id)
        ->and($sourceArticle->created_by)->toBe($source->id)
        ->and($childOne->refresh()->parent_admin_id)->toBe($newOwner->id)
        ->and($childTwo->refresh()->parent_admin_id)->toBe($newOwner->id)
        ->and($childMagazine->refresh()->owner_admin_id)->toBe($childOne->id)
        ->and($childArticle->refresh()->owner_admin_id)->toBe($childTwo->id)
        ->and($source->refresh()->is_active)->toBeFalse();

    $log = ActivityLog::query()->where('action', 'ownership_transferred')->sole();
    expect($log->new_values['magazines_transferred'])->toBe(1)
        ->and($log->new_values['articles_transferred'])->toBe(1)
        ->and($log->new_values['child_admins_reassigned'])->toBe(2)
        ->and($log->new_values['source_deactivated'])->toBeTrue();
});

test('Child Admin reassignment rejects a descendant destination that would create a cycle without mutation', function () {
    $adminRole = createOwnershipTransferRole('transfer-cycle-admin');
    $source = createOwnershipTransferAdmin('Cycle Current Owner', $adminRole, $this->superAdmin);
    $directChild = createOwnershipTransferAdmin('Cycle Direct Child', $adminRole, $source);
    $descendantDestination = createOwnershipTransferAdmin('Cycle Descendant Destination', $adminRole, $directChild);
    $magazine = Magazine::query()->forceCreate(['title' => 'Cycle Magazine', 'owner_admin_id' => $source->id]);

    $this->actingAs($this->superAdmin)
        ->post(route('admin.users.transfer-ownership.store', ['id' => $source->id]), ownershipTransferPayload(
            $descendantDestination,
            ['magazines', 'child_admins'],
        ))
        ->assertSessionHasErrors('new_owner_id');

    expect($directChild->refresh()->parent_admin_id)->toBe($source->id)
        ->and($descendantDestination->refresh()->parent_admin_id)->toBe($directChild->id)
        ->and($magazine->refresh()->owner_admin_id)->toBe($source->id)
        ->and(ActivityLog::query()->where('action', 'ownership_transferred')->exists())->toBeFalse();
});

test('authorized Parent Admin can reassign a sibling Source direct children to another sibling', function () {
    $managerRole = createOwnershipTransferRole('transfer-child-sibling-manager', ['users.edit']);
    $adminRole = createOwnershipTransferRole('transfer-child-sibling-admin');
    $actor = createOwnershipTransferAdmin('Child Sibling Parent', $managerRole, $this->superAdmin);
    $source = createOwnershipTransferAdmin('Child Sibling Source', $adminRole, $actor);
    $newOwner = createOwnershipTransferAdmin('Child Sibling Destination', $adminRole, $actor);
    $directChild = createOwnershipTransferAdmin('Child Sibling Direct Child', $adminRole, $source);

    $this->actingAs($actor)
        ->post(route('admin.users.transfer-ownership.store', ['id' => $source->id]), ownershipTransferPayload($newOwner, ['child_admins']))
        ->assertRedirect(route('admin.users.index'));

    expect($directChild->refresh()->parent_admin_id)->toBe($newOwner->id);
});

test('New Owner access remains constrained by existing RBAC and unowned content is not transferred', function () {
    $sourceRole = createOwnershipTransferRole('transfer-rbac-source');
    $newOwnerRole = createOwnershipTransferRole('transfer-rbac-new-owner', ['magazines.view']);
    $source = createOwnershipTransferAdmin('RBAC Current Owner', $sourceRole, $this->superAdmin);
    $newOwner = createOwnershipTransferAdmin('RBAC New Owner', $newOwnerRole, $this->superAdmin);
    $magazine = Magazine::query()->forceCreate(['title' => 'RBAC Magazine', 'owner_admin_id' => $source->id]);
    $unownedMagazine = Magazine::query()->forceCreate(['title' => 'Unowned Legacy Magazine', 'owner_admin_id' => null]);

    $this->actingAs($this->superAdmin)
        ->post(route('admin.users.transfer-ownership.store', ['id' => $source->id]), ownershipTransferPayload($newOwner, ['magazines']))
        ->assertRedirect(route('admin.users.index'));

    $this->actingAs($newOwner)
        ->get(route('admin.magazine.show', ['id' => $magazine->id]))
        ->assertSuccessful();
    $this->delete(route('admin.magazine.destroy', ['id' => $magazine->id]))->assertForbidden();

    expect($unownedMagazine->refresh()->owner_admin_id)->toBeNull();
});
