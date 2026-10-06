<?php

use App\Http\Requests\Admin\StoreMembershipRequest;
use App\Http\Requests\Admin\StoreSubscriptionPlanRequest;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionProductType;

test('subscription products expose shared relationships and type constants', function () {
    $product = new ReflectionClass(SubscriptionProduct::class);
    $mapping = new ReflectionClass(SubscriptionProductType::class);

    expect(SubscriptionProduct::FOR_PLAN)->toBe('plan')
        ->and(SubscriptionProduct::FOR_MEMBERSHIP)->toBe('membership')
        ->and($product->getMethod('currency')->getReturnType()?->getName())->toBe('Illuminate\\Database\\Eloquent\\Relations\\BelongsTo')
        ->and($product->getMethod('subscriptionTypes')->getReturnType()?->getName())->toBe('Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany')
        ->and($product->getMethod('typeMappings')->getReturnType()?->getName())->toBe('Illuminate\\Database\\Eloquent\\Relations\\HasMany')
        ->and($mapping->getMethod('subscriptionProduct')->getReturnType()?->getName())->toBe('Illuminate\\Database\\Eloquent\\Relations\\BelongsTo')
        ->and($mapping->getMethod('subscriptionType')->getReturnType()?->getName())->toBe('Illuminate\\Database\\Eloquent\\Relations\\BelongsTo');
});

test('plan and membership validation enforce their different type cardinality', function () {
    $planRules = (new StoreSubscriptionPlanRequest)->rules();
    $membershipRules = (new StoreMembershipRequest)->rules();

    expect($planRules)->toHaveKey('subscription_type_id')
        ->not->toHaveKey('subscription_type_ids')
        ->and($membershipRules)->toHaveKey('subscription_type_ids')
        ->toHaveKey('subscription_type_ids.*')
        ->and($membershipRules['subscription_type_ids'])->toContain('min:1')
        ->and($planRules['duration_unit'])->toHaveCount(2)
        ->and($membershipRules['discount_value'])->toContain('min:0');
});

test('shared schema contains product discriminator and unique type mappings', function () {
    $root = dirname(__DIR__, 2);
    $productMigration = file_get_contents(glob($root.'/database/migrations/*_create_subscription_products_table.php')[0]);
    $mappingMigration = file_get_contents(glob($root.'/database/migrations/*_create_subscription_product_types_table.php')[0]);

    expect($productMigration)
        ->toContain("string('product_for')->nullable()->index()")
        ->toContain("foreignId('currency_id')->nullable()->constrained()->nullOnDelete()")
        ->toContain("unsignedBigInteger('promotion_id')->nullable()->index()")
        ->and($mappingMigration)
        ->toContain("foreignId('subscription_product_id')->constrained()->cascadeOnDelete()")
        ->toContain("unique(['subscription_product_id', 'subscription_type_id'])");
});
