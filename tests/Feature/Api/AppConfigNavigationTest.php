<?php

use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\Language;
use App\Models\MetaTag;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('app config is public and explicitly exposes public safe settings with asset urls', function () {
    $urdu = Language::query()->create([
        'name' => 'Urdu',
        'code' => 'ur',
        'is_default' => true,
        'is_active' => true,
    ]);
    Language::query()->create([
        'name' => 'Inactive',
        'code' => 'xx',
        'is_default' => false,
        'is_active' => false,
    ]);
    GeneralSetting::query()->create([
        'app_name' => 'Digital Magazine',
        'url' => 'https://magazine.example.com',
        'logo' => 'images/backend-images/logo/header.webp',
        'footer_logo' => 'images/backend-images/logo/footer.webp',
        'favicon' => 'images/backend-images/logo/favicon.png',
        'footer_text' => 'Trusted magazine content.',
        'contact_1' => '03001234567',
        'contact_2' => '0211234567',
        'email' => 'hello@example.com',
        'address' => 'Karachi, Pakistan',
        'facebook' => 'https://facebook.com/example',
        'x' => 'https://x.com/example',
        'instagram' => 'https://instagram.com/example',
        'youtube' => 'https://youtube.com/@example',
        'linkedin' => 'https://linkedin.com/company/example',
        'tiktok' => 'https://tiktok.com/@example',
        'app_section_heading' => 'Download our app',
        'app_section_text' => 'Read anywhere.',
        'play_store_icon' => 'images/backend-images/apps/play.webp',
        'play_store_link' => 'https://play.google.com/store/apps/details?id=example',
        'app_store_icon' => 'images/backend-images/apps/apple.webp',
        'app_store_link' => 'https://apps.apple.com/app/example',
        'default_language_id' => $urdu->id,
        'google_ads_client_id' => 'ca-pub-1234567890123456',
        'max_devices_per_user' => 4,
        'max_concurrent_sessions' => 2,
        'maintenance_mode' => true,
    ]);

    $response = $this->getJson('/api/app/config');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.app.name', 'Digital Magazine')
        ->assertJsonPath('data.branding.header_logo_url', asset('images/backend-images/logo/header.webp'))
        ->assertJsonPath('data.branding.footer_logo_url', asset('images/backend-images/logo/footer.webp'))
        ->assertJsonPath('data.branding.favicon_url', asset('images/backend-images/logo/favicon.png'))
        ->assertJsonPath('data.contact.email', 'hello@example.com')
        ->assertJsonPath('data.social_links.linkedin', 'https://linkedin.com/company/example')
        ->assertJsonPath('data.app_stores.play_store.icon_url', asset('images/backend-images/apps/play.webp'))
        ->assertJsonPath('data.languages.default.code', 'ur')
        ->assertJsonCount(1, 'data.languages.active');

    expect($response->json('data'))->not->toHaveKeys([
        'google_ads_client_id',
        'max_devices_per_user',
        'max_concurrent_sessions',
        'maintenance_mode',
        'cookie_consent_enabled',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ]);
    expect($response->getContent())
        ->not->toContain('ca-pub-1234567890123456')
        ->not->toContain('max_devices_per_user')
        ->not->toContain('maintenance_mode');
});

test('app config handles missing settings and optional values without authentication', function () {
    $this->getJson('/api/app/config')
        ->assertOk()
        ->assertExactJson([
            'success' => true,
            'data' => [
                'app' => ['name' => null, 'website_url' => null],
                'branding' => [
                    'header_logo_url' => null,
                    'footer_logo_url' => null,
                    'favicon_url' => null,
                    'footer_text' => null,
                ],
                'contact' => [
                    'email' => null,
                    'primary_phone' => null,
                    'secondary_phone' => null,
                    'address' => null,
                ],
                'social_links' => [
                    'facebook' => null,
                    'x' => null,
                    'instagram' => null,
                    'youtube' => null,
                    'linkedin' => null,
                    'tiktok' => null,
                ],
                'app_stores' => [
                    'heading' => null,
                    'text' => null,
                    'play_store' => ['icon_url' => null, 'link' => null],
                    'app_store' => ['icon_url' => null, 'link' => null],
                ],
                'languages' => ['default' => null, 'active' => []],
            ],
        ]);
});

test('navigation preserves active Urdu id ordering meta slugs and footer first five rule', function () {
    $categories = collect(range(1, 7))->map(fn (int $number): Category => Category::query()->create([
        'language' => 'ur',
        'name' => 'Category '.$number,
        'isActive' => true,
    ]));
    Category::query()->create(['language' => 'ur', 'name' => 'Inactive', 'isActive' => false]);
    Category::query()->create(['language' => 'en', 'name' => 'English', 'isActive' => true]);
    Category::query()->create(['language' => 'ur', 'name' => '', 'isActive' => true]);
    MetaTag::query()->create([
        'table_name' => (new Category)->getTable(),
        'table_id' => $categories[1]->id,
        'language' => 'ur',
        'slug_url' => 'custom-category-slug',
    ]);

    $response = $this->getJson('/api/navigation');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(7, 'data.navigation_categories')
        ->assertJsonCount(5, 'data.footer_categories')
        ->assertJsonPath('data.navigation_categories.0.id', $categories[0]->id)
        ->assertJsonPath('data.navigation_categories.1.slug', 'custom-category-slug')
        ->assertJsonPath('data.navigation_categories.1.web_url', route('mozu-detail', [
            'id' => $categories[1]->id,
            'mozuName' => 'custom-category-slug',
        ]))
        ->assertJsonMissing(['name' => 'Inactive'])
        ->assertJsonMissing(['name' => 'English']);

    expect(collect($response->json('data.navigation_categories'))->pluck('id')->all())
        ->toBe($categories->pluck('id')->all());
    expect(collect($response->json('data.footer_categories'))->pluck('id')->all())
        ->toBe($categories->take(5)->pluck('id')->all());
});

test('navigation is public and returns empty collections safely', function () {
    $this->getJson('/api/navigation')
        ->assertOk()
        ->assertExactJson([
            'success' => true,
            'data' => [
                'navigation_categories' => [],
                'footer_categories' => [],
            ],
        ]);
});

test('chunk one routes use one api prefix without version prefixes', function () {
    expect(route('api.app.config', absolute: false))->toBe('/api/app/config')
        ->and(route('api.navigation', absolute: false))->toBe('/api/navigation');

    $this->getJson('/api/v1/app/config')->assertNotFound();
    $this->getJson('/api/api/app/config')->assertNotFound();
});

test('existing frontend home route continues to render after shared data extraction', function () {
    $this->withoutVite();

    $this->get(route('frontend.home'))->assertOk();
});
