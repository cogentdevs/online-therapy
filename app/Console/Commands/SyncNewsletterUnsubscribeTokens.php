<?php

namespace App\Console\Commands;

use App\Models\NewsletterSubscriber;
use App\Services\NewsletterBrevoService;
use Illuminate\Console\Command;

class SyncNewsletterUnsubscribeTokens extends Command
{
    protected $signature = 'newsletter:sync-unsubscribe-tokens {--chunk=100 : Subscribers processed per database chunk}';

    protected $description = 'Generate missing Newsletter unsubscribe tokens and synchronize subscribed contacts to Brevo';

    public function handle(NewsletterBrevoService $brevo): int
    {
        $chunkSize = max(1, min(1000, (int) $this->option('chunk')));
        $failed = 0;
        $processed = 0;

        NewsletterSubscriber::query()
            ->where('status', NewsletterSubscriber::STATUS_SUBSCRIBED)
            ->orderBy('id')
            ->chunkById($chunkSize, function ($subscribers) use ($brevo, &$failed, &$processed): void {
                foreach ($subscribers as $subscriber) {
                    $subscriber->ensureUnsubscribeToken();
                    $processed++;

                    if (! $brevo->sync($subscriber)) {
                        $failed++;
                        $this->error("Failed: {$subscriber->email}");
                    }
                }
            });

        $this->info("Processed {$processed} subscribed Newsletter contact(s); {$failed} failed.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
