<?php

use App\Models\About;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

uses(LazilyRefreshDatabase::class);

test('taaruf renders active Urdu sections in creation order using every configured layout', function () {
    $this->withoutVite();

    $originalPublicPath = public_path();
    $temporaryPublicPath = storage_path('framework/testing/taaruf-public-'.Str::uuid());
    app()->usePublicPath($temporaryPublicPath);

    try {
        File::ensureDirectoryExists(public_path('images/backend-images/about'));

        foreach (range(1, 5) as $number) {
            File::put(public_path('images/backend-images/about/about-'.$number.'.webp'), 'image-'.$number);
        }

        About::query()->forceCreate(['language' => 'ur', 'section_condition' => 1, 'title' => 'پہلا تعارف', 'description' => '<p><strong>معیاری صحافت</strong></p>', 'is_active' => true]);
        About::query()->forceCreate(['language' => 'ur', 'section_condition' => 2, 'image' => 'images/backend-images/about/about-1.webp', 'is_active' => true]);
        About::query()->forceCreate(['language' => 'ur', 'section_condition' => 3, 'title' => 'تیسرا تعارف', 'description' => '<p>متن پھر تصویر</p>', 'image' => 'images/backend-images/about/about-2.webp', 'is_active' => true]);
        About::query()->forceCreate(['language' => 'ur', 'section_condition' => 4, 'title' => 'چوتھا تعارف', 'description' => '<p>تصویر پھر متن</p>', 'image' => 'images/backend-images/about/about-3.webp', 'is_active' => true]);
        About::query()->forceCreate(['language' => 'ur', 'section_condition' => 5, 'title' => 'پہلا کالم', 'description' => '<p>پہلا متن</p>', 'title_2' => 'دوسرا کالم', 'description_2' => '<p>دوسرا متن</p>', 'is_active' => true]);
        About::query()->forceCreate(['language' => 'ur', 'section_condition' => 6, 'image' => 'images/backend-images/about/about-4.webp', 'image_2' => 'images/backend-images/about/about-5.webp', 'is_active' => true]);
        About::query()->forceCreate(['language' => 'ur', 'section_condition' => 1, 'title' => 'غیر فعال تعارف', 'description' => '<p>نہیں دکھنا</p>', 'is_active' => false]);
        About::query()->forceCreate(['language' => 'en', 'section_condition' => 1, 'title' => 'English About', 'description' => '<p>Hidden English section</p>', 'is_active' => true]);

        $response = $this->get(route('front.taaruf'));

        $response->assertSuccessful()
            ->assertSee('صفحہ اول')
            ->assertSee('تعارف')
            ->assertSeeInOrder(['پہلا تعارف', 'تیسرا تعارف', 'چوتھا تعارف', 'پہلا کالم'])
            ->assertSee('<strong>معیاری صحافت</strong>', false)
            ->assertSee('front-taaruf__section--1', false)
            ->assertSee('front-taaruf__section--2', false)
            ->assertSee('front-taaruf__section--3', false)
            ->assertSee('front-taaruf__section--4', false)
            ->assertSee('front-taaruf__section--5', false)
            ->assertSee('front-taaruf__section--6', false)
            ->assertDontSee('غیر فعال تعارف')
            ->assertDontSee('English About');

        expect(substr_count($response->getContent(), route('front.taaruf')))->toBeGreaterThanOrEqual(2);
    } finally {
        app()->usePublicPath($originalPublicPath);
        File::deleteDirectory($temporaryPublicPath);
    }
});

test('taaruf handles missing images and an empty active dataset without broken image markup', function () {
    $this->withoutVite();

    About::query()->forceCreate([
        'language' => 'ur',
        'section_condition' => 2,
        'image' => 'images/backend-images/about/missing.webp',
        'is_active' => true,
    ]);

    $this->get(route('front.taaruf'))
        ->assertSuccessful()
        ->assertDontSee('images/backend-images/about/missing.webp', false);

    About::query()->update(['is_active' => false]);

    $this->get(route('front.taaruf'))
        ->assertSuccessful()
        ->assertSee('تعارف کی معلومات دستیاب نہیں۔');
});
