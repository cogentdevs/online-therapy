<?php

namespace App\Console\Commands;

use App\Models\Ad;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('ads:expire')]
#[Description('Deactivate active advertisements whose expiry date has passed')]
class ExpireAds extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredAds = Ad::query()
            ->where('isActive', true)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', today()->toDateString())
            ->update(['isActive' => false]);

        $this->info("Expired {$expiredAds} advertisement(s).");

        return self::SUCCESS;
    }
}
