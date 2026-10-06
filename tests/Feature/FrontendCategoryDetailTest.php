<?php

use App\Models\Article;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Magazine;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

function detailCategory(string $name, bool $active = true): Category
{
    return Category::query()->create(['language' => 'ur', 'name' => $name, 'isActive' => $active]);
}

function categoryMagazine(string $title, string $date, array $overrides = []): Magazine
{
    return Magazine::query()->forceCreate([
        'language' => 'ur',
        'title' => $title,
        'publish_date' => $date,
        'isActive' => true,
        'status' => Magazine::STATUS_PUBLISHED,
        ...$overrides,
    ]);
}

function categoryArticle(string $title, string $date, array $overrides = []): Article
{
    return Article::query()->forceCreate([
        'language' => 'ur',
        'title' => $title,
        'article' => '<p>Full content</p>',
        'short_description' => 'مختصر تفصیل',
        'publish_date' => $date,
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
        ...$overrides,
    ]);
}

test('category detail renders active sidebar selection and defaults to magazines', function () {
    $selected = detailCategory('اسلام');
    $other = detailCategory('تعلیم');
    detailCategory('غیر فعال', false);

    $response = $this->get(route('mozu-detail', ['id' => $selected->id, 'mozuName' => 'islam']));

    $response->assertSuccessful()
        ->assertSee('صفحہ اول')
        ->assertSee('تمام موضوعات')
        ->assertSee($selected->name)
        ->assertSee($other->name)
        ->assertSee('front-category-sidebar__item active', false)
        ->assertSee('شمارے')
        ->assertSee('اس موضوع میں کوئی شمارہ دستیاب نہیں۔')
        ->assertDontSee('غیر فعال');
});

test('category detail uses the active full top banner and keeps a fallback without one', function () {
    $category = detailCategory('تعلیم');
    $bannerPath = 'images/frontend-images/banners/home-banner-one.svg';

    $fallback = $this->get(route('mozu-detail', ['id' => $category->id, 'mozuName' => 'education']));
    $fallback->assertSuccessful()
        ->assertSee('class="front-category-header "', false)
        ->assertDontSee('front-category-header__banner', false);

    Banner::query()->create([
        'language' => 'ur',
        'type' => 'full',
        'position' => 'category-detail-top-full',
        'image' => $bannerPath,
        'isActive' => true,
    ]);

    $this->get(route('mozu-detail', ['id' => $category->id, 'mozuName' => 'education']))
        ->assertSuccessful()
        ->assertSee('front-category-header--with-banner', false)
        ->assertSee($bannerPath, false);
});

test('magazines are category scoped public searchable and paginated by four complete years', function () {
    $category = detailCategory('معیشت');
    $otherCategory = detailCategory('کھیل');

    foreach ([2026, 2025, 2024, 2023, 2022] as $year) {
        $magazine = categoryMagazine("سال {$year} شمارہ", "{$year}-01-10");
        $category->magazines()->attach($magazine);
    }

    $secondInNewestYear = categoryMagazine('دوسرا 2026 شمارہ', '2026-06-10', ['issue_number' => 'M-2026002']);
    $draft = categoryMagazine('خفیہ ڈرافٹ', '2026-05-10', ['status' => Magazine::STATUS_DRAFT]);
    $unrelated = categoryMagazine('دوسرے موضوع کا شمارہ', '2026-04-10');
    $category->magazines()->attach([$secondInNewestYear->id, $draft->id]);
    $otherCategory->magazines()->attach($unrelated);

    $pageOne = $this->get(route('mozu-detail', ['id' => $category->id, 'mozuName' => 'economy']));
    $pageOne->assertSuccessful()
        ->assertSee('سال 2026 شمارہ')
        ->assertSee('دوسرا 2026 شمارہ')
        ->assertSee('سال 2023 شمارہ')
        ->assertSee('شمارہ نمبر: M-2026002')
        ->assertDontSee('سال 2022 شمارہ')
        ->assertDontSee('خفیہ ڈرافٹ')
        ->assertDontSee('دوسرے موضوع کا شمارہ')
        ->assertSee('front-category-magazines', false)
        ->assertSee('shumara-detail/'.$secondInNewestYear->id, false);

    $this->get(route('mozu-detail', [
        'id' => $category->id,
        'mozuName' => 'economy',
        'tab' => 'magazines',
        'page' => 2,
    ]))->assertSuccessful()->assertSee('سال 2022 شمارہ')->assertDontSee('سال 2026 شمارہ');

    $this->get(route('mozu-detail', [
        'id' => $category->id,
        'mozuName' => 'economy',
        'tab' => 'magazines',
        'q' => 'دوسرا',
    ]))->assertSuccessful()
        ->assertSee('دوسرا 2026 شمارہ')
        ->assertDontSee('سال 2025 شمارہ')
        ->assertSee('value="دوسرا"', false);
});

test('articles tab groups public category articles by year and uses existing detail links', function () {
    $category = detailCategory('تعلیم');
    $matching = categoryArticle('تعلیمی مضمون', '2026-02-12');
    $older = categoryArticle('پرانا مضمون', '2025-02-12');
    $inactive = categoryArticle('غیر فعال مضمون', '2026-03-12', ['isActive' => false]);
    $category->articles()->attach([$matching->id, $older->id, $inactive->id]);

    $response = $this->get(route('mozu-detail', [
        'id' => $category->id,
        'mozuName' => 'education',
        'tab' => 'articles',
        'q' => 'مضمون',
    ]));

    $response->assertSuccessful()
        ->assertSee('front-category-articles', false)
        ->assertSee('2026')
        ->assertSee('2025')
        ->assertSee($matching->title)
        ->assertSee('مزید پڑھیں')
        ->assertSee('/mazmoon/'.$matching->id.'/', false)
        ->assertSee('tab=articles', false)
        ->assertSee('value="مضمون"', false)
        ->assertDontSee($inactive->title);
});

test('inactive or non Urdu categories are not publicly accessible', function () {
    $inactive = detailCategory('بند موضوع', false);
    $english = Category::query()->create(['language' => 'en', 'name' => 'English', 'isActive' => true]);

    $this->get(route('mozu-detail', ['id' => $inactive->id, 'mozuName' => 'closed']))->assertNotFound();
    $this->get(route('mozu-detail', ['id' => $english->id, 'mozuName' => 'english']))->assertNotFound();
});
