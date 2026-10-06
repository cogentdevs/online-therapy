<?php

namespace App\Services;

use App\Mail\SubscriptionActivatedMailToUser;
use App\Mail\SubscriptionPaymentReceivedMailToAdmin;
use App\Mail\SubscriptionPaymentReceivedMailToUser;
use App\Mail\SubscriptionPaymentRejectedMailToUser;
use App\Models\UserSubscription;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SubscriptionPaymentMailService
{
    public function sendPending(UserSubscription $subscription): void
    {
        $subscription->loadMissing(['user', 'currency', 'subscriptionTypes']);

        $this->sendToUser($subscription, new SubscriptionPaymentReceivedMailToUser($subscription));

        $adminEmail = config('mail.admin_address');
        if (! is_string($adminEmail) || filter_var($adminEmail, FILTER_VALIDATE_EMAIL) === false) {
            Log::warning('Subscription payment admin notification skipped: no valid configured recipient.', [
                'user_subscription_id' => $subscription->getKey(),
            ]);

            return;
        }

        try {
            Mail::to($adminEmail)->send(new SubscriptionPaymentReceivedMailToAdmin($subscription));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public function sendApproved(UserSubscription $subscription): void
    {
        $subscription->loadMissing(['user', 'currency', 'subscriptionTypes', 'subscriptionProduct']);
        $this->sendToUser($subscription, new SubscriptionActivatedMailToUser($subscription));
    }

    public function sendRejected(UserSubscription $subscription): void
    {
        $subscription->loadMissing(['user', 'currency', 'subscriptionTypes']);
        $this->sendToUser($subscription, new SubscriptionPaymentRejectedMailToUser($subscription));
    }

    private function sendToUser(UserSubscription $subscription, Mailable $mail): void
    {
        if (! is_string($subscription->user?->email)
            || filter_var($subscription->user->email, FILTER_VALIDATE_EMAIL) === false) {
            Log::warning('Subscription payment user notification skipped: no valid recipient.', [
                'user_subscription_id' => $subscription->getKey(),
            ]);

            return;
        }

        try {
            Mail::to($subscription->user)->send($mail);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
