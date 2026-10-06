<?php

use App\Mail\AdvertisingRequestStatusMailToAdmin;
use App\Mail\AdvertisingRequestStatusMailToUser;
use App\Models\ActivityLog;
use App\Models\Ad;
use App\Models\AdRequest;
use App\Models\GeneralSetting;
use App\Models\User;
use App\Services\AdPlacementAvailabilityService;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Mail\PendingMail;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    config(['mail.admin_address' => 'cogentdevs@gmail.com']);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();
    $this->admin = User::factory()->create(['is_active' => true]);
    $this->admin->assignRole(Role::findOrCreate('super-admin', 'web'));
    $this->advertiser = User::factory()->create(['is_active' => true]);
    Mail::fake();
});

function workflowRequest(array $slots = [['header', 'header_ad']], array $attributes = []): AdRequest
{
    $request = AdRequest::query()->create(array_merge([
        'user_id' => test()->advertiser->id,
        'name' => 'Advertiser Name',
        'email' => 'snapshot@example.test',
        'phone' => '03001234567',
        'from_date' => '2026-10-10',
        'to_date' => '2026-10-20',
    ], $attributes));
    foreach ($slots as [$page, $place]) {
        $request->placements()->create(['page_name' => $page, 'place' => $place]);
    }

    return $request;
}

test('admin module uses view and edit permissions and shows persisted request detail', function () {
    $request = workflowRequest();
    $role = Role::findOrCreate('ad-request-viewer', 'web');
    $role->givePermissionTo(['admin.access', 'ad-requests.view']);
    $viewer = User::factory()->create(['is_active' => true]);
    $viewer->assignRole($role);
    $unauthorized = User::factory()->create(['is_active' => true]);
    $unauthorizedRole = Role::findOrCreate('other-admin', 'web');
    $unauthorizedRole->givePermissionTo('admin.access');
    $unauthorized->assignRole($unauthorizedRole);

    $this->actingAs($viewer)->get(route('admin.advertising-requests.index'))->assertSuccessful()->assertSee($request->request_no);
    $this->get(route('admin.advertising-requests.show', $request))->assertSuccessful()
        ->assertSee('Advertiser Name')->assertSee('Header Advertisement')->assertSee('20 Oct 2026');
    $this->post(route('admin.advertising-requests.status', $request), ['action' => 'quote_sent'])->assertForbidden();
    $this->actingAs($unauthorized)->get(route('admin.advertising-requests.index'))->assertForbidden();
    $this->get(route('admin.advertising-requests.show', $request))->assertForbidden();
    Mail::assertNothingOutgoing();
});

test('request detail has a back link and SweetAlert confirmations for available workflow actions', function () {
    $request = workflowRequest();
    $placement = $request->placements()->first();

    $this->actingAs($this->admin)
        ->get(route('admin.advertising-requests.show', $request))
        ->assertSuccessful()
        ->assertSee('Back to Advertising Requests')
        ->assertSee(route('admin.advertising-requests.index'), false)
        ->assertSee('data-action-title="Send quote for this request?"', false)
        ->assertSee('data-action-title="Reject this request?"', false)
        ->assertSee('data-action-title="Cancel this request?"', false)
        ->assertSee('data-action-title="Confirm this placement?"', false)
        ->assertSee(route('admin.advertising-requests.placements.confirm', [$request, $placement]), false);

    expect($request->fresh()->status)->toBe('pending')
        ->and($placement->fresh()->status)->toBe('pending');

    Mail::assertNothingOutgoing();
});

test('quote sent does not reserve and sends one snapshot email; invalid transitions are rejected', function () {
    $request = workflowRequest();
    $this->actingAs($this->admin)->post(route('admin.advertising-requests.status', $request), ['action' => 'quote_sent'])->assertRedirect();
    expect($request->fresh()->status)->toBe('quote_sent')
        ->and($request->placements()->first()->status)->toBe('quoted')
        ->and(app(AdPlacementAvailabilityService::class)->isAvailable('header', 'header_ad', '2026-10-10', '2026-10-20'))->toBeTrue();
    Mail::assertSent(AdvertisingRequestStatusMailToUser::class, fn (AdvertisingRequestStatusMailToUser $mail): bool => $mail->hasTo('snapshot@example.test') && $mail->status === 'quote_sent');
    Mail::assertSent(AdvertisingRequestStatusMailToAdmin::class, fn (AdvertisingRequestStatusMailToAdmin $mail): bool => $mail->hasTo('admin@example.test') && $mail->status === 'quote_sent');
    $this->post(route('admin.advertising-requests.status', $request), ['action' => 'quote_sent'])->assertSessionHasErrors('status');
    Mail::assertSentCount(2);
});

test('confirmation reserves one placement and marks only overlapping competitors conflicted', function () {
    $winner = workflowRequest();
    $competitor = workflowRequest([['header', 'header_ad'], ['home', 'home_horizontal_large']]);
    $distant = workflowRequest([['header', 'header_ad']], ['from_date' => '2026-10-21', 'to_date' => '2026-10-25']);
    $slot = $winner->placements()->first();

    $this->actingAs($this->admin)->post(route('admin.advertising-requests.placements.confirm', [$winner, $slot]))->assertRedirect();
    expect($slot->fresh()->status)->toBe('confirmed')
        ->and($winner->fresh()->status)->toBe('confirmed')
        ->and($competitor->placements()->where('page_name', 'header')->first()->status)->toBe('conflicted')
        ->and($competitor->placements()->where('page_name', 'home')->first()->status)->toBe('pending')
        ->and($distant->placements()->first()->status)->toBe('pending')
        ->and(app(AdPlacementAvailabilityService::class)->isAvailable('header', 'header_ad', '2026-10-10', '2026-10-20'))->toBeFalse();
    $this->post(route('admin.advertising-requests.placements.confirm', [$competitor, $competitor->placements()->first()]))->assertSessionHasErrors('status');
    Mail::assertSent(AdvertisingRequestStatusMailToUser::class, fn (AdvertisingRequestStatusMailToUser $mail): bool => $mail->status === 'confirmed');
    Mail::assertSent(AdvertisingRequestStatusMailToAdmin::class, fn (AdvertisingRequestStatusMailToAdmin $mail): bool => $mail->hasTo('admin@example.test') && $mail->status === 'confirmed' && $mail->placement?->is($slot));
    expect(ActivityLog::query()->where('module', 'ad_requests')->where('action', 'updated')->exists())->toBeTrue();
});

test('an actual ad conflict blocks confirmation without changing request or sending email', function () {
    $request = workflowRequest();
    Ad::query()->create(['title' => 'Existing', 'page_name' => 'header', 'place' => 'header_ad', 'start_date' => '2026-10-10', 'expiry_date' => '2026-10-20']);

    $this->actingAs($this->admin)->post(route('admin.advertising-requests.placements.confirm', [$request, $request->placements()->first()]))->assertSessionHasErrors('status');
    expect($request->fresh()->status)->toBe('pending')->and($request->placements()->first()->status)->toBe('pending');
    Mail::assertNothingOutgoing();
});

test('rejection and cancellation are controlled transitions with email and released reservation', function () {
    $rejected = workflowRequest();
    $cancelled = workflowRequest([['home', 'home_horizontal_large']]);
    $this->actingAs($this->admin)->post(route('admin.advertising-requests.status', $rejected), ['action' => 'rejected'])->assertRedirect();
    $this->post(route('admin.advertising-requests.placements.confirm', [$cancelled, $cancelled->placements()->first()]))->assertRedirect();
    $this->post(route('admin.advertising-requests.status', $cancelled), ['action' => 'cancelled'])->assertRedirect();

    expect($rejected->fresh()->status)->toBe('rejected')
        ->and($cancelled->fresh()->status)->toBe('cancelled')
        ->and($cancelled->placements()->first()->status)->toBe('cancelled')
        ->and(app(AdPlacementAvailabilityService::class)->isAvailable('home', 'home_horizontal_large', '2026-10-10', '2026-10-20'))->toBeTrue();
    Mail::assertSent(AdvertisingRequestStatusMailToUser::class, fn (AdvertisingRequestStatusMailToUser $mail): bool => $mail->status === 'rejected');
    Mail::assertSent(AdvertisingRequestStatusMailToUser::class, fn (AdvertisingRequestStatusMailToUser $mail): bool => $mail->status === 'cancelled');
    Mail::assertSent(AdvertisingRequestStatusMailToAdmin::class, fn (AdvertisingRequestStatusMailToAdmin $mail): bool => $mail->hasTo('admin@example.test') && $mail->status === 'rejected');
    Mail::assertSent(AdvertisingRequestStatusMailToAdmin::class, fn (AdvertisingRequestStatusMailToAdmin $mail): bool => $mail->hasTo('admin@example.test') && $mail->status === 'cancelled');
});

test('confirmed placement appears in Ads form and linked Ad uses persisted slot and dates', function () {
    $request = workflowRequest();
    $slot = $request->placements()->first();
    $this->actingAs($this->admin)->post(route('admin.advertising-requests.placements.confirm', [$request, $slot]))->assertRedirect();
    Mail::fake();

    expect($request->fresh()->status)->toBe('confirmed')->and($slot->fresh()->status)->toBe('confirmed');

    $this->get(route('admin.ads.create'))->assertSuccessful()->assertViewHas('requestPlacements', fn ($placements): bool => $placements->contains('id', $slot->id))->assertSee('Advertising Request (optional)')->assertSee($request->request_no);
    $this->post(route('admin.ads.store'), [
        'title' => 'Linked creative',
        'ad_request_placement_id' => $slot->id,
        'page_name' => 'home',
        'place' => 'home_horizontal_small',
        'start_date' => '2026-01-01',
        'expiry_date' => '2026-01-02',
    ])->assertRedirect(route('admin.ads.index'));

    $ad = Ad::query()->sole();
    expect($ad->ad_request_id)->toBe($request->id)
        ->and($ad->ad_request_placement_id)->toBe($slot->id)
        ->and($ad->page_name)->toBe('header')->and($ad->place)->toBe('header_ad')
        ->and($ad->start_date->toDateString())->toBe('2026-10-10')
        ->and($ad->expiry_date->toDateString())->toBe('2026-10-20')
        ->and($slot->fresh()->status)->toBe('published')
        ->and($request->fresh()->status)->toBe('published');
    Mail::assertSent(AdvertisingRequestStatusMailToUser::class, 1);
    Mail::assertSent(AdvertisingRequestStatusMailToAdmin::class, fn (AdvertisingRequestStatusMailToAdmin $mail): bool => $mail->hasTo('admin@example.test') && $mail->status === 'published' && $mail->placement?->is($slot));
    $this->get(route('admin.ads.create'))->assertViewHas('requestPlacements', fn ($placements): bool => ! $placements->contains('id', $slot->id));
});

test('linked Ad edit keeps booking fields and deletion returns placement to confirmed', function () {
    $request = workflowRequest();
    $slot = $request->placements()->first();
    $slot->status = 'confirmed';
    $slot->save();
    $request->status = 'confirmed';
    $request->save();
    $ad = Ad::query()->create([
        'title' => 'Original',
        'page_name' => 'header',
        'place' => 'header_ad',
        'start_date' => $request->from_date,
        'expiry_date' => $request->to_date,
        'ad_request_id' => $request->id,
        'ad_request_placement_id' => $slot->id,
    ]);
    $slot->status = 'published';
    $slot->save();
    $request->status = 'published';
    $request->save();

    $this->actingAs($this->admin)->put(route('admin.ads.update', $ad), [
        'title' => 'Updated',
        'page_name' => 'home',
        'place' => 'home_horizontal_small',
        'start_date' => '2026-01-01',
        'expiry_date' => '2026-01-02',
    ])->assertRedirect(route('admin.ads.index'));
    expect($ad->fresh()->title)->toBe('Updated')
        ->and($ad->fresh()->page_name)->toBe('header')
        ->and($ad->fresh()->start_date->toDateString())->toBe('2026-10-10');

    $this->delete(route('admin.ads.destroy', $ad))->assertRedirect(route('admin.ads.index'));
    expect($slot->fresh()->status)->toBe('confirmed')->and($request->fresh()->status)->toBe('confirmed');
    $this->assertDatabaseMissing('ads', ['id' => $ad->id]);
});

test('manual Ad creation remains available without a request', function () {
    $this->actingAs($this->admin)->post(route('admin.ads.store'), [
        'title' => 'Manual',
        'page_name' => 'home',
        'place' => 'home_horizontal_small',
    ])->assertRedirect(route('admin.ads.index'));
    $ad = Ad::query()->sole();
    expect($ad->ad_request_id)->toBeNull()->and($ad->ad_request_placement_id)->toBeNull();
    Mail::assertNothingOutgoing();
});

test('final Ad creation rejects a new external conflict after placement confirmation', function () {
    $request = workflowRequest();
    $slot = $request->placements()->first();
    $this->actingAs($this->admin)->post(route('admin.advertising-requests.placements.confirm', [$request, $slot]))->assertRedirect();
    Mail::fake();
    Ad::query()->create([
        'title' => 'External booking',
        'page_name' => 'header',
        'place' => 'header_ad',
        'start_date' => '2026-10-15',
        'expiry_date' => '2026-10-22',
    ]);

    $this->post(route('admin.ads.store'), [
        'title' => 'Should not publish',
        'ad_request_placement_id' => $slot->id,
    ])->assertSessionHasErrors('ad_request_placement_id');

    $this->assertDatabaseCount('ads', 1);
    expect($slot->fresh()->status)->toBe('confirmed')->and($request->fresh()->status)->toBe('confirmed');
    Mail::assertNothingOutgoing();
});

test('publishing one of several confirmed placements keeps request confirmed until all are published', function () {
    $request = workflowRequest([['header', 'header_ad'], ['home', 'home_horizontal_large']]);
    $slots = $request->placements()->orderBy('id')->get();
    $this->actingAs($this->admin)->post(route('admin.advertising-requests.placements.confirm', [$request, $slots[0]]))->assertRedirect();
    $this->post(route('admin.advertising-requests.placements.confirm', [$request, $slots[1]]))->assertRedirect();
    Mail::fake();

    $this->post(route('admin.ads.store'), ['title' => 'First', 'ad_request_placement_id' => $slots[0]->id])->assertRedirect();
    expect($request->fresh()->status)->toBe('confirmed')
        ->and($slots[0]->fresh()->status)->toBe('published')
        ->and($slots[1]->fresh()->status)->toBe('confirmed');

    $this->post(route('admin.ads.store'), ['title' => 'Second', 'ad_request_placement_id' => $slots[1]->id])->assertRedirect();
    expect($request->fresh()->status)->toBe('published')->and(Ad::query()->where('ad_request_id', $request->id)->count())->toBe(2);
    Mail::assertSent(AdvertisingRequestStatusMailToUser::class, 2);
    Mail::assertSent(AdvertisingRequestStatusMailToAdmin::class, 2);
    $this->get(route('admin.advertising-requests.show', $request))->assertSuccessful();
    Mail::assertSentCount(4);
});

test('Ads form excludes unconfirmed and already linked request placements', function () {
    $pending = workflowRequest();
    $quoted = workflowRequest([['home', 'home_horizontal_large']]);
    $quoted->placements()->first()->update(['status' => 'quoted']);
    $quoted->update(['status' => 'quote_sent']);
    $conflicted = workflowRequest([['home', 'home_horizontal_small']]);
    $conflicted->placements()->first()->update(['status' => 'conflicted']);
    $cancelled = workflowRequest([['taza_shumara', 'taza_sidebar_1_normal']]);
    $cancelled->placements()->first()->update(['status' => 'cancelled']);
    $cancelled->update(['status' => 'cancelled']);
    $expired = workflowRequest([['taza_shumara', 'taza_sidebar_2_normal']], ['from_date' => '2026-01-01', 'to_date' => '2026-01-05']);
    $expired->placements()->first()->update(['status' => 'confirmed']);
    $expired->update(['status' => 'confirmed']);

    $this->actingAs($this->admin)->get(route('admin.ads.create'))->assertViewHas('requestPlacements', fn ($placements): bool => $placements->isEmpty());
    expect($pending->placements()->first()->status)->toBe('pending');
});

test('status mail uses the existing RTL theme and a mail failure does not undo quote sent', function () {
    $request = workflowRequest();
    $mail = new AdvertisingRequestStatusMailToUser($request->fresh('placements'), 'quote_sent');
    expect($mail->envelope()->subject)->toContain($request->request_no)
        ->and($mail->render())->toContain('dir="rtl"', 'Header Advertisement', 'ابھی محفوظ نہیں ہوا');
    $adminMail = new AdvertisingRequestStatusMailToAdmin($request->fresh('placements'), 'quote_sent');
    expect($adminMail->envelope()->subject)->toContain($request->request_no)
        ->and($adminMail->render())->toContain('dir="rtl"', 'Header Advertisement', 'snapshot@example.test');
    Mail::shouldReceive('to')->once()->with('snapshot@example.test')->andThrow(new RuntimeException('SMTP unavailable'));
    $adminPendingMail = Mockery::mock(PendingMail::class);
    $adminPendingMail->shouldReceive('send')->once()->with(Mockery::type(AdvertisingRequestStatusMailToAdmin::class));
    Mail::shouldReceive('to')->once()->with('admin@example.test')->andReturn($adminPendingMail);

    $this->actingAs($this->admin)->post(route('admin.advertising-requests.status', $request), ['action' => 'quote_sent'])->assertRedirect();
    expect($request->fresh()->status)->toBe('quote_sent')->and($request->placements()->first()->status)->toBe('quoted');
});

test('admin status mail failure does not undo the transition or stop the user mail', function () {
    $request = workflowRequest();
    $userPendingMail = Mockery::mock(PendingMail::class);
    $userPendingMail->shouldReceive('send')->once()->with(Mockery::type(AdvertisingRequestStatusMailToUser::class));
    Mail::shouldReceive('to')->once()->with('snapshot@example.test')->andReturn($userPendingMail);
    Mail::shouldReceive('to')->once()->with('admin@example.test')->andThrow(new RuntimeException('Admin SMTP unavailable'));

    $this->actingAs($this->admin)->post(route('admin.advertising-requests.status', $request), ['action' => 'rejected'])->assertRedirect();

    expect($request->fresh()->status)->toBe('rejected');
});

test('status notification uses the General Settings email when no Admin mail address is configured', function () {
    config(['mail.admin_address' => null]);
    GeneralSetting::query()->create(['email' => 'cogentdevs@gmail.com']);
    $request = workflowRequest();

    $this->actingAs($this->admin)
        ->post(route('admin.advertising-requests.status', $request), ['action' => 'quote_sent'])
        ->assertRedirect();

    Mail::assertSent(AdvertisingRequestStatusMailToUser::class, 1);
    Mail::assertSent(AdvertisingRequestStatusMailToAdmin::class, fn (AdvertisingRequestStatusMailToAdmin $mail): bool => $mail->hasTo('cogentdevs@gmail.com'));
});
