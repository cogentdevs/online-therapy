<?php

namespace App\Jobs;

use App\Mail\SubscriptionExpiryReminderMailToUser;
use App\Models\SubscriptionExpiryReminder;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

class SendSubscriptionExpiryReminderJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $uniqueFor = 3600;

    public function __construct(public int $subscriptionExpiryReminderId) {}

    public function uniqueId(): string
    {
        return (string) $this->subscriptionExpiryReminderId;
    }

    /** @return list<int> */
    public function backoff(): array
    {
        return [60, 300];
    }

    public function handle(): void
    {
        $tracking = SubscriptionExpiryReminder::query()
            ->with(['userSubscription.user', 'userSubscription.subscriptionTypes'])
            ->find($this->subscriptionExpiryReminderId);

        if ($tracking === null || $tracking->status === SubscriptionExpiryReminder::STATUS_SENT) {
            return;
        }

        $subscription = $tracking->userSubscription;
        $recipientEmail = trim((string) $subscription?->user?->email);

        if ($subscription === null || $recipientEmail === '') {
            throw new RuntimeException('The subscription expiry reminder recipient is unavailable.');
        }

        Mail::mailer(SubscriptionExpiryReminderMailToUser::MAILER)
            ->to($recipientEmail)
            ->send(new SubscriptionExpiryReminderMailToUser($subscription, $tracking->reminder_days));

        $tracking->update([
            'status' => SubscriptionExpiryReminder::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        $tracking = SubscriptionExpiryReminder::query()->find($this->subscriptionExpiryReminderId);

        if ($tracking !== null && $tracking->status !== SubscriptionExpiryReminder::STATUS_SENT) {
            $tracking->update([
                'status' => SubscriptionExpiryReminder::STATUS_FAILED,
                'sent_at' => null,
            ]);

            Log::error('Subscription expiry reminder job failed after exhausting retries.', [
                'subscription_expiry_reminder_id' => $tracking->getKey(),
                'user_subscription_id' => $tracking->user_subscription_id,
                'exception' => $exception,
            ]);
        }
    }
}
