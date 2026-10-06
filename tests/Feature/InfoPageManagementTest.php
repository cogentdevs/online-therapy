<?php

use App\Models\ActivityLog;
use App\Models\GeneralSetting;
use App\Models\InfoPage;
use App\Models\Language;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Database\Seeders\InfoPageSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();
    $this->urdu = Language::query()->create(['name' => 'Urdu', 'code' => 'ur', 'is_default' => true, 'is_active' => true]);
    $this->english = Language::query()->create(['name' => 'English', 'code' => 'en', 'is_default' => false, 'is_active' => true]);
    $this->seed(InfoPageSeeder::class);
    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

function infoPageAdmin(array $permissions): User
{
    $role = Role::findOrCreate('info-page-admin-'.Str::random(8), 'web');
    $role->syncPermissions(['admin.access', 'dashboard.view', ...$permissions]);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    return $admin;
}

test('The fixed Info Page registry creates six independent language records', function () {
    expect(InfoPage::query()->count())->toBe(6)
        ->and(InfoPage::query()->where('language', 'ur')->count())->toBe(3)
        ->and(InfoPage::query()->where('language', 'en')->count())->toBe(3);

    $this->assertDatabaseHas('info_pages', ['language' => 'ur', 'page' => 'privacy_policy', 'title' => 'رازداری پالیسی']);
    $this->assertDatabaseHas('info_pages', ['language' => 'en', 'page' => 'terms_conditions', 'title' => 'Terms & Conditions']);
});

test('Super Admin can open all three fixed editors in Urdu and English', function (string $page, string $title) {
    $this->actingAs($this->superAdmin)
        ->get(route('admin.info-pages.edit', ['page' => $page, 'language' => 'ur']))
        ->assertSuccessful()
        ->assertSee($title);

    $this->actingAs($this->superAdmin)
        ->get(route('admin.info-pages.edit', ['page' => $page, 'language' => 'en']))
        ->assertSuccessful();
})->with([
    ['privacy-policy', 'رازداری پالیسی'],
    ['terms-and-conditions', 'شرائط و ضوابط'],
    ['disclaimer', 'دستبرداری'],
]);

test('Updating Urdu Privacy changes only that record and derives its title server-side', function () {
    InfoPage::query()->where('language', 'en')->where('page', 'privacy_policy')->update(['description' => '<p>English stays.</p>']);
    InfoPage::query()->where('language', 'ur')->where('page', 'terms_conditions')->update(['description' => '<p>Terms stay.</p>']);

    $this->actingAs($this->superAdmin)->put(route('admin.info-pages.update', 'privacy-policy'), [
        'language' => 'ur',
        'title' => 'Tampered title',
        'description' => '<h2>محفوظ عنوان</h2><p>اردو متن</p>',
    ])->assertRedirect();

    $privacyUrdu = InfoPage::query()->forPage('privacy_policy', 'ur')->firstOrFail();
    expect($privacyUrdu->title)->toBe('رازداری پالیسی')
        ->and($privacyUrdu->description)->toContain('<h2>محفوظ عنوان</h2>')
        ->and(InfoPage::query()->forPage('privacy_policy', 'en')->value('description'))->toBe('<p>English stays.</p>')
        ->and(InfoPage::query()->forPage('terms_conditions', 'ur')->value('description'))->toBe('<p>Terms stay.</p>');
});

test('Dangerous rich HTML is removed while supported editor markup remains formatted', function () {
    $this->actingAs($this->superAdmin)->put(route('admin.info-pages.update', 'disclaimer'), [
        'language' => 'en',
        'description' => '<script>alert(1)</script><h2 onclick="bad()">Heading</h2><p><strong>Safe</strong> text.</p><a href="javascript:alert(1)">Link</a><table><tr><td>Cell</td></tr></table>',
    ])->assertRedirect();

    $description = InfoPage::query()->forPage('disclaimer', 'en')->value('description');
    expect($description)->not->toContain('<script')->not->toContain('onclick')->not->toContain('javascript:')
        ->and($description)->toContain('<strong>Safe</strong>')->toContain('<table>');
});

test('Unsupported page and language are rejected', function () {
    $this->actingAs($this->superAdmin)->get('/admin/info-pages/arbitrary/edit?language=ur')->assertNotFound();
    $this->actingAs($this->superAdmin)->get(route('admin.info-pages.edit', ['page' => 'privacy-policy', 'language' => 'fr']))->assertNotFound();
});

test('Custom Admin permissions remain page-specific and update is independently protected', function () {
    $privacyViewer = infoPageAdmin(['privacy-policy.view']);

    $this->actingAs($privacyViewer)->get(route('admin.info-pages.edit', ['page' => 'privacy-policy', 'language' => 'ur']))->assertSuccessful();
    $this->actingAs($privacyViewer)->get(route('admin.info-pages.edit', ['page' => 'terms-and-conditions', 'language' => 'ur']))->assertForbidden();
    $this->actingAs($privacyViewer)->put(route('admin.info-pages.update', 'privacy-policy'), ['language' => 'ur', 'description' => '<p>Denied</p>'])->assertForbidden();

    $privacyEditor = infoPageAdmin(['privacy-policy.view', 'privacy-policy.edit']);
    $this->actingAs($privacyEditor)->put(route('admin.info-pages.update', 'privacy-policy'), ['language' => 'ur', 'description' => '<p>Allowed</p>'])->assertRedirect();
});

test('Sidebar links follow page-specific permissions and Super Admin sees all three', function () {
    $privacyAdmin = infoPageAdmin(['privacy-policy.view']);
    $response = $this->actingAs($privacyAdmin)->get(route('admin.dashboard'));
    $response->assertSee(route('admin.info-pages.edit', ['page' => 'privacy-policy', 'language' => 'ur']), false)
        ->assertDontSee(route('admin.info-pages.edit', ['page' => 'terms-and-conditions', 'language' => 'ur']), false)
        ->assertDontSee(route('admin.info-pages.edit', ['page' => 'disclaimer', 'language' => 'ur']), false);

    $this->actingAs($this->superAdmin)->get(route('admin.dashboard'))
        ->assertSee('Privacy Policy')->assertSee('Terms &amp; Conditions', false)->assertSee('Disclaimer');
});

test('Admin updates are captured by the existing Activity Log observer', function () {
    $this->actingAs($this->superAdmin)->put(route('admin.info-pages.update', 'privacy-policy'), ['language' => 'ur', 'description' => '<p>Audited</p>']);

    expect(ActivityLog::query()->where('module', 'info_pages')->where('action', 'updated')->count())->toBe(1);
});

test('Public fixed routes render current configured language, rich HTML, direction and footer links', function () {
    GeneralSetting::query()->create(['default_language_id' => $this->urdu->id]);
    InfoPage::query()->forPage('privacy_policy', 'ur')->update(['description' => '<h2>رازداری کا متن</h2><ul><li>نکتہ</li></ul>']);

    $this->get(route('front.privacy-policy'))->assertSuccessful()
        ->assertSee('dir="rtl"', false)
        ->assertSee('<h2>رازداری کا متن</h2>', false)
        ->assertSee(route('front.privacy-policy'), false)
        ->assertSee(route('front.terms-and-conditions'), false)
        ->assertSee(route('front.disclaimer'), false);

    GeneralSetting::query()->firstOrFail()->update(['default_language_id' => $this->english->id]);
    InfoPage::query()->forPage('terms_conditions', 'en')->update(['description' => '<p>English terms.</p>']);
    $this->get(route('front.terms-and-conditions'))->assertSuccessful()
        ->assertSee('dir="ltr"', false)
        ->assertSee('<p>English terms.</p>', false)
        ->assertSee('Home');
});

test('Empty localized content has a clean language-specific empty state', function () {
    GeneralSetting::query()->create(['default_language_id' => $this->urdu->id]);

    $this->get(route('front.disclaimer'))->assertSuccessful()->assertSee('اس صفحے کی معلومات جلد دستیاب ہوں گی۔');
});
