<?php

use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\GeneralSetting;
use App\Models\Language;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->urdu = Language::query()->create(['name' => 'Urdu', 'code' => 'ur', 'is_default' => true, 'is_active' => true]);
    $this->english = Language::query()->create(['name' => 'English', 'code' => 'en', 'is_default' => false, 'is_active' => true]);
    GeneralSetting::query()->create(['default_language_id' => $this->urdu->id, 'email' => 'help@example.test', 'contact_1' => '03001234567']);
});

function frontendFaqCategory(array $attributes = []): FaqCategory
{
    return FaqCategory::query()->create(array_replace([
        'language' => 'ur', 'name' => 'اکاؤنٹ اور رجسٹریشن', 'icon' => null, 'isActive' => true,
    ], $attributes));
}

function frontendFaq(FaqCategory $category, array $attributes = []): Faq
{
    return Faq::query()->create(array_replace([
        'language' => $category->language,
        'faq_category_id' => $category->id,
        'question' => 'اکاؤنٹ کیسے بنائیں؟',
        'answer' => 'رجسٹریشن فارم مکمل کریں۔',
        'isActive' => true,
    ], $attributes));
}

test('FAQ page renders only active Urdu categories and active matching FAQs by id order', function () {
    $first = frontendFaqCategory(['name' => 'پہلا زمرہ']);
    $second = frontendFaqCategory(['name' => 'دوسرا زمرہ']);
    $inactiveCategory = frontendFaqCategory(['name' => 'غیر فعال زمرہ', 'isActive' => false]);
    $englishCategory = frontendFaqCategory(['language' => 'en', 'name' => 'English category']);
    $firstFaq = frontendFaq($first, ['question' => 'پہلا فعال سوال؟']);
    frontendFaq($first, ['question' => 'غیر فعال سوال؟', 'isActive' => false]);
    frontendFaq($second, ['question' => 'دوسرے زمرے کا سوال؟']);
    frontendFaq($inactiveCategory, ['question' => 'غیر فعال زمرے کا سوال؟']);
    frontendFaq($englishCategory, ['question' => 'English question?']);

    $response = $this->get(route('front.faqs'));

    $response->assertSuccessful()->assertViewIs('frontend.faq')->assertSee('dir="rtl"', false)
        ->assertSee('پہلا زمرہ')->assertSee('دوسرا زمرہ')->assertSee('پہلا فعال سوال؟')->assertSee('دوسرے زمرے کا سوال؟')
        ->assertDontSee('غیر فعال زمرہ')->assertDontSee('غیر فعال سوال؟')->assertDontSee('English category')->assertDontSee('English question?')
        ->assertSee('id="faq-category-tab-'.$first->id.'"', false)
        ->assertSee('id="faq-collapse-'.$first->id.'-'.$firstFaq->id.'" class="accordion-collapse collapse show"', false);

    $categories = $response->viewData('faqCategories');
    expect($categories->modelKeys())->toBe([$first->id, $second->id])
        ->and($categories->every(fn (FaqCategory $category): bool => $category->relationLoaded('faqs')))->toBeTrue();
});

test('FAQ page uses configured English language and LTR labels', function () {
    GeneralSetting::query()->firstOrFail()->update(['default_language_id' => $this->english->id]);
    frontendFaqCategory(['name' => 'Urdu only']);
    $englishCategory = frontendFaqCategory(['language' => 'en', 'name' => 'Account support']);
    frontendFaq($englishCategory, ['question' => 'How do I register?', 'answer' => 'Complete the registration form.']);

    $this->get(route('front.faqs'))->assertSuccessful()->assertSee('dir="ltr"', false)
        ->assertSee('Frequently Asked Questions (FAQ)')->assertSee('Account support')->assertSee('How do I register?')->assertDontSee('Urdu only');
});

test('FAQ page renders category and page empty states without requiring an icon', function () {
    frontendFaqCategory(['name' => 'خالی زمرہ', 'icon' => null]);

    $this->get(route('front.faqs'))->assertSuccessful()->assertSee('خالی زمرہ')
        ->assertSee('اس زمرے میں فی الحال کوئی سوال موجود نہیں۔')->assertDontSee('img src=""', false);

    FaqCategory::query()->delete();
    $this->get(route('front.faqs'))->assertSuccessful()->assertSee('عمومی سوالات جلد دستیاب ہوں گے');
});

test('FAQ support uses General Settings and existing Contact route', function () {
    frontendFaqCategory();

    $this->get(route('front.faqs'))->assertSuccessful()->assertSee('help@example.test')
        ->assertSee('mailto:help@example.test', false)->assertSee('03001234567')->assertSee('tel:03001234567', false)
        ->assertSee(route('front.contact'), false)->assertSee(route('frontend.home'), false);
});

test('FAQ answers remain escaped while preserving line breaks', function () {
    $category = frontendFaqCategory();
    frontendFaq($category, ['answer' => "Safe line\n<script>alert('xss')</script>"]);

    $this->get(route('front.faqs'))->assertSuccessful()->assertSee('Safe line<br />', false)
        ->assertSee('&lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;', false)->assertDontSee("<script>alert('xss')</script>", false);
});

test('FAQ page keeps shared frontend header footer and working FAQ footer link', function () {
    $this->get(route('front.faqs'))->assertSuccessful()->assertSee('class="front-header"', false)
        ->assertSee('class="front-footer"', false)->assertSee('href="'.route('front.faqs').'"', false);
});

test('FAQ page includes active-category runtime question search controls', function () {
    $category = frontendFaqCategory();
    frontendFaq($category);

    $this->get(route('front.faqs'))->assertSuccessful()
        ->assertSee('data-faq-search-root', false)
        ->assertSee('data-faq-search-input', false)
        ->assertSee('data-faq-search-clear', false)
        ->assertSee('data-faq-search-item', false)
        ->assertSee('data-faq-search-empty', false);
});
