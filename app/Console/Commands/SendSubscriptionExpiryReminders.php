<?php

namespace App\Console\Commands;

use App\Jobs\SendSubscriptionExpiryReminderJob;
use App\Models\SubscriptionExpiryReminder;
use App\Models\SubscriptionNotificationSetting;
use App\Models\UserSubscription;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

#[Signature('subscriptions:send-expiry-reminders')]
#[Description('Send configured pre-expiry reminder emails for active user subscriptions')]
class SendSubscriptionExpiryReminders extends Command
{
    private const RESULT_QUEUED = 'queued';

    private const RESULT_SKIPPED = 'skipped';

    private const RESULT_DISPATCH_FAILED = 'dispatch_failed';

    public function handle(): int
    {
        $settings = SubscriptionNotificationSetting::query()->first();

        if ($settings === null || ! $settings->isActive) {
            $this->info('Subscription expiry reminders are disabled.');

            return self::SUCCESS;
        }

        $reminderDays = collect([
            $settings->first_reminder_days,
            $settings->second_reminder_days,
            $settings->third_reminder_days,
        ])->filter(fn (mixed $days): bool => is_numeric($days) && (int) $days > 0)
            ->map(fn (mixed $days): int => (int) $days)
            ->unique()
            ->sortDesc()
            ->values();

        if ($reminderDays->isEmpty()) {
            $this->info('No subscription expiry reminder thresholds are configured.');

            return self::SUCCESS;
        }

        $counts = ['eligible' => 0, 'queued' => 0, 'skipped' => 0, 'dispatch_failed' => 0];

        foreach ($reminderDays as $days) {
            UserSubscription::query()
                ->currentlyActive()
                ->whereNotNull('end_date')
                ->whereDate('end_date', today()->addDays($days)->toDateString())
                ->chunkById(50, function ($subscriptions) use ($days, &$counts): void {
                    foreach ($subscriptions as $subscription) {
                        $counts['eligible']++;
                        $result = $this->processReminder($subscription, $days);
                        $counts[$result]++;
                    }
                });
        }

        $this->info('Configured reminder thresholds: '.$reminderDays->implode(', '));
        $this->info("Eligible reminders: {$counts['eligible']}");
        $this->info("Queued: {$counts['queued']}");
        $this->info("Skipped already sent/queued: {$counts['skipped']}");
        $this->info("Failed to dispatch: {$counts['dispatch_failed']}");
        $this->info('Database chunk size: 50');

        return self::SUCCESS;
    }

    private function processReminder(UserSubscription $subscription, int $reminderDays): string
    {
        $now = now();

        $wasInserted = SubscriptionExpiryReminder::query()->insertOrIgnore([
            'user_subscription_id' => $subscription->getKey(),
            'reminder_days' => $reminderDays,
            'status' => SubscriptionExpiryReminder::STATUS_PENDING,
            'sent_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $reservation = DB::transaction(function () use ($subscription, $reminderDays, $wasInserted): array {
            $tracking = SubscriptionExpiryReminder::query()
                ->where('user_subscription_id', $subscription->getKey())
                ->where('reminder_days', $reminderDays)
                ->lockForUpdate()
                ->firstOrFail();

            if ($tracking->status === SubscriptionExpiryReminder::STATUS_SENT) {
                return ['result' => self::RESULT_SKIPPED, 'tracking_id' => null];
            }

            if ($tracking->status === SubscriptionExpiryReminder::STATUS_PENDING && $wasInserted === 0) {
                return ['result' => self::RESULT_SKIPPED, 'tracking_id' => null];
            }

            $tracking->update([
                'status' => SubscriptionExpiryReminder::STATUS_PENDING,
                'sent_at' => null,
            ]);

            return ['result' => self::RESULT_QUEUED, 'tracking_id' => $tracking->getKey()];
        });

        if ($reservation['result'] === self::RESULT_SKIPPED) {
            return self::RESULT_SKIPPED;
        }

        try {
            SendSubscriptionExpiryReminderJob::dispatch($reservation['tracking_id']);

            return self::RESULT_QUEUED;
        } catch (Throwable $exception) {
            SubscriptionExpiryReminder::query()
                ->whereKey($reservation['tracking_id'])
                ->where('status', SubscriptionExpiryReminder::STATUS_PENDING)
                ->update([
                    'status' => SubscriptionExpiryReminder::STATUS_FAILED,
                    'sent_at' => null,
                ]);

            Log::error('Subscription expiry reminder job dispatch failed.', [
                'subscription_expiry_reminder_id' => $reservation['tracking_id'],
                'user_subscription_id' => $subscription->getKey(),
                'exception' => $exception,
            ]);

            return self::RESULT_DISPATCH_FAILED;
        }
    }
}
