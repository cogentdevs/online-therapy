<?php

namespace App\Services;

use App\Mail\AdvertisingRequestReceivedMailToAdmin;
use App\Mail\AdvertisingRequestReceivedMailToUser;
use App\Mail\AdvertisingRequestStatusMailToAdmin;
use App\Mail\AdvertisingRequestStatusMailToUser;
use App\Models\AdRequest;
use App\Models\AdRequestPlacement;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AdvertisingRequestMailService
{
    public function sendStatusUpdate(AdRequest $adRequest, string $status, ?AdRequestPlacement $placement = null): void
    {
        try {
            $adRequest->refresh()->load('placements');
            $placement?->refresh();
        } catch (Throwable $exception) {
            report($exception);

            return;
        }

        try {
            Mail::to($adRequest->email)->send(new AdvertisingRequestStatusMailToUser($adRequest, $status, $placement));
        } catch (Throwable $exception) {
            report($exception);
        }

        $adminEmail = $this->adminEmail($adRequest);

        if ($adminEmail === null) {
            return;
        }

        try {
            Mail::to($adminEmail)->send(new AdvertisingRequestStatusMailToAdmin($adRequest, $status, $placement));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public function sendForCreatedRequest(AdRequest $adRequest): void
    {
        try {
            $adRequest->loadMissing('placements');
        } catch (Throwable $exception) {
            report($exception);

            return;
        }

        try {
            Mail::to($adRequest->email)->send(new AdvertisingRequestReceivedMailToUser($adRequest));
        } catch (Throwable $exception) {
            report($exception);
        }

        $adminEmail = $this->adminEmail($adRequest);

        if ($adminEmail === null) {
            return;
        }

        try {
            Mail::to($adminEmail)->send(new AdvertisingRequestReceivedMailToAdmin($adRequest));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function adminEmail(AdRequest $adRequest): ?string
    {
        try {
            $configuredAdminEmail = 'cogentdevs@gmail.com';
            $adminEmail = is_string($configuredAdminEmail) && filter_var($configuredAdminEmail, FILTER_VALIDATE_EMAIL)
                ? $configuredAdminEmail
                : GeneralSetting::query()->value('email');
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }

        if (! is_string($adminEmail) || filter_var($adminEmail, FILTER_VALIDATE_EMAIL) === false) {
            Log::warning('Advertising request admin notification skipped: no valid configured recipient.', [
                'ad_request_id' => $adRequest->getKey(),
            ]);

            return null;
        }

        return $adminEmail;
    }
}
