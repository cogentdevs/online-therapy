<?php

use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Language;
use App\Models\Magazine;
use App\Models\MediaStorageLocation;
use App\Models\StorageProvider;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use App\Services\Storage\MediaStorageService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-09-15 12:00:00');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();

    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
    $this->language = Language::query()->create(['name' => 'English', 'code' => 'en', 'is_active' => true]);
    $this->provider = StorageProvider::query()->create([
        'name' => 'Local Server',
        'slug' => 'local',
        'disk' => 'local_media',
        'provider_type' => StorageProvider::TYPE_LOCAL,
        'is_active' => true,
        'is_default' => true,
        'priority' => 1,
    ]);
});

afterEach(function () {
    Carbon::setTestNow();
});

function freeExpiryMagazinePayload(string $title, bool $isFree, ?string $freeUntil): array
{
    return [
        'language' => 'en',
        'title' => $title,
        'pdf' => UploadedFile::fake()->create($title.'.pdf', 100, 'application/pdf'),
        'provider_ids' => [test()->provider->id],
        'isFree' => $isFree ? '1' : '0',
        'free_until' => $freeUntil,
        'isFeatured' => '0',
        'is_downloadable' => '0',
    ];
}

function freeExpiryArticlePayload(string $title, bool $isFree, ?string $freeUntil): array
{
    return [
        'language' => 'en',
        'title' => $title,
        'article' => '<p>Article body</p>',
        'isFree' => $isFree ? '1' : '0',
        'free_until' => $freeUntil,
        'isFeatured' => '0',
    ];
}

function mockFreeExpiryMagazineStorage(int $times): void
{
    test()->mock(MediaStorageService::class, function ($mock) use ($times): void {
        $mock->shouldReceive('store')->times($times)->andReturnUsing(fn () => new Collection([
            new MediaStorageLocation(['status' => MediaStorageLocation::STATUS_AVAILABLE]),
        ]));
    });
}

test('free until columns are nullable dates positioned after the existing free fields', function () {
    foreach (['magazines', 'articles'] as $table) {
        $columns = collect(Schema::getColumns($table));
        $names = $columns->pluck('name')->values();
        $freeUntil = $columns->firstWhere('name', 'free_until');

        expect($freeUntil['type_name'])->toBe('date')
            ->and($freeUntil['nullable'])->toBeTrue();

        if (DB::getDriverName() !== 'sqlite') {
            expect($names->search('free_until'))->toBe($names->search('isFree') + 1);
        }
    }
});

test('admin create normalizes paid and stores permanent or temporary free content', function () {
    mockFreeExpiryMagazineStorage(3);
    $this->actingAs($this->superAdmin);

    $this->post(route('admin.magazine.store'), freeExpiryMagazinePayload('Paid Magazine', false, '2026-09-30'))
        ->assertRedirect(route('admin.magazine.index'));
    $this->post(route('admin.magazine.store'), freeExpiryMagazinePayload('Permanent Magazine', true, null))
        ->assertRedirect(route('admin.magazine.index'));
    $this->post(route('admin.magazine.store'), freeExpiryMagazinePayload('Temporary Magazine', true, '2026-09-30'))
        ->assertRedirect(route('admin.magazine.index'));

    $this->post(route('admin.article.store'), freeExpiryArticlePayload('Paid Article', false, '2026-09-30'))
        ->assertRedirect(route('admin.article.index'));
    $this->post(route('admin.article.store'), freeExpiryArticlePayload('Permanent Article', true, null))
        ->assertRedirect(route('admin.article.index'));
    $this->post(route('admin.article.store'), freeExpiryArticlePayload('Temporary Article', true, '2026-09-30'))
        ->assertRedirect(route('admin.article.index'));

    expect(Magazine::query()->where('title', 'Paid Magazine')->firstOrFail()->free_until)->toBeNull()
        ->and(Magazine::query()->where('title', 'Permanent Magazine')->firstOrFail()->free_until)->toBeNull()
        ->and(Magazine::query()->where('title', 'Temporary Magazine')->firstOrFail()->free_until?->toDateString())->toBe('2026-09-30')
        ->and(Article::query()->where('title', 'Paid Article')->firstOrFail()->free_until)->toBeNull()
        ->and(Article::query()->where('title', 'Permanent Article')->firstOrFail()->free_until)->toBeNull()
        ->and(Article::query()->where('title', 'Temporary Article')->firstOrFail()->free_until?->toDateString())->toBe('2026-09-30');
});

test('admin updates clear or remove expiry and retain audit ownership and activity logging', function () {
    $magazine = Magazine::query()->create([
        'language' => 'en', 'title' => 'Temporary Magazine', 'isFree' => true,
        'free_until' => '2026-09-30', 'isFeatured' => false, 'isActive' => true,
    ]);
    $article = Article::query()->create([
        'language' => 'en', 'title' => 'Temporary Article', 'article' => '<p>Body</p>',
        'isFree' => true, 'free_until' => '2026-09-30', 'isFeatured' => false, 'isActive' => true,
    ]);

    $this->actingAs($this->superAdmin)
        ->post(route('admin.magazine.update', ['id' => $magazine->id]), [
            'language' => 'en', 'title' => 'Temporary Magazine', 'isFree' => '0',
            'free_until' => '2026-10-01', 'isFeatured' => '0', 'isActive' => '1', 'is_downloadable' => '0',
        ])->assertRedirect(route('admin.magazine.index'));
    $this->post(route('admin.article.update', ['id' => $article->id]), [
        'language' => 'en', 'title' => 'Temporary Article', 'article' => '<p>Body</p>',
        'isFree' => '1', 'free_until' => null, 'isFeatured' => '0', 'isActive' => '1',
    ])->assertRedirect(route('admin.article.index'));

    expect($magazine->refresh()->isFree)->toBeFalse()
        ->and($magazine->free_until)->toBeNull()
        ->and($magazine->updated_by)->toBe($this->superAdmin->id)
        ->and($article->refresh()->isFree)->toBeTrue()
        ->and($article->free_until)->toBeNull()
        ->and($article->updated_by)->toBe($this->superAdmin->id)
        ->and(ActivityLog::query()->where('module', 'magazines')->where('subject_id', $magazine->id)->where('action', 'updated')->exists())->toBeTrue()
        ->and(ActivityLog::query()->where('module', 'articles')->where('subject_id', $article->id)->where('action', 'updated')->exists())->toBeTrue();

    $this->post(route('admin.magazine.update', ['id' => $magazine->id]), [
        'language' => 'en', 'title' => 'Temporary Magazine', 'isFree' => '1',
        'free_until' => '2026-10-05', 'isFeatured' => '0', 'isActive' => '1', 'is_downloadable' => '0',
    ])->assertRedirect(route('admin.magazine.index'));
    $this->post(route('admin.article.update', ['id' => $article->id]), [
        'language' => 'en', 'title' => 'Temporary Article', 'article' => '<p>Body</p>',
        'isFree' => '1', 'free_until' => '2026-10-06', 'isFeatured' => '0', 'isActive' => '1',
    ])->assertRedirect(route('admin.article.index'));

    expect($magazine->refresh()->isFree)->toBeTrue()
        ->and($magazine->free_until?->toDateString())->toBe('2026-10-05')
        ->and($article->refresh()->free_until?->toDateString())->toBe('2026-10-06');
});

test('expiry command converts only records whose free date is before today', function () {
    $records = collect([
        'pastMagazine' => Magazine::query()->create(['title' => 'Past Magazine', 'isFree' => true, 'free_until' => '2026-09-14']),
        'todayMagazine' => Magazine::query()->create(['title' => 'Today Magazine', 'isFree' => true, 'free_until' => '2026-09-15']),
        'futureMagazine' => Magazine::query()->create(['title' => 'Future Magazine', 'isFree' => true, 'free_until' => '2026-09-16']),
        'permanentMagazine' => Magazine::query()->create(['title' => 'Permanent Magazine', 'isFree' => true, 'free_until' => null]),
        'pastArticle' => Article::query()->create(['title' => 'Past Article', 'article' => 'Body', 'isFree' => true, 'free_until' => '2026-09-14']),
        'todayArticle' => Article::query()->create(['title' => 'Today Article', 'article' => 'Body', 'isFree' => true, 'free_until' => '2026-09-15']),
        'futureArticle' => Article::query()->create(['title' => 'Future Article', 'article' => 'Body', 'isFree' => true, 'free_until' => '2026-09-16']),
        'permanentArticle' => Article::query()->create(['title' => 'Permanent Article', 'article' => 'Body', 'isFree' => true, 'free_until' => null]),
    ]);
    $logCount = ActivityLog::query()->count();

    $this->artisan('content:expire-free')
        ->expectsOutput('Expired 1 Magazine(s) and 1 Article(s).')
        ->assertSuccessful();

    expect($records['pastMagazine']->refresh()->isFree)->toBeFalse()
        ->and($records['pastMagazine']->free_until)->toBeNull()
        ->and($records['pastArticle']->refresh()->isFree)->toBeFalse()
        ->and($records['pastArticle']->free_until)->toBeNull()
        ->and($records['todayMagazine']->refresh()->isFree)->toBeTrue()
        ->and($records['todayArticle']->refresh()->isFree)->toBeTrue()
        ->and($records['futureMagazine']->refresh()->isFree)->toBeTrue()
        ->and($records['futureArticle']->refresh()->isFree)->toBeTrue()
        ->and($records['permanentMagazine']->refresh()->isFree)->toBeTrue()
        ->and($records['permanentArticle']->refresh()->isFree)->toBeTrue()
        ->and(ActivityLog::query()->count())->toBe($logCount);
});

test('admin forms and listings expose the date control and compact free status', function () {
    $temporaryMagazine = Magazine::query()->create([
        'language' => 'en', 'title' => 'Temporary Magazine', 'isFree' => true, 'free_until' => '2026-09-30',
    ]);
    $temporaryArticle = Article::query()->create([
        'language' => 'en', 'title' => 'Temporary Article', 'article' => 'Body', 'isFree' => true, 'free_until' => '2026-09-30',
    ]);

    $this->actingAs($this->superAdmin)
        ->get(route('admin.magazine.create'))->assertSuccessful()->assertSee('Free Until')->assertSee('type="date"', false);
    $this->get(route('admin.magazine.edit', ['id' => $temporaryMagazine->id]))
        ->assertSuccessful()->assertSee('2026-09-30');
    $this->get(route('admin.article.create'))->assertSuccessful()->assertSee('Free Until')->assertSee('type="date"', false);
    $this->get(route('admin.article.edit', ['id' => $temporaryArticle->id]))
        ->assertSuccessful()->assertSee('2026-09-30');
    $this->get(route('admin.magazine.index'))->assertSuccessful()->assertSee('Until: 30 Sep 2026');
    $this->get(route('admin.article.index'))->assertSuccessful()->assertSee('Until: 30 Sep 2026');
});

test('past free until date is rejected when content is submitted as free', function () {
    $articlePayload = freeExpiryArticlePayload('Invalid Expiry', true, '2026-09-14');

    $this->actingAs($this->superAdmin)
        ->post(route('admin.article.store'), $articlePayload)
        ->assertSessionHasErrors('free_until');
});
