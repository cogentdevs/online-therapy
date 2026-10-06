<?php

use App\Http\Controllers\FrontController;
use App\Models\Author;
use App\Models\Category;
use App\Models\Magazine;
use App\Models\SiteVisit;
use App\Models\Tags;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

uses(LazilyRefreshDatabase::class);

function previousIssue(string $title, string $date, array $attributes = []): Magazine
{
    return Magazine::query()->forceCreate(array_merge([
        'language' => 'ur',
        'title' => $title,
        'issue_number' => 'SQ-2026-01',
        'publish_date' => $date,
        'cover_image' => 'images/backend-images/magazines/'.$title.'.webp',
        'description' => $title.' description',
        'isActive' => true,
        'status' => Magazine::STATUS_PUBLISHED,
        'published_at' => $date.' 10:00:00',
    ], $attributes));
}

function magazineListingAnalyticsVisit(Magazine $magazine): SiteVisit
{
    return SiteVisit::query()->create([
        'visitor_id' => (string) Str::uuid(),
        'public_token_hash' => hash('sha256', Str::random(64)),
        'page_key' => 'shumara-detail',
        'route_name' => 'shumara-detail',
        'visitable_type' => $magazine->getMorphClass(),
        'visitable_id' => $magazine->id,
        'started_at' => now(),
    ]);
}

test('sabqa shumare route renders breadcrumb navbar link and magazine cards', function () {
    $this->withoutVite();
    $magazine = previousIssue('Public Urdu Issue', '2026-08-15');

    $route = Route::getRoutes()->getByName('sabqa-shumare');

    expect($route)->not->toBeNull()
        ->and($route->uri())->toBe('sabqa-shumare')
        ->and($route->getActionName())->toBe(FrontController::class.'@sabqa_shumare');

    $this->get(route('sabqa-shumare'))
        ->assertSuccessful()
        ->assertSee('صفحہ اول')
        ->assertSee('سابقہ شمارے')
        ->assertSee(route('sabqa-shumare'), false)
        ->assertSee('Public Urdu Issue')
        ->assertSee(route('shumara-detail', ['id' => $magazine->id, 'slug' => 'public-urdu-issue']), false)
        ->assertSee('15 Aug 2026')
        ->assertSee('SQ-2026-01')
        ->assertSee('Public Urdu Issue description')
        ->assertSee('col-12 col-xl-6', false)
        ->assertSee('col-lg-3', false)
        ->assertSee('col-lg-9', false);
});

test('listing includes only public Urdu issues in newest first order', function () {
    $older = previousIssue('Older Public Issue', '2025-06-01');
    $newer = previousIssue('Newer Public Issue', '2026-06-01');
    previousIssue('Draft Issue', '2026-08-01', ['status' => Magazine::STATUS_DRAFT]);
    previousIssue('Inactive Issue', '2026-08-02', ['isActive' => false]);
    previousIssue('English Issue', '2026-08-03', ['language' => 'en']);
    previousIssue('Future Issue', now()->addDay()->toDateString(), ['published_at' => now()->addDay()]);

    $response = $this->get(route('sabqa-shumare'))->assertSuccessful();
    $html = $response->getContent();

    $response->assertSee($older->title)
        ->assertSee($newer->title)
        ->assertDontSee('Draft Issue')
        ->assertDontSee('Inactive Issue')
        ->assertDontSee('English Issue')
        ->assertDontSee('Future Issue');
    expect(strpos($html, $newer->title))->toBeLessThan(strpos($html, $older->title));
});

test('listing paginates eight magazines and preserves filters', function () {
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Pagination Category', 'isActive' => true]);

    foreach (range(1, 9) as $number) {
        $magazine = previousIssue('Paginated Issue '.$number, sprintf('2026-07-%02d', $number));
        $magazine->categories()->attach($category);
    }

    $response = $this->get(route('sabqa-shumare', ['category' => $category->id]));
    $html = $response->assertSuccessful()->getContent();

    expect(substr_count($html, '<article class="front-sabqa-card">'))->toBe(8)
        ->and($html)->toContain('category='.$category->id)
        ->toContain('page=2');
});

test('category year and tag filters work alone and together without relation leakage', function () {
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Business', 'isActive' => true]);
    $otherCategory = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Culture', 'isActive' => true]);
    $tag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Sharia', 'isActive' => true]);
    $otherTag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'History', 'isActive' => true]);
    $matching = previousIssue('Combined Match', '2026-05-10');
    $matching->categories()->attach($category);
    $matching->tags()->attach($tag);
    $unrelated = previousIssue('Unrelated Issue', '2025-05-10');
    $unrelated->categories()->attach($otherCategory);
    $unrelated->tags()->attach($otherTag);

    $this->get(route('sabqa-shumare', ['category' => $category->id]))
        ->assertSee('Combined Match')->assertDontSee('Unrelated Issue');
    $this->get(route('sabqa-shumare', ['year' => 2026]))
        ->assertSee('Combined Match')->assertDontSee('Unrelated Issue');
    $this->get(route('sabqa-shumare', ['tag' => $tag->id]))
        ->assertSee('Combined Match')->assertDontSee('Unrelated Issue')
        ->assertSee('class="front-ui active"', false)
        ->assertSee(route('sabqa-shumare', ['tag' => $tag->id]), false);
    $this->get(route('sabqa-shumare', ['category' => $category->id, 'year' => 2026, 'tag' => $tag->id]))
        ->assertSee('Combined Match')->assertDontSee('Unrelated Issue');
});

test('genuine magazine authors categories years and tags populate the sidebar', function () {
    $magazine = previousIssue('Related Sidebar Issue', '2024-04-20');
    $author = Author::query()->forceCreate(['name' => 'Magazine Writer', 'isActive' => true]);
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Magazine Topic', 'isActive' => true]);
    $tag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Magazine Tag', 'isActive' => true]);
    $magazine->authors()->attach($author);
    $magazine->categories()->attach($category);
    $magazine->tags()->attach($tag);

    $this->get(route('sabqa-shumare'))
        ->assertSuccessful()
        ->assertSee('Magazine Writer')
        ->assertSee('Magazine Topic')
        ->assertSee('Magazine Tag')
        ->assertSee('2024');
});

test('mapped authors render inside their magazine row and author filter works', function () {
    $author = Author::query()->forceCreate(['name' => 'Mapped Magazine Author', 'isActive' => true]);
    $otherAuthor = Author::query()->forceCreate(['name' => 'Other Magazine Author', 'isActive' => true]);
    $matching = previousIssue('Authored Matching Issue', '2026-04-01');
    $unrelated = previousIssue('Authored Unrelated Issue', '2026-03-01');
    $matching->authors()->attach($author);
    $unrelated->authors()->attach($otherAuthor);

    $response = $this->get(route('sabqa-shumare', ['author' => $author->id]));

    $response->assertSuccessful()
        ->assertSee('Authored Matching Issue')
        ->assertSee('Mapped Magazine Author')
        ->assertDontSee('Authored Unrelated Issue');
});

test('sabqa shumare shows enabled magazine counts without creating magazine visits', function () {
    $this->withoutVite();
    $visible = previousIssue('Visible Magazine Counter', '2026-08-03', ['show_visit_counter' => true]);
    previousIssue('Hidden Magazine Counter', '2026-08-02', ['show_visit_counter' => false]);
    $other = previousIssue('Other Magazine Counter', '2026-08-01', ['show_visit_counter' => true]);
    magazineListingAnalyticsVisit($visible);
    magazineListingAnalyticsVisit($visible);
    magazineListingAnalyticsVisit($other);
    SiteVisit::query()->create([
        'visitor_id' => (string) Str::uuid(),
        'public_token_hash' => hash('sha256', Str::random(64)),
        'page_key' => 'category-detail',
        'route_name' => 'mozu-detail',
        'visitable_type' => Category::class,
        'visitable_id' => 999,
        'started_at' => now(),
    ]);
    $magazineVisitCount = SiteVisit::query()->where('visitable_type', $visible->getMorphClass())->count();

    $response = $this->get(route('sabqa-shumare'))->assertSuccessful();

    expect(substr_count($response->getContent(), 'data-magazine-visit-count'))->toBe(2)
        ->and(SiteVisit::query()->where('visitable_type', $visible->getMorphClass())->count())->toBe($magazineVisitCount);
    $response->assertSee('data-magazine-visit-count="2"', false)
        ->assertSee('Visible Magazine Counter')
        ->assertSee('Hidden Magazine Counter');
});

test('empty filters show a clean empty state', function () {
    previousIssue('Only Existing Issue', '2026-01-01');

    $this->get(route('sabqa-shumare', ['year' => 1999]))
        ->assertSuccessful()
        ->assertSee('کوئی شمارہ دستیاب نہیں۔');
});

test('search matches title description issue number and mapped author', function (string $search, string $expectedTitle) {
    $titleMatch = previousIssue('Business Search Issue', '2026-08-01');
    $descriptionMatch = previousIssue('Description Result', '2026-07-01', ['description' => 'Islamic economy research']);
    $issueMatch = previousIssue('Issue Number Result', '2026-06-01', ['issue_number' => 'SPECIAL-786']);
    $authorMatch = previousIssue('Author Result', '2026-05-01');
    $author = Author::query()->forceCreate(['name' => 'Searchable Writer', 'isActive' => true]);
    $authorMatch->authors()->attach($author);
    previousIssue('Completely Unrelated', '2026-04-01');

    $this->get(route('sabqa-shumare', ['search' => $search]))
        ->assertSuccessful()
        ->assertSee($expectedTitle)
        ->assertDontSee('Completely Unrelated')
        ->assertSee('value="'.$search.'"', false);
})->with([
    'title' => ['Business', 'Business Search Issue'],
    'description' => ['Islamic economy', 'Description Result'],
    'issue number' => ['SPECIAL-786', 'Issue Number Result'],
    'author' => ['Searchable Writer', 'Author Result'],
]);

test('search combines with all sidebar filters without leaking unrelated magazines', function () {
    $author = Author::query()->forceCreate(['name' => 'Combined Writer', 'isActive' => true]);
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Combined Category', 'isActive' => true]);
    $tag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Combined Tag', 'isActive' => true]);
    $matching = previousIssue('Combined Search Match', '2026-03-01');
    $matching->authors()->attach($author);
    $matching->categories()->attach($category);
    $matching->tags()->attach($tag);
    previousIssue('Combined Search Wrong Relations', '2026-03-02');

    $parameters = [
        'author' => $author->id,
        'category' => $category->id,
        'year' => 2026,
        'tag' => $tag->id,
        'search' => 'Combined Search',
    ];
    $response = $this->get(route('sabqa-shumare', $parameters))->assertSuccessful();

    $response->assertSee('Combined Search Match')
        ->assertDontSee('Combined Search Wrong Relations')
        ->assertSee('name="author" value="'.$author->id.'"', false)
        ->assertSee('name="category" value="'.$category->id.'"', false)
        ->assertSee('name="year" value="2026"', false)
        ->assertSee('name="tag" value="'.$tag->id.'"', false)
        ->assertDontSee('name="page"', false);

    expect($response->getContent())->toContain('search=Combined%20Search');
});

test('empty search behaves normally and pagination preserves search and filters', function () {
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Search Pagination', 'isActive' => true]);

    foreach (range(1, 9) as $number) {
        $magazine = previousIssue('Searchable Page Issue '.$number, sprintf('2026-02-%02d', $number));
        $magazine->categories()->attach($category);
    }

    $emptySearchResponse = $this->get(route('sabqa-shumare', ['search' => '   ']))->assertSuccessful();
    $searchResponse = $this->get(route('sabqa-shumare', [
        'category' => $category->id,
        'search' => 'Searchable Page',
    ]))->assertSuccessful();

    $emptySearchResponse->assertSee('Searchable Page Issue 9');
    expect($searchResponse->getContent())
        ->toContain('category='.$category->id)
        ->toContain('search=Searchable%20Page')
        ->toContain('page=2');
});
