<?php

use App\Models\Article;
use App\Models\Author;
use App\Models\AuthorGeneralSetting;
use App\Models\Category;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

function frontendAuthor(array $attributes = []): Author
{
    return Author::query()->forceCreate([
        'name' => 'واجہت شیخ',
        'contact_number' => '03001234567',
        'email' => 'writer@example.test',
        'qualification' => 'ایم اے اسلامیات',
        'experience_detail' => 'تحقیق اور تدریس کا وسیع تجربہ',
        'experience_years' => 12,
        'speciality' => 'اسلامی معیشت',
        'picture' => 'images/backend-images/author/author.png',
        'isActive' => true,
        ...$attributes,
    ]);
}

test('authors page lists only active authors eight per page and navbar footer link to it', function () {
    collect(range(1, 10))->each(fn (int $number): Author => frontendAuthor(['name' => 'مضمون نگار '.$number]));
    frontendAuthor(['name' => 'غیر فعال مصنف', 'isActive' => false]);

    $response = $this->get(route('front.mazmoon-nigaar'));

    $response->assertSuccessful()
        ->assertSee('مضمون نگار')
        ->assertSee(route('front.mazmoon-nigaar'), false)
        ->assertSee('پروفائل دیکھیں')
        ->assertDontSee('غیر فعال مصنف');

    expect(substr_count($response->getContent(), 'class="front-author-card"'))->toBe(8);

    $this->get(route('front.mazmoon-nigaar', ['source' => 'footer', 'page' => 2]))
        ->assertSuccessful()
        ->assertSee('source=footer', false);
});

test('author image name and CTA link to a public visibility-aware profile', function () {
    $author = frontendAuthor(['name' => 'نیبل احمد']);
    $profileUrl = route('front.mazmoon-nigaar.detail', ['id' => $author->id, 'slug' => 'نیبل-احمد']);

    $listing = $this->get(route('front.mazmoon-nigaar'));
    expect(substr_count($listing->getContent(), 'href="'.$profileUrl.'"'))->toBe(3);

    $this->get($profileUrl)
        ->assertSuccessful()
        ->assertSee('نیبل احمد')
        ->assertSee('اسلامی معیشت');
});

test('inactive author profile is not public and hidden name is not exposed in its listing URL', function () {
    $inactiveAuthor = frontendAuthor(['name' => 'غیر فعال پروفائل', 'isActive' => false]);
    $hiddenNameAuthor = frontendAuthor(['name' => 'خفیہ نام']);
    $hiddenNameAuthor->visibilities()->create(['column_name' => 'name', 'isView' => false]);

    $this->get(route('front.mazmoon-nigaar.detail', ['id' => $inactiveAuthor->id, 'slug' => 'inactive']))
        ->assertNotFound();

    $this->get(route('front.mazmoon-nigaar'))
        ->assertSuccessful()
        ->assertDontSee('خفیہ نام')
        ->assertSee('/mazmoon-nigaar/'.$hiddenNameAuthor->id.'/author-'.$hiddenNameAuthor->id, false);
});

test('author cards apply global defaults and per-author visibility overrides without exposing hidden fields', function () {
    AuthorGeneralSetting::query()->forceCreate(['column_name' => 'email', 'isView' => false]);
    AuthorGeneralSetting::query()->forceCreate(['column_name' => 'qualification', 'isView' => true]);
    AuthorGeneralSetting::query()->forceCreate(['column_name' => 'picture', 'isView' => true]);

    $author = frontendAuthor();
    $author->visibilities()->createMany([
        ['column_name' => 'qualification', 'isView' => false],
        ['column_name' => 'email', 'isView' => true],
        ['column_name' => 'picture', 'isView' => false],
        ['column_name' => 'contact_number', 'isView' => false],
    ]);

    $response = $this->get(route('front.mazmoon-nigaar'));

    $response->assertSuccessful()
        ->assertSee('writer@example.test')
        ->assertDontSee('ایم اے اسلامیات')
        ->assertDontSee('03001234567')
        ->assertDontSee('front-author-card__media', false);
});

test('allowed missing author picture uses fallback and search covers name speciality and qualification', function () {
    frontendAuthor([
        'name' => 'عائشہ خان',
        'speciality' => 'تعلیم',
        'qualification' => 'پی ایچ ڈی',
        'picture' => 'images/backend-images/author/missing-picture.png',
    ]);
    frontendAuthor(['name' => 'دوسرے مصنف', 'speciality' => 'صحت']);

    $bySpeciality = $this->get(route('front.mazmoon-nigaar', ['q' => 'تعلیم']));
    $bySpeciality->assertSuccessful()
        ->assertSee('عائشہ خان')
        ->assertDontSee('دوسرے مصنف')
        ->assertSee(asset('images/backend-images/author/author.png'), false);

    $this->get(route('front.mazmoon-nigaar', ['q' => 'پی ایچ ڈی']))
        ->assertSuccessful()
        ->assertSee('عائشہ خان');
});

test('authors page shows the correct empty state for an unmatched search', function () {
    frontendAuthor();

    $this->get(route('front.mazmoon-nigaar', ['q' => 'ناموجود']))
        ->assertSuccessful()
        ->assertSee('آپ کی تلاش کے مطابق کوئی مضمون نگار نہیں ملا۔');
});

function publishedAuthorArticle(Author $author, Category $category, string $publishDate, array $attributes = []): Article
{
    $article = Article::query()->forceCreate([
        'language' => 'ur',
        'title' => 'علمی مضمون',
        'publish_date' => $publishDate,
        'short_description' => 'تعلیم اور تربیت پر تحقیق',
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
        'published_at' => now()->subDay(),
        ...$attributes,
    ]);

    $article->authors()->attach($author);
    $article->categories()->attach($category);

    return $article;
}

test('author detail combines category year and search filters and keeps their pagination state', function () {
    $author = frontendAuthor(['name' => 'واجد شیخ']);
    $otherAuthor = frontendAuthor(['name' => 'دوسرا مصنف']);
    $education = Category::query()->forceCreate(['language' => 'ur', 'name' => 'تعلیم', 'isActive' => true]);
    $health = Category::query()->forceCreate(['language' => 'ur', 'name' => 'صحت', 'isActive' => true]);

    collect(range(1, 10))->each(fn (int $number): Article => publishedAuthorArticle(
        $author,
        $education,
        '2025-'.str_pad((string) min($number, 9), 2, '0', STR_PAD_LEFT).'-01',
        ['title' => 'تعلیم مضمون '.$number],
    ));
    publishedAuthorArticle($author, $health, '2024-06-01', ['title' => 'صحت مضمون']);
    publishedAuthorArticle($otherAuthor, $education, '2025-06-01', ['title' => 'غیر متعلق مضمون']);

    $url = route('front.mazmoon-nigaar.detail', ['id' => $author->id, 'slug' => 'واجد-شیخ']);
    $response = $this->get($url.'?category='.$education->id.'&year=2025&q=تعلیم');

    $response->assertSuccessful()
        ->assertSee('واجد شیخ کے مضامین')
        ->assertSee('کل مضامین (10)')
        ->assertSee('تعلیم مضمون 10')
        ->assertDontSee('صحت مضمون')
        ->assertDontSee('غیر متعلق مضمون')
        ->assertSee('category='.$education->id, false)
        ->assertSee('year=2025', false)
        ->assertSee('q=%D8%AA%D8%B9%D9%84%DB%8C%D9%85', false);

    expect($response->viewData('categories')->firstWhere('id', $education->id)->author_articles_count)->toBe(10)
        ->and((int) $response->viewData('years')->firstWhere('publication_year', '2025')->article_count)->toBe(10)
        ->and($response->viewData('articles')->perPage())->toBe(9);
});

test('author detail excludes non-public articles and never exposes hidden profile fields', function () {
    AuthorGeneralSetting::query()->forceCreate(['column_name' => 'email', 'isView' => true]);
    $author = frontendAuthor(['email' => 'private-author@example.test']);
    $author->visibilities()->create(['column_name' => 'email', 'isView' => false]);
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'اسلام', 'isActive' => true]);

    publishedAuthorArticle($author, $category, now()->subMonth()->toDateString(), ['title' => 'عوامی مضمون']);
    publishedAuthorArticle($author, $category, now()->subMonth()->toDateString(), ['title' => 'مسودہ', 'status' => Article::STATUS_DRAFT]);
    publishedAuthorArticle($author, $category, now()->addMonth()->toDateString(), ['title' => 'مستقبل کا مضمون']);

    $response = $this->get(route('front.mazmoon-nigaar.detail', ['id' => $author->id, 'slug' => 'author']));

    $response->assertSuccessful()
        ->assertSee('عوامی مضمون')
        ->assertDontSee('مسودہ')
        ->assertDontSee('مستقبل کا مضمون')
        ->assertDontSee('private-author@example.test');

    expect($response->viewData('articles')->total())->toBe(1)
        ->and($response->viewData('categories')->first()->author_articles_count)->toBe(1);
});
