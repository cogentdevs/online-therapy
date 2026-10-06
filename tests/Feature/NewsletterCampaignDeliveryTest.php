<?php

use App\Models\Article;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use App\Services\NewsletterCampaignContentService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();

    config()->set('services.brevo_newsletter', [
        'api_key' => 'newsletter-secret-key',
        'list_id' => 321,
        'base_url' => 'https://api.brevo.test/v3',
        'sender_email' => 'newsletter@example.com',
        'sender_name' => 'Digital Magazine',
    ]);

    $role = Role::findOrCreate('newsletter-delivery-admin', 'web');
    $role->syncPermissions(['admin.access', 'dashboard.view', 'newsletter-campaigns.view', 'newsletter-campaigns.send']);
    $this->admin = User::factory()->create(['is_active' => true]);
    $this->admin->assignRole($role);
});

function deliveryCampaign(string $status = NewsletterCampaign::STATUS_DRAFT): NewsletterCampaign
{
    $article = Article::query()->create([
        'language' => 'ur',
        'title' => 'Ordered article',
        'short_description' => 'Newsletter description',
        'article' => 'Body',
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
    ]);
    $campaign = NewsletterCampaign::query()->create(['title' => 'Weekly Newsletter']);
    $campaign->forceFill(['status' => $status])->save();
    $campaign->contents()->create(['content_type' => 'article', 'content_id' => $article->id, 'sort_order' => 1]);

    return $campaign;
}

test('admin without send permission cannot deliver a campaign', function () {
    $admin = User::factory()->create(['is_active' => true]);

    $this->actingAs($admin)->post(route('admin.newsletter-campaigns.send', deliveryCampaign()))->assertForbidden();
});

test('authorized send targets only configured list and stores successful delivery state', function () {
    Http::preventStrayRequests();
    Http::fake([
        'https://api.brevo.test/v3/contacts/attributes' => Http::response(['attributes' => [['category' => 'normal', 'name' => 'DM_UNSUB_TOKEN', 'type' => 'text']]]),
        'https://api.brevo.test/v3/emailCampaigns' => Http::response(['id' => 987], 201),
        'https://api.brevo.test/v3/emailCampaigns/987/sendNow' => Http::response(null, 204),
    ]);
    $campaign = deliveryCampaign();

    $this->actingAs($this->admin)->post(route('admin.newsletter-campaigns.send', $campaign), ['list_id' => 999])->assertRedirect();

    $campaign->refresh();
    expect($campaign->status)->toBe(NewsletterCampaign::STATUS_SENT)
        ->and($campaign->brevo_campaign_id)->toBe(987)
        ->and($campaign->sent_at)->not->toBeNull()
        ->and($campaign->brevo_error)->toBeNull();

    Http::assertSent(function (Request $request): bool {
        if ($request->url() !== 'https://api.brevo.test/v3/emailCampaigns') {
            return false;
        }

        $html = (string) $request['htmlContent'];

        return $request['recipients']['listIds'] === [321]
            && $request['sender']['email'] === 'newsletter@example.com'
            && str_contains($html, 'Ordered article')
            && str_contains($html, route('mazmoon-detail', ['id' => Article::query()->value('id'), 'slug' => 'ordered-article']))
            && str_contains($html, '{{ unsubscribe }}')
            && str_contains($html, '{{ contact.DM_UNSUB_TOKEN }}')
            && ! str_contains($html, '<img');
    });
});

test('Brevo create failure marks campaign failed without a campaign id', function () {
    Http::fake([
        'https://api.brevo.test/v3/contacts/attributes' => Http::response(['attributes' => [['category' => 'normal', 'name' => 'DM_UNSUB_TOKEN', 'type' => 'text']]]),
        'https://api.brevo.test/v3/emailCampaigns' => Http::response(['message' => 'Create failed'], 500),
    ]);
    $campaign = deliveryCampaign();

    $this->actingAs($this->admin)->post(route('admin.newsletter-campaigns.send', $campaign))->assertRedirect();

    expect($campaign->fresh()->status)->toBe(NewsletterCampaign::STATUS_FAILED)
        ->and($campaign->fresh()->brevo_campaign_id)->toBeNull()
        ->and($campaign->fresh()->sent_at)->toBeNull();
});

test('send failure preserves Brevo id and retry reuses it without creating a duplicate campaign', function () {
    Http::fakeSequence()
        ->push(['attributes' => [['category' => 'normal', 'name' => 'DM_UNSUB_TOKEN', 'type' => 'text']]], 200)
        ->push(['id' => 654], 201)
        ->push(['message' => 'Send failed'], 500);
    $campaign = deliveryCampaign();
    $this->actingAs($this->admin)->post(route('admin.newsletter-campaigns.send', $campaign))->assertRedirect();
    expect($campaign->fresh()->status)->toBe(NewsletterCampaign::STATUS_FAILED)
        ->and($campaign->fresh()->brevo_campaign_id)->toBe(654);

    Http::fake([
        'https://api.brevo.test/v3/contacts/attributes' => Http::response(['attributes' => [['category' => 'normal', 'name' => 'DM_UNSUB_TOKEN', 'type' => 'text']]]),
        'https://api.brevo.test/v3/emailCampaigns/654/sendNow' => Http::response(null, 204),
    ]);
    $this->actingAs($this->admin)->post(route('admin.newsletter-campaigns.retry', $campaign))->assertRedirect();

    expect($campaign->fresh()->status)->toBe(NewsletterCampaign::STATUS_SENT);
    $retryRequests = collect(Http::recorded())->filter(fn (array $record): bool => $record[0]->url() === 'https://api.brevo.test/v3/emailCampaigns/654/sendNow');
    expect($retryRequests)->toHaveCount(1);
});

test('sent and sending campaigns cannot send again and sent campaigns cannot retry', function (string $status, string $route) {
    Http::preventStrayRequests();
    $campaign = deliveryCampaign($status);

    $this->actingAs($this->admin)->post(route($route, $campaign))->assertUnprocessable();
    Http::assertNothingSent();
})->with([
    'sent send now' => [NewsletterCampaign::STATUS_SENT, 'admin.newsletter-campaigns.send'],
    'sending send now' => [NewsletterCampaign::STATUS_SENDING, 'admin.newsletter-campaigns.send'],
    'sent retry' => [NewsletterCampaign::STATUS_SENT, 'admin.newsletter-campaigns.retry'],
]);

test('draft campaign without content cannot send', function () {
    Http::preventStrayRequests();
    $campaign = NewsletterCampaign::query()->create(['title' => 'Empty']);

    $this->actingAs($this->admin)->post(route('admin.newsletter-campaigns.send', $campaign))->assertUnprocessable();
    Http::assertNothingSent();
});

test('token preflight fails before Brevo campaign creation', function () {
    Http::preventStrayRequests();
    Http::fake([
        'https://api.brevo.test/v3/contacts/attributes' => Http::response(['attributes' => [['category' => 'normal', 'name' => 'DM_UNSUB_TOKEN', 'type' => 'text']]]),
    ]);
    $subscriber = NewsletterSubscriber::factory()->create(['brevo_sync_status' => NewsletterSubscriber::SYNC_SYNCED]);
    $subscriber->forceFill(['unsubscribe_token' => null])->save();
    $campaign = deliveryCampaign();

    $this->actingAs($this->admin)->post(route('admin.newsletter-campaigns.send', $campaign))->assertRedirect();

    expect($campaign->fresh()->status)->toBe(NewsletterCampaign::STATUS_FAILED)
        ->and($campaign->fresh()->brevo_campaign_id)->toBeNull()
        ->and($campaign->fresh()->brevo_error)->toContain('newsletter:sync-unsubscribe-tokens');
    Http::assertNotSent(fn (Request $request): bool => str_contains($request->url(), '/emailCampaigns'));
});

test('failure text is sanitized before storage', function () {
    Http::fake([
        'https://api.brevo.test/v3/contacts/attributes' => Http::response(['attributes' => [['category' => 'normal', 'name' => 'DM_UNSUB_TOKEN', 'type' => 'text']]]),
        'https://api.brevo.test/v3/emailCampaigns' => Http::response(['message' => 'Rejected newsletter-secret-key'], 401),
    ]);
    $campaign = deliveryCampaign();

    $this->actingAs($this->admin)->post(route('admin.newsletter-campaigns.send', $campaign))->assertRedirect();

    expect($campaign->fresh()->brevo_error)->not->toContain('newsletter-secret-key')->toContain('[redacted]');
});

test('preview rendering keeps recipient unsubscribe action non-actionable', function () {
    $campaign = deliveryCampaign();

    $html = view('frontend.mails.newsletter-campaign', [
        'campaign' => app(NewsletterCampaignContentService::class)->hydrate($campaign),
        'isTest' => true,
    ])->render();

    expect($html)->not->toContain('{{ contact.DM_UNSUB_TOKEN }}')
        ->not->toContain('/newsletter/unsubscribe/');
});
