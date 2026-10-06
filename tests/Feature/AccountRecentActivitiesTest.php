<?php

use App\Models\Article;
use App\Models\Magazine;
use App\Models\User;
use App\Models\UserContentVisit;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create(['is_active' => true]);
    $this->user->assignRole('user');
    $this->otherUser = User::factory()->create(['is_active' => true]);
    $this->otherUser->assignRole('user');
});

function activityAccountArticle(string $title, bool $isFree = true): Article
{
    return Article::query()->forceCreate([
        'language' => 'ur', 'title' => $title, 'publish_date' => today(), 'published_at' => now(),
        'article' => 'Article content', 'isFree' => $isFree, 'isActive' => true, 'status' => Article::STATUS_PUBLISHED,
    ]);
}

function activityAccountMagazine(string $title): Magazine
{
    return Magazine::query()->forceCreate([
        'language' => 'ur', 'title' => $title, 'publish_date' => today(), 'published_at' => now(),
        'description' => 'Magazine content', 'isFree' => true, 'isActive' => true, 'status' => Magazine::STATUS_PUBLISHED,
    ]);
}

function createAccountActivity(User $user, Article|Magazine $visitable, string $visitedAt): UserContentVisit
{
    return UserContentVisit::query()->forceCreate([
        'user_id' => $user->id,
        'visitable_type' => $visitable->getMorphClass(),
        'visitable_id' => $visitable->getKey(),
        'last_visited_at' => $visitedAt,
    ]);
}

test('recent activities page requires a frontend account', function () {
    $this->get(route('front.account.recent-activities'))->assertRedirect(route('front.login'));

    $this->actingAs($this->user, 'web')->get(route('front.account.recent-activities'))
        ->assertSuccessful()
        ->assertViewIs('frontend.user-account.recent-activities');
});

test('full listing is user scoped and ordered by latest visit', function () {
    createAccountActivity($this->user, activityAccountArticle('Older Activity'), '2026-09-01 10:00:00');
    createAccountActivity($this->user, activityAccountArticle('Newest Activity'), '2026-09-02 10:00:00');
    createAccountActivity($this->otherUser, activityAccountArticle('Other User Activity'), '2026-09-03 10:00:00');

    $response = $this->actingAs($this->user, 'web')->get(route('front.account.recent-activities'))
        ->assertSuccessful()
        ->assertSee('Newest Activity')
        ->assertSee('Older Activity')
        ->assertDontSee('Other User Activity');

    expect(strpos($response->getContent(), 'Newest Activity'))->toBeLessThan(strpos($response->getContent(), 'Older Activity'));
});

test('full listing paginates ten activities per page', function () {
    foreach (range(1, 11) as $number) {
        createAccountActivity(
            $this->user,
            activityAccountArticle('Paginated Activity '.$number),
            Carbon::parse('2026-09-01')->addMinutes($number)->toDateTimeString(),
        );
    }

    $this->actingAs($this->user, 'web')->get(route('front.account.recent-activities'))
        ->assertSuccessful()
        ->assertViewHas('activities', fn (LengthAwarePaginator $activities): bool => $activities->perPage() === 10
            && $activities->count() === 10
            && $activities->total() === 11)
        ->assertSee('page=2', false);
});

test('article and magazine activities use clean presentation and existing routes', function () {
    $article = activityAccountArticle('Visited Article');
    $magazine = activityAccountMagazine('Visited Magazine');
    createAccountActivity($this->user, $article, '2026-09-05 12:30:00');
    createAccountActivity($this->user, $magazine, '2026-09-06 01:45:00');

    $this->actingAs($this->user, 'web')->get(route('front.account.recent-activities'))
        ->assertSuccessful()
        ->assertSee('مضمون')
        ->assertSee('شمارہ')
        ->assertSee('Visited Article')
        ->assertSee('Visited Magazine')
        ->assertSee('05 Sep 2026, 12:30 PM PKT (UTC+5)')
        ->assertSee('06 Sep 2026, 01:45 AM PKT (UTC+5)')
        ->assertSee(route('mazmoon-detail', [$article->id, 'visited-article']), false)
        ->assertSee(route('shumara-detail', [$magazine->id, 'visited-magazine']), false);
});

test('dashboard shows only latest five activities and full listing link', function () {
    foreach (range(1, 6) as $number) {
        createAccountActivity(
            $this->user,
            activityAccountArticle('Dashboard Activity '.$number),
            Carbon::parse('2026-09-01')->addMinutes($number)->toDateTimeString(),
        );
    }
    createAccountActivity($this->otherUser, activityAccountArticle('Other Dashboard Activity'), '2026-09-10 10:00:00');

    $response = $this->actingAs($this->user, 'web')->get(route('front.account'))
        ->assertSuccessful()
        ->assertSee('Dashboard Activity 6')
        ->assertSee('Dashboard Activity 2')
        ->assertDontSee('Dashboard Activity 1')
        ->assertDontSee('Other Dashboard Activity')
        ->assertSee(route('front.account.recent-activities'), false);

    expect(strpos($response->getContent(), 'Dashboard Activity 6'))
        ->toBeLessThan(strpos($response->getContent(), 'Dashboard Activity 5'));
});

test('dashboard and full listing show empty states', function () {
    $this->actingAs($this->user, 'web')->get(route('front.account'))
        ->assertSuccessful()
        ->assertSee('ابھی کوئی حالیہ سرگرمی موجود نہیں۔');

    $this->get(route('front.account.recent-activities'))
        ->assertSuccessful()
        ->assertSee('ابھی کوئی حالیہ سرگرمی موجود نہیں۔');
});

test('orphan activities remain visible without an invalid view link', function () {
    UserContentVisit::query()->forceCreate([
        'user_id' => $this->user->id,
        'visitable_type' => Article::class,
        'visitable_id' => 999999,
        'last_visited_at' => now(),
    ]);

    foreach ([route('front.account'), route('front.account.recent-activities')] as $url) {
        $this->actingAs($this->user, 'web')->get($url)
            ->assertSuccessful()
            ->assertSee('مواد دستیاب نہیں ہے')
            ->assertDontSee('/mazmoon/999999/', false);
    }
});

test('activity links preserve paid content authorization', function () {
    $article = activityAccountArticle('Previously Visited Paid Article', false);
    createAccountActivity($this->user, $article, now()->toDateTimeString());

    $this->actingAs($this->user, 'web')->get(route('front.account.recent-activities'))
        ->assertSuccessful()
        ->assertSee(route('mazmoon-detail', [$article->id, 'previously-visited-paid-article']), false);

    $this->get(route('mazmoon-detail', [$article->id, 'previously-visited-paid-article']))
        ->assertRedirect(route('front.subscriptions'));
});

test('sidebar displays and activates the recent activities route', function () {
    $this->actingAs($this->user, 'web')->get(route('front.account.recent-activities'))
        ->assertSuccessful()
        ->assertSee('حالیہ سرگرمیاں')
        ->assertSee('front-account-nav-link active', false)
        ->assertSee('aria-current="page"', false);
});
