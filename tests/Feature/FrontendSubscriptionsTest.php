<?php

use App\Models\Currency;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders active plans under dynamic non-empty type tabs and active memberships', function () {
    $currency = Currency::query()->create([
        'name' => 'Pakistani Rupee',
        'code' => 'PKR',
        'symbol' => 'Rs',
        'isActive' => true,
    ]);
    $magazine = SubscriptionType::query()->create(['name' => 'Magazine', 'slug' => 'magazine', 'isActive' => true]);
    $articles = SubscriptionType::query()->create(['name' => 'Articles', 'slug' => 'articles', 'isActive' => true]);
    SubscriptionType::query()->create(['name' => 'Audio', 'slug' => 'audio', 'isActive' => true]);
    $podcast = SubscriptionType::query()->create(['name' => 'Podcast', 'slug' => 'podcast', 'isActive' => true]);
    $emptyType = SubscriptionType::query()->create(['name' => 'خالی قسم', 'slug' => 'empty', 'isActive' => true]);
    $inactiveType = SubscriptionType::query()->create(['name' => 'غیر فعال قسم', 'slug' => 'inactive', 'isActive' => false]);

    $plan = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_PLAN,
        'name' => 'ماہانہ منصوبہ',
        'currency_id' => $currency->id,
        'price' => 300,
        'discount_type' => 'percentage',
        'discount_value' => 10,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    $plan->subscriptionTypes()->attach([$magazine->id, $articles->id, $podcast->id]);

    $hiddenPlan = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_PLAN,
        'name' => 'غیر فعال منصوبہ',
        'currency_id' => $currency->id,
        'price' => 100,
        'duration_value' => 1,
        'duration_unit' => 'day',
        'isActive' => false,
    ]);
    $hiddenPlan->subscriptionTypes()->attach($inactiveType);

    $membership = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_MEMBERSHIP,
        'name' => 'گولڈ رکنیت',
        'currency_id' => $currency->id,
        'price' => 750,
        'discount_type' => 'fixed',
        'discount_value' => 50,
        'duration_value' => 3,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    $membership->subscriptionTypes()->attach([$magazine->id, $articles->id]);

    $this->get(route('front.subscriptions'))
        ->assertOk()
        ->assertViewIs('frontend.subscriptions')
        ->assertSee('href="'.route('front.subscriptions').'"', false)
        ->assertSee('ماہانہ منصوبہ')
        ->assertSee('شمارہ منصوبہ')
        ->assertSee('1 ماہ کے لیے مکمل رسائی')
        ->assertSee('PKR 300', false)
        ->assertSee('PKR 270', false)
        ->assertSee('10% رعایت')
        ->assertSee('گولڈ رکنیت')
        ->assertSee('PKR 750', false)
        ->assertSee('PKR 700', false)
        ->assertSee('PKR 50 رعایت')
        ->assertSee('fa-crown', false)
        ->assertSee('مضمون منصوبہ')
        ->assertSee('ہفتہ وار میگزین')
        ->assertSee('مضامین')
        ->assertSee('آڈیو')
        ->assertSee('fa-book-open', false)
        ->assertSee('fa-file-lines', false)
        ->assertSee('fa-headphones', false)
        ->assertSee('Podcast')
        ->assertSeeInOrder([
            'ماہانہ منصوبہ',
            '1 ماہ کے لیے مکمل رسائی',
            'fa-calendar-check',
            'PKR 300',
            'مدت: 1 ماہ',
            'subscription-plan-card__divider',
            'ابھی سبسکرائب کریں',
        ], false)
        ->assertSeeInOrder([
            'گولڈ رکنیت',
            '3 ماہ کے لیے منتخب ماڈیولز تک رسائی',
            'PKR 750',
            '3 ماہ کی مدت',
            'شامل ماڈیولز',
        ], false)
        ->assertDontSee('plan-type-'.$emptyType->id.'-tab', false)
        ->assertDontSee('غیر فعال منصوبہ')
        ->assertDontSee('غیر فعال قسم');
});

it('renders safe empty states when no active subscription products exist', function () {
    $this->get(route('front.subscriptions'))
        ->assertOk()
        ->assertSee('فی الحال کوئی فعال منصوبہ موجود نہیں ہے۔')
        ->assertSee('فی الحال کوئی فعال رکنیت موجود نہیں ہے۔');
});

it('renders only active videos assigned to each subscription plan', function () {
    $currency = Currency::query()->create([
        'name' => 'Pakistani Rupee',
        'code' => 'PKR',
        'symbol' => 'Rs',
        'isActive' => true,
    ]);
    $type = SubscriptionType::query()->create(['name' => 'Video', 'slug' => 'video', 'isActive' => true]);
    $plan = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_PLAN,
        'name' => 'Video Plan',
        'currency_id' => $currency->id,
        'price' => 300,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    $plan->subscriptionTypes()->attach($type);

    $activeVideo = Video::query()->create([
        'title' => 'Active Included Video',
        'video_link' => 'https://example.com/active-video',
        'short_description' => 'An active video.',
        'is_active' => true,
        'status' => Video::STATUS_PUBLISHED,
    ]);
    $inactiveVideo = Video::query()->create([
        'title' => 'Inactive Included Video',
        'video_link' => 'https://example.com/inactive-video',
        'short_description' => 'An inactive video.',
        'is_active' => false,
        'status' => Video::STATUS_PUBLISHED,
    ]);
    $draftVideo = Video::query()->create([
        'title' => 'Draft Included Video',
        'video_link' => 'https://example.com/draft-video',
        'short_description' => 'A draft video.',
        'is_active' => true,
        'status' => Video::STATUS_DRAFT,
    ]);
    $plan->videos()->attach([$activeVideo->id, $inactiveVideo->id, $draftVideo->id]);
    $otherPlan = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_PLAN,
        'name' => 'Other Video Plan',
        'currency_id' => $currency->id,
        'price' => 400,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    $otherPlan->subscriptionTypes()->attach($type);
    $otherPlanVideo = Video::query()->create([
        'title' => 'Other Plan Published Video',
        'video_link' => 'https://example.com/other-plan-video',
        'short_description' => 'Another published video.',
        'is_active' => true,
        'status' => Video::STATUS_PUBLISHED,
    ]);
    $otherPlan->videos()->attach($otherPlanVideo->id);
    $emptyPlan = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_PLAN,
        'name' => 'No Eligible Videos Plan',
        'currency_id' => $currency->id,
        'price' => 500,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    $emptyPlan->subscriptionTypes()->attach($type);

    $response = $this->get(route('front.subscriptions'))
        ->assertOk()
        ->assertSee('Included Videos')
        ->assertSee('Active Included Video')
        ->assertDontSee('Inactive Included Video')
        ->assertDontSee('Draft Included Video')
        ->assertSee('Other Plan Published Video')
        ->assertSeeInOrder([
            'Video Plan',
            'Active Included Video',
            'subscription-plan-card__divider',
            'Other Video Plan',
            'Other Plan Published Video',
            'subscription-plan-card__divider',
            'No Eligible Videos Plan',
            'subscription-plan-card__divider',
        ], false);

    expect(substr_count($response->getContent(), '<h3>Included Videos</h3>'))->toBe(2);
});
