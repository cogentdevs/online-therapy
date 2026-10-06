<?php

use App\Models\SubscriptionType;
use Database\Seeders\SubscriptionTypeSeeder;

test('subscription type model exposes the system master fields', function () {
    $subscriptionType = new SubscriptionType;

    expect($subscriptionType->getFillable())
        ->toBe(['name', 'slug', 'isActive'])
        ->and($subscriptionType->getCasts()['isActive'])
        ->toBe('boolean');
});

test('subscription type migration defines nullable fields and a unique slug', function () {
    $migrationFiles = glob(dirname(__DIR__, 2).'/database/migrations/*_create_subscription_types_table.php');

    expect($migrationFiles)->toHaveCount(1);

    $migration = file_get_contents($migrationFiles[0]);

    expect($migration)
        ->toContain("string('name')->nullable()")
        ->toContain("string('slug')->nullable()->unique()")
        ->toContain("boolean('isActive')->nullable()");
});

test('subscription type seeder is idempotent and contains every system type', function () {
    $reflection = new ReflectionClass(SubscriptionTypeSeeder::class);
    $systemTypes = $reflection->getReflectionConstant('SYSTEM_TYPES')->getValue();
    $seeder = file_get_contents($reflection->getFileName());

    expect(array_column($systemTypes, 'slug'))
        ->toBe(['magazine', 'articles', 'audio', 'consultancy', 'forum'])
        ->and(array_column($systemTypes, 'name'))
        ->toBe(['Magazine', 'Articles', 'Audio Library', 'Consultancy', 'Discussion Forum'])
        ->and($seeder)
        ->toContain('SubscriptionType::updateOrCreate(')
        ->not->toContain('SubscriptionType::create(')
        ->toContain("'isActive' => true");
});
