<?php

namespace Database\Seeders;

use App\Models\SubscriptionType;
use Illuminate\Database\Seeder;

class SubscriptionTypeSeeder extends Seeder
{
    /** @var array<int, array{name: string, slug: string}> */
    private const SYSTEM_TYPES = [
        ['name' => 'Magazine', 'slug' => 'magazine'],
        ['name' => 'Articles', 'slug' => 'articles'],
        ['name' => 'Audio Library', 'slug' => 'audio'],
        ['name' => 'Consultancy', 'slug' => 'consultancy'],
        ['name' => 'Discussion Forum', 'slug' => 'forum'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::SYSTEM_TYPES as $subscriptionType) {
            SubscriptionType::updateOrCreate(
                ['slug' => $subscriptionType['slug']],
                [
                    'name' => $subscriptionType['name'],
                    'isActive' => true,
                ],
            );
        }
    }
}
