<?php

namespace App\Services\Api;

use App\Mail\SubscriptionActivatedMailToUser;
use App\Mail\SubscriptionPurchasedMailToAdmin;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Mail;

class SubscriptionPurchaseMailService
{
    public function send(UserSubscription $subscription): void
    {
        try {
            Mail::to($subscription->user)->send(new SubscriptionActivatedMailToUser($subscription));
        } catch (\Throwable $exception) {
            report($exception);
        }

        $adminEmail = config('mail.admin_address');

        if (! is_string($adminEmail) || filter_var($adminEmail, FILTER_VALIDATE_EMAIL) === false) {
            return;
        }

        try {
            Mail::to($adminEmail)->send(new SubscriptionPurchasedMailToAdmin($subscription));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
