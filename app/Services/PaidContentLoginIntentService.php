<?php

namespace App\Services;

use Illuminate\Http\Request;

class PaidContentLoginIntentService
{
    public const INTENDED_URL_SESSION_KEY = 'front_paid_content_intended_url';

    public const MODAL_SESSION_KEY = 'show_front_login_modal';

    public const MESSAGE_SESSION_KEY = 'front_login_modal_message';

    public const RETURN_AFTER_PURCHASE_SESSION_KEY = 'front_subscription_return_url';

    public function remember(Request $request, string $message): void
    {
        $request->session()->put(self::INTENDED_URL_SESSION_KEY, $request->fullUrl());
        $request->session()->flash(self::MODAL_SESSION_KEY, true);
        $request->session()->flash(self::MESSAGE_SESSION_KEY, $message);
    }

    public function pullValidDestination(Request $request): ?string
    {
        $destination = $request->session()->pull(self::INTENDED_URL_SESSION_KEY);

        return is_string($destination) && $this->isSafeApplicationUrl($destination)
            ? $destination
            : null;
    }

    public function clear(Request $request): void
    {
        $request->session()->forget([
            self::INTENDED_URL_SESSION_KEY,
            self::MODAL_SESSION_KEY,
            self::MESSAGE_SESSION_KEY,
        ]);
    }

    public function rememberReturnAfterPurchase(Request $request): void
    {
        $request->session()->put(self::RETURN_AFTER_PURCHASE_SESSION_KEY, $request->fullUrl());
    }

    public function returnAfterPurchase(Request $request): ?string
    {
        $destination = $request->session()->get(self::RETURN_AFTER_PURCHASE_SESSION_KEY);

        if (! is_string($destination) || ! $this->isSafeApplicationUrl($destination)) {
            $request->session()->forget(self::RETURN_AFTER_PURCHASE_SESSION_KEY);

            return null;
        }

        return $destination;
    }

    public function clearReturnAfterSuccessfulAccess(Request $request): void
    {
        $destination = $this->returnAfterPurchase($request);

        if ($destination !== null && hash_equals($destination, $request->fullUrl())) {
            $request->session()->forget(self::RETURN_AFTER_PURCHASE_SESSION_KEY);
        }
    }

    private function isSafeApplicationUrl(string $destination): bool
    {
        $applicationUrl = parse_url(url('/'));
        $destinationUrl = parse_url($destination);

        if (! is_array($applicationUrl) || ! is_array($destinationUrl)) {
            return false;
        }

        foreach (['scheme', 'host', 'port'] as $part) {
            if (($destinationUrl[$part] ?? null) !== ($applicationUrl[$part] ?? null)) {
                return false;
            }
        }

        $path = '/'.ltrim((string) ($destinationUrl['path'] ?? ''), '/');
        $blockedPaths = [
            parse_url(route('admin.login'), PHP_URL_PATH),
            parse_url(route('front.login'), PHP_URL_PATH),
            parse_url(route('front.register'), PHP_URL_PATH),
            parse_url(route('front.account.activation'), PHP_URL_PATH),
            parse_url(route('front.logout'), PHP_URL_PATH),
            parse_url(route('front.two-factor.challenge'), PHP_URL_PATH),
        ];

        return ! in_array($path, $blockedPaths, true);
    }
}
