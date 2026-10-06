<?php

namespace Database\Seeders;

use App\Models\StorageProvider;
use Illuminate\Database\Seeder;

class StorageProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                'name' => 'Local Server',
                'slug' => 'local',
                'disk' => 'local_media',
                'provider_type' => StorageProvider::TYPE_LOCAL,
                'is_active' => true,
                'is_default' => true,
                'priority' => 1,
                'health_status' => StorageProvider::HEALTH_UNKNOWN,
            ],
            [
                'name' => 'Cloudflare R2',
                'slug' => 'r2',
                'disk' => 'r2',
                'provider_type' => StorageProvider::TYPE_R2,
                'is_active' => false,
                'is_default' => false,
                'priority' => 2,
                'health_status' => StorageProvider::HEALTH_UNKNOWN,
            ],
            [
                'name' => 'Amazon S3',
                'slug' => 's3',
                'disk' => 's3',
                'provider_type' => StorageProvider::TYPE_S3,
                'is_active' => false,
                'is_default' => false,
                'priority' => 3,
                'health_status' => StorageProvider::HEALTH_UNKNOWN,
            ],
        ];

        foreach ($providers as $provider) {
            StorageProvider::query()->updateOrCreate(
                ['slug' => $provider['slug']],
                $provider,
            );
        }
    }
}
