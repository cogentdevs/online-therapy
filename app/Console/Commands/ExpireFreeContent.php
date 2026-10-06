<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Magazine;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('content:expire-free')]
#[Description('Convert expired temporarily-free Magazines and Articles back to paid content')]
class ExpireFreeContent extends Command
{
    public function handle(): int
    {
        $today = today()->toDateString();
        $magazinesExpired = Magazine::query()
            ->where('isFree', true)
            ->whereNotNull('free_until')
            ->whereDate('free_until', '<', $today)
            ->update(['isFree' => false, 'free_until' => null]);
        $articlesExpired = Article::query()
            ->where('isFree', true)
            ->whereNotNull('free_until')
            ->whereDate('free_until', '<', $today)
            ->update(['isFree' => false, 'free_until' => null]);

        $this->info("Expired {$magazinesExpired} Magazine(s) and {$articlesExpired} Article(s).");

        return self::SUCCESS;
    }
}
