<?php

use App\Models\Article;
use App\Models\Bookmark;
use App\Models\Currency;
use App\Models\Magazine;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserContentVisit;
use App\Models\UserSubscription;
use Barryvdh\DomPDF\PDF;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-09-10 12:00:00');
    Role::findOrCreate('user', 'web');
    $this->reader = User::factory()->create(['is_active' => true]);
    $this->reader->assignRole('user');
    $this->otherReader = User::factory()->create(['is_active' => true]);
    $this->otherReader->assignRole('user');
    $this->currency = Currency::query()->create(['name' => 'Rupee', 'code' => 'PKR', 'symbol' => 'Rs']);
    $this->product = SubscriptionProduct::query()->create([
        'product_for' => 'membership', 'name' => 'Current Name', 'currency_id' => $this->currency->id,
        'price' => 250, 'duration_value' => 1, 'duration_unit' => 'month', 'isActive' => true,
    ]);
    $this->magazineType = SubscriptionType::query()->create(['name' => 'Magazine', 'slug' => 'magazine', 'isActive' => true]);
    $this->articleType = SubscriptionType::query()->create(['name' => 'Articles', 'slug' => 'articles', 'isActive' => true]);
});

afterEach(fn () => Carbon::setTestNow());

function c9cSubscription(User $user, SubscriptionProduct $product, Currency $currency, array $changes = []): UserSubscription
{
    return UserSubscription::query()->create(array_replace([
        'user_id' => $user->id, 'subscription_product_id' => $product->id,
        'product_for' => 'membership', 'product_name' => 'Snapshot Membership',
        'currency_id' => $currency->id, 'price' => 300, 'discount' => 50, 'total' => 250,
        'payment_method' => 'card', 'status' => 'active', 'is_active' => true,
        'start_date' => '2026-09-01', 'end_date' => '2026-10-01',
    ], $changes));
}

function c9cArticle(string $title): Article
{
    return Article::query()->forceCreate(['language' => 'ur', 'title' => $title, 'publish_date' => today(),
        'published_at' => now(), 'article' => 'Text', 'isFree' => true, 'isActive' => true, 'status' => Article::STATUS_PUBLISHED]);
}

function c9cMagazine(string $title): Magazine
{
    return Magazine::query()->forceCreate(['language' => 'ur', 'title' => $title, 'publish_date' => today(),
        'published_at' => now(), 'description' => 'Text', 'isFree' => true, 'isActive' => true, 'status' => Magazine::STATUS_PUBLISHED]);
}

test('all account read endpoints require Sanctum', function (string $url) {
    $this->getJson($url)->assertUnauthorized();
})->with(['/api/me/subscriptions/active', '/api/me/entitlements', '/api/me/subscriptions', '/api/me/bookmarks', '/api/me/recent-activities', '/api/me/subscriptions/1/invoice', '/api/me/subscriptions/1/invoice/download']);

test('empty account lists and active state return 200 without creating records', function () {
    Sanctum::actingAs($this->reader);
    $this->getJson('/api/me/subscriptions/active')->assertOk()->assertJsonPath('data.has_active_subscription', false)->assertJsonPath('data.active_subscriptions', []);
    $this->getJson('/api/me/entitlements')->assertOk()->assertJsonPath('data.modules', []);
    $this->getJson('/api/me/subscriptions')->assertOk()->assertJsonPath('data.subscriptions', [])->assertJsonPath('data.pagination.total', 0);
    $this->getJson('/api/me/bookmarks')->assertOk()->assertJsonPath('data.bookmarks', []);
    $this->getJson('/api/me/recent-activities')->assertOk()->assertJsonPath('data.recent_activities', []);
    $this->assertDatabaseCount('user_content_visits', 0);
});

test('active plan and membership use existing date rule and snapshot modules', function () {
    $plan = c9cSubscription($this->reader, $this->product, $this->currency, ['product_for' => 'plan', 'product_name' => 'Article Plan']);
    $membership = c9cSubscription($this->reader, $this->product, $this->currency, ['product_name' => 'Magazine Membership']);
    $expired = c9cSubscription($this->reader, $this->product, $this->currency, ['end_date' => '2026-09-09']);
    c9cSubscription($this->reader, $this->product, $this->currency, ['start_date' => '2026-09-11']);
    c9cSubscription($this->otherReader, $this->product, $this->currency);
    $plan->subscriptionTypes()->attach($this->articleType);
    $membership->subscriptionTypes()->attach([$this->articleType->id, $this->magazineType->id]);
    $expired->subscriptionTypes()->attach($this->magazineType);

    Sanctum::actingAs($this->reader);
    $this->getJson('/api/me/subscriptions/active')->assertOk()->assertJsonCount(2, 'data.active_subscriptions')
        ->assertJsonPath('data.active_subscriptions.0.product_name', 'Article Plan')
        ->assertJsonPath('data.active_subscriptions.1.product_for', 'membership');
    $this->getJson('/api/me/entitlements')->assertOk()->assertJsonCount(2, 'data.modules')
        ->assertJsonPath('data.modules.0.slug', 'articles');
});

test('subscription history is owned newest first searchable sorted and paginated', function () {
    foreach (range(1, 11) as $number) {
        c9cSubscription($this->reader, $this->product, $this->currency, [
            'product_name' => 'Own '.$number, 'created_at' => Carbon::parse('2026-09-01')->addMinutes($number),
        ]);
    }
    c9cSubscription($this->otherReader, $this->product, $this->currency, ['product_name' => 'Secret Other']);
    Sanctum::actingAs($this->reader);
    $this->getJson('/api/me/subscriptions')->assertOk()->assertJsonPath('data.pagination.total', 11)
        ->assertJsonPath('data.subscriptions.0.product_name', 'Own 11')->assertJsonMissing(['product_name' => 'Secret Other']);
    $this->getJson('/api/me/subscriptions?page=2')->assertOk()->assertJsonCount(1, 'data.subscriptions');
    $this->getJson('/api/me/subscriptions?search=Own%203')->assertOk()->assertJsonPath('data.pagination.total', 1);
    $this->getJson('/api/me/subscriptions?sort=unknown')->assertUnprocessable();
});

test('invoice uses same Blade PDF builder and owned record for inline and download', function () {
    $own = c9cSubscription($this->reader, $this->product, $this->currency, ['invoice_no' => '20260001']);
    $other = c9cSubscription($this->otherReader, $this->product, $this->currency);
    Sanctum::actingAs($this->reader);
    $pdf = Mockery::mock(PDF::class);
    $pdf->shouldReceive('loadView')->twice()->with('frontend.user-account.invoice_pdf', Mockery::on(
        fn (array $data): bool => $data['userSubscription']->is($own) && $data['userSubscription']->product_name === 'Snapshot Membership',
    ))->andReturnSelf();
    $pdf->shouldReceive('setPaper')->twice()->with('a4', 'portrait')->andReturnSelf();
    $pdf->shouldReceive('stream')->once()->with('invoice-20260001.pdf')->andReturn(response('pdf', 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="invoice-20260001.pdf"']));
    $pdf->shouldReceive('download')->once()->with('invoice-20260001.pdf')->andReturn(response('pdf', 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="invoice-20260001.pdf"']));
    app()->instance('dompdf.wrapper', $pdf);
    $this->get('/api/me/subscriptions/'.$own->id.'/invoice')->assertOk()->assertHeader('content-type', 'application/pdf')->assertHeader('content-disposition', 'inline; filename="invoice-20260001.pdf"');
    $this->get('/api/me/subscriptions/'.$own->id.'/invoice/download')->assertOk()->assertHeader('content-disposition', 'attachment; filename="invoice-20260001.pdf"');
    $this->getJson('/api/me/subscriptions/'.$other->id.'/invoice')->assertNotFound();
    $this->getJson('/api/me/subscriptions/999999/invoice')->assertNotFound();
    expect($own->fresh()->product_name)->toBe('Snapshot Membership');
});

test('invoice without number keeps the current service fallback filename', function () {
    $own = c9cSubscription($this->reader, $this->product, $this->currency);
    Sanctum::actingAs($this->reader);
    $pdf = Mockery::mock(PDF::class);
    $pdf->shouldReceive('loadView')->once()->with('frontend.user-account.invoice_pdf', Mockery::type('array'))->andReturnSelf();
    $pdf->shouldReceive('setPaper')->once()->with('a4', 'portrait')->andReturnSelf();
    $pdf->shouldReceive('download')->once()->with('invoice-subscription.pdf')->andReturn(response('pdf', 200, [
        'Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="invoice-subscription.pdf"',
    ]));
    app()->instance('dompdf.wrapper', $pdf);
    $this->get('/api/me/subscriptions/'.$own->id.'/invoice/download')->assertOk()
        ->assertHeader('content-disposition', 'attachment; filename="invoice-subscription.pdf"');
});

test('bookmarks list article and magazine page with ownership search and no writes', function () {
    $article = c9cArticle('Saved Article');
    $magazine = c9cMagazine('Saved Magazine');
    Bookmark::query()->create(['user_id' => $this->reader->id, 'bookmarkable_type' => $article->getMorphClass(), 'bookmarkable_id' => $article->id]);
    Bookmark::query()->create(['user_id' => $this->reader->id, 'bookmarkable_type' => $magazine->getMorphClass(), 'bookmarkable_id' => $magazine->id, 'pdf_page' => 17]);
    Bookmark::query()->create(['user_id' => $this->otherReader->id, 'bookmarkable_type' => $article->getMorphClass(), 'bookmarkable_id' => $article->id]);
    Sanctum::actingAs($this->reader);
    $this->getJson('/api/me/bookmarks')->assertOk()->assertJsonPath('data.pagination.total', 2)
        ->assertJsonPath('data.bookmarks.0.content_type', 'magazine')->assertJsonPath('data.bookmarks.0.pdf_page', 17)
        ->assertJsonPath('data.bookmarks.1.detail_url', route('api.articles.show', $article->id));
    $this->getJson('/api/me/bookmarks?search=Saved%20Article')->assertOk()->assertJsonPath('data.pagination.total', 1);
    $this->getJson('/api/me/bookmarks?sort=title&direction=asc')->assertOk()->assertJsonPath('data.bookmarks.0.title', 'Saved Article');
    $this->assertDatabaseCount('bookmarks', 3);
});

test('recent activities list only own article and magazine visits without writing', function () {
    $article = c9cArticle('Visited Article');
    $magazine = c9cMagazine('Visited Magazine');
    UserContentVisit::query()->create(['user_id' => $this->reader->id, 'visitable_type' => $article->getMorphClass(), 'visitable_id' => $article->id, 'last_visited_at' => '2026-09-01 10:00:00']);
    UserContentVisit::query()->create(['user_id' => $this->reader->id, 'visitable_type' => $magazine->getMorphClass(), 'visitable_id' => $magazine->id, 'last_visited_at' => '2026-09-02 10:00:00']);
    UserContentVisit::query()->create(['user_id' => $this->otherReader->id, 'visitable_type' => $article->getMorphClass(), 'visitable_id' => $article->id, 'last_visited_at' => '2026-09-03 10:00:00']);
    Sanctum::actingAs($this->reader);
    $this->getJson('/api/me/recent-activities')->assertOk()->assertJsonPath('data.pagination.total', 2)
        ->assertJsonPath('data.recent_activities.0.content_type', 'magazine');
    $this->getJson('/api/me/recent-activities?search=Visited%20Article')->assertOk()->assertJsonPath('data.pagination.total', 1);
    $this->assertDatabaseCount('user_content_visits', 3);
});

test('bookmark and activity lists paginate ten records and reject unsupported sorting', function () {
    foreach (range(1, 11) as $number) {
        $article = c9cArticle('Repeated Content '.$number);
        Bookmark::query()->create(['user_id' => $this->reader->id, 'bookmarkable_type' => $article->getMorphClass(), 'bookmarkable_id' => $article->id]);
        UserContentVisit::query()->forceCreate([
            'user_id' => $this->reader->id, 'visitable_type' => $article->getMorphClass(),
            'visitable_id' => $article->id, 'last_visited_at' => Carbon::parse('2026-09-01')->addMinutes($number),
        ]);
    }
    Sanctum::actingAs($this->reader);
    $this->getJson('/api/me/bookmarks')->assertOk()->assertJsonCount(10, 'data.bookmarks')->assertJsonPath('data.pagination.total', 11);
    $this->getJson('/api/me/bookmarks?page=2')->assertOk()->assertJsonCount(1, 'data.bookmarks');
    $this->getJson('/api/me/recent-activities')->assertOk()->assertJsonCount(10, 'data.recent_activities')->assertJsonPath('data.pagination.total', 11);
    $this->getJson('/api/me/recent-activities?page=2')->assertOk()->assertJsonCount(1, 'data.recent_activities');
    $this->getJson('/api/me/bookmarks?sort=status')->assertUnprocessable();
    $this->getJson('/api/me/recent-activities?direction=invalid')->assertUnprocessable();
    $this->assertDatabaseCount('bookmarks', 11);
    $this->assertDatabaseCount('user_content_visits', 11);
});
