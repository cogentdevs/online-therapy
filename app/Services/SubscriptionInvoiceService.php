<?php

namespace App\Services;

use App\Models\GeneralSetting;
use App\Models\SubscriptionProduct;
use App\Models\UserSubscription;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as Dompdf;
use Illuminate\Support\Facades\File;

class SubscriptionInvoiceService
{
    public function __construct(
        private readonly SubscriptionPresentationService $subscriptionPresentationService,
    ) {}

    public function pdf(UserSubscription $userSubscription): Dompdf
    {
        $userSubscription->loadMissing(['user', 'currency', 'subscriptionTypes', 'subscriptionProduct']);
        $generalSetting = GeneralSetting::current();

        return Pdf::loadView('frontend.user-account.invoice_pdf', [
            'userSubscription' => $userSubscription,
            'generalSetting' => $generalSetting,
            'logoDataUri' => $this->publicImageDataUri($generalSetting->logo),
            'duration' => $this->duration($userSubscription),
            'productType' => $this->productType($userSubscription->product_for),
            'documentSubtitle' => $this->documentSubtitle($userSubscription->product_for),
            'statusContent' => $this->statusContent($userSubscription),
            'pdfIcons' => $this->pdfIcons(),
        ])->setPaper('a4', 'portrait');
    }

    public function content(UserSubscription $userSubscription): string
    {
        return $this->pdf($userSubscription)->output();
    }

    public function filename(UserSubscription $userSubscription): string
    {
        return filled($userSubscription->invoice_no)
            ? 'invoice-'.$userSubscription->invoice_no.'.pdf'
            : 'invoice-subscription.pdf';
    }

    /** @return array{heading: string, message: string} */
    private function statusContent(UserSubscription $subscription): array
    {
        return match ($this->subscriptionPresentationService->effectiveStatus($subscription)) {
            'active' => [
                'heading' => 'Subscription Activated',
                'message' => $subscription->product_for === SubscriptionProduct::FOR_PLAN
                    ? 'Thank you for your purchase. Your plan is now active.'
                    : 'Thank you for your purchase. Your membership is now active.',
            ],
            'future' => [
                'heading' => 'Subscription Scheduled',
                'message' => 'Thank you for your purchase. Your subscription is scheduled to begin.',
            ],
            'expired' => [
                'heading' => 'Subscription Expired',
                'message' => 'This subscription has reached its expiry date.',
            ],
            default => [
                'heading' => 'Subscription Inactive',
                'message' => 'This subscription is not currently active.',
            ],
        };
    }

    private function duration(UserSubscription $subscription): string
    {
        $snapshotValue = $subscription->duration_value_snapshot;
        $snapshotUnit = strtolower(trim((string) $subscription->duration_unit_snapshot));

        if (is_int($snapshotValue) && $snapshotValue > 0
            && in_array($snapshotUnit, ['day', 'week', 'month', 'year'], true)) {
            return $snapshotValue.' '.ucfirst($snapshotUnit).($snapshotValue === 1 ? '' : 's');
        }

        $product = $subscription->subscriptionProduct;

        if ($product === null || ! is_numeric($product->duration_value) || (int) $product->duration_value < 1) {
            return '';
        }

        $unit = strtolower(trim((string) $product->duration_unit));
        if (! in_array($unit, ['day', 'week', 'month', 'year'], true)) {
            return '';
        }

        $value = (int) $product->duration_value;

        return $value.' '.ucfirst($unit).($value === 1 ? '' : 's');
    }

    private function productType(?string $productFor): string
    {
        return match ($productFor) {
            SubscriptionProduct::FOR_PLAN => 'Plan',
            SubscriptionProduct::FOR_MEMBERSHIP => 'Membership',
            default => '',
        };
    }

    private function documentSubtitle(?string $productFor): string
    {
        return match ($productFor) {
            SubscriptionProduct::FOR_PLAN => 'Digital Magazine Plan',
            SubscriptionProduct::FOR_MEMBERSHIP => 'Digital Magazine Membership',
            default => 'Digital Magazine Subscription',
        };
    }

    private function publicImageDataUri(?string $relativePath): ?string
    {
        if (blank($relativePath)) {
            return null;
        }

        $publicRoot = realpath(public_path());
        $imagePath = realpath(public_path(ltrim(str_replace('\\', '/', $relativePath), '/')));

        if ($publicRoot === false || $imagePath === false || ! str_starts_with(strtolower($imagePath), strtolower($publicRoot.DIRECTORY_SEPARATOR))) {
            return null;
        }

        $mimeType = File::mimeType($imagePath);
        if (! in_array($mimeType, ['image/png', 'image/jpeg', 'image/webp', 'image/gif'], true)) {
            return null;
        }

        return 'data:'.$mimeType.';base64,'.base64_encode(File::get($imagePath));
    }

    /** @return array<string, string> */
    private function pdfIcons(): array
    {
        return [
            'phone' => $this->svgDataUri('<path d="M7.1 3.3 10 2.6l2.1 5.2-2.4 1.4c1.5 3.1 4 5.6 7.1 7.1l1.4-2.4 5.2 2.1-.7 2.9c-.4 1.7-2 2.8-3.7 2.5C10.5 20.1 3.9 13.5 2.6 5c-.3-1.7.8-3.3 2.5-3.7Z"/>'),
            'email' => $this->svgDataUri('<path d="M4 5h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Zm0 2 8 6 8-6H4Zm16 10V9.5l-8 6-8-6V17h16Z"/>'),
            'youtube' => $this->svgDataUri('<path d="M21.6 7.2c-.2-1.2-1.1-2.1-2.3-2.3C17.5 4.5 14.5 4.4 12 4.4s-5.5.1-7.3.5C3.5 5.1 2.6 6 2.4 7.2 2.1 8.5 2 10.2 2 12s.1 3.5.4 4.8c.2 1.2 1.1 2.1 2.3 2.3 1.8.4 4.8.5 7.3.5s5.5-.1 7.3-.5c1.2-.2 2.1-1.1 2.3-2.3.3-1.3.4-3 .4-4.8s-.1-3.5-.4-4.8ZM10 15.5v-7l6 3.5-6 3.5Z"/>'),
            'instagram' => $this->svgDataUri('<path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5Zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7Zm10.5 1.5a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/>'),
            'facebook' => $this->svgDataUri('<path d="M14.2 22v-9h3l.5-3h-3.5V8.1c0-.9.3-1.6 1.8-1.6h1.9V3.8c-.3 0-1.5-.1-2.7-.1-2.7 0-4.5 1.6-4.5 4.6V10h-3v3h3v9h3.5Z"/>'),
            'linkedin' => $this->svgDataUri('<path d="M5.2 8.2H2V22h3.2V8.2ZM3.6 2A2 2 0 1 0 3.6 6a2 2 0 0 0 0-4ZM22 14.1c0-4.1-2.2-6-5.1-6-2.4 0-3.4 1.3-4 2.2V8.2H9.7V22h3.2v-6.8c0-1.8.3-3.6 2.6-3.6 2.2 0 2.3 2.1 2.3 3.7V22H22v-7.9Z"/>'),
            'x' => $this->svgDataUri('<path d="M3 3h4.6l5.2 6.9L18.7 3H21l-7.1 8.7L21.5 22h-4.6l-5.7-7.6L4.7 22H2.4l7.7-9.4L3 3Zm3.5 2 11.4 15h1.6L8.1 5H6.5Z"/>'),
            'tiktok' => $this->svgDataUri('<path d="M14.5 2h3.2c.3 2.2 1.6 3.6 4.3 3.8V9c-1.7.1-3.2-.4-4.3-1.2v7.1a7.1 7.1 0 1 1-6.1-7V11a3.8 3.8 0 1 0 2.9 3.7V2Z"/>'),
        ];
    }

    private function svgDataUri(string $path): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="12" fill="#174f82"/><g fill="#fff">'.$path.'</g></svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
