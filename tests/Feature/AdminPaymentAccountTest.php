<?php

use App\Models\ActivityLog;
use App\Models\PaymentAccount;
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

function paymentAccountAdmin(array $permissions): User
{
    $role = Role::findOrCreate('payment-account-admin-'.Str::random(8), 'web');
    $role->syncPermissions(['admin.access', ...$permissions]);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    return $admin;
}

function paymentAccountPayload(array $overrides = []): array
{
    return array_merge([
        'bank_name' => 'Meezan Bank',
        'account_title' => 'Digital Magazine',
        'iban' => 'pk36 mezb 0001 2345 6789 0123',
        'account_no' => '001234567890',
        'branch_code' => ' 0123 ',
    ], $overrides);
}

test('Payment Accounts permissions are synchronized in the Subscriptions group', function () {
    expect(config('admin_modules.modules.payment-accounts.group'))->toBe('Subscriptions')
        ->and(config('admin_modules.modules.payment-accounts.actions'))->toBe([
            'view' => 'View',
            'create' => 'Create',
            'edit' => 'Edit',
            'delete' => 'Delete',
        ]);

    foreach (['payment-accounts.view', 'payment-accounts.create', 'payment-accounts.edit', 'payment-accounts.delete'] as $permission) {
        $this->assertDatabaseHas('permissions', ['name' => $permission, 'guard_name' => 'web']);
    }
});

test('Payment Accounts routes and sidebar enforce effective permissions', function () {
    $viewer = paymentAccountAdmin(['dashboard.view', 'payment-accounts.view']);
    $unauthorizedAdmin = paymentAccountAdmin(['dashboard.view']);

    $this->actingAs($viewer)->get(route('admin.payment-account.index'))->assertSuccessful();
    $this->actingAs($unauthorizedAdmin)->get(route('admin.payment-account.index'))->assertForbidden();
    $this->actingAs($viewer)->get(route('admin.dashboard'))->assertSuccessful()->assertSee(route('admin.payment-account.index'), false);
    $this->actingAs($unauthorizedAdmin)->get(route('admin.dashboard'))->assertSuccessful()->assertDontSee(route('admin.payment-account.index'), false);
});

test('Payment Account creation validates identifiers records ownership and redacts financial details from activity', function () {
    $this->actingAs($this->superAdmin)
        ->post(route('admin.payment-account.store'), paymentAccountPayload())
        ->assertRedirect(route('admin.payment-account.index'));

    $paymentAccount = PaymentAccount::query()->sole();
    $activity = ActivityLog::query()
        ->where('module', 'payment_accounts')
        ->where('subject_id', $paymentAccount->id)
        ->where('action', 'created')
        ->sole();

    expect($paymentAccount->bank_name)->toBe('Meezan Bank')
        ->and($paymentAccount->account_title)->toBe('Digital Magazine')
        ->and($paymentAccount->iban)->toBe('PK36MEZB0001234567890123')
        ->and($paymentAccount->account_no)->toBe('001234567890')
        ->and($paymentAccount->branch_code)->toBe('0123')
        ->and($paymentAccount->is_active)->toBeTrue()
        ->and($paymentAccount->created_by)->toBe($this->superAdmin->id)
        ->and($paymentAccount->updated_by)->toBe($this->superAdmin->id)
        ->and($activity->description)->toContain('Digital Magazine')
        ->and($activity->new_values)->not->toHaveKeys(['iban', 'account_no']);
});

test('Payment Account input is validated and created by cannot be spoofed', function () {
    $otherUser = User::factory()->create();

    $this->actingAs($this->superAdmin)
        ->from(route('admin.payment-account.create'))
        ->post(route('admin.payment-account.store'), paymentAccountPayload([
            'bank_name' => '',
            'account_title' => '',
            'account_no' => '',
            'created_by' => $otherUser->id,
        ]))
        ->assertRedirect(route('admin.payment-account.create'))
        ->assertSessionHasErrors(['bank_name', 'account_title', 'account_no']);

    expect(PaymentAccount::query()->exists())->toBeFalse();
});

test('Payment Account update changes status retains creator and records safe status activity', function () {
    $paymentAccount = PaymentAccount::query()->create(paymentAccountPayload());
    $creatorId = $paymentAccount->created_by;

    $this->actingAs($this->superAdmin)
        ->post(route('admin.payment-account.update', ['id' => $paymentAccount->id]), paymentAccountPayload([
            'bank_name' => 'Updated Bank',
            'is_active' => '0',
        ]))
        ->assertRedirect(route('admin.payment-account.index'));

    $paymentAccount->refresh();
    $activity = ActivityLog::query()
        ->where('module', 'payment_accounts')
        ->where('subject_id', $paymentAccount->id)
        ->where('action', 'status_changed')
        ->sole();

    expect($paymentAccount->bank_name)->toBe('Updated Bank')
        ->and($paymentAccount->is_active)->toBeFalse()
        ->and($paymentAccount->created_by)->toBe($creatorId)
        ->and($paymentAccount->updated_by)->toBe($this->superAdmin->id)
        ->and($activity->old_values)->not->toHaveKeys(['iban', 'account_no'])
        ->and($activity->new_values)->not->toHaveKeys(['iban', 'account_no']);
});

test('Payment Account deletion removes the record and records redacted activity', function () {
    $paymentAccount = PaymentAccount::query()->create(paymentAccountPayload());

    $this->actingAs($this->superAdmin)
        ->delete(route('admin.payment-account.destroy', ['id' => $paymentAccount->id]))
        ->assertRedirect(route('admin.payment-account.index'));

    $activity = ActivityLog::query()
        ->where('module', 'payment_accounts')
        ->where('subject_id', $paymentAccount->id)
        ->where('action', 'deleted')
        ->sole();

    expect(PaymentAccount::query()->find($paymentAccount->id))->toBeNull()
        ->and($activity->old_values)->not->toHaveKeys(['iban', 'account_no']);
});
