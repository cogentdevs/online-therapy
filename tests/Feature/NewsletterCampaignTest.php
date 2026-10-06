<?php

use App\Mail\NewsletterCampaignTestMail;
use App\Models\Article;
use App\Models\Magazine;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);
beforeEach(function () {
    $this->withoutVite();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();
    Mail::fake();
    $role = Role::findOrCreate('campaign-admin', 'web');
    $role->syncPermissions(['admin.access', 'dashboard.view', 'newsletter-campaigns.view', 'newsletter-campaigns.create', 'newsletter-campaigns.edit', 'newsletter-campaigns.delete']);
    $this->admin = User::factory()->create(['is_active' => true]);
    $this->admin->assignRole($role);
});
function newsletterArticle(): Article
{
    return Article::query()->create(['language' => 'ur', 'title' => 'Article title', 'short_description' => 'مختصر اردو تفصیل', 'article' => 'Body', 'isActive' => true, 'status' => Article::STATUS_PUBLISHED]);
}
function newsletterMagazine(): Magazine
{
    return Magazine::query()->create(['language' => 'ur', 'title' => 'Magazine title', 'description' => 'میگزین کی مختصر تفصیل', 'isActive' => true, 'status' => Magazine::STATUS_PUBLISHED]);
}
test('authorized admin creates an ordered mixed draft without changing subscribers', function () {
    $article = newsletterArticle();
    $magazine = newsletterMagazine();
    NewsletterSubscriber::factory()->create();
    $this->actingAs($this->admin)->post(route('admin.newsletter-campaigns.store'), ['title' => 'Weekly', 'short_description' => 'Intro', 'contents' => [['type' => 'magazine', 'id' => $magazine->id, 'sort_order' => 1], ['type' => 'article', 'id' => $article->id, 'sort_order' => 2]]])->assertRedirect();
    $campaign = NewsletterCampaign::query()->sole();
    expect($campaign->status)->toBe('draft')->and($campaign->created_by)->toBe($this->admin->id)->and($campaign->contents()->pluck('content_type')->all())->toBe(['magazine', 'article'])->and(NewsletterSubscriber::query()->count())->toBe(1);
});
test('preview uses current content links without images or state changes', function () {
    $article = newsletterArticle();
    $campaign = NewsletterCampaign::query()->create(['title' => 'Preview']);
    $campaign->contents()->create(['content_type' => 'article', 'content_id' => $article->id, 'sort_order' => 1]);
    $this->actingAs($this->admin)->get(route('admin.newsletter-campaigns.preview', $campaign))->assertSuccessful()->assertSee('Article title')->assertSee(route('mazmoon-detail', ['id' => $article->id, 'slug' => 'article-title']), false)->assertDontSee('<img', false);
    expect($campaign->fresh()->status)->toBe('draft')->and($campaign->fresh()->sent_at)->toBeNull();
});
test('test email uses campaign template and does not subscribe recipient or change draft', function () {
    $article = newsletterArticle();
    $campaign = NewsletterCampaign::query()->create(['title' => 'Test']);
    $campaign->contents()->create(['content_type' => 'article', 'content_id' => $article->id, 'sort_order' => 1]);
    $this->actingAs($this->admin)->post(route('admin.newsletter-campaigns.test', $campaign), ['email' => 'test@example.com'])->assertRedirect();
    Mail::assertSent(NewsletterCampaignTestMail::class, fn ($mail) => $mail->hasTo('test@example.com'));
    expect(NewsletterSubscriber::query()->where('email', 'test@example.com')->exists())->toBeFalse()->and($campaign->fresh()->status)->toBe('draft')->and($campaign->fresh()->brevo_campaign_id)->toBeNull();
});
test('only drafts can be deleted and content deletion does not delete source records', function () {
    $article = newsletterArticle();
    $campaign = NewsletterCampaign::query()->create(['title' => 'Delete']);
    $campaign->contents()->create(['content_type' => 'article', 'content_id' => $article->id, 'sort_order' => 1]);
    $this->actingAs($this->admin)->delete(route('admin.newsletter-campaigns.destroy', $campaign))->assertRedirect();
    expect($campaign->fresh())->toBeNull()->and($article->fresh())->not->toBeNull();
});
