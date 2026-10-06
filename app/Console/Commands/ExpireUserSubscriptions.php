<?php

namespace App\Console\Commands;

use App\Jobs\SendSubscriptionExpiredNotificationJob;
use App\Models\SubscriptionExpiryNotification;
use App\Models\UserSubscription;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

#[Signature('subscriptions:expire')]
#[Description('Deactivate expired user subscriptions while preserving purchase history and entitlement snapshots')]
class ExpireUserSubscriptions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredSubscriptions = 0;
        $queuedNotifications = 0;
        $dispatchFailures = 0;

        $this->expiryEligibleQuery()->chunkById(50, function ($subscriptions) use (
            &$expiredSubscriptions,
            &$queuedNotifications,
            &$dispatchFailures,
        ): void {
            foreach ($subscriptions as $subscription) {
                $result = $this->expireAndReserveNotification($subscription);

                if (! $result['expired']) {
                    continue;
                }

                $expiredSubscriptions++;

                if ($result['notification_id'] === null) {
                    continue;
                }

                try {
                    SendSubscriptionExpiredNotificationJob::dispatch($result['notification_id']);
                    $queuedNotifications++;
                } catch (Throwable $exception) {
                    SubscriptionExpiryNotification::query()
                        ->whereKey($result['notification_id'])
                        ->where('status', SubscriptionExpiryNotification::STATUS_PENDING)
                        ->update([
                            'status' => SubscriptionExpiryNotification::STATUS_FAILED,
                            'sent_at' => null,
                        ]);

                    Log::error('Expired subscription notification job dispatch failed.', [
                        'subscription_expiry_notification_id' => $result['notification_id'],
                        'user_subscription_id' => $subscription->getKey(),
                        'exception' => $exception,
                    ]);
                    $dispatchFailures++;
                }
            }
        });

        $this->info("Expired subscriptions deactivated: {$expiredSubscriptions}");
        $this->info("Expiry notifications queued: {$queuedNotifications}");
        $this->info("Expiry notification dispatch failures: {$dispatchFailures}");

        return self::SUCCESS;
    }

    private function expiryEligibleQuery(): Builder
    {
        return UserSubscription::query()
            ->where('is_active', true)
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', today()->toDateString());
    }

    /** @return array{expired: bool, notification_id: ?int} */
    private function expireAndReserveNotification(UserSubscription $subscription): array
    {
        return DB::transaction(function () use ($subscription): array {
            $lockedSubscription = $this->expiryEligibleQuery()
                ->whereKey($subscription->getKey())
                ->lockForUpdate()
                ->first();

            if ($lockedSubscription === null) {
                return ['expired' => false, 'notification_id' => null];
            }

            $lockedSubscription->update([
                'is_active' => false,
                'status' => 'expired',
            ]);

            $wasReserved = SubscriptionExpiryNotification::query()->insertOrIgnore([
                'user_subscription_id' => $lockedSubscription->getKey(),
                'status' => SubscriptionExpiryNotification::STATUS_PENDING,
                'sent_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($wasReserved === 0) {
                return ['expired' => true, 'notification_id' => null];
            }

            $notificationId = SubscriptionExpiryNotification::query()
                ->where('user_subscription_id', $lockedSubscription->getKey())
                ->value('id');

            return ['expired' => true, 'notification_id' => (int) $notificationId];
        });
    }
}
