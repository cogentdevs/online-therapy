<?php

namespace App\Services;

use App\Models\NewsletterSubscriber;

class NewsletterSubscriptionService
{
    public function __construct(private readonly NewsletterBrevoService $brevo) {}

    /** @return array{subscriber: NewsletterSubscriber, already_subscribed: bool, synced: bool} */
    public function subscribe(string $email): array
    {
        $subscriber = NewsletterSubscriber::query()->firstOrNew(['email' => $email]);
        $alreadySubscribed = $subscriber->exists && $subscriber->status === NewsletterSubscriber::STATUS_SUBSCRIBED;

        if (! $alreadySubscribed) {
            $subscriber->forceFill([
                'status' => NewsletterSubscriber::STATUS_SUBSCRIBED,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
                'unsubscribe_reason' => null,
                'brevo_sync_status' => NewsletterSubscriber::SYNC_PENDING,
                'brevo_error' => null,
            ])->save();
        }

        $shouldSync = ! $alreadySubscribed || $subscriber->brevo_sync_status !== NewsletterSubscriber::SYNC_SYNCED;
        $synced = $shouldSync ? $this->brevo->sync($subscriber) : true;

        return [
            'subscriber' => $subscriber,
            'already_subscribed' => $alreadySubscribed,
            'synced' => $synced,
        ];
    }
}
