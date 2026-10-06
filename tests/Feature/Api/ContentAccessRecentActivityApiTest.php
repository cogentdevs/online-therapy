<?php

use App\Models\Article;
use App\Models\Currency;
use App\Models\Magazine;
use App\Models\MediaStorageLocation;
use App\Models\SiteVisit;
use App\Models\StorageProvider;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserContentVisit;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('user', 'web');
    $this->reader = User::factory()->create(['is_active' => true]);
    $this->reader->assignRole('user');
    $this->otherReader = User::factory()->create(['is_active' => true]);
    $this->otherReader->assignRole('user');
    $this->currency = Currency::query()->create(['name' => 'Rupee', 'code' => 'PKR', 'symbol' => 'Rs']);
    $this->product = SubscriptionProduct::query()->create([
        'product_for' => 'membership',
        'name' => 'Content Access',
        'currency_id' => $this->currency->id,
        'price' => 500,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    $this->articleType = SubscriptionType::query()->create(['name' => 'Articles', 'slug' => 'articles', 'isActive' => true]);
    $this->magazineType = SubscriptionType::query()->create(['name' => 'Magazine', 'slug' => 'magazine', 'isActive' => true]);
});

function c9eArticle(string $title, array $changes = []): Article
{
    return Article::query()->forceCreate(array_replace([
        'language' => 'ur',
        'title' => $title,
        'publish_date' => today(),
        'published_at' => now(),
        'article' => 'Protected article body',
        'isFree' => false,
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
    ], $changes));
}

function c9eMagazine(string $title, array $changes = []): Magazine
{
    return Magazine::query()->forceCreate(array_replace([
        'language' => 'ur',
        'title' => $title,
        'publish_date' => today(),
        'published_at' => now(),
        'description' => 'Magazine description',
        'isFree' => false,
        'is_downloadable' => false,
        'isActive' => true,
        'status' => Magazine::STATUS_PUBLISHED,
    ], $changes));
}

function c9eGrant(
    User $user,
    SubscriptionProduct $product,
    Currency $currency,
    SubscriptionType $type,
    array $changes = [],
): UserSubscription {
    $subscription = UserSubscription::query()->create(array_replace([
        'user_id' => $user->id,
        'subscription_product_id' => $product->id,
        'product_for' => 'membership',
        'product_name' => 'Content Access',
        'currency_id' => $currency->id,
        'price' => 500,
        'discount' => 0,
        'total' => 500,
        'payment_method' => 'card',
        'start_date' => today()->subDay(),
        'end_date' => today()->addMonth(),
        'status' => 'active',
        'is_active' => true,
    ], $changes));
    $subscription->subscriptionTypes()->attach($type);

    return $subscription;
}

function c9eAttachPdf(Magazine $magazine): void
{
    $disk = 'c9e_media_'.$magazine->id;
    Storage::fake($disk);
    Storage::disk($disk)->put('magazines/content.pdf', '%PDF-1.4 content');
    $provider = StorageProvider::query()->forceCreate([
        'disk' => $disk,
        'name' => 'Content Media',
        'slug' => 'content-media-'.$magazine->id,
        'provider_type' => StorageProvider::TYPE_LOCAL,
        'is_active' => true,
        'is_default' => true,
        'priority' => 1,
    ]);
    MediaStorageLocation::query()->forceCreate([
        'media_type' => MediaStorageLocation::MEDIA_MAGAZINE,
        'media_id' => $magazine->id,
        'storage_provider_id' => $provider->id,
        'path' => 'magazines/content.pdf',
        'file_name' => 'content.pdf',
        'mime_type' => 'application/pdf',
        'is_primary' => true,
        'priority' => 1,
        'status' => MediaStorageLocation::STATUS_AVAILABLE,
    ]);
}

test('guest receives free Article content while paid content stays protected without personal activity', function () {
    $free = c9eArticle('Free Article', ['isFree' => true, 'article' => 'FREE BODY']);
    $paid = c9eArticle('Paid Article', ['article' => 'PAID BODY']);

    $this->getJson(route('api.articles.show', $free))
        ->assertOk()
        ->assertJsonPath('data.article.content', 'FREE BODY')
        ->assertJsonPath('data.article.access.is_publicly_accessible', true)
        ->assertJsonPath('data.article.access.can_access', true);
    $this->getJson(route('api.articles.show', $paid))
        ->assertOk()
        ->assertJsonPath('data.article.content', null)
        ->assertJsonPath('data.article.access.requires_subscription', true)
        ->assertJsonPath('data.article.access.can_access', false);

    expect(UserContentVisit::query()->count())->toBe(0)
        ->and(SiteVisit::query()->whereMorphedTo('visitable', $free)->count())->toBe(1)
        ->and(SiteVisit::query()->whereMorphedTo('visitable', $paid)->count())->toBe(0);
});

test('authenticated free Article visits upsert personal activity and do not double count general views', function () {
    $article = c9eArticle('Free Activity Article', ['isFree' => true]);
    Sanctum::actingAs($this->reader);

    $this->getJson(route('api.articles.show', $article))->assertOk();
    $firstVisit = UserContentVisit::query()->sole();
    $this->travel(1)->minute();
    $this->getJson(route('api.articles.show', $article))->assertOk();

    expect(UserContentVisit::query()->count())->toBe(1)
        ->and($firstVisit->fresh()->last_visited_at->greaterThan($firstVisit->last_visited_at))->toBeTrue()
        ->and(SiteVisit::query()->whereMorphedTo('visitable', $article)->count())->toBe(2);
});

test('valid Article entitlement unlocks paid content and records activity', function () {
    $article = c9eArticle('Entitled Article');
    c9eGrant($this->reader, $this->product, $this->currency, $this->articleType);
    Sanctum::actingAs($this->reader);

    $this->getJson(route('api.articles.show', $article))
        ->assertOk()
        ->assertJsonPath('data.article.content', 'Protected article body')
        ->assertJsonPath('data.article.access.is_publicly_accessible', false)
        ->assertJsonPath('data.article.access.requires_subscription', true)
        ->assertJsonPath('data.article.access.can_access', true);

    expect(UserContentVisit::query()->sole()->visitable->is($article))->toBeTrue()
        ->and(SiteVisit::query()->whereMorphedTo('visitable', $article)->count())->toBe(1);
});

test('existing free until rule remains inclusive and expired temporary access stays protected', function () {
    $availableToday = c9eArticle('Free Today', ['isFree' => true, 'free_until' => today()]);
    $expired = c9eArticle('Free Expired', ['isFree' => true, 'free_until' => today()->subDay()]);

    $this->getJson(route('api.articles.show', $availableToday))
        ->assertOk()
        ->assertJsonPath('data.article.access.can_access', true)
        ->assertJsonPath('data.article.content', 'Protected article body');
    $this->getJson(route('api.articles.show', $expired))
        ->assertOk()
        ->assertJsonPath('data.article.access.can_access', false)
        ->assertJsonPath('data.article.content', null);
});

test('non entitled and inactive date invalid or other user Article subscriptions do not unlock or record', function (string $state) {
    $article = c9eArticle('Locked '.$state);
    $changes = match ($state) {
        'expired' => ['end_date' => today()->subDay()],
        'future' => ['start_date' => today()->addDay()],
        'inactive' => ['is_active' => false],
        default => [],
    };
    $subscriptionUser = $state === 'other-user' ? $this->otherReader : $this->reader;

    if ($state !== 'none') {
        c9eGrant($subscriptionUser, $this->product, $this->currency, $this->articleType, $changes);
    }

    Sanctum::actingAs($this->reader);
    $this->getJson(route('api.articles.show', $article))
        ->assertOk()
        ->assertJsonPath('data.article.content', null)
        ->assertJsonPath('data.article.access.can_access', false);

    expect(UserContentVisit::query()->count())->toBe(0)
        ->and(SiteVisit::query()->whereMorphedTo('visitable', $article)->count())->toBe(0);
})->with(['none', 'expired', 'future', 'inactive', 'other-user']);

test('Magazine detail exposes free PDF rules and guests cannot unlock paid PDF', function () {
    $free = c9eMagazine('Free Magazine', ['isFree' => true, 'is_downloadable' => true]);
    $paid = c9eMagazine('Paid Magazine', ['is_downloadable' => true]);
    c9eAttachPdf($free);
    c9eAttachPdf($paid);

    $this->getJson(route('api.magazines.show', $free))
        ->assertOk()
        ->assertJsonPath('data.magazine.access.can_access', true)
        ->assertJsonPath('data.magazine.pdf.read_url', route('api.magazines.pdf', $free))
        ->assertJsonPath('data.magazine.pdf.download_url', route('api.magazines.download', $free));
    $this->getJson(route('api.magazines.show', $paid))
        ->assertOk()
        ->assertJsonPath('data.magazine.access.can_access', false)
        ->assertJsonPath('data.magazine.pdf.read_url', null)
        ->assertJsonPath('data.magazine.pdf.download_url', null);
    $this->getJson(route('api.magazines.pdf', $paid))->assertForbidden();
    $this->getJson(route('api.magazines.download', $paid))->assertForbidden();

    expect(UserContentVisit::query()->count())->toBe(0);
});

test('Magazine entitlement unlocks detail and PDF view while download flag remains authoritative', function () {
    $viewOnly = c9eMagazine('Entitled View Only');
    $downloadable = c9eMagazine('Entitled Download', ['is_downloadable' => true]);
    c9eAttachPdf($viewOnly);
    c9eAttachPdf($downloadable);
    c9eGrant($this->reader, $this->product, $this->currency, $this->magazineType);
    Sanctum::actingAs($this->reader);

    $this->getJson(route('api.magazines.show', $viewOnly))
        ->assertOk()
        ->assertJsonPath('data.magazine.access.can_access', true)
        ->assertJsonPath('data.magazine.pdf.read_url', route('api.magazines.pdf', $viewOnly))
        ->assertJsonPath('data.magazine.pdf.download_url', null);
    $inline = $this->get(route('api.magazines.pdf', $viewOnly))->assertOk();
    expect($inline->headers->get('content-type'))->toContain('application/pdf')
        ->and($inline->headers->get('content-disposition'))->toStartWith('inline;');
    $this->getJson(route('api.magazines.download', $viewOnly))->assertForbidden();

    $this->getJson(route('api.magazines.show', $downloadable))
        ->assertOk()
        ->assertJsonPath('data.magazine.pdf.download_url', route('api.magazines.download', $downloadable));
    $download = $this->get(route('api.magazines.download', $downloadable))->assertOk();
    expect($download->headers->get('content-disposition'))->toStartWith('attachment;');
});

test('expired and future Magazine entitlements cannot unlock PDF access', function (string $state, array $changes) {
    $magazine = c9eMagazine('Locked Magazine '.$state, ['is_downloadable' => true]);
    c9eAttachPdf($magazine);
    c9eGrant($this->reader, $this->product, $this->currency, $this->magazineType, $changes);
    Sanctum::actingAs($this->reader);

    $this->getJson(route('api.magazines.show', $magazine))
        ->assertOk()
        ->assertJsonPath('data.magazine.access.can_access', false)
        ->assertJsonPath('data.magazine.pdf.read_url', null)
        ->assertJsonPath('data.magazine.pdf.download_url', null);
    $this->getJson(route('api.magazines.pdf', $magazine))->assertForbidden();
    $this->getJson(route('api.magazines.download', $magazine))->assertForbidden();
})->with([
    'expired' => ['expired', ['end_date' => today()->subDay()]],
    'future' => ['future', ['start_date' => today()->addDay()]],
]);

test('Magazine detail activity upserts and appears in the existing recent activity response', function () {
    $article = c9eArticle('Recent Article', ['isFree' => true]);
    $magazine = c9eMagazine('Recent Magazine', ['isFree' => true]);
    Sanctum::actingAs($this->reader);

    $this->getJson(route('api.articles.show', $article))->assertOk();
    $this->travel(1)->minute();
    $this->getJson(route('api.magazines.show', $magazine))->assertOk();
    $this->travel(1)->minute();
    $this->getJson(route('api.magazines.show', $magazine))->assertOk();

    expect(UserContentVisit::query()->count())->toBe(2);
    $this->getJson(route('api.me.recent-activities.index'))
        ->assertOk()
        ->assertJsonPath('data.pagination.total', 2)
        ->assertJsonPath('data.recent_activities.0.content_type', 'magazine')
        ->assertJsonPath('data.recent_activities.0.content_id', $magazine->id)
        ->assertJsonPath('data.recent_activities.1.content_type', 'article');
});

test('another users Magazine entitlement and activity remain isolated', function () {
    $magazine = c9eMagazine('Isolated Magazine');
    c9eGrant($this->otherReader, $this->product, $this->currency, $this->magazineType);
    UserContentVisit::query()->create([
        'user_id' => $this->otherReader->id,
        'visitable_type' => $magazine->getMorphClass(),
        'visitable_id' => $magazine->id,
        'last_visited_at' => now(),
    ]);
    Sanctum::actingAs($this->reader);

    $this->getJson(route('api.magazines.show', $magazine))
        ->assertOk()
        ->assertJsonPath('data.magazine.access.can_access', false);
    $this->getJson(route('api.me.recent-activities.index'))
        ->assertOk()
        ->assertJsonPath('data.pagination.total', 0);

    expect(UserContentVisit::query()->where('user_id', $this->otherReader->id)->count())->toBe(1);
});
