<?php

use App\Models\About;
use App\Models\Currency;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\GeneralSetting;
use App\Models\InfoPage;
use App\Models\Language;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

function publicCatalogueLanguage(string $code = 'ur', bool $active = true): Language
{
    return Language::query()->create(['name' => strtoupper($code), 'code' => $code, 'is_active' => $active, 'is_default' => true]);
}

test('about is public read only and preserves active Urdu section order and safe fields', function () {
    $second = About::query()->create(['language' => 'ur', 'section_condition' => 3, 'title' => 'Second', 'description' => '<p>HTML</p>', 'is_active' => true]);
    $first = About::query()->forceCreate(['id' => $second->id + 10, 'language' => 'ur', 'section_condition' => 5, 'title' => 'Later', 'is_active' => true]);
    About::query()->create(['language' => 'en', 'section_condition' => 1, 'title' => 'English', 'is_active' => true]);
    About::query()->create(['language' => 'ur', 'section_condition' => 1, 'title' => 'Inactive', 'is_active' => false]);
    $count = About::query()->count();

    $this->getJson('/api/about')->assertOk()->assertJsonCount(2, 'data.sections')
        ->assertJsonPath('data.sections.0.id', $second->id)->assertJsonPath('data.sections.1.id', $first->id)
        ->assertJsonPath('data.sections.0.section_condition', 3)->assertJsonPath('data.sections.0.content_1', '<p>HTML</p>')
        ->assertJsonMissingPath('data.sections.0.created_by')->assertJsonMissing(['title' => 'English'])->assertJsonMissing(['title' => 'Inactive']);
    expect(About::query()->count())->toBe($count);
});

test('about image URLs require an existing public file and empty state is stable', function () {
    $existingImage = 'images/frontend-images/banners/home-banner-one.svg';
    About::query()->create(['language' => 'ur', 'section_condition' => 6, 'image' => $existingImage, 'image_2' => 'images/missing-about.webp', 'is_active' => true]);
    $this->getJson('/api/about')->assertOk()->assertJsonPath('data.sections.0.image_1_url', asset($existingImage))
        ->assertJsonPath('data.sections.0.image_2_url', null);
    About::query()->delete();
    $this->getJson('/api/about')->assertOk()->assertJsonCount(0, 'data.sections');
});

test('FAQ categories follow configured active language eligibility and id order', function () {
    $urdu = publicCatalogueLanguage();
    GeneralSetting::query()->create(['default_language_id' => $urdu->id]);
    $first = FaqCategory::query()->create(['language' => 'ur', 'name' => 'First', 'isActive' => true]);
    $second = FaqCategory::query()->create(['language' => 'ur', 'name' => 'Second', 'isActive' => true]);
    FaqCategory::query()->create(['language' => 'ur', 'name' => 'Inactive', 'isActive' => false]);
    FaqCategory::query()->create(['language' => 'en', 'name' => 'English', 'isActive' => true]);

    $this->getJson('/api/faq-categories')->assertOk()->assertJsonCount(2, 'data.categories')
        ->assertJsonPath('data.categories.0.id', $first->id)->assertJsonPath('data.categories.1.id', $second->id)
        ->assertJsonMissing(['name' => 'Inactive'])->assertJsonMissing(['name' => 'English'])->assertJsonMissing(['created_by' => null]);
});

test('FAQs filter by active eligible category and default to its first category', function () {
    $urdu = publicCatalogueLanguage();
    GeneralSetting::query()->create(['default_language_id' => $urdu->id]);
    $category = FaqCategory::query()->create(['language' => 'ur', 'name' => 'First', 'isActive' => true]);
    $other = FaqCategory::query()->create(['language' => 'ur', 'name' => 'Other', 'isActive' => true]);
    $first = Faq::query()->create(['language' => 'ur', 'faq_category_id' => $category->id, 'question' => 'First?', 'answer' => 'First answer', 'isActive' => true]);
    Faq::query()->create(['language' => 'ur', 'faq_category_id' => $category->id, 'question' => 'Inactive?', 'answer' => 'No', 'isActive' => false]);
    Faq::query()->create(['language' => 'ur', 'faq_category_id' => $other->id, 'question' => 'Other?', 'answer' => 'Other answer', 'isActive' => true]);

    $this->getJson('/api/faqs')->assertOk()->assertJsonPath('data.category.id', $category->id)
        ->assertJsonCount(1, 'data.faqs')->assertJsonPath('data.faqs.0.id', $first->id)->assertJsonMissing(['question' => 'Other?']);
    $this->getJson('/api/faqs?category_id='.$other->id)->assertOk()->assertJsonPath('data.category.id', $other->id)
        ->assertJsonPath('data.faqs.0.question', 'Other?');
});

test('FAQ validation and ineligible category responses are safe', function () {
    $urdu = publicCatalogueLanguage();
    GeneralSetting::query()->create(['default_language_id' => $urdu->id]);
    $inactive = FaqCategory::query()->create(['language' => 'ur', 'name' => 'Inactive', 'isActive' => false]);

    $this->getJson('/api/faqs?category_id=bad')->assertUnprocessable();
    $this->getJson('/api/faqs?category_id='.$inactive->id)->assertNotFound();
    $this->getJson('/api/faqs')->assertOk()->assertJsonPath('data.category', null)->assertJsonCount(0, 'data.faqs');
});

test('legal endpoint strictly maps supported slugs using configured language and trusted HTML', function () {
    $english = publicCatalogueLanguage('en');
    GeneralSetting::query()->create(['default_language_id' => $english->id]);
    InfoPage::query()->create(['language' => 'en', 'page' => 'privacy_policy', 'title' => 'Privacy', 'description' => '<p>Policy</p>']);
    InfoPage::query()->create(['language' => 'en', 'page' => 'terms_conditions', 'title' => 'Terms', 'description' => '<p>Terms body</p>']);
    InfoPage::query()->create(['language' => 'en', 'page' => 'disclaimer', 'title' => 'Disclaimer', 'description' => '<p>Disclaimer body</p>']);

    $this->getJson('/api/pages/privacy-policy')->assertOk()->assertJsonPath('data.page.content', '<p>Policy</p>')
        ->assertJsonPath('data.page.content_format', 'trusted_html')->assertJsonMissingPath('data.page.id');
    $this->getJson('/api/pages/terms-and-conditions')->assertOk()->assertJsonPath('data.page.title', 'Terms');
    $this->getJson('/api/pages/disclaimer')->assertOk()->assertJsonPath('data.page.title', 'Disclaimer');
    $this->getJson('/api/pages/random-page')->assertNotFound();
});

test('a supported legal page without content returns its stable page contract', function () {
    $this->getJson('/api/pages/privacy-policy')->assertOk()->assertJsonPath('data.page.slug', 'privacy-policy')
        ->assertJsonPath('data.page.language', 'ur')->assertJsonPath('data.page.content', null);
});

test('plan catalogue reuses eligibility pricing relationships and ordering', function () {
    $currency = Currency::query()->create(['name' => 'Pakistani Rupee', 'code' => 'PKR', 'symbol' => 'Rs', 'isActive' => true]);
    $type = SubscriptionType::query()->create(['name' => 'Magazine', 'slug' => 'magazine', 'isActive' => true]);
    $later = SubscriptionProduct::query()->create(['product_for' => 'plan', 'name' => 'Later', 'currency_id' => $currency->id, 'price' => 300, 'discount_type' => 'percentage', 'discount_value' => 10, 'duration_value' => 1, 'duration_unit' => 'month', 'isActive' => true]);
    $later->subscriptionTypes()->attach($type);
    $first = SubscriptionProduct::query()->create(['product_for' => 'plan', 'name' => 'First', 'currency_id' => $currency->id, 'price' => 200, 'discount_type' => 'fixed', 'discount_value' => 25, 'duration_value' => 1, 'duration_unit' => 'week', 'isActive' => true]);
    $first->subscriptionTypes()->attach($type);
    SubscriptionProduct::query()->create(['product_for' => 'plan', 'name' => 'No type', 'currency_id' => $currency->id, 'price' => 100, 'isActive' => true]);

    $this->getJson('/api/subscription-products?type=plan')->assertOk()->assertJsonCount(2, 'data.products')
        ->assertJsonPath('data.products.0.id', $first->id)->assertJsonPath('data.products.0.price', '200.00')
        ->assertJsonPath('data.products.0.discount.amount', '25.00')->assertJsonPath('data.products.0.effective_price', '175.00')
        ->assertJsonPath('data.products.0.currency.code', 'PKR')->assertJsonPath('data.products.0.duration.unit', 'week')
        ->assertJsonPath('data.products.0.subscription_types.0.slug', 'magazine')->assertJsonMissingPath('data.products.0.promotion_id');
});

test('membership catalogue returns multiple active included types and effective price', function () {
    $currency = Currency::query()->create(['name' => 'Pakistani Rupee', 'code' => 'PKR', 'symbol' => 'Rs', 'isActive' => true]);
    $articles = SubscriptionType::query()->create(['name' => 'Articles', 'slug' => 'articles', 'isActive' => true]);
    $magazines = SubscriptionType::query()->create(['name' => 'Magazines', 'slug' => 'magazines', 'isActive' => true]);
    $inactive = SubscriptionType::query()->create(['name' => 'Inactive', 'slug' => 'inactive', 'isActive' => false]);
    $membership = SubscriptionProduct::query()->create(['product_for' => 'membership', 'name' => 'Gold', 'currency_id' => $currency->id, 'price' => 500, 'discount_type' => 'percentage', 'discount_value' => 20, 'duration_value' => 3, 'duration_unit' => 'month', 'isActive' => true]);
    $membership->subscriptionTypes()->attach([$articles->id, $magazines->id, $inactive->id]);

    $this->getJson('/api/subscription-products?type=membership')->assertOk()->assertJsonCount(1, 'data.products')
        ->assertJsonPath('data.products.0.id', $membership->id)->assertJsonPath('data.products.0.effective_price', '400.00')
        ->assertJsonCount(2, 'data.products.0.included_types')->assertJsonPath('data.products.0.included_types.0.slug', 'articles')
        ->assertJsonPath('data.products.0.included_types.1.slug', 'magazines')->assertJsonMissing(['slug' => 'inactive'])
        ->assertJsonMissingPath('data.products.0.purchase_state');
});

test('catalogue validation empty states and unversioned route boundaries are stable', function () {
    $this->getJson('/api/subscription-products')->assertUnprocessable();
    $this->getJson('/api/subscription-products?type=random')->assertUnprocessable();
    $this->getJson('/api/subscription-products?type=plan')->assertOk()->assertJsonCount(0, 'data.products');
    $this->getJson('/api/subscription-products?type=membership')->assertOk()->assertJsonCount(0, 'data.products');
    $this->getJson('/api/v1/about')->assertNotFound();
    $this->getJson('/api/api/about')->assertNotFound();
});
