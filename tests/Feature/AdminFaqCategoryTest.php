<?php

use App\Models\ActivityLog;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Language;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();
    $this->urdu = Language::query()->create(['name' => 'Urdu', 'code' => 'ur', 'is_active' => true]);
    $this->english = Language::query()->create(['name' => 'English', 'code' => 'en', 'is_active' => true]);
    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

afterEach(function () {
    File::deleteDirectory(public_path('images/backend-images/faq-category'));
});

function faqCategoryAdmin(array $permissions): User
{
    $role = Role::findOrCreate('faq-category-admin-'.Str::random(8), 'web');
    $role->syncPermissions(['admin.access', 'dashboard.view', ...$permissions]);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    return $admin;
}

function faqCategory(array $attributes = []): FaqCategory
{
    return FaqCategory::query()->create(array_merge(['language' => 'ur', 'name' => 'عمومی معلومات', 'icon' => null, 'isActive' => true], $attributes));
}

test('FAQ Category permissions are registered independently from existing FAQ permissions', function () {
    expect(config('admin_modules.modules.faq-categories.actions'))->toBe(['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'])
        ->and(config('admin_modules.modules.faqs.actions'))->toBe(['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete']);
});

test('Super Admin can list and create an active FAQ Category without an icon', function () {
    $this->actingAs($this->superAdmin)->get(route('admin.faq-categories.index'))->assertSuccessful();
    $this->actingAs($this->superAdmin)->post(route('admin.faq-categories.store'), ['language' => 'ur', 'name' => 'اکاؤنٹ'])->assertRedirect(route('admin.faq-categories.index'));

    $category = FaqCategory::query()->sole();
    expect($category->icon)->toBeNull()->and($category->isActive)->toBeTrue();
});

test('FAQ Category validates active language icon and language-scoped unique name', function () {
    faqCategory(['name' => 'Accounts']);
    $inactive = Language::query()->create(['name' => 'Inactive', 'code' => 'xx', 'is_active' => false]);

    $this->actingAs($this->superAdmin)->post(route('admin.faq-categories.store'), ['language' => 'ur', 'name' => 'Accounts'])->assertSessionHasErrors('name');
    $this->actingAs($this->superAdmin)->post(route('admin.faq-categories.store'), ['language' => $inactive->code, 'name' => 'Other'])->assertSessionHasErrors('language');
    $this->actingAs($this->superAdmin)->post(route('admin.faq-categories.store'), ['language' => 'en', 'name' => 'Accounts', 'icon' => 'invalid icon'])->assertSessionHasErrors('icon');
    $this->actingAs($this->superAdmin)->post(route('admin.faq-categories.store'), ['language' => 'en', 'name' => 'Accounts', 'icon' => UploadedFile::fake()->image('icon.png', 120, 80)])->assertRedirect();
});

test('FAQ Category can be edited and deactivated', function () {
    $category = faqCategory();
    $this->actingAs($this->superAdmin)->post(route('admin.faq-categories.update', $category->id), ['language' => 'ur', 'name' => 'تبدیل شدہ', 'icon' => '', 'isActive' => 0])->assertRedirect();

    expect($category->refresh()->name)->toBe('تبدیل شدہ')->and($category->isActive)->toBeFalse();
});

test('FAQ Category icon upload can be replaced and removed without fixed dimensions', function () {
    File::ensureDirectoryExists(public_path('images/backend-images/faq-category'));
    File::put(public_path('images/backend-images/faq-category/old.png'), 'old-icon');
    $category = faqCategory(['icon' => 'images/backend-images/faq-category/old.png']);

    $this->actingAs($this->superAdmin)->post(route('admin.faq-categories.update', $category->id), [
        'language' => 'ur',
        'name' => $category->name,
        'icon' => UploadedFile::fake()->image('replacement.png', 140, 90),
        'isActive' => 1,
    ])->assertRedirect();

    $replacement = $category->refresh()->icon;
    expect($replacement)->toStartWith('images/backend-images/faq-category/')->toEndWith('.png');
    expect(File::exists(public_path('images/backend-images/faq-category/old.png')))->toBeFalse()
        ->and(File::exists(public_path($replacement)))->toBeTrue();

    $this->actingAs($this->superAdmin)->post(route('admin.faq-categories.update', $category->id), [
        'language' => 'ur',
        'name' => $category->name,
        'remove_icon' => 1,
        'isActive' => 1,
    ])->assertRedirect();

    expect($category->refresh()->icon)->toBeNull();
    expect(File::exists(public_path($replacement)))->toBeFalse();
});

test('Unused category deletes but attached category is rejected without cascading FAQs', function () {
    $unused = faqCategory(['name' => 'Unused']);
    $used = faqCategory(['name' => 'Used']);
    $faq = Faq::query()->create(['language' => 'ur', 'faq_category_id' => $used->id, 'question' => 'Question?', 'answer' => 'Answer', 'isActive' => true]);

    $this->actingAs($this->superAdmin)->delete(route('admin.faq-categories.destroy', $unused->id))->assertRedirect();
    $this->actingAs($this->superAdmin)->delete(route('admin.faq-categories.destroy', $used->id))->assertSessionHasErrors('category');

    $this->assertModelMissing($unused);
    $this->assertModelExists($used);
    $this->assertModelExists($faq);
});

test('FAQ Category routes enforce each Custom Admin permission and sidebar visibility', function () {
    $viewer = faqCategoryAdmin(['faq-categories.view']);
    $this->actingAs($viewer)->get(route('admin.faq-categories.index'))->assertSuccessful();
    $this->actingAs($viewer)->get(route('admin.faq-categories.create'))->assertForbidden();
    $this->actingAs(faqCategoryAdmin([]))->get(route('admin.faq-categories.index'))->assertForbidden();

    $dashboard = $this->actingAs($viewer)->get(route('admin.dashboard'));
    $dashboard->assertSee(route('admin.faq-categories.index'), false)->assertDontSee('<li><span>Pages</span></li>', false);
});

test('FAQ Category actions use the existing Activity Log observer', function () {
    $this->actingAs($this->superAdmin)->post(route('admin.faq-categories.store'), ['language' => 'ur', 'name' => 'Audited']);
    $category = FaqCategory::query()->sole();
    $this->actingAs($this->superAdmin)->post(route('admin.faq-categories.update', $category->id), ['language' => 'ur', 'name' => 'Audited', 'isActive' => 0]);

    expect(ActivityLog::query()->where('module', 'faq_categories')->where('subject_id', $category->id)->count())->toBe(2);
});

test('New FAQ requires an active category matching its language', function () {
    $urdu = faqCategory(['name' => 'Urdu']);
    $english = faqCategory(['language' => 'en', 'name' => 'English']);
    $inactive = faqCategory(['name' => 'Inactive', 'isActive' => false]);
    $base = ['language' => 'ur', 'question' => 'سوال؟', 'answer' => 'جواب'];

    $this->actingAs($this->superAdmin)->post(route('admin.faq.store'), $base)->assertSessionHasErrors('faq_category_id');
    $this->actingAs($this->superAdmin)->post(route('admin.faq.store'), [...$base, 'faq_category_id' => $english->id])->assertSessionHasErrors('faq_category_id');
    $this->actingAs($this->superAdmin)->post(route('admin.faq.store'), [...$base, 'faq_category_id' => $inactive->id])->assertSessionHasErrors('faq_category_id');
    $this->actingAs($this->superAdmin)->post(route('admin.faq.store'), [...$base, 'faq_category_id' => $urdu->id])->assertRedirect(route('admin.faq.index'));

    $this->assertDatabaseHas('faqs', ['faq_category_id' => $urdu->id, 'language' => 'ur']);
});

test('Legacy uncategorized FAQ remains listable and must receive category before update', function () {
    $legacy = Faq::query()->create(['language' => 'ur', 'question' => 'Legacy?', 'answer' => 'Legacy answer', 'isActive' => true]);
    $category = faqCategory();

    $this->actingAs($this->superAdmin)->get(route('admin.faq.index'))->assertSuccessful()->assertSee('Legacy?')->assertSee('—');
    $this->actingAs($this->superAdmin)->get(route('admin.faq.edit', $legacy->id))->assertSuccessful();
    $payload = ['language' => 'ur', 'question' => 'Legacy?', 'answer' => 'Updated', 'isActive' => 1];
    $this->actingAs($this->superAdmin)->post(route('admin.faq.update', $legacy->id), $payload)->assertSessionHasErrors('faq_category_id');
    $this->actingAs($this->superAdmin)->post(route('admin.faq.update', $legacy->id), [...$payload, 'faq_category_id' => $category->id])->assertRedirect();
});

test('FAQ list renders the eager-loaded category and existing FAQ delete remains intact', function () {
    $category = faqCategory();
    $faq = Faq::query()->create(['language' => 'ur', 'faq_category_id' => $category->id, 'question' => 'Listed?', 'answer' => 'Yes', 'isActive' => true]);

    $this->actingAs($this->superAdmin)->get(route('admin.faq.index'))->assertSuccessful()->assertSee($category->name);
    $this->actingAs($this->superAdmin)->delete(route('admin.faq.destroy', $faq->id))->assertRedirect(route('admin.faq.index'));
    $this->assertModelMissing($faq);
});
