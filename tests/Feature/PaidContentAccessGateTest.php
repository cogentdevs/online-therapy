<?php

use App\Models\Article;
use App\Models\Currency;
use App\Models\Magazine;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-09-11 12:00:00');
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create(['is_active' => true]);
    $this->user->assignRole('user');
    $this->currency = Currency::query()->create(['name' => 'Rupee', 'code' => 'PKR', 'symbol' => 'Rs', 'isActive' => true]);
    $this->product = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_MEMBERSHIP, 'name' => 'Access Product',
        'currency_id' => $this->currency->id, 'price' => 1000, 'duration_value' => 1,
        'duration_unit' => 'month', 'isActive' => true,
    ]);
    $this->articlesType = SubscriptionType::query()->create(['name' => 'Articles', 'slug' => 'articles', 'isActive' => true]);
    $this->magazineType = SubscriptionType::query()->create(['name' => 'Magazine', 'slug' => 'magazine', 'isActive' => true]);
});

afterEach(fn () => Carbon::setTestNow());

function gatedArticle(bool $isFree, string $title = 'Protected Article'): Article
{
    return Article::query()->forceCreate([
        'language' => 'ur', 'title' => $title, 'publish_date' => '2026-09-01',
        'published_at' => '2026-09-01 10:00:00', 'article' => 'Protected article body',
        'isFree' => $isFree, 'isActive' => true, 'status' => Article::STATUS_PUBLISHED,
    ]);
}

function gatedMagazine(bool $isFree, string $title = 'Protected Magazine'): Magazine
{
    return Magazine::query()->forceCreate([
        'language' => 'ur', 'title' => $title, 'publish_date' => '2026-09-01',
        'published_at' => '2026-09-01 10:00:00', 'description' => 'Protected magazine body',
        'isFree' => $isFree, 'is_downloadable' => true, 'isActive' => true, 'status' => Magazine::STATUS_PUBLISHED,
    ]);
}

function grantSnapshot(User $user, SubscriptionProduct $product, Currency $currency, array $typeIds, array $overrides = []): UserSubscription
{
    $subscription = UserSubscription::query()->create(array_merge([
        'user_id' => $user->id, 'subscription_product_id' => $product->id,
        'product_for' => $product->product_for, 'product_name' => $product->name,
        'currency_id' => $currency->id, 'price' => 1000, 'discount' => 0, 'total' => 1000,
        'start_date' => '2026-09-01', 'end_date' => '2026-10-01', 'status' => 'active', 'is_active' => true,
    ], $overrides));
    $subscription->subscriptionTypes()->attach($typeIds);

    return $subscription;
}

test('free article and magazine remain public for guests and unsubscribed users', function () {
    $article = gatedArticle(true, 'Free Article');
    $magazine = gatedMagazine(true, 'Free Magazine');

    $this->get(route('mazmoon-detail', [$article->id, 'free']))->assertOk()->assertSee('Protected article body');
    $this->get(route('shumara-detail', [$magazine->id, 'free']))->assertOk()->assertSee('Protected magazine body');
    $this->actingAs($this->user)->get(route('mazmoon-detail', [$article->id, 'free']))->assertOk();
    $this->actingAs($this->user)->get(route('shumara-detail', [$magazine->id, 'free']))->assertOk();
});

test('guest paid content requests return to safe fallback with protected destination preserved', function () {
    $article = gatedArticle(false);
    $magazine = gatedMagazine(false);

    foreach ([
        route('mazmoon-detail', [$article->id, 'paid']),
        route('shumara-detail', [$magazine->id, 'paid']),
        route('shumara-detail.pdf', [$magazine->id, 'paid']),
        route('shumara-detail.download', [$magazine->id, 'paid']),
    ] as $destination) {
        $this->get($destination)->assertRedirect(route('frontend.home'))
            ->assertSessionHas('show_front_login_modal', true);
        expect(session('front_paid_content_intended_url'))->toBe($destination);
    }
});

test('matching snapshot entitlements grant access and wrong module does not', function () {
    $article = gatedArticle(false);
    $magazine = gatedMagazine(false);
    grantSnapshot($this->user, $this->product, $this->currency, [$this->magazineType->id]);

    $this->actingAs($this->user)->get(route('mazmoon-detail', [$article->id, 'paid']))->assertRedirect(route('front.subscriptions'))->assertSessionHas('warning');
    $this->actingAs($this->user)->get(route('shumara-detail', [$magazine->id, 'paid']))->assertOk();

    $articlePlan = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_PLAN, 'name' => 'Article Plan',
        'currency_id' => $this->currency->id, 'price' => 500, 'duration_value' => 1,
        'duration_unit' => 'month', 'isActive' => true,
    ]);
    grantSnapshot($this->user, $articlePlan, $this->currency, [$this->articlesType->id]);
    $this->actingAs($this->user)->get(route('mazmoon-detail', [$article->id, 'paid']))->assertOk()->assertSee('Protected article body');
});

test('expired future and inactive snapshots do not grant paid article access', function (array $overrides) {
    $article = gatedArticle(false);
    grantSnapshot($this->user, $this->product, $this->currency, [$this->articlesType->id], $overrides);

    $this->actingAs($this->user)->get(route('mazmoon-detail', [$article->id, 'paid']))->assertRedirect(route('front.subscriptions'));
})->with([
    'expired' => [['end_date' => '2026-09-10']],
    'future' => [['start_date' => '2026-09-12']],
    'inactive' => [['is_active' => false]],
]);

test('another users entitlement cannot grant access', function () {
    $article = gatedArticle(false);
    $otherUser = User::factory()->create(['is_active' => true]);
    $otherUser->assignRole('user');
    grantSnapshot($otherUser, $this->product, $this->currency, [$this->articlesType->id]);

    $this->actingAs($this->user)->get(route('mazmoon-detail', [$article->id, 'paid']))->assertRedirect(route('front.subscriptions'));
});

test('purchase snapshot stays authoritative after product module changes', function () {
    $article = gatedArticle(false);
    $this->product->subscriptionTypes()->attach($this->articlesType);
    grantSnapshot($this->user, $this->product, $this->currency, [$this->articlesType->id]);
    $this->product->subscriptionTypes()->detach($this->articlesType);

    $this->actingAs($this->user)->get(route('mazmoon-detail', [$article->id, 'paid']))->assertOk();
});

test('expired temporary free access is gated without mutating content', function () {
    $article = gatedArticle(true);
    $article->update(['free_until' => '2026-09-10']);

    $this->get(route('mazmoon-detail', [$article->id, 'expired-free']))->assertRedirect(route('frontend.home'));
    expect($article->fresh()->isFree)->toBeTrue();
});
