<?php

namespace Database\Seeders;

use App\Models\AuthorGeneralSetting;
use Illuminate\Database\Seeder;

class AuthorGeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (AuthorGeneralSetting::DEFAULT_VISIBILITY as $columnName => $isView) {
            AuthorGeneralSetting::query()->firstOrCreate(
                ['column_name' => $columnName],
                ['isView' => $isView],
            );
        }
    }
}
