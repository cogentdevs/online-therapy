<?php

namespace Database\Seeders;

use App\Models\InfoPage;
use Illuminate\Database\Seeder;

class InfoPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ((array) config('info_pages.pages', []) as $definition) {
            foreach ((array) config('info_pages.languages', []) as $language) {
                InfoPage::query()->firstOrCreate(
                    ['language' => $language, 'page' => $definition['key']],
                    ['title' => $definition['titles'][$language], 'description' => null],
                );
            }
        }
    }
}
