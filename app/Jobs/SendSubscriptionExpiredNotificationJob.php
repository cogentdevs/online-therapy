<?php

namespace App\Jobs;

use App\Mail\SubscriptionExpiredMailToUser;
use App\Models\SubscriptionExpiryNotification;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

class SendSubscriptionExpiredNotificationJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public const QUEUE = 'subscription-notifications';

    public int $tries = 3;

    public int $uniqueFor = 3600;

    public function __construct(public int $subscriptionExpiryNotificationId)
    {
        $this->onQueue(self::QUEUE);
    }

    public function uniqueId(): string
    {
        return (string) $this->subscriptionExpiryNotificationId;
    }

    /** @return list<int> */
    public function backoff(): array
    {
        return [60, 300];
    }

    public function handle(): void
    {
        $notification = SubscriptionExpiryNotification::query()
            ->with(['userSubscription.user', 'userSubscription.subscriptionTypes'])
            ->find($this->subscriptionExpiryNotificationId);

        if ($notification === null || $notification->status === SubscriptionExpiryNotification::STATUS_SENT) {
            return;
        }

        $subscription = $notification->userSubscription;
        $recipientEmail = trim((string) $subscription?->user?->email);

        if ($subscription === null || $recipientEmail === '') {
            throw new RuntimeException('The expired subscription notification recipient is unavailable.');
        }

        Mail::mailer(SubscriptionExpiredMailToUser::MAILER)
            ->to($recipientEmail)
            ->send(new SubscriptionExpiredMailToUser($subscription));

        $notification->update([
            'status' => SubscriptionExpiryNotification::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        $notification = SubscriptionExpiryNotification::query()->find($this->subscriptionExpiryNotificationId);

        if ($notification !== null && $notification->status !== SubscriptionExpiryNotification::STATUS_SENT) {
            $notification->update([
                'status' => SubscriptionExpiryNotification::STATUS_FAILED,
                'sent_at' => null,
            ]);

            Log::error('Expired subscription notification job failed after exhausting retries.', [
                'subscription_expiry_notification_id' => $notification->getKey(),
                'user_subscription_id' => $notification->user_subscription_id,
                'exception' => $exception,
            ]);
        }
    }
}
